<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BookCategory;
use Illuminate\Support\Facades\DB;

class UpdateBookCategories extends Command
{
    protected $signature = 'books:update-categories';
    protected $description = 'Update book categories with the specific categories requested';

    public function handle()
    {
        // Clear existing categories by deleting them
        BookCategory::query()->delete();
        
        $categories = [
            ['name' => 'Fiction', 'description' => 'Imaginative literature and novels'],
            ['name' => 'Science', 'description' => 'Scientific literature and research'],
            ['name' => 'Business', 'description' => 'Business and management books'],
            ['name' => 'Literature', 'description' => 'Classic and contemporary literature'],
            ['name' => 'Personal Development', 'description' => 'Self-help and personal growth'],
            ['name' => 'History', 'description' => 'Historical accounts and analysis'],
        ];

        foreach ($categories as $category) {
            BookCategory::create($category);
        }

        $this->info('Book categories updated successfully!');
        $this->table(['Name', 'Description'], $categories);
    }
} 