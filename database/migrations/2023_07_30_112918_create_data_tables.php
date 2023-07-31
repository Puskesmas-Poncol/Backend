<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('data', function (Blueprint $table) {
            $table->id();
            $table->string('NIK');
            $table->string('nama_anak');
            $table->string('tgl_lahir');
            $table->unsignedTinyInteger('umur_tahun');
            $table->unsignedTinyInteger('umur_bulan');
            $table->char('jenis_kelamin', 1);
            $table->string('nama_ortu');
            $table->string('nik_ortu');
            $table->string('hp_ortu');
            $table->string('PKM');
            $table->string('KEL');
            $table->string('POSY');
            $table->unsignedTinyInteger('RT');
            $table->unsignedTinyInteger('RW');
            $table->string('ALAMAT');
            $table->string('TANGGALUKUR');
            $table->unsignedTinyInteger('TINGGI')->nullable();
            $table->unsignedTinyInteger('BERAT')->nullable();
            $table->unsignedTinyInteger('LILA')->nullable();
            $table->unsignedTinyInteger('vita')->nullable();
            $table->unsignedTinyInteger('lingkar_kepala')->nullable();
            $table->string('asi_bulan_1')->nullable();
            $table->string('asi_bulan_2')->nullable();
            $table->string('asi_bulan_3')->nullable();
            $table->string('asi_bulan_4')->nullable();
            $table->string('asi_bulan_5')->nullable();
            $table->string('asi_bulan_6')->nullable();
            $table->string('pemberian_ke')->nullable();
            $table->string('sumber_pmt')->nullable();
            $table->string('pemberian_pusat')->nullable();
            $table->string('tahun_produksi')->nullable();
            $table->string('pemberian_daerah')->nullable();
            $table->unsignedBigInteger('id_posyandu');
            $table->unsignedBigInteger('id_upload');
            $table->timestamps();

            $table->foreign('id_posyandu')->references('id')->on('posyandu')->onDelete('cascade');
            $table->foreign('id_upload')->references('id')->on('upload')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};
