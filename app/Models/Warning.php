<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;
    public $table = 'warnings';
    
    public function midias()
    {
        return $this->morphMany(Midia::class, 'mediable');
    }
}
