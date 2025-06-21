<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengurangan', function (Blueprint $table) {
            $table->id();
            $table->string('kd_propinsi', 2);
            $table->string('kd_dati2', 2);
            $table->string('kd_kecamatan', 3);
            $table->string('kd_kelurahan', 3);
            $table->string('kd_blok', 3);
            $table->string('no_urut', 4);
            $table->string('kd_jns_op', 1);
            $table->string('thn_pajak_sppt', 4);
            $table->decimal('faktor_pengurang_sppt', 15, 2);
            $table->decimal('pbb_yg_harus_dibayar_sppt', 15, 2);
            $table->decimal('persentase', 5, 2);
            $table->string('no_sk_pengurangan')->nullable();
            $table->date('tgl_sk_pengurangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengurangan');
    }
};