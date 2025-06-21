<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatObjekPajak extends Model
{
    use HasFactory;

    protected $connection = 'oracle';
    protected $table = 'DAT_OBJEK_PAJAK'; // Pastikan nama tabel ini sesuai di Oracle, biasanya UPPERCASE
    protected $primaryKey = null; // Composite Primary Key
    public $incrementing = false;
    public $timestamps = false;

    // Casts untuk memastikan tipe data yang benar, sesuaikan jika ada yang berbeda
    protected $casts = [
        'kd_propinsi' => 'string',
        'kd_dati2' => 'string',
        'kd_kecamatan' => 'string',
        'kd_kelurahan' => 'string',
        'kd_blok' => 'string',
        'no_urut' => 'string',
        'kd_jns_op' => 'string',
        'jalan_op' => 'string',
        'rw_op' => 'string',
        'rt_op' => 'string',
        // Tambahkan cast untuk kolom lain yang mungkin akan diakses
    ];
}