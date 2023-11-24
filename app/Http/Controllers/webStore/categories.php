<?php

namespace App\Http\Controllers\webStore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\category;

class categories extends Controller
{
    public function create_Categories(){

    }
    public static function get_Categories(){
        return category::with(['thumbnail'])->where('parent_id', '=', 0)->get();
    }
    public static function get_Categories_Search(){
        return category::with(['thumbnail'])->where('parent_id', '=', 0)->get();
    }
   
}
