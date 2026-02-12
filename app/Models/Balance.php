<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    public function account()
    {
        return $this->belongsTo(Balance::class, 'kode_akun', 'kode_akun');
    }
}
