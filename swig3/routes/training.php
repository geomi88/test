<?php
//////////////////////////////////////Training/////////////////////////////
Route::get('training', 'IndexController@index'); 
Route::post('training/savemeeting', 'TrainingController@savemeeting');
Route::match(['get', 'post'],'training/createtraining', array('as'=>'export','uses'=>'TrainingController@createtraining'));

Route::get('training/training_list', 'TrainingController@index');
Route::get('training/view/{id}','TrainingController@view');
Route::post('training/training_list', 'TrainingController@index');
Route::post('training/checkroomavailability', 'TrainingController@checkroomavailability');
Route::post('training/checkemployeeavailability', 'TrainingController@checkemployeeavailability');

Route::get('training/edit/{id}','TrainingController@edit');
Route::post('training/update','TrainingController@update');
Route::post('training/updateparticipation','TrainingController@updateparticipation');
Route::get('training/delete/{id}','TrainingController@deletetraining');
Route::post('training/addnote','TrainingController@addnote');

/////////////////////////////Training Perfomance/////////////////////////////
Route::match(['get', 'post'],'training/training_performance', array('as'=>'export','uses'=>'TrainingperformanceController@index'));
Route::get('training/training_performance/add', 'TrainingperformanceController@add');
Route::post('training/training_performance/store', 'TrainingperformanceController@store');
Route::post('training/training_performance/getempdata', 'TrainingperformanceController@getempdata');

//Route::match(['get', 'post'],'training/puncheditindex', array('as'=>'export','uses'=>'TrainingperformanceController@editindex'));
//Route::get('training/training_performance/edit/{id}','TrainingperformanceController@edit');
Route::get('training/training_performance/delete/{id}','TrainingperformanceController@delete');
//Route::post('training/training_performance/update', 'TrainingperformanceController@update');
//
Route::match(['get', 'post'],'training/getemployesrating', 'PunchviewController@getemployesrating');
Route::match(['get', 'post'],'training/getmonthwiseperformance', 'PunchviewController@getmonthwiseperformance');

Route::match(['get', 'post'],'training/getnewemployesrating', 'PunchviewController@getnewemployesrating');
Route::match(['get', 'post'],'training/getnewmonthwiseperformance', 'PunchviewController@getnewmonthwiseperformance');

//Route::post('training/punch_view/exportdata', 'PunchviewController@exportdata');
//
Route::match(['get', 'post'],'training/punchreport', 'PunchreportController@index');
Route::match(['get', 'post'],'training/punchreportnew', 'PunchreportController@newindex');
?>