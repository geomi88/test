<?php
//////////////////////////////////////////////////////
Route::get('inventory', 'IndexController@index');
//////////////////////////////////////////////////////
Route::match(['get', 'post'],'inventory/inventory_items', array('as'=>'export','uses'=>'InventoryController@index'));
Route::get('inventory/inventory_items/add', 'InventoryController@add');
Route::post('inventory/inventory_items/checkproductcode', 'InventoryController@checkproductcode');
Route::post('inventory/inventory_items/checksuppliericode', 'InventoryController@checksuppliericode');
Route::post('inventory/inventory_items/getalternateunits', 'InventoryController@getalternateunits');
Route::post('inventory/inventory_items/store', 'InventoryController@store');
Route::get('inventory/inventory_items/edit/{id}', 'InventoryController@edit');
Route::post('inventory/inventory_items/update', 'InventoryController@update');
Route::get('inventory/inventory_items/Disable/{id}', 'InventoryController@Disable');
Route::get('inventory/inventory_items/Enable/{id}', 'InventoryController@Enable');
Route::get('inventory/inventory_items/delete/{id}', 'InventoryController@delete');
Route::post('inventory/inventory_items/exporttopdf', 'InventoryController@exporttopdf');
Route::post('inventory/inventory_items/getchilds', 'InventoryController@getchilds');

Route::match(['get', 'post'],'inventory/barcode/available_barcodes', array('as'=>'export','uses'=>'BarcodeController@index'));
Route::get('inventory/barcode/add', 'BarcodeController@add');
Route::post('inventory/barcode/checkbarcode', 'BarcodeController@checkbarcode');
Route::post('inventory/barcode/store', 'BarcodeController@store');
Route::match(['get', 'post'],'inventory/barcode/allocated_barcodes', array('as'=>'export','uses'=>'BarcodeController@allocated'));
Route::post('inventory/barcode/exportavailablebarcodes', 'BarcodeController@exportavailablebarcodes');
Route::post('inventory/barcode/exportallocatedbarcodes', 'BarcodeController@exportallocatedbarcodes');
Route::get('inventory/barcode/release/{id}', 'BarcodeController@release');
Route::get('inventory/barcode/delete/{id}', 'BarcodeController@delete');
Route::get('inventory/barcode/edit/{id}', 'BarcodeController@edit');
Route::post('inventory/barcode/update', 'BarcodeController@update');



Route::get('inventory/inventory_items/updatealtunits', 'InventoryController@updatealtunits');

/////////////// Stock Report - Item wise ////////////////
Route::match(["get", "post"], "inventory/itemwisestockreport", "ItemwisestockreportController@index");
Route::post("inventory/itemwisestockreport/getitemunits","ItemwisestockreportController@inventoryunit");
Route::post("inventory/itemwisestockreport/view","ItemwisestockreportController@get_stock_list");
Route::post("inventory/itemwisestockreport/detailed_view","ItemwisestockreportController@get_detailed_view");

/////////////// Stock Report - Location wise ////////////////
Route::match(["get", "post"], 'inventory/locationwisestockreport', 'LocationwisestockreportController@index');
Route::post("inventory/locationwisestockreport/getstocklist","LocationwisestockreportController@getstocklist");
Route::post("inventory/locationwisestockreport/getbatchlist","LocationwisestockreportController@getbatchlist");


////// Testing /////////////
Route::post("inventory/itemwisestockreport/test_view","ItemwisestockreportController@new_stock_list");