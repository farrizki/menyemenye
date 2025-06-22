<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembatalan_sppt', function (Blueprint $table) {
            $table->id();
            // Kolom NOP
            $table->string('kd_propinsi', 2);
            $table->string('kd_dati2', 2);
            $table->string('kd_kecamatan', 3);
            $table->string('kd_kelurahan', 3);
            $table->string('kd_blok', 3);
            $table->string('no_urut', 4);
            $table->string('kd_jns_op', 1);
            $table->string('thn_pajak_sppt', 4);

            // Detail Informasi
            $table->string('nm_wp_sppt')->nullable();
            $table->text('alamat_wp')->nullable();
            $table->text('letak_op')->nullable();
            $table->decimal('pbb_yg_harus_dibayar_sppt', 16, 0)->nullable();

            // Informasi SK & Berkas
            $table->string('no_sk');
            $table->date('tgl_sk');
            $table->string('keterangan_pembatalan');
            $table->string('berkas_path')->nullable();

            // Informasi Proses
            $table->string('status_proses'); // Cth: Berhasil, Gagal, Lunas
            $table->text('pesan_proses');
            $table->string('operator');
            $table->string('nip_operator');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembatalan_sppt');
    }
};