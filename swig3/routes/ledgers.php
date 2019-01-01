<?php
Route::get('ledgers', 'IndexController@index');

////////////////////////Suppliers/////////////////////////
Route::get('suppliers', 'SuppliersController@index');
Route::post('suppliers', 'SuppliersController@index');
Route::get('suppliers/add', 'SuppliersController@add');
Route::post('suppliers/store', 'SuppliersController@store');
Route::get('suppliers/Disable/{id}','SuppliersController@Disable');
Route::get('suppliers/Enable/{id}','SuppliersController@Enable');
Route::get('suppliers/delete/{id}','SuppliersController@delete');
Route::post('suppliers/edit/{id}','SuppliersController@edit');
Route::get('suppliers/edit/{id}','SuppliersController@edit');
Route::get('suppliers/view/{id}','SuppliersController@view');

Route::post('suppliers/upload_documents', 'SuppliersController@upload_documents');
Route::post('suppliers/update_upload_documents', 'SuppliersController@update_upload_documents');
Route::post('suppliers/editlist','SuppliersController@editlist');
Route::get('suppliers/editlist','SuppliersController@editlist');
Route::post('suppliers/exportdata', 'SuppliersController@exportdata');
Route::post('suppliers/exportdataedit', 'SuppliersController@exportdataedit');
Route::post('suppliers/autocompleteinventory', 'SuppliersController@autocompleteinventory');
Route::post('suppliers/saveproducts', 'SuppliersController@saveproducts');

////////////////////////Customers/////////////////////////
Route::get('customers', 'CustomersController@index');
Route::post('customers', 'CustomersController@index');
Route::get('customers/add', 'CustomersController@add');
Route::post('customers/store', 'CustomersController@store');
Route::get('customers/Disable/{id}','CustomersController@Disable');
Route::get('customers/Enable/{id}','CustomersController@Enable');
Route::get('customers/delete/{id}','CustomersController@delete');
Route::post('customers/edit/{id}','CustomersController@edit');
Route::get('customers/edit/{id}','CustomersController@edit');

Route::post('customers/upload_documents', 'CustomersController@upload_documents');
Route::post('customers/update_upload_documents', 'CustomersController@update_upload_documents');
Route::post('customers/editlist','CustomersController@editlist');
Route::get('customers/editlist','CustomersController@editlist');
Route::post('customers/exportdata', 'CustomersController@exportdata');
Route::post('customers/exportdataedit', 'CustomersController@exportdataedit');


////////////////////////Assets/////////////////////////
Route::get('assets', 'AssetController@index');
Route::post('assets', 'AssetController@index');
Route::get('assets/add', 'AssetController@add');
Route::post('assets/store', 'AssetController@store');
Route::get('assets/Disable/{id}','AssetController@Disable');
Route::get('assets/Enable/{id}','AssetController@Enable');
Route::get('assets/delete/{id}','AssetController@delete');
Route::post('assets/edit/{id}','AssetController@edit');
Route::get('assets/edit/{id}','AssetController@edit');

Route::post('assets/upload_documents', 'AssetController@upload_documents');
Route::post('assets/update_upload_documents', 'AssetController@update_upload_documents');
Route::post('assets/editlist','AssetController@editlist');
Route::get('assets/editlist','AssetController@editlist');
Route::post('assets/exportdata', 'AssetController@exportdata');
Route::post('assets/exportdataedit', 'AssetController@exportdataedit');


// General ledgers
Route::match(['get', 'post'],'ledgers/general_ledgers', array('as'=>'export','uses'=>'GeneralledgersController@index'));
Route::get('ledgers/general_ledgers/add', 'GeneralledgersController@add');
Route::post('ledgers/general_ledgers/store', 'GeneralledgersController@store');
Route::get('ledgers/general_ledgers/edit/{edit_id}', 'GeneralledgersController@edit');
Route::post('ledgers/general_ledgers/update', 'GeneralledgersController@update');
Route::get('ledgers/general_ledgers/delete/{id}', 'GeneralledgersController@delete');
Route::get('ledgers/general_ledgers/Disable/{id}','GeneralledgersController@Disable');
Route::get('ledgers/general_ledgers/Enable/{id}','GeneralledgersController@Enable');
Route::post('ledgers/general_ledgers/exportdata', 'GeneralledgersController@exportdata');