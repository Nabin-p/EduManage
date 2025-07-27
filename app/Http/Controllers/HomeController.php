<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\SchoolSession;
use App\Interfaces\UserInterface;
use App\Repositories\NoticeRepository;
use App\Interfaces\SchoolClassInterface;
use App\Interfaces\SchoolSessionInterface;
use App\Repositories\PromotionRepository;
use Illuminate\Support\Facades\Auth; 
use App\Models\BookIssue; 
use App\Services\PersonalizedBookRecommendationService;

class HomeController extends Controller
{
    use SchoolSession;
    protected $schoolSessionRepository;
    protected $schoolClassRepository;
    protected $userRepository;
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    
    public function __construct(
        UserInterface $userRepository, SchoolSessionInterface $schoolSessionRepository, SchoolClassInterface $schoolClassRepository)
    {
        $this->middleware('auth');
        $this->userRepository = $userRepository;
        $this->schoolSessionRepository = $schoolSessionRepository;
        $this->schoolClassRepository = $schoolClassRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $current_school_session_id = $this->getSchoolCurrentSession();

        $classCount = $this->schoolClassRepository->getAllBySession($current_school_session_id)->count();

        $studentCount = $this->userRepository->getAllStudentsBySessionCount($current_school_session_id);

        $promotionRepository = new PromotionRepository();

        $maleStudentsBySession = $promotionRepository->getMaleStudentsBySessionCount($current_school_session_id);

        $teacherCount = $this->userRepository->getAllTeachers()->count();

        $noticeRepository = new NoticeRepository();
        $notices = $noticeRepository->getAll($current_school_session_id);

        $data = [
            'classCount'    => $classCount,
            'studentCount'  => $studentCount,
            'teacherCount'  => $teacherCount,
            'notices'       => $notices,
            'maleStudentsBySession' => $maleStudentsBySession,
        ];

        $user = Auth::user();
        $viewData = []; // An array to hold all data for the view

        // Check if the logged-in user is a student
        if ($user->role == 'student') {
            // If they are a student, get their issued books
            $viewData['myIssuedBooks'] = BookIssue::with('book')
                                              ->where('student_id', $user->id)
                                              ->where('status', 'issued')
                                              ->latest('issue_date')
                                              ->limit(5)
                                              ->get();

            // Get personalized book recommendation
            $recommendationService = app(PersonalizedBookRecommendationService::class);
            $recommendedBook = $recommendationService->getDailyRecommendation($user->id);
            
            if ($recommendedBook) {
                $viewData['recommendedBook'] = $recommendedBook;
                $viewData['recommendationStats'] = $recommendationService->getRecommendationStats($user->id);
            } else {
                $viewData['noMoreBooks'] = true;
                $viewData['recommendationStats'] = $recommendationService->getRecommendationStats($user->id);
            }
        }

        return view('home', array_merge($data, $viewData));
    }
}
