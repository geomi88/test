<?php

namespace App\Http\Controllers\Organizationchart;

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
use App\Models\Punch_performance;
use DB;
use App;
use Excel;

class PunchreportController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        
        $punchdata = DB::table('employees')
                ->select('employees.id', 'employees.username', 'employees.first_name',
                        db::raw("(select count(rating) from punch_performance where rating=1 AND employe_id=employees.id AND status=1) as exceptionalcount"),
                        db::raw("(select count(rating) from punch_performance where rating=2 AND employe_id=employees.id AND status=1) as effectivecount"),
                        db::raw("(select count(rating) from punch_performance where rating=3 AND employe_id=employees.id AND status=1) as inconsistentcount"),
                        db::raw("(select count(rating) from punch_performance where rating=4 AND employe_id=employees.id AND status=1) as unsatisfactorycount"),
                        db::raw("(select count(rating) from punch_performance where rating=5 AND employe_id=employees.id AND status=1) as notacceptablecount"),
                        db::raw("(select count(rating) from punch_performance where employe_id=employees.id AND status=1) as totalcount"))
                ->leftjoin('punch_performance as punch', 'punch.employe_id', '=', 'employees.id')
                ->whereRaw("employees.status!=2")
                ->groupby('employees.id')
                ->orderby('totalcount', 'Desc')
                ->paginate($paginate);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $empcode = Input::get('empcode');
            $sortordexceptional = Input::get('sortordexceptional');
            $sortordeffective = Input::get('sortordeffective');
            $sortordinconsis = Input::get('sortordinconsis');
            $sortordunsatisfact = Input::get('sortordunsatisfact');
            $sortordnotaccept = Input::get('sortordnotaccept');
            $from_date = Input::get('from_date');
            $to_date = Input::get('to_date');
            
            if($from_date == '' || $to_date == ''){
                $filter_by_date = '';
            }else{
                $from_date = date('Y-m-d',strtotime($from_date));
                $to_date = date('Y-m-d',strtotime($to_date));
                $filter_by_date = " AND (date(created_at) >='$from_date' AND date(created_at)<='$to_date')";
            }  
            
            $sortOrdDefault = '';
            if ($sortordexceptional == '' && $sortordeffective=='' && $sortordinconsis=='' && $sortordunsatisfact=='' && $sortordnotaccept=='') {
                $sortOrdDefault = 'DESC';
            }
                
            $punchdata = DB::table('employees')
                ->select('employees.id', 'employees.username', 'employees.first_name',
                        db::raw("(select count(rating) from punch_performance where rating=1 $filter_by_date AND employe_id=employees.id AND status=1) as exceptionalcount"),
                        db::raw("(select count(rating) from punch_performance where rating=2 $filter_by_date AND employe_id=employees.id AND status=1) as effectivecount"),
                        db::raw("(select count(rating) from punch_performance where rating=3 $filter_by_date AND employe_id=employees.id AND status=1) as inconsistentcount"),
                        db::raw("(select count(rating) from punch_performance where rating=4 $filter_by_date AND employe_id=employees.id AND status=1) as unsatisfactorycount"),
                        db::raw("(select count(rating) from punch_performance where rating=5 $filter_by_date AND employe_id=employees.id AND status=1) as notacceptablecount"),
                        db::raw("(select count(rating) from punch_performance where employe_id=employees.id AND status=1) as totalcount"))
                ->leftjoin('punch_performance as punch', 'punch.employe_id', '=', 'employees.id')
                ->whereRaw("employees.status!=2")
                ->when($empcode, function ($query) use ($empcode) {
                    return $query->whereRaw("(employees.username like '%$empcode%')");
                })
                ->groupby('employees.id')
                ->when($sortordexceptional, function ($query) use ($sortordexceptional) {
                    return $query->orderby('exceptionalcount', $sortordexceptional);
                })
                ->when($sortordeffective, function ($query) use ($sortordeffective) {
                    return $query->orderby('effectivecount', $sortordeffective);
                })
                ->when($sortordinconsis, function ($query) use ($sortordinconsis) {
                    return $query->orderby('inconsistentcount', $sortordinconsis);
                })
                ->when($sortordunsatisfact, function ($query) use ($sortordunsatisfact) {
                    return $query->orderby('unsatisfactorycount', $sortordunsatisfact);
                })
                ->when($sortordnotaccept, function ($query) use ($sortordnotaccept) {
                    return $query->orderby('notacceptablecount', $sortordnotaccept);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('totalcount', $sortOrdDefault);
                })
                ->paginate($paginate);
                            
            return view('organizationchart/punch_report/result', array('punchdata' => $punchdata));
        }
        
        return view('organizationchart/punch_report/index', array('punchdata' => $punchdata));
    }
    

}
