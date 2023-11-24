<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_category extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'category_id',
        'isRestricted',
        'creator_id',
    ];
    public function category_data()
    {
        return $this->hasOne(\App\Models\category::class, 'id', 'category_id'); 
    }
}
