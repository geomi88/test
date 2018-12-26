<?php
//////////////////////////////////////Check list/////////////////////////////
Route::get('costcenter', 'IndexController@index'); 


Route::get('costcenter/cost_analysis', 'CostanalysisController@index');
Route::post('cost_analysis/getmonthlysale', 'CostanalysisController@getmonthlysale');
Route::post('cost_analysis/getbranches', 'CostanalysisController@getbranches');
Route::post('cost_analysis/getbranchgraph', 'CostanalysisController@getbranchgraph');

Route::get('costcenter/cost_allocation/add', 'CostcenterController@add');
Route::post('costcenter/cost_allocation/store', 'CostcenterController@store');

Route::get('costcenter/costcenter_report', 'CostcenterreportController@index');
Route::post('costcenter_report/getbranchwisecost', 'CostcenterreportController@getbranchwisecost');
Route::post('cost_analysis/filterbranch', 'CostanalysisController@filterbranch');

////for one time use only//////
Route::get('costcenter/keeplivedata', 'CostcenterController@keeplivedata');
?>