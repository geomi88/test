<?php

Route::get('crm', 'IndexController@index');
Route::get('crm/enter_data', 'EnterDataController@index');
Route::post('crm/enter_data', 'EnterDataController@index');
Route::get('crm/enter_data/add', 'EnterDataController@add');
Route::post('crm/enter_data/store', 'EnterDataController@store');
Route::post('crm/enter_data/checkphonenumber', 'EnterDataController@check_phone_number');

/* --- Crm feedbacks --- */

Route::get('crm/crm_feedback/add', 'CrmfeedbackController@add');
Route::post('crm/crm_feedback/store', 'CrmfeedbackController@store');

/* -------- All Crm -------- */

Route::get("crm/all_crm_feedback", "AllcrmfeedbackController@index");
Route::post("crm/all_crm_feedback", "AllcrmfeedbackController@index");
Route::get("crm/all_crm_feedback/feedback_view/{id}/{page}", "AllcrmfeedbackController@show");
Route::post("crm/crm_feedback_followup/store", "AllcrmfeedbackController@followup_store");
Route::get("crm/crm_feedback_followup/close/{id}", "AllcrmfeedbackController@close_feedback_followup");


/* ------------- All Customers ----------- */

Route::get("crm/all_customers", "AllcustomersController@index");
Route::post("crm/all_customers", "AllcustomersController@index");
Route::get("crm/all_customers/view/{mobile}/{branch}/{created_by}", "AllcustomersController@view");
Route::post("crm/all_customers/print_view", "AllcustomersController@print_data");

/*------------------CRM Followups-----------------------*/
Route::match(['get','post'],"crm/crm_followups", "CrmfollowupController@index");
