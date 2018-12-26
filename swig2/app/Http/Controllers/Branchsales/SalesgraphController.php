<?php

namespace App\Http\Controllers\Branchsales;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Module;
use App\Models\Usermodule;
use App\Models\Country;
use App\Models\Document;
use DB;
use Mail;
use App;
use PDF;
use Excel;
use Customhelper;

class SalesgraphController extends Controller {

    public function index() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $region_id = "";

        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);

        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => $data->total_sale, 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);


//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");
//        
        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/supervisor_sales_graph/index', array(
            'arrsupervisors' => $arrsupervisors, 'arrsales' => $arrsales,
            'supervisorcount' => $supervisorcount, 'totalcount' => $totalsupervisors,
            "actualsale" => $actualsale,
            'periodStartDate' => date('01-m-Y'), 'periodEndDate' => date('t-m-Y'), "regions" => $regions, "region_id" => $region_id));
    }

    public function getsupervisorsale() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;

        $region_id = Input::get('region_id');
        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
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
          ->value('total_sale'); */

        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
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
        $actualsale = Customhelper::numberformatter($actualsale);

        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => $data->total_sale, 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);

        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }


        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/supervisor_sales_graph/index', array(
            'arrsupervisors' => $arrsupervisors,
            'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
            'totalcount' => $totalsupervisors, "actualsale" => $actualsale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date, "regions" => $regions, "region_id" => $region_id));
    }
    
    public function branchsale() {

            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
              $region_id="";
            $first_day = date('Y-m-01');
            $last_day = date('Y-m-t');

            $sales = DB::table('master_resources as m')
                    ->select('m.name')
                    ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,m.name as branch_name,m.branch_code as code,m.id as branch_id'))
                    ->leftJoin('pos_sales', function($join) use ($first_day, $last_day, $company_id) {
                        $join->on('m.id', '=', 'pos_sales.branch_id')
                        ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
                    })
    //                ->leftjoin('master_resources as branch', 'branch.id', '=', 'pos_sales.branch_id')
                    ->leftjoin('master_resources as area', 'm.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->where('m.resource_type', '=', 'BRANCH')
//                    ->where('m.status', '=', '1')
                    ->groupby('m.id')
                    ->orderby('total_sale', 'DESC')
                    ->get();

           $totalSale = DB::table('master_resources as m')
                    ->select('m.name')
                    ->select(DB::raw('SUM(pos_sales.total_sale) as t_total_sale,'
                            . 'SUM(opening_amount) as t_opening_amount,'
                            . 'SUM(cash_collection) as t_cash_collection,'
                            . 'SUM(credit_sale) as t_credit_sale,'
                            . 'SUM(bank_sale) as t_bank_sale,'
                            . '(SUM(cash_collection) - SUM(cash_sale)) as t_cashdifference,'
                            . '(SUM(bank_collection) - SUM(bank_sale)) as t_bankdifference,'
                            . 'SUM(difference) as t_difference,'
                            . 'SUM(meal_consumption) as t_meal_consumption,'
                            . 'SUM(cash_sale) as t_cash_sale,'
                            . 'SUM(bank_collection) as t_bank_collection,'
                            . '(SUM(total_sale) - SUM(tax_in_mis)) as t_sale_amount,'
                            . 'SUM(tax_in_mis) as t_tax_amount'))
                    ->leftJoin('pos_sales', function($join) use ($first_day, $last_day, $company_id) {
                        $join->on('m.id', '=', 'pos_sales.branch_id')
                        ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
                    })
                    ->leftjoin('master_resources as area', 'm.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->where('m.resource_type', '=', 'BRANCH')
//                    ->where('m.status', '=', '1')
                    //->groupby('region.id')
                    ->first();



            $regions = DB::table('master_resources as region_details')
                    ->select('region_details.id as id','region_details.name as region_name','region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                    ->where('region_details.resource_type', '=', 'REGION')
                    ->where('region_details.status', '=', 1)
                    ->get();
            // All employees
            $totalbranches = DB::table('master_resources')
                  ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                  ->count();

           //$totalbranches=  Customhelper::numberformatter($totalbranches);

             $actualsale = DB::table('pos_sales')
                    ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('company_id', '=', $company_id)
                    ->value('total_sale');

            $actualsale=  Customhelper::numberformatter($actualsale);


            $arrbranches = array();
            $arrsales = array();

            foreach ($sales as $data) {
                $arrbranches[]=$data->code." : ".$data->branch_name;
                if($data->total_sale==NULL){
                    $data->total_sale=0;
                }


                $branch = \Crypt::encrypt($data->branch_id);
                $row = array('name' => $data->branch_name, 'y' => $data->total_sale, "id" => "$branch");
                array_push($arrsales, $row);
            }



            if(count($arrbranches)>8){
                $supervisorcount=count($arrbranches);
                $supervisorcount=$supervisorcount*60;
            }else{
                $supervisorcount=480;
            }

            $arrbranches = array_values($arrbranches);
            $arrbranches = json_encode($arrbranches);
            $arrsales = array_values($arrsales);
            $arrsales = json_encode($arrsales);
            return view('branchsales/branch_sales_graph/index', array(
                'arrbranches' => $arrbranches,'arrsales'=>$arrsales,"actualsale"=>$actualsale,
                'supervisorcount'=>$supervisorcount,'totalcount'=>$totalbranches,
                'periodStartDate'=>date('01-m-Y'),'periodEndDate'=>date('t-m-Y'),"regions"=>$regions,"region_id"=>$region_id,'totalSale'=>$totalSale));
        }
  

    public function getbranchsale() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $region_id = Input::get('region_id');
        if (!empty(Input::get("region_name"))) {
            $region_id = \Crypt::decrypt(Input::get("region_name"));
        }

        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;

        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }
        
        $totalSale = DB::table('master_resources as m')
                ->select('m.name')
                ->select(DB::raw('SUM(pos_sales.total_sale) as t_total_sale,'
                        . 'SUM(opening_amount) as t_opening_amount,'
                        . 'SUM(cash_collection) as t_cash_collection,'
                        . 'SUM(credit_sale) as t_credit_sale,'
                        . 'SUM(bank_sale) as t_bank_sale,'
                        . '(SUM(cash_collection) - SUM(cash_sale)) as t_cashdifference,'
                        . '(SUM(bank_collection) - SUM(bank_sale)) as t_bankdifference,'
                        . 'SUM(difference) as t_difference,'
                        . 'SUM(meal_consumption) as t_meal_consumption,'
                        . 'SUM(cash_sale) as t_cash_sale,'
                        . 'SUM(bank_collection) as t_bank_collection,'
                        . '(SUM(total_sale) - SUM(tax_in_mis)) as t_sale_amount,'
                        . 'SUM(tax_in_mis) as t_tax_amount'))
                ->leftJoin('pos_sales', function($join) use ($first_day, $last_day, $company_id) {
                    $join->on('m.id', '=', 'pos_sales.branch_id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
                })
                ->leftjoin('master_resources as area', 'm.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->where('m.resource_type', '=', 'BRANCH')
//                ->where('m.status', '=', '1')
                //->groupby('region.id')
                ->first();
                
        $sales = DB::table('master_resources as m')
                ->select('m.name')
                ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,m.name as branch_name,m.branch_code as code,m.id as branch_id'))
                ->leftJoin('pos_sales', function($join) use ($first_day, $last_day, $company_id) {
                    $join->on('m.id', '=', 'pos_sales.branch_id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
                })
//                ->leftjoin('master_resources as branch', 'branch.id', '=', 'pos_sales.branch_id')
                ->leftjoin('master_resources as area', 'm.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->where('m.resource_type', '=', 'BRANCH')
//                ->where('m.status', '=', '1')
                ->groupby('m.id')
                ->orderby('total_sale', 'DESC')
                ->get();


        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();


        // All employees
        $totalbranches = DB::table('master_resources')
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->count();

        $actualsale = DB::table('master_resources as m')
                ->select('m.name')
                ->select(DB::raw('sum(pos_sales.total_sale) as total_sale,m.name as branch_name,m.branch_code as code'))
                ->leftJoin('pos_sales', function($join) use ($first_day, $last_day, $company_id) {
                    $join->on('m.id', '=', 'pos_sales.branch_id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 and pos_sales.company_id=$company_id");
                })
//                ->leftjoin('master_resources as branch', 'branch.id', '=', 'pos_sales.branch_id')
                ->leftjoin('master_resources as area', 'm.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('m.resource_type', '=', 'BRANCH')
//                ->where('m.status', '=', '1')
                ->value('total_sale');


        $actualsale = Customhelper::numberformatter($actualsale);

        $arrbranches = array();
        $arrsales = array();

        foreach ($sales as $data) {
            $arrbranches[] = $data->code . " : " . $data->branch_name;
            if ($data->total_sale == NULL) {
                $data->total_sale = 0;
            }
            $branch = \Crypt::encrypt($data->branch_id);
            $row = array('name' => $data->branch_name, 'y' => $data->total_sale, "id" => "$branch");
            array_push($arrsales, $row);
        }


        if (count($arrbranches) > 8) {
            $supervisorcount = count($arrbranches);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrbranches = array_values($arrbranches);
        $total_branch = count($arrbranches);
        $arrbranches = json_encode($arrbranches);
        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);
        return view('branchsales/branch_sales_graph/index', array(
            'arrbranches' => $arrbranches, 'arrsales' => $arrsales, "actualsale" => $actualsale,
            'supervisorcount' => $supervisorcount, 'totalcount' => $total_branch, 'totalSale' => $totalSale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date, "regions" => $regions, "region_id" => $region_id));
    }

    public function cashiersales() {


        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $region_id = "";

        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.cash_collection) as total_sale,cashier_id,emp.first_name,emp.username,branch.name as branch_name,branch.branch_code as branch_code'))
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->groupby('cashier_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();


        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Cashier'")
                ->count();

        // $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.cash_collection),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);

//        
//        $arrsupervisors = array();
//        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name.'<br>'.$data->branch_code.'-'.$data->branch_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");
//        

        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name . '<br>' . $data->branch_code . '-' . $data->branch_name;
            $arrsales[] = array('y' => $data->total_sale, 'employee_id' => $data->cashier_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);




        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();

        return view('branchsales/report_graphs/sales_cashier', array(
            'arrsupervisors' => $arrsupervisors, 'arrsales' => $arrsales,
            'supervisorcount' => $supervisorcount, 'totalcount' => $totalsupervisors,
            "actualsale" => $actualsale,
            'periodStartDate' => date('01-m-Y'), 'periodEndDate' => date('t-m-Y'), "regions" => $regions, "region_id" => $region_id));
    }

    public function getcashiersales() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;

        $region_id = Input::get('region_id');

        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.cash_collection) as total_sale,cashier_id,emp.first_name,emp.username,branch.name as branch_name,branch.branch_code as branch_code'))
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->groupby('cashier_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Cashier'")
                ->count();

        //  $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        /*  $actualsale = DB::table('pos_sales')
          ->select(DB::raw('COALESCE(sum(pos_sales.cash_collection),0) as total_sale'))
          ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
          ->where('company_id', '=', $company_id)
          ->value('total_sale'); */



        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.cash_collection),0) as total_sale'))
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);


//        $arrsupervisors = array();
//        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name.'<br>'.$data->branch_code.'-'.$data->branch_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");

        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name . '<br>' . $data->branch_code . '-' . $data->branch_name;
            $arrsales[] = array('y' => $data->total_sale, 'employee_id' => $data->cashier_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);




        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }


        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);



        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();


        return view('branchsales/report_graphs/sales_cashier', array(
            'arrsupervisors' => $arrsupervisors,
            'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
            'totalcount' => $totalsupervisors, "actualsale" => $actualsale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date, "regions" => $regions, "region_id" => $region_id));
    }

    public function cashiertipscollection() {


        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $region_id = "";

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.tips_collected) as total_sale,cashier_id,emp.first_name,emp.username,branch.name as branch_name,branch.branch_code as branch_code'))
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->groupby('cashier_id')
                ->orderby('total_sale', 'DESC')
                ->get();



        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Cashier'")
                ->count();

        //$totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.tips_collected),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);


//        $arrsupervisors = array();
//        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name.'<br>'.$data->branch_code.'-'.$data->branch_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");
//        


        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name . '<br>' . $data->branch_code . '-' . $data->branch_name;
            $arrsales[] = array('y' => $data->total_sale, 'employee_id' => $data->cashier_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);


        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/tips_collection', array(
            'arrsupervisors' => $arrsupervisors, 'arrsales' => $arrsales,
            'supervisorcount' => $supervisorcount, 'totalcount' => $totalsupervisors,
            "actualsale" => $actualsale,
            'periodStartDate' => date('01-m-Y'), 'periodEndDate' => date('t-m-Y'), "regions" => $regions, "region_id" => $region_id));
    }

    public function getcashiertipscollection() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;

        $region_id = Input::get('region_id');

        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.tips_collected) as total_sale,cashier_id,emp.first_name,emp.username,branch.name as branch_name,branch.branch_code as branch_code'))
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->groupby('cashier_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();


        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Cashier'")
                ->count();

        //  $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        /* $actualsale = DB::table('pos_sales')
          ->select(DB::raw('COALESCE(sum(pos_sales.tips_collected),0) as total_sale'))
          ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
          ->where('company_id', '=', $company_id)
          ->value('total_sale'); */

        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.tips_collected),0) as total_sale'))
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->where('pos_sales.company_id', '=', $company_id)
                ->value('total_sale');


        $actualsale = Customhelper::numberformatter($actualsale);


