<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'author',
        'isbn',
        'description',
        'total_copies',
        'available_copies',
    ];

    /**
     * Get all of the issues for the Book.
     */
    public function issues()
    {
        return $this->hasMany(BookIssue::class);
    }
}
