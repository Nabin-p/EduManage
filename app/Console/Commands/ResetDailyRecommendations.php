<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class ResetDailyRecommendations extends Command
{
    protected $signature = 'recommendations:reset-daily';
    protected $description = 'Reset daily recommendation cache for all students';

    public function handle()
    {
        $students = User::where('role', 'student')->get();
        $count = 0;

        foreach ($students as $student) {
            $cacheKey = "daily_recommendation_{$student->id}_" . date('Y-m-d');
            Cache::forget($cacheKey);
            $count++;
        }

        $this->info("Reset daily recommendation cache for {$count} students.");
    }
} 