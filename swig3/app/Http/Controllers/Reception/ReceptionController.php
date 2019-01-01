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

class ReceptionController extends Controller {

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

            return view('reception/visitors_log/result', array('visitors' => $visitors));
        }
        return view('reception/visitors_log/index', array('visitors' => $visitors));
    }

    public function add() {
        try {

            $employees = DB::table('employees')
                    ->select('id', 'username', 'first_name')
                    ->where('employees.status', '!=', 2)
                    ->orderby('employees.first_name', 'ASC')
                    ->get();

            return view('reception/visitors_log/add', array('employees' => $employees));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('reception/visitors_log');
        }
    }

    public function store() {
        try {
            $datetime = explode(' ', Input::get('date_time'));

            $arrdate = explode('-', $datetime[0]);

            $visittime = $arrdate[2] . '-' . $arrdate[1] . '-' . $arrdate[0] . ' ' . $datetime[1];

            $objmodel = new Visitors_log();
            $objmodel->name = Input::get('name');
            $objmodel->company = Input::get('company');
            $objmodel->mobile = Input::get('mobile');
            $objmodel->email = Input::get('email');
            $objmodel->date_time = $visittime;
            $objmodel->to_meet = Input::get('to_meet');
            $objmodel->purpose = Input::get('purpose');
            $objmodel->status = 1;

            $objmodel->save();

            Toastr::success('Visitors Log Saved Successfully', $title = null, $options = []);
            return Redirect::to('reception/visitors_log');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('reception/visitors_log');
        }
    }

    public function update() {
        try {

            $editid = Input::get('editid');
            $datetime = explode(' ', Input::get('date_time'));

            $arrdate = explode('-', $datetime[0]);

            $visittime = $arrdate[2] . '-' . $arrdate[1] . '-' . $arrdate[0] . ' ' . $datetime[1];

            $objmodel = new Visitors_log();
            $objmodel->exists = true;
            $objmodel->id = $editid;
            $objmodel->name = Input::get('name');
            $objmodel->company = Input::get('company');
            $objmodel->mobile = Input::get('mobile');
            $objmodel->email = Input::get('email');
            $objmodel->date_time = $visittime;
            $objmodel->to_meet = Input::get('to_meet');
            $objmodel->purpose = Input::get('purpose');
            $objmodel->status = 1;

            $objmodel->save();

            Toastr::success('Visitors Log Updated Successfully', $title = null, $options = []);
            return Redirect::to('reception/visitors_log');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('reception/visitors_log');
        }
    }

    public function edit($id) {
        try {
            $visitorid = \Crypt::decrypt($id);
            $visitor = DB::table('visitors_log')
                    ->where(['id' => $visitorid])
                    ->first();

            $employees = DB::table('employees')
                    ->select('id', 'username', 'first_name')
                    ->where('employees.status', '!=', 2)
                    ->orderby('employees.first_name', 'ASC')
                    ->get();

            return view('reception/visitors_log/edit', array('visitor' => $visitor, 'employees' => $employees));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('reception/visitors_log');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('visitors_log')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);

            Toastr::success('Visitor Log Deleted Successfully!', $title = null, $options = []);
            return Redirect::to('reception/visitors_log');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('reception/visitors_log');
        }
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

                    $sheet->setCellValue('C3', 'Visitors List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Visitor Name', "Date & time", 'To Meet', 'Purpose'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($visitors); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $visitors[$i]->name);
                        $sheet->setCellValue('C' . $chrRow, date("d-m-Y H:i", strtotime($visitors[$i]->date_time)));
                        $sheet->setCellValue('D' . $chrRow, $visitors[$i]->username . ' : ' . $visitors[$i]->first_name);
                        $sheet->setCellValue('E' . $chrRow, $visitors[$i]->purpose);

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
