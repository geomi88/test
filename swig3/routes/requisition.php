<?php

Route::get('requisitions', 'IndexController@index');

/////////////////////////Requisition common routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/inbox', array('as' => 'export', 'uses' => 'CommonController@inbox'));
Route::match(['get', 'post'], 'requisitions/outbox', 'CommonController@outbox');
Route::post('requisitions/exportdatainbox', 'CommonController@exportdatainbox');
Route::post('requisitions/exportdataoutbox', 'CommonController@exportdataoutbox');
Route::post('requisitions/autocompleteemployees', 'CommonController@autocompleteemployees');
Route::post('requisitions/autocompleteinventory', 'CommonController@autocompleteinventory');
Route::post('requisitions/getrfqdata', 'CommonController@getrfqdata');
Route::post('requisitions/autocompletegeneralledgers', 'CommonController@autocompletegeneralledgers');
Route::post('requisitions/getsupplierdata', 'CommonController@getsupplierdata');
Route::post('requisitions/gettransactionhistory', 'CommonController@getTransactionHistory');
Route::post('requisitions/getsupplierdataQuarter', 'CommonController@getsupplierdataQuarter');
Route::post('requisitions/getinventorydataQuarter', 'PurchaserequisitionController@getinventorydataQuarter');
Route::post('requisitions/checkpendinpaymentexist', 'CommonController@checkpendinpaymentexist');

/////////////////////////Purchase requisition routes////////////////////////////
Route::get('requisitions/purchase_requisition/add', 'PurchaserequisitionController@add');
Route::post('requisitions/purchase_requisition/store', 'PurchaserequisitionController@store');
Route::get('requisitions/purchase_requisition/edit/{id}', 'PurchaserequisitionController@edit');
Route::get('requisitions/purchase_requisition/view/{id}', 'PurchaserequisitionController@view');
Route::get('requisitions/payment_advice/purchase_requisition/view/{id}', 'PurchaserequisitionController@view');
Route::get('requisitions/purchase_requisition/action_view/{id}', 'PurchaserequisitionController@action_view');
Route::post('requisitions/purchase_requisition/approve_requisition', 'PurchaserequisitionController@approve_requisition');
Route::post('requisitions/purchase_requisition/reject_requisition', 'PurchaserequisitionController@reject_requisition');
Route::post('requisitions/purchase_requisition/getinventorydata', 'PurchaserequisitionController@getinventorydata');
Route::post('requisitions/purchase_requisition/getinventorydataQuarter', 'PurchaserequisitionController@getinventorydataQuarter');


/////////////////////////General requisition routes////////////////////////////
Route::get('requisitions/general_requisition/add', 'GeneralrequisitionController@add');
Route::post('requisitions/general_requisition/store', 'GeneralrequisitionController@store');
Route::get('requisitions/general_requisition/view/{id}', 'GeneralrequisitionController@view');
Route::get('requisitions/general_requisition/action_view/{id}', 'GeneralrequisitionController@action_view');
Route::post('requisitions/general_requisition/approve_requisition', 'GeneralrequisitionController@approve_requisition');
Route::post('requisitions/general_requisition/reject_requisition', 'GeneralrequisitionController@reject_requisition');

/////////////////////////Maintanance requisition routes////////////////////////////
Route::get('requisitions/maintainance_requisition/add', 'MaintainancerequisitionController@add');
Route::post('requisitions/maintainance_requisition/store', 'MaintainancerequisitionController@store');
Route::get('requisitions/maintainance_requisition/view/{id}', 'MaintainancerequisitionController@view');
Route::get('requisitions/maintainance_requisition/action_view/{id}', 'MaintainancerequisitionController@action_view');
Route::post('requisitions/maintainance_requisition/approve_requisition', 'MaintainancerequisitionController@approve_requisition');
Route::post('requisitions/maintainance_requisition/reject_requisition', 'MaintainancerequisitionController@reject_requisition');
Route::post('requisitions/maintainance_requisition/get_cost_center', 'MaintainancerequisitionController@get_cost_center');

/////////////////////////Service requisition routes////////////////////////////
Route::get('requisitions/service_requisition/add', 'ServicerequisitionController@add');
Route::post('requisitions/service_requisition/store', 'ServicerequisitionController@store');
Route::get('requisitions/service_requisition/view/{id}', 'ServicerequisitionController@view');
Route::get('requisitions/payment_advice/service_request/view/{id}', 'ServicerequisitionController@view');
Route::get('requisitions/service_requisition/action_view/{id}', 'ServicerequisitionController@action_view');
Route::post('requisitions/service_requisition/approve_requisition', 'ServicerequisitionController@approve_requisition');
Route::post('requisitions/service_requisition/reject_requisition', 'ServicerequisitionController@reject_requisition');

