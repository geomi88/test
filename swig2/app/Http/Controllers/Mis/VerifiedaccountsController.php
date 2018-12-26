<?php

namespace App\Http\Controllers\Mis;
use Illuminate\Support\Facades\Config;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Requests;
use Kamaln7\Toastr\Facades\Toastr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Masterresources;
use App\Models\Company;
use App\Models\Document;
use App;
use PDF;
use DB;

class VerifiedaccountsController extends Controller
{
    public function index(Request $request) {
        $login_id = Session::get('login_id');
        $paginate=Config::get('app.PAGINATE');
//        $cash_collections = DB::table('cash_collection')
//                    ->select('cash_collection.*', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.job_description as job_description','verified_user.first_name as verified_fname', 'verified_user.alias_name as verified_aname','bank_details.name as bank_name')
//                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
//                    ->join('employees as verified_user', 'verified_user.id', '=', 'cash_collection.verified_by')
//                    ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
//                    ->whereRaw("cash_collection.verified_status=1")
//                    ->orderby("cash_collection.created_at","DESC")
//                    //->paginate($paginate);
//                ->toSql();
     
       
         $cash_collections="select `cash_collection`.*, `employee_details`.`username` as `username`, `employee_details`.`first_name` as `employee_fname`, `employee_details`.`alias_name` as `employee_aname`, `employee_details`.`job_description` as `job_description`, `verified_user`.`first_name` as `verified_fname`, `verified_user`.`alias_name` as `verified_aname`,`bank_details`.`name` as `bank_name` from `cash_collection` inner join `employees` as `employee_details` on `employee_details`.`id` = `cash_collection`.`supervisor_id` inner join `employees` as `verified_user` on `verified_user`.`id`=`cash_collection`.`verified_by` inner join `master_resources` as `bank_details` on `bank_details`.`id` = `cash_collection`.`bank_id` where  cash_collection.cashier_id is NULL and cash_collection.verified_status=1  union select `cash_collection`.*, `employee_details`.`username` as `username`, `employee_details`.`first_name` as `employee_fname`, `employee_details`.`alias_name` as `employee_aname`, `employee_details`.`job_description` as `job_description`,`verified_user`.`first_name` as `verified_fname`, `verified_user`.`alias_name` as `verified_aname`, `bank_details`.`name` as `bank_name` from `cash_collection` inner join `employees` as `employee_details` on `employee_details`.`id` = `cash_collection`.`cashier_id` inner join `employees` as `verified_user` on `verified_user`.`id`=`cash_collection`.`verified_by`  inner join `master_resources` as `bank_details` on `bank_details`.`id` = `cash_collection`.`bank_id` where cash_collection.cashier_id is NOT NULL and cash_collection.verified_status=1 order by id DESC ";
       
       
       
       $page = 1;
$perPage = 10;
$cash_collections = DB::select($cash_collections);
$currentPage = Input::get('page', 1) - 1;

$total=count($cash_collections);
$pagedData = array_slice($cash_collections, $currentPage * $perPage, $perPage);
$lastPage =  ceil(count($cash_collections) / $perPage);
//$cash_collections =  new Paginator($pagedData, count($cash_collections), $perPage);
$cash_collections = new LengthAwarePaginator($pagedData, count($cash_collections), $perPage);

 
        
        $bank_names = DB::table('cash_collection')
                ->select('cash_collection.bank_id')->select('bank_details.name as bank_name', 'bank_details.id as id')->distinct()
                ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                ->get();
        
        if ($request->ajax()) {
          
              
        //  return view('mis/verified_accounts/verified_accounts_result', array('cash_collections' => $cash_collections));
      
            $searchkey = Input::get('searchkey');
            $ref_searchKey = Input::get('ref_searchkey');
            $verifiedsearchkey = Input::get('verifiedsearchkey');
            $sort = Input::get('sorting');
            $vsort = Input::get('vsorting');
            $bsort = Input::get('bsort');
            $cashsort = Input::get('cashsorting');
            
            $bank = Input::get('bank');
            $cashorder = Input::get('corder');
            $cashamount = Input::get('camount');
            $created = Input::get('startdate');
            $ended = Input::get('enddate');
           
            if ($created != '') {
                $created = explode('-', $created);
                $created = $created[2] . '-' . $created[1] . '-' . $created[0];
            }
            if ($ended != '') {
                $ended = explode('-', $ended);
                $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
            }
            
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $sortbydate='DESC';
            
         /*   $cash_collections = DB::table('cash_collection')
                    ->select('cash_collection.*', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.job_description as job_description','verified_user.first_name as verified_fname', 'verified_user.alias_name as verified_aname','bank_details.name as bank_name')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
                    ->join('employees as verified_user', 'verified_user.id', '=', 'cash_collection.verified_by')
                    ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                    ->whereRaw("cash_collection.verified_status=1")
                     ->when($created, function ($query) use ($created, $ended) {
                        return $query->whereRaw("date(cash_collection.created_at)>='$created' AND date(cash_collection.created_at)<='$ended' ");
                    })
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or concat(employee_details.alias_name) like '$searchkey%')");
                    })
                    ->when($verifiedsearchkey, function ($query) use ($verifiedsearchkey) {
                        return $query->whereRaw("(verified_user.first_name like '$verifiedsearchkey%' or concat(verified_user.alias_name) like '$verifiedsearchkey%')");
                    })
                    ->when($ref_searchKey, function ($query) use ($ref_searchKey) {
                        return $query->whereRaw("(cash_collection.ref_no like '$ref_searchKey%')");
                    })
                    ->when($bank, function ($query) use ($bank) {
                        return $query->where('cash_collection.bank_id', '=', $bank);
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        return $query->whereRaw("cash_collection.amount $cashorder $cashamount ");
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('employee_details.first_name', $sort);
                    })
                    ->when($vsort, function ($query) use ($vsort) {
                        return $query->orderby('verified_user.first_name', $vsort);
                    })
                    ->when($bsort, function ($query) use ($bsort) {
                        return $query->orderby('bank_details.name', $bsort);
                    })
                    ->when($cashsort, function ($query) use ($cashsort) {
                        return $query->orderby('cash_collection.amount', $cashsort);
                    })
                    ->when($sortbydate, function ($query) use ($sortbydate) {
                        return $query->orderby('cash_collection.created_at', $sortbydate);
                    })
                    ->paginate($paginate);*/
                    
                    
                                    
                    
                    $cash_collections="select `cash_collection`.*, `employee_details`.`username` as `username`,`verified_user`.`first_name` as `verified_fname`, `verified_user`.`alias_name` as `verified_aname`, "
        . "`employee_details`.`first_name` as `employee_fname`, `employee_details`.`alias_name` as"
        . " `employee_aname`, `employee_details`.`job_description` as `job_description`, "
        . "`bank_details`.`name` as `bank_name` from `cash_collection`"
        . " inner join `employees` as `employee_details` on "
        . "`employee_details`.`id` = `cash_collection`.`supervisor_id` "
        . "inner join `employees` as `verified_user` on `verified_user`.`id`=`cash_collection`.`verified_by`"
        . " inner join `master_resources` as `bank_details` "
        . "on `bank_details`.`id` = `cash_collection`.`bank_id` where "
        . " cash_collection.cashier_id is NULL and cash_collection.verified_status=1 ";

if($searchkey){
    $cash_collections.= " and employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%'";
}
if($verifiedsearchkey){
    $cash_collections.= " and verified_user.first_name like '$verifiedsearchkey%' or concat(verified_user.alias_name) like '$verifiedsearchkey%'";
}
if($ref_searchKey){
    $cash_collections.= " and cash_collection.ref_no like '$ref_searchKey%'";
}
if($bank){
    $cash_collections.= " and cash_collection.bank_id=$bank";
}
 if($cashamount !=""  &&  $cashorder != ""){
    $cash_collections.= " and cash_collection.amount $cashorder $cashamount  ";
}
 
 if($created!="" &&  $ended!= ""){
    $cash_collections.= " and date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended'  ";
} 



$cash_collections.= " union select `cash_collection`.*,"
        . " `employee_details`.`username` as `username`,`verified_user`.`first_name` as `verified_fname`, `verified_user`.`alias_name` as `verified_aname`, "
        . "`employee_details`.`first_name` as `employee_fname`,"
        . " `employee_details`.`alias_name` as `employee_aname`, "
        . "`employee_details`.`job_description` as `job_description`,"
        . " `bank_details`.`name` as `bank_name` from `cash_collection`"
        . " inner join `employees` as `employee_details` "
        . "on `employee_details`.`id` = `cash_collection`.`cashier_id` "
        . "inner join `employees` as `verified_user` on `verified_user`.`id`=`cash_collection`.`verified_by`"
        . "inner join `master_resources` as `bank_details`"
        . " on `bank_details`.`id` = `cash_collection`.`bank_id`"
        . " where cash_collection.cashier_id is NOT NULL"
        . " and cash_collection.verified_status=1 ";

if($searchkey){
    $cash_collections.= " and employee_details.first_name like '$searchkey%' or employee_details.alias_name like '$searchkey%'";
}
if($verifiedsearchkey){
    $cash_collections.= " and verified_user.first_name like '$verifiedsearchkey%' or concat(verified_user.alias_name) like '$verifiedsearchkey%'";
}
if($ref_searchKey){
    $cash_collections.= " and cash_collection.ref_no like '$ref_searchKey%'";
}
if($bank){
    $cash_collections.= " and cash_collection.bank_id=$bank";
}
 if($cashamount !=""  &&  $cashorder != ""){
    $cash_collections.= " and cash_collection.amount $cashorder $cashamount  ";
}
if($created!="" &&  $ended!= ""){
    $cash_collections.= " and date(cash_collection.created_at) >= '$created' AND date(cash_collection.created_at)<= '$ended'  ";
} 
 if($sort ){
    $cash_collections.= " order by employee_fname $sort  ";
} 
if($vsort ){
    $cash_collections.= " order by verified_fname $vsort  ";
} 
 

if($bsort ){
    $cash_collections.= " order by name  $bsort ";
}
if($cashsort ){
    $cash_collections.= " order by amount  $cashsort ";
}
if($sortbydate ){
    $cash_collections.= " order by created_at $sortbydate ";
}
  

$cash_collections = DB::select($cash_collections);
$currentPage = Input::get('page', 1) - 1;
//$currentPage = $paginate;


$pagedData = array_slice($cash_collections, $currentPage * $perPage, $perPage);
$lastPage =  ceil(count($cash_collections) / $perPage);
$cash_collections = new LengthAwarePaginator($pagedData, count($cash_collections), $perPage);


$paginate = $cash_collections;

                    
                   
       return view('mis/verified_accounts/verified_accounts_result', array('cash_collections' => $cash_collections));
        }
        
        return view('mis/verified_accounts/index', array('cash_collections' => $cash_collections,'bank_names' => $bank_names));
    }
    
