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
use App\Models\Training_performance;
use DB;
use App;
use Excel;

class TrainingperformanceController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        
        $login_id=0;
        if(Session::get('login_id')){
            $login_id = Session::get('login_id');
        }
        
       $punchdata = DB::table('training_performance as punch')
                ->select('punch.*', 'employees.username', 'employees.first_name','r.first_name as ratedbyname','punch.guest_name','punch.guest_phone',
                        db::raw("case when rating=1 then 'Exceptional (90 - 100%)' when rating=2 then 'Effective (70 - 90%)' when rating=3 then 'Inconsistent (50 - 70%)' when rating=4 then 'Unsatisfactory (40 - 50%)' when rating=5 then 'Not Acceptable (Below 40%)' End as ratingname"))
                ->leftjoin('employees', function($join) {
                        $join->on('punch.trainee_id', '=', 'employees.id')
                        ->where('punch.traine_type', '=', 1);
                })
                ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
                ->where('punch.status', '=', 1)
                ->whereRaw("punch.rated_by=$login_id")
                ->orderby('punch.created_at', 'DESC')
                ->paginate($paginate);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $empcode = Input::get('empcode');
            $rating = Input::get('rating');
            
            $punchdata = DB::table('training_performance as punch')
                    ->select('punch.*', 'employees.username', 'employees.first_name','r.first_name as ratedbyname','punch.guest_name','punch.guest_phone',
                            db::raw("case when rating=1 then 'Exceptional (90 - 100%)' when rating=2 then 'Effective (70 - 90%)' when rating=3 then 'Inconsistent (50 - 70%)' when rating=4 then 'Unsatisfactory (40 - 50%)' when rating=5 then 'Not Acceptable (Below 40%)' End as ratingname"))
                    ->leftjoin('employees', function($join) {
                            $join->on('punch.trainee_id', '=', 'employees.id')
                            ->where('punch.traine_type', '=', 1);
                    })
                    ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
                    ->where('punch.status', '=', 1)
                    ->whereRaw("punch.rated_by=$login_id")
                    ->orderby('punch.created_at', 'DESC')
                    ->when($rating, function ($query) use ($rating) {
                        return $query->whereRaw("(punch.rating=$rating)");
                    })
                    ->when($empcode, function ($query) use ($empcode) {
                        return $query->whereRaw("(employees.username like '%$empcode%' OR punch.guest_phone like '%$empcode%')");
                    })
                    ->paginate($paginate);

            return view('training/training_performance/result', array('punchdata' => $punchdata));
        }
        
        return view('training/training_performance/index', array('punchdata' => $punchdata));
    }
    
//    public function editindex(Request $request) {
//        $paginate = Config::get('app.PAGINATE');
//  
//        $punchdata = DB::table('training_performance as punch')
//                ->select('punch.*', 'employees.username', 'employees.first_name','r.first_name as ratedbyname',
//                        db::raw("case when rating=1 then 'Exceptional (90 - 100%)' when rating=2 then 'Effective (70 - 90%)' when rating=3 then 'Inconsistent (50 - 70%)' when rating=4 then 'Unsatisfactory (40 - 50%)' when rating=5 then 'Not Acceptable (Below 40%)' End as ratingname"))
//                ->leftjoin('employees', 'punch.trainee_id', '=', 'employees.id')
//                ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
//                ->where('punch.status', '=', 1)
//                ->orderby('punch.created_at', 'DESC')
//                ->paginate($paginate);
//
//        if ($request->ajax()) {
//            $paginate = Input::get('pagelimit');
//            if ($paginate == "") {
//                $paginate = Config::get('app.PAGINATE');
//            }
//            $empcode = Input::get('empcode');
//            $rating = Input::get('rating');
//            
//            $punchdata = DB::table('training_performance as punch')
//                    ->select('punch.*', 'employees.username', 'employees.first_name','r.first_name as ratedbyname',
//                            db::raw("case when rating=1 then 'Exceptional (90 - 100%)' when rating=2 then 'Effective (70 - 90%)' when rating=3 then 'Inconsistent (50 - 70%)' when rating=4 then 'Unsatisfactory (40 - 50%)' when rating=5 then 'Not Acceptable (Below 40%)' End as ratingname"))
//                    ->leftjoin('employees', 'punch.trainee_id', '=', 'employees.id')
//                    ->leftjoin('employees as r', 'punch.rated_by', '=', 'r.id')
//                    ->where('punch.status', '=', 1)
//                    ->orderby('punch.created_at', 'DESC')
//                    ->when($rating, function ($query) use ($rating) {
//                        return $query->whereRaw("(punch.rating=$rating)");
//                    })
//                    ->when($empcode, function ($query) use ($empcode) {
//                        return $query->whereRaw("(employees.username like '%$empcode%')");
//                    })
//                    ->paginate($paginate);
//
//            return view('organizationchart/punch_edit/result', array('punchdata' => $punchdata));
//        }
//        
//        return view('organizationchart/punch_edit/index', array('punchdata' => $punchdata));
//    }

    public function add() {
        try {

            $employees = DB::table('employees')
                    ->select('id', 'username', 'first_name')
                    ->where('employees.status', '!=', 2)
                    ->orderby('employees.first_name', 'ASC')
                    ->get();
            
            $guests = DB::table('training_attendees')
                    ->select(db::raw('max(id) as id'), 'guest_name', 'guest_phone')
                    ->orderby('guest_name', 'ASC')
                    ->whereRaw("guest_phone is NOT NULL")
                    ->groupby('guest_phone')
                    ->get();

            
            return view('training/training_performance/add', array('employees' => $employees,'guests'=>$guests));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('training/training_performance');
        }
    }

    public function store() {
        try {
            $login_id = Session::get('login_id');
            $objmodel = new Training_performance();
            $emptype=Input::get('empType');
            if($emptype==2) {
                $guestphone = Input::get('guest_id');
                
                $guest = DB::table('training_attendees')
                    ->select('guest_phone', 'guest_email', 'guest_name')
                    ->whereRaw("guest_phone='$guestphone'")
                    ->first();
                
                $objmodel->trainee_id = NULL;
                $objmodel->guest_phone = $guest->guest_phone;
                $objmodel->guest_email = $guest->guest_email;
                $objmodel->guest_name = $guest->guest_name;
                            
            } else {
                $objmodel->trainee_id = Input::get('employe_id');
            }
            
            $objmodel->traine_type = $emptype;
            $objmodel->rating = Input::get('rating');
            $objmodel->rated_by = $login_id;
            $objmodel->reason = Input::get('reason');
            $objmodel->status = 1;
            $objmodel->save();

            Toastr::success('Performance Saved Successfully', $title = null, $options = []);
            return Redirect::to('training/training_performance');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('training/training_performance');
        }
    }

