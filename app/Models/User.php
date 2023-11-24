<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'userToken',
        'name',
        'email',
        'password',
        'isClient',
        'isActive',
        'hasDashboard'
    ];

    public function roles(){
        return $this->hasMany(\App\Models\userRoles::class, 'user_id', 'id')->with(['role_data']);
    }
    public function permisions(){
        return $this->hasMany(\App\Models\permissionSet::class, 'reference_ID', 'id')->where('type', '=', 3)->with(['permission_data']);
    }

    public function cart(){
        return $this->hasMany(\App\Models\cart::class, 'user_ID', 'id')->with(['product_item_data']);
    }
    
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
