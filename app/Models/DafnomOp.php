<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DafnomOp extends Model
{
    protected $connection = 'oracle';
    protected $table = 'DAFNOM_OP';
    public $timestamps = false;
    // Tambahkan primary key jika tabelnya punya
    // protected $primaryKey = '...'; 
}