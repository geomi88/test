<?php
//////////////////////////////////////supervisors/////////////////////////////
Route::get('supervisors', 'IndexController@index'); 

/////////////////////////////////////inventory request////////////////////////
Route::match(['get', 'post'],'supervisors/inventory_request', 'InventoryrequestController@index');
Route::get('supervisors/inventory_request/add', 'InventoryrequestController@add');
Route::post('supervisors/inventory_request/store', 'InventoryrequestController@store');
Route::get('supervisors/inventory_request/showdetails/{id}', 'InventoryrequestController@showdetails');
Route::post('supervisors/inventory_request/checkmaximumqty', 'InventoryrequestController@checkmaximumqty');
Route::post('supervisors/inventory_request/getunits', 'InventoryrequestController@getunits');
Route::post('supervisors/inventory_request/completerequest', 'InventoryrequestController@completerequest');
Route::post('supervisors/inventory_request/exporttopdf', 'InventoryrequestController@exporttopdf');

Route::get('supervisors/sales_dashboard', 'SalesdashboardController@dashboard') ;
Route::post('supervisors/sales_dashboard', 'SalesdashboardController@dashboard') ;

Route::get('supervisors/branch/view/{id}','BranchController@view');
Route::post('supervisors/branch/view/{id}','BranchController@view');
?>