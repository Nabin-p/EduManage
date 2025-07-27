<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookCategory;

class BookCategorySeeder extends Seeder
{
    public function run()
    {
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
    }
} 