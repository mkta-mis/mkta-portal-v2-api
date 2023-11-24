<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\roles;
use App\Models\userRoles;

use App\Models\permission;
use App\Models\permissionSet;

use App\Models\restriction_category;
use App\Models\restriction_product;
use App\Models\restriction_product_component;




use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\misc;
use App\Http\Controllers\ApiTokenController;

use App\Models\category;
use App\Models\product;

use App\Models\product_category;

use App\Models\product_components;
use DB;

class ctrl_Customers extends Controller
{
    #region Customers
    public static function customer_get( $type, $filter = array() ){
        switch (misc::text($type, 'strtolower')) {
            case 'exact-email':
                    $curUser = User::where('isClient', '!=', 0)->where('email', '=', $filter['email']);
                    if( $curUser->get()->count() != 1 ){
                        return false;
                    }
                    return $curUser->get()->first();
                break;
            case 'exact-token':
                    $curUser = User::where('userToken', '=', $filter['userToken']);
                    if( $curUser->get()->count() != 1 ){
                        return false;
                    }
                    return $curUser->get()->first();
                break;
            case 'exact-token-roles':
                $curUser = self::customer_get( 'exact-token', $filter);
                if( $curUser === false ){
                    return false;
                }
                $userRoles = userRoles::where('user_id', '=', $curUser->id)->get()->pluck('role_id');
                $Roles = roles::whereIn('id', $userRoles)->get();
                foreach ($Roles as $key => $value) {
                    $value['permissions'] = permissionSet::where('type', '=', 1)->where('reference_ID', '=', $value->id )->with(['permission_data'])->get();
                }
                return $Roles;
                break;
            case 'exact':
                    $curUser = User::where('isClient', '!=', 0)->where('id', '=', $filter['id']);
                    if( $curUser->get()->count() != 1 ){
                        return false;
                    }
                    return $curUser->get()->first();
                break;
            case 'search':
                return User::where('isClient', '!=', 0)->where($filter)->get();
                break;
            default:
                return User::where('isClient', '!=', 0)->get();
                break;
        }
    }
    public static function customer_create( $name, $email, $password, $isActive = 1 ){
        $emailTaken = User::where('email', '=', $email)->get()->count() > 0;
        if( $emailTaken ){
            return false;
        }
        return User::create(
            array(
                'userToken'     => ApiTokenController::createToken(),
                'name'          => $name,
                'email'         => $email,
                'password'      => Hash::make($password),
                'hasDashboard'  => 0,
                'isClient'      => 1,
                'isActive'      => $isActive,
            )
        );
    }
    
    #endregion
    #region Restriction
    public static function customer_restriction_get($customer_id, $type){
        switch ($type) {
            case 'categories':
                $restrictedCategories = restriction_category::where('customer_id', '=', $customer_id)->get()->pluck('category_id');
                return  category::with(['thumbnail'])->whereIn('id', $restrictedCategories)->get();
                break;
            case 'product-items':
                $restrictedProducts = restriction_product::where('customer_id', '=', $customer_id)->get()->pluck('product_id');
                return product::whereIn('id', $restrictedProducts)->with(['thumbnail', 'variants'])->get();
                break;
            case 'product-components':
                return restriction_product_component::where('customer_id', '=', $customer_id)->get()->pluck('component_key');
                break;
            case 'product-components-list':
                return product_components::distinct()->get(['key']);
                break;
            case 'pluck-categories':
                return restriction_category::where('customer_id', '=', $customer_id)->get()->pluck('category_id');
                break;
            case 'pluck-categories-products':
                $arr = array();
                $restrictedCategories = self::customer_restriction_get($customer_id, 'pluck-categories');
                $temp = category::whereIn('id', $restrictedCategories )->with(['sub_categories_pluck'])->get();
                foreach ( $temp as $key => $value) {
                    $temp2 = misc::webAdmin_Category_downline($value);
                    array_push($arr, $temp2);
                }
                $final = array();
                foreach ($arr as $key => $value) {
                    $final = array_merge($final , $value);
                }
                return product_category::whereIn('category_id', $final)->get()->pluck('product_id');
                break;
            case 'pluck-products':
                return restriction_product::where('customer_id', '=', $customer_id)->get()->pluck('product_id');
                break;
        }
    }
    public static function customer_restriction_append($customer_id, $type, $referenceValue){
        self::customer_restriction_remove($customer_id, $type, $referenceValue);
        switch ($type) {
            case 'categories':
                restriction_category::create(
                    array(
                        "customer_id" => $customer_id,
                        "category_id" => $referenceValue
                    )   
                );
                break;
            case 'product-items':
                restriction_product::create(
                    array(
                        "customer_id" => $customer_id,
                        "product_id" => $referenceValue
                    )
                );
                break;
            case 'product-components':
                restriction_product_component::create(
                    array(
                        "customer_id" => $customer_id,
                        "component_key" => $referenceValue
                    )
                );
                break;
        }
    }
    public static function customer_restriction_remove($customer_id, $type, $referenceValue){
        switch ($type) {
            case 'categories':
                restriction_category::where('customer_id', '=', $customer_id)->where('category_id', '=', $referenceValue)->delete();
                break;
            case 'product-items':
                restriction_product::where('customer_id', '=', $customer_id)->where('product_id', '=', $referenceValue)->delete();
                break;
            case 'product-components':
                restriction_product_component::where('customer_id', '=', $customer_id)->where('component_key', '=', $referenceValue)->delete();
                break;
        }
    }
    #endregion

}