/////////////////////////Requisition hiereachy routes////////////////////////////
Route::get('requisitions/requisition_hierarchy/add', 'RequisitionhierarchyController@add');
Route::post('requisitions/requisition_hierarchy/store', 'RequisitionhierarchyController@store');
Route::post('requisitions/requisition_hierarchy/gethierarchy', 'RequisitionhierarchyController@gethierarchy');

/////////////////////////Payment advice routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/payment_advice', 'PaymentadviceController@index');
Route::get('requisitions/payment_advice/add/{id}', 'PaymentadviceController@add');
Route::post('requisitions/payment_advice/store', 'PaymentadviceController@store');
Route::post('requisitions/payment_advice/exportdata', 'PaymentadviceController@exportdata');
Route::post('requisitions/exportdata_paymentadvicelist', 'PaymentadviceController@exportdata_paymentadvicelist');

/////////////////////////Requisition common routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/payment_approval/inbox', 'PaymentadviceController@inbox');
Route::get('requisitions/payment_approval/view/{id}', 'PaymentadviceController@view');
Route::post('requisitions/payment_approval/reject_payment', 'PaymentadviceController@reject_payment');
Route::post('requisitions/payment_approval/approve_payment', 'PaymentadviceController@approve_payment');
Route::match(['get', 'post'], 'requisitions/payment_advice/outbox', 'PaymentadviceController@outbox');
Route::get('requisitions/payment_advice/view/{id}', 'PaymentadviceController@listview');
Route::post('requisitions/payment_approval/exportdata_approval', 'PaymentadviceController@exportdata_approval');

/////////////////////////User wise payment routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/userwisepayments', 'PaymentadviceController@userwisepayments');
Route::post('requisitions/exportuserwispaymentsdata', 'PaymentadviceController@exportuserwispaymentsdata');
Route::get('requisitions/userwisepayments/view/{id}', 'PaymentadviceController@listview');

/////////////////////////advance payment requisition routes////////////////////////////
Route::get('requisitions/advancePayment_requisition/add', 'AdvancepaymentrequisitionController@add');
Route::post('requisitions/getgeneralledgerdata', 'CommonController@getgeneralledgerdata');
Route::post('requisitions/advancePayment_requisition/store', 'AdvancepaymentrequisitionController@store');
Route::get('requisitions/advance_payment_requisition/view/{id}', 'AdvancepaymentrequisitionController@view');
Route::get('requisitions/payment_advice/advance_requisition/view/{id}', 'AdvancepaymentrequisitionController@view');
Route::get('requisitions/advance_payment_requisition/action_view/{id}', 'AdvancepaymentrequisitionController@action_view');
Route::post('requisitions/advance_payment_requisition/approve_requisition', 'AdvancepaymentrequisitionController@approve_requisition');
Route::post('requisitions/advance_payment_requisition/reject_requisition', 'AdvancepaymentrequisitionController@reject_requisition');


/////////////////////////completed requisition routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/completed_requisition/list', 'CompletedrequisitionController@requisitionlist');
Route::post('requisitions/completed_requisition/exportdatainbox', 'CompletedrequisitionController@exportdata');
Route::get('requisitions/general_completed_requisition/view/{id}', 'GeneralrequisitionController@view');
Route::get('requisitions/completed_requisition/view/{id}', 'AdvancepaymentrequisitionController@view');
Route::get('requisitions/advance_completed_requisition/view/{id}', 'AdvancepaymentrequisitionController@view');
Route::get('requisitions/leave_completed_requisition/view/{id}', 'LeaverequisitionController@view');
Route::get('requisitions/maintainance_completed_requisition/view/{id}', 'MaintainancerequisitionController@view');
Route::get('requisitions/service_completed_requisition/view/{id}', 'ServicerequisitionController@view');
Route::get('requisitions/purchase_completed_requisition/view/{id}', 'PurchaserequisitionController@view');
Route::post('requisitions/purchase_completed_requisition/getinventorydataQuarter', 'PurchaserequisitionController@getinventorydataQuarter');

