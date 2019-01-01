<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Agent;
use App\Services\Common;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Kamaln7\Toastr\Facades\Toastr;
use Mail;

class AdminAgentController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin_auth');
        //write athenitcate function for admin
    }
    /**
     * add new aganet view
     *
     * @param  null
     * @return View
     */

    public function get_list_agents(Request $request)
    {
        $paginate = '10';
        $agentList = DB::table('agents')
            ->select('agents.*', 'municipalities.name as muncname')
            ->leftjoin('municipalities', 'municipalities.id', '=', 'agents.municipality_id')
        // ->leftjoin('districts','districts.id','=','properties.district_id')
            ->where('agents.status', '=', '1')
            ->paginate($paginate);

        $common = new Common();

        $municipalities = $common->municipalities();

        $pageData['agentList'] = $agentList;
        $pageData['municipalities'] = $municipalities;

        if ($request->ajax()) {

            $municipality = Input::get('municipality');
            $searchKey = Input::get('searchKey');
            $user_type = Input::get('user_type');
            $agentList = DB::table('agents')
                ->select('agents.*', 'municipalities.name as muncname')
                ->leftjoin('municipalities', 'municipalities.id', '=', 'agents.municipality_id')
                ->where('agents.status', '=', '1')

                ->when($user_type, function ($query) use ($user_type) {
                    return $query->whereRaw("(agents.user_type=$user_type)");
                })
                ->when($municipality, function ($query) use ($municipality) {
                    return $query->whereRaw("(agents.municipality_id=$municipality)");
                })
                ->when($searchKey, function ($query) use ($searchKey) {
                    return $query->whereRaw("(agents.name like '%$searchKey%' OR agents.email like '%$searchKey%')");
                })
                ->paginate($paginate);

            $pageData['agentList'] = $agentList;

            return view('admin.agent.agent-list-result', array('pageData' => $pageData));
        }

        return view('admin.agent.agent-listing', array('pageData' => $pageData));
    }

    public function add_agent()
    {
        try {

            $common = new Common();
            $municipalities = $common->municipalities();
            $districts = $common->districts();

            $pageData['municipalities'] = $municipalities;
            $pageData['districts'] = $districts;

            return view('admin.agent.add-agent', array('pageData' => $pageData));

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function save_agent(Request $request)
    {
        try {
            $input = Input::all();

            $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
            $pass = array();
            $alphaLength = strlen($alphabet) - 1;
            for ($i = 0; $i < 8; $i++) {
                $n = rand(0, $alphaLength);
                $pass[] = $alphabet[$n];
            }
            $newpassword = implode($pass);

            $objmodel = new Agent();
            $objmodel->user_type = Input::get('user_type');
            $objmodel->municipality_id = Input::get('municipality');
            $objmodel->name = Input::get('name');
            $objmodel->email = Input::get('email');
            $objmodel->district_id = Input::get('district');
            $objmodel->password = Hash::make($newpassword);
            $objmodel->address = Input::get('address');
            $objmodel->mobile_number = Input::get('phone_mob');
            $objmodel->office_phone = Input::get('phone_offc');
            $objmodel->member_since = date('Y-m-d', strtotime(Input::get('member_since')));
            $objmodel->user_type = Input::get('user_type'); //agent
            $objmodel->description = Input::get('description');
            $objmodel->status = '1';

            if ($request->hasFile('preview_upload')) {
                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $propertyplan = "agent" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/agent';
                $file->move($path, $propertyplan);
                $objmodel->image = $propertyplan;
            }
            $save = $objmodel->save();
            $name = Input::get('name');
            $email = Input::get('email');

            if ($save) {
                if (Input::get('user_type') == 1) {
                    $userEmail = Mail::send('emailtemplates.newagent', ['name' => $name, 'email' => $email, 'newpassword' => $newpassword], function ($message) use ($email, $name, $newpassword) {
                        $message->to($email, $name)->subject(env('APP_NAME') . ' | Welcome to LuxEstate');
                    });
                }
                if (Input::get('user_type') == 2) {
                    $userEmail = Mail::send('emailtemplates.newowner', ['name' => $name, 'email' => $email, 'newpassword' => $newpassword], function ($message) use ($email, $name, $newpassword) {
                        $message->to($email, $name)->subject(env('APP_NAME') . ' | Welcome to LuxEstate');
                    });
                }

            }

            Toastr::success('Successfully Added New Agent!', $title = null, $options = []);
            return Redirect::to('admin/agent-listing');

        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/agent-listing');
        }
    }

    public function view_agent($p_id)
    {
        try {

            $render_params = array();
            $render_params['agentDetails'] = "";

            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);

                $agentDetails = DB::table('agents')
                    ->select('agents.*', 'municipalities.name as municipality', 'districts.name as districtname')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'agents.municipality_id')
                    ->leftjoin('districts', 'districts.id', '=', 'agents.district_id')
                    ->where('agents.status', '=', '1')
                    ->where('agents.user_type', '=', '1')
                    ->where('agents.id', '=', $p_id)
                    ->first();

                $buildingDetails = DB::table('building_type')
                    ->select('building_type.*')
                    ->where('building_type.status', '=', '1')
                    ->get();
                $buildingCount = array();
                foreach ($buildingDetails as $buildings) {
                    $prpertyCount = DB::table('properties')
                        ->select(DB::raw("count('properties.id') as propertyCount"))
                        ->where('properties.status', '=', '1')
                        ->where('properties.building_type', '=', $buildings->id)
                        ->where('properties.agent_id', '=', $p_id)
                        ->first();
                    $buildingCount[$buildings->name] = $prpertyCount->propertyCount;
                }

                $render_params['agentDetails'] = $agentDetails;
                $render_params['buildingCount'] = $buildingCount;

            }
            return view('admin.agent.agent-detail-view', array('render_params' => $render_params));

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    public function edit_agent($p_id)
    {
        try {

            $pageData = array();

            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);

                $common = new Common();
                $municipalities = $common->municipalities();
                $districts = $common->districts();

                $agentDetails = DB::table('agents')
                    ->select('agents.*', 'municipalities.name as municipality', 'districts.name as districtname')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'agents.municipality_id')
                    ->leftjoin('districts', 'districts.id', '=', 'agents.district_id')
                    ->where('agents.status', '=', '1')
                    ->where('agents.user_type', '=', '1')
                    ->where('agents.id', '=', $p_id)
                    ->first();

                $pageData['agentDetails'] = $agentDetails;
                $pageData['municipalities'] = $municipalities;
                $pageData['districts'] = $districts;

            }
            return view('admin.agent.edit-agent', array('pageData' => $pageData));

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    public function update_agent(Request $request)
    {
        try {

            $input = Input::all();

            $agentId = \Crypt::decrypt($input['agentId']);

            $objmodel = new Agent();
            $objmodel->exists = true;
            $objmodel->id = $agentId;
            $objmodel->municipality_id = Input::get('municipality');
            $objmodel->name = Input::get('name');
            $objmodel->email = Input::get('email');
            $objmodel->district_id = Input::get('district');
            $objmodel->address = Input::get('address');
            $objmodel->mobile_number = Input::get('phone_mob');
            $objmodel->office_phone = Input::get('phone_offc');
            $objmodel->member_since = date('Y-m-d', strtotime(Input::get('member_since')));
            $objmodel->user_type = '1'; //agent
            $objmodel->description = Input::get('description');
            $objmodel->status = '1';

            if ($request->hasFile('preview_upload')) {
                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $propertyplan = "agent" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/agent';
                $file->move($path, $propertyplan);
                $objmodel->image = $propertyplan;
            }
            $save = $objmodel->save();

            Toastr::success('Updated User Details Successfully', $title = null, $options = []);
            return Redirect::to('admin/agent-listing');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/agent-listing');

        }

    }

    public function remove_agent($agentId)
    {
        try {

            $agent_id = \Crypt::decrypt($agentId);
            if ($agent_id != "") {
                $objmodel = new Agent();
                $objmodel->exists = true;
                $objmodel->id = $agent_id;
                $objmodel->status = '0';
                $objmodel->save();
            }

            Toastr::success('Removed Agent Successfully', $title = null, $options = []);
            return Redirect::to('admin/agent-listing');

        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/agent-listing');
        }

    }

    public function checkExist(Request $request)
    {
      try {
            
            $email = Input::get('email');
            
            $agentDetails = DB::table('agents')
                    ->select('agents.*')
                    ->where('agents.email', '=', $email)
                    ->first();        
              if($agentDetails==null) {
                  return 0;
              } else{
                  return 1;
              }    
        } catch (Exception $ex) {
            echo $ex->getMessage();
            
        }
}
}
