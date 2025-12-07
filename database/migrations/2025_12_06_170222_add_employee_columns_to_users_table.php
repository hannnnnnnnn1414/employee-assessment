<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('npk')->unique()->nullable()->after('id');
            $table->string('dept')->nullable()->after('name');
            $table->string('jabatan')->nullable()->after('dept');
            $table->string('golongan')->nullable()->after('jabatan');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['npk', 'dept', 'jabatan', 'golongan', 'role']);
        });
    }
};
