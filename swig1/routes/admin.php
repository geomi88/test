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
//Route::get('admin/login', 'Admin\LoginController@getLogin');
//Route::get('admin/dashboard', 'Admin\AdminController@get_dashboard');
//Route::get('admin/new-agent', 'Admin\AdminAgentController@get_add_agent');
//Route::get('admin/properties', 'Admin\AdminPropertyController@get_list_property');

//#property
Route::match(['get', 'post'], 'admin/property-listing', 'Admin\AdminPropertyController@get_list_property');
Route::match(['get', 'post'], 'admin/add-property', 'Admin\AdminPropertyController@add_property');
Route::match(['get', 'post'], 'admin/save-property', 'Admin\AdminPropertyController@save_property');
Route::match(['get', 'post'], 'admin/property-view/{id}', 'Admin\AdminPropertyController@view_property');
Route::match(['get', 'post'], 'admin/property-remove/{id}', 'Admin\AdminPropertyController@remove_property');
Route::match(['get', 'post'], 'admin/property-edit/{id}', 'Admin\AdminPropertyController@edit_property');
Route::match(['get', 'post'], 'admin/save-property-updates', 'Admin\AdminPropertyController@update_property');
Route::match(['get', 'post'], 'admin/image-remove', 'Admin\AdminPropertyController@remove_gallery');
//#agent
Route::match(['get', 'post'], 'admin/agent-listing', 'Admin\AdminAgentController@get_list_agents');
Route::match(['get', 'post'], 'admin/add-agent', 'Admin\AdminAgentController@add_agent');
Route::match(['get', 'post'], 'admin/save-agent', 'Admin\AdminAgentController@save_agent');
Route::match(['get', 'post'], 'admin/agent-view/{id}', 'Admin\AdminAgentController@view_agent');
Route::match(['get', 'post'], 'admin/agent-edit/{id}', 'Admin\AdminAgentController@edit_agent');
Route::match(['get', 'post'], 'admin/update-agent', 'Admin\AdminAgentController@update_agent');
Route::match(['get', 'post'], 'admin/agent-remove/{id}', 'Admin\AdminAgentController@remove_agent');

#architect
Route::match(['get', 'post'], 'admin/add-architect', 'Admin\ArchitectController@add');
Route::match(['get', 'post'], 'admin/save_architect', 'Admin\ArchitectController@save_architect');
Route::match(['get', 'post'], 'admin/architect-listing', 'Admin\ArchitectController@architect_listing');
Route::match(['get', 'post'], 'admin/architect-view/{id}', 'Admin\ArchitectController@view_architect');
Route::match(['get', 'post'], 'admin/architect-edit/{id}', 'Admin\ArchitectController@edit_architect');
Route::match(['get', 'post'], 'admin/save-architect-updates', 'Admin\ArchitectController@update_architect');
Route::match(['get', 'post'], 'admin/architect-image-remove', 'Admin\ArchitectController@remove_gallery');
Route::match(['get', 'post'], 'admin/architect-remove/{id}', 'Admin\ArchitectController@remove_architect');

#construction
Route::match(['get', 'post'], 'admin/construction-listing', 'Admin\ConstructionController@list_construction');
Route::match(['get', 'post'], 'admin/add-construction', 'Admin\ConstructionController@add_construction');
Route::match(['get', 'post'], 'admin/save-construction', 'Admin\ConstructionController@save_construction');
Route::match(['get', 'post'], 'admin/construction-view/{id}', 'Admin\ConstructionController@view_construction');
Route::match(['get', 'post'], 'admin/add-floor/{id}', 'Admin\ConstructionController@add_floor');
Route::match(['get', 'post'], 'admin/add-gallery/{id}', 'Admin\ConstructionController@add_gallery');
Route::match(['get', 'post'], 'admin/save-gallery', 'Admin\ConstructionController@save_gallery');
Route::match(['get', 'post'], 'admin/save-floor', 'Admin\ConstructionController@save_floor');
Route::match(['get', 'post'], 'admin/construction-remove/{id}', 'Admin\ConstructionController@remove_construction');
Route::match(['get', 'post'], 'admin/construction/checkFloorExist', 'Admin\ConstructionController@checkFloorExist');
Route::match(['get', 'post'], 'admin/construction/checkFloorUpdateExist', 'Admin\ConstructionController@checkFloorUpdateExist');
Route::match(['get', 'post'], 'admin/construction-edit/{id}', 'Admin\ConstructionController@edit_construction');
Route::match(['get', 'post'], 'admin/update-construction', 'Admin\ConstructionController@update_construction');
Route::match(['get', 'post'], 'admin/list-floor/{id}', 'Admin\ConstructionController@list_floor');
Route::match(['get', 'post'], 'admin/floor-edit/{id}', 'Admin\ConstructionController@edit_floor');
Route::match(['get', 'post'], 'admin/update-floor', 'Admin\ConstructionController@update_floor');
Route::match(['get', 'post'], 'admin/floor-remove/{id}/{pid}', 'Admin\ConstructionController@remove_floor');
Route::match(['get', 'post'], 'admin/edit-gallery/{id}', 'Admin\ConstructionController@edit_gallery');

