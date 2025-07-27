<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BookRecommendationService;
use App\Models\User;

class TestRecommendations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendations:test  
                            {--user_id= : Specific user ID to test}
                            {--num=5 : Number of recommendations to get}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test book recommendations for a user';

    private BookRecommendationService $recommendationService;

    public function __construct(BookRecommendationService $recommendationService)
    {
        parent::__construct();
        $this->recommendationService = $recommendationService;
    }

    public function handle(): int
    {
        $userId = $this->option('user_id');
        $numRecommendations = (int) $this->option('num');

        // If no user ID provided, ask for one
        if (!$userId) {
            $users = User::whereHas('roles', function($query) {
                $query->where('name', 'student');
            })->take(10)->get(['id', 'first_name', 'last_name']);

            if ($users->isEmpty()) {
                $this->error('No students found in the system.');
                return Command::FAILURE;
            }

            $this->info('Available students:');
            foreach ($users as $user) {
                $this->line("ID: {$user->id} - {$user->first_name} {$user->last_name}");
            }

            $userId = $this->ask('Enter a user ID to test');
        }

        $userId = (int) $userId;

        // Validate user exists
        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return Command::FAILURE;
        }

        $this->info("🔍 Testing recommendations for: {$user->first_name} {$user->last_name} (ID: {$userId})");
        $this->line('');

        // Check if model is ready
        if (!$this->recommendationService->isModelReady()) {
            $this->warn('⚠️  ML model not found. This will use fallback recommendations.');
            $this->line('Run "php artisan recommendations:train" to train the model first.');
            $this->line('');
        }

        // Get recommendations
        $this->info('Getting recommendations...');
        $startTime = microtime(true);
        
        $recommendations = $this->recommendationService->getRecommendations($userId, $numRecommendations);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);

        $this->line('');
        $this->info("✅ Got {count($recommendations)} recommendations in {$duration}ms");
        $this->line('');

        if (empty($recommendations)) {
            $this->warn('No recommendations found.');
            return Command::SUCCESS;
        }

        // Display recommendations
        $this->info('📚 Recommended Books:');
        $this->line('================');

        $tableData = [];
        foreach ($recommendations as $index => $rec) {
            $tableData[] = [
                $index + 1,
                $rec['title'],
                $rec['author'],
                substr($rec['description'], 0, 50) . (strlen($rec['description']) > 50 ? '...' : ''),
                isset($rec['is_fallback']) ? 'Fallback' : round($rec['score'], 3)
            ];
        }

        $this->table(
            ['#', 'Title', 'Author', 'Description', 'Score'],
            $tableData
        );

        // Show model info
        $modelInfo = $this->recommendationService->getModelInfo();
        $this->line('');
        $this->info('📊 Model Information:');
        $this->table(
            ['Property', 'Value'],
            [
                ['Model Ready', $modelInfo['exists'] ? 'Yes' : 'No'],
                ['Last Trained', $modelInfo['last_trained'] ?? 'Never'],
                ['Model Size', $modelInfo['size'] ? $this->formatBytes($modelInfo['size']) : 'N/A']
            ]
        );

        return Command::SUCCESS;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
} 