//    public function update() {
//        try {
//
//            $editid = Input::get('editid');
//            $login_id = Session::get('login_id');
//            
//            $objmodel = new Training_performance();
//            $objmodel->exists = true;
//            $objmodel->id = $editid;
//            $objmodel->trainee_id = Input::get('employe_id');
//            $objmodel->rating = Input::get('rating');
//            $objmodel->rated_by = $login_id;
//            $objmodel->reason = Input::get('reason');
//
//            $objmodel->save();
//
//            Toastr::success('Performance Updated Successfully', $title = null, $options = []);
//            return Redirect::to('organizationchart/puncheditindex');
//        } catch (\Exception $e) {
//            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
//            return Redirect::to('organizationchart/puncheditindex');
//        }
//    }
//
//    public function edit($id) {
//        try {
//            $editid = \Crypt::decrypt($id);
//            $punchdata = DB::table('training_performance')
//                    ->where(['id' => $editid])
//                    ->first();
//
//            $employees = DB::table('employees')
//                    ->select('id', 'username', 'first_name')
//                    ->where('employees.status', '!=', 2)
//                    ->orderby('employees.first_name', 'ASC')
//                    ->get();
//            
//            $emp_det = DB::table('employees')
//                    ->select('employees.username','employees.first_name', 'profilepic','master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
//                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
//                    ->leftjoin('country', 'country.id', '=', 'employees.nationality')
//                    ->whereRaw("employees.id=$punchdata->employe_id")
//                    ->first();
//            
//            return view('organizationchart/punch_edit/edit', array('punchdata' => $punchdata, 'employees' => $employees,'emp_det'=>$emp_det));
//        } catch (\Exception $e) {
//            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
//            return Redirect::to('organizationchart/puncheditindex');
//        }
//    }
//
    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('training_performance')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);

            Toastr::success('Performance Deleted Successfully!', $title = null, $options = []);
            return Redirect::to('training/training_performance');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('training/training_performance');
        }
    }
    
    public function getempdata() {
       
          $employe_id = Input::get('employe_id');
          $emp_det = DB::table('employees')
                    ->select('employees.username','employees.first_name', 'profilepic','master_resources.name', 'country.name as country_name', 'country.flag_128 as flag_name')
                    ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                    ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                    ->whereRaw("employees.id=$employe_id")
                    ->first();
          
            $strhtml='';
            if($emp_det){
                $strhtml='<figure class="imgHolder">
                          <img src="'.$emp_det->profilepic.'" alt="">
                      </figure>
                      <div class="details">
                          <b>'.$emp_det->username." : ".$emp_det->first_name.'</b>
                          <p>Designation : <span>'.str_replace(" ", "_", $emp_det->name).'</span></p>
                          <figure class="flagHolder">
                              <img src="'.url('images/flags').'/'.$emp_det->flag_name.'" alt="Flag">
                              <figcaption>'.$emp_det->country_name.'</figcaption>
                          </figure>
                      </div>';
          }
          
          
          echo $strhtml;
    }
    

}
