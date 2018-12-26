<?php
/*Route::get('hr', function() {
  return View::make('hr/index');
});*/
Route::get('forbidden', 'EmployeeController@forbidden');
Route::post('fetch_notification_count', 'EmployeeController@fetch_notification_count');
Route::get('fetch_notification', 'EmployeeController@fetch_notification');
Route::post('mark_notification', 'EmployeeController@mark_notification');
Route::get('employee/add', 'EmployeeController@add');
Route::post('employee/checkemployeecode', 'EmployeeController@checkemployeecode');
Route::post('employee/store', 'EmployeeController@store');
Route::post('employee/checkemail', 'EmployeeController@checkemail');
Route::post('employee/checkphoneno', 'EmployeeController@checkphoneno');
Route::post('employee/checkpassportno', 'EmployeeController@checkpassportno');
Route::post('employee/checkresidentsid', 'EmployeeController@checkresidentsid');
Route::get('employee', 'EmployeeController@index');
Route::get('employee/Disable/{id}','EmployeeController@Disable');
Route::get('employee/Enable/{id}','EmployeeController@Enable');
Route::match(['get', 'post'],'employee/delete','EmployeeController@deletelist');

Route::get('employee/delete/{id}','EmployeeController@delete');
Route::post('employee/edit/{id}','EmployeeController@edit');
Route::get('employee/edit/{id}','EmployeeController@edit');
Route::get('employee/logout', 'EmployeeController@logout');
Route::post('employee/searchemployee', 'EmployeeController@searchemployee');
Route::post('employee', 'EmployeeController@index');
Route::post('employee/upload_documents', 'EmployeeController@upload_documents');
Route::post('employee/update_upload_documents', 'EmployeeController@update_upload_documents');

Route::get('employeewithjobposition/{id}','EmployeeController@index');
Route::post('employeewithjobposition/employee', 'EmployeeController@index');

Route::get('employeewithcountry/{id}','EmployeeController@index');
Route::post('employeewithcountry/employee', 'EmployeeController@index');

Route::post('employee/edit','EmployeeController@editlist');
Route::get('employee/edit','EmployeeController@editlist');

Route::match(['get', 'post'],'employee/profile', 'EmployeeController@profile');
Route::post('employee/updatepassword', 'EmployeeController@updatepassword');
Route::post('employee/updatepassword', 'EmployeeController@updatepassword');
Route::post('employee/exportdata', 'EmployeeController@exportdata');

//Route::match(['get', 'post'],'employee', array('as'=>'export','uses'=>'EmployeeController@index'));

Route::post('employee/topmanager', 'EmployeeController@get_topmanager');

Route::post('employee/checkgossino', 'EmployeeController@checkgossino');

Route::post('employee/idprofessional', 'EmployeeController@get_idprofessional');