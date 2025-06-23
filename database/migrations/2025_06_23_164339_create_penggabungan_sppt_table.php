<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        public function up()
    {
        Schema::create('penggabungan_sppt', function (Blueprint $table) {
            $table->id();
            // NOP yang digabung
            $table->string('kd_propinsi', 2);
            $table->string('kd_dati2', 2);
            $table->string('kd_kecamatan', 3);
            $table->string('kd_kelurahan', 3);
            $table->string('kd_blok', 3);
            $table->string('no_urut', 4);
            $table->string('kd_jns_op', 1);
            $table->string('thn_pajak_sppt', 4);
            // Info NOP
            $table->string('nm_wp_sppt')->nullable();
            $table->string('alamat_wp')->nullable();
            $table->string('letak_op')->nullable();
            $table->bigInteger('luas_bumi_sppt')->nullable();
            $table->bigInteger('luas_bng_sppt')->nullable();
            $table->bigInteger('pbb_terhutang_sppt')->nullable();
            // Info Penggabungan
            $table->string('nomor_pelayanan_pembatalan');
            $table->string('nomor_pelayanan_pembetulan');
            $table->string('bidang');
            $table->string('keterangan_penggabungan');
            $table->string('berkas_path')->nullable();
            // Info Proses
            $table->string('status_proses');
            $table->text('pesan_proses');
            $table->string('operator');
            $table->string('nip_operator');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penggabungan_sppt');
    }
};
