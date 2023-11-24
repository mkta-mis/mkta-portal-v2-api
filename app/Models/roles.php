<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class roles extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
    ];
    public function users()
    {
        return $this->hasMany(\App\Models\userRoles::class, 'role_id', 'id');
    }
    public function permissions(){
        return $this->hasMany(\App\Models\userRoles::class, 'role_id', 'id');
    }
}
