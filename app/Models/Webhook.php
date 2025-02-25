<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'event', 'url', 'description', 'headers', 'is_active'];
 
}
