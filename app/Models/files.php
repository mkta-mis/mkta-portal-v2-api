<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class files extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'filename',
        'type',
        'uploader_id',
    ];
    public function user_data(){ 
        return $this->hasOne(\App\Models\User::class, 'id', 'uploader_id'); 
    }

}
