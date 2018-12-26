<?php

namespace App\Http\Controllers\Kpi;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\Masterresources;
use Illuminate\Support\Facades\Config;
use App\Models\Company;
use App\Models\Module;
use App\Models\Inventory;
use App\Models\Sales_target;
use App\Models\Usermodule;
use App;
use DB;
use Mail;
use Exception;
use Excel;

class SalestargetController extends Controller {

    public function index(Request $request) {
        try {

            $paginate = Config::get('app.PAGINATE');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $loggedin_employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->where('employees.id', '=', $login_id)
                    ->first();

            if ($loggedin_employee_details->name != 'Sales_analyst' && $loggedin_employee_details->admin_status != 1) {
                throw new Exception("No_permission");
            }

            $analystbranches = DB::table('branches_to_analyst')
                    ->select('branches_to_analyst.*')
                    ->whereRaw("analyst_id=$login_id AND status=1")
                    ->pluck("branch_id")
                    ->toArray();

            $targets = DB::table('sales_target')
                    ->select('sales_target.*', 'm.branch_code as branch_code', 'm.name as name', 'm.id as branch_id', db::raw("concat('Q',sales_target.target_quarter,' ',sales_target.target_year) as quarter"))
                    ->leftjoin('master_resources as m', 'sales_target.branch_id', '=', 'm.id')
                    ->whereraw('sales_target.status=1')
                     ->whereraw('m.status!=2')
                    ->whereIn('branch_id', $analystbranches)
                    ->orderby('sales_target.created_at', 'DESC')
                    ->paginate($paginate);
            
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $searchbyname = Input::get('searchbyname');
            $searchbycode = Input::get('searchbycode');
            $searchbyquarter = Input::get('searchbyquarter');
            $corder = Input::get('corder');
            $camount = Input::get('camount');
                
            $sortordname = Input::get('sortordname');
            $sortordercode = Input::get('sortordercode');
            $sortordquarter = Input::get('sortordquarter');
            $sortordamount = Input::get('sortordamount');

            $sortOrdDefault='';
            if($sortordname=='' && $sortordercode=='' && $sortordquarter=='' && $sortordamount==''){
                $sortOrdDefault='ASC';
            }

            $targets = DB::table('sales_target')
                    ->select('sales_target.*', 'm.branch_code as branch_code', 'm.name as name', 'm.id as branch_id', db::raw("concat('Q',sales_target.target_quarter,' ',sales_target.target_year) as quarter"))
                    ->leftjoin('master_resources as m', 'sales_target.branch_id', '=', 'm.id')
                    ->whereraw('sales_target.status=1')
                     ->whereraw('m.status!=2')
                    ->whereIn('branch_id', $analystbranches)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(m.name like '$searchbyname%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(m.branch_code like '$searchbycode%')");
                    })
                    ->when($searchbyquarter, function ($query) use ($searchbyquarter) {
                        $quarter = explode("-", $searchbyquarter);
                        return $query->whereRaw("(sales_target.target_quarter=$quarter[0] AND sales_target.target_year=$quarter[1])");
                    })
                    ->when($camount, function ($query) use ($camount, $corder) {
                        return $query->whereRaw("sales_target.target_amount $corder $camount");
                    })
                    ->when($sortordercode, function ($query) use ($sortordercode) {
                        return $query->orderby('m.branch_code', $sortordercode);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('m.name', $sortordname);
                    })
                    ->when($sortordquarter, function ($query) use ($sortordquarter) {
                        return $query->orderby('sales_target.target_year', $sortordquarter);
                    })
                    ->when($sortordamount, function ($query) use ($sortordamount) {
                        return $query->orderby('sales_target.target_amount', $sortordamount);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('sales_target.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);
                
                return view('kpi/sales_target/result', array('targets' => $targets));
            }

            return view('kpi/sales_target/index', array('targets' => $targets));
        } catch (\Exception $e) {

            if ($e->getMessage() == "No_permission") {
                Toastr::error('Sorry You have No permission To View This Page!', $title = null, $options = []);
                return Redirect::to('kpi');
            }

            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('kpi');
        }
    }

    public function add() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $loggedin_employee_details = DB::table('employees')
                    ->select('employees.*', 'master_resources.name')
                    ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->where('employees.id', '=', $login_id)
                    ->first();

            if ($loggedin_employee_details->name != 'Sales_analyst' && $loggedin_employee_details->admin_status != 1) {
                throw new Exception("No_permission");
            }
            
            $analystbranches = DB::table('branches_to_analyst')
                    ->select('branches_to_analyst.*', 'm.branch_code as branch_code', 'm.name as name')
                    ->leftjoin('master_resources as m', 'branches_to_analyst.branch_id', '=', 'm.id')
                    ->whereRaw("branches_to_analyst.status=1 AND branches_to_analyst.analyst_id=$login_id AND  m.status!=2")
                    ->get();
            
            if(count($analystbranches)==0){
                throw new Exception("No_allocation");
            }
            
            return view('kpi/sales_target/add');
        } catch (\Exception $e) {

            if ($e->getMessage() == "No_permission") {
                Toastr::error('Sorry You have No permission To View This Page!', $title = null, $options = []);
                return Redirect::to('kpi/sales_target');
            }
            
            if($e->getMessage()=="No_allocation"){
               Toastr::error('No Branch Allocated!', $title = null, $options = []);
               return Redirect::to('kpi/sales_target');
            }
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('kpi/sales_target');
        }
    }

    public function getsalestargets() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $arrQuarter = array("Q1" => 1, "Q2" => 2, "Q3" => 3, "Q4" => 4);
            $arrMonthQuarter = array("01" => 1, "02" => 1, "03" => 1, "04" => 2, "05" => 2, "06" => 2, "07" => 3, "08" => 3, "09" => 3, "10" => 4, "11" => 4, "12" => 4);
            $strQuarter = Input::get('strQuarter');
            $arrQua = explode(" ", $strQuarter);
            $Quarter = $arrQuarter["$arrQua[0]"];
            $Year = $arrQua[1];
            $currentMonth = date('m');
            $currentYear = date('Y');
            $currentQuarter = $arrMonthQuarter["$currentMonth"];

            if (($Quarter >= $currentQuarter && $Year >= $currentYear) || $Year > $currentYear) {
                $allowSave = 1;
            } else {
                $allowSave = 0;
            }

            $analystbranches = DB::table('branches_to_analyst')
                    ->select('branches_to_analyst.*', 'm.branch_code as branch_code', 'm.name as name')
                    ->leftjoin('master_resources as m', 'branches_to_analyst.branch_id', '=', 'm.id')
                    ->whereRaw("branches_to_analyst.status=1 AND branches_to_analyst.analyst_id=$login_id AND m.status!=2")
                    ->get();
            
            $branches = $analystbranches->pluck("branch_id")->toArray();

            $targets = DB::table('sales_target as s')
                    ->select('s.*')
                    ->leftjoin('master_resources as m', 's.branch_id', '=', 'm.id')
                    ->whereRaw("s.target_quarter=$Quarter AND s.target_year=$Year AND s.status=1 AND m.status!=2")
                    ->whereIn('s.branch_id', $branches)
                    ->get();

            $targetdata = array();
            foreach ($targets as $objTarget) {

                $targetdata[$objTarget->branch_id] = array(
                    "createdat" => $objTarget->created_at,
                    "targetamount" => $objTarget->target_amount
                );
            }

            $arrReturn = array();
            foreach ($analystbranches as $objBranch) {

                if (key_exists($objBranch->branch_id, $targetdata)) {
                    $arrReturn[] = array(
                        "branch_id" => $objBranch->branch_id,
                        "branch_code" => $objBranch->branch_code,
                        "name" => $objBranch->name,
                        "created_at" => date("d-m-Y", strtotime($targetdata[$objBranch->branch_id]["createdat"])),
                        "target_amount" => $targetdata[$objBranch->branch_id]["targetamount"]);
                } else {
                    $arrReturn[] = array(
                        "branch_id" => $objBranch->branch_id,
                        "branch_code" => $objBranch->branch_code,
                        "name" => $objBranch->name,
                        "created_at" => '',
                        "target_amount" => '');
                }
            }

            if (count($arrReturn) > 0) {
                return \Response::json(array("arrBranchList" => $arrReturn, "allowSave" => $allowSave));
            } else {
                return -1;
            }
        } catch (\Exception $e) {

            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    public function store() {
        try {

            $arrGlobalQuarter = array("Q1" => 1, "Q2" => 2, "Q3" => 3, "Q4" => 4);
            $arrGlobalMonth = array("Jan" => 1, "Feb" => 2, "Mar" => 3, "Apr" => 4, "May" => 5, "Jun" => 6, "Jul" => 7, "Aug" => 8, "Sep" => 9, "Oct" => 10, "Nov" => 11, "Dec" => 12);

            $txttargetamount = Input::get('txttargetamount');
            $datStartDate = Input::get('datStartDate');
            $datEndDate = Input::get('datEndDate');
            $strQuarter = Input::get('strQuarter');
            $arrData = Input::get('arrData');
            $arrData = json_decode($arrData);

            $arrQua = explode(" ", $strQuarter);
            $Quarter = $arrGlobalQuarter["$arrQua[0]"];
            $Year = $arrQua[1];

            $datStartDate = explode(' ', $datStartDate);
            $datStartDate = $datStartDate[3] . '-' . $arrGlobalMonth["$datStartDate[1]"] . '-' . $datStartDate[2];
            $datStartDate = date_format(date_create($datStartDate), "Y-m-d");

            $datEndDate = explode(' ', $datEndDate);
            $datEndDate = $datEndDate[3] . '-' . $arrGlobalMonth["$datEndDate[1]"] . '-' . $datEndDate[2];
            $datEndDate = date_format(date_create($datEndDate), "Y-m-d");

            for ($i = 0; $i < count($arrData); $i++) {

                $counts = DB::table('sales_target')
                        ->where('target_quarter', $Quarter)
                        ->where('branch_id', $arrData[$i])
                        ->count();

                if ($counts > 0) {
                    DB::table('sales_target')
                            ->where('branch_id', $arrData[$i])
                            ->where('target_quarter', $Quarter)
                            ->update([
                                'start_date' => $datStartDate,
                                'end_date' => $datEndDate,
                                'target_quarter' => $Quarter,
                                'target_year' => $Year,
                                'target_amount' => $txttargetamount
                    ]);
                } else {
                    $targetmodel = new Sales_target();
                    $targetmodel->branch_id = $arrData[$i];
                    $targetmodel->start_date = $datStartDate;
                    $targetmodel->end_date = $datEndDate;
                    $targetmodel->duration_type = "quarterly";
                    $targetmodel->target_quarter = $Quarter;
                    $targetmodel->target_year = $Year;
                    $targetmodel->target_amount = $txttargetamount;
                    $targetmodel->status = 1;
                    $targetmodel->save();
                }
            }

            Toastr::success('Targets Assigned Successfully !', $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $analystbranches = DB::table('branches_to_analyst')
                ->select('branches_to_analyst.*')
                ->whereRaw("analyst_id=$login_id AND status=1")
                ->pluck("branch_id")
                ->toArray();
            
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $searchbycode = Input::get('searchbycode');
        $searchbyquarter = Input::get('searchbyquarter');
        $corder = Input::get('corder');
        $camount = Input::get('camount');

        $sortordname = Input::get('sortordname');
        $sortordercode = Input::get('sortordercode');
        $sortordquarter = Input::get('sortordquarter');
        $sortordamount = Input::get('sortordamount');
        
        $sortOrdDefault='';
        if($sortordname=='' && $sortordercode=='' && $sortordquarter=='' && $sortordamount==''){
            $sortOrdDefault='ASC';
        }

        $targets = DB::table('sales_target')
                ->select('sales_target.*', 'm.branch_code as branch_code', 'm.name as name', 'm.id as branch_id', db::raw("concat('Q',sales_target.target_quarter,' ',sales_target.target_year) as quarter"))
                ->leftjoin('master_resources as m', 'sales_target.branch_id', '=', 'm.id')
                ->whereraw('sales_target.status=1') 
                ->whereraw('m.status!=2')
                ->whereIn('branch_id', $analystbranches)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(m.name like '$searchbyname%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("(m.branch_code like '$searchbycode%')");
                })
                ->when($searchbyquarter, function ($query) use ($searchbyquarter) {
                    $quarter = explode("-", $searchbyquarter);
                    return $query->whereRaw("(sales_target.target_quarter=$quarter[0] AND sales_target.target_year=$quarter[1])");
                })
                ->when($camount, function ($query) use ($camount, $corder) {
                    return $query->whereRaw("sales_target.target_amount $corder $camount");
                })
                ->when($sortordercode, function ($query) use ($sortordercode) {
                    return $query->orderby('m.branch_code', $sortordercode);
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('m.name', $sortordname);
                })
                ->when($sortordquarter, function ($query) use ($sortordquarter) {
                    return $query->orderby('sales_target.target_year', $sortordquarter);
                })
                ->when($sortordamount, function ($query) use ($sortordamount) {
                    return $query->orderby('sales_target.target_amount', $sortordamount);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('sales_target.created_at', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('SalesTarget', function($excel) use($targets){
                 // Set the title
                $excel->setTitle('Sales Targets');
                
                $excel->sheet('Sales Targets', function($sheet) use($targets){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Sales Targets');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Branch Code', 'Name',"Target Quarter","Target Amount"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($targets);$i++){
                        
                        $sheet->setCellValue('A'.$chrRow, $targets[$i]->branch_code);
                        $sheet->setCellValue('B'.$chrRow, $targets[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $targets[$i]->quarter);
                        $sheet->setCellValue('D'.$chrRow, $targets[$i]->target_amount);
                            
                        $sheet->cells('A'.$chrRow.':D'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });
                        
                        $chrRow++;
                    }

                });
                
            })->export('xls');
            
        } else{

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
                <div style="text-align:center;"><h1>Sales Targets</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Branch Code </td>
                                <td style="padding:10px 5px;color:#fff;"> Name </td>
                                <td style="padding:10px 5px;color:#fff;"> Target Quarter </td>
                                <td style="padding:10px 5px;color:#fff;"> Target Amount </td>
                            </tr>
                        </thead>
                        <tbody class="tbltarget" id="tbltarget" >';
            $slno=0;
            foreach ($targets as $target) {
                
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $target->branch_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $target->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $target->quarter . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $target->target_amount . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_target_list.pdf');
        }
    }
    
}
