<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sppt extends Model
{
    use HasFactory;

    protected $connection = 'oracle';
    protected $table = 'SPPT';       // Nama tabel tetap huruf besar di sini, karena ini nama tabel di DB Oracle
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    // PERBAIKAN PENTING: Ganti semua nama kolom menjadi huruf kecil di $casts
    protected $casts = [
        'pbb_terhutang_sppt' => 'float', // << PERUBAHAN DI SINI
        'faktor_pengurang_sppt' => 'float', // << PERUBAHAN DI SINI
        'pbb_yg_harus_dibayar_sppt' => 'float', // << PERUBAHAN DI SINI
        'thn_pajak_sppt' => 'integer', // << PERUBAHAN DI SINI
        'status_pembayaran_sppt' => 'integer', // << PERUBAHAN DI SINI
        'kd_propinsi' => 'string', // Tambahkan cast untuk memastikan string
        'kd_dati2' => 'string',
        'kd_kecamatan' => 'string',
        'kd_kelurahan' => 'string',
        'kd_blok' => 'string',
        'no_urut' => 'string',
        'kd_jns_op' => 'string',
        // Tambahkan cast untuk kolom lain jika perlu, pastikan namanya huruf kecil
    ];

    // PERBAIKAN PENTING: Ganti semua nama kolom menjadi huruf kecil di $fillable
    protected $fillable = [
        'kd_propinsi', 'kd_dati2', 'kd_kecamatan', 'kd_kelurahan', 'kd_blok',
        'no_urut', 'kd_jns_op', 'thn_pajak_sppt',
        'faktor_pengurang_sppt',
        'pbb_yg_harus_dibayar_sppt', 'status_pembayaran_sppt',
        'pbb_terhutang_sppt' // << PERUBAHAN DI SINI
    ];
}