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
        Schema::table('pengurangan', function (Blueprint $table) {
            // PERBAIKAN: Tambahkan kolom 'operator'
            $table->string('operator')->nullable()->after('tgl_sk_pengurangan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pengurangan', function (Blueprint $table) {
            // PERBAIKAN: Hapus kolom 'operator' saat rollback
            $table->dropColumn('operator');
        });
    }
};