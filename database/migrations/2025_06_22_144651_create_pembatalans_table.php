<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembatalans', function (Blueprint $table) {
            $table->id();
            $table->string('kd_propinsi', 2);
            $table->string('kd_dati2', 2);
            $table->string('kd_kecamatan', 3);
            $table->string('kd_kelurahan', 3);
            $table->string('kd_blok', 3);
            $table->string('no_urut', 4);
            $table->string('kd_jns_op', 1);
            $table->string('thn_pajak_sppt', 4);
            $table->string('nm_wp_sppt')->nullable();
            $table->text('letak_op')->nullable();
            $table->bigInteger('pbb_terhutang_sppt')->nullable();
            $table->string('no_sk_pembatalan');
            $table->date('tgl_sk_pembatalan');
            $table->string('operator');
            $table->string('berkas_path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembatalans');
    }
};