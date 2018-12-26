<?php

namespace App\Http\Controllers\Requisitions;

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
use App\Notifications\RequisitionNotification;
use App\Helpers\CategoryHierarchy;
use App\Models\Requisition;
use App\Models\Requisitionhistory;
use App\Models\Requisition_activity;
use App\Models\Requisition_items;
use App\Models\Requisitionitemhistory;
use App\Services\Commonfunctions;
use Customhelper;
use DB;
use App;
use PDF;
use Excel;

class CommonController extends Controller {

    public function inbox(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name')
                        ->leftjoin('requisition_types','requisition_types.id', '=', 'requisition.requisition_type')
                        ->where(['next_approver_id' => $login_id])
                        ->whereRaw("requisition.status NOT IN (4,5,6)")
                        ->orderby('requisition.created_at', 'DESC')
                        ->paginate($paginate);
            $requisitionTypes = DB::table('requisition_types')
                    ->select('requisition_types.*')
                    ->where('requisition_types.status','=',1)
                    ->get();
            
            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchbyname = Input::get('searchbyname');
                $searchbytype = Input::get('searchbytype');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordtitle = Input::get('sortordtitle');
                $sortordtype = Input::get('sortordtype');
                $sortordcode = Input::get('sortordcode');

                if ($createdatfrom != '') {
                    $createdatfrom = explode('-', $createdatfrom);
                    $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
                }
                if ($createdatto != '') {
                    $createdatto = explode('-', $createdatto);
                    $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
                }
            
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $sortOrdDefault = '';
                if ($sortordtitle == '' && $sortordcode=='' && $sortordtype=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name')
                        ->leftjoin('requisition_types','requisition_types.id', '=', 'requisition.requisition_type')
                        ->where(['next_approver_id' => $login_id])
                        ->whereRaw("requisition.status NOT IN (4,5,6)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("requisition.requisition_code like '$searchbycode%' ");
                        })
                        ->when($searchbyname, function ($query) use ($searchbyname) {
                            return $query->whereRaw("requisition.title like '$searchbyname%' ");
                        })
                        ->when($searchbytype, function ($query) use ($searchbytype) {
                            return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('requisition.requisition_code', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortordtype, function ($query) use ($sortordtype) {
                            return $query->orderby('requisition_types.name', $sortordtype);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('requisition.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
                return view('requisitions/purchase_requisition/inbox_results', array('requisitions' => $requisitions));
            }
            return view('requisitions/purchase_requisition/inbox', array('requisitions' => $requisitions,'requisitionTypes' => $requisitionTypes));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/purchase_requisition/inbox');
        }
    }

    public function outbox(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $requisitions = DB::table('requisition')
                    ->select('requisition.*','requisition_types.name')
                    ->leftjoin('requisition_types','requisition_types.id', '=', 'requisition.requisition_type')
                    ->where(['created_by' => $login_id])
                    ->whereRaw("requisition.status NOT IN (6,7)")
                    ->orderby('requisition.created_at', 'DESC')
                    ->paginate($paginate);
            $requisitionTypes = DB::table('requisition_types')
                    ->select('requisition_types.*')
                    ->where('requisition_types.status','=',1)
                    ->get();

            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchbyname = Input::get('searchbyname');
                $searchbytype = Input::get('searchbytype');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordtitle = Input::get('sortordtitle');
                $sortordtype = Input::get('sortordtype');
                $sortordcode = Input::get('sortordcode');

                if ($createdatfrom != '') {
                    $createdatfrom = explode('-', $createdatfrom);
                    $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
                }
                if ($createdatto != '') {
                    $createdatto = explode('-', $createdatto);
                    $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
                }
            
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $sortOrdDefault = '';
                if ($sortordtitle == '' && $sortordcode=='' && $sortordtype=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name')
                        ->leftjoin('requisition_types','requisition_types.id', '=', 'requisition.requisition_type')
                        ->where(['created_by' => $login_id])
                        ->whereRaw("requisition.status NOT IN (6,7)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("requisition.requisition_code like '$searchbycode%' ");
                        })
                        ->when($searchbyname, function ($query) use ($searchbyname) {
                            return $query->whereRaw("requisition.title like '$searchbyname%' ");
                        })
                        ->when($searchbytype, function ($query) use ($searchbytype) {
                            return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('requisition.requisition_code', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortordtype, function ($query) use ($sortordtype) {
                            return $query->orderby('requisition_types.name', $sortordtype);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('requisition.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);

                return view('requisitions/purchase_requisition/outbox_results', array('requisitions' => $requisitions));
            }
            return view('requisitions/purchase_requisition/outbox', array('requisitions' => $requisitions,'requisitionTypes' => $requisitionTypes));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/purchase_requisition');
        }
    }

    public function autocompleteemployees() {
        try {
            $searchkey = Input::get('searchkey');
            $common = new Commonfunctions();
            $result = $common->autocompleteemployees($searchkey);

            return \Response::json($result);
        } catch (\Exception $e) {
            return -1;
        }
    }
    
    public function autocompletegeneralledgers() {
        try {
            $searchkey = Input::get('searchkey');
            $common = new Commonfunctions();
            $result = $common->autocompletegeneralledgers($searchkey);

            return \Response::json($result);
        } catch (\Exception $e) {
            return -1;
        }
    }

    public function autocompleteinventory() {
        try {
            $searchkey = Input::get('searchkey');
            $preferredonly = Input::get('preferredonly');
            $supplierid = Input::get('supplierid');

            $common = new Commonfunctions();
            $result = $common->autocompleteinventory($searchkey, $preferredonly, $supplierid);

            return \Response::json($result);
        } catch (\Exception $e) {
            return -1;
        }
    }
    
    public function getrfqdata() {
        try {
            $searchkey = Input::get('searchkey');
            $supplierid = Input::get('supplierid');

            $common = new Commonfunctions();
            $result = $common->getrfqdata($searchkey,$supplierid);
            
            return \Response::json($result);
        } catch (\Exception $e) {
            
            return -1;
        }
    }

    public function getsupplierdata() {
        try {
            $supplierid = Input::get('supplier_id');
            
            $common = new Commonfunctions();
            $result = $common->getsupplierdata($supplierid);
             return \Response::json($result);
        } catch (\Exception $e) {
            return -1;
        }
    } 
    
    public function getgeneralledgerdata() {
        try {
            $supplierid = Input::get('supplier_id');
          
            $common = new Commonfunctions();
            $result = $common->getgeneralledgerdata($supplierid);
            
            return \Response::json($result);
        } catch (\Exception $e) {
            return -1;
        }
    }
    
    public function getTransactionHistory() {
        try {
            $requisition_code = Input::get('requisition_code');
            
            $common = new Commonfunctions();
            $result = $common->getTransactionHistory($requisition_code);

            return \Response::json($result);
        } catch (\Exception $e) {
            return -1;
        }
    }
    
    public function checkpendinpaymentexist() {
        try {
            $arrids = Input::get('arrids');
            $arrReqIds = json_decode($arrids);

            $strempids=implode(",", $arrReqIds);
            $arrids=explode(",", $strempids);
        
            $reqdata = DB::table('ac_payment_advice_details')
                    ->select('requisition_code','requisition_id')
                    ->leftjoin('ac_payment_advice','payment_advice_id', '=', 'ac_payment_advice.id')
                    ->whereIn('requisition_id',$arrids)
                    ->whereRaw("ac_payment_advice.status=1")
                    ->distinct("requisition_code")
                    ->get();
           
            $codes = $reqdata->pluck("requisition_code")->toArray();
            
            $intpendingpaymentexist=0;
            $strcodes='';
            if(count($codes)>0){
                $intpendingpaymentexist=1;
                $strcodes=implode(", ", $codes);
            }
            return \Response::json(array('intpendingpaymentexist'=>$intpendingpaymentexist,'strcodes'=>$strcodes,'reqdata'=>$reqdata));
        } catch (\Exception $e) {
            return -1;
        }
    }
    
        // Generate PDF funcion
    public function exportdatainbox() {
        
        if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
                $searchbyname = Input::get('searchbyname');
                $searchbytype = Input::get('searchbytype');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordtitle = Input::get('sortordtitle');
                $sortordtype = Input::get('sortordtype');
                $sortordcode = Input::get('sortordcode');

                if ($createdatfrom != '') {
                    $createdatfrom = explode('-', $createdatfrom);
                    $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
                }
                if ($createdatto != '') {
                    $createdatto = explode('-', $createdatto);
                    $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
                }
            
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $sortOrdDefault = '';
                if ($sortordtitle == '' && $sortordcode=='' && $sortordtype=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name')
                        ->leftjoin('requisition_types','requisition_types.id', '=', 'requisition.requisition_type')
                        ->where(['created_by' => $login_id])
                        ->whereRaw("requisition.status NOT IN (6,7)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("requisition.requisition_code like '$searchbycode%' ");
                        })
                        ->when($searchbyname, function ($query) use ($searchbyname) {
                            return $query->whereRaw("requisition.title like '$searchbyname%' ");
                        })
                        ->when($searchbytype, function ($query) use ($searchbytype) {
                            return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('requisition.requisition_code', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortordtype, function ($query) use ($sortordtype) {
                            return $query->orderby('requisition_types.name', $sortordtype);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('requisition.created_at', $sortOrdDefault);
                        })
                        ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('Requisition Inbox', function($excel) use($requisitions){
                 // Set the title
                $excel->setTitle('Requisition Inbox');
                
                $excel->sheet('Requisition Inbox', function($sheet) use($requisitions){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Requisition Inbox');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Requsition Code', 'Title','Type','Date'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($requisitions);$i++){
                        $sheet->setCellValue('A'.$chrRow, $requisitions[$i]->requisition_code);
                        $sheet->setCellValue('B'.$chrRow, $requisitions[$i]->title);
                        $sheet->setCellValue('C'.$chrRow, $requisitions[$i]->name);
                        $sheet->setCellValue('D'.$chrRow, date("d-m-Y", strtotime($requisitions[$i]->created_at)));
                            
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
                <title>Requisition Inbox</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h1>Requisition Inbox</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Requsition Code </td>
                                <td style="padding:10px 10px;color:#fff;"> Title </td>
                                <td style="padding:10px 5px;color:#fff;"> Type </td>
                                <td style="padding:10px 5px;color:#fff;"> Date </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($requisitions as $cat) {
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->requisition_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->title . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($cat->created_at)) . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_requsition_inbox_report.pdf');
        }
    }
    
    public function exportdataoutbox() {
        
        if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
                $searchbyname = Input::get('searchbyname');
                $searchbytype = Input::get('searchbytype');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordtitle = Input::get('sortordtitle');
                $sortordtype = Input::get('sortordtype');
                $sortordcode = Input::get('sortordcode');

                if ($createdatfrom != '') {
                    $createdatfrom = explode('-', $createdatfrom);
                    $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
                }
                if ($createdatto != '') {
                    $createdatto = explode('-', $createdatto);
                    $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
                }
            
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $sortOrdDefault = '';
                if ($sortordtitle == '' && $sortordcode=='' && $sortordtype=='') {
                    $sortOrdDefault = 'DESC';
                }
                
                $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name')
                        ->leftjoin('requisition_types','requisition_types.id', '=', 'requisition.requisition_type')
                        ->where(['created_by' => $login_id])
                        ->whereRaw("requisition.status NOT IN (6,7)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("requisition.requisition_code like '$searchbycode%' ");
                        })
                        ->when($searchbyname, function ($query) use ($searchbyname) {
                            return $query->whereRaw("requisition.title like '$searchbyname%' ");
                        })
                        ->when($searchbytype, function ($query) use ($searchbytype) {
                            return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('requisition.requisition_code', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortordtype, function ($query) use ($sortordtype) {
                            return $query->orderby('requisition_types.name', $sortordtype);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('requisition.created_at', $sortOrdDefault);
                        })
                        ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('Requisition Outbox', function($excel) use($requisitions){
                 // Set the title
                $excel->setTitle('Requisition Outbox');
                
                $excel->sheet('Requisition Outbox', function($sheet) use($requisitions){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Requisition Outbox');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Requsition Code', 'Title','Type','Date'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($requisitions);$i++){
                        $sheet->setCellValue('A'.$chrRow, $requisitions[$i]->requisition_code);
                        $sheet->setCellValue('B'.$chrRow, $requisitions[$i]->title);
                        $sheet->setCellValue('C'.$chrRow, $requisitions[$i]->name);
                        $sheet->setCellValue('D'.$chrRow, date("d-m-Y", strtotime($requisitions[$i]->created_at)));
                            
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
                <title>Requisition Outbox</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h1>Requisition Inbox</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Requsition Code </td>
                                <td style="padding:10px 10px;color:#fff;"> Title </td>
                                <td style="padding:10px 5px;color:#fff;"> Type </td>
                                <td style="padding:10px 5px;color:#fff;"> Date </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($requisitions as $cat) {
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->requisition_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->title . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($cat->created_at)) . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_requsition_outbox_report.pdf');
        }
    }
    
    
    public function getsupplierdataQuarter() {
        try {
            $supplierid = Input::get('supplier_id');
          $type="Supplier";
            
             $arrMonthQuarter = array("01" => 1, "02" => 1, "03" => 1, "04" => 2, "05" => 2, "06" => 2, "07" => 3, "08" => 3, "09" => 3, "10" => 4, "11" => 4, "12" => 4);
          
            $strQuarter = Input::get('createddate'); 
            $arrQua = explode(" ", $strQuarter); 
            $date = $arrQua[0]; 
            $dat = explode("-", $date); 
            $year = $dat[0];   
            $month = $dat[1];
            $quarter = $arrMonthQuarter["$month"];
            
              if($quarter == ''){
                  $year = date('Y');
                  $month = date("m");
                
            if ($month < 4) {
                $quarter = 1;
            }
            if ($month > 3 && $month < 7) {
                $quarter = 2;
            }
            if ($month > 6 && $month < 10) {
                $quarter = 3;
            }
            if ($month > 9) {
                $quarter = 4;
            }
            }
            
            
            $common = new Commonfunctions();
            $result = $common->getsupplierdataQuarter($year,$month,$quarter,$type,$supplierid);
            
            return \Response::json($result);
        } catch (\Exception $e) {
            return -1;
        }
    } 
}