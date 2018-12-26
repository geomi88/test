<?php

namespace App\Http\Controllers\Finance;

use App\Events\RequisitionSubmitted;
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
use Kamaln7\Toastr\Facades\Toastr;
use App\Helpers\CategoryHierarchy;
use App\Services\Commonfunctions;
use App\Services\Paymentadvice;
use App\Models\Payment_advice;
use App\Models\Payment_advice_details;
use App\Models\Payment_advice_activity;
use Customhelper;
use DB;
use PDF;
use Excel;

class RemittancereportController extends Controller {
    
    public function remittance_list(Request $request){
        
        try {
            
            $paginate = Config::get('app.PAGINATE');

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            $curDate=date('Y-m-d');
            $customdate=Input::get('date');
            $type= Input::get("type");
            $payments = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*','ac_accounts.code','ac_accounts.first_name',
                            db::raw("(case when ac_payment_advice.remittance_number IS NULL then DATEDIFF('$curDate',(select max(created_at) from ac_payment_advice_activity where payment_advice_id=ac_payment_advice.id AND ac_payment_advice_activity.action=1 AND ac_payment_advice_activity.status=1)) else '' end) as daycount"),
                            db::raw("(case when payment_type=1 then 'Cheque' when payment_type=2 then 'Cash' when payment_type=3 then 'Online' end) as paymode"))
//                    ->leftjoin('ac_payment_advice_activity', function($join) {
//                        $join->on('ac_payment_advice_activity.payment_advice_id','=','ac_payment_advice.id')
//                        ->whereRaw("ac_payment_advice_activity.action=1 AND ac_payment_advice_activity.status=1 AND ac_payment_advice_activity.id=(select max(id) from ac_payment_advice_activity where payment_advice_id=ac_payment_advice.id)");
//                    })
                    ->leftjoin('ac_accounts', function($join) {
                        $join->on('ac_payment_advice.payment_to_id','=','ac_accounts.type_id');
                        $join->on('ac_payment_advice.to_ledger_type','=','ac_accounts.type');
                    })
                    ->when($customdate, function ($query) use ($customdate) {
                        return $query->whereRaw("date(ac_payment_advice.remitted_date)= '$customdate' ");
                    })
                    ->when($type, function($query) use ($type){
                        if($type == 'notpaid'){
                            return $query->whereRaw("(ac_payment_advice.remittance_number='' OR ac_payment_advice.remittance_number IS NULL)");
                        }
                    })
                    ->whereRaw("ac_payment_advice.status=2")
                    ->orderby('ac_payment_advice.created_at', 'DESC')
                    ->paginate($paginate);
            
            if($type == 'notpaid'){
                $pendingPayments = DB::table('ac_payment_advice')
                    ->whereRaw("ac_payment_advice.remittance_number='' OR ac_payment_advice.remittance_number IS NULL  AND status=2")->count();
            }
            else{
                $pendingPayments = DB::table('ac_payment_advice')
                    ->whereRaw("remittance_number IS NULL AND status=2")->count();
            }
            if ($request->ajax()) {
                
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                
                $searchbycode = Input::get('searchbycode');
                $searchbysucode = Input::get('searchbysucode');
                $searchbyname = Input::get('searchbyname');
                $searchbymode = Input::get('searchbymode');
                $searchbyremitance = Input::get('searchbyremitance');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $remitteddate_from = Input::get('remitteddate_from');
                $remitteddate_to = Input::get('remitteddate_to');
                $sortordname = Input::get('sortordname');
                $sortorddate = Input::get('sortorddate');
                $sortordcode = Input::get('sortordcode');
                if($type == 'notpaid'){
                    $searchpaidstatus = 2;
                }
                else{
                    $searchpaidstatus = Input::get('searchpaidstatus');
                }
                if ($createdatfrom != '') {
                    $createdatfrom = explode('-', $createdatfrom);
                    $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
                }
                if ($createdatto != '') {
                    $createdatto = explode('-', $createdatto);
                    $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
                }
                
                if ($remitteddate_from != '') {
                    $remitteddate_from = explode('-', $remitteddate_from);
                    $remitteddate_from = $remitteddate_from[2] . '-' . $remitteddate_from[1] . '-' . $remitteddate_from[0];
                }
                
                if ($remitteddate_to != '') {
                    $remitteddate_to = explode('-', $remitteddate_to);
                    $remitteddate_to = $remitteddate_to[2] . '-' . $remitteddate_to[1] . '-' . $remitteddate_to[0];
                }
                
                $sortOrdDefault = '';
                if ($sortordname=='' && $sortorddate=='' && $sortordcode=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $payments = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*','ac_accounts.code','ac_accounts.first_name',
                            db::raw("(case when ac_payment_advice.remittance_number IS NULL then DATEDIFF('$curDate',(select max(created_at) from ac_payment_advice_activity where payment_advice_id=ac_payment_advice.id AND ac_payment_advice_activity.action=1 AND ac_payment_advice_activity.status=1)) else '' end) as daycount"),
                            db::raw("(case when payment_type=1 then 'Cheque' when payment_type=2 then 'Cash' when payment_type=3 then 'Online' end) as paymode"))
//                    ->leftjoin('ac_payment_advice_activity', function($join) {
//                        $join->on('ac_payment_advice_activity.payment_advice_id','=','ac_payment_advice.id')
//                        ->whereRaw("ac_payment_advice_activity.action=1 AND ac_payment_advice_activity.status=1 AND ac_payment_advice_activity.id=(select max(id) from ac_payment_advice_activity where payment_advice_id=ac_payment_advice.id)");
//                    })
                    ->leftjoin('ac_accounts', function($join) {
                        $join->on('ac_payment_advice.payment_to_id','=','ac_accounts.type_id');
                        $join->on('ac_payment_advice.to_ledger_type','=','ac_accounts.type');
                    })
                    ->when($searchbymode, function ($query) use ($searchbymode) {
                        return $query->whereRaw("ac_payment_advice.payment_type=$searchbymode");
                    })
                    ->whereRaw("ac_payment_advice.status=2")
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("ac_payment_advice.payment_code like '%$searchbycode%' ");
                    })
                    ->when($searchbysucode, function ($query) use ($searchbysucode) {
                        return $query->whereRaw("ac_accounts.code like '%$searchbysucode%' ");
                    })
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("ac_accounts.first_name like '$searchbyname%' ");
                    })
                    ->when($searchbyremitance, function ($query) use ($searchbyremitance) {
                        return $query->whereRaw("ac_payment_advice.remittance_number like '$searchbyremitance%' ");
                    })
                    ->when($createdatfrom, function ($query) use ($createdatfrom) {
                        return $query->whereRaw("date(ac_payment_advice.created_at)>= '$createdatfrom' ");
                    })
                    ->when($createdatto, function ($query) use ($createdatto) {
                        return $query->whereRaw("date(ac_payment_advice.created_at)<= '$createdatto' ");
                    })
                    ->when($remitteddate_from, function ($query) use ($remitteddate_from) {
                        return $query->whereRaw("date(ac_payment_advice.remitted_date)>= '$remitteddate_from' ");
                    })
                    ->when($remitteddate_to, function ($query) use ($remitteddate_to) {
                        return $query->whereRaw("date(ac_payment_advice.remitted_date)<= '$remitteddate_to' ");
                    })
                    ->when($searchpaidstatus, function ($query) use ($searchpaidstatus) {
                        if($searchpaidstatus==2){
                            return $query->whereRaw("(ac_payment_advice.remittance_number='' OR ac_payment_advice.remittance_number IS NULL)");
                        }else{
                            return $query->whereRaw("(ac_payment_advice.remittance_number!='' OR ac_payment_advice.remittance_number IS NOT NULL)");
                        }
                        
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('ac_payment_advice.payment_code', $sortordcode);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('ac_payment_advice.created_at', $sortorddate);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);
                    
                return view('finance/remittance_report/remittance_list_results',array('payments'=>$payments));
            }
            
            return view('finance/remittance_report/remittance_list',array('payments'=>$payments,'pendingPayments'=>$pendingPayments,'customdate'=>$customdate,"type"=>$type));
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!');
            return Redirect::to('finance/remittance_report');
        }

    }
    
    //Generatr PDF, EXCEL
    public function exportdata() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbysucode = Input::get('searchbysucode');
        $searchbyname = Input::get('searchbyname');
        $searchbymode = Input::get('searchbymode');
        $searchbyremitance = Input::get('searchbyremitance');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $remitteddate_from = Input::get('remitteddate_from');
        $remitteddate_to = Input::get('remitteddate_to');
        $sortordname = Input::get('sortordname');
        $sortorddate = Input::get('sortorddate');
        $sortordcode = Input::get('sortordcode');
        $searchpaidstatus = Input::get('searchpaidstatus');

        if ($createdatfrom != '') {
            $createdatfrom = explode('-', $createdatfrom);
            $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
        }
        if ($createdatto != '') {
            $createdatto = explode('-', $createdatto);
            $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
        }

        if ($remitteddate_from != '') {
            $remitteddate_from = explode('-', $remitteddate_from);
            $remitteddate_from = $remitteddate_from[2] . '-' . $remitteddate_from[1] . '-' . $remitteddate_from[0];
        }
        
        if ($remitteddate_to != '') {
            $remitteddate_to = explode('-', $remitteddate_to);
            $remitteddate_to = $remitteddate_to[2] . '-' . $remitteddate_to[1] . '-' . $remitteddate_to[0];
        }
                
        $sortOrdDefault = '';
        if ($sortordname=='' && $sortorddate=='' && $sortordcode=='') {
            $sortOrdDefault = 'DESC';
        }

        $curDate=date('Y-m-d');
        
        $payments = DB::table('ac_payment_advice')
                ->select('ac_payment_advice.*','ac_accounts.code','ac_accounts.first_name',
                        db::raw("(case when ac_payment_advice.remittance_number IS NULL then DATEDIFF('$curDate',(select max(created_at) from ac_payment_advice_activity where payment_advice_id=ac_payment_advice.id AND ac_payment_advice_activity.action=1 AND ac_payment_advice_activity.status=1)) else '' end) as daycount"),
                        db::raw("(case when payment_type=1 then 'Cheque' when payment_type=2 then 'Cash' when payment_type=3 then 'Online' end) as paymode"))
