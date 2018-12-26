<?php

Route::get('finance', 'IndexController@index');
Route::get('finance/bankdepositsale', 'BankdepositsaleController@index');
Route::post('finance/bankdepositsale', 'BankdepositsaleController@getdatewisesale');
Route::post('finance/getsupervisorsale', 'BankdepositsaleController@getsupervisorsale');
Route::get('finance/supervisordaydeposits/{emp_id}/{sale_date}','BankdepositsaleController@supervisordaydeposits');

Route::match(['get', 'post'],'finance/tax_report', array('as'=>'export','uses'=>'TaxreportController@index'));
Route::post('finance/exporttaxreport', 'TaxreportController@exporttaxreport');

Route::match(['get', 'post'],'finance/sales_variance_report', array('as'=>'export','uses'=>'SalesvariancereportController@index'));
Route::post('finance/exportvariancereport', 'SalesvariancereportController@exporttaxreport');


Route::match(['get', 'post'],'finance/minimum_sales_plan', array('as'=>'export','uses'=>'MinimumsalesplanController@index'));
Route::post('finance/minimum_sales_plan/exportdata', 'MinimumsalesplanController@exportdata');


Route::get('finance/vat_report', 'IndexController@vatreport');
Route::post('finance/getvatreport', 'IndexController@getvatreport');

Route::get('finance/salescomparison', 'SalescomparisonController@index');
Route::post('finance/getsalescomparison', 'SalescomparisonController@getsalescomparison');

Route::match(['get', 'post'],'finance/budgetcreation', 'BudgetController@index');  
Route::post('finance/addbudget', 'BudgetController@add_budget');

Route::match(['get', 'post'],'finance/budget_plan', 'BudgetController@budget_plan');
Route::post('finance/budget_plan/exportdata', 'BudgetController@exportdata');


/////////////////////////Payment advice routes////////////////////////////
Route::match(['get', 'post'],'finance/received_payments', 'ReceivedpaymentsController@index');
Route::get('finance/received_payments/view/{id}', 'ReceivedpaymentsController@view');
Route::get('finance/remittance_report/view/{id}', 'ReceivedpaymentsController@view');
Route::post('finance/received_payments/update', 'ReceivedpaymentsController@update');
Route::post('finance/received_payments/exportdata', 'ReceivedpaymentsController@exportdata');

/////////////////////////Remittance Report routes////////////////////////////
Route::match(['get', 'post'],'finance/remittance_report', 'RemittancereportController@remittance_list');
Route::post('finance/remittance_report/exportdata', 'RemittancereportController@exportdata');

//////////////////////////Cash Flow Routes//////////////////////////////////
Route::get('finance/cashflow', 'CashflowController@index');
Route::post('finance/getcustomcashflow', 'CashflowController@getcustomcashflow');

