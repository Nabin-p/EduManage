<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RecommendationController extends Controller
{
    /**
     * Display the daily book recommendations for the student.
     */
    public function show(Request $request)
    {
        $studentId = Auth::id();
        $numRecommendations = 5; // How many books to show at a time

        // The cache key is unique to the user AND the date.
        // This ensures the recommendations are the same for the whole day.
        $cacheKey = "daily_recommendations_for_user_{$studentId}_" . date('Y-m-d');

        // Get the recommended book IDs for today, from the cache or generate new ones.
        $recommendedBookIds = Cache::remember($cacheKey, now()->endOfDay(), function () use ($studentId, $numRecommendations) {
            return $this->generateNewRecommendations($studentId, $numRecommendations);
        });

        if (empty($recommendedBookIds)) {
            $data['noMoreBooks'] = true;
            $data['recommendedBooks'] = collect();
        } else {
            // Fetch the book models from the database
            $orderedIds = implode(',', $recommendedBookIds);
            $data['recommendedBooks'] = Book::whereIn('id', $recommendedBookIds)
                                          ->orderByRaw("FIELD(id, $orderedIds)")
                                          ->get();
        }

        // We will pass this data to the student dashboard view
        return view('home', $data); // Assuming you want to display this on the home page
    }

    /**
     * Handle the "refresh" request to get a new set of recommendations.
     */
    public function refresh()
    {
        $studentId = Auth::id();
        $cacheKey = "daily_recommendations_for_user_{$studentId}_" . date('Y-m-d');

        // Forget the old recommendations for today
        Cache::forget($cacheKey);

        // Redirect back to the show method, which will now be forced
        // to generate and cache a new list.
        return redirect()->route('student.recommendations.show');
    }
    
    /**
     * The core logic for generating new, non-repeating recommendations.
     *
     * @return array An array of book IDs.
     */
    private function generateNewRecommendations(int $studentId, int $limit): array
    {
        // 1. Get all book IDs the student has EVER been shown
        $seenBookIds = DB::table('student_seen_books')
                         ->where('user_id', $studentId)
                         ->pluck('book_id');

        // 2. Find new, random books that are NOT in the "seen" list
        $newBooks = Book::whereNotIn('id', $seenBookIds)
                        ->inRandomOrder()
                        ->take($limit)
                        ->pluck('id');

        // If we found new books, add them to the "seen" table for the future
        if ($newBooks->isNotEmpty()) {
            $recordsToInsert = $newBooks->map(function ($bookId) use ($studentId) {
                return [
                    'user_id' => $studentId,
                    'book_id' => $bookId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            DB::table('student_seen_books')->insert($recordsToInsert->toArray());
        }

        return $newBooks->toArray();
    }
}