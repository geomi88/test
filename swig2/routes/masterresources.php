<?php
//Route::get('/', ['middleware' => 'auth', function () {
//    //
//}]);
/*Route::get('dashboard', function() {
    return View::make('dashboard/index');
});*/

Route::get('masterresources', 'ResourcesController@index');
//companies
Route::match(['get', 'post'],'masterresources/companies', array('as'=>'export','uses'=>'CompaniesController@index'));
Route::get('masterresources/companies/add', 'CompaniesController@add');
Route::post('masterresources/companies/store', 'CompaniesController@store');
Route::post('masterresources/companies/checkcompanies', 'CompaniesController@checkcompanies');
Route::get('masterresources/companies/edit/{edit_id}', 'CompaniesController@edit');
Route::post('masterresources/companies/update', 'CompaniesController@update');
Route::get('masterresources/companies/Disable/{id}', 'CompaniesController@Disable');
Route::get('masterresources/companies/Enable/{id}', 'CompaniesController@Enable');
Route::get('masterresources/companies/delete/{id}', 'CompaniesController@delete');
Route::post('masterresources/companies/exportdata', 'CompaniesController@exportdata');


//region
Route::match(['get', 'post'],'masterresources/regions', array('as'=>'export','uses'=>'RegionController@index'));
Route::get('masterresources/region/add', 'RegionController@add');
Route::post('masterresources/region/store', 'RegionController@store');
Route::get('masterresources/region/Disable/{id}', 'RegionController@Disable');
Route::get('masterresources/region/Enable/{id}', 'RegionController@Enable');
Route::get('masterresources/region/delete/{id}', 'RegionController@delete');
Route::get('masterresources/region/edit/{id}', 'RegionController@edit');
Route::post('masterresources/region/update', 'RegionController@update');
Route::post('masterresources/region/checkregionname', 'RegionController@checkregionname');
Route::post('masterresources/region/exportdata', 'RegionController@exportdata');

// Branch
Route::match(['get', 'post'],'masterresources/branches', array('as'=>'export','uses'=>'BranchController@index'));
Route::get('masterresources/branch/add', 'BranchController@add');
Route::post('masterresources/branch/store', 'BranchController@store');
Route::post('getbranchregions', 'BranchController@getbranchregions');
Route::post('masterresources/branch/getregiondetails', 'BranchController@getregiondetails');
Route::get('masterresources/branch/Disable/{id}', 'BranchController@Disable');
Route::get('masterresources/branch/Enable/{id}', 'BranchController@Enable');
Route::get('masterresources/branch/delete/{id}', 'BranchController@delete');
Route::get('masterresources/branch/edit/{id}', 'BranchController@edit');
Route::post('masterresources/branch/update', 'BranchController@update');
Route::post('masterresources/branch/checkbranchcode', 'BranchController@checkbranchcode');
Route::post('masterresources/branch/getjobtimings', 'BranchController@getjobtimings');
Route::post('masterresources/branch/exportdata', 'BranchController@exportdata');



// docs upload routing
Route::get('masterresources/docs/add', 'DocsController@add');
Route::post('masterresources/docs/store', 'DocsController@store');
//Route::get('mis', 'DocsController@index') ;
//Route::get('mis/{groupId}', 'DocsController@show') ;
//Route::get('masterresources/planning/add', 'DocsController@add');
//Route::post('masterresources/planning/store', 'DocsController@store');
// departments
Route::match(['get', 'post'],'masterresources/department', array('as'=>'export','uses'=>'DepartmentController@index'));
Route::get('masterresources/department/add', 'DepartmentController@add');
Route::post('masterresources/department/store', 'DepartmentController@store');
Route::get('masterresources/department/edit/{edit_id}', 'DepartmentController@edit');
Route::post('masterresources/department/update', 'DepartmentController@update');
Route::get('masterresources/department/Disable/{id}', 'DepartmentController@Disable');
Route::get('masterresources/department/Enable/{id}', 'DepartmentController@Enable');
Route::get('masterresources/department/delete/{id}', 'DepartmentController@delete');
Route::post('masterresources/department/exportdata', 'DepartmentController@exportdata');

