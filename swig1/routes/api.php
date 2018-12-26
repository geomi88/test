<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('user/registration', 'UserController@registration');
Route::post('user/otpverification', 'UserController@otpverification');
Route::post('user/login', 'UserController@login');
Route::post('user/forgotpassword', 'UserController@forgotpassword');
Route::post('user/userprofile', 'UserController@userprofile');
Route::post('user/companyprofile', 'UserController@companyprofile');
Route::post('user/myorders', 'UserController@myorders');
Route::post('user/sellerprofile', 'UserController@sellerprofile');
Route::post('user/companysellerprofile', 'UserController@companysellerprofile');
Route::post('user/rateuser', 'UserController@rateuser');
Route::post('user/notifications', 'UserController@notifications');
Route::post('user/profile', 'UserController@profile');
Route::post('user/productsbuysell', 'UserController@productsbuysell');
Route::post('user/logout', 'UserController@logout');
Route::post('user/guestuser', 'UserController@guestuser');
Route::post('user/deletenotification', 'UserController@deletenotification');
Route::post('user/deleteallnotifications', 'UserController@deleteallnotifications');



Route::post('products/dashboard', 'ProductsController@dashboard');
Route::post('products/productsbycategory', 'ProductsController@productsbycategory');
Route::post('products/searchproducts', 'ProductsController@searchproducts');
Route::post('products/addtowishlist', 'ProductsController@addtowishlist');
Route::post('products/wishlist', 'ProductsController@wishlist');
Route::post('products/sell', 'ProductsController@sell');
Route::post('products/uploadimage', 'ProductsController@uploadimage');
Route::post('products/order', 'ProductsController@order');
Route::post('products/deals', 'ProductsController@deals');
Route::post('products/update', 'ProductsController@update');
Route::post('products/delete', 'ProductsController@delete');
Route::post('products/addview', 'ProductsController@addview');
Route::post('products/addmessage', 'ProductsController@addmessage');
Route::post('products/productschatusers', 'ProductsController@productschatusers');
Route::post('products/productchats', 'ProductsController@productchats');
Route::post('products/addoffer', 'ProductsController@addoffer');
Route::post('products/changeofferstatus', 'ProductsController@changeofferstatus');
Route::post('products/orderdetails', 'ProductsController@orderdetails');
Route::post('products/deleteproductchats', 'ProductsController@deleteproductchats');
Route::post('products/deleteuserchats', 'ProductsController@deleteuserchats');




Route::post('categories', 'CategoryController@index');
Route::post('categories/addtowishlist', 'CategoryController@addtowishlist');

