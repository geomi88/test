<?php

namespace App\Http\Controllers\Reception;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Visitors_log;
use DB;
use App;
use Excel;

class VisitorlistController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $visitors = DB::table('visitors_log as log')
                ->select('log.*', 'employees.username', 'employees.first_name')
                ->leftjoin('employees', 'log.to_meet', '=', 'employees.id')
                ->where('log.status', '=', 1)
                ->orderby('log.date_time', 'DESC')
                ->paginate($paginate);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $searchbyname = Input::get('searchbyname');
            $empcode = Input::get('empcode');
            $datefrom = Input::get('datefrom');
            $dateto = Input::get('dateto');

            if ($datefrom != '') {
                $datefrom = explode('-', $datefrom);
                $datefrom = $datefrom[2] . '-' . $datefrom[1] . '-' . $datefrom[0];
            }

            if ($dateto != '') {
                $dateto = explode('-', $dateto);
                $dateto = $dateto[2] . '-' . $dateto[1] . '-' . $dateto[0];
            }

            $visitors = DB::table('visitors_log as log')
                    ->select('log.*', 'employees.username', 'employees.first_name')
                    ->leftjoin('employees', 'log.to_meet', '=', 'employees.id')
                    ->where('log.status', '=', 1)
                    ->orderby('log.date_time', 'DESC')
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(log.name like '%$searchbyname%')");
                    })
                    ->when($empcode, function ($query) use ($empcode) {
                        return $query->whereRaw("(employees.username like '%$empcode%')");
                    })
                    ->when($datefrom, function ($query) use ($datefrom) {
                        return $query->whereRaw("date(log.date_time) >= '$datefrom' ");
                    })
                    ->when($dateto, function ($query) use ($dateto) {
                        return $query->whereRaw("date(log.date_time) <= '$dateto' ");
                    })
                    ->paginate($paginate);

            return view('reception/visitors_list/result', array('visitors' => $visitors));
        }
        return view('reception/visitors_list/index', array('visitors' => $visitors));
    }

    
    // Generate PDF funcion
    public function exportdata() {

        $excelorpdf = Input::get('excelorpdf');

        $searchbyname = Input::get('searchbyname');
        $empcode = Input::get('empcode');
        $datefrom = Input::get('datefrom');
        $dateto = Input::get('dateto');

        if ($datefrom != '') {
            $datefrom = explode('-', $datefrom);
            $datefrom = $datefrom[2] . '-' . $datefrom[1] . '-' . $datefrom[0];
        }

        if ($dateto != '') {
            $dateto = explode('-', $dateto);
            $dateto = $dateto[2] . '-' . $dateto[1] . '-' . $dateto[0];
        }

        $visitors = DB::table('visitors_log as log')
                ->select('log.*', 'employees.username', 'employees.first_name')
                ->leftjoin('employees', 'log.to_meet', '=', 'employees.id')
                ->where('log.status', '=', 1)
                ->orderby('log.date_time', 'DESC')
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(log.name like '%$searchbyname%')");
                })
                ->when($empcode, function ($query) use ($empcode) {
                    return $query->whereRaw("(employees.username like '%$empcode%')");
                })
                ->when($datefrom, function ($query) use ($datefrom) {
                    return $query->whereRaw("date(log.date_time) >= '$datefrom' ");
                })
                ->when($dateto, function ($query) use ($dateto) {
                    return $query->whereRaw("date(log.date_time) <= '$dateto' ");
                })
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('VisitorsList', function($excel) use($visitors) {
                // Set the title
                $excel->setTitle('Visitors List');

                $excel->sheet('Visitors List', function($sheet) use($visitors) {
                    // Sheet manipulation

                    $sheet->setCellValue('D3', 'Visitors List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:G3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Visitor Name', "Date & time", 'To Meet', 'Purpose','Mobile','Email'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:G5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($visitors); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $visitors[$i]->name);
                        $sheet->setCellValue('C' . $chrRow, date("d-m-Y H:i", strtotime($visitors[$i]->date_time)));
                        $sheet->setCellValue('D' . $chrRow, $visitors[$i]->username . ' : ' . $visitors[$i]->first_name);
                        $sheet->setCellValue('E' . $chrRow, $visitors[$i]->purpose);
                        $sheet->setCellValue('F' . $chrRow, $visitors[$i]->mobile);
                        $sheet->setCellValue('G' . $chrRow, $visitors[$i]->email);

                        $sheet->cells('A' . $chrRow . ':G' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        }
    }

}
