<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    public $table = 'areas';
    public function midias()
    {
        return $this->morphMany(Midia::class, 'mediable');
    }
}
