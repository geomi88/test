<?php

namespace App\Http\Controllers\Web;

use App;
use App\Http\Controllers\Controller;
use App\Model\ContactForm;
use App\Model\EstimateForm;
use App\Model\Property;
use App\Model\Projects;
use App\Model\StayInformedBuilding;
use App\Model\StayInformedForm;
use App\Model\StayInformedMuncipality;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Mail;
class WebController extends Controller
{

    public function __construct()
    {

    }

    /**
     * Show the website landing page .
     *
     * @param  null
     * @return View
     */
    public function landing()
    {
          $this->lang = Session::get('lang');
            $render_params=array();
            $construction = array();
            $construction[] = "project_gallery.image as images";
            $construction[] = "projects.name_" . $this->lang . " as name ";
            $construction[] = "projects.url" ;
            $construction[] = "projects.id" ;
          

            $construction = implode(',', $construction);
            $construction_list = Projects::select(DB::raw($construction))
                        ->leftjoin('project_gallery', function($join) {
                        $join->on('project_gallery.project_id', '=', 'projects.id');
                        $join->where('project_gallery.main_image', '=', '1');
                        $join->where('project_gallery.status', '=', '1');

                        })
                ->where('projects.status', '=', '1');
                $construction_list = $construction_list->groupby('projects.id');
                $construction_list = $construction_list->orderby('projects.id','desc');
                $construction_list = $construction_list->limit(3)->get();
                
                $render_params['construction_list'] = $construction_list;
        return view('web.landing',$render_params);
    }

    /**
     * ajax post response
     * Show the website landing page .
     *
     * @param  ptype=house,appartment,commercial,office
     * @return json
     */
    public function propertiesList()
    {
        $this->lang = Session::get('lang');
        //get posted pype
        //return json response
        $response = array();
        $response['error'] = 1;
        try {
            $ptype = Input::get('ptype');

            $propery_raws = array();
            //$property_raws[] = "GROUP_CONCAT(property_images.image) as images";
            //modified to get the main image
            $property_raws[] = "property_images.image as images";
            $property_raws[] = "properties.*";
            $property_raws[] = "properties.name_" . $this->lang . " as name ";
            $property_raws[] = "properties.description_" . $this->lang . " as description ";
            $property_raws[] = "properties.address_line1_" . $this->lang . " as address1 ";
            $property_raws[] = "properties.address_line2_" . $this->lang . " as address2 ";
            $property_raws[] = "properties.zip_" . $this->lang . " as zip ";

            $property_raws = implode(',', $property_raws);
            $property_list = Property::select(DB::raw($property_raws), 'municipalities.name as muncipality')
                ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                ->leftjoin('property_images', function ($join) {
                    $join->on('property_images.property_id', '=', 'properties.id');
                    $join->where('property_images.main_image', '=', '1');
                    $join->where('property_images.status', '=', '1');
                    //added to get main image only
                    $join->orderby('property_images.main_image', 'DESC');
                });
            $property_list = $property_list->groupby('properties.id');
            $property_list = $property_list->where('properties.status', '=', '1');

            /*$property_list = Property::select('properties.*', 'municipalities.name as muncipality',
            DB::raw('GROUP_CONCAT(property_images.image) as images'))
            ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
            ->leftjoin('property_images', function($join) {
            $join->on('property_images.property_id', '=', 'properties.id');
            $join->orderby('property_images.main_image', 'DESC');
            });*/
            if ($ptype != "") {
                $ptype = \Crypt::decrypt($ptype);
                $property_list = $property_list->where('building_type', '=', $ptype);
            }
            $property_list = $property_list->where('properties.status', '=', '1');
            $page_path = 'web.includes.house_grid';
            $property_list = $property_list->groupby('properties.id');
            $property_list = $property_list->get();
            $render_params = array();
            $response['error'] = 0;
            $render_params['properties'] = $property_list;

            $response['html'] = View($page_path, $render_params)->render();
            $response['message'] = 'List success $p_type';
           
            
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
            $response['message'] = $ex->getLine();
        }
        return response()->json($response);
    }

