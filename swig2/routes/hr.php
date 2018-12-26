<?php

Route::get('hr', 'HrController@index');

/////////////////////////////////User permission////////////////////////
Route::match(['get', 'post'],'hr/userpermissions', 'UserpermissionController@index');
Route::get('hr/userpermissions/showdetails/{id}', 'UserpermissionController@showdetails');
Route::post('hr/userpermissions/updatemodules', 'UserpermissionController@updatemodules');

Route::get('hr/countrywise', 'GraphController@getcountrywisegraph');
Route::get('hr/job_positionwise', 'GraphController@getjobwisegraph');

Route::get('hr/menu_order', 'MenuorderController@index');
Route::post('hr/change_priority', 'MenuorderController@change_priority');

Route::match(['get', 'post'],'hr/payroll', 'PayrollController@index');
Route::post('hr/payroll/exportdata', 'PayrollController@exportdata');