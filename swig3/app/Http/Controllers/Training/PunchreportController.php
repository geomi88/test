<?php

namespace App\Http\Controllers\Training;

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

        
            $punchdata = DB::table('training_performance as punch')
                ->select('employees.id', 'employees.username', 'employees.first_name',
                        db::raw("(select count(rating) from training_performance where rating=1 AND trainee_id=employees.id AND status=1) as exceptionalcount"),
                        db::raw("(select count(rating) from training_performance where rating=2 AND trainee_id=employees.id AND status=1) as effectivecount"),
                        db::raw("(select count(rating) from training_performance where rating=3 AND trainee_id=employees.id AND status=1) as inconsistentcount"),
                        db::raw("(select count(rating) from training_performance where rating=4 AND trainee_id=employees.id AND status=1) as unsatisfactorycount"),
                        db::raw("(select count(rating) from training_performance where rating=5 AND trainee_id=employees.id AND status=1) as notacceptablecount"),
                        db::raw("(select count(rating) from training_performance where trainee_id=employees.id AND status=1) as totalcount"))
                ->leftjoin('employees', 'punch.trainee_id', '=', 'employees.id')
                ->whereRaw("punch.status=1 AND punch.traine_type=1")
                ->groupby('punch.trainee_id')
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
              
            $sortOrdDefault = '';
            if ($sortordexceptional == '' && $sortordeffective=='' && $sortordinconsis=='' && $sortordunsatisfact=='' && $sortordnotaccept=='') {
                $sortOrdDefault = 'DESC';
            }
                
            $punchdata = DB::table('training_performance as punch')
                ->select('employees.id', 'employees.username', 'employees.first_name',
                        db::raw("(select count(rating) from training_performance where rating=1 AND trainee_id=employees.id AND status=1) as exceptionalcount"),
                        db::raw("(select count(rating) from training_performance where rating=2 AND trainee_id=employees.id AND status=1) as effectivecount"),
                        db::raw("(select count(rating) from training_performance where rating=3 AND trainee_id=employees.id AND status=1) as inconsistentcount"),
                        db::raw("(select count(rating) from training_performance where rating=4 AND trainee_id=employees.id AND status=1) as unsatisfactorycount"),
                        db::raw("(select count(rating) from training_performance where rating=5 AND trainee_id=employees.id AND status=1) as notacceptablecount"),
                        db::raw("(select count(rating) from training_performance where trainee_id=employees.id AND status=1) as totalcount"))
                ->leftjoin('employees', 'punch.trainee_id', '=', 'employees.id')
                ->whereRaw("punch.status=1 AND punch.traine_type=1")
                ->groupby('punch.trainee_id')
                ->when($empcode, function ($query) use ($empcode) {
                    return $query->whereRaw("(employees.username like '%$empcode%')");
                })
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
                            
            return view('training/punch_report/result', array('punchdata' => $punchdata));
        }
        
        return view('training/punch_report/index', array('punchdata' => $punchdata));
    }
    
    
    public function newindex(Request $request) {
        $paginate = Config::get('app.PAGINATE');

            $punchdata = DB::table('training_performance as punch')
                ->select('punch.guest_phone', 'punch.guest_name',
                        db::raw("(select count(rating) from training_performance where rating=1 AND guest_phone=t2.guest_phone AND status=1) as exceptionalcount"),
                        db::raw("(select count(rating) from training_performance where rating=2 AND guest_phone=t2.guest_phone AND status=1) as effectivecount"),
                        db::raw("(select count(rating) from training_performance where rating=3 AND guest_phone=t2.guest_phone AND status=1) as inconsistentcount"),
                        db::raw("(select count(rating) from training_performance where rating=4 AND guest_phone=t2.guest_phone AND status=1) as unsatisfactorycount"),
                        db::raw("(select count(rating) from training_performance where rating=5 AND guest_phone=t2.guest_phone AND status=1) as notacceptablecount"),
                        db::raw("(select count(rating) from training_performance where guest_phone=t2.guest_phone AND status=1) as totalcount"))
                ->leftjoin('training_performance as t2', 'punch.guest_phone', '=', 't2.guest_phone')
                ->whereRaw("punch.status=1 AND punch.traine_type=2")
                ->groupby('punch.guest_phone')
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
              
            $sortOrdDefault = '';
            if ($sortordexceptional == '' && $sortordeffective=='' && $sortordinconsis=='' && $sortordunsatisfact=='' && $sortordnotaccept=='') {
                $sortOrdDefault = 'DESC';
            }
                
            $punchdata = DB::table('training_performance as punch')
                ->select('punch.guest_phone', 'punch.guest_name',
                        db::raw("(select count(rating) from training_performance where rating=1 AND guest_phone=t2.guest_phone AND status=1) as exceptionalcount"),
                        db::raw("(select count(rating) from training_performance where rating=2 AND guest_phone=t2.guest_phone AND status=1) as effectivecount"),
                        db::raw("(select count(rating) from training_performance where rating=3 AND guest_phone=t2.guest_phone AND status=1) as inconsistentcount"),
                        db::raw("(select count(rating) from training_performance where rating=4 AND guest_phone=t2.guest_phone AND status=1) as unsatisfactorycount"),
                        db::raw("(select count(rating) from training_performance where rating=5 AND guest_phone=t2.guest_phone AND status=1) as notacceptablecount"),
                        db::raw("(select count(rating) from training_performance where guest_phone=t2.guest_phone AND status=1) as totalcount"))
                ->leftjoin('training_performance as t2', 'punch.guest_phone', '=', 't2.guest_phone')
                ->whereRaw("punch.status=1 AND punch.traine_type=2")
                ->groupby('punch.guest_phone')
                ->when($empcode, function ($query) use ($empcode) {
                    return $query->whereRaw("(punch.guest_phone like '%$empcode%')");
                })
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
                            
            return view('training/punch_report/newresult', array('punchdata' => $punchdata));
        }
        
        return view('training/punch_report/newindex', array('punchdata' => $punchdata));
    }
    

}
