<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () { return view('index'); })->name('page.index');
Route::get('/produits/{product}', function (\App\Models\Product $product) { return view('product', compact('product')); })->name('page.product');

Route::get('/api/produits', 'App\Http\Controllers\Api\ProductController@index')->name('api.products.index');
Route::get('/api/produits/{product}', 'App\Http\Controllers\Api\ProductController@show')->name('api.products.show');
