<?php

// app/Models/Visit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'url',
        'visited_at',
    ];

    // Relacionamento com o usuário (caso o usuário esteja logado)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
