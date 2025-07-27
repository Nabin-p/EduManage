<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarkController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ExamRuleController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SyllabusController;
use App\Http\Controllers\GradeRuleController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\GradingSystemController;
use App\Http\Controllers\SchoolSessionController;
use App\Http\Controllers\AcademicSettingController;
use App\Http\Controllers\AssignedTeacherController;
use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\KhaltiPaymentController;
use App\Http\Controllers\Librarian;
use App\Http\Controllers\Librarian\DashboardController;
use App\Http\Controllers\Librarian\BookController;
use App\Http\Controllers\Librarian\BookIssueController;
use App\Http\Controllers\Student\LibraryController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::prefix('school')->name('school.')->group(function () {
        Route::post('session/create', [SchoolSessionController::class, 'store'])->name('session.store');
        Route::post('session/browse', [SchoolSessionController::class, 'browse'])->name('session.browse');

        Route::post('semester/create', [SemesterController::class, 'store'])->name('semester.create');
        Route::post('final-marks-submission-status/update', [AcademicSettingController::class, 'updateFinalMarksSubmissionStatus'])->name('final.marks.submission.status.update');

        Route::post('attendance/type/update', [AcademicSettingController::class, 'updateAttendanceType'])->name('attendance.type.update');

        // Class
        Route::post('class/create', [SchoolClassController::class, 'store'])->name('class.create');
        Route::post('class/update', [SchoolClassController::class, 'update'])->name('class.update');

        // Sections
        Route::post('section/create', [SectionController::class, 'store'])->name('section.create');
        Route::post('section/update', [SectionController::class, 'update'])->name('section.update');

        // Courses
        Route::post('course/create', [CourseController::class, 'store'])->name('course.create');
        Route::post('course/update', [CourseController::class, 'update'])->name('course.update');

        // Teacher
        Route::post('teacher/create', [UserController::class, 'storeTeacher'])->name('teacher.create');
        Route::post('teacher/update', [UserController::class, 'updateTeacher'])->name('teacher.update');
        Route::post('teacher/assign', [AssignedTeacherController::class, 'store'])->name('teacher.assign');

        // Student
        Route::post('student/create', [UserController::class, 'storeStudent'])->name('student.create');
        Route::post('student/update', [UserController::class, 'updateStudent'])->name('student.update');
    });


    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Attendance
    Route::post('/attendance/submit', [AttendanceController::class, 'submitAttendance'])->name('attendance.submit');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendances/view', [AttendanceController::class, 'show'])->name('attendance.list.show');
    Route::get('/attendances/take', [AttendanceController::class, 'create'])->name('attendance.create.show');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::post('/attendance/face/submit', [AttendanceController::class, 'submitFaceAttendance'])->name('attendance.face.submit');

    // Classes and sections
    Route::get('/classes', [SchoolClassController::class, 'index']);
    Route::get('/class/edit/{id}', [SchoolClassController::class, 'edit'])->name('class.edit');
    Route::get('/sections', [SectionController::class, 'getByClassId'])->name('get.sections.courses.by.classId');
    Route::get('/section/edit/{id}', [SectionController::class, 'edit'])->name('section.edit');

    // Teachers
    Route::get('/teachers/add', function () {
        return view('teachers.add');
    })->name('teacher.create.show');
    Route::get('/teachers/edit/{id}', [UserController::class, 'editTeacher'])->name('teacher.edit.show');
    Route::get('/teachers/view/list', [UserController::class, 'getTeacherList'])->name('teacher.list.show');
    Route::get('/teachers/view/profile/{id}', [UserController::class, 'showTeacherProfile'])->name('teacher.profile.show');

    //Students
    Route::get('/students/add', [UserController::class, 'createStudent'])->name('student.create.show');
    Route::get('/students/edit/{id}', [UserController::class, 'editStudent'])->name('student.edit.show');
    Route::get('/students/view/list', [UserController::class, 'getStudentList'])->name('student.list.show');
    Route::get('/students/view/profile/{id}', [UserController::class, 'showStudentProfile'])->name('student.profile.show');
    Route::get('/students/view/attendance/{id}', [AttendanceController::class, 'showStudentAttendance'])->name('student.attendance.show');

    // Marks
    Route::get('/marks/create', [MarkController::class, 'create'])->name('course.mark.create');
    Route::post('/marks/store', [MarkController::class, 'store'])->name('course.mark.store');
    Route::get('/marks/results', [MarkController::class, 'index'])->name('course.mark.list.show');
    // Route::get('/marks/view', function () {
    //     return view('marks.view');
    // });
    Route::get('/marks/view', [MarkController::class, 'showCourseMark'])->name('course.mark.show');
    Route::get('/marks/final/submit', [MarkController::class, 'showFinalMark'])->name('course.final.mark.submit.show');
    Route::post('/marks/final/submit', [MarkController::class, 'storeFinalMark'])->name('course.final.mark.submit.store');

    // Exams
    Route::get('/exams/view', [ExamController::class, 'index'])->name('exam.list.show');
    // Route::get('/exams/view/history', function () {
    //     return view('exams.history');
    // });
    Route::post('/exams/create', [ExamController::class, 'store'])->name('exam.create');
    // Route::post('/exams/delete', [ExamController::class, 'delete'])->name('exam.delete');
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exam.create.show');
    Route::get('/exams/add-rule', [ExamRuleController::class, 'create'])->name('exam.rule.create');
    Route::post('/exams/add-rule', [ExamRuleController::class, 'store'])->name('exam.rule.store');
    Route::get('/exams/edit-rule', [ExamRuleController::class, 'edit'])->name('exam.rule.edit');
    Route::post('/exams/edit-rule', [ExamRuleController::class, 'update'])->name('exam.rule.update');
    Route::get('/exams/view-rule', [ExamRuleController::class, 'index'])->name('exam.rule.show');
    Route::get('/exams/grade/create', [GradingSystemController::class, 'create'])->name('exam.grade.system.create');
    Route::post('/exams/grade/create', [GradingSystemController::class, 'store'])->name('exam.grade.system.store');
    Route::get('/exams/grade/view', [GradingSystemController::class, 'index'])->name('exam.grade.system.index');
    Route::get('/exams/grade/add-rule', [GradeRuleController::class, 'create'])->name('exam.grade.system.rule.create');
    Route::post('/exams/grade/add-rule', [GradeRuleController::class, 'store'])->name('exam.grade.system.rule.store');
    Route::get('/exams/grade/view-rules', [GradeRuleController::class, 'index'])->name('exam.grade.system.rule.show');
    Route::post('/exams/grade/delete-rule', [GradeRuleController::class, 'destroy'])->name('exam.grade.system.rule.delete');

    // Promotions
    Route::get('/promotions/index', [PromotionController::class, 'index'])->name('promotions.index');
    Route::get('/promotions/promote', [PromotionController::class, 'create'])->name('promotions.create');
    Route::post('/promotions/promote', [PromotionController::class, 'store'])->name('promotions.store');

    // Academic settings
    Route::get('/academics/settings', [AcademicSettingController::class, 'index']);

    // Calendar events
    Route::get('calendar-event', [EventController::class, 'index'])->name('events.show');
    Route::post('calendar-crud-ajax', [EventController::class, 'calendarEvents'])->name('events.crud');

    // Routines
    Route::get('/routine/create', [RoutineController::class, 'create'])->name('section.routine.create');
    Route::get('/routine/view', [RoutineController::class, 'show'])->name('section.routine.show');
    Route::post('/routine/store', [RoutineController::class, 'store'])->name('section.routine.store');

    // Syllabus
    Route::get('/syllabus/create', [SyllabusController::class, 'create'])->name('class.syllabus.create');
    Route::post('/syllabus/create', [SyllabusController::class, 'store'])->name('syllabus.store');
    Route::get('/syllabus/index', [SyllabusController::class, 'index'])->name('course.syllabus.index');

    // Notices
    Route::get('/notice/create', [NoticeController::class, 'create'])->name('notice.create');
    Route::post('/notice/create', [NoticeController::class, 'store'])->name('notice.store');

    // Courses
    Route::get('courses/teacher/index', [AssignedTeacherController::class, 'getTeacherCourses'])->name('course.teacher.list.show');
    Route::get('courses/student/index/{student_id}', [CourseController::class, 'getStudentCourses'])->name('course.student.list.show');
    Route::get('course/edit/{id}', [CourseController::class, 'edit'])->name('course.edit');

    // Assignment
    Route::get('courses/assignments/index', [AssignmentController::class, 'getCourseAssignments'])->name('assignment.list.show');
    Route::get('courses/assignments/create', [AssignmentController::class, 'create'])->name('assignment.create');
    Route::post('courses/assignments/create', [AssignmentController::class, 'store'])->name('assignment.store');

    // Update password
    Route::get('password/edit', [UpdatePasswordController::class, 'edit'])->name('password.edit');
    Route::post('password/edit', [UpdatePasswordController::class, 'update'])->name('password.update');
});

