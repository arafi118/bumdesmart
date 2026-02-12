<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    public function paymentsDebit()
    {
        return $this->hasMany(Payment::class, 'rekening_debit', 'kode');
    }

    public function paymentsKredit()
    {
        return $this->hasMany(Payment::class, 'rekening_kredit', 'kode');
    }

    public function balance()
    {
        return $this->hasOne(Balance::class, 'kode_akun', 'kode');
    }
}
