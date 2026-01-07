<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'customer_group_id',
        'kode_pelanggan',
        'nama_pelanggan',
        'no_hp',
        'alamat',
        'username',
        'password',
        'limit_hutang',
    ];

    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
