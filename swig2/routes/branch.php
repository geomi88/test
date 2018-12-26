<?php
//////////////////////////////////////branch/////////////////////////////
Route::get('branch', 'IndexController@index'); 

//////////////////////////////////Sales Analyst////////////////////////
Route::match(['get', 'post'],'branch/sales_analyst_branches', 'SalesanalystbranchesController@index');
Route::post('branch/sales_analyst_branches/store', 'SalesanalystbranchesController@store');
Route::post('branch/sales_analyst_branches/getanalystbranches', 'SalesanalystbranchesController@getanalystbranches');
Route::post('branch/sales_analyst_branches/exporttopdf', 'SalesanalystbranchesController@exporttopdf');

//////////////////////////////////Branch Physical Stock////////////////////////
Route::match(['get', 'post'],'branch/branch_physical_stock', 'BranchphysicalstockController@index');
Route::post('branch/branch_physical_stock/getphysicalstock', 'BranchphysicalstockController@getphysicalstock');
Route::post('branch/branch_physical_stock/exporttopdf', 'BranchphysicalstockController@exporttopdf');

?>