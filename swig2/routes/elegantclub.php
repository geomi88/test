<?php
Route::get('elegantclub', 'ElegantClubController@index'); 
Route::get('elegantclub/add_corporate_customer', 'ElegantClubController@addCorporateCustomer'); 
Route::match(['get', 'post'],'elegantclub/edit_corporate_customer', 'ElegantClubController@edit_customer_list'); 
Route::get('elegantclub/edit_customer/{id}', 'ElegantClubController@edit_customer'); 
Route::get('elegantclub/customer/Enable/{id}', 'ElegantClubController@enable_customer'); 
Route::get('elegantclub/customer/Disable/{id}', 'ElegantClubController@disable_customer'); 
Route::post('elegantclub/customer/store', 'ElegantClubController@store');
Route::post('elegantclub/customer/upload_documents', 'ElegantClubController@upload_documents');
Route::match(['get', 'post'],'elegantclub/list_corporate_customer', array('as'=>'export','uses'=>'ElegantClubController@listcustomers'));
Route::match(['get', 'post'],'elegantclub/delete_corporate_customer', array('as'=>'export','uses'=>'ElegantClubController@deletelist'));
Route::get('elegantclub/delete_customer/{id}', 'ElegantClubController@delete_customer'); 
Route::get('elegantclub/customer/view/{id}', 'ElegantClubController@view_customer');

//Warehose Allocation 
Route::match(['get', 'post'],'elegantclub/warehose_allocation', 'WarehouseAllocationController@warehose_allocation'); 
Route::get('elegantclub/warehose_allocation/add', 'WarehouseAllocationController@add'); 
Route::post('elegantclub/warehose_allocation/store', 'WarehouseAllocationController@store'); 
Route::get('elegantclub/warehose_allocation/edit/{id}', 'WarehouseAllocationController@edit'); 
Route::get('elegantclub/warehose_allocation/Enable/{id}', 'WarehouseAllocationController@enable'); 
Route::get('elegantclub/warehose_allocation/Disable/{id}', 'WarehouseAllocationController@disable'); 
Route::get('elegantclub/warehose_allocation/delete/{id}', 'WarehouseAllocationController@delete'); 

//Elegant Declaration 
Route::get('elegantclub/declaration', 'ElegantDeclarationController@index'); 
Route::post('elegantclub/declaration', 'ElegantDeclarationController@index'); 
Route::get('elegantclub/declaration/add', 'ElegantDeclarationController@add'); 
Route::post('elegantclub/declaration/store', 'ElegantDeclarationController@store'); 
Route::get('elegantclub/declaration/edit/{ele_dec_id}', 'ElegantDeclarationController@edit'); 
Route::post('elegantclub/declaration/update', 'ElegantDeclarationController@update');  
Route::get('elegantclub/declaration/delete/{dec_id}', 'ElegantDeclarationController@delete'); 
?>