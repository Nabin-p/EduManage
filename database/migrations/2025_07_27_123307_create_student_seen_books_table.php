<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentSeenBooksTable extends Migration
{
    public function up()
    {
        Schema::create('student_seen_books', function (Blueprint $table) {
            $table->id();
            // Using user_id to be consistent with Spatie, but student_id is also fine
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->timestamps();

            // A user can only see a book once
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_seen_books');
    }
}