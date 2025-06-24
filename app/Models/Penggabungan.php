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
            // PERBAIKAN: Menggunakan substr() yang lebih kompatibel
            return substr($nop, 0, 2) . '.' . substr($nop, 2, 2) . '.' . substr($nop, 4, 3) . '.' .
                   substr($nop, 7, 3) . '.' . substr($nop, 10, 3) . '.' . substr($nop, 13, 4) . '.' .
                   substr($nop, 17, 1);
        }

        return $nop;
    }
}
