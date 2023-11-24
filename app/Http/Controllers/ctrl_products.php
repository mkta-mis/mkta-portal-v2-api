<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\product;
use App\Models\files;
use App\Models\product_images;
use App\Models\products_related_product;
use App\Models\product_category;
use App\Models\category;



class ctrl_products extends Controller
{
    #region Product
    
    public static function product_createVariant( $parent_id, $code, $name, $creator_id, $file_id = 1, $isVisible = 1 ){
        $curProduct = product::create(
            array(
                'code'                  => $code,
                'name'                  => $name,
                'creator_id'            => $creator_id,
                'parent_id'             => $parent_id,
                'file_id'               => $file_id,
                'isVisible'             => $isVisible,
            )
        );
        return $curProduct;
    }
    public static function product_createParent($code, $name, $description, $creator_id, $file_id = 1, $isVisible = 1 ){
        $mainProduct = product::create(
            array(
                'code'                  => $code,
                'name'                  => $name,
                'description'           => $description,
                'creator_id'            => $creator_id,
                'parent_id'             => 0,
                'isVisible'             => $isVisible,
                "file_id"               => 1
            )
        );
        $subProduct = product::create(
            array(
                'code'                  => $code,
                'name'                  => $name,
                'creator_id'            => $creator_id,
                'parent_id'             => $mainProduct->id,
                'isVisible'             => $isVisible,
                "file_id"               => 1
            )
        );
        self::product_category_create($mainProduct->id, 1);
        return $mainProduct;
    }
    public static function product_update($id, $data){
        $curProduct = product::where('id', '=', $id);
        if( $curProduct->get()->count() != 1 ){
            return false;
        }
        $curProduct = $curProduct->get()->first();
        foreach ($data as $key => $value) {
            $curProduct[$key] = $value;
        }
        $curProduct->save();
        return $curProduct;
    }
    #region Related Products
    public static function product_get_related($product_id, $relation_type = 0){
          $related_products = products_related_product::where('product_id', '=', $product_id)->where('relation_type', '=', $relation_type)->get()->pluck('target_id');
          return product::whereIn('id', $related_products )->with(['variants', 'thumbnail', 'components', 'product_categories'])->get();
    }
    public static function product_related_add($product_id, $target_id, $relation_type = 0){
          $isAdded =      products_related_product::where('target_id', '=', $target_id)
                                                  ->where('product_id', '=', $product_id)
                                                  ->where('relation_type', '=', $relation_type)
                                                  ->get()
                                                  ->count() > 0;
          if( $isAdded ){
                    return false;
          }
          products_related_product::create(
                    array(
                              "target_id" => $target_id,
                              "product_id" => $product_id,
                              "relation_type" => $relation_type,
                    )
          );
          return true;

    }
    public static function product_related_remove($product_id, $target_id = 0, $relation_type = 0){
          if( $target_id == -1 ){
                    products_related_product::where('product_id', '=', $product_id)->delete();
          }else{
                    products_related_product::where('product_id', '=', $product_id)->where('target_id', '=', $target_id)->delete();
          }
          return true;
    }
    #endregion
    #region Image 360
    public static function product_image360($id){
        return product_images::where('product_id', '=', $id)->with(['file_data'])->orderBy('index', 'asc')->get();
    }
    public static function product_image360_action_moveUp($id){
        $curData = product_images::where('id', '=', $id);
        if( $curData->get()->count() != 1 ){
            return false;
        }
        $curData = $curData->get()->first();

        product_images::where('product_id', '=', $curData->product_id)
                      ->where('index', '=', $curData->index - 1)
                      ->update(['index' => $curData->index ]);
        $curData->index = $curData->index - 1;
        $curData->save();
        return true;
    }
    public static function product_image360_action_moveDown($id){
        $curData = product_images::where('id', '=', $id);
        if( $curData->get()->count() != 1 ){
            return false;
        }
        $curData = $curData->get()->first();
        product_images::where('product_id', '=', $curData->product_id)
                      ->where('index', '=', $curData->index + 1)
                      ->update(['index' => $curData->index ]);
        $curData->index = $curData->index + 1;
        $curData->save();
        return true;
    }
    public static function product_image360_action_Delete($id){
        $target_Image360 = product_images::where('id', '=', $id);
        if( $target_Image360->get()->count() != 1 ){
            return false;
        }
        $target_Image360 = $target_Image360->get()->first();
        product_images::where('product_id', '=', $target_Image360->product_id)
                      ->where('index', '>', $target_Image360->index)
                      ->update(['index' => DB::raw( '`index` - 1' )]);

        $target_Image360->delete();
        return true;
    }
    public static function product_images360_action_CollectionReplace($id, $file_id){
        $target_Image360 = product_images::where('id', '=', $id);
        if( $target_Image360->get()->count() != 1 ){
            return false;
        }
        $target_Image360 = $target_Image360->get()->first();
        $target_Image360->file_id = $file_id;
        $target_Image360->save();
        return true;
    }
    public static function product_images360_action_CollectionAppend($id, $files = array()){
        if( count($files) == 0 ){
            return false;
        }
        $lastData = product_images::where('product_id', '=', $id)->orderBy('index', 'desc');
        $lastCount = 0;
        if( $lastData->get()->count() == 0 ){
            $lastCount = 0;
        }else{
            $lastCount =  $lastData->get()->first()->index;
        }

        foreach ($files as $key => $value) {
            $lastCount++;
            product_images::create(
                array(
                    "index"=> $lastCount,
                    "product_id" => $id,
                    "file_id"=>$value['id']
                )
            );
        }
        return true;
    }
    #endregion
    #region Product Category
    public static function product_category_create($product_id, $category_id){
        product_category::create(
            array(
                "product_id" => $product_id,
                "category_id" => $category_id,
            )
        );
        return true;
    }
    public static function product_category_change($product_id, $category_id){
          $curProdCat = product_category::where('product_id', '=', $product_id);
          if( $curProdCat->get()->count() == 0 ){
                    self::product_category_create($product_id, $category_id);
                    return false;
          }
          $curProdCat = $curProdCat->get()->first();
          $curProdCat->category_id = $category_id;
          $curProdCat->save();
          return true;
    }
    #endregion
    #endregion
    #region Category
    public static function Categeory_get($type, $filter = array()){
        switch (misc::text($type, 'strtolower')) {
            case 'exact':
                $curCategory = category::with(['full_data', 'thumbnail'])->where('id', '=', $filter['id']);
                return $curCategory->get()->count() == 0 ? false : $curCategory->get()->first();
                break;
            case 'array-id':
                return category::with(['full_data', 'thumbnail'])->whereIn('id',$filter)->get();
                break;
            default:
                # code...
                break;
        }
    }
    public static function Category_update($id, $data){
        $list_Category = collect(category::get());
        $curCategory = category::with(['full_data','thumbnail'])->where('id', '=', $id);
        if( $curCategory->get()->count() != 1 ){
            return false;
        }
        $curCategory = $curCategory->get()->first();
        foreach ($data as $key => $value) {
            if ( $key == 'name' ){
                $curCategory[$key] = $list_Category->where('parent_id', '=', $id)->where('name', '=' , $value)->count() == 0 ? $value : $curCategory[$key];
            }else{
                $curCategory[$key] = $value;
            }
        }
        $curCategory->save();
        return  category::with(['full_data','thumbnail'])->where('id', '=', $id)->get()->first();
    }
    public static function Category_create(
        $name,
        $urlKey,
        $description,
        $parent_id,
        $creator_id,
        $file_id = 1,
        $isVisible = 1
    ){
        return category::create(
            array(
                'name'              => $name,
                'urlKey'            => $urlKey,
                'description'       => $description,
                'isVisible'         => $isVisible,
                'parent_id'         => $parent_id,
                'file_id'           => $file_id,
                'creator_id'        => $creator_id,
            )
        );
    }
    #endregion
}
