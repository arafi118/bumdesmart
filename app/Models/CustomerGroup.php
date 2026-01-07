<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'nama_group',
        'deskripsi',
        'diskon_persen',
    ];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
