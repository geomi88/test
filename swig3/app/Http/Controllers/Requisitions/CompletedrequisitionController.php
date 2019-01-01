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
use App\Helpers\CategoryHierarchy;
use App\Models\Requisition;
use App\Models\Requisitionhistory;
use App\Models\Requisition_activity;
use App\Models\Requisition_items;
use App\Models\Requisitionitemhistory;
use App\Services\Commonfunctions;
use Customhelper;
use DB;
use PDF;
use Excel;

class CompletedrequisitionController extends Controller {
    
    public function requisitionlist(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $requisitions = DB::table('requisition')
                        ->select('requisition.*','requisition_types.name as req_name')
                        ->leftjoin('requisition_types','requisition_types.id','=','requisition.requisition_type')
                        ->where('requisition_types.name','!=','Owner Drawings Requisition')
                      //  ->where(['next_approver_id' => $login_id])
                        ->whereRaw("requisition.status IN (1,4,5)")
                        ->orderby('requisition.created_at', 'DESC')
                        ->paginate($paginate);
            $requisitionTypes = DB::table('requisition_types')
                    ->select('requisition_types.*')
                    ->where('requisition_types.name','!=','Owner Drawings Requisition')
                    ->where('requisition_types.status','=',1)
                    ->get();
            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchbytitle = Input::get('searchbytitle');
                $status= Input::get('status');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                 $searchbytype = Input::get('searchbytype');
                $sortordtitle = Input::get('sortordtitle');
                $sortordcode = Input::get('sortordcode');
                $sortordtype = Input::get('sortordtype');

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
                        ->select('requisition.*','requisition_types.name as req_name')
                        ->leftjoin('requisition_types','requisition_types.id','=','requisition.requisition_type')
                        ->where('requisition_types.name','!=','Owner Drawings Requisition')
                      //  ->where(['next_approver_id' => $login_id])
                        ->whereRaw("requisition.status  IN (1,4,5)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("requisition.requisition_code like '%$searchbycode%' ");
                        })
                        ->when($searchbytitle, function ($query) use ($searchbytitle) {
                            return $query->whereRaw("requisition.title like '$searchbytitle%' ");
                        })
                        ->when($status, function ($query) use ($status) {
                            return $query->whereRaw("requisition.status= $status ");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                        })
                        ->when($searchbytype, function ($query) use ($searchbytype) {
                            return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
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
                        
                return view('requisitions/completed_requisition/completed_requisitionresults', array('requisitions' => $requisitions));
            }
            return view('requisitions/completed_requisition/completed_requisition', array('requisitions' => $requisitions,'requisitionTypes'=>$requisitionTypes));
        } catch (\Exception $e) {
      
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }
    
    public function requisitionforpayment(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            
            $requisitions = DB::table('requisition')
                    ->select('requisition.*', 'requisition_types.name')
                    ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')
                    ->whereRaw("requisition.status=4 AND convert_to_payment=1 AND payment_generated=2")
                    ->orderby('requisition.updated_at', 'DESC')
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
                        ->select('requisition.*', 'requisition_types.name')
                        ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')
                        ->whereRaw("requisition.status=4 AND convert_to_payment=1 AND payment_generated=2")
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
                            return $query->whereRaw("date(requisition.updated_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(requisition.updated_at)<= '$createdatto' ");
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
                            return $query->orderby('requisition.updated_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
                return view('requisitions/requisitionfor_payment/results', array('requisitions' => $requisitions));
            }
            return view('requisitions/requisitionfor_payment/index', array('requisitions' => $requisitions,'requisitionTypes'=>$requisitionTypes));
        } catch (\Exception $e) {
      
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }
    
    
    public function paymentadvicelist(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }


