<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class restriction_product_component extends Model
{
    use HasFactory;
    protected $fillable = [ 
        "customer_id",
        "component_key",
    ];
}
