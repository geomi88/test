<?php

Route::get('dashboard/managementconsole', 'ManagementconsoleController@index');
Route::post('managementconsole/getbranches', 'ManagementconsoleController@getbranches');
Route::post('managementconsole/getmonthlysale', 'ManagementconsoleController@getmonthlysale');

Route::post('managementconsole/filterbranches', 'ManagementconsoleController@filterbranches');
