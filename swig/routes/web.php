<?php

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

//Route::get('/', function () {
//    return view('welcome');
//});
Route::get('/', 'Login\AuthController@login');
Route::post('/', 'Login\AuthController@login');
Route::post('/checklogin', 'Login\AuthController@checklogin');


Route::group(['middleware' => 'usersession'], function () {
       
    
Route::post('/dashboard', 'Dashboard\DashboardController@index');
Route::get('/dashboard', 'Dashboard\DashboardController@index');
Route::get('/users', 'Users\UsersController@index');
Route::get('/users/delete/{id}', 'Users\UsersController@delete');
Route::any('/users/search', 'Users\UsersController@search');
Route::get('/users/edit/{id}', 'Users\UsersController@edit');
Route::get('/users/checkemail', 'Users\UsersController@checkemail');
Route::get('/users/checkphone', 'Users\UsersController@checkphone');
Route::post('/users/update', 'Users\UsersController@update');
Route::get('/logout', 'Login\AuthController@logout');
Route::any('/users/notification', 'Users\UsersController@notification');
Route::post('/users/sendnotification', 'Users\UsersController@sendnotification');




Route::get('/company', 'Company\CompanyController@index');
Route::get('/company/delete/{id}', 'Company\CompanyController@delete');
Route::any('/company/search', 'Company\CompanyController@search');
Route::any('/company/add', 'Company\CompanyController@add');
Route::get('/company/edit/{id}', 'Company\CompanyController@edit');
Route::post('/company/store', 'Company\CompanyController@store');
Route::get('/company/checkemail', 'Company\CompanyController@checkemail');
Route::get('/company/checkphone', 'Company\CompanyController@checkphone');
Route::post('/company/update', 'Company\CompanyController@update');
Route::get('/company/checkname', 'Company\CompanyController@checkname');

Route::get('/category', 'Category\CategoryController@index');
Route::get('/category/delete/{id}', 'Category\CategoryController@delete');
Route::any('/category/add', 'Category\CategoryController@add');
Route::get('/category/edit/{id}', 'Category\CategoryController@edit');
Route::post('/category/store', 'Category\CategoryController@store');
Route::post('/category/update', 'Category\CategoryController@update');
Route::get('/category/checkname', 'Category\CategoryController@checkname');

Route::get('/products', 'Products\ProductsController@index');
Route::any('/products/search', 'Products\ProductsController@search');
Route::post('/products/verify', 'Products\ProductsController@verify');
Route::get('/products/view/{id}','Products\ProductsController@show');
Route::any('/products/deals', 'Products\ProductsController@deals');
Route::any('/products/searchdeals', 'Products\ProductsController@searchdeals');
Route::post('/products/changefeatured', 'Products\ProductsController@changefeatured');
Route::post('/products/addreason', 'Products\ProductsController@addreason');




Route::get('/ads','Ads\AdsController@index');
Route::any('/ads/search', 'Ads\AdsController@search');
Route::get('/ads/view/{id}','Ads\AdsController@show');
Route::get('/ads/edit/{id}', 'Ads\AdsController@edit');
Route::post('/ads/update', 'Ads\AdsController@update');

Route::get('/orders', 'Orders\OrdersController@index');
Route::post('/orders/changeshippingstatus', 'Orders\OrdersController@changeshippingstatus');
Route::any('/orders/search', 'Orders\OrdersController@search');
Route::get('/orders/view/{id}','Orders\OrdersController@show');



});

