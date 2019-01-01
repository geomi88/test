<?php


Route::get('dashboard', 'CalendarController@index');
Route::post('dashboard/getevents', 'CalendarController@getevents');
Route::get('dashboard/gotoplan/{date}', 'CalendarController@createplan');

Route::get('dashboard/todo', 'TodoController@todo');
Route::post('tasks/todo/savetodo', 'TodoController@savetodo');
Route::post('tasks/todo/deletetodo', 'TodoController@deletetodo');
Route::post('tasks/todo/change_status', 'TodoController@change_status');
Route::post('tasks/todo/complete_task', 'TodoController@complete_task');
Route::post('tasks/todo/get_task_history', 'TodoController@get_task_history');
Route::post('tasks/todo/change_priority', 'TodoController@change_priority');

Route::match(['get', 'post'],'dashboard/plan', array('as'=>'export','uses'=>'PlanController@index'));
Route::get('dashboard/plan/createplan', 'PlanController@createplan');
Route::post('dashboard/plan/saveplan', 'PlanController@saveplan');
Route::post('dashboard/getplanprint', 'PlanController@getplanprint');
Route::get('dashboard/plan/edit/{id}', 'PlanController@edit');
Route::post('dashboard/plan/update', 'PlanController@update');
Route::get('dashboard/plan/deleteplan/{id}', 'PlanController@deleteplan');
Route::post('dashboard/plan/exportdata', 'PlanController@exportdata');

Route::match(['get', 'post'],'tasks/history', array('as'=>'export','uses'=>'HistoryController@index'));
Route::post('tasks/history/exportdata', 'HistoryController@exportdata');

// remove after execution once
Route::get('updatemodulepermission', 'TodoController@deletefromusermdulestemp1');


// View to do routes
Route::match(['get', 'post'],'dashboard/view_todo', array('as'=>'export','uses'=>'ViewtodoController@index'));
Route::get('tasks/view_todo/gettodo/{id}', 'ViewtodoController@gettodo');
Route::get('todohistorywithemployee/{id}','HistoryController@index');
Route::post('todohistorywithemployee/history', 'HistoryController@index');
Route::post('dashboard/view_todo/exportdata', 'ViewtodoController@exportdata');

// View assign task routes
Route::match(['get', 'post'],'dashboard/assign_task', array('as'=>'export','uses'=>'AssigntaskController@index'));
Route::get('dashboard/assign_task/single_employee/{id}', 'AssigntaskController@single_employee');
Route::get('dashboard/assign_task/back/{id}', 'AssigntaskController@index');
Route::post('dashboard/assign_task/back/assign_task', 'AssigntaskController@index');

Route::post('dashboard/assign_task/multiple_employee', 'AssigntaskController@multiple_employee');
Route::post('dashboard/assign_task/backtoemplist', 'AssigntaskController@backtoemplist');
Route::post('dashboard/assign_task/assigntasksingleemployee', 'AssigntaskController@assigntasksingleemployee');
Route::post('dashboard/assign_task/assigntaskmultiemployee', 'AssigntaskController@assigntaskmultiemployee');
//Route::post('dashboard/assign_task/getemployeetodo', 'AssigntaskController@getemployeetodo');
Route::post('dashboard/assign_task/exportdata', 'AssigntaskController@exportdata');
Route::get('dashboard/assign_task/edit/{id}', 'AssigntaskController@edit');
Route::post('dashboard/assign_task/update', 'AssigntaskController@update');

Route::get('dashboard/assign_task/viewplan/{id}', 'PlanController@index');
Route::post('dashboard/assign_task/viewplan/plan', 'PlanController@index');
Route::get('dashboard/assign_task/with_employee/{id}','HistoryController@index');
Route::post('dashboard/assign_task/with_employee/history', 'HistoryController@index');
Route::get('dashboard/assign_task/get_todo/{id}', 'ViewtodoController@gettodo');
Route::post('dashboard/todo/get_task_history', 'TodoController@get_task_history');

// View assign task list
Route::match(['get', 'post'],'dashboard/task_list', array('as'=>'export','uses'=>'TasklistController@index'));
Route::get('dashboard/task_list/edit/{id}', 'TasklistController@edit');
Route::post('dashboard/task_list/update', 'TasklistController@update');
Route::post('dashboard/task_list/exportdata', 'TasklistController@exportdata');

// View Track Task
Route::match(['get', 'post'],'dashboard/track_task', array('as'=>'export','uses'=>'TracktaskController@index'));
Route::post('dashboard/track_task/view_task', 'TracktaskController@exportdata');
Route::post('dashboard/track_task/exportdata', 'TracktaskController@exportdata');
Route::get('dashboard/track_task/edit/{id}', 'TracktaskController@edit');
Route::get('dashboard/track_task/editcompletedtask/{id}', 'TracktaskController@editcompletedtask');
Route::post('dashboard/track_task/update', 'TracktaskController@update');
Route::get('dashboard/track_task/deleteplan/{id}', 'TracktaskController@deleteplan');

Route::match(['get', 'post'],'dashboard/suggestion', array('as'=>'export','uses'=>'SuggestionController@index'));
Route::get('dashboard/suggestion/create', 'SuggestionController@create');
Route::post('dashboard/suggestion/save', 'SuggestionController@save');
Route::get('dashboard/suggestion/edit/{id}', 'SuggestionController@edit');
Route::post('dashboard/suggestion/update', 'SuggestionController@update');
Route::get('dashboard/suggestion/delete/{id}', 'SuggestionController@delete');
Route::post('dashboard/suggestion/exportdata', 'SuggestionController@exportdata');


Route::get('dashboard/view_suggestions', 'ViewsuggestionController@view_suggestions');
Route::post('tasks/view_suggestions/deletesuggestion', 'ViewsuggestionController@deletesuggestion');
Route::post('tasks/view_suggestions/complete_task', 'ViewsuggestionController@complete_task');

Route::post('tasks/view_suggestions/downloadsuggestion', 'ViewsuggestionController@downloadsuggestion');

Route::get('dashboard/employeekpi', 'EmployeekpiController@index');

