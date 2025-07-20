<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeesToStudentsTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('student_academic_infos', function (Blueprint $table) {
        $table->decimal('fee', 8, 2)->default(20); // Add a default fee of Rs. 20
        $table->boolean('is_paid')->default(false); // Track if the fee is paid
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('student_academic_infos', function (Blueprint $table) {
        $table->dropColumn(['fee', 'is_paid']);
    });
}
}