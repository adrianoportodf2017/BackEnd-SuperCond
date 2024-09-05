<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPoll extends Model
{
    use HasFactory;
    protected $table = 'questions_polls';
    
    public function answers()
    {
        return $this->hasMany(VotePoll::class, 'question_poll_id', 'id');
    }

}
