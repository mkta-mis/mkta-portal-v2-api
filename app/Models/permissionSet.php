<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class permissionSet extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'reference_ID',
        'key'
    ];
    /*
        Token Type
            0 - Null
            1 - Roles
            2 - Groups
            3 - Users
            4 - Product Item Components

    */
    public function permission_data() {
        return $this->hasOne(\App\Models\permission::class, 'key', 'key')->select('key', 'title', 'description', 'isDefault');
    }
}
