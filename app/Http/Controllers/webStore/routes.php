<?php

namespace App\Http\Controllers\webStore;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\webStore\categories;

use App\Http\Controllers\misc;
use App\Http\Controllers\ctrl_Customers;


use App\Models\category;
use App\Models\product;
use App\Models\product_category;

use Illuminate\Support\Facades\DB;

class routes extends Controller
{
    public function webStore_Index (Request $req){
        $curCustomer = ctrl_Customers::customer_get('exact-token', array( 'userToken'=> $req->Token ));
        if( $curCustomer === false ){
            return array(
                "code" => 202, 
                "message" => "Invalid Customer Token String"
            );
        }
        $restricted_Categories = ctrl_Customers::customer_restriction_get($curCustomer->id, 'pluck-categories');
        return array(
            "code" => 200,
            "data" => category::with(['thumbnail'])->where('parent_id', '=', 0)->where('isVisible',1)->whereNotIn('id', $restricted_Categories)->get()
        );
    }
    public function webStore_Category(Request $req, $category_slug){
        // Token : localStorage.getItem('userToken')

        $parent_Category = category::where('urlKey', $category_slug);
        if( $parent_Category->get()->count() == 0 ){
            return array(
                "code"      => 404,
                "message"   => "Category not found."
            );
        }else{
            $parent_Category = $parent_Category->get()->first();
            return array(
                "code"      => 200,
                "subCat"    => category::where('parent_id', $parent_Category->id)->get()
            );
        }
    }
    public function webStore_Category_Products(Request $req, $category_slug){
        $parent_Category = category::where('urlKey', $category_slug);
        if( $parent_Category->get()->count() == 0 ){
            return array(
                "code"      => 404,
                "message"   => "Category not found."
            );
        }else{
            

            // DB::connection()->enableQueryLog();
            $parent_Category = $parent_Category->get()->first();
            $categories =  category::where('parent_id', $parent_Category->id)->get()->pluck('id');
            $products = product_category::whereIn('category_id', $categories)->orWhereIn('category_id', [$parent_Category->id])->get()->pluck('product_id');


            $products = product::whereIn('id', $products)->with(['variants', 'thumbnail', 'categories'])->get();
            // $queries = DB::getQueryLog();
            return array(
                "code"      => 200,
                "products"    => $products,
                // "query" => $queries
            );
        }
    }
    public function webStore_Product_360(Request $req, $code){
        $products = product::where('code', '=', $code)->with(['image360', 'thumbnail'])->get();
        $with360 = false;
        $images = array();
        if( $products->count() > 0 ){
            $products = $products->last();
            $with360 = !false;
            $images = $products->image360;
            if( count($images) == 0 ){
                $images = $products->thumbnail['filename'];
            }
        }
        return array(
            "code"      => $with360 ?  200 : 203,
            "data"      =>  $images
        );
    }
    public function webStore_Product_Components(Request $req, $code){
       
    }
    public function webStore_Product_List(Request $req){
        $curCustomer = ctrl_Customers::customer_get('exact-token', array( 'userToken'=> $req->Token ));
        if( $curCustomer === false ){
            return array(
                "code" => 202, 
                "message" => "Invalid Customer Token String"
            );
        }
        $query = "";
        $restricted_Products = ctrl_Customers::customer_restriction_get($curCustomer->id, 'pluck-products');
        $restricted_Categories_Products = ctrl_Customers::customer_restriction_get($curCustomer->id, 'pluck-categories-products');
        $products=array();
        switch (misc::text($req->mode)) {
            case 'all':
                $products = product::whereNotIn('id', $restricted_Products)->where('parent_id', '=', 0)->with(['variants', 'thumbnail', 'categories'])->get();    
                break;
            default:
            
                
                $products = product::where('parent_id', '!=', 0)->where('code', 'LIKE', '%'.(misc::text($req->text)).'%')->orWhere('name', 'LIKE', '%'.(misc::text($req->text)).'%')->get()->pluck('parent_id');
                DB::connection()->enableQueryLog();
                
                // restricted_Categories_Products
                $products = product::whereNotIn('id', $restricted_Products)
                                   ->whereNotIn('id', $restricted_Categories_Products)  
                                   ->whereIn('id', $products)
                                   ->where('parent_id','=', 0)
                                   ->with(['variants', 'thumbnail', 'categories'])
                                   ->get()
                                   ;
                $query = DB::getQueryLog();

                break;
        }
        return array(
            "code"      => 200,
            "data"    => $products,
        );
    }
}
