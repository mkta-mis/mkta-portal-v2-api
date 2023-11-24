<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class apiToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'tokenString',
        'tokenType',
        'doesExpire',
        'reference_ID',
        'tokenExpiration'
    ];
    /*
        Token Type
            0 - Null Token
            1 - User Token
            2 - Category Token
            3 - Product Item Token

    */
}