//        $arrsupervisors = array();
//        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name.'<br>'.$data->branch_code.'-'.$data->branch_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");
//        

        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name . '<br>' . $data->branch_code . '-' . $data->branch_name;
            $arrsales[] = array('y' => $data->total_sale, 'employee_id' => $data->cashier_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);




        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }


        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/tips_collection', array(
            'arrsupervisors' => $arrsupervisors,
            'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
            'totalcount' => $totalsupervisors, "actualsale" => $actualsale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date, "regions" => $regions, "region_id" => $region_id));
    }

    public function collectiondifference() {


        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');
        $diff_id = "cash_diff";
        $sales = DB::table('pos_sales')
                ->select(DB::raw('CASE WHEN(sum(pos_sales.cash_sale-pos_sales.cash_collection)) THEN sum(pos_sales.cash_sale-pos_sales.cash_collection) ELSE 0 END as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
        $region_id = "";

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        //  $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.cash_sale-pos_sales.cash_collection),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);


//       $arrsupervisors = array();
//       $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");


        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => $data->total_sale, 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id, 'diff_id' => $diff_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);



        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/collection_difference', array(
            'arrsupervisors' => $arrsupervisors, 'arrsales' => $arrsales,
            'supervisorcount' => $supervisorcount, 'totalcount' => $totalsupervisors,
            "actualsale" => $actualsale,
            'periodStartDate' => date('01-m-Y'), 'periodEndDate' => date('t-m-Y'), "regions" => $regions, "region_id" => $region_id, 'diff_id' => $diff_id));
    }

    public function getcollectiondifference() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }


        $region_id = Input::get('region_id');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;

        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

        $diff_id = Input::get('diff_id');

        if ($diff_id == "cash_diff") {

            $sales = DB::table('pos_sales')
                    ->select(DB::raw('CASE WHEN(sum(pos_sales.cash_sale-pos_sales.cash_collection)) THEN sum(pos_sales.cash_sale-pos_sales.cash_collection) ELSE 0 END as total_sale,employee_id,emp.first_name,emp.username'))
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->groupby('employee_id')
                    ->orderby('total_sale', 'DESC')
                    ->get();
        } else {

            $sales = DB::table('pos_sales')
                    ->select(DB::raw('CASE WHEN(sum(pos_sales.bank_sale-pos_sales.bank_collection)) THEN sum(pos_sales.bank_sale-pos_sales.bank_collection) ELSE 0 END as total_sale,employee_id,emp.first_name,emp.username'))
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->groupby('employee_id')
                    ->orderby('total_sale', 'DESC')
                    ->get();
        }
        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();


        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        //$totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        /* $actualsale = DB::table('pos_sales')
          ->select(DB::raw('COALESCE(sum(pos_sales.cash_collection-pos_sales.cash_sale),0) as total_sale'))
          ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
          ->where('company_id', '=', $company_id)
          ->value('total_sale'); */
        if ($diff_id == "cash_diff") {
            $actualsale = DB::table('pos_sales')
                    ->select(DB::raw('COALESCE(sum(pos_sales.cash_sale-pos_sales.cash_collection),0) as total_sale'))
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


            $arrsupervisors = array();
            $arrsales = '';

            $arrsales = array();



            foreach ($sales as $data) {
                $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
                $arrsales[] = array('y' => $data->total_sale, 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id, 'diff_id' => $diff_id);
            }


            $arrsales = array_values($arrsales);
            $arrsales = json_encode($arrsales);
        } else {

            $actualsale = DB::table('pos_sales')
                    ->select(DB::raw('COALESCE(sum(pos_sales.bank_sale-pos_sales.bank_collection),0) as total_sale'))
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


            $arrsupervisors = array();
            $arrsales = '';

            $arrsales = array();



            foreach ($sales as $data) {
                $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
                $arrsales[] = array('y' => $data->total_sale, 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id, 'diff_id' => $diff_id);
            }


            $arrsales = array_values($arrsales);
            $arrsales = json_encode($arrsales);
        }

        $actualsale = Customhelper::numberformatter($actualsale);


//        
//        $arrsupervisors = array();
//        $arrsales = '';
//       
//       foreach ($sales as $data) {
//           $arrsupervisors[]=$data->username.' : '.$data->first_name;
//           $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");





        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }


        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/collection_difference', array(
            'arrsupervisors' => $arrsupervisors,
            'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
            'totalcount' => $totalsupervisors, "actualsale" => $actualsale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date, "regions" => $regions, "region_id" => $region_id, "diff_id" => $diff_id));
    }

    public function creditfreesale() {


        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.credit_sale) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();


        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        // $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);



        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.credit_sale),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);


        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();

        $region_id = "";

//        $arrsupervisors = array();
//        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");



        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);

        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/credit_sale', array(
            'arrsupervisors' => $arrsupervisors, 'arrsales' => $arrsales,
            'supervisorcount' => $supervisorcount, 'totalcount' => $totalsupervisors,
            "actualsale" => $actualsale,
            'periodStartDate' => date('01-m-Y'), 'periodEndDate' => date('t-m-Y'), "regions" => $regions, "region_id" => $region_id));
    }

    public function getcreditfreesale() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;

        $region_id = Input::get('region_id');

        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.credit_sale) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        // $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.credit_sale),0) as total_sale'))
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

        $actualsale = Customhelper::numberformatter($actualsale);


//        $arrsupervisors = array();
//        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");


        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }

        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);


        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }


        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/credit_sale', array(
            'arrsupervisors' => $arrsupervisors,
            'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
            'totalcount' => $totalsupervisors, "actualsale" => $actualsale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date, "regions" => $regions, "region_id" => $region_id));
    }

    public function cashsale() {


        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $region_id = "";
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.cash_collection) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();


        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();
        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        //  $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.cash_collection),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);

//        
//        $arrsupervisors = array();
//        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");


        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);



        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/cash_sales', array(
            'arrsupervisors' => $arrsupervisors, 'arrsales' => $arrsales,
            'supervisorcount' => $supervisorcount, 'totalcount' => $totalsupervisors,
            "actualsale" => $actualsale,
            'periodStartDate' => date('01-m-Y'), 'periodEndDate' => date('t-m-Y'), "regions" => $regions, "region_id" => $region_id));
    }

    public function getcashsale() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $region_id = Input::get('region_id');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;

        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.cash_collection) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        //$totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.cash_collection),0) as total_sale'))
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

        $actualsale = Customhelper::numberformatter($actualsale);



//        $arrsupervisors = array();
//        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");

        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);



        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }


        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/cash_sales', array(
            'arrsupervisors' => $arrsupervisors,
            'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
            'totalcount' => $totalsupervisors, "actualsale" => $actualsale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date, "regions" => $regions, "region_id" => $region_id));
    }

    public function cardsale() {


        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $region_id = "";
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.bank_sale) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();


        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();


        //$totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.bank_sale),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');


        $actualsale = Customhelper::numberformatter($actualsale);


        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);


        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/card_sales', array(
            'arrsupervisors' => $arrsupervisors, 'arrsales' => $arrsales,
            'supervisorcount' => $supervisorcount, 'totalcount' => $totalsupervisors,
            "actualsale" => $actualsale,
            'periodStartDate' => date('01-m-Y'), 'periodEndDate' => date('t-m-Y'), "regions" => $regions, "region_id" => $region_id));
    }

    public function getcardsale() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $region_id = Input::get('region_id');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;

        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.bank_sale) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();


        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();


        // $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.bank_sale),0) as total_sale'))
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


        $actualsale = Customhelper::numberformatter($actualsale);

        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);

        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }


        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/card_sales', array(
            'arrsupervisors' => $arrsupervisors,
            'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
            'totalcount' => $totalsupervisors, "actualsale" => $actualsale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date, "regions" => $regions, "region_id" => $region_id));
    }

    public function openingamountbranch() {


        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $region_id = "";
        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.opening_amount) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        // $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.opening_amount),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);



