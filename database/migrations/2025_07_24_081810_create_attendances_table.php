<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // --- Core Information (Required for all attendance types) ---
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->date('attendance_date');
            $table->string('status')->default('present'); // e.g., 'present', 'absent', 'late'
            $table->unsignedInteger('session_id'); // Link to the academic year

            // --- Tracking Information ---
            $table->string('marked_by')->default('manual'); // 'manual', 'face_recognition'
            $table->time('check_in_time')->nullable(); // Useful for face recognition

            // --- Course-Specific Information (Nullable for Face Recognition) ---
            // These will be filled for manual attendance but NULL for general face recognition.
            $table->unsignedInteger('course_id')->nullable();
            $table->unsignedInteger('class_id')->nullable();
            $table->unsignedInteger('section_id')->nullable();
            
            $table->timestamps();

            // --- Constraints to prevent duplicate data ---
            // A student can only have one "face_recognition" entry per day.
            $table->unique(['student_id', 'attendance_date', 'marked_by'], 'student_daily_face_attendance_unique');

            // A student can only be marked once for a specific course on a specific day.
            $table->unique(['student_id', 'attendance_date', 'course_id'], 'student_daily_course_attendance_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
