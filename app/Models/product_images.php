<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_images extends Model
{
    use HasFactory;
    protected $fillable = [
        "index",
        "product_id",
        "file_id",
    ];
    public function thumbnail()
    {
        return $this->hasOne(\App\Models\files::class, 'id', 'file_id')->first();
    }
    public function file_data()
    {
        return $this->hasOne(\App\Models\files::class, 'id', 'file_id');
    }
}
