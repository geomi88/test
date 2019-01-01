<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use DB;
use App\Models\Admin;
use Illuminate\Support\Facades\Input;

class AdminController extends Controller
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
    public function get_dashboard()
    {
        $listing_properties = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status=1")
            ->first();
        $houses = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status=1 and properties.building_type = 1")
            ->first();
        $apartments = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status=1 and properties.building_type = 2")
            ->first();
        $offices = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status=1 and properties.building_type = 4")
            ->first();
        $commercial = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status=1 and properties.building_type = 3")
            ->first();
        $parking = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status=1 and properties.building_type = 5")
            ->first();

        /*Pie Graph start*/
        $district = DB::table('districts')
            ->whereRaw("districts.status=1")
            ->first();

        $tenure_type = 2;
        $district_id = $district->id;

        $properties = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status=1 and district_id = $district_id and tenure_type = $tenure_type")
            ->first();
        $total_properties = $properties->properties_count;
        $graph_pi = array();
        $full_pi_graph_data = array();
        if ($total_properties > 0) {
            $graph_houses = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 1 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();
            $graph_apartments = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 2 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();
            $graph_commercial = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 3 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();
            $graph_offices = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 4 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();
            $graph_parking = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 5 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();

            $houses_percentage = round((($graph_houses->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Houses", "y" => $houses_percentage, "color" => 'green');
            array_push($graph_pi, $row);

            $apartments_percentage = round((($graph_apartments->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Apartments", "y" => $apartments_percentage, "color" => 'orange');
            array_push($graph_pi, $row);

            $commercial_percentage = round((($graph_commercial->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Commercial", "y" => $commercial_percentage, "color" => 'blue');
            array_push($graph_pi, $row);

            $offices_percentage = round((($graph_offices->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Offices", "y" => $offices_percentage, "color" => 'pink');
            array_push($graph_pi, $row);

            $parking_percentage = round((($graph_parking->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Parking", "y" => $parking_percentage, "color" => 'violet');
            array_push($graph_pi, $row);

            $full_pi_graph_data = array_values($graph_pi);

        }
        $full_pi_graph_data = json_encode($full_pi_graph_data);
        $districts = DB::table('districts')
            ->whereRaw("districts.status=1")
            ->get();
        /*Pie Graph end*/

        $municipality_tenure_type = 2;
        $municipality = DB::table('municipalities')
            ->whereRaw("municipalities.status=1")
            ->first();

        $municipality_id = $municipality->id;
        $municipality_name = $municipality->name;

        $municipalities = DB::table('municipalities')
            ->whereRaw("municipalities.status=1")
            ->get();
        $municipality_houses = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 1 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();
        $municipality_apartments = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 2 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();
        $municipality_commercial = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 3 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();
        $municipality_offices = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 4 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();
        $municipality_parking = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 5 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();

        return view('admin.dashboard.dashboard', array(
            'listing_properties' => $listing_properties,
            'houses' => $houses, 'apartments' => $apartments,
            'offices' => $offices, 'commercial' => $commercial,
            'parking' => $parking, 'full_pi_graph_data' => $full_pi_graph_data,
            'tenure_type' => $tenure_type, 'districts' => $districts,
            'district_id' => $district_id, 'municipality_id' => $municipality_id,
            'municipalities' => $municipalities, 'municipality_name' => $municipality_name,
            'municipality_houses' => $municipality_houses, 'municipality_apartments' => $municipality_apartments,
            'municipality_commercial' => $municipality_commercial, 'municipality_offices' => $municipality_offices,
            'municipality_parking' => $municipality_parking, 'municipality_tenure_type' => $municipality_tenure_type,
        ));
    }

    public function getDistrictData()
    {

        $tenure_type = Input::get('tenure_type');
        $district_id = Input::get('district_id');
        $properties = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.status=1 and district_id = $district_id and tenure_type = $tenure_type")
            ->first();
        $total_properties = $properties->properties_count;
        $graph_houses = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 1 and district_id = $district_id and tenure_type = $tenure_type")
            ->first();
        $graph_apartments = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 2 and district_id = $district_id and tenure_type = $tenure_type")
            ->first();
        $graph_commercial = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 3 and district_id = $district_id and tenure_type = $tenure_type")
            ->first();
        $graph_offices = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 4 and district_id = $district_id and tenure_type = $tenure_type")
            ->first();
        $graph_parking = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 5 and district_id = $district_id and tenure_type = $tenure_type")
            ->first();

        $graph_pi = array();
        $full_pi_graph_data = array();
        if ($total_properties > 0) {
            $graph_houses = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 1 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();
            $graph_apartments = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 2 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();
            $graph_commercial = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 3 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();
            $graph_offices = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 4 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();
            $graph_parking = DB::table('properties')
                ->select(db::raw('COUNT(*) AS properties_count'))
                ->whereRaw("properties.building_type = 5 and district_id = $district_id and tenure_type = $tenure_type")
                ->first();

            $houses_percentage = round((($graph_houses->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Houses", "y" => $houses_percentage, "color" => 'green');
            array_push($graph_pi, $row);

            $apartments_percentage = round((($graph_apartments->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Apartments", "y" => $apartments_percentage, "color" => 'orange');
            array_push($graph_pi, $row);

            $commercial_percentage = round((($graph_commercial->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Commercial", "y" => $commercial_percentage, "color" => 'blue');
            array_push($graph_pi, $row);

            $offices_percentage = round((($graph_offices->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Offices", "y" => $offices_percentage, "color" => 'pink');
            array_push($graph_pi, $row);

            $parking_percentage = round((($graph_parking->properties_count / $total_properties) * 100), 2);
            $row = array("name" => "Parking", "y" => $parking_percentage, "color" => 'violet');
            array_push($graph_pi, $row);

            $full_pi_graph_data = array_values($graph_pi);

        }
        $full_pi_graph_data = json_encode($full_pi_graph_data);

        $districts = DB::table('districts')
            ->whereRaw("districts.status=1")
            ->get();
        return $full_pi_graph_data;
    }

    public function getMunicipalityData()
    {

        $tenure_type = Input::get('municipality_tenure_type');

        $municipality_id = Input::get('municipality_id');
        $municipality = DB::table('municipalities')
            ->whereRaw("municipalities.id = $municipality_id")
            ->first();

        $municipality_houses = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 1 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();
        $municipality_apartments = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 2 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();
        $municipality_commercial = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 3 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();
        $municipality_offices = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 4 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();
        $municipality_parking = DB::table('properties')
            ->select(db::raw('COUNT(*) AS properties_count'))
            ->whereRaw("properties.building_type = 5 and municipality_id = $municipality_id and tenure_type = $tenure_type")
            ->first();
        return response()->json([
            'municipality_houses' => $municipality_houses,
            'municipality_apartments' => $municipality_apartments,
            'municipality_commercial' => $municipality_commercial,
            'municipality_offices' => $municipality_offices,
            'municipality_parking' => $municipality_parking,
            'municipality' => $municipality,
        ]);
    }
    
    public function edit_profile($id){
      try {
           $pageData=array();
         
            if ($id != "") {
                $id = \Crypt::decrypt($id);
               
                $adminData = DB::table('admin')
                    ->select('admin.*')
                    ->whereRaw("admin.id=$id")
                    ->whereRaw("status='1'")
                    ->first();

                $pageData['adminData'] = $adminData;
               

            }
            return view('admin.login.edit-profile',$pageData );

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
            Redirect::to('admin/dashboard');
        }
  
    }
    
    
    public function update_account(Request $request){
     try {
           
            $input=Input::all();
         
            $profileId= \Crypt::decrypt($input['profile_id']);
            $password=Input::get('password');
            $objmodel = new Admin();
            $objmodel->exists = true;
            $objmodel->id = $profileId;
            $objmodel->name= Input::get('name');
            if($password==""){ }else{
            $objmodel->password= Hash::make($password);
            }
          
            
            if ($request->hasFile('preview_upload')) {
                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $adminProfile = "admin_" .  uniqid(). '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/agent';
                $file->move($path, $adminProfile);
                $objmodel->images= $adminProfile;
            }
            $save = $objmodel->save();
            
             $admin_data = DB::table('admin')
                        ->select('admin.*')
                        ->whereRaw("id= $profileId")
                        ->first();
             
            session(['agentLogin' => false]); session(['adminUser' => $admin_data]);
           
            Toastr::success('Updated User Details Successfully', $title = null, $options = []);
            return Redirect::to('admin/dashboard');
        }  catch (\Exception $e) {
           
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/dashboard');
            
        }
  
    }
}
