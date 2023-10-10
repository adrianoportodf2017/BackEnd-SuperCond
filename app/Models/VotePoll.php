<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotePoll extends Model
{
    use HasFactory;
    protected $table = 'votes_polls';

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_poll_id', 'id');
    }
}
