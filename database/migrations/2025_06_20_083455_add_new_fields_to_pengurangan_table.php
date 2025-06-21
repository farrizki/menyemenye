<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pengurangan', function (Blueprint $table) {
            // Pastikan nm_wp_sppt adalah string(255) atau lebih jika perlu, dan nullable
            // Jika errornya nm_wp_sppt yang terlalu panjang, ini solusinya.
            $table->string('nm_wp_sppt', 255)->nullable()->after('tgl_sk_pengurangan')->change();
            $table->text('alamat_wp')->nullable()->after('nm_wp_sppt')->change(); // Pastikan TEXT
            $table->text('letak_op')->nullable()->after('alamat_wp')->change();    // Pastikan TEXT

            // Pastikan kolom lain juga ada dan sudah dikonfigurasi dengan benar sebelumnya
            $table->decimal('luas_bumi_sppt', 15, 0)->nullable()->after('letak_op')->change();
            $table->decimal('luas_bng_sppt', 15, 0)->nullable()->after('luas_bumi_sppt')->change();
            $table->decimal('pbb_terhutang_sppt_lama', 15, 2)->nullable()->after('luas_bng_sppt')->change();
            $table->decimal('jumlah_pengurangan_baru', 15, 2)->nullable()->after('pbb_terhutang_sppt_lama')->change();
            $table->decimal('ketetapan_baru', 15, 2)->nullable()->after('jumlah_pengurangan_baru')->change();
        });
    }

    public function down()
    {
        Schema::table('pengurangan', function (Blueprint $table) {
            $table->dropColumn([
                'nm_wp_sppt',
                'alamat_wp',
                'letak_op',
                'luas_bumi_sppt',
                'luas_bng_sppt',
                'pbb_terhutang_sppt_lama',
                'jumlah_pengurangan_baru',
                'ketetapan_baru',
            ]);
        });
    }
};