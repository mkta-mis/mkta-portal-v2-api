<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Http\Controllers\ApiTokenController;


class AuthenticationController extends Controller
{

        public function UserLogIn(Request $req)
        {
                if (auth()->attempt($req->toArray())) {
                        $res['code'] = 200;
                        $res['message'] = 'Welcome ' . auth()->user()->name;
                        $user = auth()->user();
                        $res['userToken'] = auth()->user()->userToken = ApiTokenController::generateUserToken('user', auth()->user()->id, $doesExpire = true );
                        auth()->user()->save();
                        $res['name']    = auth()->user()->name;
                        $res['email']   = auth()->user()->email;
                        $res['restriction_product_component_keys'] = array();
                        $res['hasDashboard'] =  auth()->user()->hasDashboard;
                        return $res;
                } else {
                        $res['code'] = 202;
                        $res['message'] = 'Username or password is not recognized.';
                        return $res;
                }
        }
        

}
