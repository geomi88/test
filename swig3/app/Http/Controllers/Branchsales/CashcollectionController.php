<?php

namespace App\Http\Controllers\Branchsales;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
//use Illuminate\Pagination\Paginator;
use App\Models\Masterresources;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Pos_sale;
use App\Models\Cash_collection;
use DB;
use App;
use PDF;
use Excel;
use Customhelper;

class CashcollectionController extends Controller {

    public function add() {
        $login_id = Session::get('login_id');
        
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');
        
        $pos_sales = array();
        if ($from_date != '' && $to_date != '') {
            $from_date = explode('-',$from_date);
            $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
            $to_date = explode('-',$to_date);
            $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
        
            DB::table('pos_sales')
                    ->whereRaw("employee_id=$login_id and (pos_date BETWEEN '$from_date' and '$to_date') and cash_collection_final_status=0")
                    ->update(['cash_collection_temp_status' => 0]);
            $pos_sales = DB::table('pos_sales')
                    ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname',DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                    ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                    ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id' )
                    ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                    ->whereRaw("pos_sales.employee_id=$login_id and (date(pos_sales.pos_date) BETWEEN '$from_date' and '$to_date') and pos_sales.cash_collection_final_status=0 AND pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->get();
        }
        $cashieremployees = DB::table('employees')
                ->select('employees.*')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('master_resources.name', '=', 'Top_Cashier')->where('master_resources.resource_type', '=', 'JOB_POSITION')->where('employees.status', '=', 1)
                ->get();
        $banks = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'BANK', 'status' => 1])->get();
        $employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->join('country', 'country.id', '=', 'employees.nationality')
                    ->where('employees.id', '=', Session::get('login_id'))
                    ->first();
    
        if ($employee_details->admin_status != 1) {
            $loggedin_employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->join('country', 'country.id', '=', 'employees.nationality')
                    ->where('employees.id', '=', Session::get('login_id'))
                    ->first();
             
        } else {
            $loggedin_employee_details = $employee_details;
        }
      
        return view('branchsales/cash_collection/add', array('cashieremployees' => $cashieremployees, 'loggedin_employee_details' => $loggedin_employee_details, 'pos_sales' => $pos_sales, 'from_date' => Input::get('from_date'), 'to_date' => Input::get('to_date'),'banks' => $banks));
    }

    public function store() {
        try {
            $data = Input::all();
            
            $pos_ids = implode(",", $data['pos_ids']);
            $cashcollectionmodel = new Cash_collection;
            $cashcollectionmodel->pos_ids = $pos_ids;
            $cashcollectionmodel->supervisor_id = Session::get('login_id');
            if(Input::get('payment')=='TOP_CASHIER')
            {
            $cashcollectionmodel->cashier_id = Input::get('top_cashier_id');
            }
            else
            {
            $cashcollectionmodel->bank_id = Input::get('bank_id');
            $cashcollectionmodel->transfer_status = 1;
            }
            $cashcollectionmodel->ref_no = Input::get('ref_no');
            $cashcollectionmodel->amount = Input::get('amount');
            $cashcollectionmodel->submitted_by = 'SUPERVISOR';
            $cashcollectionmodel->submitted_to = Input::get('payment');
            
            $cashcollectionmodel->save();
            if (isset($data['pos_ids'])) {
                foreach ($data['pos_ids'] as $pos_id) {
                    DB::table('pos_sales')
                    ->whereRaw("id = $pos_id")
                    ->update(['cash_collection_final_status' => 1]);
                }
            }
            Toastr::success('Successfully Added!', $title = null, $options = []);
            return Redirect::to('branchsales/cash_collection/add');
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('branchsales/cash_collection/add');
        }
    }

    public function changecollectionstatus() {
        $selected_pos = Input::get('selected_pos');
        $selected_pos = json_decode($selected_pos, true);
        $login_id = Session::get('login_id');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');
        $status = Input::get('status');
        foreach ($selected_pos as $pos_id) {
            DB::table('pos_sales')
                    ->where('id', $pos_id)
                    ->update(['cash_collection_temp_status' => $status]);
        }
        $from_date = explode('-',$from_date);
        $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
        $to_date = explode('-',$to_date);
        $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
        $pos_sales = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_name',DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->whereRaw("pos_sales.employee_id=$login_id and (date(pos_sales.pos_date) BETWEEN '$from_date' and '$to_date') and pos_sales.cash_collection_temp_status=1 and pos_sales.cash_collection_final_status=0 AND pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->get();
        echo '<div class="listHolderType1"><div class="listerType1">
                <table style="width: 188%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Date</td>
                                <td>Branch Name</td>
                                <td>Shift</td>
                                <td>Total Sale</td>
                                <td>Collected Cash</td>
                                <td>Credit Sale</td>
                                <td>Bank Sale</td>
                                <td>Difference</td> 
                                <td>Staff Meal Consumption</td>
                                <td>Reason</td>
                                <td>Remove</td>
                            </tr>
                        </thead>
                        <div>
                        <tbody>';
        $total_cash = 0;
        foreach ($pos_sales as $pos_sale) {
            $total_cash = $total_cash+$pos_sale->cash_collection;
            echo '<tr><td>' . date("d-m-Y", strtotime($pos_sale->pos_date)). '</td>';
            echo '<td>' . $pos_sale->branch_name . '</td>';
            echo '<td>' . $pos_sale->jobshift_name . '</td>';
            echo '<td>' . $pos_sale->total_sale . '</td>';
            echo '<td>' . $pos_sale->cash_collection . '</td>';
            echo '<td>' . $pos_sale->credit_sale . '</td>';
            echo '<td>' . $pos_sale->bank_sale . '</td>';
            echo '<td>' . $pos_sale->difference . '</td>';
            echo '<td>' . $pos_sale->meal_consumption . '</td>';
            echo '<td>' . $pos_sale->reason . '</td>';
            echo '<td><a class="remove_pos_collected" id="remove_pos_collected" href="javascript:void(0);" value="'.$pos_sale->id.'">Remove</a>
                  </td>';
            echo'</tr> <input type="hidden" name="pos_ids[]" value="'.$pos_sale->id.'">';
        }
        echo '</tbody></table><div class="commonLoaderV1"></div></div>					
            </div>
            <input type="hidden" name="amount" value="'.$total_cash.'">
            <h4 class="blockHeadingV1 spacingBtm2 alignRight">Total : '.$total_cash.'</h4>';
    }

    public function notcollectedpos() {
        $login_id = Session::get('login_id');
        $from_date = Input::get('from_date');
        $to_date = Input::get('to_date');

        $from_date = explode('-',$from_date);
        $from_date = $from_date[2].'-'.$from_date[1].'-'.$from_date[0];
            
        $to_date = explode('-',$to_date);
        $to_date = $to_date[2].'-'.$to_date[1].'-'.$to_date[0];
        $pos_sales = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_name',DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->whereRaw("pos_sales.employee_id=$login_id and (date(pos_sales.pos_date) BETWEEN '$from_date' and '$to_date') and pos_sales.cash_collection_temp_status=0 AND pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->get();
        echo '<table style="width: 190%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Date</td>
                                <td>Branch Name</td>
                                <td>Shift</td>
                                <td>Total Sale</td>
                                <td>Collected Cash</td>
                                <td>Credit Sale</td>
                                <td>Bank Sale</td>
                                <td>Difference</td>
                                <td>Staff Meal Consumption</td>
                                <td>Reason</td>
                                <td class="alignCenter"><label class="listSelectAll"><input type="checkbox" >Select All</label></td>
                            </tr>
                        </thead>
                        <div>
                        <tbody>';
        foreach ($pos_sales as $pos_sale) {

            echo '<tr><td>' . date("d-m-Y", strtotime($pos_sale->pos_date)) . '</td>';
            echo '<td>' . $pos_sale->branch_name . '</td>';
            echo '<td>' . $pos_sale->jobshift_name . '</td>';
            echo '<td>' . $pos_sale->total_sale . '</td>';
            echo '<td>' . $pos_sale->cash_collection . '</td>';
            echo '<td>' . $pos_sale->credit_sale . '</td>';
            echo '<td>' . $pos_sale->bank_sale . '</td>';
            echo '<td>' . $pos_sale->difference . '</td>';       
            echo '<td>' . $pos_sale->meal_consumption . '</td>';
            echo '<td>' . $pos_sale->reason . '</td>';
            echo '<td  class="alignCenter"><input type="checkbox" class="alltSelected" name="selected_pos[]" value="'.$pos_sale->id.'" id="selected_pos[]">
                  </td>';
            echo'</tr> ';
        }
        echo '</tbody></table><div class="commonLoaderV1"></div>';
    }
    
    public function getcashierdetails() {
        $top_cashier_id = Input::get('top_cashier_id');

        $employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->join('country', 'country.id', '=', 'employees.nationality')
                    ->where('employees.id', '=', $top_cashier_id)
                    ->first();
        $pic_url = $employee_details->profilepic;
        $employee_name = $employee_details->first_name . " " . $employee_details->alias_name;
        $country_name = $employee_details->country_name;
        $flag_name = $employee_details->flag_name;
        echo '<figure class="imgHolder"><img src="'.$pic_url.'" alt="Profile"></figure>
                            <div class="details">
                                <b>'.$employee_name.'</b>
                                <p>Designation : Top cashier</p>
                            </div>
                            <div class="customClear"></div>
                            <figure class="flagHolder">
                                <img src="../../images/flags/'.$flag_name.'" alt="Flag">
                                <figcaption>'.$country_name.'</figcaption>
                            </figure>';
    }
    
    public function topcashierfund(Request $request) {
        $login_id = Session::get('login_id');
        
        $cashier_collections = DB::table('cash_collection')
                    ->select('cash_collection.*', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.username as username')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
                    ->whereRaw("cash_collection.cashier_id=$login_id and cash_collection.transfer_status=0")
                    ->get();
        
        
        $banks = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'BANK', 'status' => 1])->get();
        
        
          
        if ($request->ajax()) {
            $searchkey = Input::get('searchkey');
            
            $aorder = Input::get('aorder');
            
            $amount = Input::get('aamount');
            $empSearch = Input::get('empSearch');
            
           $created = Input::get('startdate');
           $ended = Input::get('enddate');
           if($created!='')
           {
               $created = explode('-',$created);
               $created = $created[2].'-'.$created[1].'-'.$created[0];
           }
           if($ended!='')
           {
               $ended = explode('-',$ended);
               $ended = $ended[2].'-'.$ended[1].'-'.$ended[0];
            }
           
            
             $cashier_collections = DB::table('cash_collection')
                    ->select('cash_collection.*', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.username as username')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
                    ->whereRaw("cash_collection.cashier_id=$login_id and cash_collection.transfer_status=0")
                  ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%')");
                    })
                    ->when($amount, function ($query) use ($amount, $aorder) {
                        return $query->whereRaw("cash_collection.amount $aorder $amount ");
                    })
                    ->when($created, function ($query) use ($created, $ended) {
                        //echo "pos_sales.created_at >= '$created' AND pos_sales.created_at<= '$ended' ";
                        return $query->whereRaw("date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended' ");
                    })
                    ->when($empSearch, function ($query) use ($empSearch) {
                        return $query->whereRaw("(employee_details.username like '$empSearch%')");
                    })
                    ->get();
           
                  
           return view('branchsales/cash_collection/cash_collection_result', array('cashier_collections' => $cashier_collections,'banks' => $banks));
        }
        
        
        return view('branchsales/cash_collection/topcashierfund', array('cashier_collections' => $cashier_collections,'banks' => $banks));
    }
    
    public function transferfund() {
        try {
            $input = Input::all();
            $strCollectionIds = $input['strCollectionIds'];
            $bank_id = Input::get('bank_id');
            $ref_no = Input::get('ref_no');
            $comment = Input::get('comment');
            
            $subfolder = "topcashiercollection";
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            
            $doc_url = '';
            if (Input::hasFile('attachment')) {
                $attach = Input::file('attachment');
                $extension = time() . '.' . $attach->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world.'/';
                $filePath = $filePath . $subfolder . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($attach), 'public');
                $doc_url = Storage::disk('s3')->url($filePath);
            }
            
            $cash = DB::table('cash_collection')
                    ->whereRaw("cash_collection.id IN ($strCollectionIds)")
                    ->update(['bank_id' => $bank_id, 'ref_no' => $ref_no,'top_cashier_file' => $doc_url, 'transfer_status' => 1,'comment'=>$comment]);

            Toastr::success('Successfully Transfered!', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    public function accounts(Request $request) {
        $login_id = Session::get('login_id');
         $paginate = Config::get('app.PAGINATE');
//        $cash_collections1 = DB::table('cash_collection')
//                    ->select('cash_collection.*','employee_details.username as username', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.job_description as job_description','bank_details.name as bank_name')
//                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
//                    ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
//                    ->whereRaw("cash_collection.verified_status=0 and cash_collection.transfer_status=1");
//                     ->paginate($paginate);
//        
         
      
         $cash_collections="select `cash_collection`.*, `employee_details`.`username` as `username`, `employee_details`.`first_name` as `employee_fname`,`employee_job`.`name` as `job_description`, `employee_details`.`alias_name` as `employee_aname`,  `bank_details`.`name` as `bank_name` from `cash_collection` inner join `employees` as `employee_details` on `employee_details`.`id` = `cash_collection`.`supervisor_id` inner join `master_resources` as `bank_details` on `bank_details`.`id` = `cash_collection`.`bank_id` inner join `master_resources` as `employee_job` on `employee_details`.`job_position` = `employee_job`.`id` where  cash_collection.cashier_id is NULL and cash_collection.verified_status=0 and cash_collection.transfer_status=1 union select `cash_collection`.*, `employee_details`.`username` as `username`, `employee_details`.`first_name` as `employee_fname`,`employee_job`.`name` as `job_description`, `employee_details`.`alias_name` as `employee_aname`, `bank_details`.`name` as `bank_name` from `cash_collection` inner join `employees` as `employee_details` on `employee_details`.`id` = `cash_collection`.`cashier_id` inner join `master_resources` as `bank_details` on `bank_details`.`id` = `cash_collection`.`bank_id` inner join `master_resources` as `employee_job` on `employee_details`.`job_position` = `employee_job`.`id` where cash_collection.cashier_id is NOT NULL and cash_collection.verified_status=0 and cash_collection.transfer_status=1";
       
       
       $page = 1;
$perPage = 10;
$cash_collections = DB::select($cash_collections);
$currentPage = Input::get('page', 1) - 1;

$total=count($cash_collections);
$pagedData = array_slice($cash_collections, $currentPage * $perPage, $perPage);
$lastPage =  ceil(count($cash_collections) / $perPage);
//$cash_collections =  new Paginator($pagedData, count($cash_collections), $perPage);
$cash_collections = new LengthAwarePaginator($pagedData, count($cash_collections), $perPage);


//$paginate = $cash_collections;


         $banks = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'BANK', 'status' => 1])->get();
         
         if($request->ajax()){
             
             
             $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchkey = Input::get('searchkey');
            
            $bank_id = Input::get('bank_id');
            $aorder = Input::get('aorder');
            
            $amount = Input::get('aamount');
            
           $created = Input::get('startdate');
           $ended = Input::get('enddate');
           if($created!='')
           {
               $created = explode('-',$created);
               $created = $created[2].'-'.$created[1].'-'.$created[0];
           }
           if($ended!='')
           {
               $ended = explode('-',$ended);
               $ended = $ended[2].'-'.$ended[1].'-'.$ended[0];
            }
           
            
//            $cash_collections = DB::table('cash_collection')
//                    ->select('cash_collection.*','employee_details.username as username', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.job_description as job_description','bank_details.name as bank_name')
//                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
//                    ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
//                    ->whereRaw("cash_collection.verified_status=0 and cash_collection.transfer_status=1")
//                    ->when($searchkey, function ($query) use ($searchkey) {
//                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%')");
//                    })
//                     ->when($bank_id, function ($query) use ($bank_id) {
//                    return $query->where('cash_collection.bank_id', '=', $bank_id);
//                      })
//                    ->when($amount, function ($query) use ($amount, $aorder) {
//                        return $query->whereRaw("cash_collection.amount $aorder $amount ");
//                    })
//                    ->when($created, function ($query) use ($created, $ended) {
//                        //echo "pos_sales.created_at >= '$created' AND pos_sales.created_at<= '$ended' ";
//                        return $query->whereRaw("date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended' ");
//                    })
//       
//                    ->paginate($paginate);
          
$cash_collections="select `cash_collection`.*, `employee_details`.`username` as `username`, "
        . "`employee_details`.`first_name` as `employee_fname`,`employee_job`.`name` as `job_description`, `employee_details`.`alias_name` as"
        . " `employee_aname`, "
        . "`bank_details`.`name` as `bank_name` from `cash_collection`"
        . " inner join `employees` as `employee_details` on "
        . "`employee_details`.`id` = `cash_collection`.`supervisor_id` inner join"
        . " `master_resources` as `bank_details` "
        . "on `bank_details`.`id` = `cash_collection`.`bank_id` inner join `master_resources` as `employee_job` on `employee_details`.`job_position` = `employee_job`.`id` where "
        . " cash_collection.cashier_id is NULL and cash_collection.verified_status=0 "
        . "and cash_collection.transfer_status=1 ";

if($searchkey){
    $cash_collections.= " and employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%'";
}
if($bank_id){
    $cash_collections.= " and cash_collection.bank_id=$bank_id";
}
 if($amount!="" &&  $aorder!= ""){
    $cash_collections.= " and cash_collection.amount $aorder $amount ";
}      
 if($created!="" &&  $ended!= ""){
    $cash_collections.= " and date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended'  ";
} 
$cash_collections.= " union select `cash_collection`.*,"
        . " `employee_details`.`username` as `username`, "
        . "`employee_details`.`first_name` as `employee_fname`,`employee_job`.`name` as `job_description`,"
        . " `employee_details`.`alias_name` as `employee_aname`, "
        . " `bank_details`.`name` as `bank_name` from `cash_collection`"
        . " inner join `employees` as `employee_details` "
        . "on `employee_details`.`id` = `cash_collection`.`cashier_id` "
        . "inner join `master_resources` as `bank_details`"
        . " on `bank_details`.`id` = `cash_collection`.`bank_id` inner join `master_resources` as `employee_job` on `employee_details`.`job_position` = `employee_job`.`id`"
        . " where cash_collection.cashier_id is NOT NULL"
        . " and cash_collection.verified_status=0 and cash_collection.transfer_status=1";
if($searchkey){
    $cash_collections.= " and employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%'";
}
if($bank_id){
    $cash_collections.= " and cash_collection.bank_id=$bank_id";
}
 if($amount!="" &&  $aorder!= ""){
    $cash_collections.= " and cash_collection.amount $aorder $amount ";
}      
 if($created!="" &&  $ended!= ""){
    $cash_collections.= " and date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended'  ";
} 
       
       
       //$page = 1;
//$perPage = 10;
$cash_collections = DB::select($cash_collections);
$currentPage = Input::get('page', 1) - 1;
//$currentPage = $paginate;


$pagedData = array_slice($cash_collections, $currentPage * $perPage, $perPage);
$lastPage =  ceil(count($cash_collections) / $perPage);
$cash_collections = new LengthAwarePaginator($pagedData, count($cash_collections), $perPage);


$paginate = $cash_collections;

                  
           return view('branchsales/cash_collection/accounts_result', array('cash_collections' => $cash_collections,'banks' => $banks));
        
         }
        return view('branchsales/cash_collection/accounts', array('cash_collections' => $cash_collections,'banks'=>$banks));
    }
    
    public function changeverificationstatus() {
        $fund_id = Input::get('fund_id');
        $login_id = Session::get('login_id');
            DB::table('cash_collection')
                    ->where('id', $fund_id)
                    ->update(['verified_by' => $login_id,'verified_status' => 1]);
        
        $cash_collections = DB::table('cash_collection')
                    ->select('cash_collection.*', 'employee_details.first_name as employee_fname','employee_details.alias_name as employee_aname','bank_details.name as bank_name')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
                    ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                    ->whereRaw("cash_collection.verified_status=0 and cash_collection.transfer_status=1")
                    ->get();
        echo '<div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Date</td>
                                <td>Supervisor Name</td>
                                <td>Bank</td>
                                <td>Reference Number</td>
                                <td>Amount</td>
                                <td>Verify</td>
                            </tr>
                        </thead><tbody>';
        foreach ($cash_collections as $cash_collection) {
            echo '<tr><td>' . date("d-m-Y", strtotime($cash_collection->created_at)) . '</td>';
            echo '<td>'.$cash_collection->employee_fname.' '.$cash_collection->employee_aname.'</td>';
            echo '<td>' . $cash_collection->bank_name . '</td>';
            echo '<td>' . $cash_collection->ref_no . '</td>';
            echo '<td>' . $cash_collection->amount . '</td>';
            echo '<td><a id="fund_id" class="fund_id" href="javascript:void(0);" value="'.$cash_collection->id.'">Verify</a>
                  </td></tr>';
        }
        echo '</tbody></table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>';
    }
    
    public function supervisorcashdeposit(Request $request) {
        $login_id = Session::get('login_id');
        $paginate = Config::get('app.PAGINATE');

        $cash_collections = DB::table('cash_collection')
                    ->select('cash_collection.*', 'employee_details.first_name as employee_fname','employee_details.alias_name as employee_aname','bank_details.name as bank_name')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.cashier_id')
                    ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                    ->whereRaw("cash_collection.supervisor_id=$login_id")
                    ->paginate($paginate);
        return view('branchsales/cash_collection/supervisor_cashdeposit_report', array('cash_collections' => $cash_collections));
    }

    

    public function show($id) {
        $id = \Crypt::decrypt($id);
        $login_id = Session::get('login_id');
        $collected_by_name = DB::table('employees')
                ->select('first_name','alias_name','username','profilepic')
                ->where('id','=',$login_id)->first();

        $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_name',
                        'cashier_details.username as cashier_code','cashier_details.first_name as cashier_fname',
                        'edited_details.username as edited_code','edited_details.first_name as edited_fname',
                        DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as cashier_details','cashier_details.id','=','pos_sales.cashier_id')
                ->leftjoin('employees as edited_details','edited_details.id','=','pos_sales.edited_by')
                ->whereRaw("pos_sales.id IN ($id) and pos_sales.status=1")
                ->get();
        
        $total_cash=$sale_details_data->sum('cash_collection');
        
        $cashcollectionid = DB::table('cash_collection')
                ->select('cash_collection.id','employees.first_name','employees.alias_name','employees.username','employees.profilepic')
                ->leftjoin('employees','employees.id','=','cash_collection.supervisor_id')
                ->where(['pos_ids' => $id])
                ->first();
        
        $branch_names = DB::table('pos_sales')
                ->select('pos_sales.*')->select('branch_details.name as branch_name', 'branch_details.id as branch_id')->distinct()
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->get();
        
        $shift_names = DB::table('pos_sales')
                ->select('pos_sales.*')->select('jobshift_details.name as jobshift_name', 'jobshift_details.id as jobshift_id')->distinct()
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->get();
        
        return view('branchsales/cash_collection/showdetails', array('sale_details_data' => $sale_details_data,'cashcollectionid'=>$cashcollectionid,
            'posids'=>$id,'branch_names' => $branch_names, 'shift_names' => $shift_names,'total_cash'=>$total_cash,
            'collected_by_name' => $collected_by_name));
    }
    
    public function filterdetail(Request $request) {
        
        $branch = Input::get('branch');
        $shift = Input::get('shift');
        $created = Input::get('startdate');
        $ended = Input::get('enddate');
        $id = Input::get('posids'); 
        $torder = Input::get('torder');
        $tamount = Input::get('tamount');
        $saleorder = Input::get('saleorder');
        $saleamount = Input::get('saleamount');
        $taxorder = Input::get('taxorder');
        $taxamount = Input::get('taxamount');
        $cashsaleorder = Input::get('cashsaleorder');
        $cashsaleamount = Input::get('cashsaleamount');
        $openingorder = Input::get('openingorder');
        $openingamount = Input::get('openingamount');
        $ccorder = Input::get('ccorder');  
        $ccamount = Input::get('ccamount');
        $cashdifforder = Input::get('cashdifforder');  
        $cashdiffamount = Input::get('cashdiffamount');
        $crorder = Input::get('crorder');  
        $cramount = Input::get('cramount');   
        $border = Input::get('border'); 
        $bamount = Input::get('bamount'); 
        $bankcollectionorder = Input::get('bankcollectionorder'); 
        $bankcollectionamount = Input::get('bankcollectionamount');
        $bankdifforder = Input::get('bankdifforder'); 
        $bankdiffamount = Input::get('bankdiffamount');
        $dorder = Input::get('dorder'); 
        $damount = Input::get('damount');   
        $morder = Input::get('morder'); 
        $mamount = Input::get('mamount');   
        $cashiername = Input::get('cashiername');   
     
        
        if($created!='')
        {
            $created = explode('-',$created);
            $created = $created[2].'-'.$created[1].'-'.$created[0];
        }
        if($ended!='')
        {
            $ended = explode('-',$ended);
            $ended = $ended[2].'-'.$ended[1].'-'.$ended[0];
        }
            
        
        $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_name',
                        'cashier_details.username as cashier_code','cashier_details.first_name as cashier_fname',
                        'edited_details.username as edited_code','edited_details.first_name as edited_fname',
                        DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as cashier_details','cashier_details.id','=','pos_sales.cashier_id')
                ->leftjoin('employees as edited_details','edited_details.id','=','pos_sales.edited_by')
                ->whereRaw("pos_sales.id IN ($id) and pos_sales.status=1")
                ->when($branch, function ($query) use ($branch) {
                    return $query->where('branch_details.id', '=', $branch);
                })
                ->when($shift, function ($query) use ($shift) {
                    return $query->where('jobshift_details.id', '=', $shift);
                })
                ->when($created, function ($query) use ($created, $ended) {
                    return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                })
                ->when($tamount, function ($query) use ($tamount, $torder) {
                        return $query->whereRaw("pos_sales.total_sale $torder $tamount ");
                    })
                ->when($saleamount, function ($query) use ($saleamount, $saleorder) {
                        return $query->whereRaw("(pos_sales.total_sale-pos_sales.tax_in_mis) $saleorder $saleamount ");
                    })
                ->when($taxamount, function ($query) use ($taxamount, $taxorder) {
                        return $query->whereRaw("pos_sales.tax_in_mis $taxorder $taxamount ");
                    })
                ->when($cashsaleamount, function ($query) use ($cashsaleamount, $cashsaleorder) {
                        return $query->whereRaw("pos_sales.cash_sale $cashsaleorder $cashsaleamount ");
                    })
                ->when($openingamount, function ($query) use ($openingamount, $openingorder) {
                        return $query->whereRaw("pos_sales.opening_amount $openingorder $openingamount ");
                    })
                ->when($ccamount, function ($query) use ($ccamount, $ccorder) {
                        return $query->whereRaw("pos_sales.cash_collection $ccorder $ccamount ");
                    })
                ->when($cashdiffamount, function ($query) use ($cashdiffamount, $cashdifforder) {
                        return $query->whereRaw("(pos_sales.cash_sale-pos_sales.cash_collection) $cashdifforder $cashdiffamount ");
                    })
                ->when($cramount, function ($query) use ($cramount, $crorder) {
                        return $query->whereRaw("pos_sales.credit_sale $crorder $cramount ");
                    })
                ->when($bamount, function ($query) use ($bamount, $border) {
                        return $query->whereRaw("pos_sales.bank_sale $border $bamount ");
                    })
                ->when($bankcollectionamount, function ($query) use ($bankcollectionamount, $bankcollectionorder) {
                        return $query->whereRaw("pos_sales.bank_collection $bankcollectionorder $bankcollectionamount ");
                    })
                ->when($bankdiffamount, function ($query) use ($bankdiffamount, $bankdifforder) {
                        return $query->whereRaw("(pos_sales.bank_sale-pos_sales.bank_collection) $bankdifforder $bankdiffamount ");
                    })
                ->when($damount, function ($query) use ($damount, $dorder) {
                        return $query->whereRaw("pos_sales.difference $dorder $damount ");
                    })
                ->when($mamount, function ($query) use ($mamount, $morder) {
                        return $query->whereRaw("pos_sales.meal_consumption $morder $mamount ");
                    })
                ->when($cashiername, function ($query) use ($cashiername) {
                        return $query->whereRaw("cashier_details.username like '$cashiername%'");
                    })
                ->get();
        
        $total_cash=$sale_details_data->sum('cash_collection');
                
        return view('branchsales/cash_collection/filter_result', array('sale_details_data' => $sale_details_data,'total_cash'=>$total_cash));
    }
    
    public function collect_cash() {
        try {

            $arrSalesId = Input::get('arrSalesId');
            $cashcollectionid = Input::get('cashcollectionid');
            $posids = Input::get('posids');

            $cash = DB::table('pos_sales')
                    ->whereRaw("pos_sales.id IN ($arrSalesId)")
                    ->update(['collection_status' => 1]);
            
            $notCollectedCount = DB::table('pos_sales')
                    ->whereRaw("pos_sales.id IN ($posids) AND collection_status=0 and pos_sales.status=1")
                    ->count();
            
            if($notCollectedCount==0){
                 $cash = DB::table('cash_collection')
                        ->where(['id' => $cashcollectionid])
                        ->update(['all_collected_status' => 1]);
            }

            Toastr::success('Cash Collected Successfully!', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return 1;
        }
    }

    public function showrecodetail($id) {
        $id = \Crypt::decrypt($id);

        $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_name',DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->whereRaw("pos_sales.id IN ($id) and pos_sales.status=1")
                ->get();

        return view('branchsales/cash_collection/showrecodetail', array('sale_details_data' => $sale_details_data));
    }
    
    public function show_details($id){
        $id = \Crypt::decrypt($id); 
        $details = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_name',
                        'cashier_details.username as cashier_code','cashier_details.first_name as cashier_fname',
                        'edited_details.username as edited_code','edited_details.first_name as edited_fname',
                        DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as cashier_details','cashier_details.id','=','pos_sales.cashier_id')
                ->leftjoin('employees as edited_details','edited_details.id','=','pos_sales.edited_by')
                ->where('pos_sales.id','=',$id)
                ->first();
        return view('branchsales/cash_collection/detailview',array('details' => $details));

    }
    
    public function exportviewtopdf() {

        try {
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', 3000);
            $id = Input::get("view_details");
            $type = Input::get("excelorpdf");
            //$ids = \Crypt::decrypt($id);
            $paginate = Input::get('pagelimit');

            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_name',
                        'cashier_details.username as cashier_code','cashier_details.first_name as cashier_fname',
                        'edited_details.username as edited_code','edited_details.first_name as edited_fname',
                        DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->leftjoin('employees as cashier_details','cashier_details.id','=','pos_sales.cashier_id')
                ->leftjoin('employees as edited_details','edited_details.id','=','pos_sales.edited_by')
                ->where('pos_sales.id','=',$id)
                ->first();
            if($sale_details_data->collection_status==1){
                $collection_status =  "Collected";
            }else{ 
                $collection_status = "Pending";
            }
            if (isset($sale_details_data)) {
                if ($type == 'PDF') {
                    $html_table = '<!DOCTYPE html>
                                        <html>
                                            <head>
                                                <title>Branch sale report</title>
                                                <meta charset="UTF-8">
                                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                            </head>
                                            <body>
                                                <div class="innerContent" style="background: #fff; padding: 32px; position: relative; font-family: "open_sansregular";
                                                     font-size: 12px;">
                                                    <header class="pageTitle" style="padding-top: 0px; margin-bottom: 28px; color: #554d4f; padding: 10px 0 10px; padding-top: 10px; border-bottom: 1px solid #e4dee0;">
                                                        <h1 style="font-family: "open_sanssemibold"; padding: 0 0 10px 0; line-height: 32px;">Branch Sale Report <span></span></h1>
                                                    </header>	
                                                    <div class="reportV1" style="border: 4px solid #e7e7e7;">
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-1 alignCenter" style="font-size: 15px; display: table-cell; padding: 12px; width: 20.33%;">
                                                                <b style="display: block; padding-bottom: 6px;">Collection Status</b>
                                                                ' . ($collection_status ? $collection_status : '') . '</li>
                                                            <li class="custCol-1 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 20.33%;">
                                                                <b style="display: block; padding-bottom: 6px;">Date</b>
                                                                ' . (date("d-m-Y", strtotime($sale_details_data->pos_date))) . '</li>

                                                            <li class="custCol-3 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 16.66%;">
                                                                <b style="display: block; padding-bottom: 6px;">Shift</b>
                                                                ' . ($sale_details_data->jobshift_name ? $sale_details_data->jobshift_name : '') . '
                                                            </li>

                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Branch</b>
                                                                ' . ($sale_details_data->branch_name ? $sale_details_data->branch_name : '') . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Opening Amount</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->opening_amount) ? $sale_details_data->opening_amount : 0))) . '</span>
                                                            </li>
                                                            
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Total Branch Sale</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->total_sale) ? $sale_details_data->total_sale : 0))) . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Sale Amount</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->total_sale) ? ($sale_details_data->total_sale-$sale_details_data->tax_in_mis) : 0))) . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Tax Amount</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->tax_in_mis) ? $sale_details_data->tax_in_mis : 0))) . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Total Cash Sale</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->cash_sale) ? $sale_details_data->cash_sale : 0))) . '
                                                            </li>
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Cash Collection</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->cash_collection) ? $sale_details_data->cash_collection : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Cash Difference</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->cash_sale) ? ($sale_details_data->cash_sale-$sale_details_data->cash_collection) : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Total Bank Sale</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->bank_sale) ? $sale_details_data->bank_sale : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Bank Collection</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->bank_collection) ? $sale_details_data->bank_collection : 0))) . '</span>
                                                            </li>
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Bank Difference</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->bank_sale) ? ($sale_details_data->bank_sale-$sale_details_data->bank_collection) : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Credit/Free Sale</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->credit_sale) ? $sale_details_data->credit_sale : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b  style="display: block; padding-bottom: 6px;">Net Difference</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->difference) ? $sale_details_data->difference : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b  style="display: block; padding-bottom: 6px;">Meal Consumption</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->meal_consumption) ? $sale_details_data->meal_consumption : 0))) . '</span>
                                                            </li>
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-4" style="font-size: 15px;  display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Cashier Name</b>
                                                                ' . ($sale_details_data->cashier_code ? $sale_details_data->cashier_code.':' : "") . ($sale_details_data->cashier_fname ? $sale_details_data->cashier_fname : "") . '
                                                            </li>
                                                            <li class="custCol-4 " style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Edited By</b>
                                                                ' . ($sale_details_data->edited_code ? $sale_details_data->edited_code.':' : "") . ($sale_details_data->edited_fname ? $sale_details_data->edited_fname : "") . '
                                                            </li>
                                                        </ul>

                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">

                                                            <li class="custCol-8 " style="font-size: 15px;  display: table-cell; padding: 12px;">
                                                                <b style="display: block; padding-bottom: 6px;">Reason</b>
                                                                ' . ($sale_details_data->reason ? $sale_details_data->reason : "") . '
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </body>
                                        </html>';
                    $pdf = App::make('dompdf.wrapper');
                    $pdf->loadHTML($html_table);
                    return $pdf->download('branch_sales_report.pdf');
                } else if ($type == 'Excel') {
                    Excel::create('CollectionDetails', function($excel) use($sale_details_data,$collection_status) {
                        // Set the title
                        $excel->setTitle('Collection Details');

                        $excel->sheet('Collection Details', function($sheet) use($sale_details_data,$collection_status) {
                            // Sheet manipulation

                            $sheet->setCellValue('J3', 'Collection Details');
                            $sheet->setHeight(3, 20);
                            $sheet->cells('A3:T3', function($cells) {
                                $cells->setBackground('#00CED1');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(14);
                            });

                            $chrRow = 6;

                            $sheet->row(5, array('Collection Status' ,'Date', 'Shift', 'Branch', 'Opening Amount', "Total Branch Sale", 'Sale Amount', 'Tax Amount', 'Total Cash Sale', 'Cash Collection', 'Cash Difference',"Total Bank Sale", 'Bank Collection', 'Bank Difference', "Credit Sale",  "Net Difference", "Meal Consumption" ,"Cashier Name", "Edited By", "Reason"));
                            $sheet->setHeight(5, 15);
                            $sheet->cells('A5:T5', function($cells) {
                                $cells->setBackground('#6495ED');
                                $cells->setFontWeight('bold');
                            });
                            $sheet->setCellValue('A' . $chrRow, ($collection_status ? $collection_status : ''));
                            $sheet->setCellValue('B' . $chrRow, (date("d-m-Y", strtotime($sale_details_data->pos_date))));
                            $sheet->setCellValue('C' . $chrRow, $sale_details_data->jobshift_name);
                            $sheet->setCellValue('D' . $chrRow, $sale_details_data->branch_name);
                            $sheet->setCellValue('E' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->opening_amount) ? $sale_details_data->opening_amount : 0)));
                            $sheet->setCellValue('F' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->total_sale) ? $sale_details_data->total_sale : 0)));
                            $sheet->setCellValue('G' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->total_sale) ? ($sale_details_data->total_sale-$sale_details_data->tax_in_mis) : 0)));
                            $sheet->setCellValue('H' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->tax_in_mis) ? $sale_details_data->tax_in_mis : 0)));
                            $sheet->setCellValue('I' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->cash_sale) ? $sale_details_data->cash_sale : 0)));
                            $sheet->setCellValue('J' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->cash_collection) ? $sale_details_data->cash_collection : 0)));
                            $sheet->setCellValue('K' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->cash_sale) ? ($sale_details_data->cash_sale-$sale_details_data->cash_collection) : 0)));
                            $sheet->setCellValue('L' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->bank_sale) ? $sale_details_data->bank_sale : 0)));
                            $sheet->setCellValue('M' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->bank_collection) ? $sale_details_data->bank_collection : 0)));
                            $sheet->setCellValue('N' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->bank_sale) ? ($sale_details_data->bank_sale-$sale_details_data->bank_collection) : 0)));
                            $sheet->setCellValue('O' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->credit_sale) ? $sale_details_data->credit_sale : 0)));
                            $sheet->setCellValue('P' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->difference) ? $sale_details_data->difference : 0)));
                            $sheet->setCellValue('Q' . $chrRow, Customhelper::numberformatter((((int) $sale_details_data->meal_consumption) ? $sale_details_data->meal_consumption : 0)));
                            $sheet->setCellValue('R' . $chrRow, ($sale_details_data->cashier_code . $sale_details_data->cashier_fname));
                            $sheet->setCellValue('S' . $chrRow, ($sale_details_data->edited_code . $sale_details_data->edited_fname));
                            $sheet->setCellValue('T' . $chrRow, ($sale_details_data->reason));

                            $sheet->cells('A' . $chrRow . ':T' . $chrRow, function($cells) {
                                $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                            });

                            $chrRow++;
                        });
                    })->export('xls');
                } else if ($type == 'PRINT') {
                    $html_table = '<!DOCTYPE html>
                                        <html>
                                            <head>
                                                <title>Collection Details</title>
                                                <meta charset="UTF-8">
                                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                            </head>
                                            <body>
                                                <div class="innerContent" style="background: #fff; padding: 32px; position: relative; font-family: "open_sansregular";
                                                     font-size: 12px;">
                                                    <header class="pageTitle" style="padding-top: 0px; margin-bottom: 28px; color: #554d4f; padding: 10px 0 10px; padding-top: 10px; border-bottom: 1px solid #e4dee0;">
                                                        <h1 style="font-family: "open_sanssemibold"; padding: 0 0 10px 0; line-height: 32px;">Branch Sale Report <span></span></h1>
                                                    </header>	
                                                    <div class="reportV1" style="border: 4px solid #e7e7e7;">
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-1 alignCenter" style="font-size: 15px; display: table-cell; padding: 12px; width: 20.33%;">
                                                                <b style="display: block; padding-bottom: 6px;">Collection Status</b>
                                                                ' . ($collection_status ? $collection_status : '') . '</li>
                                                            <li class="custCol-1 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 20.33%;">
                                                                <b style="display: block; padding-bottom: 6px;">Date</b>
                                                                ' . (date("d-m-Y", strtotime($sale_details_data->pos_date))) . '</li>

                                                            <li class="custCol-3 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 16.66%;">
                                                                <b style="display: block; padding-bottom: 6px;">Shift</b>
                                                                ' . ($sale_details_data->jobshift_name ? $sale_details_data->jobshift_name : '') . '
                                                            </li>

                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Branch</b>
                                                                ' . ($sale_details_data->branch_name ? $sale_details_data->branch_name : '') . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Opening Amount</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->opening_amount) ? $sale_details_data->opening_amount : 0))) . '</span>
                                                            </li>
                                                            
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Total Branch Sale</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->total_sale) ? $sale_details_data->total_sale : 0))) . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Sale Amount</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->total_sale) ? ($sale_details_data->total_sale-$sale_details_data->tax_in_mis) : 0))) . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Tax Amount</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->tax_in_mis) ? $sale_details_data->tax_in_mis : 0))) . '
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Total Cash Sale</b>
                                                                ' . (Customhelper::numberformatter((((int) $sale_details_data->cash_sale) ? $sale_details_data->cash_sale : 0))) . '
                                                            </li>
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Cash Collection</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->cash_collection) ? $sale_details_data->cash_collection : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Cash Difference</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->cash_sale) ? ($sale_details_data->cash_sale-$sale_details_data->cash_collection) : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Total Bank Sale</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->bank_sale) ? $sale_details_data->bank_sale : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Bank Collection</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->bank_collection) ? $sale_details_data->bank_collection : 0))) . '</span>
                                                            </li>
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Bank Difference</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->bank_sale) ? ($sale_details_data->bank_sale-$sale_details_data->bank_collection) : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b style="display: block; padding-bottom: 6px;">Credit/Free Sale</b>
                                                                <span style="color: #851e32;"> ' . (Customhelper::numberformatter((((int) $sale_details_data->credit_sale) ? $sale_details_data->credit_sale : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b  style="display: block; padding-bottom: 6px;">Net Difference</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->difference) ? $sale_details_data->difference : 0))) . '</span>
                                                            </li>
                                                            <li class="custCol-2 alignCenter" style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 24.99%;">
                                                                <b  style="display: block; padding-bottom: 6px;">Meal Consumption</b>
                                                                <span style="color: #851e32;">' . (Customhelper::numberformatter((((int) $sale_details_data->meal_consumption) ? $sale_details_data->meal_consumption : 0))) . '</span>
                                                            </li>
                                                        </ul>
                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">
                                                            <li class="custCol-4" style="font-size: 15px;  display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Cashier Name</b>
                                                                ' . ($sale_details_data->cashier_code ? $sale_details_data->cashier_code.':' : "") . ($sale_details_data->cashier_fname ? $sale_details_data->cashier_fname : "") . '
                                                            </li>
                                                            <li class="custCol-4 " style="font-size: 15px; border-left: 1px solid #e7e7e7; display: table-cell; padding: 12px; width: 33.32%; font-family: DejaVu Sans;">
                                                                <b style="display: block; padding-bottom: 6px;">Edited By</b>
                                                                ' . ($sale_details_data->edited_code ? $sale_details_data->edited_code.':' : "") . ($sale_details_data->edited_fname ? $sale_details_data->edited_fname : "") . '
                                                            </li>
                                                        </ul>

                                                        <ul class="custRow" style="display: table; width: 100%; text-align: center; padding:0; border-bottom: 1px solid #e7e7e7; margin: 0;">

                                                            <li class="custCol-8 " style="font-size: 15px;  display: table-cell; padding: 12px;">
                                                                <b style="display: block; padding-bottom: 6px;">Reason</b>
                                                                ' . ($sale_details_data->reason ? $sale_details_data->reason : "") . '
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </body>
                                        </html>';
                    $ret = array("data" => $html_table, "has_data" => '1');
                    echo json_encode($ret);
                }
            } else {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            }
        } Catch (\Exception $e) {echo $e->getMessage(); die();
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return redirect()->back();
        }
    }
    
    
     public function exporttopdf() {
 $login_id = Session::get('login_id');
         $searchkey = Input::get('searchkey');
            
            $aorder = Input::get('aorder');
            
            $amount = Input::get('aamount');
            
           $created = Input::get('startdate');
           $ended = Input::get('enddate');
           if($created!='')
           {
               $created = explode('-',$created);
               $created = $created[2].'-'.$created[1].'-'.$created[0];
           }
           if($ended!='')
           {
               $ended = explode('-',$ended);
               $ended = $ended[2].'-'.$ended[1].'-'.$ended[0];
            }
           
            
             $cashier_collections = DB::table('cash_collection')
                    ->select('cash_collection.*', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.username as username')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
                    ->whereRaw("cash_collection.cashier_id=$login_id and cash_collection.transfer_status=0")
                  ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%')");
                    })
                    ->when($amount, function ($query) use ($amount, $aorder) {
                        return $query->whereRaw("cash_collection.amount $aorder $amount ");
                    })
                    ->when($created, function ($query) use ($created, $ended) {
                        //echo "pos_sales.created_at >= '$created' AND pos_sales.created_at<= '$ended' ";
                        return $query->whereRaw("date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended' ");
                    })
                    ->get();
           
                  

        $html_table = '<!DOCTYPE html>
        <html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Project Name</title>
		<style>
			.listerType1 tr:nth-of-type(2n) {
				background: rgba(0, 75, 111, 0.12) none repeat 0 0;
			}
			
		</style>
	</head>
	<body style="margin: 0; padding: 0;font-family:Arial;">
		<section id="container">
                <div style="text-align:center;"><h1>Top Cashier Cash Collections</h1></div>
			<table style="width: 100%; border: 3px solid #3088da; margin:0 0px 0 -25px" box-sizing: border-box; cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Date</td>
						<td style="padding:10px 5px;color:#fff;"> Employee Code </td>
						<td style="padding:10px 5px;color:#fff;"> Supervisor Name </td>
						<td style="padding:10px 5px;color:#fff;"> Amount </td>
					</tr>
				</thead>
				<thead class="listHeaderBottom">
					<tr class="headingHolder">
						<td class="filterFields"></td>
						<td></td>
						<td class=""></td>
						
					</tr>
				</thead>
				<tbody class="pos" id="pos" >';
        foreach ($cashier_collections as $cashier_collection) {
            $pos_sale_date =  date("d-m-Y", strtotime($cashier_collection->created_at));
             
            $html_table .='<tr>
                


						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px;">' . $pos_sale_date . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px;">' . $cashier_collection->username . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:100px;  word-break: break-word;">' . $cashier_collection->employee_fname ." ". $cashier_collection->employee_aname . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px;">' .$cashier_collection->amount. '</td>
						</tr>';
        }
        $html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('top_cashier_cash_collections.pdf');
    }
    
    
    
      
     public function collectionexporttopdf() {
         
      $branch = Input::get('branch');
        $shift = Input::get('shift');
        $created = Input::get('startdate');
        $ended = Input::get('enddate');
        $id = Input::get('posids'); 
        $torder = Input::get('torder');
        $tamount = Input::get('tamount');
         $ccorder = Input::get('ccorder');  
         $ccamount = Input::get('ccamount');
         $crorder = Input::get('crorder');  
         $cramount = Input::get('cramount');   
         $border = Input::get('border'); 
         $bamount = Input::get('bamount'); 
         $dorder = Input::get('dorder'); 
         $damount = Input::get('damount');   
     
       
        if($created!='')
        {
            $created = explode('-',$created);
            $created = $created[2].'-'.$created[1].'-'.$created[0];
        }
        if($ended!='')
        {
            $ended = explode('-',$ended);
            $ended = $ended[2].'-'.$ended[1].'-'.$ended[0];
        }
            
        
        $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name','branch_details.branch_code as branch_code', 'employee_details.first_name as employee_name',DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->whereRaw("pos_sales.id IN ($id) and pos_sales.status=1")
                ->when($branch, function ($query) use ($branch) {
                    return $query->where('branch_details.id', '=', $branch);
                })
                ->when($shift, function ($query) use ($shift) {
                    return $query->where('jobshift_details.id', '=', $shift);
                })
                ->when($created, function ($query) use ($created, $ended) {
                    return $query->whereRaw("date(pos_sales.pos_date) >= '$created' AND date(pos_sales.pos_date)<= '$ended' ");
                })
                ->when($tamount, function ($query) use ($tamount, $torder) {
                        return $query->whereRaw("pos_sales.total_sale $torder $tamount ");
                    })
                ->when($ccamount, function ($query) use ($ccamount, $ccorder) {
                        return $query->whereRaw("pos_sales.cash_collection $ccorder $ccamount ");
                    })
                 ->when($cramount, function ($query) use ($cramount, $crorder) {
                        return $query->whereRaw("pos_sales.credit_sale $crorder $cramount ");
                    })
                  ->when($bamount, function ($query) use ($bamount, $border) {
                        return $query->whereRaw("pos_sales.bank_sale $border $bamount ");
                    })
                  ->when($damount, function ($query) use ($damount, $dorder) {
                        return $query->whereRaw("pos_sales.difference $dorder $damount ");
                    })
                ->get();
        
        
           
                  

        $html_table = '<!DOCTYPE html>
        <html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Project Name</title>
		<style>
			.listerType1 tr:nth-of-type(2n) {
				background: rgba(0, 75, 111, 0.12) none repeat 0 0;
			}
			
		</style>
	</head>
	<body style="margin: 0; padding: 0;font-family:Arial;">
		<section id="container">
                <div style="text-align:center;"><h1>Top Cashier Collection Details</h1></div>
			<table style="width: 100%; border: 3px solid #3088da; margin:0 0px 0 -25px" box-sizing: border-box; cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Date</td>
						<td style="padding:10px 5px;color:#fff;"> Branch Name </td>
						<td style="padding:10px 5px;color:#fff;"> Shift </td>
						<td style="padding:10px 5px;color:#fff;"> Total Sale </td>
                                                <td style="padding:10px 5px;color:#fff; word-break: break-word;"> Collected Cash  </td>
                                                <td style="padding:10px 5px;color:#fff;"> Credit Sale  </td>
                                                <td style="padding:10px 5px;color:#fff;"> Bank Sale  </td>
                                                <td style="padding:10px 5px;color:#fff;"> Difference  </td>
                                                <td style="padding:10px 5px;color:#fff;  word-break: break-word;"> Meals</td>
                                                <td style="padding:10px 5px;color:#fff;"> Reason  </td>
					</tr>
				</thead>
				<thead class="listHeaderBottom">
					<tr class="headingHolder">
						<td class="filterFields"></td>
						<td></td>
                                                <td></td>
						<td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td class=""></td>
						
					</tr>
				</thead>
				<tbody class="pos" id="pos" >';
        foreach ($sale_details_data as $cashier_collection) {
            $pos_sale_date =  date("d-m-Y", strtotime($cashier_collection->created_at));
             
            $html_table .='<tr>
                


						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px;">' . $pos_sale_date . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . $cashier_collection->branch_code .'-'.$cashier_collection->branch_name . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . $cashier_collection->jobshift_name . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . $cashier_collection->total_sale .'</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' .$cashier_collection->cash_collection. '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' .$cashier_collection->credit_sale. '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:60px; word-break: break-word;">' .$cashier_collection->bank_sale. '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' .$cashier_collection->difference. '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:60px; word-break: break-word;">' .$cashier_collection->meal_consumption. '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:60px; word-break: break-word;">' .$cashier_collection->reason. '</td>
						</tr>';
        }
        $html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('top_cashier_collection_details.pdf');
    }
    
      
     public function exportreconcilationpdf() {
         
          ini_set('memory_limit', '-1');
         ini_set('max_execution_time', 300);
         
              $excelorpdf = Input::get('excelorpdf');
            
           /* $searchkey = Input::get('searchkey');
            
            $bank_id = Input::get('bank_id');
            $aorder = Input::get('aorder');
            
            $amount = Input::get('aamount');
            
           $created = Input::get('startdate');
           $ended = Input::get('enddate');
           if($created!='')
           {
               $created = explode('-',$created);
               $created = $created[2].'-'.$created[1].'-'.$created[0];
           }
           if($ended!='')
           {
               $ended = explode('-',$ended);
               $ended = $ended[2].'-'.$ended[1].'-'.$ended[0];
            }
           
           
            
            $cash_collections = DB::table('cash_collection')
                    ->select('cash_collection.*','employee_details.username as username', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.job_description as job_description','bank_details.name as bank_name')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
                    ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                    ->whereRaw("cash_collection.verified_status=0 and cash_collection.transfer_status=1")
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%')");
                    })
                     ->when($bank_id, function ($query) use ($bank_id) {
                    return $query->where('cash_collection.bank_id', '=', $bank_id);
                      })
                    ->when($amount, function ($query) use ($amount, $aorder) {
                        return $query->whereRaw("cash_collection.amount $aorder $amount ");
                    })
                    ->when($created, function ($query) use ($created, $ended) {
                        //echo "pos_sales.created_at >= '$created' AND pos_sales.created_at<= '$ended' ";
                        return $query->whereRaw("date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended' ");
                    })
                     ->get(); */
              
                
             $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchkey = Input::get('searchkey');
            
            $bank_id = Input::get('bank_id');
            $aorder = Input::get('aorder');
            
            $amount = Input::get('aamount');
            
           $created = Input::get('startdate');
           $ended = Input::get('enddate');
           if($created!='')
           {
               $created = explode('-',$created);
               $created = $created[2].'-'.$created[1].'-'.$created[0];
           }
           if($ended!='')
           {
               $ended = explode('-',$ended);
               $ended = $ended[2].'-'.$ended[1].'-'.$ended[0];
            }
           
            
//            $cash_collections = DB::table('cash_collection')
//                    ->select('cash_collection.*','employee_details.username as username', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.job_description as job_description','bank_details.name as bank_name')
//                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
//                    ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
//                    ->whereRaw("cash_collection.verified_status=0 and cash_collection.transfer_status=1")
//                    ->when($searchkey, function ($query) use ($searchkey) {
//                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%')");
//                    })
//                     ->when($bank_id, function ($query) use ($bank_id) {
//                    return $query->where('cash_collection.bank_id', '=', $bank_id);
//                      })
//                    ->when($amount, function ($query) use ($amount, $aorder) {
//                        return $query->whereRaw("cash_collection.amount $aorder $amount ");
//                    })
//                    ->when($created, function ($query) use ($created, $ended) {
//                        //echo "pos_sales.created_at >= '$created' AND pos_sales.created_at<= '$ended' ";
//                        return $query->whereRaw("date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended' ");
//                    })
//       
//                    ->paginate($paginate);
          
$cash_collections="select `cash_collection`.*, `employee_details`.`username` as `username`, "
        . "`employee_details`.`first_name` as `employee_fname`,`employee_job`.`name` as `job_description`, `employee_details`.`alias_name` as"
        . " `employee_aname`, "
        . "`bank_details`.`name` as `bank_name` from `cash_collection`"
        . " inner join `employees` as `employee_details` on "
        . "`employee_details`.`id` = `cash_collection`.`supervisor_id` inner join"
        . " `master_resources` as `bank_details` "
        . "on `bank_details`.`id` = `cash_collection`.`bank_id` inner join `master_resources` as `employee_job` on `employee_details`.`job_position` = `employee_job`.`id`  where "
        . " cash_collection.cashier_id is NULL and cash_collection.verified_status=0 "
        . "and cash_collection.transfer_status=1 ";

if($searchkey){
    $cash_collections.= " and employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%'";
}
if($bank_id){
    $cash_collections.= " and cash_collection.bank_id=$bank_id";
}
 if($amount!="" &&  $aorder!= ""){
    $cash_collections.= " and cash_collection.amount $aorder $amount ";
}      
 if($created!="" &&  $ended!= ""){
    $cash_collections.= " and date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended'  ";
} 
$cash_collections.= " union select `cash_collection`.*,"
        . " `employee_details`.`username` as `username`, "
        . "`employee_details`.`first_name` as `employee_fname`, `employee_job`.`name` as `job_description`,"
        . " `employee_details`.`alias_name` as `employee_aname`, "
        . " `bank_details`.`name` as `bank_name` from `cash_collection`"
        . " inner join `employees` as `employee_details` "
        . "on `employee_details`.`id` = `cash_collection`.`cashier_id` "
        . "inner join `master_resources` as `bank_details`"
        . " on `bank_details`.`id` = `cash_collection`.`bank_id` inner join `master_resources` as `employee_job` on `employee_details`.`job_position` = `employee_job`.`id`"
        . " where cash_collection.cashier_id is NOT NULL"
        . " and cash_collection.verified_status=0 and cash_collection.transfer_status=1";
if($searchkey){
    $cash_collections.= " and employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%'";
}
if($bank_id){
    $cash_collections.= " and cash_collection.bank_id=$bank_id";
}
 if($amount!="" &&  $aorder!= ""){
    $cash_collections.= " and cash_collection.amount $aorder $amount ";
}      
 if($created!="" &&  $ended!= ""){
    $cash_collections.= " and date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended'  ";
} 
       
       
       //$page = 1;
//$perPage = 10;
$cash_collections = DB::select($cash_collections);
//$currentPage = Input::get('page', 1) - 1;
//$currentPage = $paginate;


//$pagedData = array_slice($cash_collections, $currentPage * $perPage, $perPage);
//$lastPage =  ceil(count($cash_collections) / $perPage);
//$cash_collections = new LengthAwarePaginator($pagedData, count($cash_collections), $perPage);


//$paginate = $cash_collections;

           
          if($excelorpdf=="EXCEL"){
            
            Excel::create('AccountReconcilation', function($excel) use($cash_collections){
                 // Set the title
                $excel->setTitle('Account Reconcilation');
                
                $excel->sheet('Account Reconcilation', function($sheet) use($cash_collections){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('D3', 'Accounts Reconciliation');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:H3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No','Date','Employee Code','Depositor Name','Deposited By','Bank',"Reference No:",'Amount'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:H5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($cash_collections);$i++){
                       $date= date("d-m-Y", strtotime($cash_collections[$i]->created_at));
                        
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $date);
                        $sheet->setCellValue('C'.$chrRow, $cash_collections[$i]->username);
                        $sheet->setCellValue('D'.$chrRow, $cash_collections[$i]->employee_fname.' '. $cash_collections[$i]->employee_aname);
                        $sheet->setCellValue('E'.$chrRow, $cash_collections[$i]->job_description); 
                        $sheet->setCellValue('F'.$chrRow, $cash_collections[$i]->bank_name);
                        $sheet->setCellValue('G'.$chrRow, $cash_collections[$i]->ref_no);
                        $sheet->setCellValue('H'.$chrRow, $cash_collections[$i]->amount);
                            
                        $sheet->cells('A'.$chrRow.':H'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                        
                        $chrRow++;
                    }

                });
                
            })->export('xls');
            
        }
     
      else{  $html_table = '<!DOCTYPE html>
        <html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Project Name</title>
		<style>
			.listerType1 tr:nth-of-type(2n) {
				background: rgba(0, 75, 111, 0.12) none repeat 0 0;
			}
			
		</style>
	</head>
	<body style="margin: 0; padding: 0;font-family:Arial;">
		<section id="container">
                <div style="text-align:center;"><h1>Accounts Reconciliation</h1></div>
			<table style="width: 100%; border: 3px solid #3088da; margin:0 0px 0 -25px" box-sizing: border-box; cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Date</td>
						<td style="padding:10px 5px;color:#fff;"> Employee Code </td>
						<td style="padding:10px 5px;color:#fff;"> Depositor Name </td>
						<td style="padding:10px 5px;color:#fff;"> Deposited By </td>
						<td style="padding:10px 5px;color:#fff;"> Bank </td>
                                                <td style="padding:10px 5px;color:#fff; "> Reference No: </td>
                                                <td style="padding:10px 5px;color:#fff;"> Amount  </td>
                                               
					</tr>
				</thead>
				<thead class="listHeaderBottom">
					<tr class="headingHolder">
						<td class="filterFields"></td>
						<td></td>
                                                <td></td>
						<td></td>
                                                <td></td>
                                                
                                                <td class=""></td>
						
					</tr>
				</thead>
				<tbody class="pos" id="pos" >';
        foreach ($cash_collections as $cashier_collection) {
            $pos_sale_date =  date("d-m-Y", strtotime($cashier_collection->created_at));
             
            $html_table .='<tr>
                


						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px;">' . $pos_sale_date . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . $cashier_collection->username . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' . $cashier_collection->employee_fname .' '.$cashier_collection->employee_aname . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . $cashier_collection->job_description .'</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' . $cashier_collection->bank_name .'</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:80px; word-break: break-word;">' .$cashier_collection->ref_no. '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;width:70px; word-break: break-word;">' .$cashier_collection->amount. '</td>
						</tr>';
        }
        $html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('accounts_reconciliation.pdf');
    }
    
     }
    
}
