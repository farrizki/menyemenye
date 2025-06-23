<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PstDetail extends Model
{
    use HasFactory;

    // Arahkan model ini untuk menggunakan koneksi database Oracle
    protected $connection = 'oracle';

    // Tentukan nama tabel secara eksplisit
    protected $table = 'PST_DETAIL';

    // Oracle tidak menggunakan primary key auto-increment standar, 
    // jadi kita nonaktifkan agar tidak error.
    public $incrementing = false;

    // Jika nama primary key bukan 'id', tentukan di sini.
    // Karena kuncinya komposit, kita biarkan kosong atau null untuk query manual.
    protected $primaryKey = null; 
}
