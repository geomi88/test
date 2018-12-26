<?php
//////////////////////////////////////warehouse/////////////////////////////
Route::get('warehouse', 'IndexController@index'); 

/////////////////////////////////////inventory request////////////////////////
Route::match(['get', 'post'],'warehouse/received_inventory_request', 'ReceivedinventoryrequestController@index');
Route::get('warehouse/received_inventory_request/add', 'ReceivedinventoryrequestController@add');
Route::post('warehouse/received_inventory_request/store', 'ReceivedinventoryrequestController@store');
Route::get('warehouse/received_inventory_request/showdetails/{id}', 'ReceivedinventoryrequestController@showdetails');
Route::post('warehouse/received_inventory_request/approverequest', 'ReceivedinventoryrequestController@approverequest');
Route::post('warehouse/received_inventory_request/rejectrequest', 'ReceivedinventoryrequestController@rejectrequest');
Route::post('warehouse/received_inventory_request/holdrequest', 'ReceivedinventoryrequestController@holdrequest');
Route::post('warehouse/received_inventory_request/cancelitem', 'ReceivedinventoryrequestController@cancelitem');
Route::post('warehouse/received_inventory_request/exporttopdf', 'ReceivedinventoryrequestController@exporttopdf');

///
Route::match(['get', 'post'],'warehouse/warehouse_physical_stock', 'WarehousestockController@index');

//Route::match(['get', 'post'],'warehouse/warehouse_physical_stock', array('as'=>'export','uses'=>'WarehousestockController@index'));
Route::post('warehouse/warehouse_physical_stock/store', 'WarehousestockController@store');

Route::match(['get', 'post'],'warehouse/get_warehouse_physical_stock', 'WarehousestockController@get_physical_stock');

?>