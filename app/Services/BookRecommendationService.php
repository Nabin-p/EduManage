<?php

namespace App\Services; // <-- This namespace must be correct

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class BookRecommendationService
{
    private string $pythonPath;
    private string $scriptPath;
    private string $modelFile;

    public function __construct()
    {
        $this->pythonPath = config('app.python_path', 'python3');
        // Ensure this path is correct for your project structure
        $this->scriptPath = base_path('ml_recommender/book_recommender.py');
        $this->modelFile = storage_path('ml_models/book_recommender_model.pkl');
    }

    public function isModelReady(): bool
    {
        return file_exists($this->modelFile);
    }

    public function getModelInfo(): array
    {
        if (!$this->isModelReady()) {
            return ['exists' => false, 'last_trained' => 'Never', 'size' => null];
        }
        return ['exists' => true, 'last_trained' => date('Y-m-d H:i:s', filemtime($this->modelFile)), 'size' => filesize($this->modelFile)];
    }

    public function trainModel(): bool
    {
        try {
            $process = new Process([$this->pythonPath, $this->scriptPath, 'train']);
            $process->setWorkingDirectory(base_path());
            $process->setTimeout(360);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Book recommendation training failed: ' . $process->getErrorOutput());
                return false;
            }
            Log::info('Book recommendation training successful: ' . $process->getOutput());
            return true;
        } catch (\Exception $e) {
            Log::error('Exception during model training: ' . $e->getMessage());
            return false;
        }
    }

    public function getRecommendations(int $userId, int $numRecommendations = 5): array
    {
        $cacheKey = "recommendations_for_user_{$userId}";
        
        return Cache::remember($cacheKey, now()->addHours(6), function () use ($userId, $numRecommendations) {
            try {
                if (!$this->isModelReady()) {
                    return $this->getPopularBooksFallback($userId, $numRecommendations);
                }

                $process = new Process([$this->pythonPath, $this->scriptPath, 'recommend', '--user_id', $userId, '--num_recommendations', $numRecommendations]);
                $process->setWorkingDirectory(base_path());
                $process->run();

                if (!$process->isSuccessful()) {
                    Log::error("Recommendation script failed for user {$userId}: " . $process->getErrorOutput());
                    return $this->getPopularBooksFallback($userId, $numRecommendations);
                }

                $output = $process->getOutput();
                return json_decode($output, true) ?? $this->getPopularBooksFallback($userId, $numRecommendations);

            } catch (\Exception $e) {
                Log::error("Exception getting recommendations for user {$userId}: " . $e->getMessage());
                return $this->getPopularBooksFallback($userId, $numRecommendations);
            }
        });
    }

    public function getRecommendedBooks(int $userId, int $numRecommendations = 5): Collection
    {
        $recommendations = $this->getRecommendations($userId, $numRecommendations);
        $bookIds = collect($recommendations)->pluck('book_id')->toArray();

        if (empty($bookIds)) {
            return new Collection();
        }

        $orderedIds = implode(',', $bookIds);
        return Book::whereIn('id', $bookIds)->orderByRaw("FIELD(id, $orderedIds)")->get();
    }

    private function getPopularBooksFallback(int $userId, int $limit): array
    {
        $readBookIds = DB::table('book_issues')->where('student_id', $userId)->pluck('book_id');

        $popularBooks = Book::with('author')
            ->select('books.*')
            ->whereNotIn('books.id', $readBookIds)
            ->withCount('issues')
            ->orderByDesc('issues_count')
            ->take($limit)
            ->get();
        
        return $popularBooks->map(function ($book) {
            return [
                'book_id' => $book->id,
                'title' => $book->title,
                'author' => optional($book->author)->name,
                'description' => $book->description,
                'score' => 0.0,
                'is_fallback' => true
            ];
        })->toArray();
    }
}