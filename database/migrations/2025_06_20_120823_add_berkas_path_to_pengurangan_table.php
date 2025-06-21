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
            // PERBAIKAN: Tambahkan kolom 'berkas_path' untuk menyimpan jalur file PDF
            $table->string('berkas_path', 255)->nullable()->after('operator'); // Setelah operator
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
            // PERBAIKAN: Hapus kolom 'berkas_path' saat rollback
            $table->dropColumn('berkas_path');
        });
    }
};