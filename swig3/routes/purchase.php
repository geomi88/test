<?php
//////////////////////////////////////warehouse/////////////////////////////
Route::get('purchase', 'IndexController@index'); 

/////////////////////////GRN routes////////////////////////////
Route::match(['get', 'post'],'purchase/grn', 'GrnController@index');
Route::post('purchase/grn/updatestock', 'GrnController@updatestock');
Route::get('purchase/grn/view/{id}', 'GrnController@view');

///////////////////////////// Pending PO ///////////////////////
Route::match(['get', 'post'],'purchase/pending_po', 'PendingpoController@index');
Route::get('purchase/pending_po/view/{id}', 'PendingpoController@view');
Route::post('purchase/pending_po/updatestock', 'PendingpoController@updatestock');

///////////// Update PO Status //////////////
Route::match(["get", "post"], "purchase/update_po_status", "UpdatepostatusController@index");
Route::get("purchase/update_po_status/view/{id}","UpdatepostatusController@view");
Route::post("purchase/update_po_status/update","UpdatepostatusController@update_status");

/////////////// PO view Status //////////////////

Route::match(["get","post"],"purchase/view_postatus","PostatusviewController@index");
Route::get("purchase/view_postatus/view/{id}","PostatusviewController@view");
Route::post("purchase/view_postatus/get_payment_details","PostatusviewController@pay_details");

/////////////////// Opening stock //////////////////////
Route::get("purchase/opening_stock/add","OpeningstockController@add");
Route::post('purchase/opening_stock/getinventoryunits', 'OpeningstockController@getinventoryunits');
Route::post('purchase/opening_stock/getprevopeningstock', 'OpeningstockController@getprevopeningstock');
Route::post('purchase/opening_stock/updateopeningstock', 'OpeningstockController@updateopeningstock');

/////////////////// Received GRN ///////////////////
Route::match(['get', 'post'],'purchase/received_grn', 'ReceivedgrnController@index');
Route::get('purchase/received_grn/view/{id}', 'ReceivedgrnController@view');

/////////////////// Physical stock //////////////////////
Route::get("purchase/physical_stock/add","PhysicalstockController@add");
Route::post('purchase/physical_stock/getbatches', 'PhysicalstockController@getbatches');
Route::post('purchase/physical_stock/updatephysicalstock', 'PhysicalstockController@updatephysicalstock');
Route::post('purchase/physical_stock/getinventoryunits', 'OpeningstockController@getinventoryunits');

?>