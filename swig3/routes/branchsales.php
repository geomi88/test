<?php

Route::get('branchsales', 'BranchsalesController@index');
Route::get('branchsales/pos_sales/add', 'PossalesController@add');
Route::post('branchsales/pos_sales/store', 'PossalesController@store');
Route::post('branchsales/pos_sales/getbranchname', 'PossalesController@getbranchname');
Route::post('branchsales/pos_sales/getjobtimings', 'PossalesController@getjobtimings');
Route::post('branchsales/pos_sales/getsupervisordetails', 'PossalesController@getsupervisordetails');
Route::post('branchsales/pos_sales/getcashierdetails', 'PossalesController@getcashierdetails');
Route::post('branchsales/pos_sales/getopenamount', 'PossalesController@getopenamount');
Route::post('branchsales/pos_sales/branch_shifts', 'PossalesController@branch_shifts');
Route::post('branchsales/pos_sales/shift_cashier', 'PossalesController@shift_cashier');

Route::post('branchsales/pos_sales/gettaxamount', 'PossalesController@gettaxamount');
Route::post('branchsales/pos_sales/dateCompare', 'PossalesController@dateCompare');
//////////////////////branch attendes////////////////////////////////////////

Route::get('branchsales/branch_attendees', 'BranchattendeesController@index');
Route::get('branchsales/branch_attendees/add', 'BranchattendeesController@add');
Route::post('branchsales/branch_attendees/store', 'BranchattendeesController@store');
Route::post('branchsales/branch_attendees/getcashier', 'BranchattendeesController@getcashier');
Route::post('branchsales/branch_attendees/getbarista', 'BranchattendeesController@getbarista');
Route::get('branchsales/branch_attendees/show/{id}', 'BranchattendeesController@show');
Route::post('branchsales/branch_attendees/shiftdetails', 'BranchattendeesController@shiftdetails');
Route::get('branchsales/cash_collection/add', 'CashcollectionController@add');
Route::post('branchsales/cash_collection/add', 'CashcollectionController@add');
Route::post('branchsales/cash_collection/changecollectionstatus', 'CashcollectionController@changecollectionstatus');
Route::post('branchsales/cash_collection/notcollectedpos', 'CashcollectionController@notcollectedpos');
Route::post('branchsales/cash_collection/getcashierdetails', 'CashcollectionController@getcashierdetails');
Route::post('branchsales/cash_collection/store', 'CashcollectionController@store');
//Route::get('branchsales/cash_collection/topcashierfund', 'CashcollectionController@topcashierfund');
Route::match(['get', 'post'], 'branchsales/cash_collection/topcashierfund', 'CashcollectionController@topcashierfund');


Route::post('branchsales/cash_collection/transferfund', 'CashcollectionController@transferfund');
//Route::get('branchsales/cash_collection/accounts', 'CashcollectionController@accounts');
Route::post('branchsales/cash_collection/changeverificationstatus', 'CashcollectionController@changeverificationstatus');
Route::get('branchsales/cash_collection/show/{id}', 'CashcollectionController@show');
Route::get('branchsales/collection_details/show/{id}', 'CashcollectionController@show_details');
Route::get('branchsales/cash_collection/showrecodetail/{id}', 'CashcollectionController@showrecodetail');
Route::get('branchsales/cash_collection/supervisor_cashdeposit_report', 'CashcollectionController@supervisorcashdeposit');
Route::post('branchsales/cash_collection/collect_cash', 'CashcollectionController@collect_cash');
Route::match(['get', 'post'], 'branchsales/cash_collection/filter_result', array('as' => 'export', 'uses' => 'CashcollectionController@filterdetail'));
Route::post('branchsales/cash_collection/exporttopdf', 'CashcollectionController@exporttopdf');
Route::post('branchsales/cash_collection/collectionexporttopdf', 'CashcollectionController@collectionexporttopdf');
Route::match(['get', 'post'], 'branchsales/cash_collection/accounts', 'CashcollectionController@accounts');
Route::post('branchsales/cash_collection/exportaccounts', 'CashcollectionController@exportreconcilationpdf');
Route::post('branchsales/cash_collection/export_view', 'CashcollectionController@exportviewtopdf');

///////////////////////Physical stock//////////////////////////////////
Route::match(['get', 'post'], 'branchsales/physicalstock', array('as' => 'export', 'uses' => 'PhysicalstockController@index'));
Route::post('branchsales/physicalstock/store', 'PhysicalstockController@store');
Route::get('branchsales/physicalstock/edit/{id}', 'PhysicalstockController@edit');
Route::post('branchsales/physicalstock/update', 'PhysicalstockController@update');
Route::post('branchsales/physicalstock/exporttopdf', 'PhysicalstockController@exporttopdf');


Route::get('branchsales/analyst_discussion', 'AnalystdiscussionController@index');
Route::post('branchsales/analyst_discussion', 'AnalystdiscussionController@index');
Route::get('branchsales/analyst_discussion/view/{id}', 'AnalystdiscussionController@view');
Route::post('branchsales/analyst_discussion/sendreply', 'AnalystdiscussionController@sendreply');

Route::get('branchsales/supervisor_wise_sales', 'SalesgraphController@index');
Route::post('branchsales/getsupervisorsale', 'SalesgraphController@getsupervisorsale');

Route::get('branchsales/branch_wise_sales', 'SalesgraphController@branchsale');
Route::post('branchsales/getbranchsale', 'SalesgraphController@getbranchsale');

