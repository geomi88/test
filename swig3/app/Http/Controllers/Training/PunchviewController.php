<?php

namespace App\Http\Controllers\Training;

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

        $ratingdata = DB::table('training_performance')
                ->select('rating', db::raw("count(DISTINCT trainee_id) as ratingCount"))
                ->whereRaw("EXTRACT(MONTH FROM created_at)=$currentMonth AND EXTRACT(YEAR FROM created_at)=$currentYear AND status=1 AND traine_type=1")
                ->groupBy(['rating'])
                ->get();

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

        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $empcode = Input::get('empcode');
            $rating = Input::get('rating');

           
                $employedata = DB::table('training_performance as punch')
                        ->select('punch.id', 'employees.username', 'employees.first_name', 'r.first_name as ratedbyname', 'reason', 'punch.created_at')
                        ->leftjoin('employees', 'punch.trainee_id', '=', 'employees.id')
                        ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
                        ->whereRaw("punch.status=1 AND rating=$rating AND traine_type=1")
                        ->whereRaw("EXTRACT(MONTH FROM punch.created_at)=$currentMonth AND EXTRACT(YEAR FROM punch.created_at)=$currentYear")
                        ->orderby('punch.created_at', 'DESC')
                        ->when($empcode, function ($query) use ($empcode) {
                            return $query->whereRaw("(employees.username like '%$empcode%')");
                        })
                        ->paginate($paginate);
           

            return view('training/punch_view/result', array('employedata' => $employedata));
        }

        return view('training/punch_view/index', array('employedata' => array(),
            "currentMonth" => $currentMonth,
            "currentYear" => $currentYear,
            "full_pi_graph_data" => $full_pi_graph_data,
        ));
    }

    public function getmonthwiseperformance(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $currentMonth = Input::get('cmbmonth');
        $currentYear = Input::get('cmbyear');

       $ratingdata = DB::table('training_performance')
                ->select('rating', db::raw("count(DISTINCT trainee_id) as ratingCount"))
                ->whereRaw("EXTRACT(MONTH FROM created_at)=$currentMonth AND EXTRACT(YEAR FROM created_at)=$currentYear AND status=1 AND traine_type=1")
                ->groupBy(['rating'])
                ->get();

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


        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $empcode = Input::get('empcode');
            $rating = Input::get('rating');


                $employedata = DB::table('training_performance as punch')
                        ->select('punch.id', 'employees.username', 'employees.first_name', 'r.first_name as ratedbyname', 'reason', 'punch.created_at')
                        ->leftjoin('employees', 'punch.trainee_id', '=', 'employees.id')
                        ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
                        ->whereRaw("punch.status=1 AND rating=$rating AND traine_type=1")
                        ->whereRaw("EXTRACT(MONTH FROM punch.created_at)=$currentMonth AND EXTRACT(YEAR FROM punch.created_at)=$currentYear")
                        ->orderby('punch.created_at', 'DESC')
                        ->when($empcode, function ($query) use ($empcode) {
                            return $query->whereRaw("(employees.username like '%$empcode%')");
                        })
                        ->paginate($paginate);

            return view('training/punch_view/result', array('employedata' => $employedata));
        }

        return view('training/punch_view/index', array('employedata' => array(),
            "currentMonth" => $currentMonth,
            "currentYear" => $currentYear,
            "full_pi_graph_data" => $full_pi_graph_data,
        ));
    }

    public function getnewemployesrating(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $currentMonth = date('m');
        $currentYear = date('Y');

        $ratingdata = DB::table('training_performance')
                ->select('rating', db::raw("count(DISTINCT guest_phone) as ratingCount"))
                ->whereRaw("EXTRACT(MONTH FROM training_performance.created_at)=$currentMonth AND EXTRACT(YEAR FROM training_performance.created_at)=$currentYear AND training_performance.status=1 AND traine_type=2")
                ->groupBy(['rating'])
                ->get();

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

        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $empcode = Input::get('empcode');
            $rating = Input::get('rating');

           
                $employedata = DB::table('training_performance as punch')
                        ->select('punch.id', 'punch.guest_name', 'punch.guest_phone', 'r.first_name as ratedbyname', 'reason', 'punch.created_at')
                        ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
                        ->whereRaw("punch.status=1 AND rating=$rating AND traine_type=2")
                        ->whereRaw("EXTRACT(MONTH FROM punch.created_at)=$currentMonth AND EXTRACT(YEAR FROM punch.created_at)=$currentYear")
                        ->orderby('punch.created_at', 'DESC')
                        ->when($empcode, function ($query) use ($empcode) {
                            return $query->whereRaw("(punch.guest_phone like '%$empcode%')");
                        })
                        ->paginate($paginate);
           

            return view('training/punch_view_new/result', array('employedata' => $employedata));
        }

        return view('training/punch_view_new/index', array('employedata' => array(),
            "currentMonth" => $currentMonth,
            "currentYear" => $currentYear,
            "full_pi_graph_data" => $full_pi_graph_data,
        ));
    }

    public function getnewmonthwiseperformance(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $currentMonth = Input::get('cmbmonth');
        $currentYear = Input::get('cmbyear');

        $ratingdata = DB::table('training_performance')
                ->select('rating', db::raw("count(DISTINCT guest_phone) as ratingCount"))
                ->whereRaw("EXTRACT(MONTH FROM training_performance.created_at)=$currentMonth AND EXTRACT(YEAR FROM training_performance.created_at)=$currentYear AND training_performance.status=1 AND traine_type=2")
                ->groupBy(['rating'])
                ->get();

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


        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $empcode = Input::get('empcode');
            $rating = Input::get('rating');


                $employedata = DB::table('training_performance as punch')
                        ->select('punch.id', 'punch.guest_name', 'punch.guest_phone', 'r.first_name as ratedbyname', 'reason', 'punch.created_at')
                        ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
                        ->whereRaw("punch.status=1 AND rating=$rating AND traine_type=2")
                        ->whereRaw("EXTRACT(MONTH FROM punch.created_at)=$currentMonth AND EXTRACT(YEAR FROM punch.created_at)=$currentYear")
                        ->orderby('punch.created_at', 'DESC')
                        ->when($empcode, function ($query) use ($empcode) {
                            return $query->whereRaw("(punch.guest_phone like '%$empcode%')");
                        })
                        ->paginate($paginate);

            return view('training/punch_view_new/result', array('employedata' => $employedata));
        }

        return view('training/punch_view_new/index', array('employedata' => array(),
            "currentMonth" => $currentMonth,
            "currentYear" => $currentYear,
            "full_pi_graph_data" => $full_pi_graph_data,
        ));
    }
}
