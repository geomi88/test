<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Projects;
use App\Model\ProjectGallery;
use App\Model\ProjectGarden;
use App\Model\ProjectSlider;
use App\Model\ProjectSurrounding;
use App\Model\ProjectFloors;
use App\Model\Municipalities;

use App\Model\PropertyImage;
use App\Services\Common;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Kamaln7\Toastr\Facades\Toastr;
class ConstructionController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin_auth');
        //write athenitcate function for admin
    }
    
    public function list_construction(Request $request)
    {
        $paginate = '10';
        $projectsList = DB::table('projects')
            ->select('projects.*',  'municipalities.name as muncname', 'districts.name as districtname')
            //->leftjoin('agents', 'agents.id', '=', 'projects.agent_id')
            ->leftjoin('municipalities', 'municipalities.id', '=', 'projects.municipality_id')
            ->leftjoin('districts', 'districts.id', '=', 'projects.district_id')
            ->where('projects.status', '=', '1')
            ->paginate($paginate);

        $common = new Common();
       // $agents = $common->agents();
        $districts = $common->districts();

        $pageData['projectsList'] = $projectsList;
       // $pageData['agentList'] = $agents;
        $pageData['districts'] = $districts;

        if ($request->ajax()) {

           // $agent = Input::get('agent');

            $district = Input::get('district');
            $searchKey = Input::get('searchKey');

           $projectsList = DB::table('projects')
                ->select('projects.*',  'municipalities.name as muncname', 'districts.name as districtname')
                //->leftjoin('agents', 'agents.id', '=', 'projects.agent_id')
                ->leftjoin('municipalities', 'municipalities.id', '=', 'projects.municipality_id')
                ->leftjoin('districts', 'districts.id', '=', 'projects.district_id')
                ->where('projects.status', '=', '1')
//                ->when($agent, function ($query) use ($agent) {
//                    if ($agent == "admin") {
//                        return $query->whereRaw("(projects.agent_id IS NULL)");
//                    } else {
//                        return $query->whereRaw("(projects.agent_id='$agent')");
//                    }
//
//                })
                ->when($district, function ($query) use ($district) {
                    return $query->whereRaw("(projects.district_id=$district)");
                })
                ->when($searchKey, function ($query) use ($searchKey) {
                    return $query->whereRaw("(projects.name_en like '%$searchKey%' OR municipalities.name like '%$searchKey%' OR districts.name like '%$searchKey%')");
                })
                ->paginate($paginate);

            $pageData['projectsList'] = $projectsList;

            return view('admin.construction.construction-list-result', array('pageData' => $pageData));
        }

        return view('admin.construction.construction-list', array('pageData' => $pageData));
    }

    public function add_construction()
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

            return view('admin.construction.add-construction', array('pageData' => $pageData));

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
       
    }

    public function save_construction(Request $request)
    {
     
        try{
         $input = Input::all();
            
            $objmodel = new Projects();
            $objmodel->name_en = Input::get('name_en');
            $objmodel->name_ka = Input::get('name_ka');
            $objmodel->name_ru = Input::get('name_ru');
            $objmodel->price = Input::get('price');
            
            $objmodel->url = Input::get('url');
            $objmodel->address_line1_en = Input::get('address_line1_en');
            $objmodel->address_line1_ka = Input::get('address_line1_ka');
            $objmodel->address_line1_ru = Input::get('address_line1_ru');
            $objmodel->address_line2_en = Input::get('address_line2_en');
            $objmodel->address_line2_ka = Input::get('address_line2_ka');
            $objmodel->address_line2_ru = Input::get('address_line2_ru');
            $objmodel->zip_en = Input::get('zip_en');
            $objmodel->zip_ka = Input::get('zip_ka');
            $objmodel->zip_ru = Input::get('zip_ru');


            $objmodel->description_en = Input::get('description_en');
            $objmodel->description_ka = Input::get('description_ka');
            $objmodel->description_ru = Input::get('description_ru');
            
            $objmodel->surrounding_description_en = Input::get('surrounding_description_en');
            $objmodel->surrounding_description_ka = Input::get('surrounding_description_ka');
            $objmodel->surrounding_description_ru = Input::get('surrounding_description_ru');
            
            $objmodel->garden_description_en = Input::get('garden_description_en');
            $objmodel->garden_description_ka = Input::get('garden_description_ka');
            $objmodel->garden_description_ru = Input::get('garden_description_ru');
            
            $objmodel->comfort_description_en = Input::get('comfort_description_en');
            $objmodel->comfort_description_ka = Input::get('comfort_description_ka');
            $objmodel->comfort_description_ru = Input::get('comfort_description_ru');

            
            $objmodel->municipality_id = Input::get('municipality_en');
            $objmodel->district_id = Input::get('district_en');
            
            $objmodel->planning_permit = Input::get('planning_permits');
            $objmodel->subpoenas = Input::get('subpoenas');
            $objmodel->judicial_sayings = Input::get('judicial_sayings');
            $objmodel->pre_emption_right = Input::get('pre_emption_right');
            $objmodel->subdivision_permit = Input::get('subdivision_permit');
            $objmodel->flood_area = Input::get('flood_area');
            $objmodel->destination = Input::get('destination');
            $objmodel->status = '1';

            //$latitude = Input::get('latitude');
           // $longitude = Input::get('longitude');

           // if ($latitude == "" && $longitude == "") {
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

            //}

            $objmodel->latitude = $latitude;
            $objmodel->longitude = $longitude;

            
//            if (Session::get('adminLogin') != 'true') {
//                $objmodel->agent_id = null;
//            }
            
             if ($request->file('floorplan')) {

                    $image = $request->file('floorplan');
                    $name = $image->getClientOriginalName();
                    $filename = "floorplan".'_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/floor-plan';
                    $image->move($path, $filename);

                    
                    $objmodel->floorplan = $filename;
                    
                }

            $objmodel->save();
            $property_id =\Crypt::encrypt($objmodel->id) ;

            


            Toastr::success('Successfully Added New Property!', $title = null, $options = []);
            return Redirect::to('admin/construction-view/'.$property_id);

        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/property-listing');
        }
}

