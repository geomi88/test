<?php

namespace App\Http\Controllers\Checklist;

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
use App\Models\Company;
use App\Models\Checklist_entry;
use DB;
use App;
use PDF;
use Excel;
use Exception;

class ChecklistentryController extends Controller {

    public function index() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $job_position = DB::table('employees')
                    ->select('employees.job_position as job_position')
                    ->whereRaw("employees.id=$login_id")
                    ->first();
        
        $weekday=date('w')+1;
        $checkcategories = DB::table('check_list')
                    ->select('check_list.category_id','category.name as categoryname','category.alias_name as alias_name')
                    ->leftjoin('master_resources as category', 'check_list.category_id', '=', 'category.id')
                    ->where('check_list.status', '!=', 2)
                    ->whereRaw("check_list.job_position=$job_position->job_position")
                    ->whereRaw("(check_list.daystring like '%$weekday%' OR check_list.all_day=1)")
                    ->groupby('check_list.category_id')
                    ->orderby('category.name', 'ASC')
                    ->get();
        
        return view('checklist/checklist_entry/index', array('checkcategories' => $checkcategories));
    }
    
    public function graphindex() {
        
        $region_id="";
        
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');
        
        $ratings = DB::table('checklist_entry')
                ->select(DB::raw('count(*) as catcount,rating'))
                ->whereraw("date(checklist_entry.entry_date)>='$first_day' AND date(checklist_entry.entry_date)<='$last_day' and checklist_entry.status=1")
                ->groupby('rating')
                ->get();
     
        $goodCount=0;
        $avgCount=0;
        $badCount=0;
        
        if(count($ratings)>0){
            foreach ($ratings as $rating) {
                if($rating->rating=="Good"){
                    $goodCount=$rating->catcount;
                }

                if($rating->rating=="Average"){
                    $avgCount=$rating->catcount;
                }

                if($rating->rating=="Bad"){
                    $badCount=$rating->catcount;
                }
            }
        }
       
        $totalcount=$goodCount+$avgCount+$badCount;
        
        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id','region_details.name as region_name','region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
         
        $public_path= url('/');
                
        $arrcount = array();
        
        $arrcount[]=array('y'=>$goodCount,'color'=>"green",'url'=>$public_path.'/warningwithcategory/'.\Crypt::encrypt("Good"));
        $arrcount[]=array('y'=>$avgCount,'color'=>"yellow",'url'=>$public_path.'/warningwithcategory/'.\Crypt::encrypt("Average"));
        $arrcount[]=array('y'=>$badCount,'color'=>"red",'url'=>$public_path.'/warningwithcategory/'.\Crypt::encrypt("Bad"));
        
        $arrcount = array_values($arrcount);
        $arrcount = json_encode($arrcount);
        
        return view('checklist/rating_graph/index', array(
            'arrcount'=>$arrcount,'totalcount'=>$totalcount,
            'periodStartDate'=>date('01-m-Y'),'periodEndDate'=>date('t-m-Y'),
            'regions'=>$regions,'region_id'=>$region_id));
    }
    
    public function getratinggraph() {
        
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        
        $period_start_date = $first_day;
        $period_end_date = $last_day;
        
        $region_id=Input::get('region_id');
        
        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }
        
       $ratings = DB::table('checklist_entry')
                ->select(DB::raw('count(*) as catcount,rating'))
                ->leftjoin('master_resources as branch', 'checklist_entry.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                 }) 
                ->whereraw("date(checklist_entry.entry_date)>='$first_day' AND date(checklist_entry.entry_date)<='$last_day' and checklist_entry.status=1")
                ->groupby('rating')
                ->get();
        
        $goodCount=0;
        $avgCount=0;
        $badCount=0;
        
        if(count($ratings)>0){
            
            foreach ($ratings as $rating) {
                if($rating->rating=="Good"){
                    $goodCount=$rating->catcount;
                }

                if($rating->rating=="Average"){
                    $avgCount=$rating->catcount;
                }

                if($rating->rating=="Bad"){
                    $badCount=$rating->catcount;
                }
            }
            
        }
       
        $totalcount=$goodCount+$avgCount+$badCount;
        
        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id','region_details.name as region_name','region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
         
        $public_path= url('/');
                
        $arrcount = array();
        
        $arrcount[]=array('y'=>$goodCount,'color'=>"green",'url'=>$public_path.'/checklistwithrating/'.\Crypt::encrypt("Good"));
        $arrcount[]=array('y'=>$avgCount,'color'=>"yellow",'url'=>$public_path.'/checklistwithrating/'.\Crypt::encrypt("Average"));
        $arrcount[]=array('y'=>$badCount,'color'=>"red",'url'=>$public_path.'/checklistwithrating/'.\Crypt::encrypt("Bad"));
        
        $arrcount = array_values($arrcount);
        $arrcount = json_encode($arrcount);
        
        return view('checklist/rating_graph/index', array(
            'arrcount'=>$arrcount,'totalcount'=>$totalcount,
            'periodStartDate'=>$period_start_date,'periodEndDate'=>$period_end_date,
            'regions'=>$regions,'region_id'=>$region_id));
    }
    
    public function getcheckpoints($id) {
        try {

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            $weekday = date('w') + 1;
            $main_category = \Crypt::decrypt($id);
            $job_position = DB::table('employees')
                    ->select('employees.job_position as job_position','p.name as designation')
                    ->leftjoin('master_resources as p', 'employees.job_position', '=', 'p.id')
                    ->whereRaw("employees.id=$login_id")
                    ->first();
            
            $allbranches=array();
            $allbranches = DB::table('resource_allocation')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->leftjoin('master_resources', 'resource_allocation.branch_id', '=', 'master_resources.id')
                ->whereRaw("resource_allocation.employee_id=$login_id and active=1")
                ->get();

            if (count($allbranches) == 0) {
                throw new Exception("No_allocation");
            }
            
//            For future reference
            
//            if($job_position->designation=='Supervisor' || $job_position->designation=='Cashier'){
                
//            }else{
//                $allbranches = DB::table('master_resources')
//                    ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
//                    ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
//                    ->get();
//            }

            $checkpoints = DB::table('check_list')
                    ->select('check_list.*', 'category.name as categoryname')
                    ->leftjoin('master_resources as category', 'check_list.category_id', '=', 'category.id')
                    ->where('check_list.status', '!=', 2)
                    ->whereRaw("check_list.category_id=$main_category AND check_list.job_position=$job_position->job_position")
                    ->whereRaw("(check_list.daystring like '%$weekday%' OR check_list.all_day=1)")
                    ->orderby('check_list.checkpoint', 'ASC')
                    ->get();

            return view('checklist/checklist_entry/check_points', array('checkpoints' => $checkpoints, 'allbranches' => $allbranches, 'main_category' => $main_category, 'arrentry' => array()));
            
        } catch (\Exception $e) {

            if ($e->getMessage() == "No_allocation") {
                Toastr::error('No Branches Allocated!', $title = null, $options = []);
                return Redirect::to('checklist/checklist_entry');
            }

            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('checklist/checklist_entry');
        }
    }

    public function getbranchentry() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $weekday=date('w')+1;
        $curdate=date('Y-m-d');
        $main_category = Input::get('maincategory');
        $branch_id = Input::get('branch_id');
        
        $job_position = DB::table('employees')
                    ->select('employees.job_position as job_position')
                    ->whereRaw("employees.id=$login_id")
                    ->first();
                
        $checkpoints = DB::table('check_list')
                    ->select('check_list.*','category.name as categoryname')
                    ->leftjoin('master_resources as category', 'check_list.category_id', '=', 'category.id')
                    ->where('check_list.status', '!=', 2)
                    ->whereRaw("check_list.category_id=$main_category AND check_list.job_position=$job_position->job_position")
                    ->whereRaw("(check_list.daystring like '%$weekday%' OR check_list.all_day=1)")
                    ->orderby('check_list.checkpoint', 'ASC')
                    ->get();
        
        $checkentry=array();
        if($branch_id!=''){
            $checkentry = DB::table('checklist_entry')
                    ->select('checklist_entry.*')
                    ->leftjoin('check_list', 'checklist_entry.checkpoint_id', '=', 'check_list.id')
                    ->where('checklist_entry.status', '=', 1)
                    ->whereRaw("checklist_entry.employee_id=$login_id AND check_list.job_position=$job_position->job_position AND checklist_entry.branch_id=$branch_id AND date(entry_date)='$curdate' AND check_list.category_id=$main_category")
                    ->get();
        }
        
        $arrentry=array();
        if(count($checkentry)>0){
            foreach ($checkentry as $value) {
                $arrentry[$value->checkpoint_id]=array("rating"=>$value->rating,"comments"=>$value->comments);
            }
        }
        
        return view('checklist/checklist_entry/result', array('checkpoints' => $checkpoints,'main_category'=>$main_category,'arrentry'=>$arrentry));
    }
    
    public function store() {
        try {

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $curdate=date('Y-m-d');
            $cmbbranch = Input::get('cmbbranch');
            $main_category = Input::get('maincategory');
            $arrPointList = Input::get('arrPointList');
            $arrPointList = json_decode($arrPointList);
            
            $job_position = DB::table('employees')
                    ->select('employees.job_position as job_position')
                    ->whereRaw("employees.id=$login_id")
                    ->first();
             
            $checkentry = DB::table('checklist_entry')
                    ->select('checklist_entry.*')
                    ->leftjoin('check_list', 'checklist_entry.checkpoint_id', '=', 'check_list.id')
                    ->where('checklist_entry.status', '=', 1)
                    ->whereRaw("checklist_entry.employee_id=$login_id AND check_list.job_position=$job_position->job_position AND checklist_entry.branch_id=$cmbbranch AND date(entry_date)='$curdate' AND check_list.category_id=$main_category")
                    ->get();
            
            
            if(count($checkentry)>0){
                $entryids=$checkentry->pluck("id")->toArray();
                $categories = DB::table('checklist_entry')
                    ->whereIn("checklist_entry.id",$entryids)
                    ->update(['status' => 0]);
            }
            
            foreach ($arrPointList as $query) {

                $checkmodel = new Checklist_entry();
                
                $checkmodel->checkpoint_id = $query->checkpoint;
                $checkmodel->entry_date = date('Y-m-d');
                $checkmodel->employee_id = $login_id;
                $checkmodel->branch_id = $cmbbranch;
                $checkmodel->rating = $query->rating;
                $checkmodel->comments = $query->comment;

                $checkmodel->save();
            }

            Toastr::success("Check List Entry Saved Successfully!", $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }


}
