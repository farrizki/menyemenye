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
            // PERBAIKAN: Tambahkan kolom 'jenis_pengurangan'
            $table->string('jenis_pengurangan')->nullable()->after('persentase');
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
            // PERBAIKAN: Hapus kolom 'jenis_pengurangan' saat rollback
            $table->dropColumn('jenis_pengurangan');
        });
    }
};