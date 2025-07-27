<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get all books in this category.
     */
    public function books()
    {
        return $this->hasMany(Book::class, 'category_id');
    }

    /**
     * Get recommendation tracking for this category.
     */
    public function recommendationTracking()
    {
        return $this->hasMany(StudentRecommendationTracking::class, 'category_id');
    }
} 