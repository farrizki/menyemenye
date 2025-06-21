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
            // PERBAIKAN PENTING: Tambahkan kolom-kolom baru tanpa .change()
            // karena diasumsikan kolom-kolom ini belum ada di tabel.

            // Default string() di Laravel tanpa panjang berarti VARCHAR(255)
            $table->string('nm_wp_sppt')->nullable()->after('tgl_sk_pengurangan');
            $table->text('alamat_wp')->nullable()->after('nm_wp_sppt'); // Gunakan TEXT untuk alamat
            $table->text('letak_op')->nullable()->after('alamat_wp');    // Gunakan TEXT untuk letak OP
            $table->decimal('luas_bumi_sppt', 15, 0)->nullable()->after('letak_op');
            $table->decimal('luas_bng_sppt', 15, 0)->nullable()->after('luas_bumi_sppt');
            $table->decimal('pbb_terhutang_sppt_lama', 15, 2)->nullable()->after('luas_bng_sppt');
            $table->decimal('jumlah_pengurangan_baru', 15, 2)->nullable()->after('pbb_terhutang_sppt_lama');
            $table->decimal('ketetapan_baru', 15, 2)->nullable()->after('jumlah_pengurangan_baru');
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
            // Saat rollback, hapus kolom-kolom ini
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