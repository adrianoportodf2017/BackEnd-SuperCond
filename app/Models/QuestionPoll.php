<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionPoll extends Model
{
    use HasFactory;
    protected $table = 'questions_polls';
    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'poll_id',
        'title',
        // outros campos que você deseja permitir preenchimento em massa
    ];
    public function answers()
    {
        return $this->hasMany(VotePoll::class, 'question_poll_id', 'id');
    }

 