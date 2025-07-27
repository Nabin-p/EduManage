<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BookRecommendationService;

class TrainRecommendationModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recommendations:train 
                            {--force : Force training even if model exists}
                            {--schedule : Run as scheduled task}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Train the book recommendation machine learning model';

    private BookRecommendationService $recommendationService;

    /**
     * Create a new command instance.
     */
    public function __construct(BookRecommendationService $recommendationService)
    {
        parent::__construct();
        $this->recommendationService = $recommendationService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = $this->option('force');
        $isScheduled = $this->option('schedule');

        if (!$isScheduled) {
            $this->info('🤖 Book Recommendation Model Training');
            $this->info('=====================================');
        }
 
        // Check if model already exists
        if (!$force && $this->recommendationService->isModelReady()) {
            $modelInfo = $this->recommendationService->getModelInfo();
            
            if (!$isScheduled) {
                $this->warn('Model already exists (trained: ' . $modelInfo['last_trained'] . ')');
                
                if (!$this->confirm('Do you want to retrain the model?')) {
                    $this->info('Training cancelled.');
                    return Command::SUCCESS;
                }
            } else {
                // For scheduled runs, check if model is older than 24 hours
                $lastTrained = strtotime($modelInfo['last_trained']);
                $oneDayAgo = time() - (24 * 60 * 60);
                
                if ($lastTrained > $oneDayAgo) {
                    $this->info('Model is recent (< 24 hours), skipping training.');
                    return Command::SUCCESS;
                }
            }
        }

        // Check Python and script availability
        if (!$this->checkPythonAvailability()) {
            $this->error('Python or required packages not available. Please check requirements.');
            return Command::FAILURE;
        }

        // Start training
        if (!$isScheduled) {
            $this->info('Starting model training...');
            $this->info('This may take a few minutes depending on your data size.');
        }

        $startTime = microtime(true);
        
        if (!$isScheduled) {
            $progressBar = $this->output->createProgressBar();
            $progressBar->start();
            
            // Simulate progress for user experience
            for ($i = 0; $i < 50; $i++) {
                usleep(100000); // 0.1 second
                $progressBar->advance();
            }
        }

        $success = $this->recommendationService->trainModel();

        if (!$isScheduled) {
            $progressBar->finish();
            $this->newLine(2);
        }

        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);

        if ($success) {
            $this->info("✅ Model training completed successfully in {$duration} seconds!");
            
            // Show model info
            $modelInfo = $this->recommendationService->getModelInfo();
            $this->table(
                ['Property', 'Value'],
                [
                    ['Status', 'Ready'],
                    ['Last Trained', $modelInfo['last_trained']],
                    ['Model Size', $this->formatBytes($modelInfo['size'])],
                    ['Training Duration', $duration . ' seconds']
                ]
            );

            if (!$isScheduled) {
                $this->info('💡 You can now get recommendations for students!');
                $this->info('   Use: php artisan recommendations:test --user_id=1');
            }

            return Command::SUCCESS;
        } else {
            $this->error("❌ Model training failed after {$duration} seconds.");
            $this->error('Check the logs for more details.');
            return Command::FAILURE;
        }
    }

    /**
     * Check if Python and required packages are available
     */
    private function checkPythonAvailability(): bool
    {
        try {
            $pythonPath = config('app.python_path', 'python3');
            $scriptPath = base_path('ml_recommender/book_recommender.py');
            
            if (!file_exists($scriptPath)) {
                $this->error('Python script not found: ' . $scriptPath);
                return false;
            }

            // Test Python availability by trying to import required packages
            $testCommand = escapeshellcmd($pythonPath) . ' -c "import pandas, numpy, sklearn, mysql.connector, joblib; print(\'OK\')" 2>&1';
            $result = shell_exec($testCommand);
            
            return strpos($result ?? '', 'OK') !== false;
        } catch (\Exception $e) {
            $this->error('Error checking Python availability: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
} 