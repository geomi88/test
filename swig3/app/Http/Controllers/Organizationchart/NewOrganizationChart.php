<?php

namespace App\Http\Controllers\Organizationchart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use App\Models\Organizationchart;
use App\Models\Organizationnodes;
use DB;
use App;
use PDF;
use Excel;

class NewOrganizationChart extends Controller {

    public function index() {
        
        return view('organizationchart/organization_chart/index');
    }
    
    public function getchartlist(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $categories = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'CHART_CATEGORY', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
        
        $charts = DB::table('organization_chart')
                ->select('organization_chart.*','category.name as category')
                ->leftjoin('master_resources as category', 'organization_chart.category', '=', 'category.id')
                ->where('organization_chart.status', '=', 1)
                ->orderby('organization_chart.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbytype = Input::get('searchbytype');
            $searchbycategory = Input::get('searchbycategory');
            
            $sortordname = Input::get('sortordname');
            $sortordtype = Input::get('sortordtype');
            $sortordcategory = Input::get('sortordcategory');
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortordtype=='' && $sortordcategory==''){
                $sortOrdDefault='DESC';
            }

            $charts = DB::table('organization_chart')
                    ->select('organization_chart.*','category.name as category')
                    ->leftjoin('master_resources as category', 'organization_chart.category', '=', 'category.id')
                    ->where('organization_chart.status', '=', 1)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("organization_chart.name like '$searchbyname%'");
                    })
                    ->when($searchbytype, function ($query) use ($searchbytype) {
                        return $query->whereRaw("organization_chart.based_on='$searchbytype'");
                    })
                    ->when($searchbycategory, function ($query) use ($searchbycategory) {
                        return $query->whereRaw("organization_chart.category='$searchbycategory'");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('organization_chart.name', $sortordname);
                    })
                    ->when($sortordtype, function ($query) use ($sortordtype) {
                        return $query->orderby('organization_chart.based_on', $sortordtype);
                    })
                    ->when($sortordcategory, function ($query) use ($sortordcategory) {
                        return $query->orderby('master_resources.name', $sortordcategory);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('organization_chart.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('organizationchart/list_chart_new/result', array('charts' => $charts));
        }

        return view('organizationchart/list_chart_new/index', array('charts' => $charts,'categories'=>$categories));
    }
    
    public function editlist(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $categories = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'CHART_CATEGORY', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
        
        $charts = DB::table('organization_chart')
                ->select('organization_chart.*','category.name as category')
                ->leftjoin('master_resources as category', 'organization_chart.category', '=', 'category.id')
                ->where('organization_chart.status', '!=', 2)
                ->orderby('organization_chart.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbytype = Input::get('searchbytype');
            $searchbycategory = Input::get('searchbycategory');
            
            $sortordname = Input::get('sortordname');
            $sortordtype = Input::get('sortordtype');
            $sortordcategory = Input::get('sortordcategory');
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortordtype=='' && $sortordcategory==''){
                $sortOrdDefault='DESC';
            }

            $charts = DB::table('organization_chart')
                    ->select('organization_chart.*','category.name as category')
                    ->leftjoin('master_resources as category', 'organization_chart.category', '=', 'category.id')
                    ->where('organization_chart.status', '!=', 2)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("organization_chart.name like '$searchbyname%'");
                    })
                    ->when($searchbytype, function ($query) use ($searchbytype) {
                        return $query->whereRaw("organization_chart.based_on='$searchbytype'");
                    })
                    ->when($searchbycategory, function ($query) use ($searchbycategory) {
                        return $query->whereRaw("organization_chart.category='$searchbycategory'");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('organization_chart.name', $sortordname);
                    })
                    ->when($sortordtype, function ($query) use ($sortordtype) {
                        return $query->orderby('organization_chart.based_on', $sortordtype);
                    })
                    ->when($sortordcategory, function ($query) use ($sortordcategory) {
                        return $query->orderby('master_resources.name', $sortordcategory);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('organization_chart.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('organizationchart/edit_chart_new/result', array('charts' => $charts));
        }

        return view('organizationchart/edit_chart_new/index', array('charts' => $charts,'categories'=>$categories));
    }
    
    public function add() {
        
        $job_positions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        $categories = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'CHART_CATEGORY', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
        
        return view('organizationchart/organization_chart/create_new', array('job_positions'=>$job_positions,'categories'=>$categories));
    }
    
    public function addjobwise() {
        $charttype="Job_Position";
        $job_positions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        $categories = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'CHART_CATEGORY', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
        
        return view('organizationchart/organization_chart/create', array('job_positions'=>$job_positions,'categories'=>$categories,'charttype'=>$charttype));
    }

    public function store() {
        try {

            $arrMaster = Input::get('arrMaster');
            $arrMaster = json_decode($arrMaster);
            
            $arrTreeNode = Input::get('treeNodesJson');
            $arrTreeNodes = json_decode($arrTreeNode);
            
            $arrEmployee = Input::get('objEmployeesJson');
            $arrEmployees = json_decode($arrEmployee);
               
            $chartmodel = new Organizationchart();
             
            $chartmodel->name = $arrMaster->name;
            $chartmodel->alias_name = $arrMaster->alias_name;
            $chartmodel->category = $arrMaster->category;
            
            $chartmodel->based_on = 'Custom';
            

            $chartmodel->save();
            $lastid=$chartmodel->id;
            
            foreach ($arrTreeNodes as $node) {
                $arrEmp=array();
                $key=$node->id;
                
                if(isset($arrEmployees->$key)){
                    $arrEmp = array_map(function($e) {
                        return is_object($e) ? $e->id : $e['id'];
                    }, $arrEmployees->$key);
                   
                }
                $empids=implode(",", $arrEmp);
                
                $nodemodel = new Organizationnodes();
                
                $nodemodel->child_node  = $node->id;
                $nodemodel->parent_node = $node->parent;
                $nodemodel->employee_ids = $empids;
                $nodemodel->node_name = $node->name;
                $nodemodel->chart_id = $lastid;

                $nodemodel->save();
            }

            Toastr::success("Chart Saved Successfully!", $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function updatechart() {
        try {

            $chartid = Input::get('chartid');
            $arrMaster = Input::get('arrMaster');
            $arrMaster = json_decode($arrMaster);
            
            $arrTreeNode = Input::get('treeNodesJson');
            $arrTreeNodes = json_decode($arrTreeNode);
            
            $arrEmployee = Input::get('objEmployeesJson');
            $arrEmployees = json_decode($arrEmployee);
            
            $charts = DB::table('organization_chart')
                        ->where(['id' => $chartid])
                        ->update(['name' => $arrMaster->name,
                                'alias_name' => $arrMaster->alias_name,
                                'category' => $arrMaster->category,
                                ]);
             
            $delete = DB::table('oraganization_chart_nodes')
                        ->where('chart_id', '=', $chartid)
                        ->delete();
                    
            $lastid=$chartid;
            
            foreach ($arrTreeNodes as $node) {
                $arrEmp=array();
                $key=$node->id;
                if(isset($arrEmployees->$key)){
                    $arrEmp = array_map(function($e) {
                        return is_object($e) ? $e->id : $e['id'];
                    }, $arrEmployees->$key);
                }
                $empids=implode(",", $arrEmp);
                
                $nodemodel = new Organizationnodes();
                
                $nodemodel->child_node  = $node->id;
                $nodemodel->parent_node = $node->parent;
                $nodemodel->employee_ids = $empids;
                $nodemodel->node_name = $node->name;
                $nodemodel->chart_id = $lastid;

                $nodemodel->save();
            }

            Toastr::success("Chart Updated Successfully!", $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function viewchart($id) {
        $chartid = \Crypt::decrypt($id);
        
        $master_data = DB::table('organization_chart')
                        ->select('organization_chart.*','category.name as category')
                        ->leftjoin('master_resources as category', 'organization_chart.category', '=', 'category.id')
                        ->where(['organization_chart.id' => $chartid])
                        ->first();
        
        $treenodes = DB::table('oraganization_chart_nodes as node')
                        ->select('node.child_node as id','node.parent_node as parent',
                                'node.employee_ids as employee_ids','node_name as name')
                        ->whereRaw("node.chart_id=$master_data->id AND node.status=1")
                        ->get();
        
        $arrempid=$treenodes->pluck("employee_ids")->toArray();
        $strempids=implode(",", $arrempid);
        $arrempids=explode(",", $strempids);
       
        $employees = DB::table('employees')
                ->select('employees.id as id','employees.username','employees.first_name as first_name','employees.alias_name as alias_name','employees.job_position as job_position','employees.profilepic as profilepic','country.name as country_name','country.flag_32 AS flag_pic')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->whereIn('employees.id',$arrempids)
                ->where('status', '!=', 2)
                ->get();
        
        
        $public_path = url('/');
        $arrEmployeesById=array();
        foreach ($employees as $value) {
            
            $employee_name=$value->username." : ".$value->first_name." ".$value->alias_name;
            $pic_url=$value->profilepic;
            $flag_pic=$public_path.'/images/flags/'.$value->flag_pic;
            $country=$value->country_name;
            
            $arrEmployeesById[$value->id]=array('id'=>$value->id, 'name'=>$employee_name, 'pic'=>$pic_url,'flag'=>$flag_pic,'country'=>$country,'node'=>$value->job_position );
        }
        
        $arrEmployees=array();
        foreach ($treenodes as $node) {
            if($node->employee_ids){
                
                $arremps=explode(",", $node->employee_ids);
                $node->name=$node->name.' (<span>'.count($arremps).'</span>)';
                foreach ($arremps as $emp) {
                    if(key_exists($emp, $arrEmployeesById)){
                        $arrEmployees[$node->id][]=$arrEmployeesById[$emp];
                    }
                    
                }
            }
        }
        
        $arrEmployees=json_encode($arrEmployees);
        
        
        return view('organizationchart/list_chart_new/view', array('master_data'=>$master_data,'treenodes' => $treenodes,'arrEmployees'=>$arrEmployees));
    }
    
    public function edit($id) {
        $chartid = \Crypt::decrypt($id);
        
        $job_positions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        $categories = DB::table('master_resources')
             ->select('master_resources.*')
             ->where(['resource_type' => 'CHART_CATEGORY', 'status' => 1])
             ->orderby('name', 'ASC')
             ->get();
        
        $master_data = DB::table('organization_chart')
                        ->select('organization_chart.*','category.name as categoryname')
                        ->leftjoin('master_resources as category', 'organization_chart.category', '=', 'category.id')
                        ->where(['organization_chart.id' => $chartid])
                        ->first();
        
        $treenodes = DB::table('oraganization_chart_nodes as node')
                        ->select('node.child_node as id','node.parent_node as parent',
                                'node.employee_ids as employee_ids','node_name as name')
                        ->whereRaw("node.chart_id=$master_data->id AND node.status=1")
                        ->get();
        
        $arrSelectdPosition=$treenodes->pluck("id");
        
        $arrempid=$treenodes->pluck("employee_ids")->toArray();
        $strempids=implode(",", $arrempid);
        $arrempids=explode(",", $strempids);
        
        $employees = DB::table('employees')
                ->select('employees.id as id','employees.username','employees.first_name as first_name','employees.alias_name as alias_name','employees.job_position as job_position','employees.profilepic as profilepic','country.name as country_name','country.flag_32 AS flag_pic')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->whereIn('employees.id',$arrempids)
                ->where('status', '!=', 2)
                ->get();
        
        $public_path = url('/');
        $arrEmployeesById=array();
        foreach ($employees as $value) {
            
            $employee_name=$value->username." : ".$value->first_name." ".$value->alias_name;
            $pic_url=$value->profilepic;
            $flag_pic=$public_path.'/images/flags/'.$value->flag_pic;
            $country=$value->country_name;
            
            $arrEmployeesById[$value->id]=array('id'=>$value->id, 'name'=>$employee_name, 'pic'=>$pic_url,'flag'=>$flag_pic,'country'=>$country,'node'=>$value->job_position,'jobid'=>$value->job_position);
        }
        
        $arrEmployees=array();
        foreach ($treenodes as $node) {
            if($node->employee_ids){
                
                $arremps=explode(",", $node->employee_ids);
                
                foreach ($arremps as $emp) {
                    if(key_exists($emp, $arrEmployeesById)){
                        $arrEmployees[$node->id][]=$arrEmployeesById[$emp];
                    }
                    
                }
                
            }
        }
       
        $arrEmployees=json_encode($arrEmployees);
                
        return view('organizationchart/edit_chart_new/edit', array('master_data'=>$master_data,'treenodes' => $treenodes,'arrEmployees'=>$arrEmployees,'arrSelectdPosition'=>$arrSelectdPosition,'job_positions'=>$job_positions,'categories'=>$categories));
    }
    
    public function getemployeesbyjob() {
        $job_position = Input::get('jobposition');
        $blnrootnode = Input::get('blnrootnode');
        $arrEmps = Input::get('arrEmps');
        $arrEmployees=json_decode($arrEmps);
        
        $arrEmp=array();
        if($arrEmployees){
            $arrEmp = array_map(function($e) {
                return is_object($e) ? $e->id : $e['id'];
            }, $arrEmployees);

        }
       
        $employees = DB::table('employees')
                ->select('employees.id','employees.username','employees.first_name','employees.alias_name','employees.profilepic',
                        'employees.job_position','country.name as country_name','country.flag_32 AS flag_pic')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.job_position', '=', $job_position)
                ->where('status', '!=', 2)
                ->get();
       
        $strHtml='';
        $public_path = url('/');
        $clsrootchkboxes="";
        if($blnrootnode==1){
            $clsrootchkboxes="clschkroot";
        }
        foreach ($employees as $value) {
            
            $employee_name=$value->username." : ".$value->first_name." ".$value->alias_name;
            $pic_url=$value->profilepic;
            $flag_pic=$value->flag_pic;
            $country=$value->country_name;
            $job=$value->job_position;
            
            $checked="";
            $disabled="";
            if(in_array($value->id, $arrEmp)){
                $checked="checked";
                $disabled="";
            }
            $strHtml.='<div class="empList filterempcode" attrsearch="'.$value->username.'">
                    <div class="commonCheckHolder checkboxRender">
                        <label>
                            <input name="gender" id="'.$value->id.'" type="checkbox" class="clschkempbyjob '.$clsrootchkboxes.'" attrName="'.$employee_name.'" attrPic="' . $pic_url . '" attrFlag="' . $public_path . '/images/flags/' . $flag_pic . '" attrCountry="'.$country.'" attrJobPos='.$job.' '.$disabled.' '.$checked.' >
                            <span></span>
                        </label>
                    </div>
                    <figure class="imgHolder">
                        <img crossOrigin="Anonymous" src="' . $pic_url . '" alt="Profile">
                    </figure>
                    <div class="details">
                        <b>'.$employee_name.'</b>
                        <figure class="flagHolder">
                            <img src="' . $public_path . '/images/flags/' . $flag_pic . '" alt="Flag">
                            <figcaption>'.$country.'</figcaption>
                        </figure>
                    </div>
                    <div class="customClear"></div>
                </div>';
        }
        
        
        if (count($employees) > 0) {
            echo $strHtml;
        } else {
            return -1;
        }
    }
    
    public function getprofiledetails() {
        $id =Input::get('emp_id') ;
         
       
         $profile_details = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name','country.flag_32 AS flag_pic','country.name AS flag_code','top_managers.first_name AS topmanager_first_name','top_managers.middle_name AS topmanager_middle_name','top_managers.last_name AS topmanager_last_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('employees as top_managers', 'top_managers.id', '=', 'employees.top_manager_id')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
//                ->where('employees.status', '!=', 2)
                ->where('employees.id', '=', $id)
                ->first();
      
       $flagUrl="../../../images/flags/".$profile_details->flag_pic;
                                    
         echo '<div class="innerContent"><div class="allListHolder">
                        <div class="profileWidget">
                            <figure class="proPic" style="background-image: url('.$profile_details->profilepic.')" alt="Profile">
                                <figcaption>'.$profile_details->username.'</figcaption>
                            </figure>
                            <aside>
                                <h3>'.$profile_details->first_name.' '.$profile_details->last_name.'<span>'.str_replace("_"," ",$profile_details->job_position_name).'</span></h3>
                                <ul class="quickInfo">
                                    <li class="phone"><a>'.$profile_details->mobile_number.'</a></li>
                                    <li class="email"><a>'.$profile_details->email.'</a></li>
                                </ul>
                                <div class="country">
                                    
                                    <figure style="background-image: url('.$flagUrl.')" alt="Flag"></figure>
                                    <span>'.$profile_details->country_name.'</span>
                                </div>
                            </aside>
                        </div>
                        <div class="profileDtlView">
                            <div class="toggleOptn selected">
                                <h5><em></em>Professional Details</h5>
                                <div class="toggleOptnDtl">
                                    <ul>
                                        <li><b>Top Manager</b> : '.$profile_details->topmanager_first_name.' '.$profile_details->topmanager_middle_name.'</li>
                                        <li><b>Division</b> : '.$profile_details->division_name.'</li>
                                      
                                    </ul>
                                </div>
                            </div>

                            <div class="toggleOptn selected">
                                <h5><em></em>Personal Details</h5>
                                <div class="toggleOptnDtl">
                                    <ul>
                                          <li><b>DOB</b> : '.$profile_details->dob.'</li>
                                          <li><b>Passport No</b> : '.$profile_details->passport_number.'</li>
                                       <li><b>Residence Id</b>: '.$profile_details->residence_id_number.'</li>
                                        <li><b>Address</b> : '.$profile_details->current_address.'</li>
                                         
                                    </ul>
                                </div>
                            </div>
                               <div class="toggleOptn selected">
                                <h5><em></em>Job Description Agreement</h5>
                                <div class="toggleOptnDtl">
                                    <ul>
                                          <li> <textarea id="job_description" readonly style="width:500px;height:500px;">'.strip_tags($profile_details->job_description).'</textarea></li>
                                        
                                    </ul>
                                </div>
                            </div>        
                        </div>
                    </div></div>';
   
      }
      
    public function getprofiledetailscreate() {
        $id =Input::get('emp_id') ;
         
       
         $profile_details = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', 'division.id as division_id', 'division.name as division_name', 'country.name as country_name','country.flag_32 AS flag_pic','country.name AS flag_code','top_managers.first_name AS topmanager_first_name','top_managers.middle_name AS topmanager_middle_name','top_managers.last_name AS topmanager_last_name')
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->leftjoin('master_resources as division', 'division.id', '=', 'employees.division')
                ->leftjoin('employees as top_managers', 'top_managers.id', '=', 'employees.top_manager_id')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
//                ->where('employees.status', '!=', 2)
                ->where('employees.id', '=', $id)
                ->first();
      
       $flagUrl="../../images/flags/".$profile_details->flag_pic;
                                    
         echo '<div class="innerContent"><div class="allListHolder">
                        <div class="profileWidget">
                            <figure class="proPic" style="background-image: url('.$profile_details->profilepic.')" alt="Profile">
                                <figcaption>'.$profile_details->username.'</figcaption>
                            </figure>
                            <aside>
                                <h3>'.$profile_details->first_name.' '.$profile_details->last_name.'<span>'.str_replace("_"," ",$profile_details->job_position_name).'</span></h3>
                                <ul class="quickInfo">
                                    <li class="phone"><a>'.$profile_details->mobile_number.'</a></li>
                                    <li class="email"><a>'.$profile_details->email.'</a></li>
                                </ul>
                                <div class="country">
                                    
                                    <figure style="background-image: url('.$flagUrl.')" alt="Flag"></figure>
                                    <span>'.$profile_details->country_name.'</span>
                                </div>
                            </aside>
                        </div>
                        <div class="profileDtlView">
                            <div class="toggleOptn selected">
                                <h5><em></em>Professional Details</h5>
                                <div class="toggleOptnDtl">
                                    <ul>
                                        <li><b>Top Manager</b> : '.$profile_details->topmanager_first_name.' '.$profile_details->topmanager_middle_name.'</li>
                                        <li><b>Division</b> : '.$profile_details->division_name.'</li>
                                      
                                    </ul>
                                </div>
                            </div>

                            <div class="toggleOptn selected">
                                <h5><em></em>Personal Details</h5>
                                <div class="toggleOptnDtl">
                                    <ul>
                                          <li><b>DOB</b> : '.$profile_details->dob.'</li>
                                          <li><b>Passport No</b> : '.$profile_details->passport_number.'</li>
                                       <li><b>Residence Id</b>: '.$profile_details->residence_id_number.'</li>
                                        <li><b>Address</b> : '.$profile_details->current_address.'</li>
                                         
                                    </ul>
                                </div>
                            </div>
                               <div class="toggleOptn selected">
                                <h5><em></em>Job Description Agreement</h5>
                                <div class="toggleOptnDtl">
                                    <ul>
                                          <li> <textarea id="job_description" readonly style="width:500px;height:500px;">'.strip_tags($profile_details->job_description).'</textarea></li>
                                        
                                    </ul>
                                </div>
                            </div>        
                        </div>
                    </div></div>';
   
      }
      
    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('organization_chart')
                            ->where(['id' => $dn])
                            ->update(['status' => 2]);

            
            Toastr::success('Chart Deleted Successfully', $title = null, $options = []);
            return Redirect::to('organizationchart/organizationchartnew/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('organizationchart/organizationchartnew/editlist');
        }
    }
    
    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('organization_chart')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);

                        
            Toastr::success('Chart Disabled Successfully', $title = null, $options = []);
            return Redirect::to('organizationchart/organizationchartnew/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('organizationchart/organizationchartnew/editlist');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('organization_chart')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);

            
            Toastr::success('Chart Enabled Successfully ', $title = null, $options = []);
            return Redirect::to('organizationchart/organizationchartnew/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('organizationchart/organizationchartnew/editlist');
        }
    }
}