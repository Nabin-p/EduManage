<?php
// In the new migration file (e.g., xxxx_xx_xx_xxxxxx_add_is_final_marks_submitted_to_academic_settings_table.php)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsFinalMarksSubmittedToAcademicSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('academic_settings', function (Blueprint $table) {
            // Add the new column, make it a boolean, and default to 0 (false)
            $table->boolean('is_final_marks_submitted')->default(0)->after('attendance_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('academic_settings', function (Blueprint $table) {
            // This allows you to undo the migration if needed
            $table->dropColumn('is_final_marks_submitted');
        });
    }

}
