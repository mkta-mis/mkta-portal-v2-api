<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Http\Controllers\ApiTokenController;
Route::match(['put', 'get', 'post'], '/current-test',  [App\Http\Controllers\webAdmin\z_routes::class, 'current']);

use App\Http\Controllers\AuthenticationController;
Route::match(['put', 'get', 'post'], '/login',  [AuthenticationController::class, 'UserLogIn']);

Route::prefix('/categories')->group(function () {
    Route::prefix('/web-admin')->group(function () {
        Route::match(['put', 'get', 'post'], '/list', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Category']);
        Route::match(['put', 'get', 'post'], '/action', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Category_Change']);
    });
    Route::match(['put', 'get', 'post'], 'web-store', [App\Http\Controllers\webStore\routes::class, 'webStore_Index']);
    Route::match(['put', 'get', 'post'], '{cat_urlKey}', [App\Http\Controllers\webStore\routes::class, 'webStore_Category']);
    Route::match(['put', 'get', 'post'], 'products/{cat_urlKey}', [App\Http\Controllers\webStore\routes::class, 'webStore_Category_Products']);
});
Route::prefix('/users')->group(function () {
    Route::prefix('/web-admin')->group(function () {
        Route::match(['put', 'get', 'post'], '/list', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Users_get']);
        Route::match(['put', 'get', 'post'], '/register', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Users_Registration']);
        Route::match(['put', 'get', 'post'], '/reset-password', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Users_Reset_Password']);
        Route::match(['put', 'get', 'post'], '/update', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Users_Update']);
    });
});

Route::prefix('/customers')->group(function () {

    Route::match(['put', 'get', 'post'], 'token/{token}', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Customers_getData']);


    Route::prefix('/web-admin')->group(function () {
        Route::match(['put', 'get', 'post'], '/list', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Customers_get']);
        Route::match(['put', 'get', 'post'], '/register', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Customers_Registration']);
        Route::match(['put', 'get', 'post'], '/reset-password', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Users_Reset_Password']);
        Route::match(['put', 'get', 'post'], '/update', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Users_Update']);
        Route::match(['put', 'get', 'post'], '/restriction/{mode}/{ref}', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Customers_Restriction']);
    });
});

Route::prefix('/products')->group(function () {
    Route::match(['put', 'get', 'post'], 'list', [App\Http\Controllers\webStore\routes::class, 'webStore_Product_List']);
    
    Route::match(['put', 'get', 'post'], 'thumbnail/update', [App\Http\Controllers\webAdmin\z_routes::class, 'webStore_Product_Thumbnail_update']);

          Route::match(['put', 'get', 'post'], 'data/full-info/update', [App\Http\Controllers\webAdmin\z_routes::class, 'webStore_Product_Info_Update']);
          Route::match(['put', 'get', 'post'], 'data/full-info/{code}', [App\Http\Controllers\webAdmin\z_routes::class, 'webStore_Product_Index']);
          Route::match(['put', 'get', 'post'], 'data/related/{code}/{ref}', [App\Http\Controllers\webAdmin\z_routes::class, 'webStore_Product_related']);
          Route::match(['put', 'get', 'post'], 'data/related/action', [App\Http\Controllers\webAdmin\z_routes::class, 'webStore_Product_related_actions']);


    
    Route::match(['put', 'get', 'post'], 'data/image360/{code}', [App\Http\Controllers\webAdmin\z_routes::class, 'webStore_Product_360']);
    

    Route::match(['put', 'get', 'post'], 'web-admin/product-info', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Product']);
    Route::match(['put', 'get', 'post'], 'web-admin/list', [App\Http\Controllers\webAdmin\z_routes::class, 'webStore_Product_List']);

    Route::match(['put', 'get', 'post'], 'data/components/add', [App\Http\Controllers\webAdmin\z_routes::class, 'product_components_create']);
    Route::match(['put', 'get', 'post'], 'data/components/remove', [App\Http\Controllers\webAdmin\z_routes::class, 'product_components_remove']);
    Route::match(['put', 'get', 'post'], 'data/components/update', [App\Http\Controllers\webAdmin\z_routes::class, 'product_components_update']);
    Route::match(['put', 'get', 'post'], 'data/components/{code}', [App\Http\Controllers\webAdmin\z_routes::class, 'product_components_get']);
    

    Route::match(['put', 'get', 'post'], 'image360/action', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Product_Image360_action']);
    Route::match(['put', 'get', 'post'], 'image360-collections', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Product_Image360']);



    Route::match(['put', 'get', 'post'], 'image360/{code}', [App\Http\Controllers\webStore\routes::class, 'webStore_Product_360']);
    Route::match(['put', 'get', 'post'], 'components/{code}', [App\Http\Controllers\webStore\routes::class, 'webStore_Product_Components']);
    Route::match(['put', 'get', 'post'], 'update/{code}', [App\Http\Controllers\webStore\routes::class, 'webStore_Product_Index']);
});

Route::prefix('/files')->group(function () {
    Route::match(['put', 'get', 'post'], 'search', [App\Http\Controllers\ctrl_Files::class, 'files_search']);
    Route::match(['put', 'get', 'post'], 'upload', [App\Http\Controllers\ctrl_Files::class, 'files_upload']);
});

Route::prefix('/roles')->group(function () {
    Route::match(['put', 'get', 'post'], 'get', [App\Http\Controllers\webAdmin\z_routes::class, 'db_Roles']);

    Route::prefix('/web-admin')->group(function () {
        Route::match(['put', 'get', 'post'], '/get', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Roles_get']);
        Route::match(['put', 'get', 'post'], '/action', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Roles_Change']);
    });
    
});
Route::prefix('/permissions')->group(function () {
    Route::prefix('/web-admin')->group(function () {
        Route::match(['put', 'get', 'post'], '/get', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Permissions_get']);
        Route::match(['put', 'get', 'post'], '/action', [App\Http\Controllers\webAdmin\z_routes::class, 'webAdmin_Permissions_Change']);
    });
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
