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

Route::match(['get','post'],'agent/property-view/{id}', 'Agent\AgentController@view_property');
Route::match(['get','post'],'agent/property-remove/{id}', 'Agent\AgentController@remove_property');
Route::match(['get','post'],'agent/property-edit/{id}', 'Agent\AgentController@edit_property');
Route::match(['get','post'],'agent/add-property', 'Agent\AgentController@add_property');
Route::match(['get','post'],'agent/save-property-updates', 'Agent\AgentController@update_property');
Route::match(['get','post'],'agent/image-remove', 'Agent\AgentController@remove_gallery');
Route::match(['get','post'],'agent/property-listing', 'Agent\AgentController@get_list_property');
Route::match(['get','post'],'agent/save-property', 'Agent\AgentController@save_property');

Route::match(['get', 'post'], 'agent/getDistrictData', 'Agent\AgentController@getDistrictData');
Route::match(['get', 'post'], 'agent/getMunicipalityData', 'Agent\AgentController@getMunicipalityData');
Route::match(['get', 'post'], 'agent/agent-logout', 'Agent\AgentController@agentLogout');



