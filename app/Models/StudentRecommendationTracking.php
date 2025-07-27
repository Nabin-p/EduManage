<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRecommendationTracking extends Model
{
    use HasFactory;

    protected $table = 'student_recommendation_tracking';

    protected $fillable = [
        'user_id',
        'book_id',
        'category_id',
        'recommended_date',
        'is_daily_recommendation',
    ];

    protected $casts = [
        'recommended_date' => 'date',
        'is_daily_recommendation' => 'boolean',
    ];

    /**
     * Get the user (student) that received this recommendation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that was recommended.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the category of the recommended book.
     */
    public function category()
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }
} 