//Inventory category
Route::match(['get', 'post'],'masterresources/inventory_category', array('as'=>'export','uses'=>'InventorycategoryController@index'));
Route::get('masterresources/inventory_category/add', 'InventorycategoryController@add');
Route::post('masterresources/inventory_category/store', 'InventorycategoryController@store');
Route::post('masterresources/inventory_category/checkcategories', 'InventorycategoryController@checkcategories');
Route::get('masterresources/inventory_category/edit/{edit_id}', 'InventorycategoryController@edit');
Route::post('masterresources/inventory_category/update', 'InventorycategoryController@update');
Route::get('masterresources/inventory_category/Disable/{id}', 'InventorycategoryController@Disable');
Route::get('masterresources/inventory_category/Enable/{id}', 'InventorycategoryController@Enable');
Route::get('masterresources/inventory_category/delete/{id}', 'InventorycategoryController@delete');
Route::post('masterresources/inventory_category/exportdata', 'InventorycategoryController@exportdata');

//Inventory sub category
Route::get('masterresources/inventory_sub_category', 'InventorysubcategoryController@index');
Route::get('masterresources/inventory_sub_category/add', 'InventorysubcategoryController@add');
Route::post('masterresources/inventory_sub_category/store', 'InventorysubcategoryController@store');
Route::post('masterresources/inventory_sub_category/checksubcategories', 'InventorysubcategoryController@checksubcategories');
Route::get('masterresources/inventory_sub_category/edit/{edit_id}', 'InventorysubcategoryController@edit');
Route::post('masterresources/inventory_sub_category/update', 'InventorysubcategoryController@update');
Route::get('masterresources/inventory_sub_category/Disable/{id}', 'InventorysubcategoryController@Disable');
Route::get('masterresources/inventory_sub_category/Enable/{id}', 'InventorysubcategoryController@Enable');
Route::get('masterresources/inventory_sub_category/delete/{id}', 'InventorysubcategoryController@delete');

//Warehouse
Route::match(['get', 'post'],'masterresources/warehouse', array('as'=>'export','uses'=>'WarehouseController@index'));
Route::get('masterresources/warehouse/add', 'WarehouseController@add');
Route::post('masterresources/warehouse/store', 'WarehouseController@store');
Route::post('masterresources/warehouse/checkwarehouse', 'WarehouseController@checkwarehouse');
Route::get('masterresources/warehouse/edit/{edit_id}', 'WarehouseController@edit');
Route::post('masterresources/warehouse/update', 'WarehouseController@update');
Route::get('masterresources/warehouse/Disable/{id}', 'WarehouseController@Disable');
Route::get('masterresources/warehouse/Enable/{id}', 'WarehouseController@Enable');
Route::get('masterresources/warehouse/delete/{id}', 'WarehouseController@delete');
Route::post('masterresources/warehouse/exportdata', 'WarehouseController@exportdata');

//Spots
Route::match(['get', 'post'],'masterresources/spot', array('as'=>'export','uses'=>'SpotController@index'));
Route::get('masterresources/spot/add', 'SpotController@add');
Route::post('masterresources/spot/store', 'SpotController@store');
Route::post('masterresources/spot/checkspots', 'SpotController@checkspots');
Route::get('masterresources/spot/edit/{edit_id}', 'SpotController@edit');
Route::post('masterresources/spot/update', 'SpotController@update');
Route::get('masterresources/spot/Disable/{id}', 'SpotController@Disable');
Route::get('masterresources/spot/Enable/{id}', 'SpotController@Enable');
Route::get('masterresources/spot/delete/{id}', 'SpotController@delete');
Route::post('masterresources/spot/exportdata', 'SpotController@exportdata');

