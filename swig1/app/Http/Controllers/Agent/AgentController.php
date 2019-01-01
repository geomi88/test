<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
use App\Model\Property;
use App\Model\Agent;
use App\Model\PropertyImage;
use DB;
use App\Services\Common;

class AgentController extends Controller {

    public function __construct() {
        $this->middleware('admin_auth');
        //write athenitcate function for admin
    }

    /**
     * Show the dashboard
     *
     * @param  null
     * @return View
     */
    public function get_dashboard() {
        try{
        $agentId=Session::get('agentId');
        $widgetData=DB::table('properties')
                ->select(DB::raw(" 
                    SUM(CASE WHEN building_type='1' THEN 1 ELSE 0 END) AS houseCount
                   ,SUM(CASE WHEN building_type='2' THEN 1 ELSE 0 END) AS apartmentCount
                   ,SUM(CASE WHEN building_type='3' THEN 1 ELSE 0 END) AS commercialCount
                   ,SUM(CASE WHEN building_type='4' THEN 1 ELSE 0 END) AS officeCount
                   ,SUM(CASE WHEN building_type='5' THEN 1 ELSE 0 END) AS parkingCount"))
                ->whereRaw("properties.status='1' and properties.agent_id =$agentId")
                ->first();
        
        $graphData=DB::table('properties')
                ->select(DB::raw(" 
                    SUM(CASE WHEN building_type='1' THEN 1 ELSE 0 END) AS houseCount
                   ,SUM(CASE WHEN building_type='2' THEN 1 ELSE 0 END) AS apartmentCount
                   ,SUM(CASE WHEN building_type='3' THEN 1 ELSE 0 END) AS commercialCount
                   ,SUM(CASE WHEN building_type='4' THEN 1 ELSE 0 END) AS officeCount
                   ,SUM(CASE WHEN building_type='5' THEN 1 ELSE 0 END) AS parkingCount"))
                ->whereRaw("properties.status='1' and properties.agent_id =$agentId and properties.tenure_type ='1'")
                ->first();
        
        $agentDistricts=DB::table('properties')
               ->select("districts.name","districts.id")
               ->leftjoin("districts","properties.district_id","=","districts.id")
               ->whereRaw("properties.status='1' and agent_id=$agentId")
              // ->whereRaw("properties.tenure_type='1'")
               ->groupby('district_id')
               ->get();
       
        $agentCity=DB::table('properties')
               ->select("municipalities.name","municipalities.id")
               ->leftjoin("municipalities","properties.municipality_id","=","municipalities.id")
               ->whereRaw("properties.status='1' and agent_id=$agentId")
              // ->whereRaw("properties.tenure_type='1'")
               ->groupby('municipality_id')
               ->get();
       
       
        $property_list = Property::select('properties.*', 'municipalities.name as muncipality')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('property_images', function($join) {
                $join->on('property_images.property_id', '=', 'properties.id');
                $join->where('property_images.main_image', '=', '1');
 		$join->where('property_images.status', '=', '1');
                
            });
            $property_list = $property_list->where('properties.status', '=', '1');
            $property_list = $property_list->where('properties.agent_id', '=', $agentId);
            $property_list=$property_list->groupby('properties.id');
            $property_list = $property_list->limit(4)->get();
            
            
            

        
        $district_id =0;
        if(count($agentDistricts)>0){
        $district_id = $agentDistricts[0]->id;
        }
        $properties = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status='1' and district_id = $district_id and tenure_type ='1'")
            ->first();
        $total_properties = $properties->properties_count;
        $graph_pi = array();
        $full_pi_graph_data = array();
        if ($total_properties > 0) {
            

            $houses_percentage = round((($graphData->houseCount / $total_properties) * 100), 2);
            $row = array("name" => "Houses", "y" => $houses_percentage, "color" => 'green');
            array_push($graph_pi, $row);

            $apartments_percentage = round((($graphData->apartmentCount / $total_properties) * 100), 2);
            $row = array("name" => "Apartments", "y" => $apartments_percentage, "color" => 'orange');
            array_push($graph_pi, $row);

            $commercial_percentage = round((($graphData->commercialCount / $total_properties) * 100), 2);
            $row = array("name" => "Commercial", "y" => $commercial_percentage, "color" => 'blue');
            array_push($graph_pi, $row);

            $offices_percentage = round((($graphData->officeCount / $total_properties) * 100), 2);
            $row = array("name" => "Offices", "y" => $offices_percentage, "color" => 'pink');
            array_push($graph_pi, $row);

            $parking_percentage = round((($graphData->parkingCount/ $total_properties) * 100), 2);
            $row = array("name" => "Parking", "y" => $parking_percentage, "color" => 'violet');
            array_push($graph_pi, $row);

            $full_pi_graph_data = array_values($graph_pi);

        }
       $full_pi_graph_data = json_encode($full_pi_graph_data);
        $dataArray=array(
           'widgetData'=>$widgetData,
           'agentDistricts'=>$agentDistricts,
           'agentCity'=>$agentCity,
           'cityData'=>$graphData,
           'property_list'=>$property_list,
           'full_pi_graph_data' => $full_pi_graph_data
            
        );
   return view('agent.dashboard.dashboard',$dataArray);
       
    }catch(\Exception $ex){
      
        echo $ex->getMessage();echo $ex->getLine();
        die();
    }
    }
   public function view_property($p_id) {
        try {
            
            $common = new Common();
            $render_params = array();
            $render_params['property'] = "";
            $render_params['gallery'] = "";
            $agentId=Session::get('agentId');
            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);
             
            $property_list = Property::select('properties.*', 'municipalities.name as munc_name','property_images.image as mainimage','neighbourhoods.name_en as neighborname','neighbourhoods.description_en as neighbordesc','neighbourhoods.image as neighborImage','building_type.name as buildingType','districts.name as district')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('building_type', 'building_type.id', '=', 'properties.building_type')
                    ->leftjoin('districts', 'districts.id', '=', 'properties.district_id')
                    ->leftjoin('property_images', function($join) {
                         $join->on('property_images.property_id', '=', 'properties.id');
                         $join->where('property_images.main_image', '=', '1');
                         $join->where('property_images.status', '=', '1');
                     })
                    ->leftjoin('neighbourhoods', 'neighbourhoods.id', '=', 'properties.neighborhood')
                    ->whereRaw("properties.id=$p_id")
                    ->whereRaw("properties.agent_id=$agentId")
                    ->first();
            $neighborId="";
            if($property_list!=""){
                $neighborId=$property_list->neighborhood;
            }
           
            $gallery= DB::table('property_images')
                ->select('property_images.*')
                ->whereRaw("property_id=$p_id")
                ->where('property_images.status', '=', '1')
                ->get();
            
           
         
        $render_params['property']=$property_list;
        $render_params['gallery']=$gallery;
       
          
            }
            return view('agent.property.property-detail-view', $render_params);
   
        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }
        
        
      }
      
        
    public function add_property() {
        try{
            
            $buildings= DB::table('building_type')
                  ->select('*')
                  ->where('status', '=', '1')
                  ->get();
            $neighborhoods= DB::table('neighbourhoods')
                  ->select('id','name_en as name')
                  ->where('status', '=', '1')
                  ->get();
            
            $municipalities= DB::table('municipalities')
                  ->select('*')
                  ->where('status', '=', '1')
                  ->get();
            
            $districts= DB::table('districts')
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

            $pageData['buildings']=$buildings;
            $pageData['neighborhoods']=$neighborhoods;
            $pageData['municipalities']=$municipalities;
            $pageData['reference_number']=$reference_number;
            $pageData['district']=$districts;
            
        return view('agent.property.add-property',array('pageData'=>$pageData));
        
        }  catch (Exception $ex){
            echo $ex->getMessage();
        }
    }
    
    public function save_property(Request $request) {
        try{
           $input=Input::all();
          
            $objmodel = new Property();
            $objmodel->tenure_type= Input::get('propertyType');
            $objmodel->building_type= Input::get('buildingType');
            $objmodel->estimated_price= Input::get('estimated_price');
            $objmodel->reference_number= Input::get('reference_num');
            $objmodel->name_en= Input::get('name_en');
            $objmodel->name_ka= Input::get('name_ka');
            $objmodel->name_ru= Input::get('name_ru');
            $objmodel->address_line1_en= Input::get('address_line1_en');
            $objmodel->address_line1_ka= Input::get('address_line1_ka');
            $objmodel->address_line1_ru= Input::get('address_line1_ru');
            $objmodel->address_line2_en= Input::get('address_line2_en');
            $objmodel->address_line2_ka= Input::get('address_line2_ka');
            $objmodel->address_line2_ru= Input::get('address_line2_ru');
            $objmodel->zip_en= Input::get('zip_en');
            $objmodel->zip_ka= Input::get('zip_ka');
            $objmodel->zip_ru= Input::get('zip_ru');
          
            $objmodel->description_en= Input::get('description_en');
            $objmodel->description_ka= Input::get('description_ka');
            $objmodel->description_ru= Input::get('description_ru');
            
            $objmodel->total_area= Input::get('total_area');
            $objmodel->habitable_area= Input::get('habitable_area');
            $objmodel->no_of_baths= Input::get('no_of_baths');
            $objmodel->no_of_garages= Input::get('no_of_garages');
            $objmodel->no_of_floors= Input::get('no_of_floors');
            $objmodel->no_of_beds= Input::get('no_of_beds');
            $objmodel->no_of_balcony= Input::get('no_of_balcony');
            $objmodel->terrace= Input::get('terrace');
            $objmodel->underground_parking= Input::get('parking');
            $objmodel->construction_year= date('Y-m-d',strtotime(Input::get('construction_year')));
            $objmodel->availability= date('Y-m-d',strtotime(Input::get('availability')));
            $objmodel->garden= Input::get('gardens');
            $objmodel->municipality_id= Input::get('municipality_en');
            $objmodel->reference_number= Input::get('reference_num');
            $objmodel->district_id= Input::get('district_en');
            $objmodel->status= '1';
            
            $objmodel->planning_permits = Input::get('planning_permits');
            $objmodel->subpoenas = Input::get('subpoenas');
            $objmodel->judicial_sayings = Input::get('judicial_sayings');
            $objmodel->pre_emption_right = Input::get('pre_emption_right');
            $objmodel->subdivision_permit = Input::get('subdivision_permit');
            $objmodel->flood_area = Input::get('flood_area');
            
            
            $latitude=Input::get('latitude'); 
            $longitude=Input::get('longitude');
            
            if($latitude=="" && $longitude==""){
                $muncId=Input::get('municipality_en');
                $districtId=Input::get('district_en');
                $municipalityId= DB::table('municipalities')
                    ->select('municipalities.name')
                    ->whereRaw("municipalities.id=$muncId")
                    ->first();
                $districtId= DB::table('districts')
                    ->select('districts.name')
                    ->whereRaw("districts.id=$districtId")
                    ->first();
                $city="";
                $district="";
                if($municipalityId!=""){
                    $city=$municipalityId->name;
                } 
                if($districtId!=""){
                    $district=$districtId->name;
                }

                $adrLine1=Input::get('address_line1_en');
                $adrLine2=Input::get('address_line2_en');
                $zip=Input::get('zip_en');
                $address = $adrLine1.','.$adrLine2.','.$zip.','.$city.','.$district;

                $prepAddr = str_replace(' ','+',$address);
                $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE&address='.$prepAddr.'&sensor=false');
                $output= json_decode($geocode);
                
                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;
                
             }
             
            $objmodel->latitude=$latitude;
            $objmodel->longitude=$longitude;
           
        
            if ($request->hasFile('property_plan')) {
                $file = $request->file('property_plan');
                $name = $file->getClientOriginalName();
                $propertyplan = "property_plan_" .strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/property-plan';
                $file->move($path, $propertyplan);
                $objmodel->property_plan= $propertyplan;
            }
                $objmodel->agent_id= Session::get('agentId');
            
            
            $objmodel->save();
            $property_id=$objmodel->id;
          
            
              
            $galleryCount = Input::get('galleryCount');
            for ($i = 0; $i <= $galleryCount; $i++) {

                if ($request->file('gallery_upload_'.$i)) {
                        
                        $image=$request->file('gallery_upload_'.$i);
                        $name = $image->getClientOriginalName();
                        $filename = "gallery_".$i.'_' . uniqid() . '.' . $image->getClientOriginalExtension();
                        $path = public_path() . '/uploads/property-gallery';
                        $image->move($path, $filename);

                        $property = New PropertyImage();
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
                
                $mainImg = New PropertyImage();
                $mainImg->property_id = $property_id;
                $mainImg->image = $gallery;
                $mainImg->main_image = '1';
                $mainImg->save();
            }
            
            Toastr::success( 'Successfully Added New Property!', $title = null, $options = []);
            return Redirect::to('agent/add-property');
      
        }  catch (Exception $ex){
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('agent/add-property');
        }
    }
    
    
      public function edit_property($p_id) {
        try {
            /* initialise 
             * 
             */
            //$slug is the name of property just showing in url
            $common = new Common();
            
            $pageData = array();
            $pageData['municipalities']="";
            $pageData['district']="";
            $pageData['property']="";
            $pageData['gallery']="";
            $pageData['buildings']="";
            $pageData['neighborhoods']="";
            
            
            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);
                
                
            $render_params = array();
            
            
            $property_list = Property::select('properties.*', 'municipalities.name as munc_name','property_images.image as mainimage','neighbourhoods.name_en as neighborname','neighbourhoods.description_en as neighbordesc','neighbourhoods.image as neighborImage','building_type.name as buildingType','districts.name as district')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('building_type', 'building_type.id', '=', 'properties.building_type')
                    ->leftjoin('districts', 'districts.id', '=', 'properties.district_id')
                    //->leftjoin('property_images', 'property_images.property_id', '=', 'properties.id')
                    ->leftjoin('property_images', function($join) {
                         $join->on('property_images.property_id', '=', 'properties.id');
                         $join->where('property_images.main_image', '=', '1'); 
                         $join->where('property_images.status', '=', '1');
                     })
                    ->leftjoin('neighbourhoods', 'neighbourhoods.id', '=', 'properties.neighborhood')
                    ->whereRaw("properties.id=$p_id")
                    //->whereRaw("property_images.main_image='1'")
                    ->first();
            $neighborId="";
            if($property_list!=""){
                $neighborId=$property_list->neighborhood;
            }
           
            $gallery= DB::table('property_images')
                ->select('property_images.*')
                ->whereRaw("property_id=$p_id")
                ->whereRaw("main_image IS NULL ")
                ->whereRaw("status='1' ")
                ->get();
            
           
            
         
            $render_params=$common->getBasics();
            $pageData['buildings']=$render_params['buildings'];
            $pageData['neighborhoods']=$render_params['neighborhoods'];
            $pageData['municipalities']=$render_params['municipalities'];
            $pageData['district']=$render_params['district'];
            $pageData['property']=$property_list;
            $pageData['gallery']=$gallery;
       
          
            }
            return view('agent.property.edit-property', array('pageData'=>$pageData));
   
        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }
        
        
      }
      
      
         public function update_property(Request $request) {
        try {
           
            $input=Input::all();
        
            $propertyid= \Crypt::decrypt($input['propertyId']);
          
            $objmodel = new Property();
            $objmodel->tenure_type= Input::get('propertyType');
            $objmodel->exists = true;
            $objmodel->id = $propertyid;
            $objmodel->building_type= Input::get('buildingType');
            $objmodel->estimated_price= Input::get('estimated_price');
            $objmodel->reference_number= Input::get('reference_num');
            $objmodel->name_en= Input::get('name_en');
            $objmodel->name_ka= Input::get('name_ka');
            $objmodel->name_ru= Input::get('name_ru');
            $objmodel->address_line1_en= Input::get('address_line1_en');
            $objmodel->address_line1_ka= Input::get('address_line1_ka');
            $objmodel->address_line1_ru= Input::get('address_line1_ru');
            $objmodel->address_line2_en= Input::get('address_line2_en');
            $objmodel->address_line2_ka= Input::get('address_line2_ka');
            $objmodel->address_line2_ru= Input::get('address_line2_ru');
            $objmodel->zip_en= Input::get('zip_en');
            $objmodel->zip_ka= Input::get('zip_ka');
            $objmodel->zip_ru= Input::get('zip_ru');
            
            $objmodel->description_en= Input::get('description_en');
            $objmodel->description_ka= Input::get('description_ka');
            $objmodel->description_ru= Input::get('description_ru');
            
            $objmodel->total_area= Input::get('total_area');
            $objmodel->habitable_area= Input::get('habitable_area');
            $objmodel->no_of_baths= Input::get('no_of_baths');
            $objmodel->no_of_garages= Input::get('no_of_garages');
            $objmodel->no_of_floors= Input::get('no_of_floors');
            $objmodel->no_of_beds= Input::get('no_of_beds');
            $objmodel->no_of_balcony= Input::get('no_of_balcony');
            $objmodel->terrace= Input::get('terrace');
            $objmodel->underground_parking= Input::get('parking');
            $objmodel->construction_year= date('Y-m-d',strtotime(Input::get('construction_year')));
            $objmodel->availability= date('Y-m-d',strtotime(Input::get('availability')));
            $objmodel->garden= Input::get('gardens');
            $objmodel->municipality_id= Input::get('municipality_en');
            $objmodel->reference_number= Input::get('reference_num');
            $objmodel->district_id= Input::get('district_en');
            
            $objmodel->planning_permits = Input::get('planning_permits');
            $objmodel->subpoenas = Input::get('subpoenas');
            $objmodel->judicial_sayings = Input::get('judicial_sayings');
            $objmodel->pre_emption_right = Input::get('pre_emption_right');
            $objmodel->subdivision_permit = Input::get('subdivision_permit');
            $objmodel->flood_area = Input::get('flood_area');
           
            
            $latitude=Input::get('latitude'); 
            $longitude=Input::get('longitude');
            
            if($latitude=="" && $longitude==""){
                $muncId=Input::get('municipality_en');
                $districtId=Input::get('district_en');
                $municipalityId= DB::table('municipalities')
                    ->select('municipalities.name')
                    ->whereRaw("municipalities.id=$muncId")
                    ->first();
                $districtId= DB::table('districts')
                    ->select('districts.name')
                    ->whereRaw("districts.id=$districtId")
                    ->first();
                $city="";
                $district="";
                if($municipalityId!=""){
                    $city=$municipalityId->name;
                } 
                if($districtId!=""){
                    $district=$districtId->name;
                }

                $adrLine1=Input::get('address_line1_en');
                $adrLine2=Input::get('address_line2_en');
                $zip=Input::get('zip_en');
                $address = $adrLine1.','.$adrLine2.','.$zip.','.$city.','.$district;

                $prepAddr = str_replace(' ','+',$address);
                $geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE&address='.$prepAddr.'&sensor=false');
                $output= json_decode($geocode);
                
                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;
                
             }
             
            $objmodel->latitude=$latitude;
            $objmodel->longitude=$longitude;
            
            
            if ($request->hasFile('property_plan')) {
                $file = $request->file('property_plan');
                $name = $file->getClientOriginalName();
                $propertyplan = "property_plan_" .strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/property-plan';
                $file->move($path, $propertyplan);
                $objmodel->property_plan= $propertyplan;
            }
            if(Session::get('agentLogin')=='true'){
                 $objmodel->agent_id=Session::get('agentId');
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
                
                $mainImg = New PropertyImage();
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
                        
                       
                        $property = New PropertyImage();
                        $property->property_id = $propertyid;
                        $property->image = $filename;
                        $property->main_image = NULL;
                        $property->save();
                        
                    }
                }
        
           Toastr::success('Updated Property Details Successfully', $title = null, $options = []);
            return Redirect::to('agent/property-listing');
        }  catch (\Exception $e) {
            echo $e->getMessage();
           // Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
         //return Redirect::to('agent/property-listing');
            
        }
     
}

public function remove_gallery(Request $request){
    
            $imgId=Input::get('imgId');
             DB::table('property_images')
                     ->whereRaw("id=$imgId")
                     ->update(['status' => '0']);
             return 1;
           
}

