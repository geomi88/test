<?php

namespace App\Http\Controllers\Finance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Usermodule;
use DB;
use Customhelper;


class IndexController extends Controller {

    public function index() {
        $employee_details = Employee::where('id', Session::get('login_id'))->first();
        
        $loggedin_details = DB::table('employees')
                ->select('employees.*','master_resources.name as job_position')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('employees.id', '=', Session::get('login_id'))
                ->first();
        $user_sub_modules = Usermodule::where('employee_id', Session::get('login_id'))->join('modules', 'modules.id', '=', 'user_modules.module_id')->whereRaw("modules.parent_id in (select id from modules where name='Finance')")->get();
        
        return view('finance/index', array('employee_details' => $employee_details,'loggedin_details' => $loggedin_details, 'user_sub_modules' => $user_sub_modules));
         
    }
    
    public function vatreport() {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
          $region_id="";
        
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');
        
        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,sum(pos_sales.tax_in_mis) as tax_in_mis'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->orderby('total_sale','DESC')
                ->first();
       
           $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id','region_details.name as region_name','region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
         
          // All employees
        $totalsupervisors = DB::table('employees')
              ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
              ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
              ->count();
        
        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.tax_in_mis),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');
             
        $actualsale=  Customhelper::numberformatter($actualsale);
        if($sales->total_sale==""){
            $sales->total_sale=0;
        }
        if($sales->tax_in_mis==""){
            $sales->tax_in_mis=0;
        }
         $totalsale=$sales->total_sale;
       $tax_in_mis=$sales->tax_in_mis;
       $difference=$totalsale-$tax_in_mis;
       
//       echo "totalsale".$totalsale; 
//       echo "tax_in_mis".$tax_in_mis; 
//       echo "difference".$difference;
//       die();
        $arrsupervisors = array();
        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");
//        
         $arramount = array();
        
        $arrsales[]=array('y'=>$totalsale);
        $arrsales[]=array('y'=>$tax_in_mis);
        $arrsales[]=array('y'=>$difference);
        
        $arrcount = array_values($arrsales);
        $arrcount = json_encode($arrcount);
        
        
        if(count($arrsupervisors)>8){
            $supervisorcount=count($arrsupervisors);
            $supervisorcount=$supervisorcount*60;
        }else{
            $supervisorcount=480;
        }
        
        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('finance/vatreport', array(
            'arrsupervisors' => $arrsupervisors,'arrsales'=>$arrcount,
            'supervisorcount'=>$supervisorcount,'totalcount'=>$totalsupervisors,
            "actualsale"=>$actualsale,
            'periodStartDate'=>date('01-m-Y'),'periodEndDate'=>date('t-m-Y'),"regions"=>$regions,"region_id"=>$region_id));
    }
     
    public function getvatreport() {
        
 
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
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
        
        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,sum(pos_sales.tax_in_mis) as tax_in_mis'))
                 ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                 ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                 ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                 ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                       return $query->where('area.region_id', '=', $region_id);
                       }) 
                ->first();
        
           $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id','region_details.name as region_name','region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
         
        // All employees
        $totalsupervisors = DB::table('employees')
              ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
              ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
              ->count();
        
      /*  $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');*/
        
      $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.tax_in_mis),0) as total_sale'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                 ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                 ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                 ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                       return $query->where('area.region_id', '=', $region_id);
                       }) 
                ->value('total_sale');
       
       
       // $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);
       $actualsale=  Customhelper::numberformatter($actualsale);
       
       
      
       if($sales->total_sale==""){
            $sales->total_sale=0;
        }
        if($sales->tax_in_mis==""){
            $sales->tax_in_mis=0;
        }
         $totalsale=$sales->total_sale;
       $tax_in_mis=$sales->tax_in_mis;
       $difference=$totalsale-$tax_in_mis;

        $arrsupervisors = array();
        $arrsales = '';

         $arramount = array();
        
        $arrsales[]=array('y'=>$totalsale);
        $arrsales[]=array('y'=>$tax_in_mis);
        $arrsales[]=array('y'=>$difference);
        
        $arrcount = array_values($arrsales);
        $arrcount = json_encode($arrcount);
        
        
        
        if(count($arrsupervisors)>8){
            $supervisorcount=count($arrsupervisors);
            $supervisorcount=$supervisorcount*60;
        }else{
            $supervisorcount=480;
        }
        
        
        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('finance/vatreport', array(
                        'arrsupervisors' => $arrsupervisors,
                        'arrsales'=>$arrcount,'supervisorcount'=>$supervisorcount,
                        'totalcount'=>$totalsupervisors,"actualsale"=>$actualsale,
                        'periodStartDate'=>$period_start_date,'periodEndDate'=>$period_end_date,"regions"=>$regions,"region_id"=>$region_id));
    }
     
 
   
   
}
