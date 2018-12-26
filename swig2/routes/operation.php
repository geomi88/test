<?php

//Route::get('operation', function() {
//    return View::make('operation/index');
//});
Route::get('operation', 'IndexController@index');
////////////////////resource allocation//////////////////
Route::get('operation/resource_allocation', 'ResourceallocationController@index');
Route::post('operation/resource_allocation/showbranch', 'ResourceallocationController@showbranch');
Route::post('operation/resource_allocation/show_resource_details', 'ResourceallocationController@show_resource_details');
Route::post('operation/resource_allocation/show_shifts', 'ResourceallocationController@show_shifts');



/////////////supervisors////////////////////
Route::post('operation/resource_allocation/show_supervisors', 'ResourceallocationController@show_supervisors');
Route::post('operation/resource_allocation/save_supervisors', 'ResourceallocationController@save_supervisors');
Route::post('operation/resource_allocation/show_allocated_supervisors', 'ResourceallocationController@show_allocated_supervisors');
Route::post('operation/resource_allocation/deallocate_supervisors', 'ResourceallocationController@deallocate_supervisors');

//////////////////////////////////cashiers////////////////////////


Route::post('operation/resource_allocation/show_allocated_cashiers', 'ResourceallocationController@show_allocated_cashiers');
Route::post('operation/resource_allocation/show_cashiers', 'ResourceallocationController@show_cashiers');
Route::post('operation/resource_allocation/save_cashiers_shift1', 'ResourceallocationController@save_cashiers_shift1');
Route::post('operation/resource_allocation/deallocated_cashiers', 'ResourceallocationController@deallocated_cashiers');


/////////////////////////////////////Baristas////////////////////////////

Route::post('operation/resource_allocation/show_allocated_baristas', 'ResourceallocationController@show_allocated_baristas');
Route::post('operation/resource_allocation/show_baristas', 'ResourceallocationController@show_baristas');
Route::post('operation/resource_allocation/save_barista', 'ResourceallocationController@save_barista');
Route::post('operation/resource_allocation/deallocated_baristas', 'ResourceallocationController@deallocated_baristas');

////////////////////////nationality/////////////////////
Route::post('operation/resource_allocation/check_nationality', 'ResourceallocationController@check_nationality');

///////////////////////////////////regional manager/////////////////////////////////
Route::post('operation/resource_allocation/show_regional_managers', 'ResourceallocationController@show_regional_managers');
Route::post('operation/resource_allocation/save_regional_managers', 'ResourceallocationController@save_regional_managers');
Route::post('operation/resource_allocation/show_allocated_regional_managers', 'ResourceallocationController@show_allocated_regional_managers');
//  Route::post('operation/resource_allocation/deallocate_regional_managers', 'ResourceallocationController@deallocate_regional_managers') ;
Route::post('operation/resource_allocation/show_region_modal', 'ResourceallocationController@show_region_modal');

Route::post('operation/resource_allocation/free_region_employee', 'ResourceallocationController@free_region_employee');
Route::post('operation/resource_allocation/edit_region_managers', 'ResourceallocationController@edit_region_managers');


/////////////////////////////////////Area managers///////////////////////////////////
Route::post('operation/resource_allocation/show_area_managers', 'ResourceallocationController@show_area_managers');
Route::post('operation/resource_allocation/save_area_managers', 'ResourceallocationController@save_area_managers');
Route::post('operation/resource_allocation/show_area_modal', 'ResourceallocationController@show_area_modal');

Route::post('operation/resource_allocation/free_area_employee', 'ResourceallocationController@free_area_employee');
Route::post('operation/resource_allocation/edit_area_managers', 'ResourceallocationController@edit_area_managers');

Route::post('operation/resource_allocation/show_allocated_area_managers', 'ResourceallocationController@show_allocated_area_managers');
//   Route::post('operation/resource_allocation/deallocate_area_managers', 'ResourceallocationController@deallocate_area_managers') ;
/////////// sending mails//////////////////
Route::post('operation/resource_allocation/send_region_manager_mail', 'ResourceallocationController@send_region_manager_mail');
Route::post('operation/resource_allocation/send_area_manager_mail', 'ResourceallocationController@send_area_manager_mail');
Route::post('operation/resource_allocation/send_branch_manager_mail', 'ResourceallocationController@send_branch_manager_mail');
Route::post('operation/resource_allocation/send_cashier_mail', 'ResourceallocationController@send_cashier_mail');
Route::post('operation/resource_allocation/send_barista_mail', 'ResourceallocationController@send_barista_mail');

