<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\UserInterface;
use App\Interfaces\SchoolClassInterface;
use App\Interfaces\SchoolSessionInterface;
use App\Interfaces\AcademicSettingInterface;
use App\Http\Requests\AttendanceStoreRequest;
use App\Interfaces\SectionInterface;
use App\Repositories\AttendanceRepository;
use App\Traits\SchoolSession;
use App\Models\AcademicSetting; // Import the model for creating a default
use Illuminate\Support\Facades\Log; // Import the Log facade for better error logging

/*
|--------------------------------------------------------------------------
| Expected Routes in routes/web.php
|--------------------------------------------------------------------------
|
| Route::get('/attendance/take', [AttendanceController::class, 'create'])->name('attendance.create');
| Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
|
*/

class AttendanceController extends Controller
{
    use SchoolSession;
    protected $academicSettingRepository;
    protected $schoolSessionRepository;
    protected $schoolClassRepository;
    protected $sectionRepository;
    protected $userRepository;

    public function __construct(
        UserInterface $userRepository,
        AcademicSettingInterface $academicSettingRepository,
        SchoolSessionInterface $schoolSessionRepository,
        SchoolClassInterface $schoolClassRepository,
        SectionInterface $sectionRepository
    ) {
        $this->middleware(['can:view attendances']);
        $this->userRepository = $userRepository;
        $this->academicSettingRepository = $academicSettingRepository;
        $this->schoolSessionRepository = $schoolSessionRepository;
        $this->schoolClassRepository = $schoolClassRepository;
        $this->sectionRepository = $sectionRepository;
    }

    public function index()
    {
        return back();
    }

