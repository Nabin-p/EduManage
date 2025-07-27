<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentRecommendationTrackingTable extends Migration
{
    public function up()
    {
        Schema::create('student_recommendation_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('book_categories')->onDelete('set null');
            $table->date('recommended_date');
            $table->boolean('is_daily_recommendation')->default(true);
            $table->timestamps();

            // Ensure a student can only see a book once
            $table->unique(['user_id', 'book_id']);
            
            // Index for efficient queries
            $table->index(['user_id', 'recommended_date']);
            $table->index(['user_id', 'category_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_recommendation_tracking');
    }
} 