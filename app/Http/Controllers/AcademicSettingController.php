<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\SchoolSession;
use App\Interfaces\UserInterface;
use App\Interfaces\CourseInterface;
use App\Interfaces\SectionInterface;
use App\Interfaces\SemesterInterface;
use App\Interfaces\SchoolClassInterface;
use App\Interfaces\SchoolSessionInterface;
use App\Interfaces\AcademicSettingInterface;
use App\Http\Requests\AttendanceTypeUpdateRequest;
use App\Models\AcademicSetting; // Import the model
use Illuminate\Support\Facades\File; // Import the File facade
use Illuminate\Support\Facades\Http; // Import the Http facade
use Illuminate\Support\Facades\Log; // Import the Log facade

class AcademicSettingController extends Controller
{
    use SchoolSession;
    protected $academicSettingRepository;
    protected $schoolSessionRepository;
    protected $schoolClassRepository;
    protected $schoolSectionRepository;
    protected $userRepository;
    protected $courseRepository;
    protected $semesterRepository;

    public function __construct(
        AcademicSettingInterface $academicSettingRepository,
        SchoolSessionInterface $schoolSessionRepository,
        SchoolClassInterface $schoolClassRepository,
        SectionInterface $schoolSectionRepository,
        UserInterface $userRepository,
        CourseInterface $courseRepository,
        SemesterInterface $semesterRepository
    ) {
        $this->middleware(['can:view academic settings']);
        $this->academicSettingRepository = $academicSettingRepository;
        $this->schoolSessionRepository = $schoolSessionRepository;
        $this->schoolClassRepository = $schoolClassRepository;
        $this->schoolSectionRepository = $schoolSectionRepository;
        $this->userRepository = $userRepository;
        $this->courseRepository = $courseRepository;
        $this->semesterRepository = $semesterRepository;
    }

    public function index()
    {
        $current_school_session_id = $this->getSchoolCurrentSession();
        $latest_school_session = $this->schoolSessionRepository->getLatestSession();
        $academic_setting = $this->academicSettingRepository->getAcademicSetting();

        if (!$academic_setting) {
            $academic_setting = AcademicSetting::create([
                'attendance_type' => 'section',
                'is_final_marks_submitted' => 0,
            ]);
        }

        $school_sessions = $this->schoolSessionRepository->getAll();
        $school_classes = $this->schoolClassRepository->getAllBySession($current_school_session_id);
        $school_sections = $this->schoolSectionRepository->getAllBySession($current_school_session_id);
        $teachers = $this->userRepository->getAllTeachers();
        $courses = $this->courseRepository->getAll($current_school_session_id);
        $semesters = $this->semesterRepository->getAll($current_school_session_id);

        $data = [
            'current_school_session_id' => $current_school_session_id,
            'latest_school_session_id'  => $latest_school_session->id,
            'academic_setting'          => $academic_setting,
            'school_sessions'           => $school_sessions,
            'school_classes'            => $school_classes,
            'school_sections'           => $school_sections,
            'teachers'                  => $teachers,
            'courses'                   => $courses,
            'semesters'                 => $semesters,
        ];

        return view('academics.settings', $data);
    }

    // === NEW METHOD FOR UPLOADING STUDENT PHOTOS ===
    /**
     * Handle the upload of a student's photo for face recognition.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $student_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadStudentPhoto(Request $request, $student_id)
    {
        // 1. Validate the incoming request
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // 2. Define the path to the Python project's 'known_faces' directory.
            //    This assumes your Python project folder is named 'FAST_API' and is in the Laravel root.
            $knownFacesPath = base_path('FAST_API/known_faces');
            $studentDirPath = $knownFacesPath . '/' . $student_id;

            // 3. Create the student's specific directory if it doesn't exist.
            if (!File::isDirectory($studentDirPath)) {
                File::makeDirectory($studentDirPath, 0755, true, true);
            }

            // 4. Save the uploaded file into that directory.
            $file = $request->file('photo');
            $fileName = $student_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($studentDirPath, $fileName);

            // 5. Trigger the Python API to rebuild its face database.
            $pythonApiUrl = env('PYTHON_API_URL', 'http://127.0.0.1:8000');
            $response = Http::post("{$pythonApiUrl}/admin/create-database");

            if ($response->failed()) {
                Log::error('Face recognition retrain trigger failed.', ['status' => $response->status(), 'body' => $response->body()]);
                return back()->with('warning', 'Student photo was saved, but the recognition model could not be updated. Please ask the administrator to retrain it manually.');
            }

            return back()->with('success', 'Student photo uploaded and recognition model has been updated!');

        } catch (\Exception $e) {
            Log::error('Failed to save student photo: ' . $e->getMessage());
            return back()->with('error', 'An error occurred while saving the photo. Please check file system permissions and try again.');
        }
    }


    public function updateAttendanceType(AttendanceTypeUpdateRequest $request)
    {
        try {
            $this->academicSettingRepository->updateAttendanceType($request->validated());
            return back()->with('status', 'Attendance type update was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function updateFinalMarksSubmissionStatus(Request $request) {
        try {
            $this->academicSettingRepository->updateFinalMarksSubmissionStatus($request);
            return back()->with('status', 'Final marks submission status update was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }
}