     public function showverifieddetails($id) {
        $id = \Crypt::decrypt($id);

        $sale_details_data = DB::table('pos_sales')
                ->select('pos_sales.*', 'branch_details.name as branch_name', 'jobshift_details.name as jobshift_name', 'employee_details.first_name as employee_name',DB::raw('CASE WHEN pos_sales.reason_id IS NULL THEN pos_sales.reason_details ELSE reason.name END as reason'))
                ->join('master_resources as branch_details', 'branch_details.id', '=', 'pos_sales.branch_id')
                ->join('master_resources as jobshift_details', 'jobshift_details.id', '=', 'pos_sales.job_shift_id')
                ->leftjoin('master_resources as reason', 'reason.id', '=', 'pos_sales.reason_id')
                ->join('employees as employee_details', 'employee_details.id', '=', 'pos_sales.employee_id')
                ->whereRaw("pos_sales.id IN ($id) and pos_sales.status=1")
                ->get();

        return view('mis/verified_accounts/showverifieddetails', array('sale_details_data' => $sale_details_data));
    }
    
   // Generate PDF funcion
    public function exporttopdf() {
            $searchkey = Input::get('searchkey');
            $ref_searchKey = Input::get('ref_search');
            $verifiedsearchkey = Input::get('verifiedby');
            
            $sort = Input::get('sortsimp');
            $vsort = Input::get('vsorting');
            $bsort = Input::get('bsort');
            $cashsort = Input::get('cashsorting');
            
            $bank = Input::get('bank');
            $cashorder = Input::get('corder');
            $cashamount = Input::get('camount');
            $created = Input::get('startdate');
            $ended = Input::get('enddate');
            
            if ($created != '') {
                $created = explode('-', $created);
                $created = $created[2] . '-' . $created[1] . '-' . $created[0];
            }
            if ($ended != '') {
                $ended = explode('-', $ended);
                $ended = $ended[2] . '-' . $ended[1] . '-' . $ended[0];
            }
            
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $sortbydate='DESC';
            
            $cash_collections = DB::table('cash_collection')
                    ->select('cash_collection.*', 'employee_details.first_name as employee_fname', 'employee_details.alias_name as employee_aname','employee_details.job_description as job_description','verified_user.first_name as verified_fname', 'verified_user.alias_name as verified_aname','bank_details.name as bank_name')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'cash_collection.supervisor_id')
                    ->join('employees as verified_user', 'verified_user.id', '=', 'cash_collection.verified_by')
                    ->join('master_resources as bank_details', 'bank_details.id', '=', 'cash_collection.bank_id')
                    ->whereRaw("cash_collection.verified_status=1")
                     ->when($created, function ($query) use ($created, $ended) {
                        return $query->whereRaw("date(cash_collection.created_at)>='$created' AND date(cash_collection.created_at)<='$ended' ");
                    })
                    ->when($searchkey, function ($query) use ($searchkey) {
                        return $query->whereRaw("(employee_details.first_name like '$searchkey%' or concat(employee_details.alias_name) like '$searchkey%')");
                    })
                    ->when($verifiedsearchkey, function ($query) use ($verifiedsearchkey) {
                        return $query->whereRaw("(verified_user.first_name like '$verifiedsearchkey%' or concat(verified_user.alias_name) like '$verifiedsearchkey%')");
                    })
                    ->when($ref_searchKey, function ($query) use ($ref_searchKey) {
                        return $query->whereRaw("(cash_collection.ref_no like '$ref_searchKey%')");
                    })
                    ->when($bank, function ($query) use ($bank) {
                        return $query->where('cash_collection.bank_id', '=', $bank);
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        return $query->whereRaw("cash_collection.amount $cashorder $cashamount ");
                    })
                    ->when($sort, function ($query) use ($sort) {
                        return $query->orderby('employee_details.first_name', $sort);
                    })
                    ->when($vsort, function ($query) use ($vsort) {
                        return $query->orderby('verified_user.first_name', $vsort);
                    })
                    ->when($bsort, function ($query) use ($bsort) {
                        return $query->orderby('bank_details.name', $bsort);
                    })
                    ->when($cashsort, function ($query) use ($cashsort) {
                        return $query->orderby('cash_collection.amount', $cashsort);
                    })
                    ->when($sortbydate, function ($query) use ($sortbydate) {
                        return $query->orderby('cash_collection.created_at', $sortbydate);
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
                <div style="text-align:center;"><h1>Verified Accounts Report</h1></div>
			<table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Date</td>
						<td style="padding:10px 5px;color:#fff;"> Deposited By </td>
						<td style="padding:10px 5px;color:#fff;"> Depositor Name </td>
						<td style="padding:10px 5px;color:#fff;"> Verified  By </td>
						<td style="padding:10px 5px;color:#fff;"> Bank Name</td>
						<td style="padding:10px 5px;color:#fff;"> Reference No</td>
						<td style="padding:10px 5px;color:#fff;"> Amount </td>
					</tr>
				</thead>
				
				<tbody class="pos" id="pos" >';
        foreach ($cash_collections as $cash_collection) {
            $created_date = date("d-m-Y", strtotime($cash_collection->created_at));
            $html_table .='<tr>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $created_date . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cash_collection->job_description . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cash_collection->employee_fname." ".$cash_collection->employee_aname . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cash_collection->verified_fname." ".$cash_collection->verified_aname . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cash_collection->bank_name . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cash_collection->ref_no . '</td>
						<td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cash_collection->amount . '</td>
					</tr>';
        }
        $html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('mtg_verified_accounts_pdf_report.pdf');
    }
}
