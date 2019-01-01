<?php
/////////////////////////////reception/////////////////////////////
Route::get('reception', 'IndexController@index'); 

Route::match(['get', 'post'],'reception/visitors_log', array('as'=>'export','uses'=>'ReceptionController@index'));
Route::get('reception/visitors_log/add', 'ReceptionController@add');
Route::post('reception/visitors_log/store', 'ReceptionController@store');
Route::post('reception/visitors_log/update', 'ReceptionController@update');

Route::get('reception/visitors_log/view/{id}','ReceptionController@view');
Route::get('reception/visitors_log/edit/{id}','ReceptionController@edit');
Route::get('reception/visitors_log/delete/{id}','ReceptionController@delete');

Route::post('reception/visitors_log/exportdata', 'ReceptionController@exportdata');

Route::match(['get', 'post'],'reception/visitors_list', array('as'=>'export','uses'=>'VisitorlistController@index'));
Route::post('reception/visitors_list/exportdata', 'VisitorlistController@exportdata');

?>