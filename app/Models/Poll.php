<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Poll extends Model
{
    use HasFactory;
    public function options()
    {
        return $this->hasMany(QuestionPoll::class, 'poll_id', 'id');
    }
}
