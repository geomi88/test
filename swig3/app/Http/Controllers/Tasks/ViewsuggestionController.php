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
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\Masterresources;
use Illuminate\Support\Facades\Config;
use App\Models\Company;
use App\Models\Suggestions;
use App;
use DB;


class ViewsuggestionController extends Controller

{

    public function view_suggestions() {
        
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $curDate=date('Y-m-d');
                
        $emp = DB::table('employees')
                ->select('m.name as job')
                ->leftjoin('master_resources as m', 'employees.job_position', '=', 'm.id')
                ->whereRaw("employees.id=$login_id")
                ->first();
        
        $jobposition=$emp->job;
        
        $new_suggestons = DB::table('suggestions')
                ->select('suggestions.*','emp.first_name as first_name')
                ->leftjoin('employees as emp', 'suggestions.created_by', '=', 'emp.id')
                ->whereRaw("suggestions.status=1 AND submitted_to IN ('$jobposition','Owner/CEO')")
                ->orderby('created_at','DESC')
                ->get();
        
        $noted_suggestons = DB::table('suggestions')
                ->select('suggestions.*','emp.first_name as first_name')
                ->leftjoin('employees as emp', 'suggestions.created_by', '=', 'emp.id')
                ->whereRaw("suggestions.status=2 AND submitted_to IN ('$jobposition','Owner/CEO')")
                ->orderby('updated_at','DESC')
                ->get();
        
        return view('dashboard/view_suggestion',array("new_suggestons"=>$new_suggestons,"noted_suggestons"=>$noted_suggestons));
    }
    
    public function complete_task() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $taskid = Input::get('taskid');
           
            $tasks = DB::table('suggestions')
                    ->where(['id' => $taskid])
                    ->update(['status' => 2]);
            
            Toastr::success('Suggestion Noted Successfully', $title = null, $options = []);
            return 1;
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/view_suggestions');
        }
    }
    
    
    public function deletesuggestion() {
        try {
            $taskid = Input::get('taskid');
            $tasks = DB::table('suggestions')
                    ->where(['id' => $taskid])
                    ->update(['status' => 0]);
            
            return 1;
        } catch (\Exception $e) {
            
            return -1;
        }
    }
    
}