//Units
Route::get('masterresources/units', 'UnitsController@index');
Route::get('masterresources/units/add', 'UnitsController@add');
Route::post('masterresources/units/store', 'UnitsController@store');
Route::post('masterresources/units/checkunits', 'UnitsController@checkunits');
Route::get('masterresources/units/edit/{edit_id}', 'UnitsController@edit');
Route::post('masterresources/units/cupdate', 'UnitsController@cupdate');
Route::post('masterresources/units/supdate', 'UnitsController@supdate');
Route::get('masterresources/units/Disable/{id}', 'UnitsController@Disable');
Route::get('masterresources/units/Enable/{id}', 'UnitsController@Enable');
Route::get('masterresources/units/delete/{id}', 'UnitsController@delete');
Route::post('masterresources/units/setsession', 'UnitsController@setsession');


//shifts
Route::match(['get', 'post'],'masterresources/job_shifts', array('as'=>'export','uses'=>'JobshiftController@index'));
Route::get('masterresources/job_shifts/add', 'JobshiftController@add');
Route::post('masterresources/job_shifts/store', 'JobshiftController@store');
Route::post('masterresources/job_shifts/checkshifts', 'JobshiftController@checkshifts');
Route::get('masterresources/job_shifts/edit/{id}', 'JobshiftController@edit');
Route::post('masterresources/job_shifts/update', 'JobshiftController@update');
Route::get('masterresources/job_shifts/Disable/{id}', 'JobshiftController@Disable');
Route::get('masterresources/job_shifts/Enable/{id}', 'JobshiftController@Enable');
Route::get('masterresources/job_shifts/delete/{id}', 'JobshiftController@delete');
Route::post('masterresources/job_shifts/exportdata', 'JobshiftController@exportdata');


//posreasons
Route::match(['get', 'post'],'masterresources/pos_reasons', array('as'=>'export','uses'=>'PosreasonController@index'));
Route::get('masterresources/pos_reasons/add', 'PosreasonController@add');
Route::post('masterresources/pos_reasons/store', 'PosreasonController@store');
Route::post('masterresources/pos_reasons/checkpos', 'PosreasonController@checkpos');
Route::get('masterresources/pos_reasons/edit/{id}', 'PosreasonController@edit');
Route::post('masterresources/pos_reasons/update', 'PosreasonController@update');
Route::get('masterresources/pos_reasons/Disable/{id}', 'PosreasonController@Disable');
Route::get('masterresources/pos_reasons/Enable/{id}', 'PosreasonController@Enable');
Route::get('masterresources/pos_reasons/delete/{id}', 'PosreasonController@delete');
Route::get('masterresources/pos_reasons/exportdata', 'PosreasonController@exportdata');

//Positions
Route::match(['get', 'post'],'masterresources/job_positions', array('as'=>'export','uses'=>'JobpositionController@index'));
Route::get('masterresources/job_positions/add', 'JobpositionController@add');
Route::post('masterresources/job_positions/store', 'JobpositionController@store');
Route::post('masterresources/job_positions/checkjobpos', 'JobpositionController@checkjobpos');
Route::get('masterresources/job_positions/edit/{edit_id}', 'JobpositionController@edit');
Route::post('masterresources/job_positions/update', 'JobpositionController@update');
Route::get('masterresources/job_positions/Disable/{id}', 'JobpositionController@Disable');
Route::get('masterresources/job_positions/Enable/{id}', 'JobpositionController@Enable');
Route::get('masterresources/job_positions/delete/{id}', 'JobpositionController@delete');
Route::post('masterresources/job_positions/exportdata', 'JobpositionController@exportdata');

