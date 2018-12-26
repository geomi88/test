<?php

namespace App\Http\Controllers\Meeting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Company;
use App\Models\Tasks;
use App\Models\Task_history;
use App\Models\Meeting_attendees;
use DB;
use App;
use PDF;
use Excel;

class AgendaController extends Controller {

    public function index(Request $request, $id = '') {
        
    }

    
    public function add_agenda($meeting_id) {
        $meeting_details = DB::table('tasks')
                        ->select('tasks.*')
                        ->whereRaw("id = $meeting_id")->first();
        if($meeting_details->parent_meeting_id > 0)
        {
            $parent_meeting_id = $meeting_details->parent_meeting_id;
        }
        else
        {
            $parent_meeting_id = $meeting_id;
        }
        
        $meetings = DB::table('tasks')
                        ->select('tasks.*')
                        ->whereRaw("id = $parent_meeting_id or parent_meeting_id = $parent_meeting_id and task_type=3")
                        ->whereRaw("tasks.status=1")
                        ->orderby('tasks.start_date', 'ASC')
                        ->get();
        $meeting_attendees = DB::table('meeting_attendees')
                        ->select('meeting_attendees.*','employee_details.first_name','employee_details.last_name','employee_details.username','employee_details.email')
                        ->join('employees as employee_details', 'employee_details.id', '=', 'meeting_attendees.user_id')
                        ->whereRaw("meeting_attendees.meeting_id = $meeting_id")->get();

        
        return view('meeting/agenda/add_agenda', array('meetings' => $meetings, 'meeting_id' => $meeting_id,'meeting_attendees' => $meeting_attendees));
    }
    public function save_agenda() {
        try {
            $input = Input::all();
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            $meeting_id = Input::get('meeting_id');
            $start_date = Input::get('start_date');
            $end_date = Input::get('end_date');
            
            $attendees = array();
            if (!empty(Input::get('empdetails'))) {
                $attendees = json_decode(Input::get('empdetails'));
            }

            $start_date = explode('-', $start_date);
            $start_date = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];

            $end_date = explode('-', $end_date);
            $end_date = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];


            $start_date = $start_date . ' 00:00:00';
            $end_date = $end_date . ' 00:00:00';

            

            
            
            foreach ($attendees as $attendee) {
                
                $model = new Tasks();
                $model->title = Input::get('title');
                $model->description = Input::get('description');
                $model->start_date = $start_date;
                $model->end_date = $end_date;
                $model->owner_id = $attendee->emp_id;
                $model->is_all_day_task = 1;
                $model->priority = 0;
                $model->status = 1;
                $model->task_type = 4;
                $model->parent_meeting_id = $meeting_id;
                $meeting_data = $model->save();

                /** task history insertion**/
                $historyModal=new Task_history();
                $historyModal->task_id=$model->id;
                $historyModal->status=1;
                $historyModal->save();
                /***/
                
                /*$attendees_model = new Meeting_attendees();
                $attendees_model->meeting_id = $model->id;
                $attendees_model->user_id = $attendee->emp_id;
                $attendees_model->save();*/
            }

            


            Toastr::success('Agenda Added Successfully!', $title = null, $options = []);
            return Redirect::to('meeting/agenda/add_agenda/' . $meeting_id);
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('meeting');
        }
    }
    public function edit($agenda_id) {
        try {
            $agenda_id = \Crypt::decrypt($agenda_id);
            $agenda_details = DB::table('tasks as agenda_details')
                            ->select('agenda_details.*','meeting_details.owner_id')
                            ->join('tasks as meeting_details', 'meeting_details.id', '=', 'agenda_details.parent_meeting_id')
                            ->whereRaw("agenda_details.id = $agenda_id")->first();

            return view('meeting/agenda/edit', array('agenda_details' => $agenda_details));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('meeting/meeting_list');
        }
    }

    public function update() {
        try {
            $id = Input::get('agenda_id');
            $description = Input::get('description');
            if(Input::get('agenda_status') == -1)
            {
                $agenda_status = 1;
            }
            else
            {
                $agenda_status = Input::get('agenda_status');
            }
            $update_agenda = DB::table('tasks')
                    ->where(['id' => $id])
                    ->update(['description' => $description,'status' => $agenda_status]);
            $task_history = DB::table('task_history')
                        ->select('task_history.*')
                        ->where(['task_id' => $id,'status' =>$agenda_status])
                        ->get();
            if(count($task_history)<1)
            {
                $historyModal=new Task_history();

                $historyModal->task_id=$id;
                $historyModal->status=$agenda_status;
                $historyModal->save();
            }
            $agenda_id = \Crypt::encrypt($id);
            Toastr::success('Updated Successfully!', $title = null, $options = []);
            return Redirect::to("meeting/agenda/edit/$agenda_id");
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('meeting/meeting_list');
        }
    }

}
