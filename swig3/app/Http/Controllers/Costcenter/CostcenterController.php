<?php

namespace App\Http\Controllers\Costcenter;

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
use App\Models\Masterresources;
use App\Models\Branch_fixed_cost;
use DB;
use App;
use PDF;
use Excel;

class CostcenterController extends Controller {

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $branch = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'BRANCH', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        $costs = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'COST_NAME', 'status' => 1])
                ->orderby('name', 'ASC')->get();
        
        return view('costcenter/cost_allocation/add', array('branches'=>$branch,'costs'=>$costs));
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            $arrCostList = Input::get('arrCostList');
            $arrCostList = json_decode($arrCostList);

            foreach ($arrCostList as $list) {
                
               
                $costmodel = new Branch_fixed_cost();
                $costmodel->branch_id = $arraData->branch_id;
                $costmodel->cost_id = $list->costid;
                $costmodel->cost_amount = $list->costamount;
                $costmodel->save();
            }

            Toastr::success("Branch Cost Allocation Saved Successfully!", $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function keeplivedata() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $costnames = DB::table('branch_fixed_cost as bfc')
                ->select('bfc.cost_name as cost_name')
                ->whereRaw("bfc.status=1 AND cost_name!=''")
                ->distinct("cost_name")
                ->get()->pluck("cost_name")->toArray();
        
       
        foreach ($costnames as $value) {
            
            $masterresourcemodel = new Masterresources;
            $masterresourcemodel->name = $value;
            $masterresourcemodel->resource_type = 'COST_NAME';
            $masterresourcemodel->company_id = $company_id;
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();
        }
        
        echo "<pre>";
        print_r($costnames);
        
        echo "<br><br>".count($costnames)." Cost names created<br>";
        
        $costs = DB::table('master_resources')
                ->select('master_resources.id','name')
                ->where(['resource_type' => 'COST_NAME', 'status' => 1])
                ->get();
        
        $rowcounts=0;
        foreach ($costs as $cost) {
            DB::table('branch_fixed_cost')
                ->whereRaw("cost_name='$cost->name'")
                ->update(['cost_id' => $cost->id]);
            
            $rowcounts += DB::table('branch_fixed_cost')
                        ->whereRaw("cost_name='$cost->name'")->count();
        }
        
        die("Total $rowcounts rows updated with cost name id in branch cost table");
    }
 
}