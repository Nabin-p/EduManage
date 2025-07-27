<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\PersonalizedBookRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BookRecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(PersonalizedBookRecommendationService $recommendationService)
    {
        $this->middleware('auth');
        $this->middleware('is_student');
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get today's book recommendation for the student.
     */
    public function getDailyRecommendation()
    {
        $userId = Auth::id();
        
        // Check if student has exhausted all books
        if ($this->recommendationService->hasExhaustedAllBooks($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'No more books to recommend.',
                'exhausted' => true,
                'stats' => $this->recommendationService->getRecommendationStats($userId)
            ]);
        }

        $book = $this->recommendationService->getDailyRecommendation($userId);
        
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'No recommendation available at the moment.',
                'exhausted' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'book' => [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'description' => $book->description,
                'isbn' => $book->isbn,
                'category' => $book->category ? $book->category->name : 'Uncategorized',
                'available_copies' => $book->available_copies,
            ],
            'stats' => $this->recommendationService->getRecommendationStats($userId)
        ]);
    }

    /**
     * Get a new recommendation (refresh button).
     */
    public function getNewRecommendation()
    {
        try {
            $userId = Auth::id();
            
            // Clear today's cache to force a new recommendation
            $cacheKey = "daily_recommendation_{$userId}_" . date('Y-m-d');
            Cache::forget($cacheKey);
            
            // Check if student has exhausted all books
            if ($this->recommendationService->hasExhaustedAllBooks($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No more books to recommend.',
                    'exhausted' => true,
                    'stats' => $this->recommendationService->getRecommendationStats($userId)
                ]);
            }

            $book = $this->recommendationService->getNewRecommendation($userId);
            
            if (!$book) {
                return response()->json([
                    'success' => false,
                    'message' => 'No new recommendation available.',
                    'exhausted' => true
                ]);
            }

            return response()->json([
                'success' => true,
                'book' => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'author' => $book->author,
                    'description' => $book->description,
                    'isbn' => $book->isbn,
                    'category' => $book->category ? $book->category->name : 'Uncategorized',
                    'available_copies' => $book->available_copies,
                ],
                'stats' => $this->recommendationService->getRecommendationStats($userId)
            ]);
        } catch (\Exception $e) {
            \Log::error('Book recommendation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while getting the recommendation.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recommendation statistics for the student.
     */
    public function getStats()
    {
        $userId = Auth::id();
        
        return response()->json([
            'success' => true,
            'stats' => $this->recommendationService->getRecommendationStats($userId),
            'category_diversity' => $this->recommendationService->getCategoryDiversityStats($userId)
        ]);
    }

    /**
     * Reset recommendation history for a student (admin function).
     */
    public function resetHistory(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $userId = $request->user_id;
        
        // Delete all recommendation tracking for this user
        \App\Models\StudentRecommendationTracking::where('user_id', $userId)->delete();
        
        // Clear any cached recommendations
        $cacheKey = "daily_recommendation_{$userId}_" . date('Y-m-d');
        Cache::forget($cacheKey);
        
        return response()->json([
            'success' => true,
            'message' => 'Recommendation history reset successfully.'
        ]);
    }
} 