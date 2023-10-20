<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Classified;


class Folder extends Model
{
    protected $fillable = ['title', 'content', 'thumb', 'thumb_file', 'folder_id'];

    public function children()
    {
        return $this->hasMany(Folder::class, 'folder_id');
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }
}