/* Added by Bergin */

Route::get('branchsales/getbranchsale', 'SalesgraphController@getbranchsale');

//added by anju

Route::get('branchsales/cashier_wise_sales', 'SalesgraphController@cashiersales');
Route::post('branchsales/getcashiersale', 'SalesgraphController@getcashiersales');

Route::get('branchsales/cashier_tips_collection', 'SalesgraphController@cashiertipscollection');
Route::post('branchsales/getcashiertipscollection', 'SalesgraphController@getcashiertipscollection');


Route::get('branchsales/collection_difference', 'SalesgraphController@collectiondifference');
Route::post('branchsales/getcollectiondifference', 'SalesgraphController@getcollectiondifference');


Route::get('branchsales/credit_free_sale', 'SalesgraphController@creditfreesale');
Route::post('branchsales/getcreditfreesale', 'SalesgraphController@getcreditfreesale');

Route::get('branchsales/cash_sale', 'SalesgraphController@cashsale');
Route::post('branchsales/getcashsale', 'SalesgraphController@getcashsale');

Route::get('branchsales/card_sale', 'SalesgraphController@cardsale');
Route::post('branchsales/getcardsale', 'SalesgraphController@getcardsale');


Route::get('branchsales/opening_amount_branch', 'SalesgraphController@openingamountbranch');
Route::post('branchsales/getopeningamountbranch', 'SalesgraphController@getopeningamountbranch');

Route::match(['get', 'post'], 'branchsales/cashier_evening_shift', array('as' => 'export', 'uses' => 'SalesgraphController@cashiereveningshift'));

Route::match(['get', 'post'], 'branchsales/cashier_morning_shift', array('as' => 'export', 'uses' => 'SalesgraphController@cashiermorningshift'));

Route::match(['get', 'post'], 'branchsales/supervisor_branch_list', array('as' => 'export', 'uses' => 'SalesgraphController@supervisorbranchlist'));

Route::post('branchsales/exportdatashift', 'SalesgraphController@exportdatashift');

Route::post('branchsales/exportdatasupervisor', 'SalesgraphController@exportdatasupervisor');

Route::post('branchsales/exportdata', 'SalesgraphController@exportdata');

Route::match(['get', 'post'], 'branchsales/barista_morning_shift', array('as' => 'export', 'uses' => 'BaristareportController@morning'));
Route::post('branchsales/barista_morning_shift/exportdata', 'BaristareportController@exportdatamorning');

Route::match(['get', 'post'], 'branchsales/barista_evening_shift', array('as' => 'export', 'uses' => 'BaristareportController@evening'));
Route::post('branchsales/barista_evening_shift/exportdata', 'BaristareportController@exportdataevening');

Route::get('branchsales/meal_consumption_branchwise', 'SalesgraphController@mealconsumptionbranchwise');
Route::post('branchsales/getmealconsumptionbranchwise', 'SalesgraphController@getmealconsumptionbranchwise');

Route::match(['get', 'post'], 'branchsales/getSupervisorSalesDetails', array('as' => 'export', 'uses' => 'SalesgraphController@supervisorsalesdetails'));
Route::post('branchsales/exportsupervisorsales', 'SalesgraphController@exportsupervisorsalesdetails');

Route::match(['get', 'post'], 'branchsales/getCashierSalesDetails', array('as' => 'export', 'uses' => 'SalesgraphController@cashiersalesdetails'));
Route::post('branchsales/exportcashiersales', 'SalesgraphController@exportcashiersalesdetails');

Route::match(['get', 'post'], 'branchsales/getCashierTipsDetails', array('as' => 'export', 'uses' => 'SalesgraphController@cashiertipsdetails'));
Route::post('branchsales/exportcashiertips', 'SalesgraphController@exportcashiertipsdetails');

Route::match(['get', 'post'], 'branchsales/getCollectionDifferenceDetails', array('as' => 'export', 'uses' => 'SalesgraphController@collectiondifferencedetails'));
Route::post('branchsales/exportCollectionDifference', 'SalesgraphController@exportcollectiondifferencedetails');

Route::match(['get', 'post'], 'branchsales/getCreditDetails', array('as' => 'export', 'uses' => 'SalesgraphController@creditdetails'));
Route::post('branchsales/exportCreditDetails', 'SalesgraphController@exportcreditdetails');

Route::match(['get', 'post'], 'branchsales/getCashDetails', array('as' => 'export', 'uses' => 'SalesgraphController@cashdetails'));
Route::post('branchsales/exportCashDetails', 'SalesgraphController@exportcashdetails');

Route::match(['get', 'post'], 'branchsales/getCardDetails', array('as' => 'export', 'uses' => 'SalesgraphController@carddetails'));
Route::post('branchsales/exportCardDetails', 'SalesgraphController@exportcarddetails');

Route::match(['get', 'post'], 'branchsales/getOpeningDetails', array('as' => 'export', 'uses' => 'SalesgraphController@openingdetails'));
Route::post('branchsales/exportOpeningDetails', 'SalesgraphController@exportopeningdetails');

Route::match(['get', 'post'], 'branchsales/getMealConsumptionDetails', array('as' => 'export', 'uses' => 'SalesgraphController@mealconsumptiondetails'));
Route::post('branchsales/exportMealConsumptionDetails', 'SalesgraphController@exportmealconsumptiondetails');
