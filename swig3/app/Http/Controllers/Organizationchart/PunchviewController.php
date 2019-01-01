<?php

namespace App\Http\Controllers\Organizationchart;

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
use App;
use DB;
use Mail;
use Excel;

class PunchviewController extends Controller {

    public function getemployesrating(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $currentMonth = date('m');
        $currentYear = date('Y');

        $ratingdata = DB::table('punch_performance')
                ->select('rating', db::raw("count(DISTINCT employe_id) as ratingCount"))
                ->whereRaw("EXTRACT(MONTH FROM created_at)=$currentMonth AND EXTRACT(YEAR FROM created_at)=$currentYear AND status=1")
                ->groupBy(['rating'])
                ->get();

        $totalempcount = DB::table('employees')->whereRaw("status!=2")->count();
        $ratedempcount = DB::table('punch_performance')->whereRaw("EXTRACT(MONTH FROM created_at)=$currentMonth AND EXTRACT(YEAR FROM created_at)=$currentYear AND status=1")->count(DB::raw('DISTINCT employe_id'));

        $graph_pi = array();
        $full_row = array();
        $full_pi_graph_data = array();

        foreach ($ratingdata as $rating) {
            $strRating = '';
            $color = '';
            
            if ($rating->rating == 1) {
                $strRating = 'Exceptional (90 - 100%)';
                $color = '#029346';
            } else if ($rating->rating == 2) {
                $strRating = 'Effective (70 - 90%)';
                $color = '#42fb1d';
            } else if ($rating->rating == 3) {
                $strRating = 'Inconsistent (50 - 70%)';
                $color = '#f5f516';
            } else if ($rating->rating == 4) {
                $strRating = 'Unsatisfactory (40 - 50%)';
                $color = '#f87c31';
            } else if ($rating->rating == 5) {
                $strRating = 'Not Acceptable (Below 40%)';
                $color = '#d60f06';
            }

            $row = array("name" => "$strRating", "y" => $rating->ratingCount, "id" => $rating->rating, 'color' => $color);
            array_push($graph_pi, $row);
        }

        $row = array("name" => "Not Rated", "y" => ($totalempcount - $ratedempcount), "id" => 6, 'color' => '#999966');
        array_push($graph_pi, $row);

        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $empcode = Input::get('empcode');
            $rating = Input::get('rating');

            if ($rating == 6) {

                $ratedemploye = DB::table('punch_performance as punch')
                                ->select('punch.employe_id')
                                ->whereRaw("punch.status=1")
                                ->groupby('employe_id')
                                ->get()->pluck('employe_id')->toArray();

                $employedata = DB::table('employees')
                        ->select('employees.id', 'employees.username', 'employees.first_name', db::raw(" '' as ratedbyname"), db::raw(" '' as reason"), db::raw(" null as created_at"))
                        ->whereRaw("employees.status!=2")
                        ->whereNotIn('id', $ratedemploye)
                        ->orderby('employees.first_name', 'ASC')
                        ->when($empcode, function ($query) use ($empcode) {
                            return $query->whereRaw("(employees.username like '%$empcode%')");
                        })
                        ->paginate($paginate);
            } else {
                
                if(!$rating){
                    $rating=-1;
                }
                $employedata = DB::table('punch_performance as punch')
                        ->select('punch.id', 'employees.username', 'employees.first_name', 'r.first_name as ratedbyname', 'reason', 'punch.created_at')
                        ->leftjoin('employees', 'punch.employe_id', '=', 'employees.id')
                        ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
                        ->whereRaw("punch.status=1 AND rating=$rating")
                        ->whereRaw("EXTRACT(MONTH FROM punch.created_at)=$currentMonth AND EXTRACT(YEAR FROM punch.created_at)=$currentYear")
                        ->orderby('punch.created_at', 'DESC')
                        ->when($empcode, function ($query) use ($empcode) {
                            return $query->whereRaw("(employees.username like '%$empcode%')");
                        })
                        ->paginate($paginate);
            }

            return view('organizationchart/punch_view/result', array('employedata' => $employedata));
        }

