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
        Schema::table('denda_administratif', function (Blueprint $table) {
            // PERBAIKAN PENTING: Tambahkan kolom tgl_jatuh_tempo_sppt_lama
            $table->date('tgl_jatuh_tempo_sppt_lama')->nullable()->after('tgl_jatuh_tempo_baru');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('denda_administratif', function (Blueprint $table) {
            // PERBAIKAN PENTING: Hapus kolom tgl_jatuh_tempo_sppt_lama saat rollback
            $table->dropColumn('tgl_jatuh_tempo_sppt_lama');
        });
    }
};