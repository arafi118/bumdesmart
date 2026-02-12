<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunLevel1 extends Model
{
    use HasFactory;

    public function akunLevel2()
    {
        return $this->hasMany(AkunLevel2::class, 'parent_id', 'id');
    }
}
