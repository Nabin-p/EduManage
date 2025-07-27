<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\StudentRecommendationTracking;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class PersonalizedBookRecommendationService
{
    /**
     * Get today's recommendation for a student.
     * Ensures category diversity and no repetition.
     */
    public function getDailyRecommendation(int $userId): ?Book
    {
        $cacheKey = "daily_recommendation_{$userId}_" . date('Y-m-d');
        
        return Cache::remember($cacheKey, now()->endOfDay(), function () use ($userId) {
            return $this->generateDailyRecommendation($userId);
        });
    }

    /**
     * Get a new recommendation (for refresh button).
     * Ensures category diversity and no repetition.
     */
    public function getNewRecommendation(int $userId): ?Book
    {
        try {
            return $this->generateDailyRecommendation($userId);
        } catch (\Exception $e) {
            \Log::error('Error in getNewRecommendation: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if student has exhausted all books.
     */
    public function hasExhaustedAllBooks(int $userId): bool
    {
        $totalBooks = Book::count();
        $seenBooks = StudentRecommendationTracking::where('user_id', $userId)->count();
        
        return $seenBooks >= $totalBooks;
    }

    /**
     * Get recommendation statistics for a student.
     */
    public function getRecommendationStats(int $userId): array
    {
        $totalBooks = Book::count();
        $seenBooks = StudentRecommendationTracking::where('user_id', $userId)->count();
        $categoriesSeen = StudentRecommendationTracking::where('user_id', $userId)
            ->whereNotNull('category_id')
            ->distinct('category_id')
            ->count();

        return [
            'total_books' => $totalBooks,
            'seen_books' => $seenBooks,
            'remaining_books' => $totalBooks - $seenBooks,
            'categories_seen' => $categoriesSeen,
            'exhausted' => $seenBooks >= $totalBooks,
        ];
    }

    /**
     * Core logic for generating recommendations with category diversity.
     */
    private function generateDailyRecommendation(int $userId): ?Book
    {
        // Get all books the student has seen
        $seenBookIds = StudentRecommendationTracking::where('user_id', $userId)
            ->pluck('book_id')
            ->toArray();

        // If all books have been seen, return null
        if (count($seenBookIds) >= Book::count()) {
            return null;
        }

        // Get the last recommended category for this student
        $lastRecommendedCategory = StudentRecommendationTracking::where('user_id', $userId)
            ->latest('recommended_date')
            ->value('category_id');

        // Get all available categories
        $availableCategories = BookCategory::whereHas('books', function ($query) use ($seenBookIds) {
            $query->whereNotIn('id', $seenBookIds);
        })->pluck('id')->toArray();

        // If no categories available, return any unseen book
        if (empty($availableCategories)) {
            return Book::whereNotIn('id', $seenBookIds)
                ->inRandomOrder()
                ->first();
        }

        // Prioritize categories not recently recommended
        $preferredCategories = array_diff($availableCategories, [$lastRecommendedCategory]);
        
        // If we have preferred categories, use them; otherwise use any available
        $targetCategories = !empty($preferredCategories) ? $preferredCategories : $availableCategories;
        
        // Get a random book from preferred categories
        $book = Book::whereNotIn('id', $seenBookIds)
            ->whereIn('category_id', $targetCategories)
            ->inRandomOrder()
            ->first();

        // If no book found in preferred categories, get any unseen book
        if (!$book) {
            $book = Book::whereNotIn('id', $seenBookIds)
                ->inRandomOrder()
                ->first();
        }

        // Record this recommendation
        if ($book) {
            $this->recordRecommendation($userId, $book->id, $book->category_id);
        }

        return $book;
    }

    /**
     * Record a recommendation for tracking.
     */
    private function recordRecommendation(int $userId, int $bookId, ?int $categoryId): void
    {
        StudentRecommendationTracking::create([
            'user_id' => $userId,
            'book_id' => $bookId,
            'category_id' => $categoryId,
            'recommended_date' => now()->toDateString(),
            'is_daily_recommendation' => true,
        ]);
    }

    /**
     * Get books by category for a student (unseen books only).
     */
    public function getBooksByCategory(int $userId, int $categoryId): Collection
    {
        $seenBookIds = StudentRecommendationTracking::where('user_id', $userId)
            ->pluck('book_id')
            ->toArray();

        return Book::where('category_id', $categoryId)
            ->whereNotIn('id', $seenBookIds)
            ->get();
    }

    /**
     * Get category diversity statistics.
     */
    public function getCategoryDiversityStats(int $userId): array
    {
        $categoryStats = DB::table('student_recommendation_tracking as srt')
            ->join('book_categories as bc', 'srt.category_id', '=', 'bc.id')
            ->where('srt.user_id', $userId)
            ->select('bc.name', DB::raw('count(*) as count'))
            ->groupBy('bc.id', 'bc.name')
            ->orderBy('count', 'desc')
            ->get();

        return $categoryStats->toArray();
    }
} 