//        $arrsupervisors = array();
//        $arrsales = '';
//        
//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name;
//            $arrsales=$arrsales.$data->total_sale.",";
//        }
//        
//        $arrsales=rtrim($arrsales, ",");

        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);


        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/opening_amount', array(
            'arrsupervisors' => $arrsupervisors, 'arrsales' => $arrsales,
            'supervisorcount' => $supervisorcount, 'totalcount' => $totalsupervisors,
            "actualsale" => $actualsale,
            'periodStartDate' => date('01-m-Y'), 'periodEndDate' => date('t-m-Y'), "regions" => $regions, "region_id" => $region_id));
    }

    public function getopeningamountbranch() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;
        $region_id = Input::get('region_id');
        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

        $sales = DB::table('pos_sales')
                ->select(DB::raw('sum(pos_sales.opening_amount) as total_sale,employee_id,emp.first_name,emp.username'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->groupby('employee_id')
                ->orderby('total_sale', 'DESC')
                ->get();

        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        //  $totalsupervisors=  Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.opening_amount),0) as total_sale'))
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

        $actualsale = Customhelper::numberformatter($actualsale);




        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->username . ' : ' . $data->first_name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'employee_id' => $data->employee_id, 'first_day' => $first_day, 'last_day' => $last_day, 'region_id' => $region_id);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);

        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }


        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/opening_amount', array(
            'arrsupervisors' => $arrsupervisors,
            'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
            'totalcount' => $totalsupervisors, "actualsale" => $actualsale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date, "regions" => $regions, "region_id" => $region_id));
    }

    public function cashiereveningshift(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $branch_id = "";
        $region_id = "";

        $shiftid = DB::table('master_resources')
                ->select('master_resources.id')
                ->where(['resource_type' => 'JOB_SHIFT', 'name' => 'Evening Shift', 'status' => 1])
                ->first();


        $employees = DB::table('resource_allocation')
                ->select('cashier_details.username as employee_code', 'cashier_details.first_name as first_name', 'cashier_details.middle_name as middle_name', 'cashier_details.last_name as last_name', 'cashier_details.alias_name as alias_name', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'region.name as region')
                ->leftjoin('employees as cashier_details', 'resource_allocation.employee_id', '=', 'cashier_details.id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                // ->leftjoin('master_resources as region', 'branch_details.region_id', '=', 'region.id')
                ->leftjoin('master_resources as area', 'branch_details.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->where('cashier_details.status', '!=', 2)
                ->where('cashier_details.admin_status', '!=', 1)
                ->where('resource_allocation.shift_id', '=', $shiftid->id)
                ->where('resource_allocation.resource_type', '=', 'CASHIER')
                ->where('resource_allocation.active', '=', 1)
                ->orderby('cashier_details.created_at', 'DESC')
                ->paginate($paginate);



        $branches = DB::table('master_resources as branch_details')
                ->select('branch_details.id as id', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'branch_details.id as branch_id')->distinct()
                ->where('branch_details.resource_type', '=', 'BRANCH')
                ->where('branch_details.status', '=', 1)
                ->get();
        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();


        if ($request->ajax()) {


            $shiftid = DB::table('master_resources')
                    ->select('master_resources.id')
                    ->where(['resource_type' => 'JOB_SHIFT', 'name' => 'Evening Shift', 'status' => 1])
                    ->first();

            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $branch = Input::get('branch');
            $region = Input::get('region');

            $sortordcode = Input::get('sortordcode');
            $sortordname = Input::get('sortordname');
            $sortordbranch = Input::get('sortordbranch');
            $sortordregion = Input::get('sortordregion');


            $employees = DB::table('resource_allocation')
                    ->select('cashier_details.username as employee_code', 'cashier_details.first_name as first_name', 'cashier_details.middle_name as middle_name', 'cashier_details.last_name as last_name', 'cashier_details.alias_name as alias_name', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'region.name as region')
                    ->leftjoin('employees as cashier_details', 'resource_allocation.employee_id', '=', 'cashier_details.id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->leftjoin('master_resources as area', 'branch_details.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->where('cashier_details.status', '!=', 2)
                    ->where('cashier_details.admin_status', '!=', 1)
                    ->where('resource_allocation.shift_id', '=', $shiftid->id)
                    ->where('resource_allocation.resource_type', '=', 'CASHIER')
                    ->where('resource_allocation.active', '=', 1)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(cashier_details.first_name like '$search_key%' or concat(cashier_details.first_name,' ',cashier_details.alias_name,' ',cashier_details.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(cashier_details.username like '$searchbycode%')");
                    })
                    ->when($branch, function ($query) use ($branch) {
                        return $query->where('resource_allocation.branch_id', '=', $branch);
                    })
                    ->when($region, function ($query) use ($region) {
                        return $query->where('area.region_id', '=', $region);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('cashier_details.username', $sortordcode);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('cashier_details.first_name', $sortordname);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch_details.name', $sortordbranch);
                    })
                    ->when($sortordregion, function ($query) use ($sortordregion) {
                        return $query->orderby('area.name', $sortordregion);
                    })
                    ->orderby('cashier_details.created_at', 'DESC')
                    ->paginate($paginate);

            return view('branchsales/cashier_evening/searchresults', array('employees' => $employees));
        }
        return view('branchsales/cashier_evening/index', array('employees' => $employees, 'branches' => $branches, 'branch_id' => $branch_id, 'regions' => $regions, 'region_id' => $region_id));
    }

    public function supervisorbranchlist(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');

        $branch_id = '';


        $employees = DB::table('resource_allocation')
                ->select('supervisor_details.first_name as first_name', 'supervisor_details.alias_name as alias_name', 'supervisor_details.middle_name as middle_name', 'supervisor_details.username as employee_code', 'supervisor_details.last_name as last_name', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code')
                ->leftjoin('employees as supervisor_details', 'resource_allocation.employee_id', '=', 'supervisor_details.id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->where('supervisor_details.status', '!=', 2)
                ->where('supervisor_details.admin_status', '!=', 1)
                ->where('resource_allocation.resource_type', '=', 'SUPERVISOR')
                ->where('resource_allocation.active', '=', 1)
                ->orderby('supervisor_details.created_at', 'DESC')
                ->groupby('supervisor_details.id')
                //->distinct()
                ->paginate($paginate);


        $branches = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'BRANCH', 'status' => 1])
                ->orderby('name', 'ASC')
                ->get();

        if ($request->ajax()) {

            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $branch = Input::get('branch');



            $employees = DB::table('resource_allocation')
                    ->select('supervisor_details.first_name as first_name', 'supervisor_details.alias_name as alias_name', 'supervisor_details.middle_name as middle_name', 'supervisor_details.username as employee_code', 'supervisor_details.last_name as last_name', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code')
                    ->leftjoin('employees as supervisor_details', 'resource_allocation.employee_id', '=', 'supervisor_details.id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->where('supervisor_details.status', '!=', 2)
                    ->where('supervisor_details.admin_status', '!=', 1)
                    ->where('resource_allocation.resource_type', '=', 'SUPERVISOR')
                    ->where('resource_allocation.active', '=', 1)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(supervisor_details.first_name like '$search_key%' or concat(supervisor_details.first_name,' ',supervisor_details.alias_name,' ',employees.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(supervisor_details.username like '$searchbycode%')");
                    })
                    ->when($branch, function ($query) use ($branch) {
                        return $query->where('branch_details.id', '=', $branch);
                    })
                    ->orderby('supervisor_details.created_at', 'DESC')
                    ->groupby('supervisor_details.id')
                    ->paginate($paginate);
            return view('branchsales/supervisor_branch/searchresults', array('employees' => $employees));
        }
        return view('branchsales/supervisor_branch/index', array('employees' => $employees, 'branches' => $branches, 'branch_id' => $branch_id));
    }

    public function mealconsumptionbranchwise() {


        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = date('Y-m-01');
        $last_day = date('Y-m-t');

        $sales = DB::table('master_resources as branchdetails')
                ->select(DB::raw('branchdetails.name,sum(pos_sales.meal_consumption) as total_sale,branch_id,branchdetails.branch_code'))
                ->leftjoin('pos_sales', 'pos_sales.branch_id', '=', 'branchdetails.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('branchdetails.status', '=', 1)
                ->groupby('branchdetails.id')
                ->orderby('total_sale', 'DESC')
                ->get();


        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        $totalsupervisors = Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.meal_consumption),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);


        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



//        foreach ($sales as $data) {
//            $arrsupervisors[]=$data->username.' : '.$data->first_name;
//            $arrsales[]=array('y'=>floatval($data->total_sale),'employee_id'=>$data->employee_id,'first_day'=>$first_day,'last_day'=>$last_day,'region_id'=>$region_id);
//        
//        
//        }


        foreach ($sales as $data) {
            $arrsupervisors[] = $data->branch_code . ' - ' . $data->name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'branch_id' => $data->branch_id, 'first_day' => $first_day, 'last_day' => $last_day);
        }


        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);

        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }

        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/meal_consumption', array(
            'arrsupervisors' => $arrsupervisors, 'arrsales' => $arrsales,
            'supervisorcount' => $supervisorcount, 'totalcount' => $totalsupervisors,
            "actualsale" => $actualsale,
            'periodStartDate' => date('01-m-Y'), 'periodEndDate' => date('t-m-Y')));
    }

    public function getmealconsumptionbranchwise() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');

        $period_start_date = $first_day;
        $period_end_date = $last_day;

        if ($first_day != '') {
            $first_day = explode('-', $first_day);
            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
        }
        if ($last_day != '') {
            $last_day = explode('-', $last_day);
            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
        }

