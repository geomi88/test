<?php
//////////////////////////////////////Check list/////////////////////////////
Route::get('checklist', 'IndexController@index'); 


//Check List category
Route::match(['get', 'post'],'checklist/check_list', array('as'=>'export','uses'=>'ChecklistController@index'));
Route::get('checklist/check_list/add', 'ChecklistController@add');
Route::post('checklist/check_list/store', 'ChecklistController@store');
Route::post('checklist/check_list/checkcategories', 'ChecklistController@checkcategories');
Route::get('checklist/check_list/edit/{edit_id}', 'ChecklistController@edit');
Route::post('checklist/check_list/update', 'ChecklistController@update');
Route::get('checklist/check_list/Disable/{id}', 'ChecklistController@Disable');
Route::get('checklist/check_list/Enable/{id}', 'ChecklistController@Enable');
Route::get('checklist/check_list/delete/{id}', 'ChecklistController@delete');
Route::post('checklist/check_list/exportdata', 'ChecklistController@exportdata');


//Check List category
Route::match(['get', 'post'],'masterresources/check_list_category', array('as'=>'export','uses'=>'ChecklistcategoryController@index'));
Route::get('masterresources/check_list_category/add', 'ChecklistcategoryController@add');
Route::post('masterresources/check_list_category/store', 'ChecklistcategoryController@store');
Route::post('masterresources/check_list_category/checkcategories', 'ChecklistcategoryController@checkcategories');
Route::get('masterresources/check_list_category/edit/{edit_id}', 'ChecklistcategoryController@edit');
Route::post('masterresources/check_list_category/update', 'ChecklistcategoryController@update');
Route::get('masterresources/check_list_category/Disable/{id}', 'ChecklistcategoryController@Disable');
Route::get('masterresources/check_list_category/Enable/{id}', 'ChecklistcategoryController@Enable');
Route::get('masterresources/check_list_category/delete/{id}', 'ChecklistcategoryController@delete');
Route::post('masterresources/check_list_category/exportdata', 'ChecklistcategoryController@exportdata');

//Check List category
Route::get('checklist/checklist_entry', 'ChecklistentryController@index');
Route::get('checklist/checklist_entry/getcheckpoints/{id}', 'ChecklistentryController@getcheckpoints');
Route::post('checklist/checklist_entry/store', 'ChecklistentryController@store');
Route::post('checklist/checklist_entry/getbranchentry', 'ChecklistentryController@getbranchentry');

//Check List Report
Route::match(['get', 'post'],'checklist/checklist_report', array('as'=>'export','uses'=>'ChecklistreportController@index'));
Route::get('checklist/checklist_report/getdetails/{id}', 'ChecklistreportController@getdetails');
Route::post('checklist/checklist_report/exportdata', 'ChecklistreportController@exportdata');
Route::get('checklistwithrating/{id}','ChecklistreportController@index');
Route::post('checklistwithrating/checklist_report', 'ChecklistreportController@index');


//Warnings
Route::match(['get', 'post'],'checklist/warnings', array('as'=>'export','uses'=>'WarningsController@index'));
Route::match(['get', 'post'],'checklist/editwarnings', array('as'=>'export','uses'=>'WarningsController@editindex'));
Route::get('checklist/warnings/add', 'WarningsController@add');
Route::post('checklist/warnings/store', 'WarningsController@store');
Route::get('checklist/warnings/edit/{edit_id}', 'WarningsController@edit');
Route::post('checklist/warnings/update', 'WarningsController@update');
Route::get('checklist/warnings/delete/{id}', 'WarningsController@delete');
Route::post('checklist/warnings/getbranchemployees', 'WarningsController@getbranchemployees');
Route::post('checklist/warnings/exportdata', 'WarningsController@exportdata');

//Warnings Report
Route::match(['get', 'post'],'checklist/warnings_report', array('as'=>'export','uses'=>'WarningreportController@index'));
Route::post('checklist/warnings_report/exportdata', 'WarningreportController@exportdata');
Route::get('warningwithcategory/{id}','WarningreportController@index');
Route::post('warningwithcategory/warnings_report', 'WarningreportController@index');

// Warning graph
Route::get('checklist/graphindex', 'ChecklistcategoryController@graphindex');
Route::post('checklist/getcategorygraph', 'ChecklistcategoryController@getcategorygraph');

// Check Points Graph
Route::get('checklist/ratingindex', 'ChecklistentryController@graphindex');
Route::post('checklist/getratinggraph', 'ChecklistentryController@getratinggraph');

?>