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
use Customhelper;
use DB;
use PDF;
use Excel;

class LeavereportController extends Controller {

    public function index(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $requisitions = DB::table('requisition')
                    ->select('requisition.*', 'employees.first_name', db::raw("(case when requisition.status=4 then 'Approved' when requisition.status=5 then 'Rejected' else 'Pending' end) as reqstatus,"
                                    . "CASE "
                                    . "WHEN requisition.leave_length=0 THEN 'Full day' "
                                    . "WHEN requisition.leave_length=1 THEN 'Half Day' "
                                    . "WHEN requisition.leave_length=2 THEN 'Annual Vacation' "
                                    . "WHEN requisition.leave_length=3 THEN 'Sick Leave' "
                                    . "WHEN requisition.leave_length=4 THEN 'Maternity Leave' "
                                    . "WHEN requisition.leave_length=5 THEN 'Emergency Leave' "
                                    . "WHEN requisition.leave_length=6 THEN 'Business Leave' "
                                    . "END as leave_type_status,"
                                    . "(select m.name FROM master_resources  m where m.id = employees.job_position) as sys_position"))
                    ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')
                    ->leftjoin('employees', 'requisition.created_by', '=', 'employees.id')
                    ->whereRaw("requisition_types.name='Leave Requisition'")
                    ->orderby('requisition.created_at', 'DESC')
                    ->paginate($paginate);

            if ($request->ajax()) {

                $searchbycode = Input::get('searchbycode');
                $searchbytitle = Input::get('searchbytitle');
                $status = Input::get('status');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $sortordtitle = Input::get('sortordtitle');
                $sortordcode = Input::get('sortordcode');
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
                if ($sortordtitle == '' && $sortordcode == '' && $sortorddate == '') {
                    $sortOrdDefault = 'DESC';
                }

                $requisitions = DB::table('requisition')
                        ->select('requisition.*', 'employees.first_name', db::raw("(case when requisition.status=4 then 'Approved' when requisition.status=5 then 'Rejected' else 'Pending' end) as reqstatus,"
                                        . "CASE "
                                        . "WHEN requisition.leave_length=0 THEN 'Full day' "
                                        . "WHEN requisition.leave_length=1 THEN 'Half Day' "
                                        . "WHEN requisition.leave_length=2 THEN 'Annual Vacation' "
                                        . "WHEN requisition.leave_length=3 THEN 'Sick Leave' "
                                        . "WHEN requisition.leave_length=4 THEN 'Maternity Leave' "
                                        . "WHEN requisition.leave_length=5 THEN 'Emergency Leave' "
                                        . "WHEN requisition.leave_length=6 THEN 'Business Leave' "
                                        . "END as leave_type_status,"
                                        . "(select m.name FROM master_resources  m where m.id = employees.job_position) as sys_position"))
                        ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')
                        ->leftjoin('employees', 'requisition.created_by', '=', 'employees.id')
                        ->whereRaw("requisition_types.name='Leave Requisition'")
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
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('requisition.id', $sortordcode);
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('requisition.title', $sortordtitle);
                        })
                        ->when($sortorddate, function ($query) use ($sortorddate) {
                            return $query->orderby('requisition.created_at', $sortorddate);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('requisition.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);

                return view('requisitions/leave_report/results', array('requisitions' => $requisitions));
            }

            return view('requisitions/leave_report/index', array('requisitions' => $requisitions));
        } catch (\Exception $e) {

            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions');
        }
    }

    public function exportdata() {

        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbytitle = Input::get('searchbytitle');
        $status = Input::get('status');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $sortordtitle = Input::get('sortordtitle');
        $sortordcode = Input::get('sortordcode');
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
        if ($sortordtitle == '' && $sortordcode == '' && $sortorddate == '') {
            $sortOrdDefault = 'DESC';
        }

        $requisitions = DB::table('requisition')
                ->select('requisition.*', 'employees.first_name', db::raw("(case when requisition.status=4 then 'Approved' when requisition.status=5 then 'Rejected' else 'Pending' end) as reqstatus,"
                                        . "CASE "
                                        . "WHEN requisition.leave_length=0 THEN 'Full day' "
                                        . "WHEN requisition.leave_length=1 THEN 'Half Day' "
                                        . "WHEN requisition.leave_length=2 THEN 'Annual Vacation' "
                                        . "WHEN requisition.leave_length=3 THEN 'Sick Leave' "
                                        . "WHEN requisition.leave_length=4 THEN 'Maternity Leave' "
                                        . "WHEN requisition.leave_length=5 THEN 'Emergency Leave' "
                                        . "WHEN requisition.leave_length=6 THEN 'Business Leave' "
                                        . "END as leave_type_status,"
                                        . "(select m.name FROM master_resources  m where m.id = employees.job_position) as sys_position"))
                ->leftjoin('requisition_types', 'requisition_types.id', '=', 'requisition.requisition_type')
                ->leftjoin('employees', 'requisition.created_by', '=', 'employees.id')
                ->whereRaw("requisition_types.name='Leave Requisition'")
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
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('requisition.id', $sortordcode);
                })
                ->when($sortordtitle, function ($query) use ($sortordtitle) {
                    return $query->orderby('requisition.title', $sortordtitle);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('requisition.created_at', $sortorddate);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('requisition.created_at', $sortOrdDefault);
                })
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Leave_Requisition_Report', function($excel) use($requisitions) {
                // Set the title
                $excel->setTitle('Leave Requisition Report');

                $excel->sheet('Leave Requisition Report', function($sheet) use($requisitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Leave Requisition Report');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:I3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Requsition Code', 'Title', 'Date', 'Status','Leave Type','From Date','To Date', 'Created By','System Position', 'Leave Description'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:I5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requisitions); $i++) {
                        $sheet->setCellValue('A' . $chrRow, $requisitions[$i]->requisition_code);
                        $sheet->setCellValue('B' . $chrRow, $requisitions[$i]->title);
                        $sheet->setCellValue('C' . $chrRow, date("d-m-Y", strtotime($requisitions[$i]->created_at)));
                        $sheet->setCellValue('D' . $chrRow, $requisitions[$i]->reqstatus);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($requisitions[$i]->leave_from)));
                        $sheet->setCellValue('F' . $chrRow, date("d-m-Y", strtotime($requisitions[$i]->leave_to)));
                        $sheet->setCellValue('G' . $chrRow, $requisitions[$i]->first_name);
                        $sheet->setCellValue('H' . $chrRow, str_replace("_"," ",$requisitions[$i]->sys_position));
                        $sheet->setCellValue('I' . $chrRow, $requisitions[$i]->description);

                        $sheet->cells('A' . $chrRow . ':I' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        }
    }

}