//        $sales = DB::table('pos_sales')
//                ->select(DB::raw('sum(pos_sales.bank_sale) as total_sale,employee_id,emp.first_name,emp.username'))
//                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
//                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
//                ->where('company_id', '=', $company_id)
//                ->groupby('employee_id')
//                ->orderby('total_sale','DESC')
//                ->get();

        $sales = DB::table('master_resources as branchdetails')
                ->select(DB::raw('branchdetails.name,sum(pos_sales.meal_consumption) as total_sale,branch_id,branchdetails.branch_code'))
                ->leftjoin('pos_sales', 'pos_sales.branch_id', '=', 'branchdetails.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('branchdetails.status', '=', 1)
                ->groupby('branchdetails.id')
                ->orderby('total_sale', 'DESC')
                ->get();

        // All employees
        $totalsupervisors = DB::table('employees')
                ->leftjoin('master_resources as job_pos', 'employees.job_position', '=', 'job_pos.id')
                ->whereRaw("employees.status=1 AND job_pos.name='Supervisor'")
                ->count();

        $totalsupervisors = Customhelper::numberformatter($totalsupervisors);


        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.meal_consumption),0) as total_sale'))
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');

        $actualsale = Customhelper::numberformatter($actualsale);



        $arrsupervisors = array();
        $arrsales = '';

        $arrsales = array();



        foreach ($sales as $data) {
            $arrsupervisors[] = $data->branch_code . ' - ' . $data->name;
            $arrsales[] = array('y' => floatval($data->total_sale), 'branch_id' => $data->branch_id, 'first_day' => $first_day, 'last_day' => $last_day);
        }




        $arrsales = array_values($arrsales);
        $arrsales = json_encode($arrsales);

        if (count($arrsupervisors) > 8) {
            $supervisorcount = count($arrsupervisors);
            $supervisorcount = $supervisorcount * 60;
        } else {
            $supervisorcount = 480;
        }


        $arrsupervisors = array_values($arrsupervisors);
        $arrsupervisors = json_encode($arrsupervisors);

        return view('branchsales/report_graphs/meal_consumption', array(
            'arrsupervisors' => $arrsupervisors,
            'arrsales' => $arrsales, 'supervisorcount' => $supervisorcount,
            'totalcount' => $totalsupervisors, "actualsale" => $actualsale,
            'periodStartDate' => $period_start_date, 'periodEndDate' => $period_end_date));
    }

    // Generate PDF funcion
    public function exportdata() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $shiftid = DB::table('master_resources')
                ->select('master_resources.id')
                ->where(['resource_type' => 'JOB_SHIFT', 'name' => 'Evening Shift', 'status' => 1])
                ->first();

        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }
        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $branch = Input::get('branch');
        $region = Input::get('region');

        $sortordcode = Input::get('sortordcode');
        $sortordname = Input::get('sortordname');
        $sortordbranch = Input::get('sortordbranch');
        $sortordregion = Input::get('sortordregion');


        $employees = DB::table('resource_allocation')
                ->select('cashier_details.username as employee_code', 'cashier_details.first_name as first_name', 'cashier_details.middle_name as middle_name', 'cashier_details.last_name as last_name', 'cashier_details.alias_name as alias_name', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'region.name as region')
                ->leftjoin('employees as cashier_details', 'resource_allocation.employee_id', '=', 'cashier_details.id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('master_resources as area', 'branch_details.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->where('cashier_details.status', '!=', 2)
                ->where('cashier_details.admin_status', '!=', 1)
                ->where('resource_allocation.shift_id', '=', $shiftid->id)
                ->where('resource_allocation.resource_type', '=', 'CASHIER')
                ->where('resource_allocation.active', '=', 1)
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(cashier_details.first_name like '$search_key%' or concat(cashier_details.first_name,' ',cashier_details.alias_name,' ',cashier_details.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(cashier_details.username like '$searchbycode%')");
                })
                ->when($branch, function ($query) use ($branch) {
                    return $query->where('resource_allocation.branch_id', '=', $branch);
                })
                ->when($region, function ($query) use ($region) {
                    return $query->where('area.region_id', '=', $region);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('cashier_details.username', $sortordcode);
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('cashier_details.first_name', $sortordname);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch_details.name', $sortordbranch);
                })
                ->when($sortordregion, function ($query) use ($sortordregion) {
                    return $query->orderby('area.name', $sortordregion);
                })
                ->orderby('cashier_details.created_at', 'DESC')
                ->get();



        if ($excelorpdf == "Excel") {

            Excel::create('CashierDutyEveningShift', function($excel) use($employees) {
                // Set the title
                $excel->setTitle('Cashier in Duty Evening Shift');

                $excel->sheet('Cashier in Duty Evening Shift', function($sheet) use($employees) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Cashier in Duty Evening Shift');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Region"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($employees); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $employees[$i]->employee_code);
                        $sheet->setCellValue('C' . $chrRow, $employees[$i]->first_name . ' ' . $employees[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $employees[$i]->branch_code . '-' . $employees[$i]->branch_name);
                        $sheet->setCellValue('E' . $chrRow, $employees[$i]->region);

                        $sheet->cells('A' . $chrRow . ':E' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Cashier in Duty Evening Shift</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Region </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($employees as $employee) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->employee_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->first_name . " " . $employee->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->branch_code . "-" . $employee->branch_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->region . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('cashier_on_duty_evening_shift.pdf');
        }
    }

    public function cashiermorningshift(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $branch_id = "";
        $region_id = "";

        $shiftid = DB::table('master_resources')
                ->select('master_resources.id')
                ->where(['resource_type' => 'JOB_SHIFT', 'name' => 'Morning Shift', 'status' => 1])
                ->first();


        $employees = DB::table('resource_allocation')
                ->select('cashier_details.username as employee_code', 'cashier_details.first_name as first_name', 'cashier_details.middle_name as middle_name', 'cashier_details.last_name as last_name', 'cashier_details.alias_name as alias_name', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'region.name as region')
                ->leftjoin('employees as cashier_details', 'resource_allocation.employee_id', '=', 'cashier_details.id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('master_resources as area', 'branch_details.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->where('cashier_details.status', '!=', 2)
                ->where('cashier_details.admin_status', '!=', 1)
                ->where('resource_allocation.shift_id', '=', $shiftid->id)
                ->where('resource_allocation.resource_type', '=', 'CASHIER')
                ->where('resource_allocation.active', '=', 1)
                ->orderby('cashier_details.created_at', 'DESC')
                ->paginate($paginate);

        $branches = DB::table('master_resources as branch_details')
                ->select('branch_details.id as id', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'branch_details.id as branch_id')->distinct()
                ->where('branch_details.resource_type', '=', 'BRANCH')
                ->where('branch_details.status', '=', 1)
                ->get();
        $regions = DB::table('master_resources as region_details')
                ->select('region_details.id as id', 'region_details.name as region_name', 'region_details.alias_name as region_alias', 'region_details.id as region_id')->distinct()
                ->where('region_details.resource_type', '=', 'REGION')
                ->where('region_details.status', '=', 1)
                ->get();


        if ($request->ajax()) {


            $shiftid = DB::table('master_resources')
                    ->select('master_resources.id')
                    ->where(['resource_type' => 'JOB_SHIFT', 'name' => 'Morning Shift', 'status' => 1])
                    ->first();

            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $branch = Input::get('branch');
            $region = Input::get('region');


            $sortordcode = Input::get('sortordcode');
            $sortordname = Input::get('sortordname');
            $sortordbranch = Input::get('sortordbranch');
            $sortordregion = Input::get('sortordregion');

            $employees = DB::table('resource_allocation')
                    ->select('cashier_details.username as employee_code', 'cashier_details.first_name as first_name', 'cashier_details.middle_name as middle_name', 'cashier_details.last_name as last_name', 'cashier_details.alias_name as alias_name', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'region.name as region')
                    ->leftjoin('employees as cashier_details', 'resource_allocation.employee_id', '=', 'cashier_details.id')
                    ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                    ->leftjoin('master_resources as area', 'branch_details.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->where('cashier_details.status', '!=', 2)
                    ->where('cashier_details.admin_status', '!=', 1)
                    ->where('resource_allocation.shift_id', '=', $shiftid->id)
                    ->where('resource_allocation.resource_type', '=', 'CASHIER')
                    ->where('resource_allocation.active', '=', 1)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(cashier_details.first_name like '$search_key%' or concat(cashier_details.first_name,' ',cashier_details.alias_name,' ',cashier_details.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(cashier_details.username like '$searchbycode%')");
                    })
                    ->when($branch, function ($query) use ($branch) {
                        return $query->where('resource_allocation.branch_id', '=', $branch);
                    })
                    ->when($region, function ($query) use ($region) {
                        return $query->where('area.region_id', '=', $region);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('cashier_details.username', $sortordcode);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('cashier_details.first_name', $sortordname);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch_details.name', $sortordbranch);
                    })
                    ->when($sortordregion, function ($query) use ($sortordregion) {
                        return $query->orderby('area.name', $sortordregion);
                    })
                    ->orderby('cashier_details.created_at', 'DESC')
                    ->paginate($paginate);

            return view('branchsales/cashier_morning/searchresults', array('employees' => $employees));
        }
        return view('branchsales/cashier_morning/index', array('employees' => $employees, 'branches' => $branches, 'branch_id' => $branch_id, 'regions' => $regions, 'region_id' => $region_id));
    }

    // Generate PDF funcion
    public function exportdatashift() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $shiftid = DB::table('master_resources')
                ->select('master_resources.id')
                ->where(['resource_type' => 'JOB_SHIFT', 'name' => 'Morning Shift', 'status' => 1])
                ->first();

        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }
        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $branch = Input::get('branch');
        $region = Input::get('region');

        $sortordcode = Input::get('sortordcode');
        $sortordname = Input::get('sortordname');
        $sortordbranch = Input::get('sortordbranch');
        $sortordregion = Input::get('sortordregion');

        $employees = DB::table('resource_allocation')
                ->select('cashier_details.username as employee_code', 'cashier_details.first_name as first_name', 'cashier_details.middle_name as middle_name', 'cashier_details.last_name as last_name', 'cashier_details.alias_name as alias_name', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code', 'region.name as region')
                ->leftjoin('employees as cashier_details', 'resource_allocation.employee_id', '=', 'cashier_details.id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->leftjoin('master_resources as area', 'branch_details.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->where('cashier_details.status', '!=', 2)
                ->where('cashier_details.admin_status', '!=', 1)
                ->where('resource_allocation.shift_id', '=', $shiftid->id)
                ->where('resource_allocation.resource_type', '=', 'CASHIER')
                ->where('resource_allocation.active', '=', 1)
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(cashier_details.first_name like '$search_key%' or concat(cashier_details.first_name,' ',cashier_details.alias_name,' ',cashier_details.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(cashier_details.username like '$searchbycode%')");
                })
                ->when($branch, function ($query) use ($branch) {
                    return $query->where('resource_allocation.branch_id', '=', $branch);
                })
                ->when($region, function ($query) use ($region) {
                    return $query->where('area.region_id', '=', $region);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('cashier_details.username', $sortordcode);
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('cashier_details.first_name', $sortordname);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch_details.name', $sortordbranch);
                })
                ->when($sortordregion, function ($query) use ($sortordregion) {
                    return $query->orderby('area.name', $sortordregion);
                })
                ->orderby('cashier_details.created_at', 'DESC')
                ->get();



        if ($excelorpdf == "Excel") {

            Excel::create('CashierDutyMorningShift', function($excel) use($employees) {
                // Set the title
                $excel->setTitle('Cashier in Duty Morning Shift');

                $excel->sheet('Cashier in Duty Morning Shift', function($sheet) use($employees) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Cashier in Duty Morning Shift');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Region"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($employees); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $employees[$i]->employee_code);
                        $sheet->setCellValue('C' . $chrRow, $employees[$i]->first_name . ' ' . $employees[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $employees[$i]->branch_code . '-' . $employees[$i]->branch_name);
                        $sheet->setCellValue('E' . $chrRow, $employees[$i]->region);

                        $sheet->cells('A' . $chrRow . ':E' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Cashier in Duty Morning Shift</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Region </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($employees as $employee) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->employee_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->first_name . " " . $employee->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->branch_code . "-" . $employee->branch_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->region . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('cashier_on_duty_morning_shift.pdf');
        }
    }

    // Generate PDF funcion
    public function exportdatasupervisor() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');





        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }
        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $branch = Input::get('branch');



        $employees = DB::table('resource_allocation')
                ->select('supervisor_details.first_name as first_name', 'supervisor_details.alias_name as alias_name', 'supervisor_details.middle_name as middle_name', 'supervisor_details.username as employee_code', 'supervisor_details.last_name as last_name', 'branch_details.name as branch_name', 'branch_details.branch_code as branch_code')
                ->leftjoin('employees as supervisor_details', 'resource_allocation.employee_id', '=', 'supervisor_details.id')
                ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'resource_allocation.branch_id')
                ->where('supervisor_details.status', '!=', 2)
                ->where('supervisor_details.admin_status', '!=', 1)
                ->where('resource_allocation.resource_type', '=', 'SUPERVISOR')
                ->where('resource_allocation.active', '=', 1)
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(supervisor_details.first_name like '$search_key%' or concat(supervisor_details.first_name,' ',supervisor_details.alias_name,' ',employees.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(supervisor_details.username like '$searchbycode%')");
                })
                ->when($branch, function ($query) use ($branch) {
                    return $query->where('branch_details.id', '=', $branch);
                })
                ->orderby('supervisor_details.created_at', 'DESC')
                ->groupby('supervisor_details.id')
                ->get();


        if ($excelorpdf == "Excel") {

            Excel::create('Supervisorbranchlist', function($excel) use($employees) {
                // Set the title
                $excel->setTitle('Supervisor Branch List');

                $excel->sheet('Supervisor Branch List', function($sheet) use($employees) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Supervisor Branch List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($employees); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $employees[$i]->employee_code);
                        $sheet->setCellValue('C' . $chrRow, $employees[$i]->first_name . ' ' . $employees[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $employees[$i]->branch_code . '-' . $employees[$i]->branch_name);

                        $sheet->cells('A' . $chrRow . ':D' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Supervisor Branch List</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                               </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($employees as $employee) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->employee_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->first_name . " " . $employee->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $employee->branch_code . "-" . $employee->branch_name . '</td>
                                     </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('Supervisor_Branch_List.pdf');
        }
    }

    public function supervisorsalesdetails(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $rating_id = '';
        if ($id) {
            $rating_id = \Crypt::decrypt($id);
        }

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');



        $sales = DB::table('pos_sales')
                ->select('pos_sales.total_sale as total_sale', 'employee_id', 'emp.first_name', 'emp.username', 'emp.alias_name', 'pos_date', 'branch.name as branchname')
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->orderby('pos_date', 'DESC')
                ->orderby('pos_sales.id', 'DESC')
                ->paginate($paginate);


        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();





        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordamnt = Input::get('sortordamnt');

            $employee_id = Input::get('empid');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');
            $region_id = Input::get('region_id');


            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                $sortOrdDefault = 'DESC';
            }


            $sales = DB::table('pos_sales')
                    ->select('pos_sales.total_sale as total_sale', 'employee_id', 'emp.first_name', 'emp.username', 'emp.alias_name', 'emp.last_name', 'pos_date', 'branch.name as branchname')
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('employee_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(emp.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('pos_sales.pos_date', $sortorddate);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordamnt, function ($query) use ($sortordamnt) {
                        return $query->orderby('pos_sales.total_sale', $sortordamnt);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_date', $sortOrdDefault);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.id', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('branchsales/supervisor_sales_graph/result', array('sales' => $sales));
        }

        return view('branchsales/supervisor_sales_graph/supervisorsales', array('sales' => $sales, 'empid' => $employee_id, 'from_date' => $first_day, 'to_date' => $last_day, 'region_id' => $region_id));
    }

    // Generate PDF funcion
    public function exportsupervisorsalesdetails() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortorddate = Input::get('sortorddate');
        $sortordbranch = Input::get('sortordbranch');
        $sortordamnt = Input::get('sortordamnt');

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');

        $sortOrdDefault = '';
        if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
            $sortOrdDefault = 'DESC';
        }


        $sales = DB::table('pos_sales')
                ->select('pos_sales.total_sale as total_sale', 'employee_id', 'emp.first_name', 'emp.username', 'emp.alias_name', 'emp.last_name', 'pos_date', 'branch.name as branchname', 'branch.branch_code as branch_code')
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(emp.username like '$searchbycode%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('pos_sales.pos_date', $sortorddate);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordamnt, function ($query) use ($sortordamnt) {
                    return $query->orderby('pos_sales.total_sale', $sortordamnt);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_date', $sortOrdDefault);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.id', $sortOrdDefault);
                })
                ->get();



        if ($excelorpdf == "Excel") {

            Excel::create('SupervisorDetails', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Supervisor Sales Report');

                $excel->sheet('Supervisor Sales Report', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Supervisor Sales Report');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Amount"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($sales); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                        $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->branchname);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));
                        $sheet->setCellValue('F' . $chrRow, $sales[$i]->total_sale);

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Supervisor Sales Report</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($sales as $sale) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->branchname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->total_sale . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('supervisor_sales_report.pdf');
        }
    }

    public function cashiersalesdetails(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $rating_id = '';
        if ($id) {
            $rating_id = \Crypt::decrypt($id);
        }

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');

//            if ($first_day != '') {
//            $first_day = explode('-', $first_day);
//            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
//        }
//        if ($last_day != '') {
//            $last_day = explode('-', $last_day);
//            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
//        }

        $sales = DB::table('pos_sales')
                ->select('pos_sales.cash_collection as total_sale', 'employee_id', 'emp.first_name', 'emp.username', 'emp.alias_name', 'pos_date', 'branch.name as branchname', 'branch.branch_code as branch_code')
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('pos_sales.cashier_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->orderby('pos_date', 'DESC')
                ->orderby('pos_sales.id', 'DESC')
                ->paginate($paginate);
        //->toSql();
        // echo $sales;
        // die();


        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();





        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordamnt = Input::get('sortordamnt');

            $employee_id = Input::get('empid');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');
            $region_id = Input::get('region_id');

//            if ($first_day != '') {
//            $first_day = explode('-', $first_day);
//            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
//        }
//        if ($last_day != '') {
//            $last_day = explode('-', $last_day);
//            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
//        }


            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                $sortOrdDefault = 'DESC';
            }



            $sales = DB::table('pos_sales')
                    ->select('pos_sales.cash_collection as total_sale', 'employee_id', 'emp.first_name', 'emp.username', 'emp.alias_name', 'pos_date', 'branch.name as branchname', 'branch.branch_code as branch_code')
                    ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('pos_sales.cashier_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(emp.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('pos_sales.pos_date', $sortorddate);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordamnt, function ($query) use ($sortordamnt) {
                        return $query->orderby('pos_sales.cash_collection', $sortordamnt);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_date', $sortOrdDefault);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.id', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('branchsales/report_graphs/cashier_sales_result', array('sales' => $sales));
        }

        return view('branchsales/report_graphs/cashiersales', array('sales' => $sales, 'empid' => $employee_id, 'from_date' => $first_day, 'to_date' => $last_day, 'region_id' => $region_id));
    }

    // Generate PDF funcion
    public function exportcashiersalesdetails() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortorddate = Input::get('sortorddate');
        $sortordbranch = Input::get('sortordbranch');
        $sortordamnt = Input::get('sortordamnt');

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');

//            if ($first_day != '') {
//            $first_day = explode('-', $first_day);
//            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
//        }
//        if ($last_day != '') {
//            $last_day = explode('-', $last_day);
//            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
//        }


        $sortOrdDefault = '';
        if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
            $sortOrdDefault = 'DESC';
        }


        $sales = DB::table('pos_sales')
                ->select('pos_sales.cash_collection as total_sale', 'employee_id', 'emp.first_name', 'emp.username', 'emp.alias_name', 'pos_date', 'branch.name as branchname', 'branch.branch_code as branch_code')
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('pos_sales.cashier_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(emp.username like '$searchbycode%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('pos_sales.pos_date', $sortorddate);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordamnt, function ($query) use ($sortordamnt) {
                    return $query->orderby('pos_sales.cash_collection', $sortordamnt);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_date', $sortOrdDefault);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.id', $sortOrdDefault);
                })
                ->get();



        if ($excelorpdf == "Excel") {

            Excel::create('CashierDetails', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Cashier Sales Report');

                $excel->sheet('Cashier Sales Report', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Cashier Sales Report');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Amount"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($sales); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                        $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->branchname);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));
                        $sheet->setCellValue('F' . $chrRow, Customhelper::numberformatter($sales[$i]->total_sale));

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Cashier Sales Report</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($sales as $sale) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->branchname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . Customhelper::numberformatter($sale->total_sale) . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('cashier_sales_report.pdf');
        }
    }

    public function cashiertipsdetails(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $rating_id = '';
        if ($id) {
            $rating_id = \Crypt::decrypt($id);
        }

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');

//            if ($first_day != '') {
//            $first_day = explode('-', $first_day);
//            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
//        }
//        if ($last_day != '') {
//            $last_day = explode('-', $last_day);
//            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
//        }

        $sales = DB::table('pos_sales')
                ->select('pos_sales.tips_collected as total_sale', 'employee_id', 'emp.first_name', 'emp.username', 'emp.alias_name', 'pos_date', 'branch.name as branchname', 'branch.branch_code as branch_code')
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('pos_sales.cashier_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->orderby('pos_date', 'DESC')
                ->orderby('pos_sales.id', 'DESC')
                ->paginate($paginate);
        //->toSql();
        //     echo $sales;
        // die();


        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();





        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordamnt = Input::get('sortordamnt');

            $employee_id = Input::get('empid');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');
            $region_id = Input::get('region_id');

//            if ($first_day != '') {
//            $first_day = explode('-', $first_day);
//            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
//        }
//        if ($last_day != '') {
//            $last_day = explode('-', $last_day);
//            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
//        }



            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                $sortOrdDefault = 'DESC';
            }



            $sales = DB::table('pos_sales')
                    ->select('pos_sales.tips_collected as total_sale', 'employee_id', 'emp.first_name', 'emp.username', 'emp.alias_name', 'pos_date', 'branch.name as branchname', 'branch.branch_code as branch_code')
                    ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('pos_sales.cashier_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(emp.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('pos_sales.pos_date', $sortorddate);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordamnt, function ($query) use ($sortordamnt) {
                        return $query->orderby('pos_sales.tips_collected', $sortordamnt);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_date', $sortOrdDefault);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.id', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('branchsales/report_graphs/cashier_tips_result', array('sales' => $sales));
        }

        return view('branchsales/report_graphs/cashiertips', array('sales' => $sales, 'empid' => $employee_id, 'from_date' => $first_day, 'to_date' => $last_day, 'region_id' => $region_id));
    }

    // Generate PDF funcion
    public function exportcashiertipsdetails() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortorddate = Input::get('sortorddate');
        $sortordbranch = Input::get('sortordbranch');
        $sortordamnt = Input::get('sortordamnt');

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');

//            if ($first_day != '') {
//            $first_day = explode('-', $first_day);
//            $first_day = $first_day[2] . '-' . $first_day[1] . '-' . $first_day[0];
//        }
//        if ($last_day != '') {
//            $last_day = explode('-', $last_day);
//            $last_day = $last_day[2] . '-' . $last_day[1] . '-' . $last_day[0];
//        }

        $sortOrdDefault = '';
        if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
            $sortOrdDefault = 'DESC';
        }

        $sales = DB::table('pos_sales')
                ->select('pos_sales.tips_collected as total_sale', 'employee_id', 'emp.first_name', 'emp.username', 'emp.alias_name', 'pos_date', 'branch.name as branchname', 'branch.branch_code as branch_code')
                ->leftjoin('employees as emp', 'pos_sales.cashier_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Cashier' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('pos_sales.cashier_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(emp.username like '$searchbycode%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('pos_sales.pos_date', $sortorddate);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordamnt, function ($query) use ($sortordamnt) {
                    return $query->orderby('pos_sales.tips_collected', $sortordamnt);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_date', $sortOrdDefault);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.id', $sortOrdDefault);
                })
                ->get();



        if ($excelorpdf == "Excel") {

            Excel::create('CashierTipsDetails', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Cashier Tips Report');

                $excel->sheet('Cashier Tips Report', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Cashier Tips Report');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Tip Amount"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($sales); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                        $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->branchname);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));

                        if ($sales[$i]->total_sale == "") {
                            $sales[$i]->total_sale = 0;
                        }

                        $sheet->setCellValue('F' . $chrRow, Customhelper::numberformatter($sales[$i]->total_sale));

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Cashier Sales Report</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($sales as $sale) {
                if ($sale->total_sale == "") {
                    $sale->total_sale = 0;
                }
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->branchname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . Customhelper::numberformatter($sale->total_sale) . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('cashier_tips_report.pdf');
        }
    }

    public function collectiondifferencedetails(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $rating_id = '';
        if ($id) {
            $rating_id = \Crypt::decrypt($id);
        }

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');
        $diff_id = Input::get('diff_id');


        if ($diff_id == "cash_diff") {

            $sales = DB::table('pos_sales')
                    ->select(DB::raw('CASE WHEN((pos_sales.cash_sale-pos_sales.cash_collection)) THEN (pos_sales.cash_sale-pos_sales.cash_collection) ELSE 0 END as total_sale,employee_id,emp.first_name,emp.username,emp.alias_name ,pos_date,branch.name as branchname,branch.branch_code as branch_code'))
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('pos_sales.employee_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->orderby('pos_date', 'DESC')
                    ->orderby('pos_sales.id', 'DESC')
                    ->paginate($paginate);
        } else {

            $sales = DB::table('pos_sales')
                    ->select(DB::raw('CASE WHEN((pos_sales.bank_sale-pos_sales.bank_collection)) THEN (pos_sales.bank_sale-pos_sales.bank_collection) ELSE 0 END as total_sale,employee_id,emp.first_name,emp.username,emp.alias_name ,pos_date,branch.name as branchname,branch.branch_code as branch_code'))
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('pos_sales.employee_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->orderby('pos_date', 'DESC')
                    ->orderby('pos_sales.id', 'DESC')
                    ->paginate($paginate);
        }


        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();





        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordamnt = Input::get('sortordamnt');

            $employee_id = Input::get('empid');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');
            $region_id = Input::get('region_id');
            $diff_id = Input::get('diff_id');

            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                $sortOrdDefault = 'DESC';
            }

            if ($diff_id == "cash_diff") {

                $sales = DB::table('pos_sales')
                        ->select(DB::raw('CASE WHEN((pos_sales.cash_sale-pos_sales.cash_collection)) THEN (pos_sales.cash_sale-pos_sales.cash_collection) ELSE 0 END as total_sale,employee_id,emp.first_name,emp.username,emp.alias_name ,pos_date,branch.name as branchname,branch.branch_code as branch_code'))
                        ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                        ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                        ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                        ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                        ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                        ->where('pos_sales.company_id', '=', $company_id)
                        ->where('pos_sales.employee_id', '=', $employee_id)
                        ->when($region_id, function ($query) use ($region_id) {
                            return $query->where('area.region_id', '=', $region_id);
                        })
                        ->when($search_key, function ($query) use ($search_key) {
                            return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                        })
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("(emp.username like '$searchbycode%')");
                        })
                        ->when($sortordname, function ($query) use ($sortordname) {
                            return $query->orderby('emp.first_name', $sortordname);
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('emp.username', $sortordcode);
                        })
                        ->when($sortorddate, function ($query) use ($sortorddate) {
                            return $query->orderby('pos_sales.pos_date', $sortorddate);
                        })
                        ->when($sortordbranch, function ($query) use ($sortordbranch) {
                            return $query->orderby('branch.name', $sortordbranch);
                        })
                        ->when($sortordamnt, function ($query) use ($sortordamnt) {
                            return $query->orderby('total_sale', $sortordamnt);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('pos_date', $sortOrdDefault);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('pos_sales.id', $sortOrdDefault);
                        })
                        ->paginate($paginate);
            } else {


                $sortOrdDefault = '';
                if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                    $sortOrdDefault = 'DESC';
                }

                $sales = DB::table('pos_sales')
                        ->select(DB::raw('CASE WHEN((pos_sales.bank_sale-pos_sales.bank_collection)) THEN (pos_sales.bank_sale-pos_sales.bank_collection) ELSE 0 END as total_sale,employee_id,emp.first_name,emp.username ,emp.alias_name,pos_date,branch.name as branchname,branch.branch_code as branch_code'))
                        ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                        ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                        ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                        ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                        ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                        ->where('pos_sales.company_id', '=', $company_id)
                        ->where('pos_sales.employee_id', '=', $employee_id)
                        ->when($region_id, function ($query) use ($region_id) {
                            return $query->where('area.region_id', '=', $region_id);
                        })
                        ->when($search_key, function ($query) use ($search_key) {
                            return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                        })
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("(emp.username like '$searchbycode%')");
                        })
                        ->when($sortordname, function ($query) use ($sortordname) {
                            return $query->orderby('emp.first_name', $sortordname);
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('emp.username', $sortordcode);
                        })
                        ->when($sortorddate, function ($query) use ($sortorddate) {
                            return $query->orderby('pos_sales.pos_date', $sortorddate);
                        })
                        ->when($sortordbranch, function ($query) use ($sortordbranch) {
                            return $query->orderby('branch.name', $sortordbranch);
                        })
                        ->when($sortordamnt, function ($query) use ($sortordamnt) {
                            return $query->orderby('total_sale', $sortordamnt);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('pos_date', $sortOrdDefault);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('pos_sales.id', $sortOrdDefault);
                        })
                        ->paginate($paginate);
            }


            return view('branchsales/report_graphs/collectiondifference_result', array('sales' => $sales));
        }

        return view('branchsales/report_graphs/collectiondifference', array('sales' => $sales, 'empid' => $employee_id, 'from_date' => $first_day, 'to_date' => $last_day, 'region_id' => $region_id, 'diff_id' => $diff_id));
    }

    // Generate PDF funcion
    public function exportcollectiondifferencedetails() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortorddate = Input::get('sortorddate');
        $sortordbranch = Input::get('sortordbranch');
        $sortordamnt = Input::get('sortordamnt');

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');
        $diff_id = Input::get('diff_id');


        $sortOrdDefault = '';
        if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
            $sortOrdDefault = 'DESC';
        }


        $report_name = "";
        if ($diff_id == "cash_diff") {
            $report_name = "Cash Difference Report";

            $sales = DB::table('pos_sales')
                    ->select(DB::raw('CASE WHEN((pos_sales.cash_sale-pos_sales.cash_collection)) THEN (pos_sales.cash_sale-pos_sales.cash_collection) ELSE 0 END as total_sale,employee_id,emp.first_name,emp.username,emp.alias_name ,pos_date,branch.name as branchname,branch.branch_code as branch_code'))
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('pos_sales.employee_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(emp.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('pos_sales.pos_date', $sortorddate);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordamnt, function ($query) use ($sortordamnt) {
                        return $query->orderby('total_sale', $sortordamnt);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_date', $sortOrdDefault);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.id', $sortOrdDefault);
                    })
                    ->get();



            if ($excelorpdf == "Excel") {

                Excel::create('Cash Difference Report', function($excel) use($sales) {
                    // Set the title
                    $excel->setTitle('Cash Difference Report');

                    $excel->sheet('Cash Difference Report', function($sheet) use($sales) {
                        // Sheet manipulation

                        $sheet->setCellValue('C3', 'Cash Difference Report');
                        $sheet->setHeight(3, 20);
                        $sheet->cells('A3:F3', function($cells) {
                            $cells->setBackground('#00CED1');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(14);
                        });

                        $chrRow = 6;

                        $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Amount"));
                        $sheet->setHeight(5, 15);
                        $sheet->cells('A5:F5', function($cells) {
                            $cells->setBackground('#6495ED');
                            $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        for ($i = 0; $i < count($sales); $i++) {
                            $sheet->setCellValue('A' . $chrRow, ($i + 1));
                            $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                            $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                            $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->branchname);
                            $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));
                            $sheet->setCellValue('F' . $chrRow, Customhelper::numberformatter($sales[$i]->total_sale));

                            $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                                $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $chrRow++;
                        }
                    });
                })->export('xls');
            } else {

                $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Cash Difference Report</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
                $slno = 0;
                foreach ($sales as $sale) {
                    $slno++;
                    $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->branchname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . Customhelper::numberformatter($sale->total_sale) . '</td>
                                </tr>';
                }
                $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($html_table);
                return $pdf->download('cash_difference_report.pdf');
            }
        } else {

            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                $sortOrdDefault = 'DESC';
            }



            $sales = DB::table('pos_sales')
                    ->select(DB::raw('CASE WHEN((pos_sales.bank_sale-pos_sales.bank_collection)) THEN (pos_sales.bank_sale-pos_sales.bank_collection) ELSE 0 END as total_sale,employee_id,emp.first_name,emp.username, emp.alias_name ,pos_date,branch.name as branchname,branch.branch_code as branch_code'))
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('pos_sales.employee_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(emp.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('pos_sales.pos_date', $sortorddate);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordamnt, function ($query) use ($sortordamnt) {
                        return $query->orderby('total_sale', $sortordamnt);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_date', $sortOrdDefault);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.id', $sortOrdDefault);
                    })
                    ->get();



            if ($excelorpdf == "Excel") {

                Excel::create('Bank Difference Report', function($excel) use($sales) {
                    // Set the title
                    $excel->setTitle('Bank Difference Report');

                    $excel->sheet('Bank Difference Report', function($sheet) use($sales) {
                        // Sheet manipulation

                        $sheet->setCellValue('C3', 'Bank Difference Report');
                        $sheet->setHeight(3, 20);
                        $sheet->cells('A3:F3', function($cells) {
                            $cells->setBackground('#00CED1');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(14);
                        });

                        $chrRow = 6;

                        $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Amount"));
                        $sheet->setHeight(5, 15);
                        $sheet->cells('A5:F5', function($cells) {
                            $cells->setBackground('#6495ED');
                            $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        for ($i = 0; $i < count($sales); $i++) {
                            $sheet->setCellValue('A' . $chrRow, ($i + 1));
                            $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                            $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                            $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->branchname);
                            $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));
                            $sheet->setCellValue('F' . $chrRow, Customhelper::numberformatter($sales[$i]->total_sale));

                            $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                                $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $chrRow++;
                        }
                    });
                })->export('xls');
            } else {

                $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Bank Difference Report</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
                $slno = 0;
                foreach ($sales as $sale) {
                    $slno++;
                    $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->branchname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . Customhelper::numberformatter($sale->total_sale) . '</td>
                                </tr>';
                }
                $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


                $pdf = App::make('dompdf.wrapper');
                $pdf->loadHTML($html_table);
                return $pdf->download('bank_difference_rport.pdf');
            }
        }
    }

    public function creditdetails(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $rating_id = '';
        if ($id) {
            $rating_id = \Crypt::decrypt($id);
        }

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');


        $sales = DB::table('pos_sales')
                ->select(DB::raw('(pos_sales.credit_sale) as total_sale,employee_id,emp.first_name,pos_date,emp.username,emp.alias_name,branch.branch_code,branch.name as branchname'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->orderby('pos_date', 'DESC')
                ->orderby('pos_sales.id', 'DESC')
                ->paginate($paginate);


        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();





        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordamnt = Input::get('sortordamnt');

            $employee_id = Input::get('empid');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');
            $region_id = Input::get('region_id');


            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                $sortOrdDefault = 'DESC';
            }


            $sales = DB::table('pos_sales')
                            ->select(DB::raw('(pos_sales.credit_sale) as total_sale,employee_id,pos_date,emp.first_name,emp.username,emp.alias_name,branch.name as branchname,branch.branch_code'))
                            ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                            ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                            ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                            ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                            ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                            ->where('pos_sales.company_id', '=', $company_id)
                            ->where('employee_id', '=', $employee_id)
                            ->when($region_id, function ($query) use ($region_id) {
                                return $query->where('area.region_id', '=', $region_id);
                            })
                            ->when($search_key, function ($query) use ($search_key) {
                                return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                            })
                            ->when($searchbycode, function ($query) use ($searchbycode) {
                                return $query->whereRaw("(emp.username like '$searchbycode%')");
                            })
                            ->when($sortordname, function ($query) use ($sortordname) {
                                return $query->orderby('emp.first_name', $sortordname);
                            })
                            ->when($sortordcode, function ($query) use ($sortordcode) {
                                return $query->orderby('emp.username', $sortordcode);
                            })
                            ->when($sortorddate, function ($query) use ($sortorddate) {
                                return $query->orderby('pos_sales.pos_date', $sortorddate);
                            })
                            ->when($sortordbranch, function ($query) use ($sortordbranch) {
                                return $query->orderby('branch.name', $sortordbranch);
                            })
                            ->when($sortordamnt, function ($query) use ($sortordamnt) {
                                return $query->orderby('total_sale', $sortordamnt);
                            })
                            ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                                return $query->orderby('pos_date', $sortOrdDefault);
                            }) > when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                                return $query->orderby('pos_sales.id', $sortOrdDefault);
                            })
                            ->paginate($paginate);

            return view('branchsales/report_graphs/creditsales_result', array('sales' => $sales));
        }

        return view('branchsales/report_graphs/creditsales', array('sales' => $sales, 'empid' => $employee_id, 'from_date' => $first_day, 'to_date' => $last_day, 'region_id' => $region_id));
    }

    // Generate PDF funcion
    public function exportcreditdetails() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortorddate = Input::get('sortorddate');
        $sortordbranch = Input::get('sortordbranch');
        $sortordamnt = Input::get('sortordamnt');

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');


        $sortOrdDefault = '';
        if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
            $sortOrdDefault = 'DESC';
        }


        $sales = DB::table('pos_sales')
                ->select(DB::raw('(pos_sales.credit_sale) as total_sale,employee_id,pos_date,emp.first_name,emp.username,emp.alias_name,branch.name as branchname,branch.branch_code'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(emp.username like '$searchbycode%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('pos_sales.pos_date', $sortorddate);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordamnt, function ($query) use ($sortordamnt) {
                    return $query->orderby('total_sale', $sortordamnt);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_date', $sortOrdDefault);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.id', $sortOrdDefault);
                })
                ->get();



        if ($excelorpdf == "Excel") {

            Excel::create('CreditSalesDetails', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Credit Sales Details');

                $excel->sheet('Credit Sales Details', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Credit Sales Details');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Amount"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($sales); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                        $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->branchname);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));
                        $sheet->setCellValue('F' . $chrRow, $sales[$i]->total_sale);

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Credit Sales Details</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($sales as $sale) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->branchname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->total_sale . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('creditsalesdetails.pdf');
        }
    }

    public function cashdetails(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $rating_id = '';
        if ($id) {
            $rating_id = \Crypt::decrypt($id);
        }

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');


        $sales = DB::table('pos_sales')
                ->select(DB::raw('(pos_sales.cash_collection) as total_sale,employee_id,emp.first_name,pos_date,emp.username,emp.alias_name,branch.branch_code,branch.name as branchname'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->orderby('pos_date', 'DESC')
                ->orderby('pos_sales.id', 'DESC')
                ->paginate($paginate);


        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();





        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordamnt = Input::get('sortordamnt');

            $employee_id = Input::get('empid');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');
            $region_id = Input::get('region_id');


            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                $sortOrdDefault = 'DESC';
            }





            $sales = DB::table('pos_sales')
                    ->select(DB::raw('(pos_sales.cash_collection) as total_sale,employee_id,emp.first_name,pos_date,emp.username,emp.alias_name,branch.branch_code,branch.name as branchname'))
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('employee_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(emp.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('pos_sales.pos_date', $sortorddate);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordamnt, function ($query) use ($sortordamnt) {
                        return $query->orderby('cash_collection', $sortordamnt);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.pos_date', $sortOrdDefault);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.id', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('branchsales/report_graphs/cashsales_result', array('sales' => $sales));
        }

        return view('branchsales/report_graphs/cashsalesdetails', array('sales' => $sales, 'empid' => $employee_id, 'from_date' => $first_day, 'to_date' => $last_day, 'region_id' => $region_id));
    }

    // Generate PDF funcion
    public function exportcashdetails() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortorddate = Input::get('sortorddate');
        $sortordbranch = Input::get('sortordbranch');
        $sortordamnt = Input::get('sortordamnt');

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');


        $sortOrdDefault = '';
        if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
            $sortOrdDefault = 'DESC';
        }


        $sales = DB::table('pos_sales')
                ->select(DB::raw('(pos_sales.cash_collection) as total_sale,employee_id,pos_date,emp.first_name,emp.username,emp.alias_name,branch.name as branchname,branch.branch_code'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(emp.username like '$searchbycode%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('pos_sales.pos_date', $sortorddate);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordamnt, function ($query) use ($sortordamnt) {
                    return $query->orderby('cash_collection', $sortordamnt);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.pos_date', $sortOrdDefault);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.id', $sortOrdDefault);
                })
                ->get();

        //  print_r($sales);
        //     die();

        if ($excelorpdf == "Excel") {

            Excel::create('CashSalesDetails', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Cash Sales Details');

                $excel->sheet('Cash Sales Details', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Cash Sales Details');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Amount"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($sales); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                        $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->branchname);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));
                        $sheet->setCellValue('F' . $chrRow, $sales[$i]->total_sale);

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Cash Sales Details</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($sales as $sale) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->branchname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->total_sale . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';

//print_r($html_table);
//die();
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('cashsalesdetails.pdf');
        }
    }

    public function carddetails(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $rating_id = '';
        if ($id) {
            $rating_id = \Crypt::decrypt($id);
        }

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');


        $sales = DB::table('pos_sales')
                ->select(DB::raw('(pos_sales.bank_collection) as total_sale,employee_id,emp.first_name,pos_date,emp.username,emp.alias_name,branch.branch_code,branch.name as branchname'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->orderby('pos_date', 'DESC')
                ->orderby('pos_sales.id', 'DESC')
                ->paginate($paginate);


        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();





        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordamnt = Input::get('sortordamnt');

            $employee_id = Input::get('empid');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');
            $region_id = Input::get('region_id');


            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                $sortOrdDefault = 'DESC';
            }




            $sales = DB::table('pos_sales')
                    ->select(DB::raw('(pos_sales.bank_collection) as total_sale,employee_id,emp.first_name,pos_date,emp.username,emp.alias_name,branch.branch_code,branch.name as branchname'))
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('employee_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(emp.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('pos_sales.pos_date', $sortorddate);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordamnt, function ($query) use ($sortordamnt) {
                        return $query->orderby('bank_collection', $sortordamnt);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.pos_date', $sortOrdDefault);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.id', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('branchsales/report_graphs/cardsales_result', array('sales' => $sales));
        }

        return view('branchsales/report_graphs/cardsalesdetails', array('sales' => $sales, 'empid' => $employee_id, 'from_date' => $first_day, 'to_date' => $last_day, 'region_id' => $region_id));
    }

    // Generate PDF funcion
    public function exportcarddetails() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortorddate = Input::get('sortorddate');
        $sortordbranch = Input::get('sortordbranch');
        $sortordamnt = Input::get('sortordamnt');

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');


        $sortOrdDefault = '';
        if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
            $sortOrdDefault = 'DESC';
        }


        $sales = DB::table('pos_sales')
                ->select(DB::raw('(pos_sales.bank_collection) as total_sale,employee_id,pos_date,emp.first_name,emp.username,emp.alias_name,branch.name as branchname,branch.branch_code'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(emp.username like '$searchbycode%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('pos_sales.pos_date', $sortorddate);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordamnt, function ($query) use ($sortordamnt) {
                    return $query->orderby('bank_collection', $sortordamnt);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.pos_date', $sortOrdDefault);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.id', $sortOrdDefault);
                })
                ->get();



        if ($excelorpdf == "Excel") {

            Excel::create('CardSalesDetails', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Card Sales Details');

                $excel->sheet('Card Sales Details', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Card Sales Details');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Amount"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($sales); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                        $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->branchname);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));
                        $sheet->setCellValue('F' . $chrRow, $sales[$i]->total_sale);

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Card Sales Details</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($sales as $sale) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->branchname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->total_sale . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('cardsalesdetails.pdf');
        }
    }

    public function openingdetails(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $rating_id = '';
        if ($id) {
            $rating_id = \Crypt::decrypt($id);
        }

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');


        $sales = DB::table('pos_sales')
                ->select(DB::raw('(pos_sales.opening_amount) as total_sale,employee_id,emp.first_name,pos_date,emp.username,emp.alias_name,branch.branch_code,branch.name as branchname'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->orderby('pos_date', 'DESC')
                ->orderby('pos_sales.id', 'DESC')
                ->paginate($paginate);


        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();





        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordamnt = Input::get('sortordamnt');

            $employee_id = Input::get('empid');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');
            $region_id = Input::get('region_id');


            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
                $sortOrdDefault = 'DESC';
            }




            $sales = DB::table('pos_sales')
                    ->select(DB::raw('(pos_sales.opening_amount) as total_sale,employee_id,emp.first_name,pos_date,emp.username,emp.alias_name,branch.branch_code,branch.name as branchname'))
                    ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('employee_id', '=', $employee_id)
                    ->when($region_id, function ($query) use ($region_id) {
                        return $query->where('area.region_id', '=', $region_id);
                    })
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(emp.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('pos_sales.pos_date', $sortorddate);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordamnt, function ($query) use ($sortordamnt) {
                        return $query->orderby('opening_amount', $sortordamnt);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.pos_date', $sortOrdDefault);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.id', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('branchsales/report_graphs/openingsales_result', array('sales' => $sales));
        }

        return view('branchsales/report_graphs/openingsalesdetails', array('sales' => $sales, 'empid' => $employee_id, 'from_date' => $first_day, 'to_date' => $last_day, 'region_id' => $region_id));
    }

    // Generate PDF funcion
    public function exportopeningdetails() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortorddate = Input::get('sortorddate');
        $sortordbranch = Input::get('sortordbranch');
        $sortordamnt = Input::get('sortordamnt');

        $employee_id = Input::get('empid');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');
        $region_id = Input::get('region_id');


        $sortOrdDefault = '';
        if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '') {
            $sortOrdDefault = 'DESC';
        }


        $sales = DB::table('pos_sales')
                ->select(DB::raw('(pos_sales.opening_amount) as total_sale,employee_id,pos_date,emp.first_name,emp.username,emp.alias_name,branch.name as branchname,branch.branch_code'))
                ->leftjoin('employees as emp', 'pos_sales.employee_id', '=', 'emp.id')
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('employee_id', '=', $employee_id)
                ->when($region_id, function ($query) use ($region_id) {
                    return $query->where('area.region_id', '=', $region_id);
                })
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(emp.username like '$searchbycode%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('pos_sales.pos_date', $sortorddate);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordamnt, function ($query) use ($sortordamnt) {
                    return $query->orderby('opening_amount', $sortordamnt);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.pos_date', $sortOrdDefault);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.id', $sortOrdDefault);
                })
                ->get();



        if ($excelorpdf == "Excel") {

            Excel::create('OpeningAmountDetails', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Opening Amount Details');

                $excel->sheet('Opening Amount Details', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Opening Amount Details');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Amount"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($sales); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                        $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->branchname);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));
                        $sheet->setCellValue('F' . $chrRow, $sales[$i]->total_sale);

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Opening Amount Details</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($sales as $sale) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->branchname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->total_sale . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('openingamountdetails.pdf');
        }
    }

    public function mealconsumptiondetails(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $rating_id = '';
        if ($id) {
            $rating_id = \Crypt::decrypt($id);
        }

        $branch_id = Input::get('branch_id');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');



        $sales = DB::table('master_resources as branchdetails')
                ->select(DB::raw('branchdetails.name,employees.username,employees.first_name,employees.alias_name,pos_date,jobshift.name as shiftname,pos_sales.meal_consumption as total_sale,branch_id,branchdetails.branch_code'))
                ->leftjoin('pos_sales', 'pos_sales.branch_id', '=', 'branchdetails.id')
                ->leftjoin('employees', 'employees.id', '=', 'pos_sales.employee_id')
                ->leftjoin('master_resources as jobshift', 'jobshift.id', '=', 'pos_sales.job_shift_id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('branchdetails.status', '=', 1)
                ->where('branchdetails.id', '=', $branch_id)
                ->orderby('pos_date', 'DESC')
                ->orderby('pos_sales.id', 'DESC')
                ->paginate($paginate);


        $allbranches = DB::table('master_resources')
                ->select('master_resources.id as branch_id', 'master_resources.name as branch_name', 'master_resources.branch_code as code')
                ->where('master_resources.company_id', '=', $company_id)
                ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
                ->get();





        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $search_key = Input::get('search_key');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortorddate = Input::get('sortorddate');
            $sortordbranch = Input::get('sortordbranch');
            $sortordamnt = Input::get('sortordamnt');
            $sortordshift = Input::get('sortordshift');

            $branch_id = Input::get('branch_id');
            $first_day = Input::get('from_date');
            $last_day = Input::get('to_date');


            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '' && $sortordshift = "") {
                $sortOrdDefault = 'DESC';
            }




            $sales = DB::table('master_resources as branchdetails')
                    ->select(DB::raw('branchdetails.name,employees.username,employees.first_name,employees.alias_name,pos_date,jobshift.name as shiftname,pos_sales.meal_consumption as total_sale,branch_id,branchdetails.branch_code'))
                    ->leftjoin('pos_sales', 'pos_sales.branch_id', '=', 'branchdetails.id')
                    ->leftjoin('employees', 'employees.id', '=', 'pos_sales.employee_id')
                    ->leftjoin('master_resources as jobshift', 'jobshift.id', '=', 'pos_sales.job_shift_id')
                    ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->where('pos_sales.company_id', '=', $company_id)
                    ->where('branchdetails.status', '=', 1)
                    ->where('branchdetails.id', '=', $branch_id)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(emp.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('emp.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('emp.username', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('pos_sales.pos_date', $sortorddate);
                    })
                    ->when($sortordbranch, function ($query) use ($sortordbranch) {
                        return $query->orderby('branch.name', $sortordbranch);
                    })
                    ->when($sortordamnt, function ($query) use ($sortordamnt) {
                        return $query->orderby('meal_consumption', $sortordamnt);
                    })
                    ->when($sortordshift, function ($query) use ($sortordshift) {
                        return $query->orderby('jobshift.name', $sortordshift);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.pos_date', $sortOrdDefault);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('pos_sales.id', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('branchsales/report_graphs/mealconsumption_result', array('sales' => $sales));
        }


        return view('branchsales/report_graphs/mealconsumptiondetails', array('sales' => $sales, 'branch_id' => $branch_id, 'from_date' => $first_day, 'to_date' => $last_day));
    }

    // Generate PDF funcion
    public function exportmealconsumptiondetails() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }


        $search_key = Input::get('search_key');
        $searchbycode = Input::get('searchbycode');
        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortorddate = Input::get('sortorddate');
        $sortordbranch = Input::get('sortordbranch');
        $sortordamnt = Input::get('sortordamnt');
        $sortordshift = Input::get('sortordshift');

        $branch_id = Input::get('branch_id');
        $first_day = Input::get('from_date');
        $last_day = Input::get('to_date');


        $sortOrdDefault = '';
        if ($sortordname == '' && $sortordcode == '' && $sortorddate == '' && $sortordbranch == '' && $sortordamnt == '' && $sortordshift = "") {
            $sortOrdDefault = 'DESC';
        }

        $sales = DB::table('master_resources as branchdetails')
                ->select(DB::raw('branchdetails.name,employees.username,employees.first_name,employees.alias_name,pos_date,jobshift.name as shiftname,pos_sales.meal_consumption as total_sale,branch_id,branchdetails.branch_code'))
                ->leftjoin('pos_sales', 'pos_sales.branch_id', '=', 'branchdetails.id')
                ->leftjoin('employees', 'employees.id', '=', 'pos_sales.employee_id')
                ->leftjoin('master_resources as jobshift', 'jobshift.id', '=', 'pos_sales.job_shift_id')
                ->whereraw("date(pos_sales.pos_date)>='$first_day' AND date(pos_sales.pos_date)<='$last_day' and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('pos_sales.company_id', '=', $company_id)
                ->where('branchdetails.status', '=', 1)
                ->where('branchdetails.id', '=', $branch_id)
                ->when($search_key, function ($query) use ($search_key) {
                    return $query->whereRaw("(emp.first_name like '$search_key%' or concat(emp.first_name,' ',emp.alias_name,' ',emp.last_name) like '$search_key%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(emp.username like '$searchbycode%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('emp.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('emp.username', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('pos_sales.pos_date', $sortorddate);
                })
                ->when($sortordbranch, function ($query) use ($sortordbranch) {
                    return $query->orderby('branch.name', $sortordbranch);
                })
                ->when($sortordamnt, function ($query) use ($sortordamnt) {
                    return $query->orderby('meal_consumption', $sortordamnt);
                })
                ->when($sortordshift, function ($query) use ($sortordshift) {
                    return $query->orderby('jobshift.name', $sortordshift);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.pos_date', $sortOrdDefault);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('pos_sales.id', $sortOrdDefault);
                })
                ->get();




        if ($excelorpdf == "Excel") {

            Excel::create('MealConsumptionDetails', function($excel) use($sales) {
                // Set the title
                $excel->setTitle('Meal Consumption Details');

                $excel->sheet('Meal Consumption Details', function($sheet) use($sales) {
                    // Sheet manipulation

                    $sheet->setCellValue('D3', 'Meal Consumption Details');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Code', 'Employee Name', 'Branch', "Collection Date", "Shift", "Amount"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:G5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($sales); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $sales[$i]->username);
                        $sheet->setCellValue('C' . $chrRow, $sales[$i]->first_name . ' ' . $sales[$i]->alias_name);
                        $sheet->setCellValue('D' . $chrRow, $sales[$i]->branch_code . '-' . $sales[$i]->name);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($sales[$i]->pos_date)));
                        $sheet->setCellValue('F' . $chrRow, $sales[$i]->shiftname);
                        $sheet->setCellValue('G' . $chrRow, $sales[$i]->total_sale);

                        $sheet->cells('A' . $chrRow . ':G' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Meal Consumption Details</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Employee Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Branch</td>
                                <td style="padding:10px 5px;color:#fff;"> Collection Date </td>
                                 <td style="padding:10px 5px;color:#fff;"> Job Shift </td>
                                <td style="padding:10px 5px;color:#fff;"> Amount </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno = 0;
            foreach ($sales as $sale) {
                $slno++;
                $html_table .= '<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->username . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->first_name . " " . $sale->alias_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->branch_code . "-" . $sale->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($sale->pos_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->shiftname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $sale->total_sale . '</td>
                                </tr>';
            }
            $html_table .= '</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mealconsumptiondetails.pdf');
        }
    }

}
