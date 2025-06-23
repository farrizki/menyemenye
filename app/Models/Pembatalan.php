<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembatalan extends Model
{
    use HasFactory;

    // Model ini akan menggunakan koneksi default (MySQL)
    protected $table = 'pembatalan_sppt';

    // Izinkan semua kolom untuk diisi secara massal
    protected $guarded = ['id'];

    protected $casts = [
        'tgl_sk' => 'datetime', // <-- TAMBAHKAN BARIS INI
    ];

    // Accessor untuk memformat NOP agar mudah dibaca di laporan
    public function getFormattedNopAttribute()
    {
        $nopRaw = $this->kd_propinsi . $this->kd_dati2 . $this->kd_kecamatan . $this->kd_kelurahan . $this->kd_blok . $this->no_urut . $this->kd_jns_op;
        if (strlen($nopRaw) == 18) {
            return substr($nopRaw, 0, 2) . '.' .
                   substr($nopRaw, 2, 2) . '.' .
                   substr($nopRaw, 4, 3) . '.' .
                   substr($nopRaw, 7, 3) . '.' .
                   substr($nopRaw, 10, 3) . '.' .
                   substr($nopRaw, 13, 4) . '.' .
                   substr($nopRaw, 17, 1);
        }
        return $nopRaw;
    }
}