public function view_construction($p_id)
    {
        try {
          
            $common = new Common();
            $render_params = array();
            $render_params['project'] = "";
            

            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);


                    
                $projectsList = DB::table('projects')
                            ->select('projects.*', 'municipalities.name as muncname', 'districts.name as districtname')
                            //->leftjoin('agents', 'agents.id', '=', 'projects.agent_id')
                            ->leftjoin('municipalities', 'municipalities.id', '=', 'projects.municipality_id')
                            ->leftjoin('districts', 'districts.id', '=', 'projects.district_id')
                            ->where('projects.status', '=', '1')
                            ->whereRaw("projects.id=$p_id")
                            ->first();
                    
                  $contact_form= DB::table('contact_form')
                    ->select('contact_form.*')
                    ->whereRaw("project_id=$p_id")
                    ->get();

               $data= array(
                   'project'=>$projectsList,
                   'project_interest'=>count($contact_form)
                       );
               

            }
            return view('admin.construction.construction-detail', $data);

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }
    
    public function add_floor($p_id) {

        try {

            $dataArray = array(
                'projectId' => $p_id
            );

         return view('admin.construction.add-floor', $dataArray);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function add_gallery($p_id) {
        
         try {

             $dataArray=array(
                 'projectId' => $p_id
                     );
           
            return view('admin.construction.add-gallery', $dataArray);

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
       
    }
    
    public function save_gallery(Request $request)
    {
     
        try {
            $input = Input::all();
            $projectId = Input::get('projectId');
            $property_id = Input::get('projectId');

            $projectId = \Crypt::decrypt($projectId);
            
                DB::table('projects')
                        ->whereRaw("id=$projectId")
                        ->update(['gallery_insert' => '1']);

            $galleryCount = Input::get('galleryCount');

            for ($i = 0; $i <= $galleryCount; $i++) {

                if ($request->file('gallery_upload_' . $i)) {

                    $image = $request->file('gallery_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "gallery_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/project-gallery';
                    $image->move($path, $filename);

                    $property = new ProjectGallery();
                    $property->project_id = $projectId;
                    $property->image = $filename;
                    $property->save();
                }
            }

            $gardenCount = Input::get('gardenCount');

            for ($i = 0; $i <= $gardenCount; $i++) {

                if ($request->file('garden_upload_' . $i)) {

                    $image = $request->file('garden_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "garden_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/project-garden';
                    $image->move($path, $filename);

                    $property = new ProjectGarden();
                    $property->project_id = $projectId;
                    $property->image = $filename;
                    $property->save();
                }
            }

            $sliderCount = Input::get('sliderCount');

            for ($i = 0; $i <= $sliderCount; $i++) {

                if ($request->file('slider_upload_' . $i)) {

                    $image = $request->file('slider_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "slider_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/project-slider';
                    $image->move($path, $filename);

                    $property = new ProjectSlider();
                    $property->project_id = $projectId;
                    $property->image = $filename;
                    $property->save();
                }
            }

            $surroundCount = Input::get('surroundCount');

            for ($i = 0; $i <= $surroundCount; $i++) {

                if ($request->file('surrounding_upload_' . $i)) {

                    $image = $request->file('surrounding_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "surrounding_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/project-surrounding';
                    $image->move($path, $filename);

                    $property = new ProjectSurrounding();
                    $property->project_id = $projectId;
                    $property->image = $filename;
                    $property->save();
                }
            }
            
             if ($request->hasFile('preview_upload')) {
                 DB::table('project_gallery')
                        ->whereRaw("project_id=$projectId")
                        ->whereRaw("main_image='1'")
                        ->update(['status' => '0']);
                 
                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $filename = "gallery_" .uniqid() . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/project-gallery';
                $file->move($path, $filename);
                
                    $property = new ProjectGallery();
                    $property->project_id = $projectId;
                    $property->image = $filename;
                    $property->main_image = '1';
                    $property->save();
            }




            Toastr::success('Successfully Added Gallery!', $title = null, $options = []);
            return Redirect::to('admin/construction-view/' . $property_id);
        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/construction-list');
        }
}

  public function save_floor(Request $request)
    {
     
        try {
            $input = Input::all();
            $projectId = Input::get('projectId');
            $projectId = \Crypt::decrypt($projectId);

            $property_id = Input::get('projectId');
            
             DB::table('projects')
                        ->whereRaw("id=$projectId")
                        ->update(['floor_insert' => '1']);

            $objModel = new ProjectFloors();
            $objModel->name_en = Input::get('name_en');
            $objModel->name_ka = Input::get('name_ka');
            $objModel->name_ru = Input::get('name_ru');
            $objModel->price = Input::get('price');
            $objModel->approximate_area = Input::get('area');
            $objModel->terrace = Input::get('terrace');
            $objModel->no_of_beds = Input::get('bedroom');
            $objModel->no_of_baths = Input::get('bathroom');
            $objModel->project_id = $projectId;
            $objModel->floor_num =Input::get('floor');
            $saveType = Input::get('saveType');
           
            
            if ($request->file('floor_plan')) {

                    $image = $request->file('floor_plan');
                    $name = $image->getClientOriginalName();
                    $filename = "floor_plan".'_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/floor-plan';
                    $image->move($path, $filename);

                    
                    $objModel->floor_plan = $filename;
                    
                }
           $objModel->save();
          Toastr::success('Successfully Added Floors!', $title = null, $options = []);
          if($saveType=='Add'){
            return Redirect::to('admin/add-floor/' . $property_id);
          }else{
            return Redirect::to('admin/construction-view/'. $property_id);
          }
        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/construction-list');
        }
}


public function remove_construction($id)
    {
      try {
            
            $projectId = \Crypt::decrypt($id);
            $property = new Projects();
                    $property->exists = true;
                    $property->id = $projectId;
                    $property->status = '0';
                    $property->save();
                    
          
            Toastr::success('Successfully Removed Construction!', $title = null, $options = []);
            return Redirect::to('admin/construction-list');
        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/construction-list');
        }
}

public function checkFloorExist(Request $request)
    {
      try {
            
            $projectId = Input::get('projectId');
            $id        =  \Crypt::decrypt($projectId);
            $floor = Input::get('floor');
            $property = DB::table('project_floors')
                    ->select('project_floors.*')
                    ->whereRaw("project_floors.project_id = $id")
                    ->whereRaw("project_floors.status = '1'")
                    ->whereRaw("project_floors.floor_num = $floor")
                    ->first();
              if($property==null) {
                  return 0;
              } else{
                  return 1;
              }    
        } catch (Exception $ex) {
            echo $ex->getMessage();
            
           // return Redirect::to('admin/construction-list');
        }
}


 public function edit_construction($p_id)
    {
       try {
          
            $common = new Common();
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

            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);


                    
                $projectsList = DB::table('projects')
                            ->select('projects.*',  'municipalities.name as muncname', 'districts.name as districtname')
                            //->leftjoin('agents', 'agents.id', '=', 'projects.agent_id')
                            ->leftjoin('municipalities', 'municipalities.id', '=', 'projects.municipality_id')
                            ->leftjoin('districts', 'districts.id', '=', 'projects.district_id')
                            ->where('projects.status', '=', '1')
                            ->whereRaw("projects.id=$p_id")
                            ->first();
                    
     
                $data= array(
                   'project'=>$projectsList,
                   'buildings'=>$buildings,
                   'neighborhoods'=>$neighborhoods,
                   'municipalities'=>$municipalities,
                   'district'=>$districts
                       );
               

            }
            return view('admin.construction.edit-construction', $data);

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    
    public function update_construction(Request $request){
        
         try{
         $input = Input::all();
           $id=Input::get('projectId');
           $projectId= \Crypt::decrypt(Input::get('projectId')); 
           
            $objmodel = new Projects();
            $objmodel->exists = true;
            $objmodel->id = $projectId;
            $objmodel->name_en = Input::get('name_en');
            $objmodel->name_ka = Input::get('name_ka');
            $objmodel->name_ru = Input::get('name_ru');
            $objmodel->price = Input::get('price');
            
            $objmodel->url = Input::get('url');
            $objmodel->address_line1_en = Input::get('address_line1_en');
            $objmodel->address_line1_ka = Input::get('address_line1_ka');
            $objmodel->address_line1_ru = Input::get('address_line1_ru');
            $objmodel->address_line2_en = Input::get('address_line2_en');
            $objmodel->address_line2_ka = Input::get('address_line2_ka');
            $objmodel->address_line2_ru = Input::get('address_line2_ru');
            $objmodel->zip_en = Input::get('zip_en');
            $objmodel->zip_ka = Input::get('zip_ka');
            $objmodel->zip_ru = Input::get('zip_ru');

            $objmodel->description_en = Input::get('description_en');
            $objmodel->description_ka = Input::get('description_ka');
            $objmodel->description_ru = Input::get('description_ru');
            
            $objmodel->surrounding_description_en = Input::get('surrounding_description_en');
            $objmodel->surrounding_description_ka = Input::get('surrounding_description_ka');
            $objmodel->surrounding_description_ru = Input::get('surrounding_description_ru');
            
            $objmodel->garden_description_en = Input::get('garden_description_en');
            $objmodel->garden_description_ka = Input::get('garden_description_ka');
            $objmodel->garden_description_ru = Input::get('garden_description_ru');
            
            $objmodel->comfort_description_en = Input::get('comfort_description_en');
            $objmodel->comfort_description_ka = Input::get('comfort_description_ka');
            $objmodel->comfort_description_ru = Input::get('comfort_description_ru');

            
            $municipality_name=Input::get('municipality_name');
            if($municipality_name==""){
                $objmodel->municipality_id = Input::get('municipality_en');
                $muncId=Input::get('municipality_en');
            }else{
              $municipalityDetail=  DB::table('municipalities')
                    ->select('municipalities.id')
                    ->whereRaw("municipalities.name=$municipality_name")
                    ->first();
              if($municipalityDetail==""){
                  $newInsert=new Municipalities();
                  $newInsert->name=$municipality_name;
                  $newInsert->save();
                  $muncId=$newInsert->id;
                  $objmodel->municipality_id = $muncId;
              }else{
                  $objmodel->municipality_id =$municipalityDetail->id;
                  $muncId=$municipalityDetail->id;
              }
              
                
            }
            
               $district_name=Input::get('district_name');
            if($district_name==""){
                $objmodel->district_id = Input::get('district_en');
                $districtId = Input::get('district_en');
            }else{
              $districtDetail=  DB::table('districts')
                    ->select('districts.id')
                    ->whereRaw("districts.name=$district_name")
                    ->first();
              if($districtDetail==""){
                  $disInsert=new Districts();
                  $disInsert->name=$district_name;
                  $disInsert->save();
                  $districtId=$disInsert->id;
                  $objmodel->district_id = $districtId;
                  
              }else{
                  $objmodel->district_id =$districtDetail->id;
                  $districtId=$districtDetail->id;
              }
              
                
            }
                      
            
            
            $objmodel->planning_permit = Input::get('planning_permits');
            $objmodel->subpoenas = Input::get('subpoenas');
            $objmodel->judicial_sayings = Input::get('judicial_sayings');
            $objmodel->pre_emption_right = Input::get('pre_emption_right');
            $objmodel->subdivision_permit = Input::get('subdivision_permit');
            $objmodel->flood_area = Input::get('flood_area');
            $objmodel->destination = Input::get('destination');
            $objmodel->status = '1';

            $latitude = Input::get('latitude');
            $longitude = Input::get('longitude');

           // if ($latitude == "" && $longitude == "") {
          //      $muncId = Input::get('municipality_en');
          //      $districtId = Input::get('district_en');
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

          //  }

            $objmodel->latitude = $latitude;
            $objmodel->longitude=$longitude;

              if ($request->file('floorplan')) {

                    $image = $request->file('floorplan');
                    $name = $image->getClientOriginalName();
                    $filename = "floorplan".'_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/floor-plan';
                    $image->move($path, $filename);

                    
                    $objmodel->floorplan = $filename;
                    
                }
            
//            if (Session::get('adminLogin') != 'true') {
//                $objmodel->agent_id = null;
//            }

            $objmodel->save();
           

            Toastr::success('Successfully Updated Property!', $title = null, $options = []);
            return Redirect::to('admin/construction-view/'.$id);

        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/property-listing');
        }
    }
    
   /* public function list_floor($id){
        $projectId=\Crypt::decrypt($id);
        
        $floorList = DB::table('project_floors')
            ->select('project_floors.*')
            ->where('projects.status', '=', '1')
            ->where('projects.id', '=', $projectId)
            ->paginate($paginate);
        
        $pageData=array(
            'floorList'=>$floorList
        );

            return view('admin.construction.floor-list-result', $pageData);
    }*/
    
      public function list_floor($id)
    {
        $paginate = '10';
        $projectId=\Crypt::decrypt($id);
        
        $floorList = DB::table('project_floors')
            ->select('project_floors.*')
            ->where('project_floors.status', '=', '1')
            ->where('project_floors.project_id', '=', $projectId)
            ->paginate($paginate);
        
        $pageData=array(
            'floorList'=>$floorList,
            'projectId'=>$id
        );
        
       return view('admin.construction.floor-list',$pageData);
    }
    
     public function edit_floor($id)
     {
        
        $floorId=\Crypt::decrypt($id);
       
        
        $floorList = DB::table('project_floors')
            ->select('project_floors.*')
            ->where('project_floors.status', '=', '1')
            ->where('project_floors.id', '=', $floorId)
            ->first();
        $projectId= \Crypt::encrypt($floorList->project_id);
        
        $pageData=array(
            'floorList'=>$floorList,
            'projectId'=>$projectId
        );
        
       return view('admin.construction.edit-floor',$pageData);
    }

    public function checkFloorUpdateExist(Request $request)
    {
      try {
            
            $projectId = Input::get('projectId');
            $id        =  \Crypt::decrypt($projectId);
            $floor = Input::get('floor');
            $floorId = Input::get('floorId');
            $property = DB::table('project_floors')
                    ->select('project_floors.*')
                    ->whereRaw("project_floors.project_id = $id")
                    ->whereRaw("project_floors.status = '1'")
                    ->whereRaw("project_floors.floor_num = $floor")
                    ->whereRaw("project_floors.id <> $floorId")
                    ->first();
              if($property==null) {
                  return 0;
              } else{
                  return 1;
              }    
        } catch (Exception $ex) {
            echo $ex->getMessage();
            
           // return Redirect::to('admin/construction-list');
        }
}

 public function update_floor(Request $request)
    {
     
        try {
            $input = Input::all();
            $floorId = Input::get('floorId');
            
            $projectId = Input::get('projectId');
            $objModel = new ProjectFloors();
            $objModel->exists = true;
            $objModel->id = $floorId;
            $objModel->name_en = Input::get('name_en');
            $objModel->name_ka = Input::get('name_ka');
            $objModel->name_ru = Input::get('name_ru');
            $objModel->price = Input::get('price');
            $objModel->approximate_area = Input::get('area');
            $objModel->terrace = Input::get('terrace');
            $objModel->no_of_beds = Input::get('bedroom');
            $objModel->no_of_baths = Input::get('bathroom');
           
            $objModel->floor_num =Input::get('floor');
            $saveType = Input::get('saveType');
           
            
            if ($request->file('floor_plan')) {

                    $image = $request->file('floor_plan');
                    $name = $image->getClientOriginalName();
                    $filename = "floor_plan".'_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/floor-plan';
                    $image->move($path, $filename);

                    
                    $objModel->floor_plan = $filename;
                    
                }
           $objModel->save();
          Toastr::success('Successfully Added Floors!', $title = null, $options = []);
          if($saveType=='Add'){
            return Redirect::to('admin/list-floor/' . $projectId);
          }else{
            return Redirect::to('admin/list-floor/'. $projectId);
          }
        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/construction-list');
        }
}

public function remove_floor($id,$pid)
    {
      try {
            
            $floorId = \Crypt::decrypt($id);
            $property = new ProjectFloors();
                    $property->exists = true;
                    $property->id = $floorId;
                    $property->status = '0';
                    $property->save();
                    
          
            Toastr::success('Successfully Removed Floor!', $title = null, $options = []);
            return Redirect::to('admin/list-floor/'.$pid);
        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/list-floor/'.$pid);
        }
}


  public function edit_gallery($id)
    {
      $pid=\Crypt::decrypt($id);
        $previewImg= DB::table('project_gallery')
                    ->select('project_gallery.*')
                    ->whereRaw("project_gallery.project_id = $pid")
                    ->whereRaw("project_gallery.main_image = '1'")
                    ->whereRaw("project_gallery.status = '1'")
                    ->first();
        $galleryImg= DB::table('project_gallery')
                    ->select('project_gallery.*')
                    ->whereRaw("project_gallery.project_id = $pid")
                    ->whereRaw("project_gallery.status = '1'")
                    ->whereRaw("project_gallery.main_image IS NULL")
                    ->get();
        $gardenImg = DB::table('project_garden')
                    ->select('project_garden.*')
                    ->whereRaw("project_garden.project_id = $pid")
                    ->whereRaw("project_garden.status = '1'")
                    ->get();
        
        $slidergallery = DB::table('project_slider')
                    ->select('project_slider.*')
                    ->whereRaw("project_slider.project_id = $pid")
                    ->whereRaw("project_slider.status = '1'")
                    ->get();
        
        $surroundImg = DB::table('project_surrounding')
                    ->select('project_surrounding.*')
                    ->whereRaw("project_surrounding.project_id = $pid")
                    ->whereRaw("project_surrounding.status = '1'")
                    ->get();
        
        
        $dataArray = array(
            'projectId' => $id,
            'galleryImg' => $galleryImg,
            'gardenImg' => $gardenImg,
            'slidergallery' => $slidergallery,
            'surroundImg' => $surroundImg,
            'previewImg'=>$previewImg
                    );

        return view('admin.construction.edit-gallery',$dataArray);
    
       
    }
    
    public function remove_slider()
    {
       $imgId=Input::get('imgId');
       
       
            DB::table('project_slider')
            ->whereRaw("id=$imgId")
            ->update(['status' => '0']);
            
        return 1;
}

     public function remove_gallery()
    {
        $imgId=Input::get('imgId');
       
       
            DB::table('project_gallery')
            ->whereRaw("id=$imgId")
            ->update(['status' => '0']);
            
        return 1;
}
    
     public function remove_surround()
    {
        $imgId=Input::get('imgId');
        
       
            DB::table('project_surrounding')
            ->whereRaw("id=$imgId")
            ->update(['status' => '0']);
            
        return 1;
}
     public function remove_garden()
    {
        $imgId=Input::get('imgId');
       
       
            DB::table('project_garden')
            ->whereRaw("id=$imgId")
            ->update(['status' => '0']);
            
        return 1;
}


public function checkNameExist(Request $request)
    {
      try {
            
            $projectUrl = Input::get('projectUrl');
            $property = DB::table('projects')
                    ->select('projects.url')
                    ->whereRaw("projects.status = '1'")
                    ->whereRaw("projects.url = '$projectUrl'")
                    ->first();
              if($property==null) {
                  return 0;
              } else{
                  return 1;
              }    
        } catch (Exception $ex) {
            echo $ex->getMessage();
            
           // return Redirect::to('admin/construction-list');
        }
}

   
 public function checkNameUpdateExist(Request $request)
    {
      try {
            
            $projectId = Input::get('projectId');
            $projectUrl = Input::get('projectUrl');
            $id        =  \Crypt::decrypt($projectId);
            
            $property = DB::table('projects')
                    ->select('projects.url')
                    ->whereRaw("projects.status = '1'")
                    ->whereRaw("projects.url = '$projectUrl'")
                    ->whereRaw("projects.id = $id")
                    ->first();
              if($property==null) {
                  return 0;
              } else{
                  return 1;
              }    
        } catch (Exception $ex) {
            echo $ex->getMessage();
            
           // return Redirect::to('admin/construction-list');
        }
}

}
