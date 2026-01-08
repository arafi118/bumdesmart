<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'kode_supplier',
        'nama_supplier',
        'no_hp',
        'alamat',
        'email',
    ];
}
