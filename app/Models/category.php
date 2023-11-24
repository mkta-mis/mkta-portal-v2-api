<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'urlKey',
        'description',
        'isVisible',
        'parent_id',
        'file_id',
        'creator_id'
    ];
    public function creator_data(){ return $this->hasOne(\App\Models\User::class, 'id', 'creator_id'); }
    
    /**
     * Get the user associated with the category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function thumbnail(){ 
        return $this->hasOne(\App\Models\files::class, 'id', 'file_id');
    }
    public function sub_categories_pluck()
    {
        return $this->hasMany(\App\Models\category::class, 'parent_id', 'id')->with(['sub_categories_pluck']);
    }
    public function products()
    {
        return $this->hasManyThrough(
            \App\Models\product::class, 
            \App\Models\product_category::class, 
            'category_id', 'id')
            ->where('parent_id', '=', 0)
            ->with(['variants'])
            ->select('products.id','code', 'name');;
    }
    public function parent_data()
    {
        return $this->hasOne(\App\Models\category::class, 'id', 'parent_id'); 
    }
    public function full_data()
    {
        return $this->hasMany(\App\Models\category::class, 'parent_id', 'id')->with(['full_data']); 
    }

}
