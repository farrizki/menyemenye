<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Penggabungan extends Model
{
    use HasFactory;

    protected $table = 'penggabungan_sppt';
    protected $guarded = ['id'];

    // Accessor untuk NOP yang diformat
    public function getFormattedNopAttribute()
    {
        $nop = $this->kd_propinsi . $this->kd_dati2 . $this->kd_kecamatan . $this->kd_kelurahan . $this->kd_blok . $this->no_urut . $this->kd_jns_op;
        if (strlen($nop) == 18) {
            // Menggunakan helper Str untuk menyisipkan titik
            return Str::of($nop)->insert(2, '.')->insert(5, '.')->insert(9, '.')->insert(13, '.')->insert(17, '.')->insert(22, '.');
        }
        return $nop;
    }
}
