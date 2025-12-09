<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToAssessmentsTable extends Migration
{
    public function up()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'completed'])->default('draft');
            $table->boolean('is_imported')->default(false);
            $table->timestamp('submitted_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn(['status', 'is_imported', 'submitted_at']);
        });
    }
}
