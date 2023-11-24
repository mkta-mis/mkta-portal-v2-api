<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiTokenController;


class misc extends Controller
{
    public static function IdentifyToken($tokenString){
        return ApiTokenController::IdentifyToken($tokenString);
    }
    public static function text($str, $options = 'trim'){
        switch($options){
            case 'strtolower':
                return trim((strtolower($str)));
                break;
            case 'strtoupper':
                return trim((strtoupper($str)));
                break;
            case 'ucfirst':
                return trim(ucfirst(strtolower($str)));
                break;
            case 'ucwords':
                return trim(ucwords(strtolower($str)));
                break;
            default:
                return trim((($str)));    
                break;
        }
    }
    public static function webAdmin_Category_downline($data){
        $full_line = array();
        if (count( $data['sub_categories_pluck'] ) > 0) {
            foreach ($data['sub_categories_pluck'] as $key => $value) {
                $temp = self::webAdmin_Category_downline($value);
                foreach ($temp as $key => $value) {
                    array_push($full_line, $value);
                }
            }
        }
        array_push($full_line, $data['id']);
        return $full_line;
    }
}
