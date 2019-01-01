<?php

namespace App\Http\Controllers\Tasks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Kamaln7\Toastr\Facades\Toastr;
use DB;
use App;
use Excel;

class EmployeekpiController extends Controller {

    public function index(Request $request) {
        try {
            $login_id = Session::get('login_id');
            $arrGrade=[
                        '1'=>array('grade'=>'A','class'=>'greenDark'),
                        '2'=>array('grade'=>'B','class'=>'greenLight'),
                        '3'=>array('grade'=>'C','class'=>'yellow'),
                        '4'=>array('grade'=>'D','class'=>'orange'),
                        '5'=>array('grade'=>'E','class'=>'red')
                    ];
                    
            $trainingrating = DB::table('training_performance')
                    ->select('rating',db::raw('count(rating) as ratingcount'))
                    ->whereRaw("trainee_id=$login_id AND status=1")
                    ->orderby('ratingcount','DESC')
                    ->orderby('rating','ASC')
                    ->groupby('rating')
                    ->first();
            
            if(isset($trainingrating)){
               $trainingGrade=$arrGrade[$trainingrating->rating];
            }else{
                $trainingGrade=array('grade'=>'NA','class'=>'gray');
            }

           
            $employeerating = DB::table('punch_performance')
                    ->select('rating',db::raw('count(rating) as ratingcount'))
                    ->whereRaw("employe_id=$login_id AND status=1")
                    ->orderby('ratingcount','DESC')
                    ->orderby('rating','ASC')
                    ->groupby('rating')
                    ->first();
            
            if(isset($employeerating)){
               $workGrade=$arrGrade[$employeerating->rating];
            }else{
                $workGrade=array('grade'=>'NA','class'=>'gray');
            }
            
            $curdate=date('Y-m-d');
            $days_ago = date('Y-m-d', strtotime('-3 days', strtotime($curdate)));
            
            $todorating = DB::table('task_history as his')
                    ->leftjoin('tasks', 'his.task_id', '=', 'tasks.id')
                    ->whereRaw("date(his.created_at)<='$curdate' AND date(his.created_at)>='$days_ago' AND his.status=3 AND tasks.owner_id=$login_id AND tasks.task_type NOT IN(3,5)")
                    ->count();
            
            if($todorating>=10){
                $todoGrade=array('grade'=>'Good','class'=>'greenDark');
            } else if($todorating<10 && $todorating>0){
                $todoGrade=array('grade'=>'Average','class'=>'yellow');
            }else{
               $todoGrade=array('grade'=>'Bad','class'=>'red');
            }
           
            return view('dashboard/kpi/index', array('trainingGrade' => $trainingGrade,'workGrade'=>$workGrade,'todoGrade'=>$todoGrade));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }

}
