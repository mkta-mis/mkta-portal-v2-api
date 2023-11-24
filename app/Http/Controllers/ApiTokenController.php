<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\apiToken;

use Illuminate\Support\Str;

use Carbon\Carbon;
use App\Http\Controllers\misc;


class ApiTokenController extends Controller
{
        public static function createToken(){
                $token = '';
                $length = 32; // Length of the token
                do {
                        $token = Str::random($length);
                } while (apiToken::where('tokenString', $token)->exists()); // Check if the token already exists in the database
                return $token;
        }
        public static function generateUserToken($tokenType, $reference_ID, $doesExpire = true ){

                switch ( misc::text( $tokenType, 'strtolower' ) ) {
                        case 'user'     : $tokenType = 1; break;
                        case 'category' : $tokenType = 2; break;
                        case 'product'  : $tokenType = 3; break;
                        default: $tokenType = 0; break;
                }

                $tokenString = self::createToken();

                apiToken::where('reference_ID', '=', $reference_ID)
                        ->where('tokenType', '=', $tokenType)
                        ->delete();

                apiToken::create(
                        array(
                                "tokenString"           =>      $tokenString,
                                "tokenType"             =>      $tokenType,
                                "doesExpire"            =>      $doesExpire ? 1 : 0 ,
                                "reference_ID"          =>      $reference_ID,
                                "tokenExpiration"       =>      $doesExpire ? Carbon::now()->addDays(10) : NULL
                        )
                );
                return $tokenString;
        }
        public static function IdentifyToken($tokenString){
                $apiToken = apiToken::where('tokenString', '=', $tokenString);
                if($apiToken->count() == 0){
                        return array(
                                "isValid"       => false,
                                "tokenType"     => null,
                                "reference_ID"  => -1,
                                "isExpired"     => false,
                        );
                }else{

                        $apiToken = $apiToken->first();
                        $isExpired = Carbon::now() < $apiToken->tokenExpiration;
                        return array(
                                "isValid"       => true,
                                "tokenType"     => $apiToken->tokenType,
                                "reference_ID"  => $apiToken->reference_ID,
                                "isExpired"     => $isExpired,
                        );
                }
                
        }
        public static function destroy_Token($tokenString){

        }
        public static function isValid_userToken($tokenString){
                $apiToken = apiToken::where('tokenString', '=', $tokenString)->where('tokenType', '=', 1);
                return $apiToken->get()->count() == 1;
        }
        
}
