<?php

namespace App\Http\Controllers\webAdmin;

use App\Http\Controllers\Controller;



use App\Http\Controllers\misc;
use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\ctrl_Files;
use App\Http\Controllers\ctrl_products;
use App\Http\Controllers\ctrl_User;
use App\Http\Controllers\ctrl_Customers;


use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


use App\Models\roles;
use App\Models\permission;
use App\Models\product_components;
use App\Models\product_component_keys;

use App\Models\category;
use App\Models\product;
use App\Models\product_category;

use DB;

class z_routes extends Controller
{
    #region Roles
    public function db_Roles(Request $req){
        return array(
            "code" => 200,
            "data" => roles::with(['users'])->get()
        );
    }
    public function webAdmin_Roles_get(Request $req){
        if( !$req->has('mode') ){
            return array(
                "code" => 200,
                "type" => "list",
                "results" => ctrl_User::role_get('all')
            );
        }
        return array(
            "code" => 200,
            "results" => ctrl_User::role_get(
                                                misc::text($req->mode , 'strtolower'), 
                                                array(
                                                    "id" => $req->roleIndex
                                                    )
                                                )
        );
    }
    public function webAdmin_Roles_Change(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }
        $data = array();
        switch (misc::text($req->action , 'strtolower')) {
            case 'register':
                $data = ctrl_User::role_create($req->data['title'], $req->data['description']);
                if( $data === false ){
                    return array(
                        "code" => 202,
                        "message" => "Roles is already in use."
                    );
                }
                break;
            case 'update':
                $data = ctrl_User::role_update( $req->data['roleIndex'] , $req->data['title'], $req->data['description']);
                if( $data === false ){
                    return array(
                        "code" => 202,
                        "message" => "Roles is already in use."
                    );
                }
                break;
            case 'append-permission':
                ctrl_User::role_permission_append ($req->key , $req->roleIndex );
                $data = array(
                    "key" => $req->key,
                    "roleIndex" => $req->roleIndex
                );
                break;
            case 'remove-permission':
                ctrl_User::role_permission_remove( $req->key , $req->roleIndex );
                $data = array(
                    "key" => $req->key,
                    "roleIndex" => $req->roleIndex
                );
                break;
            default: 
                break;
        }
        return array(
            "code" => 200,
            "message" => "Roles Action: ".$req->action,
            "data" =>$data
        );
    }
    #endregion
    #region Permission
    public function webAdmin_Permissions_get(Request $req){
        if( !$req->has('mode') ){
            return array(
                "code" => 200,
                "results" => ctrl_User::permission_get('all')
            );
        }
        return array(
            "code" => 200,
            "results" => ctrl_User::permission_get( $req->mode , array( "id" => $req->permission_id ))
        );
        

    }
    public function webAdmin_Permissions_Change(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }
        $data = array();
        switch (misc::text($req->action , 'strtolower')) {
            case 'register':
                $data = ctrl_User::permission_create($req->data['key'], 
                $req->data['title'], 
                $req->data['description'], 
                $req->data['isDefault']);
                if( $data === false ){
                    return array(
                        "code" => 202,
                        "message" => "Permission key is already in use"
                    );
                }
                break;
            case 'update':
                $data = ctrl_User::permission_update( 
                                                        $req->permission_key , 
                                                        $req->data
                                                    );
                if( $data === false ){
                    return array(
                        "code" => 202,
                        "message" => "Roles is already in use."
                    );
                }
                break;
            case 'remove-permission':
                $data = ctrl_User::permission_delete($key);
                break;
            default: 
                break;
        }
        return array(
            "code" => 200,
            "message" => "Roles Action: ".$req->action,
            "data" =>$data
        );
    }
    #endregion
    #region Products
        #region Product Full Info
        public function webStore_Product_List(Request $req){
            $curUser = ctrl_User::user_get('exact-token', array('userToken'=>$req->userToken));
        if( $curUser === false ){
            return array(
                "code" => 202,
                "message" => "Token not found"
            );
        }
            $products=array();
            switch (misc::text($req->mode)) {
                case 'all':
                    $products = product::where('parent_id', '=', 0)->with(['variants', 'thumbnail', 'categories'])->get();    
                    break;
                default:
                    $products = product::where('parent_id', '!=', 0)->where('code', 'LIKE', '%'.(misc::text($req->text)).'%')->orWhere('name', 'LIKE', '%'.(misc::text($req->text)).'%')->get()->pluck('parent_id');
                    $products = product::whereIn('id', $products)->with(['variants', 'thumbnail', 'categories'])->get();
                    break;
            }
            return array(
                "code"      => 200,
                "data"    => $products
            );
        }
        public function webStore_Product_Index(Request $req, $code){
            $products = product::where('code', $code)->where('parent_id', 0)->with(['variants', 'thumbnail', 'components', 'product_categories'])->get()->first();
            $products['category_trace'] = array();
            if( count($products['product_categories'] ) > 0 ){
                $products['category_trace'] = self::webAdmin_Category_Root($products['product_categories'][0]->id);
            }

            
            return array(
                "code"      => 200,
                "data"    => $products,

            );
        }
        public function webStore_Product_Thumbnail_update(Request $req){
            $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
            if( $tokenData['isValid'] == 0){
                return array(
                    "code" => 202, 
                    "message" => "Invalid Token String"
                );
            }
            if( !$req->has('image') ){
                return array(
                    "code" => 202,
                    "message" => "File not included in the submitted form."
                );
            }
            if( !$req->hasFile('image') ){
                return array(
                    "code" => 202,
                    "message" => "File not included in the submitted form."
                );
            }

            $curFile = $req->file('image');
            $fileName =  $req->file('image')->getClientOriginalName();
            $ext = $req->file('image')->getClientOriginalExtension();
            $type = $req->file('image')->getClientMimeType();

            $file_name = $req->file('image')->getClientOriginalName();
            $generated_new_name = bin2hex($fileName).'.'.$ext;
            
            $res['message'] = 'File upload success';
            $curFile = ctrl_Files::file_create(
                                            $fileName, 
                                            $fileName.".".$ext, 
                                            $type, 
                                            $tokenData['reference_ID']
                                        );
            $generated_new_name = bin2hex($curFile->id).".".$ext;
            $curFile = ctrl_Files::file_update($curFile->id, 
                                                    array(
                                                        "filename"=>$generated_new_name
                                                    )
                                                );
            $req->file('image')->storeAs( ('public/resources/'), $generated_new_name);


            ctrl_products::product_update(
                $req->product_id, array(
                    "file_id" => $curFile->id)
            );                               


            return array(
                "code" => 200,
                "message" => "Product Thumbnail updated.",
            );
        }
        public function webStore_Product_Info_Update(Request $req){
            $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
            if( $tokenData['isValid'] == 0){
                return array(
                    "code" => 202, 
                    "message" => "Invalid Token String"
                );
            }
            $product = product::where('id', '=', $req->product_id);
            if(  $product->get()->count() == 0){
                return array(
                    "code" => 202, 
                    "message" => "Product Not Found"
                );
            }
            $product = $product->get()->first();
            ctrl_products::product_update($product->id, $req->data);
            if( $req->has('category_data') ){
                    ctrl_products::product_category_change($product->id, $req->category_data);
            }
            return array(
                "code" => 200,
                "message" => "Product Information Updated"
            );
        }
        #endregion
        #region Product Creation
        public function webAdmin_Product(Request $req){
            $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
            if( $tokenData['isValid'] == 0){
                return array(
                    "code" => 202, 
                    "message" => "Invalid Token String"
                );
            }
            $res = [];
            switch (misc::text($req->action , 'strtolower')) {
                case 'append-variant':
                    $continue =  product::where("code", '=', $req->data['code'])->get()->count() == 0;
                    if( $continue ){
                        $isSave = ctrl_products::product_createVariant( 
                            $req->mainProduct, 
                            $req->data['code'], 
                            $req->data['name'], 
                            $tokenData['reference_ID'], 
                            $file_id = 1, 
                            $isVisible = 1 );
                            return array(
                                "code" => 200,
                                "message" => "Product Variant is registered"
                            );
                    }else{
                        return array(
                            "code" => 202,
                            "message" => "Product Code is already in use"
                        );
                    }
                    break;
                case 'new-product':
                    $continue =  product::where("code", '=', $req->data['code'])->get()->count() == 0;
                    if( $continue ){
                        ctrl_products::product_createParent(
                            $req->data['code'], 
                            $req->data['name'], 
                            $req->data['description'], 
                            $tokenData['reference_ID'], 
                            $file_id = 1, 
                            $isVisible = 1 );
                            return array(
                                "code" => 200,
                                "message" => "Product Item is registered"
                            );
                    }else{
                        return array(
                            "code" => 202,
                            "message" => "Product Code is already in use"
                        );
                    }
                    break;
                default:
                    break;
            }
        }
        #endregion
        #region Product Image 360 
        public function webAdmin_Product_Image360(Request $req){
            return array(
                "code" => 200,
                "results" => ctrl_products::product_image360($req->product_id)
            );
        }
        public function webAdmin_Product_Image360_action(Request $req){
            switch (misc::text($req->action , 'strtolower')) {
                case 'up':
                    ctrl_products::product_image360_action_moveUp($req->product_image_id);
                    break;
                case 'down':
                    ctrl_products::product_image360_action_moveDown($req->product_image_id);
                    break;
                case 'delete':
                    ctrl_products::product_image360_action_Delete($req->product_image_id);
                    break;
                case 'append':
                    ctrl_products::product_images360_action_CollectionAppend($req->product_id, $req->file_data);
                    break;
                case 'replace':
                    ctrl_products::product_images360_action_CollectionReplace($req->mainFile, $req->file_id);
                    break;
                default:
                    
                    break;
            }
            return array(
                "code" => 200,
                "results" => $req->action
            );
        }
        #endregion
        #region Related Products
        public function webStore_Product_related(Request $req, $code, $ref){
                    $products = product::where('code', $code)->where('parent_id', 0);
                    if( $products->get()->count() <= 0 ){
                              return array(
                                        "code" => 202,
                                        "message" => "Product Code not found"
                              );
                    }
                    $products = $products->get()->first();
                    $relation_type = -1;
                    switch (misc::text($ref, 'strtolower')) {
                              case 'group':
                                        $relation_type = 0;
                                        break;
                              case 'related':
                                        $relation_type = 1;
                                        break;
                    }
                    if( $relation_type == -1 ){
                              return array(
                                        "code" => 202,
                                        "message" => "No related products"
                              );
                    } 
                    return array(
                              "code" => 200,
                              "results" => ctrl_products::product_get_related( $products->id ,$relation_type),
                    ); 
        }
        public function webStore_Product_related_actions(Request $req){
                    $isDone = false;
                    switch (misc::text($req->mode)) {
                              case 'append':
                                        $isDone = ctrl_products::product_related_add($req->product_id, $req->target_id, $req->relation_type);
                                        break;
                              case 'remove':
                                        $isDone = ctrl_products::product_related_remove($req->product_id, $req->target_id, $req->relation_type); 
                                        break;
                    }
                    return array(
                              "code" => $isDone ? 200 : 202,
                              "message" => $isDone ? 'Product related activity done' : 'Something went wrong',
                    );
        }
        #endregion
        #region Product Components
        public function product_components_get(Request $req){
            return array(
                "code" => 200,
                "components" => product_components::where('product_id', '=', $req->product_id)->with(['keys_data'])->get()
            );
        }
        public function product_components_create(Request $req){
            $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
            if( $tokenData['isValid'] == 0){
                return array(
                    "code" => 202, 
                    "message" => "Invalid Token String"
                );
            }

            product_components::create(
                array(
                    'title'                 => $req->title,
                    'product_id'            => $req->product_id,
                    'key'                   => misc::text($req->key, 'strtolower'),
                    'displayType'           => $req->displayType,
                    'content'               => $req->contentType == 'JSON' ? json_encode($req->content) : $req->content,
                    'contentType'           => $req->contentType,
                    'creator_id'            => $tokenData['reference_ID'],
                )
            );
            return array(
                "code" => 200,
                "message" => "Product Components Added"
            );
        }
        public function product_components_update(Request $req){
            $curComponents = product_components::where('id', '=', $req->id);
            if( $curComponents->get()->count() != 1 ){
                return array( 
                    "code" => 202,
                    "message" => "No Components Found"
                );
            }
            $curComponents = $curComponents->get()->first();
            foreach ($req->data as $key => $value) {
                if( $req->contentType == "JSON" ){
                    $curComponents[$key] = json_encode($value);
                }else{
                    $curComponents[$key] = $value;   
                }
            }
            $curComponents->save();
            return array(
                "code" => 200,
                "message" => "Components Updated"
            );
        }
        public function product_components_remove(Request $req){
            product_components::where('id', '=', $req->id)->delete();
            return array(
                "code" => 200,
            );
        }
        #endregion
    #endregion
    #region Category
    public function webAdmin_Category(Request $req){
        if( $req->has('withs') ){
            return array(
                "code" => 200,
                "data" => category::where('parent_id', '=', '0')->with($req->withs)->get()
            );    
        }
        return array(
            "code" => 200,
            "data" => category::where('parent_id', '=', '0')->with(['full_data', 'thumbnail'])->get()
        );
    }
    public function webAdmin_Category_Change(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }
        $data = array();
        switch (misc::text($req->action , 'strtolower')) {
            case 'append':
                $data = ctrl_products::Category_create(
                    $req->data['name'],
                    $req->data['urlKey'],
                    $req->data['description'],
                    $req->data['parent_id'],
                    $tokenData['reference_ID'],
                    $req->data['file_id']
                );
                $data = ctrl_products::Categeory_get('exact', 
                    array(
                        "id"=>$req->data['parent_id']
                    )
                );
                break;
            case 'update':
                $data = ctrl_products::Category_update( $req->category_id, $req->data );
                break;
            default: 
                return array(
                    "code" => 404,
                    "message" => "Action not Found: ".$req->action
                );
                break;
        }
        return array(
            "code" => 200,
            "message" => "Action Taken".$req->action,
            "data" =>$data
        );

    }
    public function webAdmin_Category_Root($parent_id){
        $footSteps = array();
        $category = collect(category::get());
        while( $parent_id > 0 ){
            array_push( $footSteps, $parent_id );
            $cat = $category->where('id', '=', $parent_id);
            if($cat->count() > 0){
                $cat = $category->where('id', '=', $parent_id)->first();
                $parent_id = $cat->parent_id;
            }else{
                $parent_id = -1;
            }
        }
        return $footSteps;
    }
    public function webAdmin_Category_downline($data){
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
    #endregion
    #region Users
    public function webAdmin_Users_Reset_Password(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }
        $password = Str::random(10);
        $curUser = ctrl_User::user_get('exact-token', array('userToken'=>$req->userToken));
        if( $curUser === false ){
            return array(
                "code" => 202,
                "message" => "Token not found"
            );
        }
        ctrl_User::user_update($curUser->id, array( 'password' => Hash::make($password) ));
        return array(
            "code" => 200,
            "results" => $password
        );
    }
    public function webAdmin_Users_Registration(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }

        $password = Str::random(10);
        $isSuccess = ctrl_User::user_create( 
                                                $req->data['name'], 
                                                $req->data['email'], 
                                                $password, 
                                                $hasDashboard = 1, 
                                                $isClient = 0, 
                                                $isActive = 0 );
        if( $req->has('role_data') ){
            ctrl_User::user_set_role( $isSuccess->id, $req->role_data );
        }
        if( !$isSuccess ){
            return array(
                "code"      => 202,
                "message"   => ""
            );
        }
        return array(
            "code"      => 200,
            "user_data" => array(
                "name"=> $req->data['name'],
                "email"=> $req->data['email'],
                "password"=> $password
            )
        );
    }
    public function webAdmin_Users_Update(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }
        if( !$req->has('mode') ){
            return array(
                "code" => 202,
                "results" => 'Mode not found'
            );
        }
        switch (misc::text($req->mode, 'strtolower')) {
            case 'remove-role':
                $curUser = ctrl_User::user_get('exact-token', array('userToken' => $req->data['userToken']));
                if ( $curUser === false ){
                    return array(
                        "code" => 200, 
                        "message" => "Invalid User Token"
                    );
                }
                ctrl_User::user_remove_role($curUser->id, $req->data['role_data']);
                return array(
                    "code" => 200
                );
                break;
            case 'append-role':
                $curUser = ctrl_User::user_get('exact-token', array('userToken' => $req->data['userToken']));
                if ( $curUser === false ){
                    return array(
                        "code" => 200, 
                        "message" => "Invalid User Token"
                    );
                }
                ctrl_User::user_append_role($curUser->id, $req->data['role_data']);
                return array(
                    "code" => 200
                );
                break;
            default:
                return array(
                    "code" => 200, 
                    "message" => "Mode not found"
                );
                break;
        }
    }
    public function webAdmin_Users_get(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }
        
        if( !$req->has('mode') ){
            return array(
                "code" => 200,
                "type" => "list",
                "results" => ctrl_User::user_get('default')
            );
        }
        return array(
            "code" => 200,
            "type" => $req->mode,
            "results" => ctrl_User::user_get(misc::text($req->mode, 'trim'), $req->data)
        );        
    }
    
    #endregion
    #region Customers
    public function webAdmin_Customers_getData(Request $req, $token){
        $curCustomer = ctrl_Customers::customer_get('exact-token', array( 'userToken'=> $token ));
        if( $curCustomer === false ){
            return array(
                "code" => 202, 
                "message" => "Invalid Customer Token String"
            );
        }
        return $curCustomer;
    }
    public function webAdmin_Customers_get(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }
        
        if( !$req->has('mode') ){
            return array(
                "code" => 200,
                "type" => "list",
                "results" => ctrl_Customers::customer_get('default')
            );
        }
        return array(
            "code" => 200,
            "type" => $req->mode,
            "results" => ctrl_Customers::customer_get(misc::text($req->mode, 'trim'), $req->data)
        );        
    }
    public function webAdmin_Customers_Registration(Request $req){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 204, 
                "message" => "Invalid Token String"
            );
        }

        $password = Str::random(10);
        $isSuccess = ctrl_Customers::customer_create( 
                                                $req->data['name'], 
                                                $req->data['email'], 
                                                $password, 
                                                $isActive = 0 );
        if( !$isSuccess ){
            return array(
                "code"      => 202,
                "message"   => "Email is in use."
            );
        }
        return array(
            "code"      => 200,
            "user_data" => array(
                "name"=> $req->data['name'],
                "email"=> $req->data['email'],
                "password"=> $password
            )
        );
    }
    public function webAdmin_Customers_Restriction(Request $req, $mode, $ref ){
        $tokenData = ApiTokenController::IdentifyToken($req->tokenString);
        if( $tokenData['isValid'] == 0){
            return array(
                "code" => 202, 
                "message" => "Invalid Token String"
            );
        }
        
        $curCustomer = ctrl_Customers::customer_get('exact-token', array( 'userToken'=> $req->customerToken ));
        if( $curCustomer === false ){
            return array(
                "code" => 202, 
                "message" => "Invalid Customer Token String"
            );
        }

        switch ( misc::text($mode, 'strtolower') ) {
            case 'append':
                ctrl_Customers::customer_restriction_append( $curCustomer->id, $ref, $req->referenceValue);
                break;
            case 'remove':
                ctrl_Customers::customer_restriction_remove( $curCustomer->id, $ref, $req->referenceValue);
                break;
            case 'fetch':
                return array(
                    "code" => 200,
                    "results" => ctrl_Customers::customer_restriction_get( $curCustomer->id, $ref)
                );
                break;
        }
    }
    #endregion
    #region Test Function
        public function current(){
            return array(
                "code" => ctrl_Customers::customer_restriction_get(7, 'pluck-categories-products')
            );
        }
    #endregion
}
