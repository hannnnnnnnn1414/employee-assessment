<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('periode_penilaian'); // Contoh: "Januari 2024"
            $table->date('tanggal_penilaian');

            $table->string('nama');
            $table->string('jabatan');
            $table->string('dept_seksi');
            $table->string('npk');
            $table->string('golongan');

            $table->integer('kualitas')->nullable();
            $table->integer('kuantitas')->nullable();
            $table->integer('sub_total_prestasi')->nullable();

            $table->integer('kerjasama')->nullable();
            $table->integer('inisiatif_kreatifitas')->nullable();
            $table->integer('keandalan_tanggung_jawab')->nullable();
            $table->integer('disiplin')->nullable();
            $table->integer('integritas_loyalitas')->nullable();
            $table->integer('qcc_ss')->nullable();
            $table->integer('sub_total_non_prestasi')->nullable();

            $table->integer('mengarahkan_menghargai')->nullable();
            $table->integer('sub_total_man_management')->nullable();

            $table->integer('ijin')->default(0);
            $table->integer('mangkir')->default(0);
            $table->integer('sp1')->default(0);
            $table->integer('sp2')->default(0);
            $table->integer('sp3')->default(0);
            $table->integer('demerit')->default(0);

            $table->integer('nilai_total')->nullable();
            $table->integer('nilai_akhir')->nullable();
            $table->string('nilai_mutu')->nullable();

            $table->text('kekuatan')->nullable();
            $table->text('kelemahan')->nullable();

            $table->string('yang_menilai')->nullable();
            $table->string('atasan_yang_menilai')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assessments');
    }
};
