<?php

namespace App\Http\Controllers\Admin;

use Analytics;
use App\Http\Controllers\Controller;
use App\Model\Property;
use App\Model\PropertyImage;
use App\Services\Common;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Kamaln7\Toastr\Facades\Toastr;
use Spatie\Analytics\Period;

class AdminPropertyController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin_auth');
        //write athenitcate function for admin
    }

    /**
     * Show the dashboard
     *
     * @param  null
     * @return View
     */
    public function get_list_property(Request $request)
    {
        $paginate = '10';
        $propertyList = DB::table('properties')
            ->select('properties.*', 'agents.name as agentname', 'municipalities.name as muncname', 'districts.name as districtname')
            ->leftjoin('agents', 'agents.id', '=', 'properties.agent_id')
            ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
            ->leftjoin('districts', 'districts.id', '=', 'properties.district_id')
            ->where('properties.status', '=', '1')
            ->paginate($paginate);

        $common = new Common();
        $agents = $common->agents();
        $districts = $common->districts();

        $pageData['propertyList'] = $propertyList;
        $pageData['agentList'] = $agents;
        $pageData['districts'] = $districts;

        if ($request->ajax()) {

            $agent = Input::get('agent');

            $district = Input::get('district');
            $searchKey = Input::get('searchKey');

            $propertyList = DB::table('properties')
                ->select('properties.*', 'agents.name as agentname', 'municipalities.name as muncname', 'districts.name as districtname')
                ->leftjoin('agents', 'agents.id', '=', 'properties.agent_id')
                ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                ->leftjoin('districts', 'districts.id', '=', 'properties.district_id')
                ->where('properties.status', '=', '1')
                ->when($agent, function ($query) use ($agent) {
                    if ($agent == "admin") {
                        return $query->whereRaw("(properties.agent_id IS NULL)");
                    } else {
                        return $query->whereRaw("(properties.agent_id='$agent')");
                    }

                })
                ->when($district, function ($query) use ($district) {
                    return $query->whereRaw("(properties.district_id=$district)");
                })
                ->when($searchKey, function ($query) use ($searchKey) {
                    return $query->whereRaw("(properties.name_en like '%$searchKey%' OR municipalities.name like '%$searchKey%' OR districts.name like '%$searchKey%')");
                })
                ->paginate($paginate);

            $pageData['propertyList'] = $propertyList;

            return view('admin.property.property-list-result', array('pageData' => $pageData));
        }

        return view('admin.property.property-listing', array('pageData' => $pageData));
    }
 
    public function add_property()
    {
        try {

            $buildings = DB::table('building_type')
                ->select('*')
                ->where('status', '=', '1')
                ->get();
            $neighborhoods = DB::table('neighbourhoods')
                ->select('id', 'name_en as name')
                ->where('status', '=', '1')
                ->get();

            $municipalities = DB::table('municipalities')
                ->select('*')
                ->where('status', '=', '1')
                ->get();

            $districts = DB::table('districts')
                ->select('*')
                ->where('status', '=', '1')
                ->get();
            $tested = [];
            $unique = false;
            do {
                // Generate random string of characters
                $random = strtoupper(chr(97 + mt_rand(0, 25))) . mt_rand(10000001, 99999999);
                // Check if it's already testing
                // If so, don't query the database again
                if (in_array($random, $tested)) {
                    continue;
                }
                // Check if it is unique in the database
                $count = DB::table('properties')->where('reference_number', '=', $random)->count();
                // Store the random character in the tested array
                // To keep track which ones are already tested
                $tested[] = $random;
                // String appears to be unique
                if ($count == 0) {
                    // Set unique to true to break the loop
                    $unique = true;
                }
                // If unique is still false at this point
                // it will just repeat all the steps until
                // it has generated a random string of characters
            } while (!$unique);
            $reference_number = $random;

            $pageData['buildings'] = $buildings;
            $pageData['neighborhoods'] = $neighborhoods;
            $pageData['municipalities'] = $municipalities;
            $pageData['reference_number'] = $reference_number;
            $pageData['district'] = $districts;

            return view('admin.property.add-property', array('pageData' => $pageData));

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function save_property(Request $request)
    {
        try {
            $input = Input::all();
            // print_r($input);
            // die();
            $objmodel = new Property();
            $objmodel->tenure_type = Input::get('propertyType');
            $objmodel->building_type = Input::get('buildingType');
            $objmodel->estimated_price = Input::get('estimated_price');
            $objmodel->reference_number = Input::get('reference_num');
            $objmodel->name_en = Input::get('name_en');
            $objmodel->name_ka = Input::get('name_ka');
            $objmodel->name_ru = Input::get('name_ru');
            $objmodel->address_line1_en = Input::get('address_line1_en');
            $objmodel->address_line1_ka = Input::get('address_line1_ka');
            $objmodel->address_line1_ru = Input::get('address_line1_ru');
            $objmodel->address_line2_en = Input::get('address_line2_en');
            $objmodel->address_line2_ka = Input::get('address_line2_ka');
            $objmodel->address_line2_ru = Input::get('address_line2_ru');
            $objmodel->zip_en = Input::get('zip_en');
            $objmodel->zip_ka = Input::get('zip_ka');
            $objmodel->zip_ru = Input::get('zip_ru');
            $objmodel->neighborhood = Input::get('neighborhood');
            
             if( $request->has('new_property') ){
                $objmodel->new_property ='1';  
             }else{
                 $objmodel->new_property ='0';  
             }

            $objmodel->description_en = Input::get('description_en');
            $objmodel->description_ka = Input::get('description_ka');
            $objmodel->description_ru = Input::get('description_ru');

            $objmodel->total_area = Input::get('total_area');
            $objmodel->habitable_area = Input::get('habitable_area');
            $objmodel->no_of_baths = Input::get('no_of_baths');
            $objmodel->no_of_garages = Input::get('no_of_garages');
            $objmodel->no_of_floors = Input::get('no_of_floors');
            $objmodel->no_of_beds = Input::get('no_of_beds');
            $objmodel->no_of_balcony = Input::get('no_of_balcony');
            $objmodel->terrace = Input::get('terrace');
            $objmodel->underground_parking = Input::get('parking');
            $objmodel->construction_year = date('Y-m-d', strtotime(Input::get('construction_year')));
            $objmodel->availability = date('Y-m-d', strtotime(Input::get('availability')));
            $objmodel->garden = Input::get('gardens');
            $objmodel->municipality_id = Input::get('municipality_en');
            $objmodel->reference_number = Input::get('reference_num');
            $objmodel->district_id = Input::get('district_en');
            $objmodel->status = '1';
            
            $objmodel->planning_permits = Input::get('planning_permits');
            $objmodel->subpoenas = Input::get('subpoenas');
            $objmodel->judicial_sayings = Input::get('judicial_sayings');
            $objmodel->pre_emption_right = Input::get('pre_emption_right');
            $objmodel->subdivision_permit = Input::get('subdivision_permit');
            $objmodel->flood_area = Input::get('flood_area');
           
            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');

            if ($latitude == "" && $longitude == "") {
                $muncId = Input::get('municipality_en');
                $districtId = Input::get('district_en');
                $municipalityId = DB::table('municipalities')
                    ->select('municipalities.name')
                    ->whereRaw("municipalities.id=$muncId")
                    ->first();
                $districtId = DB::table('districts')
                    ->select('districts.name')
                    ->whereRaw("districts.id=$districtId")
                    ->first();
                $city = "";
                $district = "";
                if ($municipalityId != "") {
                    $city = $municipalityId->name;
                }
                if ($districtId != "") {
                    $district = $districtId->name;
                }

                $adrLine1 = Input::get('address_line1_en');
                $adrLine2 = Input::get('address_line2_en');
                $zip = Input::get('zip_en');
                $address = $adrLine1 . ',' . $adrLine2 . ',' . $zip . ',' . $city . ',' . $district;

                $prepAddr = str_replace(' ', '+', $address);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE&address=' . $prepAddr . '&sensor=false');
                $output = json_decode($geocode);

                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;

            }

            $objmodel->latitude = $latitude;
            $objmodel->longitude = $longitude;

            if ($request->hasFile('property_plan')) {
                $file = $request->file('property_plan');
                $name = $file->getClientOriginalName();
                $propertyplan = "property_plan_" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/property-plan';
                $file->move($path, $propertyplan);
                $objmodel->property_plan = $propertyplan;
            }
            if (Session::get('adminLogin') != 'true') {
                $objmodel->agent_id = null;
            }

            $objmodel->save();
            $property_id = $objmodel->id;

            $galleryCount = Input::get('galleryCount');
            for ($i = 0; $i <= $galleryCount; $i++) {

                if ($request->file('gallery_upload_' . $i)) {

                    $image = $request->file('gallery_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "gallery_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/property-gallery';
                    $image->move($path, $filename);

                    $property = new PropertyImage();
                    $property->property_id = $property_id;
                    $property->image = $filename;
                    $property->save();

                }
            }

            if ($request->hasFile('preview_upload')) {
                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $gallery = "gallery_" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/property-gallery';
                $file->move($path, $gallery);

                $mainImg = new PropertyImage();
                $mainImg->property_id = $property_id;
                $mainImg->image = $gallery;
                $mainImg->main_image = '1';
                $mainImg->save();
            }

            Toastr::success('Successfully Added New Property!', $title = null, $options = []);
            return Redirect::to('admin/property-listing');

        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/property-listing');
        }
    }

    public function view_property($p_id)
    {

        try {
            /* initialise
             *
             */
            //$slug is the name of property just showing in url
            $common = new Common();
            $analytics_base_url = env("ANALYTICS_BASE_URL");
            $render_params = array();
            $render_params['property'] = "";
            $render_params['gallery'] = "";

            if ($p_id != "") {

                $encrypted_id = $p_id;
                $p_id = \Crypt::decrypt($p_id);

                $render_params = array();

                $property_list = Property::select('properties.*', 'municipalities.name as munc_name', 'property_images.image as mainimage', 'neighbourhoods.name_en as neighborname', 'neighbourhoods.description_en as neighbordesc', 'neighbourhoods.image as neighborImage', 'building_type.name as buildingType', 'districts.name as district')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('building_type', 'building_type.id', '=', 'properties.building_type')
                    ->leftjoin('districts', 'districts.id', '=', 'properties.district_id')
                //->leftjoin('property_images', 'property_images.property_id', '=', 'properties.id')
                    ->leftjoin('property_images', function ($join) {
                        $join->on('property_images.property_id', '=', 'properties.id');
                        $join->where('property_images.main_image', '=', '1');
                        $join->where('property_images.status', '=', '1');
                    })
                    ->leftjoin('neighbourhoods', 'neighbourhoods.id', '=', 'properties.neighborhood')
                    ->whereRaw("properties.id=$p_id")
                //->whereRaw("property_images.main_image='1'")
                    ->first();

                $slug = substr(strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $property_list->name_en)), 0, 25);
                $reference_number = $property_list->reference_number;
                $analyticsData = Analytics::performQuery(
                    Period::years(1),
                    'ga:sessions',
                    [
                        'metrics' => 'ga:sessions, ga:pageviews,ga:sessionDuration,ga:uniquePageviews,ga:avgSessionDuration',
                        
                        'filters' => "ga:pagePath==/property_view/$slug/$reference_number",
                    ]
                );
                $neighborId = "";
                if ($property_list != "") {
                    $neighborId = $property_list->neighborhood;
                }

                $gallery = DB::table('property_images')
                    ->select('property_images.*')
                    ->whereRaw("property_id=$p_id")
                    ->where('property_images.status', '=', '1')
                    ->get();

                
                $contact_form= DB::table('contact_form')
                    ->select('contact_form.*')
                    ->whereRaw("property_id=$p_id")
                    ->get();
                

                $render_params['property'] = $property_list;
                $render_params['gallery'] = $gallery;
                $render_params['property_view'] = $analyticsData->totalsForAllResults['ga:pageviews'];
                $render_params['property_interest']=count($contact_form);
                $render_params['interest_users']=$contact_form;
            }
            return view('admin.property.property-detail-view', array('render_params' => $render_params));

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    public function edit_property($p_id)
    {
        try {
            /* initialise
             *
             */
            //$slug is the name of property just showing in url
            $common = new Common();

            $pageData = array();
            $pageData['municipalities'] = "";
            $pageData['district'] = "";
            $pageData['property'] = "";
            $pageData['gallery'] = "";
            $pageData['buildings'] = "";
            $pageData['neighborhoods'] = "";

            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);

                $render_params = array();

                $property_list = Property::select('properties.*', 'municipalities.name as munc_name', 'property_images.image as mainimage', 'neighbourhoods.name_en as neighborname', 'neighbourhoods.description_en as neighbordesc', 'neighbourhoods.image as neighborImage', 'building_type.name as buildingType', 'districts.name as district')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('building_type', 'building_type.id', '=', 'properties.building_type')
                    ->leftjoin('districts', 'districts.id', '=', 'properties.district_id')
                //->leftjoin('property_images', 'property_images.property_id', '=', 'properties.id')
                    ->leftjoin('property_images', function ($join) {
                        $join->on('property_images.property_id', '=', 'properties.id');
                        $join->where('property_images.main_image', '=', '1');
                        $join->where('property_images.status', '=', '1');
                    })
                    ->leftjoin('neighbourhoods', 'neighbourhoods.id', '=', 'properties.neighborhood')
                    ->whereRaw("properties.id=$p_id")
                //->whereRaw("property_images.main_image='1'")
                    ->first();
                $neighborId = "";
                if ($property_list != "") {
                    $neighborId = $property_list->neighborhood;
                }

                $gallery = DB::table('property_images')
                    ->select('property_images.*')
                    ->whereRaw("property_id=$p_id")
                    ->whereRaw("main_image IS NULL ")
                    ->whereRaw("status='1' ")
                    ->get();

                $render_params = $common->getBasics();
                $pageData['buildings'] = $render_params['buildings'];
                $pageData['neighborhoods'] = $render_params['neighborhoods'];
                $pageData['municipalities'] = $render_params['municipalities'];
                $pageData['district'] = $render_params['district'];
                $pageData['property'] = $property_list;
                $pageData['gallery'] = $gallery;

            }
            return view('admin.property.edit-property', array('pageData' => $pageData));

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    public function update_property(Request $request)
    {
        try {

            $input = Input::all();

            $propertyid = \Crypt::decrypt($input['propertyId']);

            $objmodel = new Property();
            $objmodel->tenure_type = Input::get('propertyType');
            $objmodel->exists = true;
            $objmodel->id = $propertyid;
            $objmodel->building_type = Input::get('buildingType');
            $objmodel->estimated_price = Input::get('estimated_price');
            $objmodel->reference_number = Input::get('reference_num');
            $objmodel->name_en = Input::get('name_en');
            $objmodel->name_ka = Input::get('name_ka');
            $objmodel->name_ru = Input::get('name_ru');
            $objmodel->address_line1_en = Input::get('address_line1_en');
            $objmodel->address_line1_ka = Input::get('address_line1_ka');
            $objmodel->address_line1_ru = Input::get('address_line1_ru');
            $objmodel->address_line2_en = Input::get('address_line2_en');
            $objmodel->address_line2_ka = Input::get('address_line2_ka');
            $objmodel->address_line2_ru = Input::get('address_line2_ru');
            $objmodel->zip_en = Input::get('zip_en');
            $objmodel->zip_ka = Input::get('zip_ka');
            $objmodel->zip_ru = Input::get('zip_ru');
            $objmodel->neighborhood = Input::get('neighborhood');
            
            if( $request->has('new_property') ){
                $objmodel->new_property ='1';  
             }else{
                 $objmodel->new_property ='0';
             }


            $objmodel->description_en = Input::get('description_en');
            $objmodel->description_ka = Input::get('description_ka');
            $objmodel->description_ru = Input::get('description_ru');

            $objmodel->total_area = Input::get('total_area');
            $objmodel->habitable_area = Input::get('habitable_area');
            $objmodel->no_of_baths = Input::get('no_of_baths');
            $objmodel->no_of_garages = Input::get('no_of_garages');
            $objmodel->no_of_floors = Input::get('no_of_floors');
            $objmodel->no_of_beds = Input::get('no_of_beds');
            $objmodel->no_of_balcony = Input::get('no_of_balcony');
            $objmodel->terrace = Input::get('terrace');
            $objmodel->underground_parking = Input::get('parking');
            $objmodel->construction_year = date('Y-m-d', strtotime(Input::get('construction_year')));
            $objmodel->availability = date('Y-m-d', strtotime(Input::get('availability')));
            $objmodel->garden = Input::get('gardens');
            $objmodel->municipality_id = Input::get('municipality_en');
            $objmodel->reference_number = Input::get('reference_num');
            $objmodel->district_id = Input::get('district_en');
            
            $objmodel->planning_permits = Input::get('planning_permits');
            $objmodel->subpoenas = Input::get('subpoenas');
            $objmodel->judicial_sayings = Input::get('judicial_sayings');
            $objmodel->pre_emption_right = Input::get('pre_emption_right');
            $objmodel->subdivision_permit = Input::get('subdivision_permit');
            $objmodel->flood_area = Input::get('flood_area');
           

            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');

            if ($latitude == "" && $longitude == "") {
                $muncId = Input::get('municipality_en');
                $districtId = Input::get('district_en');
                $municipalityId = DB::table('municipalities')
                    ->select('municipalities.name')
                    ->whereRaw("municipalities.id=$muncId")
                    ->first();
                $districtId = DB::table('districts')
                    ->select('districts.name')
                    ->whereRaw("districts.id=$districtId")
                    ->first();
                $city = "";
                $district = "";
                if ($municipalityId != "") {
                    $city = $municipalityId->name;
                }
                if ($districtId != "") {
                    $district = $districtId->name;
                }

                $adrLine1 = Input::get('address_line1_en');
                $adrLine2 = Input::get('address_line2_en');
                $zip = Input::get('zip_en');
                $address = $adrLine1 . ',' . $adrLine2 . ',' . $zip . ',' . $city . ',' . $district;

                $prepAddr = str_replace(' ', '+', $address);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE&address=' . $prepAddr . '&sensor=false');
                $output = json_decode($geocode);

                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;

            }

            $objmodel->latitude = $latitude;
            $objmodel->longitude = $longitude;

            if ($request->hasFile('property_plan')) {
                $file = $request->file('property_plan');
                $name = $file->getClientOriginalName();
                $propertyplan = "property_plan_" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/property-plan';
                $file->move($path, $propertyplan);
                $objmodel->property_plan = $propertyplan;
            }
            if (Session::get('adminLogin') != 'true') {
                $objmodel->agent_id = null;
            }

            $objmodel->save();

            if ($request->hasFile('preview_upload')) {
                DB::table('property_images')
                    ->whereRaw("property_id=$propertyid AND status='1' AND main_image='1'")
                    ->update(['status' => '0']);

                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $gallery = "gallery_" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/property-gallery';
                $file->move($path, $gallery);

                $mainImg = new PropertyImage();
                $mainImg->property_id = $propertyid;
                $mainImg->image = $gallery;
                $mainImg->main_image = '1';
                $mainImg->save();
            }

            $galleryCount = Input::get('galleryCount');

            for ($i = 0; $i <= $galleryCount; $i++) {

                if ($request->file('gallery_upload_' . $i)) {

                    $image = $request->file('gallery_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "gallery_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/property-gallery';
                    $image->move($path, $filename);

                    $property = new PropertyImage();
                    $property->property_id = $propertyid;
                    $property->image = $filename;
                    $property->main_image = null;
                    $property->save();

                }
            }

            Toastr::success('Updated Property Details Successfully', $title = null, $options = []);
            return Redirect::to('admin/property-listing');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/property-listing');

        }

    }

    public function remove_property(Request $request, $propertyId)
    {
        try {
            $property_id = \Crypt::decrypt($propertyId);
            if ($property_id != "") {
                $objmodel = new Property();
                $objmodel->exists = true;
                $objmodel->id = $property_id;
                $objmodel->status = 0;
                $objmodel->save();
            }

            Toastr::success('Successfully Removed Property Details!', $title = null, $options = []);
            return Redirect::to('admin/property-listing');
        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/property-listing');
        }
    }

    public function remove_gallery(Request $request)
    {

        $imgId = Input::get('imgId');
        DB::table('property_images')
            ->whereRaw("id=$imgId")
            ->update(['status' => '0']);
        return 1;

    }

    public function get_lat_long($address)
    {

        $address = str_replace(" ", "+", $address);

        $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
        $json = json_decode($json);

        $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        return $lat . ',' . $long;
    }

    
    public function get_agent_property($id,Request $request)
    {
        
        $agentId=\Crypt::decrypt($id);
        $pageData['agentId'] = $agentId;
        
        $paginate = '10';
        $propertyList = DB::table('properties')
            ->select('properties.*', 'agents.name as agentname', 'municipalities.name as muncname', 'districts.name as districtname')
            ->leftjoin('agents', 'agents.id', '=', 'properties.agent_id')
            ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
            ->leftjoin('districts', 'districts.id', '=', 'properties.district_id')
            ->where('properties.status', '=', '1')
            ->where('properties.agent_id', '=',$agentId)
            ->paginate($paginate);

        $common = new Common();
        $agents = $common->agents();
        $districts = $common->districts();
if ($request->ajax()) {

            $agent = Input::get('agent');

            $district = Input::get('district');
            $searchKey = Input::get('searchKey');

            $propertyList = DB::table('properties')
                ->select('properties.*', 'agents.name as agentname', 'municipalities.name as muncname', 'districts.name as districtname')
                ->leftjoin('agents', 'agents.id', '=', 'properties.agent_id')
                ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                ->leftjoin('districts', 'districts.id', '=', 'properties.district_id')
                ->where('properties.status', '=', '1')
                ->when($agent, function ($query) use ($agent) {
                    if ($agent == "admin") {
                        return $query->whereRaw("(properties.agent_id IS NULL)");
                    } else {
                        return $query->whereRaw("(properties.agent_id='$agent')");
                    }

                })
                ->when($district, function ($query) use ($district) {
                    return $query->whereRaw("(properties.district_id=$district)");
                })
                ->when($searchKey, function ($query) use ($searchKey) {
                    return $query->whereRaw("(properties.name_en like '%$searchKey%' OR municipalities.name like '%$searchKey%' OR districts.name like '%$searchKey%')");
                })
                ->paginate($paginate);

            $pageData['propertyList'] = $propertyList;
            

            return view('admin.agent.agent-property-list-result', array('pageData' => $pageData));
        }
        $pageData['propertyList'] = $propertyList;
        $pageData['agentList'] = $agents;
        $pageData['districts'] = $districts;

        return view('admin.agent.agent-property-listing', array('pageData' => $pageData));
    }

   
}