////////////////////////areas////////////////////////////////
Route::match(['get', 'post'],'masterresources/areas', array('as'=>'export','uses'=>'AreaController@index'));
Route::get('masterresources/areas/add', 'AreaController@add');
Route::post('masterresources/areas/store', 'AreaController@store');
Route::get('masterresources/areas/Disable/{id}', 'AreaController@Disable');
Route::get('masterresources/areas/Enable/{id}', 'AreaController@Enable');
Route::get('masterresources/areas/delete/{id}', 'AreaController@delete');
Route::get('masterresources/areas/edit/{id}', 'AreaController@edit');
Route::post('masterresources/areas/update', 'AreaController@update');
Route::post('masterresources/areas/checkareaname', 'AreaController@checkareaname');
Route::post('masterresources/areas/exportdata', 'AreaController@exportdata');


///////////////////////////////////////devisions///////////////////////////
Route::match(['get', 'post'],'masterresources/divisions', array('as'=>'export','uses'=>'DivisionController@index'));
Route::get('masterresources/divisions/add', 'DivisionController@add');
Route::post('masterresources/divisions/store', 'DivisionController@store');
Route::get('masterresources/divisions/Disable/{id}', 'DivisionController@Disable');
Route::get('masterresources/divisions/Enable/{id}', 'DivisionController@Enable');
Route::get('masterresources/divisions/delete/{id}', 'DivisionController@delete');
Route::get('masterresources/divisions/edit/{id}', 'DivisionController@edit');
Route::post('masterresources/divisions/update', 'DivisionController@update');
Route::post('masterresources/divisions/checkdevisionname', 'DivisionController@checkdivisionname');
Route::post('masterresources/divisions/exportdata', 'DivisionController@exportdata');

//BANK
Route::match(['get', 'post'],'masterresources/bank', array('as'=>'export','uses'=>'BankController@index'));
Route::get('masterresources/bank/add', 'BankController@add');
Route::post('masterresources/bank/store', 'BankController@store');
Route::get('masterresources/bank/Disable/{id}', 'BankController@Disable');
Route::get('masterresources/bank/Enable/{id}', 'BankController@Enable');
Route::get('masterresources/bank/delete/{id}', 'BankController@delete');
Route::get('masterresources/bank/edit/{id}', 'BankController@edit');
Route::post('masterresources/bank/update', 'BankController@update');
Route::post('masterresources/bank/checkbankname', 'BankController@checkbankname');
Route::post('masterresources/bank/exportdata', 'BankController@exportdata');

// ledger
Route::match(['get', 'post'],'masterresources/ledger', array('as'=>'export','uses'=>'LedgerController@index'));
Route::get('masterresources/ledger/add', 'LedgerController@add');
Route::post('masterresources/ledger/store', 'LedgerController@store');
Route::get('masterresources/ledger/edit/{edit_id}', 'LedgerController@edit');
Route::post('masterresources/ledger/update', 'LedgerController@update');
Route::get('masterresources/ledger/Disable/{id}', 'LedgerController@Disable');
Route::get('masterresources/ledger/Enable/{id}', 'LedgerController@Enable');
Route::get('masterresources/ledger/delete/{id}', 'LedgerController@delete');
Route::post('masterresources/ledger/checkledgername', 'LedgerController@checkledgername');
Route::post('masterresources/ledger/exportdata', 'LedgerController@exportdata');

// Excluded employee
Route::match(['get', 'post'],'masterresources/excepted', array('as'=>'export','uses'=>'ExcludeemployeeController@index'));
Route::get('masterresources/excepted/index', 'ExcludeemployeeController@index');
Route::post('masterresources/excepted/exclude_employees', 'ExcludeemployeeController@exclude_employees');
Route::post('masterresources/excepted/notexcludedids', 'ExcludeemployeeController@notexcludedids');
Route::post('masterresource/excepted/store', 'ExcludeemployeeController@store');
Route::post('masterresources/excepted/remove_employees', 'ExcludeemployeeController@remove_employees');
Route::post('masterresources/excepted/notexcludedlist', 'ExcludeemployeeController@notexcludedlist');


