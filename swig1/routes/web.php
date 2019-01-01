<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::group(['middleware' => 'lang_auth'], function () {
    Route::get('/', 'Web\WebController@landing');
    Route::match(['get', 'post'], 'web/changeLang', 'Web\WebController@setLanguage');
    Route::get('properties/search', 'Web\WebPropertyController@searchList');
    Route::post('web/load_properties', 'Web\WebController@propertiesList');
    Route::post('properties/ajax_search', 'Web\WebPropertyController@ajax_searchList');
    Route::get('properties/ajax_city_filter/{city_text?}', 'Web\WebPropertyController@ajax_city_filter');
    Route::get('properties/ajax_city_filter/{city_text?}', 'Web\WebController@ajax_city_filter');
    Route::get('property_view/{slug}/{p_id}', 'Web\WebPropertyController@property_view');
    Route::get('web/newconstruction', 'Web\WebConstructionController@newconstruction');

    #architects
    Route::get('web/architects', 'Web\WebArchitectController@architects');
    Route::match(['get', 'post'], 'web/architect/architect-view/{id?}', 'Web\WebArchitectController@view_architects');

    #neighborhoods
    Route::get('web/neighborhoods', 'Web\WebNeighborhoodController@neighborhoods');
    Route::match(['get', 'post'], 'web/neighborhood/neighborhood-view/{id?}', 'Web\WebNeighborhoodController@view_neighborhood');

    Route::get('web/stayinformed', 'Web\WebController@informed');
    Route::get('web/estimate', 'Web\WebController@estimate');
    Route::get('web/contact', 'Web\WebController@contact');

    Route::post('web/savecontactform', 'Web\WebController@savecontactform');
    Route::post('web/savestayinformed', 'Web\WebController@savestayinformed');
    Route::post('web/saveestimate', 'Web\WebController@saveestimate');
    
  Route::match(['get', 'post'], 'project/construction-view/{url}', 'Web\WebConstructionController@viewConstruction');
  Route::match(['get', 'post'], 'project/construction-plan/{url}', 'Web\WebConstructionController@constructionPlan');
  Route::match(['get', 'post'], 'project/construction-surrouding/{url}', 'Web\WebConstructionController@constructionSurrounding');
  Route::match(['get', 'post'], 'project/construction-garden/{url}', 'Web\WebConstructionController@constructionGarden');
  Route::match(['get', 'post'], 'project/construction-perfect-comfort/{url}', 'Web\WebConstructionController@constructionComfort');
  Route::match(['get', 'post'], 'project/construction-contact/{url}', 'Web\WebConstructionController@constructionContact');
  Route::post('construction-contact/savecontactform', 'Web\WebConstructionController@saveProjectcontactform');
  Route::post('construction/floordetails', 'Web\WebConstructionController@floordetails');

});
