<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classified;


class Midia extends Model
{   
    
    
    use HasFactory;
    protected $table = 'midias';
    protected $fillable = ['title', 'url', 'file', 'status', 'type', 'user_id'];

 }