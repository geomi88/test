<?php

Route::get('kpi', 'IndexController@index') ;

Route::get('kpi/dashboard', 'IndexController@dashboard') ;
Route::post('kpi/dashboard', 'IndexController@dashboard') ;

Route::get('kpi/branch/view/{id}','BranchController@view');
Route::post('kpi/branch/view/{id}','BranchController@view');
Route::post('kpi/branch/contactpersons', 'BranchController@contactpersons') ;
Route::get('kpi/analyst_discussion/{branch_id}', 'AnalystdiscussionController@index') ;
Route::get('kpi/analyst_discussion/view/{id}', 'AnalystdiscussionController@view') ;
Route::post('kpi/analyst_discussion/store', 'AnalystdiscussionController@store') ;
Route::post('kpi/analyst_discussion/sendreply', 'AnalystdiscussionController@sendreply') ;

///////////////////////Inventory Consumption///////////////////////////////
Route::match(['get', 'post'],'kpi/inventory_consumption', array('as'=>'export','uses'=>'InventoryconsumptionController@index'));
Route::post('kpi/inventory_consumption/search', 'InventoryconsumptionController@search');

///////////////////////////////Sales Target///////////////////////
Route::match(['get', 'post'],'kpi/sales_target', array('as'=>'export','uses'=>'SalestargetController@index'));
Route::get('kpi/sales_target/add', 'SalestargetController@add');
Route::post('kpi/sales_target/getsalestargets', 'SalestargetController@getsalestargets');
Route::post('kpi/sales_target/store', 'SalestargetController@store');
Route::get('kpi/sales_target/edit/{id}', 'SalestargetController@edit');
Route::post('kpi/sales_target/update', 'SalestargetController@update');
Route::post('kpi/sales_target/exportdata', 'SalestargetController@exportdata');

////////widget//////////////////
//Route::post('kpi/filterbranch', 'IndexController@filterbranch') ;
Route::match(['get', 'post'],'kpi/filterbranch', 'IndexController@filterbranch');

