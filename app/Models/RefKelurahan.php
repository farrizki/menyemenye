<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefKelurahan extends Model
{
    use HasFactory;

    protected $connection = 'oracle';
    protected $table = 'REF_KELURAHAN'; // Pastikan nama tabel ini sesuai di Oracle, biasanya UPPERCASE
    protected $primaryKey = null; // Composite Primary Key
    public $incrementing = false;
    public $timestamps = false;

    // Casts untuk memastikan tipe data yang benar, sesuaikan jika ada yang berbeda
    protected $casts = [
        'kd_propinsi' => 'string',
        'kd_dati2' => 'string',
        'kd_kecamatan' => 'string',
        'kd_kelurahan' => 'string',
        'nm_kelurahan' => 'string',
        // Tambahkan cast lain jika perlu
    ];
}