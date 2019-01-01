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

class OrganizationchartController extends Controller {

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

            return view('organizationchart/list_chart/result', array('charts' => $charts));
        }

        return view('organizationchart/list_chart/index', array('charts' => $charts,'categories'=>$categories));
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

            return view('organizationchart/edit_chart/result', array('charts' => $charts));
        }

        return view('organizationchart/edit_chart/index', array('charts' => $charts,'categories'=>$categories));
    }
    
    public function add() {
        $charttype="employees";
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
            if ($arrMaster->based_on == "employees") {
                $chartmodel->based_on = 'employees';
            } else {
                $chartmodel->based_on = 'Job_Position';
            }

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
                                'node.employee_ids as employee_ids',db::raw('REPLACE(job.name, "_", " ") as name'))
                        ->leftjoin('master_resources as job', 'node.child_node', '=', 'job.id')
                        ->whereRaw("node.chart_id=$master_data->id AND node.status=1")
                        ->get();
        
        $arrempid=$treenodes->pluck("employee_ids")->toArray();
        $strempids=implode(",", $arrempid);
        $arrempids=explode(",", $strempids);
        
        $employees = DB::table('employees')
                ->select('employees.id as id','employees.first_name as first_name','employees.alias_name as alias_name','employees.job_position as job_position','employees.profilepic as profilepic','country.name as country_name','country.flag_32 AS flag_pic')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->whereIn('employees.id',$arrempids)
                ->where('status', '!=', 2)
                ->get();
        
        $public_path = url('/');
        $arrEmployees=array();
        foreach ($employees as $value) {
            
            $employee_name=$value->first_name." ".$value->alias_name;
            $pic_url=$value->profilepic;
            $flag_pic=$public_path.'/images/flags/'.$value->flag_pic;
            $country=$value->country_name;
            
            $arrEmployees[$value->job_position][]=array('id'=>$value->id, 'name'=>$employee_name, 'pic'=>$pic_url,'flag'=>$flag_pic,'country'=>$country,'node'=>$value->job_position );
        }
        
        $arrEmployees=json_encode($arrEmployees);
        
        
        return view('organizationchart/list_chart/view', array('master_data'=>$master_data,'treenodes' => $treenodes,'arrEmployees'=>$arrEmployees));
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
                                'node.employee_ids as employee_ids',db::raw('REPLACE(job.name, "_", " ") as name'))
                        ->leftjoin('master_resources as job', 'node.child_node', '=', 'job.id')
                        ->whereRaw("node.chart_id=$master_data->id AND node.status=1")
                        ->get();
        
        $arrSelectdPosition=$treenodes->pluck("id");
        
        $arrempid=$treenodes->pluck("employee_ids")->toArray();
        $strempids=implode(",", $arrempid);
        $arrempids=explode(",", $strempids);
        
        $employees = DB::table('employees')
                ->select('employees.id as id','employees.first_name as first_name','employees.alias_name as alias_name','employees.job_position as job_position','employees.profilepic as profilepic','country.name as country_name','country.flag_32 AS flag_pic')
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->whereIn('employees.id',$arrempids)
                ->where('status', '!=', 2)
                ->get();
        
        $public_path = url('/');
        $arrEmployees=array();
        foreach ($employees as $value) {
            
            $employee_name=$value->first_name." ".$value->alias_name;
            $pic_url=$value->profilepic;
            $flag_pic=$public_path.'/images/flags/'.$value->flag_pic;
            $country=$value->country_name;
            
            $arrEmployees[$value->job_position][]=array('id'=>$value->id, 'name'=>$employee_name, 'pic'=>$pic_url,'flag'=>$flag_pic,'country'=>$country,'node'=>$value->job_position );
        }
        
        $arrEmployees=json_encode($arrEmployees);
                
        return view('organizationchart/edit_chart/edit', array('master_data'=>$master_data,'treenodes' => $treenodes,'arrEmployees'=>$arrEmployees,'arrSelectdPosition'=>$arrSelectdPosition,'job_positions'=>$job_positions,'categories'=>$categories));
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
                ->select('employees.*','country.name as country_name','country.flag_32 AS flag_pic')
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
            
            $employee_name=$value->first_name." ".$value->alias_name;
            $pic_url=$value->profilepic;
            $flag_pic=$value->flag_pic;
            $country=$value->country_name;
            
            $checked="";
            $disabled="";
            if(in_array($value->id, $arrEmp)){
                $checked="checked";
                $disabled="disabled='disabled'";
            }
            $strHtml.='<div class="empList">
                    <div class="commonCheckHolder checkboxRender">
                        <label>
                            <input name="gender" id="'.$value->id.'" type="checkbox" class="clschkempbyjob '.$clsrootchkboxes.'" attrName="'.$employee_name.'" attrPic="' . $pic_url . '" attrFlag="' . $public_path . '/images/flags/' . $flag_pic . '" attrCountry="'.$country.'" '.$disabled.' '.$checked.' >
                            <span></span>
                        </label>
                    </div>
                    <figure class="imgHolder">
                        <img src="' . $pic_url . '" alt="Profile">
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
}