<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefKecamatan extends Model
{
    use HasFactory;

    protected $connection = 'oracle';
    protected $table = 'REF_KECAMATAN'; // Pastikan nama tabel ini sesuai di Oracle, biasanya UPPERCASE
    protected $primaryKey = null; // Composite Primary Key
    public $incrementing = false;
    public $timestamps = false;

    // Casts untuk memastikan tipe data yang benar, sesuaikan jika ada yang berbeda
    protected $casts = [
        'kd_propinsi' => 'string',
        'kd_dati2' => 'string',
        'kd_kecamatan' => 'string',
        'nm_kecamatan' => 'string',
        // Tambahkan cast lain jika perlu
    ];
}