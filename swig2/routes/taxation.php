<?php
/*Route::get('hr', function() {
  return View::make('hr/index');
});*/
Route::get('taxation', 'IndexController@index');
Route::match(['get', 'post'],'taxation/tax', 'TaxationController@add');
Route::post('taxation/store', 'TaxationController@store');
