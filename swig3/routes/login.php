<?php

//use App\Notifications\SubscriptionCancelled;
//Auth::LoginUsingId(1);
//Route::get('/', function() {
//    $post="hhhhhhhhhhhhhhhhh";
//    Auth::user()->notify(new SubscriptionCancelled($post));
//  //return View::make('login/index');
//});
Route::get('/', 'AuthController@index');
Route::get('/login', 'AuthController@index');
Route::get('employees', 'AuthController@employees');
Route::post('login', 'AuthController@login');
Route::get('logout', 'AuthController@logout');


