<?php

namespace App\Http\Controllers\Supervisors;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Usermodule;
use DB;

class SalesdashboardController extends Controller {

    public function dashboard() {
        $search_key = Input::get('search_key');
        $selected_year = Input::get('selected_year');
        $selected_quarter = Input::get('selected_quarter');
        $notifications = DB::table('notifications')
                        ->select('notifications.*','analyst_discussion.id as discussion_parent_id','analyst_discussion.subject','employees.first_name')
                        ->leftjoin('analyst_discussion', 'analyst_discussion.id', '=', 'notifications.data->notifiable_id')
                        ->join('employees', 'employees.id', '=', 'notifications.data->from')
                        ->where('notifications.data->to', Session::get('login_id'))
                        ->where('notifications.data->category','Analyst Discussion')
                        ->orderBy('notifications.created_at', 'DESC')->get();
        return view('supervisors/sales_dashboard/dashboard', array('search_key' => $search_key,'notifications' => $notifications, 'selected_year' => $selected_year, 'selected_quarter' => $selected_quarter));
    }

}
