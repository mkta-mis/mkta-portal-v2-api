<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userRoles extends Model
{
    use HasFactory;
    protected $fillable = [
        'role_id',
        'user_id',
    ];

    public function role_data() {
        return $this->hasOne(\App\Models\roles::class, 'id', 'role_id')->select('title','description');
    }
}
