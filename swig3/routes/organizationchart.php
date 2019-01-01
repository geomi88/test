<?php
/////////////////////////////////Organization Chart/////////////////////////////


Route::get('organizationchart', 'IndexController@index'); 

Route::get('organizationchart/organizationchart/add', 'OrganizationchartController@add');
Route::get('organizationchart/organizationchart/addjobwise', 'OrganizationchartController@addjobwise');
Route::get('organizationchart/organizationchart/index', 'OrganizationchartController@index');
Route::post('organizationchart/organizationchart/getemployeesbyjob', 'OrganizationchartController@getemployeesbyjob');
Route::post('organizationchart/organizationchart/savechart', 'OrganizationchartController@store');
Route::post('organizationchart/organizationchart/updatechart', 'OrganizationchartController@updatechart');

Route::match(['get', 'post'],'organizationchart/organizationchart/getchartlist', array('as'=>'export','uses'=>'OrganizationchartController@getchartlist'));
Route::match(['get', 'post'],'organizationchart/organizationchart/editlist', array('as'=>'export','uses'=>'OrganizationchartController@editlist'));

Route::get('organizationchart/organizationchart/viewchart/{id}', 'OrganizationchartController@viewchart');
Route::get('organizationchart/organizationchart/edit/{id}', 'OrganizationchartController@edit');


//////////////////////////////new chart///////////////////////////////////////

Route::get('organizationchart/organizationchartnew/add', 'NewOrganizationChart@add');
Route::get('organizationchart/organizationchartnew/addjobwise', 'NewOrganizationChart@addjobwise');
Route::get('organizationchart/organizationchartnew/index', 'NewOrganizationChart@index');
Route::post('organizationchart/organizationchartnew/getemployeesbyjob', 'NewOrganizationChart@getemployeesbyjob');
Route::post('organizationchart/organizationchartnew/savechart', 'NewOrganizationChart@store');
Route::post('organizationchart/organizationchartnew/updatechart', 'NewOrganizationChart@updatechart');
Route::post('organizationchart/organizationchartnew/getprofiledetails', 'NewOrganizationChart@getprofiledetails');
Route::post('organizationchart/getprofiledetails', 'NewOrganizationChart@getprofiledetailscreate');

Route::match(['get', 'post'],'organizationchart/organizationchartnew/getchartlist', array('as'=>'export','uses'=>'NewOrganizationChart@getchartlist'));
Route::match(['get', 'post'],'organizationchart/organizationchartnew/editlist', array('as'=>'export','uses'=>'NewOrganizationChart@editlist'));

Route::get('organizationchart/organizationchartnew/viewchart/{id}', 'NewOrganizationChart@viewchart');
Route::get('organizationchart/organizationchartnew/edit/{id}', 'NewOrganizationChart@edit');
Route::get('organizationchart/organizationchartnew/delete/{id}', 'NewOrganizationChart@delete');
Route::get('organizationchart/organizationchartnew/Enable/{id}', 'NewOrganizationChart@Enable');
Route::get('organizationchart/organizationchartnew/Disable/{id}', 'NewOrganizationChart@Disable');

/////////////////////////////Policy/////////////////////////////
Route::match(['get', 'post'],'organizationchart/policy', array('as'=>'export','uses'=>'PolicyController@index'));
Route::get('organizationchart/policy/add', 'PolicyController@add');
Route::post('organizationchart/policy/store', 'PolicyController@store');
Route::post('organizationchart/policy/update', 'PolicyController@update');

Route::get('organizationchart/policy/view/{id}','PolicyController@view');
Route::get('organizationchart/policy/edit/{id}','PolicyController@edit');
Route::get('organizationchart/policy/delete/{id}','PolicyController@delete');

Route::post('organizationchart/policy/exportdata', 'PolicyController@exportdata');

Route::match(['get', 'post'],'organizationchart/policy_list', array('as'=>'export','uses'=>'PolicyController@listindex'));


/////////////////////////////Punch Perfomance/////////////////////////////
Route::match(['get', 'post'],'organizationchart/punch_performance', array('as'=>'export','uses'=>'PunchperformanceController@index'));
Route::get('organizationchart/punch_performance/add', 'PunchperformanceController@add');
Route::post('organizationchart/punch_performance/store', 'PunchperformanceController@store');
Route::post('organizationchart/punch_performance/getempdata', 'PunchperformanceController@getempdata');

Route::match(['get', 'post'],'organizationchart/puncheditindex', array('as'=>'export','uses'=>'PunchperformanceController@editindex'));
Route::get('organizationchart/punch_performance/edit/{id}','PunchperformanceController@edit');
Route::get('organizationchart/punch_performance/delete/{id}','PunchperformanceController@delete');
Route::post('organizationchart/punch_performance/update', 'PunchperformanceController@update');

Route::match(['get', 'post'],'organizationchart/getemployesrating', 'PunchviewController@getemployesrating');
Route::match(['get', 'post'],'organizationchart/getmonthwiseperformance', 'PunchviewController@getmonthwiseperformance');
Route::post('organizationchart/punch_view/exportdata', 'PunchviewController@exportdata');

Route::match(['get', 'post'],'organizationchart/punchreport', 'PunchreportController@index');
?>