        return view('organizationchart/punch_view/index', array('employedata' => array(),
            "currentMonth" => $currentMonth,
            "currentYear" => $currentYear,
            "full_pi_graph_data" => $full_pi_graph_data,
        ));
    }

    public function getmonthwiseperformance(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $currentMonth = Input::get('cmbmonth');
        $currentYear = Input::get('cmbyear');

        $ratingdata = DB::table('punch_performance')
                ->select('rating', db::raw("count(DISTINCT employe_id) as ratingCount"))
                ->whereRaw("EXTRACT(MONTH FROM created_at)=$currentMonth AND EXTRACT(YEAR FROM created_at)=$currentYear AND status=1")
                ->groupBy(['rating'])
                ->get();

        $totalempcount = DB::table('employees')->whereRaw("status!=2")->count();
        $ratedempcount = DB::table('punch_performance')->whereRaw("EXTRACT(MONTH FROM created_at)=$currentMonth AND EXTRACT(YEAR FROM created_at)=$currentYear AND status=1")->count(DB::raw('DISTINCT employe_id'));

        $graph_pi = array();
        $full_row = array();
        $full_pi_graph_data = array();

        foreach ($ratingdata as $rating) {
            $strRating = '';
            $color = '';
            if ($rating->rating == 1) {
                $strRating = 'Exceptional (90 - 100%)';
                $color = '#029346';
            } else if ($rating->rating == 2) {
                $strRating = 'Effective (70 - 90%)';
                $color = '#42fb1d';
            } else if ($rating->rating == 3) {
                $strRating = 'Inconsistent (50 - 70%)';
                $color = '#f5f516';
            } else if ($rating->rating == 4) {
                $strRating = 'Unsatisfactory (40 - 50%)';
                $color = '#f87c31';
            } else if ($rating->rating == 5) {
                $strRating = 'Not Acceptable (Below 40%)';
                $color = '#d60f06';
            }

            $row = array("name" => "$strRating", "y" => $rating->ratingCount, "id" => $rating->rating, 'color' => $color);
            array_push($graph_pi, $row);
        }

        $row = array("name" => "Not Rated", "y" => ($totalempcount - $ratedempcount), "id" => 6, 'color' => '#999966');
        array_push($graph_pi, $row);

        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $empcode = Input::get('empcode');
            $rating = Input::get('rating');

            if ($rating == 6) {

                $ratedemploye = DB::table('punch_performance as punch')
                                ->select('punch.employe_id')
                                ->whereRaw("punch.status=1")
                                ->groupby('employe_id')
                                ->get()->pluck('employe_id')->toArray();

                $employedata = DB::table('employees')
                        ->select('employees.id', 'employees.username', 'employees.first_name', db::raw(" '' as ratedbyname"), db::raw(" '' as reason"), db::raw(" null as created_at"))
                        ->whereRaw("employees.status!=2")
                        ->whereNotIn('id', $ratedemploye)
                        ->orderby('employees.first_name', 'ASC')
                        ->when($empcode, function ($query) use ($empcode) {
                            return $query->whereRaw("(employees.username like '%$empcode%')");
                        })
                        ->paginate($paginate);
            } else {
                if(!$rating){
                    $rating=-1;
                }
                $employedata = DB::table('punch_performance as punch')
                        ->select('punch.id', 'employees.username', 'employees.first_name', 'r.first_name as ratedbyname', 'reason', 'punch.created_at')
                        ->leftjoin('employees', 'punch.employe_id', '=', 'employees.id')
                        ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
                        ->whereRaw("punch.status=1 AND rating=$rating")
                        ->whereRaw("EXTRACT(MONTH FROM punch.created_at)=$currentMonth AND EXTRACT(YEAR FROM punch.created_at)=$currentYear")
                        ->orderby('punch.created_at', 'DESC')
                        ->when($empcode, function ($query) use ($empcode) {
                            return $query->whereRaw("(employees.username like '%$empcode%')");
                        })
                        ->paginate($paginate);
            }

            return view('organizationchart/punch_view/result', array('employedata' => $employedata));
        }

        return view('organizationchart/punch_view/index', array('employedata' => array(),
            "currentMonth" => $currentMonth,
            "currentYear" => $currentYear,
            "full_pi_graph_data" => $full_pi_graph_data,
        ));
    }

    // Generate PDF funcion
    public function exportdata() {

        $excelorpdf = Input::get('excelorpdf');
        $empcode = Input::get('empcode');
        $rating = Input::get('rating');
        $ratingname = Input::get('ratingname').' Employees';
        $currentMonth = Input::get('month');
        $currentYear = Input::get('year');
        if ($rating == 6) {

            $ratedemploye = DB::table('punch_performance as punch')
                            ->select('punch.employe_id')
                            ->whereRaw("punch.status=1")
                            ->groupby('employe_id')
                            ->get()->pluck('employe_id')->toArray();

            $employedata = DB::table('employees')
                    ->select('employees.id', 'employees.username', 'employees.first_name', db::raw(" '' as ratedbyname"), db::raw(" '' as reason"), db::raw(" null as created_at"))
                    ->whereRaw("employees.status!=2")
                    ->whereNotIn('id', $ratedemploye)
                    ->orderby('employees.first_name', 'ASC')
                    ->when($empcode, function ($query) use ($empcode) {
                        return $query->whereRaw("(employees.username like '%$empcode%')");
                    })
                    ->get();
        } else {
            $employedata = DB::table('punch_performance as punch')
                    ->select('punch.id', 'employees.username', 'employees.first_name', 'r.first_name as ratedbyname', 'reason', 'punch.created_at')
                    ->leftjoin('employees', 'punch.employe_id', '=', 'employees.id')
                    ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
                    ->whereRaw("punch.status=1 AND rating=$rating")
                    ->whereRaw("EXTRACT(MONTH FROM punch.created_at)=$currentMonth AND EXTRACT(YEAR FROM punch.created_at)=$currentYear")
                    ->orderby('punch.created_at', 'DESC')
                    ->when($empcode, function ($query) use ($empcode) {
                        return $query->whereRaw("(employees.username like '%$empcode%')");
                    })
                    ->get();
        }
        
       
        if ($excelorpdf == "Excel") {

            Excel::create('employelist', function($excel) use($employedata,$ratingname) {
                // Set the title
                $excel->setTitle('Employe Rating');

                $excel->sheet('Employe Rating', function($sheet) use($employedata,$ratingname) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', $ratingname);
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Employee Name', "Date", 'Valued By', 'Reason'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($employedata); $i++) {
                        $date='';
                        if ($employedata[$i]->created_at) {
                            $date = date('d-m-Y', strtotime($employedata[$i]->created_at));
                        }
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $employedata[$i]->username.' : '.$employedata[$i]->first_name);
                        $sheet->setCellValue('C' . $chrRow, $date);
                        $sheet->setCellValue('D' . $chrRow, $employedata[$i]->ratedbyname);
                        $sheet->setCellValue('E' . $chrRow, $employedata[$i]->reason);

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