//Warning Type
Route::match(['get', 'post'],'masterresources/warning_type', array('as'=>'export','uses'=>'WarningtypeController@index'));
Route::get('masterresources/warning_type/add', 'WarningtypeController@add');
Route::post('masterresources/warning_type/store', 'WarningtypeController@store');
Route::get('masterresources/warning_type/edit/{edit_id}', 'WarningtypeController@edit');
Route::post('masterresources/warning_type/update', 'WarningtypeController@update');
Route::get('masterresources/warning_type/delete/{id}', 'WarningtypeController@delete');
Route::post('masterresources/warning_type/exportdata', 'WarningtypeController@exportdata');

//Meeting Room
Route::match(['get', 'post'],'masterresources/meeting_room', array('as'=>'export','uses'=>'MeetingroomController@index'));
Route::get('masterresources/meeting_room/add', 'MeetingroomController@add');
Route::post('masterresources/meeting_room/store', 'MeetingroomController@store');
Route::get('masterresources/meeting_room/edit/{edit_id}', 'MeetingroomController@edit');
Route::post('masterresources/meeting_room/update', 'MeetingroomController@update');
Route::get('masterresources/meeting_room/delete/{id}', 'MeetingroomController@delete');
Route::post('masterresources/meeting_room/exportdata', 'MeetingroomController@exportdata');

//Chart Category
Route::match(['get', 'post'],'masterresources/chartcategory', array('as'=>'export','uses'=>'ChartcategoryController@index'));
Route::get('masterresources/chartcategory/add', 'ChartcategoryController@add');
Route::post('masterresources/chartcategory/store', 'ChartcategoryController@store');
Route::get('masterresources/chartcategory/edit/{edit_id}', 'ChartcategoryController@edit');
Route::post('masterresources/chartcategory/update', 'ChartcategoryController@update');
Route::get('masterresources/chartcategory/delete/{id}', 'ChartcategoryController@delete');
Route::post('masterresources/chartcategory/exportdata', 'ChartcategoryController@exportdata');


//Cut off Date
Route::get('masterresources/cutoffdate', 'CutoffController@index');
Route::post('masterresources/cutoffdate/store', 'CutoffController@store');


// ledger_group
Route::match(['get', 'post'],'masterresources/ledger_group', array('as'=>'export','uses'=>'LedgergroupController@index'));
Route::get('masterresources/ledger_group/add', 'LedgergroupController@add');
Route::post('masterresources/ledger_group/store', 'LedgergroupController@store');
Route::get('masterresources/ledger_group/edit/{edit_id}', 'LedgergroupController@edit');
Route::post('masterresources/ledger_group/update', 'LedgergroupController@update');
Route::post('masterresources/ledger_group/gerparentgroups', 'LedgergroupController@gerparentgroups');
Route::get('masterresources/ledger_group/delete/{id}', 'LedgergroupController@delete');
Route::post('masterresources/ledger_group/checkledgername', 'LedgergroupController@checkledgername');
Route::post('masterresources/ledger_group/exportdata', 'LedgergroupController@exportdata');


// id professionals

Route::match(['get', 'post'],'masterresources/id_professionals', array('as'=>'export','uses'=>'IdprofessionalController@index'));
Route::get('masterresources/id_professionals/add', 'IdprofessionalController@add');
Route::post('masterresources/id_professionals/store', 'IdprofessionalController@store');
Route::post('masterresources/id_professionals/checkidprof', 'IdprofessionalController@checkidprof');
Route::post('masterresources/id_professionals/checkname', 'IdprofessionalController@checkname');
Route::get('masterresources/id_professionals/edit/{edit_id}', 'IdprofessionalController@edit');
Route::post('masterresources/id_professionals/update', 'IdprofessionalController@update');
Route::post('masterresources/id_professionals/exportdata', 'IdprofessionalController@exportdata');
Route::get('masterresources/id_professionals/Disable/{id}', 'IdprofessionalController@Disable');
Route::get('masterresources/id_professionals/Enable/{id}', 'IdprofessionalController@Enable');
Route::get('masterresources/id_professionals/delete/{id}', 'IdprofessionalController@delete');

