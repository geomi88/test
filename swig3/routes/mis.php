<?php

/* Route::get('mis', function() {
  return View::make('mis/index');
  }); */
Route::get('mis', 'IndexController@index');

//PLANNING
Route::get('mis/planning', 'PlanningController@index');
Route::get('mis/planning/docs/Disable/{id}', 'PlanningController@Disable');
Route::get('mis/planning/docs/Enable/{id}', 'PlanningController@Enable');
Route::get('mis/planning/docs/delete/{id}', 'PlanningController@delete');
//KPI 
Route::get('mis/kpi', 'KpiController@index');
Route::get('mis/kpi/docs/Disable/{id}', 'KpiController@Disable');
Route::get('mis/kpi/docs/Enable/{id}', 'KpiController@Enable');
Route::get('mis/kpi/docs/delete/{id}', 'KpiController@delete');

//RECIPE
Route::get('mis/recipe', 'RecipeController@index');
Route::get('mis/recipe/docs/Disable/{id}', 'RecipeController@Disable');
Route::get('mis/recipe/docs/Enable/{id}', 'RecipeController@Enable');
Route::get('mis/recipe/docs/delete/{id}', 'RecipeController@delete');

//CHART
Route::get('mis/chart', 'ChartController@index');
Route::get('mis/chart/docs/Disable/{id}', 'ChartController@Disable');
Route::get('mis/chart/docs/Enable/{id}', 'ChartController@Enable');
Route::get('mis/chart/docs/delete/{id}', 'ChartController@delete');


//POS SALES

Route::match(['get', 'post'], 'mis/pos_sales', array('as' => 'export', 'uses' => 'PossalesController@index'));
//Route::post('mis/pos_sales', 'PossalesController@index');
Route::get('mis/pos_sales/show/{show_id}', 'PossalesController@show');
Route::post('mis/pos_sales/search', 'PossalesController@search');
Route::get('mis/pos_sales/search', 'PossalesController@search');
Route::match(['get', 'post'], 'mis/pos_sales/pdfs', 'PossalesController@pdfs');
Route::get('mis/pos_sales/testsearch', 'PossalesController@testsearch');
Route::post('mis/pos_sales/search_via_branch', 'PossalesController@search_via_branch');
Route::get('mis/pos_sales/testingsearch', 'PossalesController@productList');
Route::match(['get', 'post'], 'mis/pos_sales/pdfview', 'PossalesController@pdfview');
Route::post('mis/pos_sales/exporttopdf', 'PossalesController@exporttopdf');

///////////////////////////pos cashier sale report///////////////////////
Route::match(['get', 'post'], 'mis/pos_sales/cashierreports', array('as' => 'export', 'uses' => 'PoscashierController@index'));
Route::get('mis/pos_sales/cashier_show/{show_id}', 'PoscashierController@show');
Route::post('mis/pos_sales/cashierreports/exporttopdf', 'PoscashierController@exporttopdf');

//////////////////////////////supervisor sale report////////////////////////////////////
Route::match(['get', 'post'], 'mis/pos_sales/supervisorreports', array('as' => 'export', 'uses' => 'PossupervisorController@index'));
Route::get('mis/pos_sales/supervisor_show/{show_id}', 'PossupervisorController@show');
Route::post('mis/pos_sales/export_view', 'PossupervisorController@exportviewtopdf');
Route::post('mis/pos_sales/supervisorreports/exporttopdf', 'PossupervisorController@exporttopdf');

//////////////////////////////verified accounts report////////////////////////////////////
//Route::get('mis/verified_accounts', 'VerifiedaccountsController@index') ;
Route::match(['get', 'post'], 'mis/verified_accounts', array('as' => 'export', 'uses' => 'VerifiedaccountsController@index'));
Route::get('mis/verified_accounts/showverifieddetails/{id}', 'VerifiedaccountsController@showverifieddetails');
Route::post('mis/verified_accounts/exporttopdf', 'VerifiedaccountsController@exporttopdf');


//////////////////////////////ledger report////////////////////////////////////
//Route::get('mis/verified_accounts', 'VerifiedaccountsController@index') ;
Route::match(['get', 'post'], 'mis/ledger_report', array('as' => 'export', 'uses' => 'LedgerreportController@index'));
Route::get('mis/ledger_report/showdetails/{id}', 'LedgerreportController@showdetails');
Route::post('mis/ledger_report/exporttopdf', 'LedgerreportController@exporttopdf');


//////////////////////////////Supervisor cahs deposit report////////////////////////////////////
//Route::get('mis/verified_accounts', 'VerifiedaccountsController@index') ;
Route::match(['get', 'post'], 'mis/supervisor_cash_deposit_report', array('as' => 'export', 'uses' => 'SupervisorcashdepositreportController@index'));
Route::get('mis/supervisor_cash_deposit_report/showdepositdetails/{id}', 'SupervisorcashdepositreportController@showdepositdetails');
Route::post('mis/supervisor_cash_deposit_report/exporttopdf', 'SupervisorcashdepositreportController@exporttopdf');

//////////////////////////////Top cashier cash deposit report////////////////////////////////////
//Route::get('mis/verified_accounts', 'VerifiedaccountsController@index') ;
Route::match(['get', 'post'], 'mis/topcashier_cash_deposit_report', array('as' => 'export', 'uses' => 'TopcashiercashdepositreportController@index'));
Route::get('mis/topcashier_cash_deposit_report/showdepositdetails/{id}', 'TopcashiercashdepositreportController@showdepositdetails');
Route::post('mis/topcashier_cash_deposit_report/exporttopdf', 'TopcashiercashdepositreportController@exporttopdf');

//////////////////////////////Newly Opening branches////////////////////////////////////
Route::match(['get', 'post'], 'mis/opening_branches', array('as' => 'export', 'uses' => 'OpeningbranchesController@index'));
Route::post('mis/opening_branches/exporttopdf', 'OpeningbranchesController@exportdata');
