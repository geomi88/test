<?php

namespace App\Http\Controllers\Kpi;

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

    public function index($branch_id) {
        $branch_id = \Crypt::decrypt($branch_id);
        try {
            $login_id = Session::get('login_id');
            $discussions = DB::table('analyst_discussion')
                            ->select('analyst_discussion.*')
                            ->whereRaw("(JSON_CONTAINS(participants, '$login_id') or creator_id = $login_id) and parent_id = 0 and branch_id = $branch_id")
                            ->orderBy('created_at', 'DESC')->get();
            return view('kpi/analyst_discussion/index', array('discussions' => $discussions));
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('kpi/branch/view/' . $branch_id);
        }
    }

    public function store() {
        $data = Input::all();
        $branch_id = \Crypt::encrypt($data['branch_id']);
        try {

            $analystdiscussionmodel = new Analyst_discussion;

            $analystdiscussionmodel->creator_id = Session::get('login_id');
            $analystdiscussionmodel->participants = $data['selected_contacts'];
            $analystdiscussionmodel->branch_id = $data['branch_id'];
            $analystdiscussionmodel->parent_id = 0;
            $analystdiscussionmodel->subject = $data['subject'];
            $analystdiscussionmodel->message = $data['message'];
            $analystdiscussionmodel->type = $data['type'];
            $analystdiscussionmodel->status = 1;

            $analystdiscussionmodel->save();
            $latest_id = $analystdiscussionmodel->id;

            $selected_contacts = json_decode($data['selected_contacts']);
            foreach ($selected_contacts as $selected_contact) {
                $from = Session::get('login_id');
                $to = (int) $selected_contact;
                if($to!=Session::get('login_id'))
                {
                $message = 'You have a new message in kpi analysis discussion';
                $category = "Analyst Discussion";
                $type = "branchsales/analyst_discussion/view";
                Auth::user()->notify(new AnalystDiscussionNotifications($from, $to, $message, $category, (int)$latest_id, $type));
                }
            }

            Toastr::success('Successfully created the discussion!', $title = null, $options = []);
            return Redirect::to('kpi/branch/view/' . $branch_id);
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('kpi/branch/view/' . $branch_id);
        }
    }

    public function view($id) {
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
        return view('kpi/analyst_discussion/view', array('messages' => $messages, 'discussion_topic' => $discussion_topic));
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
                if($to!=Session::get('login_id'))
                {
                $message = 'You have a new message in kpi analysis discussion';
                $category = "Analyst Discussion";
                $type = "branchsales/analyst_discussion/view";
                Auth::user()->notify(new AnalystDiscussionNotifications($from, $to, $message, $category, (int)$data['parent_id'], $type));
                }
            }
        
        
        $id = \Crypt::encrypt($id);
        return Redirect::to('kpi/analyst_discussion/view/' . $id);
    }

}
