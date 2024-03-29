<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostAndFound extends Model
{
    use HasFactory;
    protected $table = 'lost_end_found';
    public function midias()
    {
        return $this->morphMany(Midia::class, 'mediable');
    }

}
