<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('books', function (Blueprint $table) {
        $table->id(); // Primary Key (bigint, unsigned, auto-increment)
        $table->string('title');
        $table->string('author');
        $table->string('isbn')->unique(); // ISBN should be unique
        $table->text('description')->nullable(); // Can be empty
        $table->unsignedInteger('total_copies');
        $table->unsignedInteger('available_copies');
        $table->timestamps(); // created_at and updated_at
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