//                ->leftjoin('ac_payment_advice_activity', function($join) {
//                    $join->on('ac_payment_advice.id','=','ac_payment_advice_activity.payment_advice_id')
//                    ->whereRaw("ac_payment_advice_activity.action=1 AND ac_payment_advice_activity.status=1 AND ac_payment_advice_activity.id=(select max(id) from ac_payment_advice_activity where payment_advice_id=ac_payment_advice.id)");
//                })
                ->leftjoin('ac_accounts', function($join) {
                    $join->on('ac_payment_advice.payment_to_id','=','ac_accounts.type_id');
                    $join->on('ac_payment_advice.to_ledger_type','=','ac_accounts.type');
                })
                ->when($searchbymode, function ($query) use ($searchbymode) {
                    return $query->whereRaw("ac_payment_advice.payment_type=$searchbymode");
                })
                ->whereRaw("ac_payment_advice.status=2")
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("ac_payment_advice.payment_code like '%$searchbycode%' ");
                })
                ->when($searchbysucode, function ($query) use ($searchbysucode) {
                    return $query->whereRaw("ac_accounts.code like '%$searchbysucode%' ");
                })
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("ac_accounts.first_name like '$searchbyname%' ");
                })
                ->when($searchbyremitance, function ($query) use ($searchbyremitance) {
                    return $query->whereRaw("ac_payment_advice.remittance_number like '$searchbyremitance%' ");
                })
                ->when($createdatfrom, function ($query) use ($createdatfrom) {
                    return $query->whereRaw("date(ac_payment_advice.created_at)>= '$createdatfrom' ");
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(ac_payment_advice.created_at)<= '$createdatto' ");
                })
                ->when($searchpaidstatus, function ($query) use ($searchpaidstatus) {
                    if($searchpaidstatus==2){
                        return $query->whereRaw("(ac_payment_advice.remittance_number='' OR ac_payment_advice.remittance_number IS NULL)");
                    }else{
                        return $query->whereRaw("(ac_payment_advice.remittance_number!='' OR ac_payment_advice.remittance_number IS NOT NULL)");
                    }

                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('ac_payment_advice.payment_code', $sortordcode);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('ac_payment_advice.created_at', $sortorddate);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                })
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Remittance_Report', function($excel) use($payments) {
                // Set the title
                $excel->setTitle('Remittance Report');

                $excel->sheet('Remittance Report', function($sheet) use($payments) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Remittance Report');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:J3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Payment Code','Date','Remitted Date', 'Party Code','Party Name','Payment Mode','Remittance No.','Status','Amount','Day Count'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:J5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($payments); $i++) {
                        
                        if($payments[$i]->remittance_number != ''){
                            $paystatus='Paid';
                        }else{
                            $paystatus='Not Paid';
                        }
                        
                        if(isset($payments[$i]->remitted_date)){
                            $remidatedate=date("d-m-Y", strtotime($payments[$i]->remitted_date));
                        }else{
                            $remidatedate='';
                        }
                        
                        $sheet->setCellValue('A' . $chrRow, $payments[$i]->payment_code);
                        $sheet->setCellValue('B' . $chrRow, date("d-m-Y", strtotime($payments[$i]->created_at)));
                        $sheet->setCellValue('C' . $chrRow, $remidatedate);
                        $sheet->setCellValue('D' . $chrRow, $payments[$i]->code);
                        $sheet->setCellValue('E' . $chrRow, $payments[$i]->first_name);
                        $sheet->setCellValue('F' . $chrRow, $payments[$i]->paymode);
                        $sheet->setCellValue('G' . $chrRow, $payments[$i]->remittance_number);
                        $sheet->setCellValue('H' . $chrRow, $paystatus);
                        $sheet->setCellValue('I' . $chrRow, $payments[$i]->total_amount);
                        $sheet->setCellValue('J' . $chrRow, $payments[$i]->daycount);
                        
                        $sheet->cells('A' . $chrRow . ':J' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        }
    }
}