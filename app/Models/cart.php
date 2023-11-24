<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cart extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_ID',
        'product_ID',
        'quantity',
    ];
    public function product_item_data() {
        return $this->hasOne(\App\Models\product::class, 'id', 'product_ID');
    }
}
