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


//#property

Route::match(['get','post'],'owner/property-view/{id}', 'Owner\OwnerController@view_property');
Route::match(['get','post'],'owner/property-remove/{id}', 'Owner\OwnerController@remove_property');
Route::match(['get','post'],'owner/property-edit/{id}', 'Owner\OwnerController@edit_property');
Route::match(['get','post'],'owner/add-property', 'Owner\OwnerController@add_property');
Route::match(['get','post'],'owner/save-property-updates', 'Owner\OwnerController@update_property');
Route::match(['get','post'],'owner/image-remove', 'Owner\OwnerController@remove_gallery');
Route::match(['get','post'],'owner/property-listing', 'Owner\OwnerController@get_list_property');
Route::match(['get','post'],'owner/save-property', 'Owner\OwnerController@save_property');

Route::match(['get', 'post'], 'owner/getDistrictData', 'Owner\OwnerController@getDistrictData');
Route::match(['get', 'post'], 'owner/getMunicipalityData', 'Owner\OwnerController@getMunicipalityData');
Route::match(['get', 'post'], 'owner/owner-logout', 'Owner\OwnerController@ownerLogout');