/////////////////////resource listing//////////////////////////////
Route::get('operation/resource_listing', 'ResourcelistingController@index');
Route::post('operation/resource_listing/get_region_manager', 'ResourcelistingController@get_region_manager');
Route::post('operation/resource_listing/get_area_manager', 'ResourcelistingController@get_area_manager');
Route::post('operation/resource_listing/get_supervisor', 'ResourcelistingController@get_supervisor');
Route::post('operation/resource_listing/get_cashier', 'ResourcelistingController@get_cashier');
Route::post('operation/resource_listing/get_barista', 'ResourcelistingController@get_barista');
Route::post('operation/resource_listing/show_shifts', 'ResourcelistingController@show_shifts');

Route::post('operation/resource_allocation/show_branch_supervisor_modal', 'ResourceallocationController@show_branch_supervisor_modal');
Route::post('operation/resource_allocation/edit_branch_supervisor', 'ResourceallocationController@edit_branch_supervisor');
Route::post('operation/resource_allocation/show_branch_barista_modal', 'ResourceallocationController@show_branch_barista_modal');
Route::post('operation/resource_allocation/edit_branch_barista', 'ResourceallocationController@edit_branch_barista');
Route::post('operation/resource_allocation/show_branch_cashier_modal', 'ResourceallocationController@show_branch_cashier_modal');
Route::post('operation/resource_allocation/edit_branch_cashier', 'ResourceallocationController@edit_branch_cashier');
Route::post('operation/resource_allocation/free_branch_supervisor', 'ResourceallocationController@free_branch_supervisor');
Route::post('operation/resource_allocation/free_branch_barista', 'ResourceallocationController@free_branch_barista');
Route::post('operation/resource_allocation/free_branch_cashier', 'ResourceallocationController@free_branch_cashier');


Route::post('operation/resource_allocation/regional_manager_allocations_modal', 'ResourceallocationController@regional_manager_allocations_modal');
Route::post('operation/resource_allocation/area_manager_allocations_modal', 'ResourceallocationController@area_manager_allocations_modal');
Route::post('operation/resource_allocation/supervisor_allocations_modal', 'ResourceallocationController@supervisor_allocations_modal');
Route::post('operation/resource_allocation/barista_allocations_modal', 'ResourceallocationController@barista_allocations_modal');
Route::post('operation/resource_allocation/cashier_allocations_modal', 'ResourceallocationController@cashier_allocations_modal');




/////////////////////pos_cashier_edit listing//////////////////////////////
Route::get('operation/pos_cashier_edit', 'PoscashiereditController@index');
Route::post('operation/pos_cashier_edit/show_shifts', 'PoscashiereditController@show_shifts');
Route::post('operation/pos_cashier_edit/store', 'PoscashiereditController@store');
Route::post('operation/pos_cashier_edit/save', 'PoscashiereditController@save');
Route::post('operation/pos_cashier_edit/show_cashier', 'PoscashiereditController@show_cashiers');
Route::post('operation/pos_cashier_edit/show_supervisors', 'PoscashiereditController@show_supervisors');

Route::post('operation/pos_cashier_edit/gettaxamount', 'PoscashiereditController@gettaxamount');
/////////////////////pos_supervisor_edit listing//////////////////////////////
Route::get('operation/pos_supervisor_edit', 'PossupervisoreditController@index');
Route::post('operation/pos_supervisor_edit/show_shifts', 'PossupervisoreditController@show_shifts');
Route::post('operation/pos_supervisor_edit/store', 'PossupervisoreditController@store');
Route::post('operation/pos_supervisor_edit/save', 'PossupervisoreditController@save');
Route::post('operation/pos_supervisor_edit/show_supervisors', 'PossupervisoreditController@show_supervisors');

Route::post('operation/pos_supervisor_edit/gettaxamount', 'PoscashiereditController@gettaxamount');