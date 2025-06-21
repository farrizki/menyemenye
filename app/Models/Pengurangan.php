<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengurangan extends Model
{
    use HasFactory;

    protected $connection = 'oracle';
    protected $table = 'pengurangan';
    public $timestamps = true;

    protected $fillable = [
        'kd_propinsi', 'kd_dati2', 'kd_kecamatan', 'kd_kelurahan', 'kd_blok',
        'no_urut', 'kd_jns_op', 'thn_pajak_sppt',
        'faktor_pengurang_sppt',
        'pbb_yg_harus_dibayar_sppt',
        'persentase',
        'jenis_pengurangan',
        'no_sk_pengurangan',
        'tgl_sk_pengurangan',
        'nm_wp_sppt',
        'alamat_wp',
        'letak_op',
        'luas_bumi_sppt',
        'luas_bng_sppt',
        'pbb_terhutang_sppt_lama',
        'jumlah_pengurangan_baru',
        'ketetapan_baru',
        'operator',
        'berkas_path', // PERBAIKAN: Tambahkan 'berkas_path'
    ];

    protected $casts = [
        'tgl_sk_pengurangan' => 'date',
    ];
}