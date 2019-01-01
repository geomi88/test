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
use Exception;
use DB;
use PDF;
use Excel;
use App;

class MaintenancerequsitionReportController extends Controller {

    public function view(Request $request) {

        $paginate = Config::get('app.PAGINATE');
        $requsitions = DB::table('requisition')
                ->select('requisition.*')
                ->leftjoin('requisition_types', 'requisition.requisition_type', '=', 'requisition_types.id')
                ->where('requisition_types.name', '=', 'Maintainance Requisition')
                ->orderby('requisition.created_at', 'DESC')
                ->paginate($paginate);
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $searchbycode = Input::get('searchbycode');
            $searchbyname = Input::get('searchbyname');
            $createdatfrom = Input::get('created_at_from');
            $createdatto = Input::get('created_at_to');
            $sortordcode = Input::get('sortordcode');
            $sortordtitle = Input::get('sortordtitle');
            $sortorddate = Input::get('sortorddate');
            $status = Input::get('status');

            $sortOrdDefault = '';
            if ($sortordtitle == '' && $sortordcode == '') {
                $sortOrdDefault = 'DESC';
            }

            if ($createdatfrom != '') {
                $createdatfrom = explode('-', $createdatfrom);
                $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
            }
            if ($createdatto != '') {
                $createdatto = explode('-', $createdatto);
                $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
            }

            $requsitions = DB::table('requisition')
                    ->select('requisition.*')
                    ->leftjoin('requisition_types', 'requisition.requisition_type', '=', 'requisition_types.id')
                    ->where('requisition_types.name', '=', 'Maintainance Requisition')
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("requisition.requisition_code like '$searchbycode%' ");
                    })
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("requisition.title like '$searchbyname%' ");
                    })
                    ->when($createdatfrom, function ($query) use ($createdatfrom) {
                        return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                    })
                    ->when($createdatto, function ($query) use ($createdatto) {
                        return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                    })
                    ->when($status, function ($query) use ($status) {
                        return $query->whereRaw("requisition.status = '$status%' ");
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('requisition.requisition_code', $sortordcode);
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

            return view('requisitions/maintenance_report/maintenance_report_results', array('requsitions' => $requsitions));
        }
        return view('requisitions/maintenance_report/maintenance_report', array('requsitions' => $requsitions));
    }

    public function exportdata() {


        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbyname = Input::get('searchbyname');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $sortordcode = Input::get('sortordcode');
        $sortordtitle = Input::get('sortordtitle');
        $sortorddate = Input::get('sortorddate');
        $status = Input::get('status');

        $sortOrdDefault = '';
        if ($sortordtitle == '' && $sortordcode == '') {
            $sortOrdDefault = 'DESC';
        }

        if ($createdatfrom != '') {
            $createdatfrom = explode('-', $createdatfrom);
            $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
        }
        if ($createdatto != '') {
            $createdatto = explode('-', $createdatto);
            $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
        }

        $requsitions = DB::table('requisition')
                ->select('requisition.*')
                ->leftjoin('requisition_types', 'requisition.requisition_type', '=', 'requisition_types.id')
                ->where('requisition_types.name', '=', 'Maintainance Requisition')
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("requisition.requisition_code like '$searchbycode%' ");
                })
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("requisition.title like '$searchbyname%' ");
                })
                ->when($createdatfrom, function ($query) use ($createdatfrom) {
                    return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                })
                ->when($status, function ($query) use ($status) {
                    return $query->whereRaw("requisition.status = '$status%' ");
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('requisition.requisition_code', $sortordcode);
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


        if ($excelorpdf == "EXCEL") {

            Excel::create('Maintenance Requisition Report', function($excel) use($requsitions) {
                // Set the title
                $excel->setTitle('Maintenance Requisition Report');

                $excel->sheet('Maintenance Requisition Report', function($sheet) use($requsitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Maintenance Requisition Report');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Requisition Code', 'Title', 'Date', 'Description', 'Status'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requsitions); $i++) {
                        if ($requsitions[$i]->status == 1)
                            $status = 'Pending';
                        elseif ($requsitions[$i]->status == 4)
                            $status = 'Approved';
                        elseif ($requsitions[$i]->status == 5)
                            $status = 'Rejected';

                        $sheet->setCellValue('A' . $chrRow, $requsitions[$i]->requisition_code);
                        $sheet->setCellValue('B' . $chrRow, $requsitions[$i]->title);
                        $sheet->setCellValue('C' . $chrRow, date("d-m-Y", strtotime($requsitions[$i]->created_at)));
                        $sheet->setCellValue('D' . $chrRow, $requsitions[$i]->description);
                        $sheet->setCellValue('E' . $chrRow, $status);

                        $sheet->cells('A' . $chrRow . ':E' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
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
                <title>Maintenance Requisition Report</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h1>Maintenance Requisition Report</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                               <td style="padding:10px 10px;color:#fff;"> Requisition Code</td>
                               <td style="padding:10px 5px;color:#fff;"> Title</td>
                               <td style="padding:10px 5px;color:#fff;"> Date</td>
                               <td style="padding:10px 5px;color:#fff;"> Description</td>
                              <td style="padding:10px 5px;color:#fff;"> Status</td>
                            </tr>
                        </thead>
                        <tbody class="ledgerbody" id="ledgerbody" >';
            foreach ($requsitions as $requsition) {
                if ($requsition->status == 1)
                    $status = 'Pending';
                elseif ($requsition->status == 4)
                    $status = 'Approved';
                elseif ($requsition->status == 5)
                    $status = 'Rejected';
                $html_table .='<tr>
                                   <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $requsition->requisition_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $requsition->title . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($requsition->created_at)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 20px;">' . $requsition->description . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $status . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('maintenance_requsition_report.pdf');
        }
    }

}
