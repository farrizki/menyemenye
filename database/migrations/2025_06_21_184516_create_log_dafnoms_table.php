<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('log_dafnoms', function (Blueprint $table) {
            $table->id();
            $table->year('tahun_pembentukan');
            $table->string('metode'); // Buat Ulang / Susulan
            $table->string('wilayah_text'); // Deskripsi wilayah, misal: "Kecamatan Nganjuk"
            $table->string('kd_kecamatan')->nullable();
            $table->string('kd_kelurahan')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('user_name');
            $table->string('user_nip');
            $table->string('status')->default('pending'); // pending, processing, success, failed
            $table->text('message')->nullable(); // Untuk pesan error jika gagal
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_dafnoms');
    }
};