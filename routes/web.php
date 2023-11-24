<?php

use Illuminate\Support\Facades\Route;
use Storage as Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('thumbnail/{code}', function ($code) {
    $curProduct = \App\Models\product::where('code', '=', $code)->with(['thumbnail'])->get();
    $filename = 'No Image.jpg';
    if($curProduct->count() != 0){
        $curProduct = $curProduct->first();
        $filename = $curProduct->thumbnail['filename'];
    }
    return Storage::disk('s3')->get($filename);
});
Route::get('resources/{filename}', function ($filename) {
    return Storage::disk('s3')->get($filename);
});
Route::get('/', function () {
    return view('welcome');
});
