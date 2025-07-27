<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Book;
use App\Models\BookCategory;

class AssignBookCategories extends Command
{
    protected $signature = 'books:assign-categories';
    protected $description = 'Assign random categories to existing books';

    public function handle()
    {
        $books = Book::whereNull('category_id')->get();
        $categories = BookCategory::all();

        if ($categories->isEmpty()) {
            $this->error('No categories found. Please run the BookCategorySeeder first.');
            return;
        }

        $count = 0;
        foreach ($books as $book) {
            $book->update(['category_id' => $categories->random()->id]);
            $count++;
        }

        $this->info("Assigned categories to {$count} books.");
    }
} 