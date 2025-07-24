<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookIssue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'book_id',
        'student_id',
        'issue_date',
        'due_date',
        'return_date',
        'status',
    ];

    /**
     * Get the book that owns the issue.
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the student (user) that owns the issue.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}