Route::post('/pay-fee', [KhaltiPaymentController::class, 'initiatePayment'])->name('khalti.initiate');
Route::post('/verify-payment', [KhaltiPaymentController::class, 'verifyPayment'])->name('khalti.verify');

// This is the route to SHOW the attendance page (You likely have this already)
Route::get('/attendance/take/{class_id}', [AttendanceController::class, 'create'])->name('attendance.take');
// ** THIS IS THE MISSING ROUTE TO ADD **
// This route will handle the form submission when you click "Submit Attendance"
Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');

Route::post('/academics/settings/upload-student-photo/{student_id}', [AcademicSettingController::class, 'uploadStudentPhoto'])->name('academic-settings.upload-student-photo');

Route::post('/users/students/{student_id}/upload-photo', [UserController::class, 'uploadStudentPhoto'])->name('users.students.upload-photo');

// LIBRARIAN ROUTES
Route::middleware(['auth', 'is_librarian'])->prefix('librarian')->name('librarian.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Book Management (CRUD)
    Route::resource('books', BookController::class);

    // Book Issuing and Returning
    Route::get('/issues', [BookIssueController::class, 'index'])->name('issues.index');
    Route::get('/issue/new', [BookIssueController::class, 'create'])->name('issue.create');
    Route::post('/issue/new', [BookIssueController::class, 'store'])->name('issue.store');
    Route::post('/issue/{bookIssue}/return', [BookIssueController::class, 'returnBook'])->name('issue.return');

    Route::middleware(['auth', 'is_librarian'])->prefix('librarian')->name('librarian.')->group(function () {
        // ... your existing dashboard, books, and issue routes ...

        // ADD THESE TWO NEW ROUTES FOR AJAX SEARCH
        Route::get('/api/books/search', [BookIssueController::class, 'searchBooks'])->name('api.books.search');
        Route::get('/api/students/search', [BookIssueController::class, 'searchStudents'])->name('api.students.search');
    });
    Route::get('/api/books/search', [BookIssueController::class, 'searchBooks'])->name('api.books.search');
    Route::get('/api/students/search', [BookIssueController::class, 'searchStudents'])->name('api.students.search');
});