/////////////////////////User wise requisition routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/userwise_requisitions', 'CompletedrequisitionController@userwiserequisitions');
Route::post('requisitions/userwise_requisitions/exportuserwisereq', 'CompletedrequisitionController@exportuserwisereq');
Route::get('requisitions/general_userwise_requisition/view/{id}', 'GeneralrequisitionController@view');
Route::get('requisitions/advance_userwise_requisition/view/{id}', 'AdvancepaymentrequisitionController@view');
Route::get('requisitions/leave_userwise_requisition/view/{id}', 'LeaverequisitionController@view');
Route::get('requisitions/maintainance_userwise_requisition/view/{id}', 'MaintainancerequisitionController@view');
Route::get('requisitions/service_userwise_requisition/view/{id}', 'ServicerequisitionController@view');
Route::get('requisitions/purchase_userwise_requisition/view/{id}', 'PurchaserequisitionController@view');

/////////////////////////completed requisition routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/completed_paymentadvice/list', 'CompletedrequisitionController@paymentadvicelist');
Route::get('requisitions/payment_completed_advice/view/{id}', 'PaymentadviceController@listview');
Route::post('requisitions/completed_paymentadvice/exportdata_paymentadvicelist', 'CompletedrequisitionController@exportdata_paymentadvicelist');

/////////////////////////outstanding payment routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/outstanding_payments', 'OutstandingpaymentsController@index');
Route::post('requisitions/outstanding_payments/add', 'OutstandingpaymentsController@add');
Route::post('requisitions/outstanding_payments/store', 'OutstandingpaymentsController@store');


/////////////////////////leave requisition routes////////////////////////////
Route::get('requisitions/leave_requisition/add', 'LeaverequisitionController@add');
Route::match(['get', 'post'], 'requisitions/leave_requisition/store', 'LeaverequisitionController@store');
Route::get('requisitions/leave_requisition/view/{id}', 'LeaverequisitionController@view');
Route::get('requisitions/leave_requisition/action_view/{id}', 'LeaverequisitionController@action_view');
Route::post('requisitions/leave_requisition/approve_requisition', 'LeaverequisitionController@approve_requisition');
Route::post('requisitions/leave_requisition/reject_requisition', 'LeaverequisitionController@reject_requisition');

/////////////////////////purchase orders routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/purchase_orders', 'PurchaseorderController@index');
Route::get('requisitions/purchase_orders/view/{id}', 'PurchaseorderController@view');
Route::post('requisitions/purchase_orders/update', 'PurchaseorderController@update');
Route::post('requisitions/exportdata_purchaseorder', 'PurchaseorderController@exportdata_purchaseorder');

Route::match(['get', 'post'], 'requisitions/import_purchase_orders', 'PurchaseorderimportController@index');
Route::post('requisitions/exportdata_importpurchaseorder', 'PurchaseorderimportController@exportdata_purchaseorder');

/////////////////////////purchase orders routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/purchase_order_list', 'PurchaseorderlistController@index');
Route::get('requisitions/purchase_order_list/view/{id}', 'PurchaseorderlistController@view');
Route::post('requisitions/exportdata_orderlist', 'PurchaseorderlistController@exportdata');
Route::post('requisitions/purchase_order_list/mailto_supplier', 'PurchaseorderlistController@mailto_supplier');
Route::post('requisitions/purchase_order_list/download_pfd', 'PurchaseorderlistController@download_pfd');

/////////////////////////Maintanance requisition report routes////////////////////////////
Route::match(['get', 'post'], 'requisition/maintenance_requisition_report', 'MaintenancerequsitionReportController@view');
Route::post('requisition/maintenance_requisition_report/exportdata', 'MaintenancerequsitionReportController@exportdata');
Route::get('requisitions/maintenance_report_results/view/{id}', 'MaintainancerequisitionController@view');

///////////////////////// Leave report routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/leave_report', 'LeavereportController@index');
Route::post('requisitions/leave_report/exportdata', 'LeavereportController@exportdata');
Route::get('requisitions/leave_report/view/{id}', 'LeaverequisitionController@view');

/////////////////////////completed requisition for payment routes////////////////////////////
Route::match(['get', 'post'], 'requisitions/requisitionfor_payment/requisitionforpayment', 'CompletedrequisitionController@requisitionforpayment');
Route::post('requisitions/requisitionfor_payment/exportdata', 'CompletedrequisitionController@exportrequisitionforpaymentdata');

/////////////////////////Maintanance in pending routes////////////////////////////
Route::match(['get', 'post'], 'requisition/maintenance_in_pending', 'MaintenanceinpendingController@index');
Route::post('requisition/maintenance_in_pending/exportdata', 'MaintenanceinpendingController@exportdata');
Route::get('requisitions/maintenance_in_pending/view/{id}', 'MaintenanceinpendingController@view');
Route::post('requisitions/maintenance_in_pending/update', 'MaintenanceinpendingController@update');