    public function create(Request $request)
    {
        if ($request->query('class_id') == null) {
            return abort(404);
        }

        // === FIX #1: Ensure Python API URL is set ===
        $pythonApiUrl = env('PYTHON_API_URL');
        if (!$pythonApiUrl) {
            Log::error('PYTHON_API_URL is not set in the .env file.');
            return back()->withError('The Face Recognition service has not been configured by the administrator.');
        }
        // ===========================================

        try {
            // === FIX #2: Handle null academic_setting ===
            $academic_setting = $this->academicSettingRepository->getAcademicSetting();
            if (!$academic_setting) {
                // If no settings exist, create a default one to prevent crashes.
                $academic_setting = AcademicSetting::create([
                    'attendance_type' => 'section', // A sensible default
                    'is_final_marks_submitted' => 0,
                ]);
            }
            // ============================================

            $current_school_session_id = $this->getSchoolCurrentSession();

            $class_id = $request->query('class_id');
            $section_id = $request->query('section_id', 0);
            $course_id = $request->query('course_id');

            $student_list = $this->userRepository->getAllStudents($current_school_session_id, $class_id, $section_id);
            $school_class = $this->schoolClassRepository->findById($class_id);
            $school_section = $this->sectionRepository->findById($section_id);

            $attendanceRepository = new AttendanceRepository();
            $attendance_count = 0; // Initialize to 0

            // Now this check is safe because $academic_setting is guaranteed to be an object
            if ($academic_setting->attendance_type == 'section') {
                $attendance_count = $attendanceRepository->getSectionAttendance($class_id, $section_id, $current_school_session_id)->count();
            } else {
                $attendance_count = $attendanceRepository->getCourseAttendance($class_id, $course_id, $current_school_session_id)->count();
            }

            $data = [
                'current_school_session_id' => $current_school_session_id,
                'academic_setting'  => $academic_setting,
                'student_list'      => $student_list,
                'school_class'      => $school_class,
                'school_section'    => $school_section,
                'attendance_count'  => $attendance_count,
                'class_id'          => $class_id,
                'section_id'        => $section_id,
                'course_id'         => $course_id,
                'pythonApiUrl'      => $pythonApiUrl,
            ];

            return view('attendances.take', $data);
        } catch (\Exception $e) {
            Log::error('Attendance Create Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            return back()->withError('An unexpected error occurred while preparing the attendance page. Please try again.');
        }
    }

    // public function store(AttendanceStoreRequest $request)
    // {
    //     try {
    //         $attendanceRepository = new AttendanceRepository();
    //         $attendanceRepository->saveAttendance($request->validated());

    //         return back()->with('status', 'Attendance has been saved successfully!');
    //     } catch (\Exception $e) {
    //         Log::error('Attendance Store Error: ' . $e->getMessage());
    //         return back()->withError('Failed to save attendance. Please try again.');
    //     }
    // }

    public function store(AttendanceStoreRequest $request)
{
    try {
        // The AttendanceStoreRequest and AttendanceRepository should handle the
        // logic of looping through the `status` array and saving each record.
        // This part of your code was likely already correct.
        $attendanceRepository = new AttendanceRepository();
        $attendanceRepository->saveAttendance($request->validated());

        return back()->with('status', 'Attendance has been saved successfully!');
    } catch (\Exception $e) {
        Log::error('Attendance Store Error: ' . $e->getMessage());
        return back()->withError('Failed to save attendance. Please try again.');
    }
}

    public function show(Request $request)
    {
        // This method also uses academic_setting, so we should apply the same fix here.
        if ($request->query('class_id') == null) {
            return abort(404);
        }

        try {
            $academic_setting = $this->academicSettingRepository->getAcademicSetting();
            if (!$academic_setting) {
                // Just create a temporary object here since we only read from it.
                $academic_setting = (object)['attendance_type' => 'section'];
            }

            $current_school_session_id = $this->getSchoolCurrentSession();
            $class_id = $request->query('class_id');
            $section_id = $request->query('section_id');
            $course_id = $request->query('course_id');

            $attendanceRepository = new AttendanceRepository();
            $attendances = collect(); // Default to an empty collection

            if ($academic_setting->attendance_type == 'section') {
                $attendances = $attendanceRepository->getSectionAttendance($class_id, $section_id, $current_school_session_id);
            } else {
                $attendances = $attendanceRepository->getCourseAttendance($class_id, $course_id, $current_school_session_id);
            }
            $data = ['attendances' => $attendances];

            return view('attendances.view', $data);
        } catch (\Exception $e) {
            Log::error('Attendance Show Error: ' . $e->getMessage());
            return back()->withError($e->getMessage());
        }
    }

    public function showStudentAttendance($id)
    {
        if (auth()->user()->role == "student" && auth()->user()->id != $id) {
            return abort(404);
        }
        $current_school_session_id = $this->getSchoolCurrentSession();
        $attendanceRepository = new AttendanceRepository();
        $attendances = $attendanceRepository->getStudentAttendance($current_school_session_id, $id);
        $student = $this->userRepository->findStudent($id);

        $data = [
            'attendances'   => $attendances,
            'student'       => $student,
        ];
        return view('attendances.attendance', $data);
    }

    public function submitFaceAttendance(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|integer',
            'section_id' => 'required|integer',
            'class_id' => 'required|integer',
            'present_students' => 'nullable|array', // List of IDs for present students
            'present_students.*' => 'integer', // Validate each ID in the array
        ]);

        try {
            $active_session = $this->getSchoolCurrentSession();
            $all_students_in_section = $this->userRepository->getAllStudents($active_session, $validated['class_id'], $validated['section_id']);
            $present_student_ids = $validated['present_students'] ?? [];

            // Use a transaction to ensure all records are saved or none are.
            DB::transaction(function () use ($all_students_in_section, $present_student_ids, $validated, $active_session) {
                foreach ($all_students_in_section as $enrollment) {
                    $student_id = $enrollment->student_id;

                    // Determine the status
                    $status = in_array($student_id, $present_student_ids) ? 'present' : 'absent';

                    // Save the record
                    Attendance::updateOrCreate(
                        [
                            'student_id'      => $student_id,
                            'attendance_date' => Carbon::today(),
                            'course_id'       => $validated['course_id'],
                        ],
                        [
                            'status'          => $status,
                            'marked_by'       => 'face_recognition', // Or a mix if you want more complex logic
                            'session_id'      => $active_session,
                            'class_id'        => $validated['class_id'],
                            'section_id'      => $validated['section_id'],
                            'check_in_time'   => ($status == 'present') ? Carbon::now()->toTimeString() : null,
                        ]
                    );
                }
            });

            return response()->json(['success' => true, 'message' => 'Attendance submitted successfully!']);
        } catch (\Exception $e) {
            Log::error('Face Attendance Submit Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred while saving attendance.'], 500);
        }
    }
}