//Report Settings
Route::match(['get', 'post'],'masterresources/report_settings', array('as'=>'export','uses'=>'ReportsettingsController@index'));
Route::get('masterresources/report_settings/add', 'ReportsettingsController@add');
Route::post('masterresources/report_settings/store', 'ReportsettingsController@store');
Route::get('masterresources/report_settings/edit/{edit_id}', 'ReportsettingsController@edit');
Route::post('masterresources/report_settings/update', 'ReportsettingsController@update');
Route::get('masterresources/report_settings/delete/{id}', 'ReportsettingsController@delete');
Route::post('masterresources/report_settings/autocompleteemployee', 'ReportsettingsController@autocompleteemployee');
Route::post('masterresources/report_settings/checksettingsexistornot', 'ReportsettingsController@checksettingsexistornot');

//Office
Route::match(['get', 'post'],'masterresources/office', array('as'=>'export','uses'=>'OfficeController@index'));
Route::get('masterresources/office/add', 'OfficeController@add');
Route::post('masterresources/office/store', 'OfficeController@store');
Route::get('masterresources/office/edit/{edit_id}', 'OfficeController@edit');
Route::post('masterresources/office/update', 'OfficeController@update');
Route::get('masterresources/office/Disable/{id}', 'OfficeController@Disable');
Route::get('masterresources/office/Enable/{id}', 'OfficeController@Enable');
Route::get('masterresources/office/delete/{id}', 'OfficeController@delete');
Route::post('masterresources/office/exportdata', 'OfficeController@exportdata');

//staff house routes
Route::match(['get', 'post'],'masterresources/staff_house', array('as'=>'export','uses'=>'StaffhouseController@index'));
Route::get('masterresources/staff_house/add', 'StaffhouseController@add');
Route::post('masterresources/staff_house/store', 'StaffhouseController@store');
Route::post('masterresources/staff_house/checkidprof', 'StaffhouseController@checkstaffhouse');
Route::post('masterresources/staff_house/checkname', 'StaffhouseController@checkname');
Route::get('masterresources/staff_house/edit/{edit_id}', 'StaffhouseController@edit');
Route::post('masterresources/staff_house/update', 'StaffhouseController@update');
Route::get('masterresources/staff_house/delete/{id}', 'StaffhouseController@delete');
Route::post('masterresources/staff_house/exportdata', 'StaffhouseController@exportdata');


///////////////////// Cost name ////////////////////
Route::match(['get', 'post'],'masterresources/costname', 'CostnameController@index');
Route::get('masterresources/costname/add', 'CostnameController@add');
Route::post('masterresources/costname/store', 'CostnameController@store');
Route::post('masterresources/costname/checknameunique', 'CostnameController@checknameunique');
Route::get('masterresources/costname/edit/{edit_id}', 'CostnameController@edit');
Route::post('masterresources/costname/update', 'CostnameController@update');
Route::get('masterresources/costname/delete/{id}', 'CostnameController@delete');


///////////////////// Ploicy master ////////////////////
Route::match(['get', 'post'],'masterresources/policy_master', 'PolicymasterController@index');
Route::get('masterresources/policy_master/add', 'PolicymasterController@add');
Route::post('masterresources/policy_master/store', 'PolicymasterController@store');
Route::post('masterresources/policy_master/checknameunique', 'PolicymasterController@checknameunique');
Route::get('masterresources/policy_master/edit/{edit_id}', 'PolicymasterController@edit');
Route::post('masterresources/policy_master/update', 'PolicymasterController@update');
Route::get('masterresources/policy_master/delete/{id}', 'PolicymasterController@delete');