 public function get_list_property(Request $request){
        $paginate='10';
        $agentId=Session::get('agentId');
     
        $buildingType=DB::table('building_type')
                ->select('building_type.*')
                ->where('building_type.status', '=', '1')
                ->get();
            
        $municipality=Property::select('municipalities.id as id','municipalities.name as name')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->where('properties.agent_id', '=', $agentId)
                    ->groupby('municipality_id')
                    ->get();
                    
        $property_list = Property::select('properties.*', 'municipalities.name as muncipality')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('property_images', function($join) {
                        $join->on('property_images.property_id', '=', 'properties.id');
                        $join->where('property_images.main_image', '=', '1');
                        $join->where('property_images.status', '=', '1');
                     })
                    ->where('properties.agent_id', '=', $agentId)
                    ->where('properties.status', '=', '1')
                    ->paginate($paginate);
    
        if($request->ajax()) { 
            $building=Input::get('building');
            $city=Input::get('city');
            $bedroom=Input::get('bedroom');
            $bathroom=Input::get('bathroom');
            $garden=Input::get('garden');
            $terrace=Input::get('terrace');
            
            $property_list = Property::select('properties.*', 'municipalities.name as muncipality')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('property_images', function($join) {
                        $join->on('property_images.property_id', '=', 'properties.id');
                        $join->where('property_images.main_image', '=', '1');
                        $join->where('property_images.status', '=', '1');
                    })
                    ->where('properties.agent_id', '=', $agentId)
                    ->where('properties.status', '=', '1')
                    ->when($building, function ($query) use ($building) {
                        return $query->whereRaw("building_type = $building");
                    })
                    ->when($city != NULL, function ($query) use ($city) {
                        return $query->whereRaw("municipality_id = $city");
                    })
                    ->when($bedroom != NULL, function ($query) use ($bedroom) {
                        return $query->whereRaw("no_of_beds = $bedroom");
                    })
                    ->when($bathroom != NULL, function ($query) use ($bathroom) {
                        return $query->whereRaw("no_of_baths = $bathroom");
                    })
                    ->when($garden != NULL, function ($query) use ($garden) {
                        return $query->whereRaw("garden = $garden");
                    })
                    ->when($terrace != NULL, function ($query) use ($terrace) {
                        return $query->whereRaw("terrace = $terrace");
                    })
                    ->paginate($paginate);

            return view('agent.property.property-list-result', array('property_list'=>$property_list));
        }   
        
         $dataArray=array(
             'buildingType'=>$buildingType,
             'municipality'=>$municipality,
             'property_list'=>$property_list
         );
            return view('agent.property.property-listing',$dataArray );
}

    
    public function getDistrictData()
    {

        $tenure_type = Input::get('tenure_type');
        $district_id = Input::get('district_id');
        $agentId=Session::get('agentId');
        $properties = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status='1'")
            ->when($tenure_type, function ($query) use ($tenure_type) {
                        return $query->whereRaw("tenure_type = $tenure_type");
                 })
            ->when($district_id != NULL, function ($query) use ($district_id) {
                        return $query->whereRaw("district_id = $district_id");
                 })
            ->first();
        $total_properties = $properties->properties_count;
        
        $graph_pi = array();
        $full_pi_graph_data = array();
        if ($total_properties > 0) {
            
            $widgetData=DB::table('properties')
                ->select(DB::raw(" 
                    SUM(CASE WHEN building_type='1' THEN 1 ELSE 0 END) AS houseCount
                   ,SUM(CASE WHEN building_type='2' THEN 1 ELSE 0 END) AS apartmentCount
                   ,SUM(CASE WHEN building_type='3' THEN 1 ELSE 0 END) AS commercialCount
                   ,SUM(CASE WHEN building_type='4' THEN 1 ELSE 0 END) AS officeCount
                   ,SUM(CASE WHEN building_type='5' THEN 1 ELSE 0 END) AS parkingCount"))
                ->whereRaw("properties.status='1' and properties.agent_id =$agentId")
                ->when($tenure_type, function ($query) use ($tenure_type) {
                        return $query->whereRaw("tenure_type = $tenure_type");
                 })
                 ->when($district_id != NULL, function ($query) use ($district_id) {
                        return $query->whereRaw("district_id = $district_id");
                 })
                 ->first();
            
            $houses_percentage = round((($widgetData->houseCount / $total_properties) * 100), 2);
            $row = array("name" => "Houses", "y" => $houses_percentage, "color" => 'green');
            array_push($graph_pi, $row);

            $apartments_percentage = round((($widgetData->apartmentCount / $total_properties) * 100), 2);
            $row = array("name" => "Apartments", "y" => $apartments_percentage, "color" => 'orange');
            array_push($graph_pi, $row);

            $commercial_percentage = round((($widgetData->commercialCount / $total_properties) * 100), 2);
            $row = array("name" => "Commercial", "y" => $commercial_percentage, "color" => 'blue');
            array_push($graph_pi, $row);

            $offices_percentage = round((($widgetData->officeCount / $total_properties) * 100), 2);
            $row = array("name" => "Offices", "y" => $offices_percentage, "color" => 'pink');
            array_push($graph_pi, $row);

            $parking_percentage = round((($widgetData->parkingCount / $total_properties) * 100), 2);
            $row = array("name" => "Parking", "y" => $parking_percentage, "color" => 'violet');
            array_push($graph_pi, $row);

            $full_pi_graph_data = array_values($graph_pi);

        }
        $full_pi_graph_data = json_encode($full_pi_graph_data);

        $districts = DB::table('districts')
            ->whereRaw("districts.status='1'")
            ->get();
        return $full_pi_graph_data;
    }
     
    
    
     public function getMunicipalityData()
    {

        $tenure_type = Input::get('municipality_tenure_type');

        $municipality_id = Input::get('municipality_id');
        $agentId=Session::get('agentId');
        
       $graphData=DB::table('properties')
                ->select(DB::raw(" 
                    SUM(CASE WHEN building_type='1' THEN 1 ELSE 0 END) AS houseCount
                   ,SUM(CASE WHEN building_type='2' THEN 1 ELSE 0 END) AS apartmentCount
                   ,SUM(CASE WHEN building_type='3' THEN 1 ELSE 0 END) AS commercialCount
                   ,SUM(CASE WHEN building_type='4' THEN 1 ELSE 0 END) AS officeCount
                   ,SUM(CASE WHEN building_type='5' THEN 1 ELSE 0 END) AS parkingCount"))
                ->whereRaw("properties.status='1' and properties.agent_id =$agentId")
                ->when($tenure_type, function ($query) use ($tenure_type) {
                        return $query->whereRaw("tenure_type = $tenure_type");
                 })
                 ->when($municipality_id != NULL, function ($query) use ($municipality_id) {
                        return $query->whereRaw("municipality_id = $municipality_id");
                 })
                ->first();
               
      
         if($graphData->houseCount==NULL){ 
             $graphData->houseCount=0;}
         if($graphData->apartmentCount==NULL){ 
              $graphData->apartmentCount=0;} 
         if($graphData->commercialCount==NULL){ 
              $graphData->commercialCount=0;} 
         if($graphData->officeCount==NULL){ 
              $graphData->officeCount=0;}
         if($graphData->parkingCount==NULL){ 
              $graphData->parkingCount=0;}
         
       
        return response()->json([
            'municipality_houses' => $graphData->houseCount,
            'municipality_apartments' => $graphData->apartmentCount,
            'municipality_commercial' => $graphData->commercialCount,
            'municipality_offices' => $graphData->officeCount,
            'municipality_parking' => $graphData->parkingCount
           
        ]);
    }
    
    function agentLogout(){
           try {
            session()->forget('agentId');
            session()->forget('agentLogin');
            session()->forget('adminLogin');
            session()->forget('agentUser');
             
            return Redirect::to('agent/login');
        } catch (\Exception $e) {
            
        }
    }
    
    public function edit_profile($id){
      try {
           $pageData=array();
         
            if ($id != "") {
                $id = \Crypt::decrypt($id);
               
                $agentData = DB::table('agents')
                    ->select('agents.*')
                    ->whereRaw("agents.id=$id")
                    ->whereRaw("status='1'")
                    ->first();

                $pageData['agentData'] = $agentData;
               

            }
            return view('agent.login.edit-profile',$pageData );

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
            Redirect::to('agent/dashboard');
        }
  
    }
    
    public function update_account(Request $request){
     try {
           
            $input=Input::all();
         
            $profileId= \Crypt::decrypt($input['profile_id']);
            $password=Input::get('password');
            $objmodel = new Agent();
            $objmodel->exists = true;
            $objmodel->id = $profileId;
            $objmodel->name= Input::get('name');
            if($password==""){}else{
            $objmodel->password= Hash::make($password);
            }
           
            if ($request->hasFile('preview_upload')) {
                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $adminProfile = "admin_" .  uniqid(). '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/agent';
                $file->move($path, $adminProfile);
                $objmodel->image= $adminProfile;
            }
            $save = $objmodel->save();
            
             $agent_data = DB::table('agents')
                        ->select('agents.*')
                        ->whereRaw("id= $profileId")
                        ->first();
             
            session(['agentLogin' => false]); session(['agentUser' => $agent_data]);
           
            Toastr::success('Updated User Details Successfully', $title = null, $options = []);
            return Redirect::to('agent/dashboard');
        }  catch (\Exception $e) {
           
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('agent/dashboard');
            
        }
  
    }
}
