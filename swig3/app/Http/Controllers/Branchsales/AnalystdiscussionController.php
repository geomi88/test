<?php

namespace App\Http\Controllers\Branchsales;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use App\Notifications\AnalystDiscussionNotifications;
use App\Models\Pos_sale;
use App\Models\Masterresources;
use App\Models\Analyst_discussion;
use DB;

class AnalystdiscussionController extends Controller {

    public function index() {
        try {
            $login_id = Session::get('login_id');
            /* $user_branches = DB::table('branches_to_analyst')
              ->select('branches_to_analyst.*','branch_details.name')
              ->leftjoin('master_resources as branch_details', 'branch_details.id', '=', 'branches_to_analyst.branch_id')
              ->whereRaw("branches_to_analyst.analyst_id = $login_id and branches_to_analyst.status = 1")
              ->orderBy('created_at', 'DESC')->get(); */
           /* $branches = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->where(['resource_type' => 'BRANCH', 'status' => 1])->get();*/
            
             $branches = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->join('resource_allocation','resource_allocation.branch_id','=','master_resources.id')
                            ->where(['master_resources.resource_type' => 'BRANCH', 'status' => 1,'active'=>1,'resource_allocation.employee_id'=>$login_id])->get();
            
            
            $branch_id = Input::get('branch_id');
            if ($branch_id == '' || $branch_id == 0) {
                $discussions = DB::table('analyst_discussion')
                                ->select('analyst_discussion.*')
                                ->whereRaw("(JSON_CONTAINS(participants, '$login_id') or creator_id = $login_id) and parent_id = 0")
                                ->orderBy('created_at', 'DESC')->get();
            } else {
                $discussions = DB::table('analyst_discussion')
                                ->select('analyst_discussion.*')
                                ->whereRaw("(JSON_CONTAINS(participants, '$login_id') or creator_id = $login_id) and parent_id = 0 and branch_id = $branch_id")
                                ->orderBy('created_at', 'DESC')->get();
            }
            return view('branchsales/analyst_discussion/index', array('discussions' => $discussions, 'branches' => $branches,'branch_id' => $branch_id));
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('branchsales');
        }
    }

    public function view($id) {
        try {
            $id = \Crypt::decrypt($id);

            $discussion_topic = DB::table('analyst_discussion')
                    ->select('analyst_discussion.*', 'employee_details.first_name', 'employee_details.alias_name')
                    ->join('employees as employee_details', 'employee_details.id', '=', 'analyst_discussion.creator_id')
                    ->whereRaw("analyst_discussion.id = $id")
                    ->first();

            $messages = DB::table('analyst_discussion')
                            ->select('analyst_discussion.*', 'employee_details.first_name', 'employee_details.alias_name')
                            ->join('employees as employee_details', 'employee_details.id', '=', 'analyst_discussion.creator_id')
                            ->whereRaw("analyst_discussion.parent_id = $id")
                            ->orderBy('analyst_discussion.id', 'ASC')->get();
            return view('branchsales/analyst_discussion/view', array('messages' => $messages, 'discussion_topic' => $discussion_topic));
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('branchsales/analyst_discussion');
        }
    }

    public function sendreply() {

        $analystdiscussionmodel = new Analyst_discussion;
        $data = Input::all();
        $id = $data['parent_id'];
        $analystdiscussionmodel->creator_id = Session::get('login_id');
        $analystdiscussionmodel->parent_id = $data['parent_id'];
        $analystdiscussionmodel->branch_id = $data['branch_id'];
        $analystdiscussionmodel->message = $data['message'];
        $analystdiscussionmodel->save();

        $discussion_topic = DB::table('analyst_discussion')
                ->select('analyst_discussion.*', 'employee_details.first_name', 'employee_details.alias_name')
                ->join('employees as employee_details', 'employee_details.id', '=', 'analyst_discussion.creator_id')
                ->whereRaw("analyst_discussion.id = $id")
                ->first();

        $messages = DB::table('analyst_discussion')
                        ->select('analyst_discussion.*', 'employee_details.first_name', 'employee_details.alias_name')
                        ->join('employees as employee_details', 'employee_details.id', '=', 'analyst_discussion.creator_id')
                        ->whereRaw("analyst_discussion.parent_id = $id")
                        ->orderBy('analyst_discussion.id', 'ASC')->get();
        $selected_contacts = json_decode($discussion_topic->participants);
        foreach ($selected_contacts as $selected_contact) {
            $from = Session::get('login_id');
            $to = (int) $selected_contact;
            if ($to != Session::get('login_id')) {
                $message = 'You have a new message in kpi analysis discussion';
                $category = "Analyst Discussion";
                $type = "branchsales/analyst_discussion/view";
                Auth::user()->notify(new AnalystDiscussionNotifications($from, $to, $message, $category, (int) $data['parent_id'], $type));
            }
        }
        $id = \Crypt::encrypt($id);
        return Redirect::to('branchsales/analyst_discussion/view/' . $id);
    }

}
