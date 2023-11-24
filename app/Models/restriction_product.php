<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class restriction_product extends Model
{
    use HasFactory;
    protected $fillable = [ 
        "customer_id",
        "product_id",
    ];
}
