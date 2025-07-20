<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\SchoolSession;
use App\Interfaces\UserInterface;
use App\Interfaces\SectionInterface;
use App\Interfaces\SchoolClassInterface;
use App\Repositories\PromotionRepository;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Requests\TeacherStoreRequest;
use App\Interfaces\SchoolSessionInterface;
use App\Repositories\StudentParentInfoRepository;

// === ADD THESE 'use' STATEMENTS FOR THE NEW FUNCTIONALITY ===
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
// ========================================================

class UserController extends Controller
{
    use SchoolSession;
    protected $userRepository;
    protected $schoolSessionRepository;
    protected $schoolClassRepository;
    protected $schoolSectionRepository;

    public function __construct(UserInterface $userRepository, SchoolSessionInterface $schoolSessionRepository,
    SchoolClassInterface $schoolClassRepository,
    SectionInterface $schoolSectionRepository)
    {
        // Your middleware can be adjusted to allow access to the new method if needed.
        // For now, we assume an admin who can view users can also upload photos.
        $this->middleware(['can:view users']);

        $this->userRepository = $userRepository;
        $this->schoolSessionRepository = $schoolSessionRepository;
        $this->schoolClassRepository = $schoolClassRepository;
        $this->schoolSectionRepository = $schoolSectionRepository;
    }

    // --- YOUR EXISTING METHODS (UNCHANGED) ---

    public function storeTeacher(TeacherStoreRequest $request)
    {
        try {
            $this->userRepository->createTeacher($request->validated());
            return back()->with('status', 'Teacher creation was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function getStudentList(Request $request) {
        $current_school_session_id = $this->getSchoolCurrentSession();
        $class_id = $request->query('class_id', 0);
        $section_id = $request->query('section_id', 0);
        try{
            $school_classes = $this->schoolClassRepository->getAllBySession($current_school_session_id);
            $studentList = $this->userRepository->getAllStudents($current_school_session_id, $class_id, $section_id);
            $data = [
                'studentList'       => $studentList,
                'school_classes'    => $school_classes,
            ];
            return view('students.list', $data);
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function showStudentProfile($id) {
        $student = $this->userRepository->findStudent($id);
        $current_school_session_id = $this->getSchoolCurrentSession();
        $promotionRepository = new PromotionRepository();
        $promotion_info = $promotionRepository->getPromotionInfoById($current_school_session_id, $id);
        $data = [
            'student'           => $student,
            'promotion_info'    => $promotion_info,
        ];
        return view('students.profile', $data);
    }

    public function showTeacherProfile($id) {
        $teacher = $this->userRepository->findTeacher($id);
        $data = [ 'teacher' => $teacher ];
        return view('teachers.profile', $data);
    }

    public function createStudent() {
        $current_school_session_id = $this->getSchoolCurrentSession();
        $school_classes = $this->schoolClassRepository->getAllBySession($current_school_session_id);
        $data = [
            'current_school_session_id' => $current_school_session_id,
            'school_classes'            => $school_classes,
        ];
        return view('students.add', $data);
    }

    public function storeStudent(StudentStoreRequest $request)
    {
        try {
            $this->userRepository->createStudent($request->validated());
            return back()->with('status', 'Student creation was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function editStudent($student_id) {
        $student = $this->userRepository->findStudent($student_id);
        $studentParentInfoRepository = new StudentParentInfoRepository();
        $parent_info = $studentParentInfoRepository->getParentInfo($student_id);
        $promotionRepository = new PromotionRepository();
        $current_school_session_id = $this->getSchoolCurrentSession();
        $promotion_info = $promotionRepository->getPromotionInfoById($current_school_session_id, $student_id);
        $data = [
            'student'       => $student,
            'parent_info'   => $parent_info,
            'promotion_info'=> $promotion_info,
        ];
        return view('students.edit', $data);
    }

    public function updateStudent(Request $request) {
        try {
            $this->userRepository->updateStudent($request->toArray());
            return back()->with('status', 'Student update was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function editTeacher($teacher_id) {
        $teacher = $this->userRepository->findTeacher($teacher_id);
        $data = [ 'teacher' => $teacher ];
        return view('teachers.edit', $data);
    }

    public function updateTeacher(Request $request) {
        try {
            $this->userRepository->updateTeacher($request->toArray());
            return back()->with('status', 'Teacher update was successful!');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function getTeacherList(){
        $teachers = $this->userRepository->getAllTeachers();
        $data = [ 'teachers' => $teachers ];
        return view('teachers.list', $data);
    }


    // === NEW METHOD FOR FACE ID PHOTO UPLOAD ===
    /**
     * Handle the upload of a student's photo for face recognition.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $student_id  // This corresponds to the user's ID
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
}