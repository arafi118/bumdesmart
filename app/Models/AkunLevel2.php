<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunLevel2 extends Model
{
    use HasFactory;

    public function akunLevel3()
    {
        return $this->hasMany(AkunLevel3::class, 'parent_id', 'id');
    }
}
