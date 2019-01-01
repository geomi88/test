<?php
//////////////////////////////////////Meeting/////////////////////////////
Route::get('meeting', 'IndexController@index'); 
//Route::get('meeting/createmeeting', 'MeetingController@createmeeting');
Route::post('meeting/savemeeting', 'MeetingController@savemeeting');
Route::match(['get', 'post'],'meeting/createmeeting', array('as'=>'export','uses'=>'MeetingController@createmeeting'));
Route::get('meeting/agenda/add_agenda/{id}','AgendaController@add_agenda');
Route::post('meeting/agenda/save_agenda', 'AgendaController@save_agenda');
Route::get('meeting/meeting_list', 'MeetingController@index');
Route::get('meeting/view/{id}','MeetingController@view');
Route::post('meeting/meeting_list', 'MeetingController@index');
Route::post('meeting/checkroomavailability', 'MeetingController@checkroomavailability');
Route::post('meeting/checkemployeeavailability', 'MeetingController@checkemployeeavailability');
Route::get('meeting/edit/{id}','MeetingController@edit');
Route::post('meeting/update','MeetingController@update');
Route::post('meeting/updateparticipation','MeetingController@updateparticipation');
Route::get('meeting/agenda/edit/{id}','AgendaController@edit');
Route::post('meeting/agenda/update','AgendaController@update');
Route::get('meeting/delete/{id}','MeetingController@deletemeeting');

Route::post('meeting/addnote','MeetingController@addnote');
?>