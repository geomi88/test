<?php

namespace App\Http\Controllers\Costcenter;

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
use Customhelper;

class CostcenterreportController extends Controller {

    
    
    public function index() {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $costnames=DB::table('master_resources')
                ->select('master_resources.id','name as cost_name')
                ->where(['resource_type' => 'COST_NAME', 'status' => 1])
                ->get();
        
        $cost = DB::table('branch_fixed_cost as bfc')
                ->select('bfc.branch_id',DB::raw('sum(bfc.cost_amount) as cost_amount,m.name as branch_name,m.branch_code as code'))
                ->leftjoin('master_resources as m', 'bfc.branch_id', '=', 'm.id')
                ->groupby('bfc.branch_id')
                ->orderby('cost_amount','DESC')
                ->get();
        
        $totalcost=Customhelper::numberformatter($cost->sum('cost_amount'));
        $arrBranchCost=array();
        foreach ($cost as $value) {
            $arrBranchCost[$value->branch_id]=array(
                                                    "branch_id"=>$value->branch_id,
                                                    "branch_name"=>$value->branch_name,
                                                    "code"=>$value->code,
                                                    "cost_amount"=>$value->cost_amount
                                                );
        }
        
        // All employees
        $allbranches = DB::table('master_resources')
              ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
              ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
              ->get();
        
        foreach ($allbranches as $value) {
            if(!key_exists($value->branch_id, $arrBranchCost)){
                $arrBranchCost[$value->branch_id]=array(
                                                    "branch_id"=>$value->branch_id,
                                                    "branch_name"=>$value->branch_name,
                                                    "code"=>$value->code,
                                                    "cost_amount"=>0
                                                );
            }
            
        }

        
        // All employees
        $totalbranches = DB::table('master_resources')
              ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
              ->count();
      $totalbranches=  Customhelper::numberformatter($totalbranches);
      
      
        $arrbranches = array();
        $arrsales = '';
        foreach ($arrBranchCost as $data) {
            $arrbranches[]=$data['code']." : ".$data['branch_name'];
            $arrsales=$arrsales.$data['cost_amount'].",";
        }
        
        $arrsales=rtrim($arrsales, ",");
        
        if(count($arrbranches)>8){
            $branchcount=count($arrbranches);
            $branchcount=$branchcount*60;
        }else{
            $branchcount=480;
        }
        
        $arrbranches = array_values($arrbranches);
        $arrbranches = json_encode($arrbranches);

        return view('costcenter/costcenter_report/index', array('costnames'=>$costnames,
            'arrbranches' => $arrbranches,'arrsales'=>$arrsales,"totalcost"=>$totalcost,
            'branchcount'=>$branchcount,'totalcount'=>$totalbranches,'selectedcostid'=>'','selectedcostname'=>''));
    }
    
    public function getbranchwisecost() {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $selectedcostid = Input::get('selectedcostid');
      
        $costnames=DB::table('master_resources')
                ->select('master_resources.id','name as cost_name')
                ->where(['resource_type' => 'COST_NAME', 'status' => 1])
                ->get();
        
        $selectedcostname='';
        if($selectedcostid!=''){
            $selectedcostname=DB::table('master_resources')
                ->select('name as cost_name')
                ->whereRaw("id=$selectedcostid")
                ->first()->cost_name;
        }
        
        
        $cost = DB::table('branch_fixed_cost as bfc')
                ->select('bfc.branch_id',DB::raw('sum(bfc.cost_amount) as cost_amount,m.name as branch_name,m.branch_code as code'))
                ->leftjoin('master_resources as m', 'bfc.branch_id', '=', 'm.id')
                ->when($selectedcostid, function ($query) use ($selectedcostid) {
                    return $query->whereRaw("(bfc.cost_id=$selectedcostid)");
                })
                ->groupby('bfc.branch_id')
                ->orderby('cost_amount','DESC')
                ->get();
        
        $totalcost=Customhelper::numberformatter($cost->sum('cost_amount'));
        $arrBranchCost=array();
        foreach ($cost as $value) {
            $arrBranchCost[$value->branch_id]=array(
                                                    "branch_id"=>$value->branch_id,
                                                    "branch_name"=>$value->branch_name,
                                                    "code"=>$value->code,
                                                    "cost_amount"=>$value->cost_amount
                                                );
        }
        
        // All employees
        $allbranches = DB::table('master_resources')
              ->select('master_resources.id as branch_id','master_resources.name as branch_name','master_resources.branch_code as code')
              ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
              ->get();
        
        foreach ($allbranches as $value) {
            if(!key_exists($value->branch_id, $arrBranchCost)){
                $arrBranchCost[$value->branch_id]=array(
                                                    "branch_id"=>$value->branch_id,
                                                    "branch_name"=>$value->branch_name,
                                                    "code"=>$value->code,
                                                    "cost_amount"=>0
                                                );
            }
            
        }

        
        // All employees
        $totalbranches = DB::table('master_resources')
              ->whereRaw("master_resources.status=1 AND master_resources.resource_type='BRANCH'")
              ->count();
        
       $totalbranches= Customhelper::numberformatter($totalbranches);
        $arrbranches = array();
        $arrsales = '';
        foreach ($arrBranchCost as $data) {
            $arrbranches[]=$data['code']." : ".$data['branch_name'];
            $arrsales=$arrsales.$data['cost_amount'].",";
        }
        
        $arrsales=rtrim($arrsales, ",");
        
        if(count($arrbranches)>8){
            $branchcount=count($arrbranches);
            $branchcount=$branchcount*60;
        }else{
            $branchcount=480;
        }
        
        $arrbranches = array_values($arrbranches);
        $arrbranches = json_encode($arrbranches);

        return view('costcenter/costcenter_report/index', array('costnames'=>$costnames,
            'arrbranches' => $arrbranches,'arrsales'=>$arrsales,"totalcost"=>$totalcost,
            'branchcount'=>$branchcount,'totalcount'=>$totalbranches,'selectedcostid'=>$selectedcostid,'selectedcostname'=>$selectedcostname));
    }
 
}
