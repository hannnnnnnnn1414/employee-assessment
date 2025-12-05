<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->decimal('rata_prestasi', 5, 2)->nullable()->after('kuantitas');
            $table->decimal('rata_non_prestasi', 5, 2)->nullable()->after('qcc_ss');
            $table->decimal('bobot_prestasi', 5, 2)->nullable()->after('sub_total_man_management');
            $table->decimal('bobot_non_prestasi', 5, 2)->nullable()->after('bobot_prestasi');
            $table->decimal('bobot_man_management', 5, 2)->nullable()->after('bobot_non_prestasi');
        });
    }

    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn([
                'rata_prestasi',
                'rata_non_prestasi',
                'bobot_prestasi',
                'bobot_non_prestasi',
                'bobot_man_management'
            ]);
        });
    }
};
