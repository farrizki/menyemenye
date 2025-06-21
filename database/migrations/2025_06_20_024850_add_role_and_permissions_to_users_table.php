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
        Schema::table('users', function (Blueprint $table) {
            // PERBAIKAN: Tambahkan kolom 'role' dengan default 'operator'
            $table->string('role')->default('operator')->after('password');
            // PERBAIKAN: Tambahkan kolom 'allowed_menus' untuk menyimpan izin dalam format JSON
            $table->json('allowed_menus')->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // PERBAIKAN: Hapus kolom 'role' dan 'allowed_menus' saat rollback
            $table->dropColumn(['role', 'allowed_menus']);
        });
    }
};