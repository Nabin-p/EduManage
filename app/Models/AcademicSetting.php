<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * This array tells Laravel which fields are safe to be set all at once
     * using methods like Model::create() or Model::update().
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attendance_type',
        'is_final_marks_submitted',
        // Add any other columns from your 'academic_settings' table here
        // if they should also be mass-assignable.
    ];
}