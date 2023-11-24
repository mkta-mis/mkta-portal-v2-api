<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class restriction_category extends Model
{
    use HasFactory;
    protected $fillable = [ 
        "customer_id",
        "category_id",
    ];
}