    /**
     * ajax post response
     * Show the website landing page .
     *
     * @param  ptype=house,appartment,commercial,office
     * @return json
     */
    public function ajax_city_filter($city_text = "")
    {
        $lang = Session::get('lang');
        //get posted pype
        //return json response
        $response = array();
        $response['error'] = 1;
        $response['html'] = "";
        try {
            $cities = DB::table('municipalities as m')
                ->select('m.*', DB::raw('count(p.municipality_id) as property_count'))
                ->leftjoin('properties  as p', 'm.id', '=', 'p.municipality_id')
                ->groupby('m.id')
                ->orderby('property_count', 'DESC')
                ->orderby("m.name", 'ACS')
                ->whereRaw(" m.name LIKE '%" . $city_text . "%'")
                ->where('m.status', 1)->get();
            $render_params = array();
            $response['error'] = 0;
            $response['city_list'] = $cities;
            $response['html'] = View('web.includes.cityradio', $response)->render();
            $response['message'] = 'Rendered DATA';
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return response()->json($response);
    }

    public function setLanguage(Request $request)
    {

        $lang = Input::get('lang');
        $langName = Input::get('langName');

        if ($lang == null) {
            session(['locale' => 'en']);
            session(['langName' => 'English']);
            session(['lang' => 'en']);
        } else {
            session(['locale' => $lang]);
            session(['langName' => $langName]);
            session(['lang' => $lang]);
        }
        return 1;
    }
    public function savecontactform()
    {
        try {
            $contact_model = new ContactForm();
            $contact_model->first_name = Input::get('first_name');
            $contact_model->last_name = Input::get('last_name');
            $contact_model->email = Input::get('email');
            $contact_model->phone = Input::get('phone');
            $contact_model->message = Input::get('message');
            $contact_model->property_id = Input::get('propertyId');
            $contact_model->stay_informed = Input::get('stay_informed');
            $contact_model->save();

            $email = env('MAIL_USERNAME');
            $name = Input::get('first_name')." ".Input::get('last_name');
            $user_email = Input::get('email');
            $phone = Input::get('phone');
            $user_message = Input::get('message');
            $userEmail = Mail::send('emailtemplates.contact-request', ['name' => $name, 'user_email' => $user_email, 'phone' => $phone,'user_message' => $user_message], function ($mail) use ($user_email, $name, $phone,$user_message,$email) {
                $mail->to($email, $name)->subject('Contact Request');
            });
            return 1;
        } catch (Exception $ex) {
            return 0;
        }
    }
    public function informed()
    {
        return view('web.stay_informed');
    }
    public function savestayinformed()
    {
        try {
            $informed_model = new StayInformedForm();
            $informed_model->first_name = Input::get('first_name');
            $informed_model->last_name = Input::get('last_name');
            $informed_model->email = Input::get('email');
            $informed_model->phone = Input::get('phone');
            $informed_model->message = Input::get('message');
            $informed_model->street_number = Input::get('street_number');
            $informed_model->city = Input::get('city');
            $informed_model->zip = Input::get('zip');
            $informed_model->district = Input::get('district');
            $informed_model->tenure_type = Input::get('tenure_type');
            $informed_model->min_price = Input::get('min_price');
            $informed_model->max_price = Input::get('max_price');
            $informed_model->stay_informed = Input::get('stay_informed');
            $informed_model->save();
            $informed_id = $informed_model->id;

            $places_list = Input::get('places_list');
            $muncipality_array = array();
            $muncipalities = '';
            if (count($places_list) > 0) {
                
                foreach ($places_list as $muncipality) {
                    $informed_muncipality_model = new StayInformedMuncipality();
                    $informed_muncipality_model->stay_informed_id = $informed_id;
                    $informed_muncipality_model->muncipality_id = $muncipality;
                    $informed_muncipality_model->save();


                $muncipality_details = DB::table('municipalities as m')
                ->select('m.name')
                ->where('m.id', $muncipality)->first();
                array_push($muncipality_array,$muncipality_details->name);
                }
                $muncipalities = implode(',',$muncipality_array);
            }

            


            $building_type = Input::get('building_type');
            $building_array = array();
            $buildings = '';
            if (count($building_type) > 0) {
                foreach ($building_type as $building) {
                    $informed_building_model = new StayInformedBuilding();
                    $informed_building_model->stay_informed_id = $informed_id;
                    $informed_building_model->building_type_id = $building;
                    $informed_building_model->save();

                $building_details = DB::table('building_type as b')
                ->select('b.name')
                ->where('b.id', $building)->first();
                array_push($building_array,$building_details->name);
                }
                $buildings = implode(',',$building_array);
            }
            

            $email = env('MAIL_USERNAME');
            $name = Input::get('first_name')." ".Input::get('last_name');
            $user_email = Input::get('email');
            $phone = Input::get('phone');
            $city = Input::get('city');
            $zip = Input::get('zip');
            $district = Input::get('district');

            if(Input::get('tenure_type') == 1)
            {
                $tenure = "Sale";
            }
            else
            {
                $tenure = "Rent";
            }
            $min_price = Input::get('min_price');
            $max_price = Input::get('max_price');
            
            $user_message = Input::get('message');
            $userEmail = Mail::send('emailtemplates.stay-informed', ['name' => $name, 'user_email' => $user_email, 'phone' => $phone,'user_message' => $user_message,'city' => $city,'zip' => $zip,'district' => $district,'tenure' => $tenure,'min_price' => $min_price,'max_price' => $max_price,'muncipalities' => $muncipalities,'buildings' => $buildings], function ($mail) use ($user_email, $name, $phone,$user_message,$email,$city,$zip,$district,$tenure,$min_price,$max_price,$muncipalities,$buildings) {
                $mail->to($email, $name)->subject('Stay Informed');
            });
            return 1;
        } catch (Exception $ex) {
            return 0;
        }
    }

    public function estimate()
    {
        return view('web.estimate');
    }
    public function saveestimate()
    {
        try {
            $estimate_model = new EstimateForm();
            $estimate_model->first_name = Input::get('first_name');
            $estimate_model->last_name = Input::get('last_name');
            $estimate_model->email = Input::get('email');
            $estimate_model->tele_phone = Input::get('tele_phone');
            $estimate_model->mobile_phone = Input::get('mobile_phone');
            $estimate_model->message = Input::get('message');
            $estimate_model->street_number = Input::get('street_number');
            $estimate_model->city = Input::get('city');
            $estimate_model->zip = Input::get('zip');
            $estimate_model->district = Input::get('district');

            $estimate_model->stay_informed = Input::get('stay_informed');
            $estimate_model->save();

            return 1;
        } catch (Exception $ex) {
            return 0;
        }
    }
    
     public function saveProjectcontactform()
    {
        try {
        $ptype =Input::get('projectId');
        $pid=\Crypt::decrypt($ptype);
            $contact_model = new ContactForm();
            $contact_model->first_name = Input::get('first_name');
            $contact_model->last_name = Input::get('last_name');
            $contact_model->email = Input::get('email');
            $contact_model->phone = Input::get('phone');
            $contact_model->message = Input::get('message');
            $contact_model->project_id = $pid;
            $contact_model->stay_informed = Input::get('stay_informed');
            $contact_model->save();
            return 1;
        } catch (Exception $ex) {
            return 0;
        }
    }

}
