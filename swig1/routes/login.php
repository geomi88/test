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
Route::get('admin/login', 'Login\AuthController@index');
Route::post('admin/dologin', 'Login\AuthController@login');
//Route::get('admin/dashboard', 'Admin\LoginController@getLogin');
Route::match(['get', 'post'], 'admin/dashboard', 'Admin\AdminController@get_dashboard');
Route::get('admin/new-agent', 'Admin\AdminAgentController@get_add_agent');
//Route::match(['get', 'post'], 'admin/getDistrictData', 'Admin\AdminController@getDistrictData');
Route::match(['get', 'post'], 'admin/getMunicipalityData', 'Admin\AdminController@getMunicipalityData');

Route::get('admin/logout', 'Login\AuthController@logout');

Route::match(['get', 'post'], 'admin/getDistrictData', 'Admin\AdminController@getDistrictData');
Route::match(['get', 'post'], 'admin/getMunicipalityData', 'Admin\AdminController@getMunicipalityData');

//agent-login
Route::get('agent/login', 'Login\AuthController@agentIndex');
Route::match(['get','post'],'agent/doAgentLogin', 'Login\AuthController@agentLogin');
Route::match(['get','post'],'agent/dashboard', 'Agent\AgentController@get_dashboard');

//Owner-login
Route::get('owner/login', 'Login\AuthController@ownerIndex');
Route::match(['get','post'],'owner/doOwnerLogin', 'Login\AuthController@ownerLogin');
Route::match(['get','post'],'owner/dashboard', 'Owner\OwnerController@get_dashboard');


 