            $requisitions = DB::table('ac_payment_advice')
                    ->select('ac_payment_advice.*','fromdetails.first_name as fromname','fromdetails.alias_name as fromalias','todetails.first_name as toname','todetails.alias_name as toalias')
                    ->leftjoin('ac_accounts as fromdetails', function($join) {
                            $join->on('fromdetails.type_id', '=', 'ac_payment_advice.payment_from_id');
                            $join->on('fromdetails.type', '=', 'ac_payment_advice.from_ledger_type');
                    })
                    ->leftjoin('ac_accounts as todetails', function($join) {
                        $join->on('todetails.type_id', '=', 'ac_payment_advice.payment_to_id');
                        $join->on('todetails.type', '=', 'ac_payment_advice.to_ledger_type');
                    })
                    ->where('ac_payment_advice.is_owner_drawing_requsition','!=',1)
                    ->whereRaw("ac_payment_advice.status IN (1,2,3)")
                    ->orderby('ac_payment_advice.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchpaidto = Input::get('searchpaidto');
                $searchpaidby = Input::get('searchpaidby');
                $sortpaidto = Input::get('sortpaidto');
                $sortpaidby = Input::get('sortpaidby');
                $sortordcode = Input::get('sortordcode');

                $status = Input::get('status');

            
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $sortOrdDefault = '';
                if ($sortpaidby == '' && $sortordcode=='' && $sortpaidto== '') {
                    $sortOrdDefault = 'DESC';
                }
                
                $requisitions = DB::table('ac_payment_advice')
                        ->select('ac_payment_advice.*','fromdetails.first_name as fromname','fromdetails.alias_name as fromalias','todetails.first_name as toname','todetails.alias_name as toalias')
                        ->leftjoin('ac_accounts as fromdetails', function($join) {
                            $join->on('fromdetails.type_id', '=', 'ac_payment_advice.payment_from_id');
                            $join->on('fromdetails.type', '=', 'ac_payment_advice.from_ledger_type');
                        })
                        ->leftjoin('ac_accounts as todetails', function($join) {
                            $join->on('todetails.type_id', '=', 'ac_payment_advice.payment_to_id');
                            $join->on('todetails.type', '=', 'ac_payment_advice.to_ledger_type');
                        })
                        ->where('ac_payment_advice.is_owner_drawing_requsition','!=',1)
                        ->whereRaw("ac_payment_advice.status IN (1,2,3)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("ac_payment_advice.payment_code like '%$searchbycode%' ");
                        })
                        ->when($searchpaidby, function ($query) use ($searchpaidby) {
                            return $query->whereRaw("fromdetails.first_name like '$searchpaidby%' ");
                        })
                         ->when($searchpaidto, function ($query) use ($searchpaidto) {
                            return $query->whereRaw("todetails.first_name like '$searchpaidto%' ");
                        })
                         ->when($status, function ($query) use ($status) {
                            return $query->whereRaw("ac_payment_advice.status= $status ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('ac_payment_advice.payment_code', $sortordcode);
                        })
                        ->when($sortpaidto, function ($query) use ($sortpaidto) {
                            return $query->orderby('todetails.first_name', $sortpaidto);
                        }) 
                        ->when($sortpaidby, function ($query) use ($sortpaidby) {
                            return $query->orderby('fromdetails.first_name', $sortpaidby);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);
                        
               return view('requisitions/completed_requisition/completed_payment_adviceresults', array('requisitions' => $requisitions));
            }
            return view('requisitions/completed_requisition/completed_payment_advice', array('requisitions' => $requisitions));
       } catch (\Exception $e) {
          
          
           Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
          return Redirect::to('requisitions');
        }
    }
    
    public function userwiserequisitions(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $requisitions = DB::table('requisition')
                ->select('requisition.*','requisition_activity.created_at as createdat','requisition_types.name as req_name',db::raw("(case when requisition_activity.action=1 then 'Approved' when requisition_activity.action=3 then 'Rejected' else '' end) as actionstatus"))
                ->leftjoin('requisition_activity', function($join) {
                    $join->on('requisition.id','=','requisition_activity.requisition_id')
                    ->whereRaw("requisition_activity.status=1");
                })
                ->leftjoin('requisition_types','requisition_types.id','=','requisition.requisition_type')
                ->whereRaw("requisition_activity.actor_id=$login_id")
                ->orderby('requisition_activity.created_at', 'DESC')
                ->paginate($paginate);
            
            $requisitionTypes = DB::table('requisition_types')
                    ->select('requisition_types.*')
                    ->where('requisition_types.status','=',1)
                    ->get();
            
            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchbytitle = Input::get('searchbytitle');
                $status= Input::get('status');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $searchbytype = Input::get('searchbytype');
                $sortordtitle = Input::get('sortordtitle');
                $sortordcode = Input::get('sortordcode');
                $sortordtype = Input::get('sortordtype');
                $sortorddate = Input::get('sortorddate');

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
                if ($sortordtitle == '' && $sortordcode=='' && $sortordtype=='' && $sortorddate=='') {
                    $sortOrdDefault = 'DESC';
                }
                
               $requisitions = DB::table('requisition')
                    ->select('requisition.*','requisition_activity.created_at as createdat','requisition_types.name as req_name',db::raw("(case when requisition_activity.action=1 then 'Approved' when requisition_activity.action=3 then 'Rejected' else '' end) as actionstatus"))
                    ->leftjoin('requisition_activity', function($join) {
                        $join->on('requisition.id','=','requisition_activity.requisition_id')
                        ->whereRaw("requisition_activity.status=1");
                    })
                    ->leftjoin('requisition_types','requisition_types.id','=','requisition.requisition_type')
                    ->whereRaw("requisition_activity.actor_id=$login_id")
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("requisition.requisition_code like '%$searchbycode%' ");
                    })
                    ->when($searchbytitle, function ($query) use ($searchbytitle) {
                        return $query->whereRaw("requisition.title like '$searchbytitle%' ");
                    })
                    ->when($status, function ($query) use ($status) {
                        return $query->whereRaw("requisition_activity.action= $status ");
                    })
                    ->when($createdatfrom, function ($query) use ($createdatfrom) {
                        return $query->whereRaw("date(requisition_activity.created_at)>= '$createdatfrom' ");
                    })
                    ->when($createdatto, function ($query) use ($createdatto) {
                        return $query->whereRaw("date(requisition_activity.created_at)<= '$createdatto' ");
                    })
                    ->when($searchbytype, function ($query) use ($searchbytype) {
                        return $query->whereRaw("requisition.requisition_type=$searchbytype");
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
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('requisition_activity.created_at', $sortorddate);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('requisition_activity.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);
                        
                return view('requisitions/userwise_requisitions/results', array('requisitions' => $requisitions));
            }
            
            return view('requisitions/userwise_requisitions/index', array('requisitions' => $requisitions,'requisitionTypes'=>$requisitionTypes));
        } catch (\Exception $e) {
      
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }
    
    public function exportdata() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbytitle = Input::get('searchbytitle');
        $status = Input::get('status');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $searchbytype = Input::get('searchbytype');
        $sortordtitle = Input::get('sortordtitle');
        $sortordcode = Input::get('sortordcode');
        $sortordtype = Input::get('sortordtype');

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
        if ($sortordtitle == '' && $sortordcode == '' && $sortordtype == '') {
            $sortOrdDefault = 'DESC';
        }

        $requisitions = DB::table('requisition')
                ->select('requisition.*', 'requisition_types.name as req_name')
                ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')

                //  ->where(['next_approver_id' => $login_id])
                ->whereRaw("requisition.status  IN (4,5)")
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("requisition.requisition_code like '%$searchbycode%' ");
                })
                ->when($searchbytitle, function ($query) use ($searchbytitle) {
                    return $query->whereRaw("requisition.title like '$searchbytitle%' ");
                })
                ->when($status, function ($query) use ($status) {
                    return $query->whereRaw("requisition.status= $status ");
                })
                ->when($createdatfrom, function ($query) use ($createdatfrom) {
                    return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                })
                ->when($searchbytype, function ($query) use ($searchbytype) {
                    return $query->whereRaw("requisition.requisition_type like '$searchbytype%' ");
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

        if ($excelorpdf == "Excel") {

            Excel::create('Completed Requsition List', function($excel) use($requisitions) {
                // Set the title
                $excel->setTitle('Completed Requsition List');

                $excel->sheet('Completed Requsition List', function($sheet) use($requisitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Completed Requsition List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Requsition Code', 'Title', 'Date', 'Type', 'Status'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requisitions); $i++) {
                        if ($requisitions[$i]->status == 4)
                            $status = 'Approved';
                        else
                            $status = 'Rejected';

                        $sheet->setCellValue('A' . $chrRow, $requisitions[$i]->requisition_code);
                        $sheet->setCellValue('B' . $chrRow, $requisitions[$i]->title);
                        $sheet->setCellValue('C' . $chrRow, date("d-m-Y", strtotime($requisitions[$i]->created_at)));
                        $sheet->setCellValue('D' . $chrRow, $requisitions[$i]->req_name);
                        $sheet->setCellValue('E' . $chrRow, $status);

                        $sheet->cells('A' . $chrRow . ':E' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } 
    }

    public function exportrequisitionforpaymentdata() {
       
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
        if ($sortordtitle == '' && $sortordcode == '' && $sortordtype == '') {
            $sortOrdDefault = 'DESC';
        }

        $requisitions = DB::table('requisition')
                ->select('requisition.*', 'requisition_types.name')
                ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')
                ->whereRaw("requisition.status=4 AND convert_to_payment=1 AND payment_generated=2")
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
                    return $query->whereRaw("date(requisition.updated_at)>= '$createdatfrom' ");
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(requisition.updated_at)<= '$createdatto' ");
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
                    return $query->orderby('requisition.updated_at', $sortOrdDefault);
                })
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Approved_Requisitions_For_Payment', function($excel) use($requisitions) {
                // Set the title
                $excel->setTitle('Approved Requisitions For Payment');

                $excel->sheet('Approved Requisitions For Payment', function($sheet) use($requisitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('B3', 'Approved Requisitions For Payment');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Requsition Code', 'Title', 'Type', 'Date'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requisitions); $i++) {
                        $sheet->setCellValue('A' . $chrRow, $requisitions[$i]->requisition_code);
                        $sheet->setCellValue('B' . $chrRow, $requisitions[$i]->title);
                        $sheet->setCellValue('C' . $chrRow, $requisitions[$i]->name);
                        $sheet->setCellValue('D' . $chrRow, date("d-m-Y", strtotime($requisitions[$i]->updated_at)));
                        
                        $sheet->cells('A' . $chrRow . ':D' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } 
    }
    
    public function exportdata_paymentadvicelist() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchpaidto = Input::get('searchpaidto');
        $searchpaidby = Input::get('searchpaidby');
        $sortpaidto = Input::get('sortpaidto');
        $sortpaidby = Input::get('sortpaidby');
        $sortordcode = Input::get('sortordcode');

        $status = Input::get('status');


        $paginate = Input::get('pagelimit');
        if ($paginate == "") {
            $paginate = Config::get('app.PAGINATE');
        }

        $sortOrdDefault = '';
        if ($sortpaidby == '' && $sortordcode == '' && $sortpaidto == '') {
            $sortOrdDefault = 'DESC';
        }

        $requisitions = DB::table('ac_payment_advice')
                        ->select('ac_payment_advice.*','fromdetails.first_name as fromname','fromdetails.alias_name as fromalias','todetails.first_name as toname','todetails.alias_name as toalias')
                        ->leftjoin('ac_accounts as fromdetails', function($join) {
                            $join->on('fromdetails.type_id', '=', 'ac_payment_advice.payment_from_id');
                            $join->on('fromdetails.type', '=', 'ac_payment_advice.from_ledger_type');
                        })
                        ->leftjoin('ac_accounts as todetails', function($join) {
                            $join->on('todetails.type_id', '=', 'ac_payment_advice.payment_to_id');
                            $join->on('todetails.type', '=', 'ac_payment_advice.to_ledger_type');
                        })
                        ->whereRaw("ac_payment_advice.status IN (2,3)")
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("ac_payment_advice.payment_code like '%$searchbycode%' ");
                        })
                        ->when($searchpaidby, function ($query) use ($searchpaidby) {
                            return $query->whereRaw("fromdetails.first_name like '$searchpaidby%' ");
                        })
                         ->when($searchpaidto, function ($query) use ($searchpaidto) {
                            return $query->whereRaw("todetails.first_name like '$searchpaidto%' ");
                        })
                         ->when($status, function ($query) use ($status) {
                            return $query->whereRaw("ac_payment_advice.status= $status ");
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('ac_payment_advice.payment_code', $sortordcode);
                        })
                        ->when($sortpaidto, function ($query) use ($sortpaidto) {
                            return $query->orderby('todetails.first_name', $sortpaidto);
                        }) 
                        ->when($sortpaidby, function ($query) use ($sortpaidby) {
                            return $query->orderby('fromdetails.first_name', $sortpaidby);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('ac_payment_advice.created_at', $sortOrdDefault);
                        })
                        ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('All Payment Advices', function($excel) use($requisitions) {
                // Set the title
                $excel->setTitle('All Payment Advices');

                $excel->sheet('All Payment Advices', function($sheet) use($requisitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'All Payment Advices');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Payment Advice Code', 'Paid By', 'Paid To', 'Amount', 'Status'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requisitions); $i++) {
                        if ($requisitions[$i]->status == 2)
                            $status = 'Approved';
                        else
                            $status = 'Rejected';

                        $sheet->setCellValue('A' . $chrRow, $requisitions[$i]->payment_code);
                        $sheet->setCellValue('B' . $chrRow, $requisitions[$i]->fromname.' '.$requisitions[$i]->fromalias);
                        $sheet->setCellValue('C' . $chrRow, $requisitions[$i]->toname.' '.$requisitions[$i]->toalias);
                        $sheet->setCellValue('D' . $chrRow, $requisitions[$i]->total_amount);
                        $sheet->setCellValue('E' . $chrRow, $status);

                        $sheet->cells('A' . $chrRow . ':E' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        }
    }

    public function exportuserwisereq() {
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbytitle = Input::get('searchbytitle');
        $status= Input::get('status');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $searchbytype = Input::get('searchbytype');
        $sortordtitle = Input::get('sortordtitle');
        $sortordcode = Input::get('sortordcode');
        $sortordtype = Input::get('sortordtype');
        $sortorddate = Input::get('sortorddate');

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
        if ($sortordtitle == '' && $sortordcode=='' && $sortordtype=='' && $sortorddate=='') {
            $sortOrdDefault = 'DESC';
        }

       $requisitions = DB::table('requisition')
            ->select('requisition.*','requisition_activity.created_at as createdat','requisition_types.name as req_name',db::raw("(case when requisition_activity.action=1 then 'Approved' when requisition_activity.action=3 then 'Rejected' else '' end) as actionstatus"))
            ->leftjoin('requisition_activity', function($join) {
                $join->on('requisition.id','=','requisition_activity.requisition_id')
                ->whereRaw("requisition_activity.status=1");
            })
            ->leftjoin('requisition_types','requisition_types.id','=','requisition.requisition_type')
            ->whereRaw("requisition_activity.actor_id=$login_id")
            ->when($searchbycode, function ($query) use ($searchbycode) {
                return $query->whereRaw("requisition.requisition_code like '%$searchbycode%' ");
            })
            ->when($searchbytitle, function ($query) use ($searchbytitle) {
                return $query->whereRaw("requisition.title like '$searchbytitle%' ");
            })
            ->when($status, function ($query) use ($status) {
                return $query->whereRaw("requisition_activity.action= $status ");
            })
            ->when($createdatfrom, function ($query) use ($createdatfrom) {
                return $query->whereRaw("date(requisition_activity.created_at)>= '$createdatfrom' ");
            })
            ->when($createdatto, function ($query) use ($createdatto) {
                return $query->whereRaw("date(requisition_activity.created_at)<= '$createdatto' ");
            })
            ->when($searchbytype, function ($query) use ($searchbytype) {
                return $query->whereRaw("requisition.requisition_type=$searchbytype");
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
            ->when($sortorddate, function ($query) use ($sortorddate) {
                return $query->orderby('requisition_activity.created_at', $sortorddate);
            })
            ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                return $query->orderby('requisition_activity.created_at', $sortOrdDefault);
            })
            ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('User_Wise_Requsitions', function($excel) use($requisitions) {
                // Set the title
                $excel->setTitle('User Wise Requsitions');

                $excel->sheet('User Wise Requsitions', function($sheet) use($requisitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'User Wise Requsitions');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Requsition Code', 'Title', 'Type','Action Date',  'Action Taken'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requisitions); $i++) {
                        $sheet->setCellValue('A' . $chrRow, $requisitions[$i]->requisition_code);
                        $sheet->setCellValue('B' . $chrRow, $requisitions[$i]->title);
                        $sheet->setCellValue('C' . $chrRow, $requisitions[$i]->req_name);
                        $sheet->setCellValue('D' . $chrRow, date("d-m-Y", strtotime($requisitions[$i]->createdat)));
                        $sheet->setCellValue('E' . $chrRow, $requisitions[$i]->actionstatus);

                        $sheet->cells('A' . $chrRow . ':E' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } 
    }
}