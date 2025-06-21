<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DendaAdministratif extends Model
{
    use HasFactory;

    protected $connection = 'oracle';
    protected $table = 'denda_administratif';
    public $timestamps = true;

    protected $fillable = [
        'kd_propinsi', 'kd_dati2', 'kd_kecamatan', 'kd_kelurahan', 'kd_blok',
        'no_urut', 'kd_jns_op', 'thn_pajak_sppt',
        'nm_wp_sppt', 'alamat_wp', 'letak_op',
        'pokok', 'denda', 'jumlah_pajak', 'sanksi_administratif', 'yang_harus_dibayar',
        'no_sk', 'tgl_sk', 'berkas_path',
        'tgl_jatuh_tempo_baru', 'operator',
        'tgl_jatuh_tempo_sppt_lama', // PENTING: Kolom ini untuk menyimpan Tgl JTT lama
    ];

    protected $casts = [
        'tgl_sk' => 'date',
        'tgl_jatuh_tempo_baru' => 'date',
        'tgl_jatuh_tempo_sppt_lama' => 'date', // PENTING: Cast sebagai date
        'pokok' => 'float',
        'denda' => 'float',
        'jumlah_pajak' => 'float',
        'sanksi_administratif' => 'float',
        'yang_harus_dibayar' => 'float',
    ];

    // Accessor untuk memformat NOP
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