Route::match(['get', 'post'], 'admin/slider-remove', 'Admin\ConstructionController@remove_slider');
Route::match(['get', 'post'], 'admin/gallery-remove', 'Admin\ConstructionController@remove_gallery');
Route::match(['get', 'post'], 'admin/surround-remove', 'Admin\ConstructionController@remove_surround');
Route::match(['get', 'post'], 'admin/garden-remove', 'Admin\ConstructionController@remove_garden');

//#neighbourhood
Route::match(['get', 'post'], 'admin/neighbourhood-listing', 'Admin\NeighbourhoodController@get_list_neighbourhood');
Route::match(['get', 'post'], 'admin/add-neighbourhood', 'Admin\NeighbourhoodController@add_neighbourhood');
Route::match(['get', 'post'], 'admin/save-neighbourhood', 'Admin\NeighbourhoodController@save_neighbourhood');
Route::match(['get', 'post'], 'admin/neighbourhood-view/{id}', 'Admin\NeighbourhoodController@view_neighbourhood');
Route::match(['get', 'post'], 'admin/neighbourhood-remove/{id}', 'Admin\NeighbourhoodController@remove_neighbourhood');
Route::match(['get', 'post'], 'admin/neighbourhood-edit/{id}', 'Admin\NeighbourhoodController@edit_neighbourhood');
Route::match(['get', 'post'], 'admin/save-neighbourhood-updates', 'Admin\NeighbourhoodController@update_neighbourhood');
Route::match(['get', 'post'], 'admin/neighbourhoodImg-remove', 'Admin\NeighbourhoodController@remove_neighbourhoodGallery');

Route::match(['get', 'post'], 'admin/admin-logout', 'Agent\AuthController@logout');
Route::match(['get', 'post'], 'admin/profile-edit/{id}', 'Admin\AdminController@edit_profile');
Route::match(['get', 'post'], 'admin/update-account', 'Admin\AdminController@update_account');

Route::match(['get', 'post'], 'admin/agent-property-listing/{id}', 'Admin\AdminPropertyController@get_agent_property');
Route::match(['get', 'post'], 'admin/agent-property-listing', 'Admin\AdminPropertyController@get_ajax_property');
Route::match(['get', 'post'], 'agent/profile-edit/{id}', 'Agent\AgentController@edit_profile');
Route::match(['get', 'post'], 'agent/update-account', 'Agent\AgentController@update_account');
Route::match(['get', 'post'], 'agent/update-account', 'Agent\AgentController@update_account');

Route::match(['get', 'post'], 'admin/construction/checkProjectName', 'Admin\ConstructionController@checkNameExist');
Route::match(['get', 'post'], 'admin/construction/checkProjectUpdateExist', 'Admin\ConstructionController@checkNameUpdateExist');



//Estimates

Route::match(['get', 'post'], 'admin/estimates-listing', 'Admin\EstimatesController@estimates_listing');
Route::match(['get', 'post'], 'admin/estimate-detail-view/{id}', 'Admin\EstimatesController@view_estimate');
Route::match(['get', 'post'], 'admin/estimate-remove/{id}', 'Admin\EstimatesController@remove_estimate');


//Website Contents

Route::match(['get', 'post'], 'admin/website-contents', 'Admin\WebsitecontentsController@index');
Route::match(['get', 'post'], 'admin/get-content', 'Admin\WebsitecontentsController@getcontent');
Route::match(['get', 'post'], 'website/save-content', 'Admin\WebsitecontentsController@savecontent');


Route::match(['get', 'post'], 'owner/profile-edit/{id}', 'Owner\OwnerController@edit_profile');
Route::match(['get', 'post'], 'owner/update-account', 'Owner\OwnerController@update_account');
Route::match(['get', 'post'], 'owner/update-account', 'Owner\OwnerController@update_account');


Route::match(['get', 'post'], 'admin/agent/checkExist', 'Admin\AdminAgentController@checkExist');