/////////////////////////Maintenance Requisition Status routes////////////////////////////
Route::match(['get', 'post'], 'requisition/maintenance_status', 'MaintenancestatusController@index');
Route::post('requisition/maintenance_status/exportdata', 'MaintenancestatusController@exportdata');
Route::get('requisitions/maintenance_status/view/{id}', 'MaintenancestatusController@view');

/////////////////////////Import Purchase requisition routes////////////////////////////
Route::get('requisitions/import_purchase_requisition/add', 'ImportpurchaserequisitionController@add');
Route::post('requisitions/import_purchase_requisition/store', 'ImportpurchaserequisitionController@store');
Route::get('requisitions/import_purchase_requisition/edit/{id}', 'ImportpurchaserequisitionController@edit');
Route::get('requisitions/import_purchase_requisition/view/{id}', 'ImportpurchaserequisitionController@view');
Route::get('requisitions/payment_advice/import_purchase_requisition/view/{id}', 'ImportpurchaserequisitionController@view');
Route::get('requisitions/import_purchase_requisition/action_view/{id}', 'ImportpurchaserequisitionController@action_view');
Route::post('requisitions/import_purchase_requisition/approve_requisition', 'ImportpurchaserequisitionController@approve_requisition');
Route::post('requisitions/import_purchase_requisition/reject_requisition', 'ImportpurchaserequisitionController@reject_requisition');
Route::post('requisitions/import_purchase_requisition/getinventorydata', 'ImportpurchaserequisitionController@getinventorydata');
Route::post('requisitions/import_purchase_requisition/getinventorydataQuarter', 'ImportpurchaserequisitionController@getinventorydataQuarter');

/////////////////////////RFQ routes////////////////////////////
Route::get('requisitions/rfq/add', 'RfqController@add');
Route::post('requisitions/rfq/store', 'RfqController@store');
Route::post('requisitions/rfq/update', 'RfqController@update');
Route::post('requisitions/rfq/getinventorydata', 'RfqController@getinventorydata');
Route::post('requisitions/rfq/getinventorydataQuarter', 'RfqController@getinventorydataQuarter');
Route::match(['get', 'post'], 'requisitions/rfq', 'RfqController@index');
Route::match(['get', 'post'], 'requisitions/editrfq', 'RfqController@editindex');
Route::match(['get', 'post'], 'requisitions/approvedlist', 'RfqController@approvedlist');
Route::get('requisitions/rfq/view/{id}', 'RfqController@view');
Route::get('requisitions/rfq/edit/{id}', 'RfqController@edit');
Route::get('requisitions/rfq/delete/{id}', 'RfqController@delete');
Route::post('requisitions/rfq/mailto_supplier', 'RfqController@mailto_supplier');

/////////////////////////Drawing Requisition routes////////////////////////////
Route::get('requisitions/drawing_requsition/add', 'DrawingrequisitionController@add');
Route::post('requisitions/drawing_requsition/store', 'DrawingrequisitionController@store');
Route::get('requisitions/drawing_requsition/view/{id}', 'DrawingrequisitionController@view');
Route::get('requisitions/drawing_requsition/action_view/{id}', 'DrawingrequisitionController@action_view');
Route::post('requisitions/drawing_requsition/approve_requisition', 'DrawingrequisitionController@approve_requisition');
Route::post('requisitions/drawing_requsition/reject_requisition', 'DrawingrequisitionController@reject_requisition');


/////////////////////////Drawing requisition Payment Advice Routes////////////////////////////
Route::match(['get', 'post'],'requisitions/drawing_requsition_payment_advice/list', 'DrawingrequisitionController@paymentadvicelist');
Route::post('requisitions/drawing_requsition_payment_advice/exportdata_paymentadvicelist', 'DrawingrequisitionController@exportdata_paymentadvicelist');
Route::get('requisitions/drawing_requsition_payment_advice/view/{id}', 'DrawingrequisitionController@view');

/////////////////////////List Drawing requisition Routes////////////////////////////
Route::match(['get', 'post'],'requisitions/drawing_requsition/list', 'DrawingrequisitionController@drawingrequisitionlist');
Route::post('requisitions/drawing_requsition/exportdatainbox', 'DrawingrequisitionController@exportdata');


////////////////////////   Po action import   /////////////////////

Route::match(['get', 'post'], 'requisitions/po_action_import', 'PoactionimportController@index');
Route::post('requisitions/exportdata_importlist', 'PoactionimportController@exportdata');


////////////////////////   Purchase Order View   ////////////////

Route::post("requisitions/sendto_warehouse","PurchaseorderlistController@send_to_warehouse");

////////////////// Po action list /////////////

Route::match(["get","post"],"requisitions/po_action_list","PoactionlistController@index");

