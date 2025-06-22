<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembatalan_sppt', function (Blueprint $table) {
            // Tambahkan kolom setelah 'pbb_yg_harus_dibayar_sppt'
            $table->decimal('luas_bumi_sppt', 16, 0)->nullable()->after('pbb_yg_harus_dibayar_sppt');
            $table->decimal('luas_bng_sppt', 16, 0)->nullable()->after('luas_bumi_sppt');
        });
    }

    public function down()
    {
        Schema::table('pembatalan_sppt', function (Blueprint $table) {
            $table->dropColumn(['luas_bumi_sppt', 'luas_bng_sppt']);
        });
    }
};