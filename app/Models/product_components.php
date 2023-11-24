<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_components extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'key',
        'title',
        'displayType',
        'content',
        'contentType',
        'isVisible',
        'creator_id',
    ];
    /**
     * Get the user associated with the product_components
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function keys_data()
    {
        return $this->hasOne(\App\Models\product_component_keys::class, 'key', 'key');
    }
}
