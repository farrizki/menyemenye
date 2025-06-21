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
        Schema::create('denda_administratif', function (Blueprint $table) {
            $table->id();
            // Kolom NOP
            $table->string('kd_propinsi', 2);
            $table->string('kd_dati2', 2);
            $table->string('kd_kecamatan', 3);
            $table->string('kd_kelurahan', 3);
            $table->string('kd_blok', 3);
            $table->string('no_urut', 4);
            $table->string('kd_jns_op', 1);
            $table->string('thn_pajak_sppt', 4); // Tahun Pajak yang diupdate

            // Data WP & OP
            $table->string('nm_wp_sppt')->nullable();
            $table->text('alamat_wp')->nullable();
            $table->text('letak_op')->nullable();

            // Detail Denda & Pembayaran
            $table->decimal('pokok', 15, 2)->nullable(); // Dari pbb_yg_dibayar_sppt
            $table->decimal('denda', 15, 2)->nullable();
            $table->decimal('jumlah_pajak', 15, 2)->nullable(); // Pokok + Denda
            $table->decimal('sanksi_administratif', 15, 2)->nullable(); // Dari Denda
            $table->decimal('yang_harus_dibayar', 15, 2)->nullable(); // Dari Pokok

            // Info SK & Berkas
            $table->string('no_sk')->nullable(); // Format lengkap SK
            $table->date('tgl_sk')->nullable();
            $table->string('berkas_path')->nullable(); // Lokasi file PDF

            // Info Proses
            $table->date('tgl_jatuh_tempo_baru')->nullable(); // Tanggal Jatuh Tempo yang baru diupdate
            $table->string('operator')->nullable();
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
        Schema::dropIfExists('denda_administratif');
    }
};