// Student Book Recommendation Routes
Route::middleware(['auth', 'is_student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/book-recommendation', [App\Http\Controllers\Student\BookRecommendationController::class, 'getDailyRecommendation'])->name('book-recommendation.daily');
    Route::post('/book-recommendation/refresh', [App\Http\Controllers\Student\BookRecommendationController::class, 'getNewRecommendation'])->name('book-recommendation.refresh');
    Route::get('/book-recommendation/stats', [App\Http\Controllers\Student\BookRecommendationController::class, 'getStats'])->name('book-recommendation.stats');
    
    // Test route
    Route::get('/test', function() {
        return response()->json(['success' => true, 'message' => 'Student middleware working']);
    })->name('test');
});

// Admin route for resetting recommendation history
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/book-recommendation/reset', [App\Http\Controllers\Student\BookRecommendationController::class, 'resetHistory'])->name('book-recommendation.reset');
});

Route::middleware(['auth', 'is_student'])->prefix('student')->name('student.')->group(function () {

    // ... any other student routes you have ...

    // ADD THIS NEW ROUTE FOR THE STUDENT'S BOOK LIST
    Route::get('/my-books', [LibraryController::class, 'myBooks'])->name('library.my_books');
});

Route::get('/attendance/capture/class/{class_id}/section/{section_id}/course/{course_id}', [AttendanceController::class, 'showCapturePage'])
    ->name('attendance.capture');

// The endpoint that receives the captured image and all IDs
Route::post('/attendance/mark-by-face', [AttendanceController::class, 'markByFace'])
    ->name('attendance.mark');