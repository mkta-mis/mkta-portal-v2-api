<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'description',
        'parent_id',
        'file_id',
        'isVisible',
        'creator_id',

        "Dimension_Raw_L",
        "Dimension_Raw_W",
        "Dimension_Raw_H",
        
        "Dimension_Packed_L",
        "Dimension_Packed_W",
        "Dimension_Packed_H",

        "Weight_Net",
        "Weight_Gross",

        "Volume_Raw",
        "Volume_Packed",

    ];
    public function creator_data(){ 
        return $this->hasOne(\App\Models\User::class, 'id', 'creator_id'); 
    }
    public function thumbnail(){ 
        return $this->hasOne(\App\Models\files::class, 'id', 'file_id'); 
    }
    public function variants() { 
        return $this->hasMany(\App\Models\product::class, 'parent_id', 'id')->with(['thumbnail', 'components']); 
    }
    public function product_categories(){
        return $this->hasManyThrough(           
                                            \App\Models\category::class, // Island Table
                                            \App\Models\product_category::class, // Bridge Table
                                            'product_id', // FK in Bridge Table of the Main Table
                                            'id', // PK on Island Table ID
                                            'id', // PK on Main ID
                                            'category_id' // FK in Bridge Table of the Island Table
        )->select('categories.id');
    }
    public function categories()
    {
        return $this->hasMany(\App\Models\product_category::class, 'product_id', 'id')->with('category_data')->select('*');
    }
    public function image360()
    {
        return $this->hasMany(\App\Models\product_images::class, 'product_id', 'id')->orderBy('index', 'ASC')->with(['file_data']);
    }
    public function components()
    {
        return $this->hasMany(\App\Models\product_components::class, 'product_id', 'id')->with(['keys_data'])->where('isVisible', 1);
    }
    
}
