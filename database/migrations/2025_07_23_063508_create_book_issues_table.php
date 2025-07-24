<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('book_issues', function (Blueprint $table) {
        $table->id();

        // Foreign Key for the book
        $table->foreignId('book_id')->constrained('books')->onDelete('cascade');

        // Foreign Key for the student
        // IMPORTANT: If your students are in the 'users' table, change 'students' to 'users'.
        // If your student model has a different table name, use that.
        $table->foreignId('student_id')->constrained('users')->onDelete('cascade');

        $table->date('issue_date');
        $table->date('due_date');
        $table->date('return_date')->nullable(); // Nullable because it's not returned yet
        $table->string('status')->default('issued'); // e.g., 'issued', 'returned', 'overdue'
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('book_issues');
    }
}
