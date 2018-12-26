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
use App\Notifications\MeetingNotification;
use DB;
use App;
use PDF;
use Excel;
use Mail;

class MeetingController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        $companyid = session('company');
        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }
        $meetings = DB::table('tasks')
                ->select('tasks.*')
                ->join('meeting_attendees', 'meeting_attendees.meeting_id', '=', 'tasks.id')
                ->whereRaw("tasks.task_type = 3 and (meeting_attendees.user_id = $login_id or tasks.owner_id = $login_id)")
                ->whereRaw("tasks.status=1")
                ->orderby('tasks.created_at', 'DESC')
                ->groupby('tasks.id')
                ->paginate($paginate);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }



            $meetings = DB::table('tasks')
                ->select('tasks.*')
                ->join('meeting_attendees', 'meeting_attendees.meeting_id', '=', 'tasks.id')
                ->whereRaw("tasks.task_type = 3 and (meeting_attendees.user_id = $login_id or tasks.owner_id = $login_id)")
                ->whereRaw("tasks.status=1")
                ->orderby('tasks.created_at', 'DESC')
                ->groupby('tasks.id')
                ->paginate($paginate);

            return view('meeting/meeting_result', array('meetings' => $meetings));
        }

        return view('meeting/meeting_list', array('meetings' => $meetings));
    }

    public function createmeeting(Request $request, $id = '') {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $arrId = array();
        if ($id) {
            $empid = \Crypt::decrypt($id);
            $arrId = explode(",", $empid);
        }
        $curDate = date('Y-m-d');
        $employees = DB::table('employees')
                ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', db::raw("(select count(*) from tasks where status=1 AND owner_id=employees.id) as new_count"), db::raw("(select count(*) from tasks where status=2 AND owner_id=employees.id) as pending_count"))
                ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                ->where('employees.status', '!=', 2)
                ->orderby('employees.first_name', 'ASC')
                ->paginate($paginate);
        $job_positions = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'JOB_POSITION', 'status' => 1])
                ->orderby('name', 'ASC')
                ->get();
        $meeting_rooms = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'MEETING_ROOM', 'status' => 1])
                ->orderby('name', 'ASC')
                ->get();
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_key = Input::get('search_key');
            $job_position = Input::get('job_position');
            $searchbycode = Input::get('searchbycode');
            $sortordname = Input::get('sortordname');
            $sortordjob = Input::get('sortordjob');
            $sortordcode = Input::get('sortordcode');

            $strids = Input::get('strids');
            $strids = rtrim($strids, ",");
            $arrId = explode(",", $strids);

            $sortOrdDefault = '';
            if ($sortordname == '' && $sortordjob == '' && $sortordcode == '') {
                $sortOrdDefault = 'ASC';
            }

            $employees = DB::table('employees')
                    ->select('employees.*', 'job_position.id as job_position_id', 'job_position.name as job_position_name', db::raw("(select count(*) from tasks where status=1 AND owner_id=employees.id) as new_count"), db::raw("(select count(*) from tasks where status=2 AND owner_id=employees.id) as pending_count"))
                    ->leftjoin('master_resources as job_position', 'job_position.id', '=', 'employees.job_position')
                    ->where('employees.status', '!=', 2)
                    ->when($search_key, function ($query) use ($search_key) {
                        return $query->whereRaw("(employees.first_name like '$search_key%' or concat(employees.first_name,' ',employees.alias_name,' ',employees.last_name) like '$search_key%')");
                    })
                    ->when($job_position, function ($query) use ($job_position) {
                        return $query->where('employees.job_position', '=', $job_position);
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(employees.username like '$searchbycode%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('employees.first_name', $sortordname);
                    })
                    ->when($sortordjob, function ($query) use ($sortordjob) {
                        return $query->orderby('job_position.name', $sortordjob);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('employees.username', $sortordcode);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('employees.first_name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('meeting/emp_result', array('employees' => $employees, 'arrid' => $arrId));
        }
        return view('meeting/createmeeting', array('employees' => $employees, "meetingdate" => "", 'job_positions' => $job_positions, 'arrid' => $arrId, 'meeting_rooms' => $meeting_rooms));
    }

    public function savemeeting() {
//        try {
            $input = Input::all();
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $start_date = Input::get('start_date');
            $end_date = Input::get('end_date');
            $start_time = Input::get('start_time');
            $duration = Input::get('duration');
            $isalldaytask = 0;
            $attendees = array();
            if (!empty(Input::get('empdetails'))) {
                $attendees = json_decode(Input::get('empdetails'));
            }
            $guests = array();
            if (!empty(Input::get('selected_guests'))) {
                $guests = json_decode(Input::get('selected_guests'));
            }

            $start_date = explode('-', $start_date);
            $start_date = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];

            $end_date = explode('-', $end_date);
            $end_date = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];


            $start_date = $start_date . ' ' . $start_time . ':00';

            $duration_exploded = explode(':', $duration);
            $duration_in_seconds = (($duration_exploded[0] * 60) + $duration_exploded[1]) * 60;
            $parent_end_date = $end_date . ' ' . $start_time . ':00';
            $parent_end_date = strtotime($start_date) + $duration_in_seconds;
            $parent_end_date = date('Y-m-d H:i:s', $parent_end_date);

            $model = new Tasks();
            $model->title = Input::get('title');
            $model->description = Input::get('description');
            $model->start_date = $start_date;
            $model->end_date = $parent_end_date;
            $model->owner_id = $login_id;
            $model->is_all_day_task = $isalldaytask;
            $model->priority = 0;
            $model->status = 1;
            $model->task_type = 3;
            $model->meeting_room = Input::get('meeting_room');
            $meeting_data = $model->save();
            $parent_meeting_id = $model->id;

            /** task history insertion* */
            $historyModal = new Task_history();
            $historyModal->task_id = $model->id;
            $historyModal->status = 1;
            $historyModal->save();
            /*             * */

            $meeting_room_id = Input::get('meeting_room');
            $meeting_room_details = DB::table('master_resources')
                            ->select('master_resources.name')
                            ->whereRaw("master_resources.id = $meeting_room_id")->first();

            foreach ($attendees as $attendee) {
                $attendees_model = new Meeting_attendees();
                $attendees_model->meeting_id = $model->id;
                $attendees_model->user_id = $attendee->emp_id;
                $attendees_model->is_organizer = $attendee->is_organizer;
                $attendees_model->save();

                /*$attendee_details = DB::table('employees')
                                ->select('employees.first_name', db::raw("COALESCE(employees.email,employees.contact_email) as email"))
                                ->whereRaw("employees.id = $attendee->emp_id")->first();*/
                $attendee_details = DB::table('employees')
                                ->select('employees.first_name', 'employees.email','employees.contact_email')
                                ->whereRaw("employees.id = $attendee->emp_id")->first();
                $attendee_email = $attendee_details->email;
                if($attendee_email == '' || $attendee_email == NULL)
                {
                    $attendee_email = $attendee_details->contact_email;
                }
                Mail::send('emailtemplates.meeting_details', ['name' => $attendee_details->first_name, 'title' => Input::get('title'), 'description' => Input::get('description'), 'start_time' => $start_date, 'end_date' => $parent_end_date, 'meeting_room' => $meeting_room_details->name], function($message)use ($attendee_email) {
                    $message->to($attendee_email)->subject('Meeting Schedule');
                });

                //Notification starts
                $type = "../../meeting/view";
                $from = '';
                $to = (int) $attendee->emp_id;
                $category = 'Meeting';
                $message = 'Meeting ' . Input::get('title') . ' has been scheduled';
                // Notification to next level
                Auth::user()->notify(new MeetingNotification($from, $to, $message, $category, $parent_meeting_id, $type));
                //Notification ends
            }
            foreach ($guests as $guest) {
                $attendees_model = new Meeting_attendees();
                $attendees_model->meeting_id = $model->id;
                $attendees_model->guest_name = $guest->guest_name;
                $attendees_model->guest_email = $guest->guest_email;
                $attendees_model->guest_phone = $guest->guest_phone;
                $attendees_model->save();
                $password = Input::get('mobile_number');

                Mail::send('emailtemplates.meeting_details', ['name' => $guest->guest_name, 'title' => Input::get('title'), 'description' => Input::get('description'), 'start_time' => $start_date, 'end_date' => $parent_end_date, 'meeting_room' => $meeting_room_details->name], function($message)use ($guest) {
                    $message->to($guest->guest_email)->subject('Meeting Schedule');
                });
            }

            //$start_date = date('Y-m-d H:i:s', strtotime($start_date . ' +1 day'));
            if (Input::get('repeat_option') == 'Daily') {
                $endDate = strtotime($end_date);
                $daily_selected_days = array();
                if (!empty(Input::get('daily_selected_days'))) {
                    $daily_selected_days = json_decode(Input::get('daily_selected_days'));
                    foreach ($daily_selected_days as $daily_selected_day) {
                        for ($i = strtotime($daily_selected_day, strtotime($start_date)); $i <= $endDate; $i = strtotime('+1 week', $i)) {
                            $meeting_start_time = date('Y-m-d', $i);
                            $meeting_start_time = $meeting_start_time . ' ' . $start_time . ':00';
                            $meeting_end_time = strtotime($meeting_start_time) + $duration_in_seconds;
                            $meeting_end_time = date('Y-m-d H:i:s', $meeting_end_time);
                            if ($start_date != $meeting_start_time) {
                                $model = new Tasks();
                                $model->title = Input::get('title');
                                $model->description = Input::get('description');
                                $model->start_date = $meeting_start_time;
                                $model->end_date = $meeting_end_time;
                                $model->owner_id = $login_id;
                                $model->is_all_day_task = $isalldaytask;
                                $model->priority = 0;
                                $model->status = 1;
                                $model->task_type = 3;
                                $model->meeting_room = Input::get('meeting_room');
                                $model->parent_meeting_id = $parent_meeting_id;
                                $meeting_data = $model->save();

                                /** task history insertion* */
                                $historyModal = new Task_history();
                                $historyModal->task_id = $model->id;
                                $historyModal->status = 1;
                                $historyModal->save();
                                /*                                 * */

                                foreach ($attendees as $attendee) {
                                    $attendees_model = new Meeting_attendees();
                                    $attendees_model->meeting_id = $model->id;
                                    $attendees_model->user_id = $attendee->emp_id;
                                    $attendees_model->is_organizer = $attendee->is_organizer;
                                    $attendees_model->save();

                                    $attendee_details = DB::table('employees')
                                                    ->select('employees.first_name', db::raw("COALESCE(employees.email,employees.contact_email) as email"))
                                                    ->whereRaw("employees.id = $attendee->emp_id")->first();
//                                    Mail::send('emailtemplates.meeting_details', ['name' => $attendee_details->first_name, 'title' => Input::get('title'), 'description' => Input::get('description'), 'start_time' => $meeting_start_time, 'end_date' => $meeting_end_time, 'meeting_room' => $meeting_room_details->name], function($message)use ($attendee_details) {
//                                        $message->to($attendee_details->email)->subject('Meeting Schedule');
//                                    });

                                    //Notification starts
                                    $type = "../../meeting/view";
                                    $from = '';
                                    $to = (int) $attendee->emp_id;
                                    $category = 'Meeting';
                                    $message = 'Meeting ' . Input::get('title') . ' has been scheduled';
                                    // Notification to next level
                                    Auth::user()->notify(new MeetingNotification($from, $to, $message, $category, $model->id, $type));
                                    //Notification ends
                                }
                                foreach ($guests as $guest) {
                                    $attendees_model = new Meeting_attendees();
                                    $attendees_model->meeting_id = $model->id;
                                    $attendees_model->guest_name = $guest->guest_name;
                                    $attendees_model->guest_email = $guest->guest_email;
                                    $attendees_model->guest_phone = $guest->guest_phone;
                                    $attendees_model->save();

//                                    Mail::send('emailtemplates.meeting_details', ['name' => $guest->guest_name, 'title' => Input::get('title'), 'description' => Input::get('description'), 'start_time' => $meeting_start_time, 'end_date' => $meeting_end_time, 'meeting_room' => $meeting_room_details->name], function($message)use ($guest) {
//                                        $message->to($guest->guest_email)->subject('Meeting Schedule');
//                                    });
                                }
                            }
                        }
                    }
                }
            }


            if (Input::get('repeat_option') == 'Weekly') {
                $endDate = strtotime($end_date);
                $repeat_number_week = Input::get('repeat_number_week');
                $weekly_selected_days = array();
                if (!empty(Input::get('weekly_selected_days'))) {
                    $weekly_selected_days = json_decode(Input::get('weekly_selected_days'));
                    foreach ($weekly_selected_days as $weekly_selected_day) {
                        for ($i = strtotime($weekly_selected_day, strtotime($start_date)); $i <= $endDate; $i = strtotime("+$repeat_number_week week", $i)) {
                            $meeting_start_time = date('Y-m-d', $i);
                            $meeting_start_time = $meeting_start_time . ' ' . $start_time . ':00';
                            $meeting_end_time = strtotime($meeting_start_time) + $duration_in_seconds;
                            $meeting_end_time = date('Y-m-d H:i:s', $meeting_end_time);
                            if ($start_date != $meeting_start_time) {
                                $model = new Tasks();
                                $model->title = Input::get('title');
                                $model->description = Input::get('description');
                                $model->start_date = $meeting_start_time;
                                $model->end_date = $meeting_end_time;
                                $model->owner_id = $login_id;
                                $model->is_all_day_task = $isalldaytask;
                                $model->priority = 0;
                                $model->status = 1;
                                $model->task_type = 3;
                                $model->meeting_room = Input::get('meeting_room');
                                $model->parent_meeting_id = $parent_meeting_id;
                                $meeting_data = $model->save();

                                /** task history insertion* */
                                $historyModal = new Task_history();
                                $historyModal->task_id = $model->id;
                                $historyModal->status = 1;
                                $historyModal->save();
                                /*                                 * */

                                foreach ($attendees as $attendee) {
                                    $attendees_model = new Meeting_attendees();
                                    $attendees_model->meeting_id = $model->id;
                                    $attendees_model->user_id = $attendee->emp_id;
                                    $attendees_model->is_organizer = $attendee->is_organizer;
                                    $attendees_model->save();

                                    $attendee_details = DB::table('employees')
                                                    ->select('employees.first_name', db::raw("COALESCE(employees.email,employees.contact_email) as email"))
                                                    ->whereRaw("employees.id = $attendee->emp_id")->first();
//                                    Mail::send('emailtemplates.meeting_details', ['name' => $attendee_details->first_name, 'title' => Input::get('title'), 'description' => Input::get('description'), 'start_time' => $meeting_start_time, 'end_date' => $meeting_end_time, 'meeting_room' => $meeting_room_details->name], function($message)use ($attendee_details) {
//                                        $message->to($attendee_details->email)->subject('Meeting Schedule');
//                                    });

                                    //Notification starts
                                    $type = "../../meeting/view";
                                    $from = '';
                                    $to = (int) $attendee->emp_id;
                                    $category = 'Meeting';
                                    $message = 'Meeting ' . Input::get('title') . ' has been scheduled';
                                    // Notification to next level
                                    Auth::user()->notify(new MeetingNotification($from, $to, $message, $category, $model->id, $type));
                                    //Notification ends
                                }
                                foreach ($guests as $guest) {
                                    $attendees_model = new Meeting_attendees();
                                    $attendees_model->meeting_id = $model->id;
                                    $attendees_model->guest_name = $guest->guest_name;
                                    $attendees_model->guest_email = $guest->guest_email;
                                    $attendees_model->guest_phone = $guest->guest_phone;
                                    $attendees_model->save();

//                                    Mail::send('emailtemplates.meeting_details', ['name' => $guest->guest_name, 'title' => Input::get('title'), 'description' => Input::get('description'), 'start_time' => $meeting_start_time, 'end_date' => $meeting_end_time, 'meeting_room' => $meeting_room_details->name], function($message)use ($guest) {
//                                        $message->to($guest->guest_email)->subject('Meeting Schedule');
//                                    });
                                }
                            }
                        }
                    }
                }
            }


            if (Input::get('repeat_option') == 'Custom') {
                $endDate = strtotime($end_date);
                $selected_custom_dates = array();
                if (!empty(Input::get('selected_custom_dates'))) {
                    $selected_custom_dates = json_decode(Input::get('selected_custom_dates'));
                }
                foreach ($selected_custom_dates as $selected_custom_date) {
                    $selected_custom_date = explode('-', $selected_custom_date);
                    $selected_custom_date = $selected_custom_date[2] . '-' . $selected_custom_date[1] . '-' . $selected_custom_date[0];
                    $meeting_start_time = $selected_custom_date;
                    $meeting_start_time = $meeting_start_time . ' ' . $start_time . ':00';
                    $meeting_end_time = strtotime($meeting_start_time) + $duration_in_seconds;
                    $meeting_end_time = date('Y-m-d H:i:s', $meeting_end_time);
                    $model = new Tasks();
                    $model->title = Input::get('title');
                    $model->description = Input::get('description');
                    $model->start_date = $meeting_start_time;
                    $model->end_date = $meeting_end_time;
                    $model->owner_id = $login_id;
                    $model->is_all_day_task = $isalldaytask;
                    $model->priority = 0;
                    $model->status = 1;
                    $model->task_type = 3;
                    $model->meeting_room = Input::get('meeting_room');
                    $model->parent_meeting_id = $parent_meeting_id;
                    $meeting_data = $model->save();

                    /** task history insertion* */
                    $historyModal = new Task_history();
                    $historyModal->task_id = $model->id;
                    $historyModal->status = 1;
                    $historyModal->save();
                    /*                     * */

                    foreach ($attendees as $attendee) {
                        $attendees_model = new Meeting_attendees();
                        $attendees_model->meeting_id = $model->id;
                        $attendees_model->user_id = $attendee->emp_id;
                        $attendees_model->is_organizer = $attendee->is_organizer;
                        $attendees_model->save();

                        $attendee_details = DB::table('employees')
                                        ->select('employees.first_name', db::raw("COALESCE(employees.email,employees.contact_email) as email"))
                                        ->whereRaw("employees.id = $attendee->emp_id")->first();
//                        Mail::send('emailtemplates.meeting_details', ['name' => $attendee_details->first_name, 'title' => Input::get('title'), 'description' => Input::get('description'), 'start_time' => $meeting_start_time, 'end_date' => $meeting_end_time, 'meeting_room' => $meeting_room_details->name], function($message)use ($attendee_details) {
//                            $message->to($attendee_details->email)->subject('Meeting Schedule');
//                        });

                        //Notification starts
                        $type = "../../meeting/view";
                        $from = '';
                        $to = (int) $attendee->emp_id;
                        $category = 'Meeting';
                        $message = 'Meeting ' . Input::get('title') . ' has been scheduled';
                        // Notification to next level
                        Auth::user()->notify(new MeetingNotification($from, $to, $message, $category, $model->id, $type));
                        //Notification ends
                    }
                    foreach ($guests as $guest) {
                        $attendees_model = new Meeting_attendees();
                        $attendees_model->meeting_id = $model->id;
                        $attendees_model->guest_name = $guest->guest_name;
                        $attendees_model->guest_email = $guest->guest_email;
                        $attendees_model->guest_phone = $guest->guest_phone;
                        $attendees_model->save();

//                        Mail::send('emailtemplates.meeting_details', ['name' => $guest->guest_name, 'title' => Input::get('title'), 'description' => Input::get('description'), 'start_time' => $meeting_start_time, 'end_date' => $meeting_end_time, 'meeting_room' => $meeting_room_details->name], function($message)use ($guest) {
//                            $message->to($guest->guest_email)->subject('Meeting Schedule');
//                        });
                    }
                }
            }


            Toastr::success('Meeting Added Successfully!', $title = null, $options = []);
            return Redirect::to('meeting/agenda/add_agenda/' . $parent_meeting_id);
//        } catch (\Exception $e) {
//            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
//            return Redirect::to('meeting');
//        }
    }

    public function view($meeting_id) {
        try {
            $meeting_id = \Crypt::decrypt($meeting_id);
            $meeting_details = DB::table('tasks')
                            ->select('tasks.*', 'master_resources.name as meeting_room', 'employee_details.first_name as owner_first_name', 'employee_details.last_name as owner_last_name', 'employee_details.username as owner_username', 'employee_details.email as owner_email')
                            ->leftjoin('master_resources', 'master_resources.id', '=', 'tasks.meeting_room')
                            ->leftjoin('employees as employee_details', 'employee_details.id', '=', 'tasks.owner_id')
                            ->whereRaw("tasks.id = $meeting_id")->first();
            $meeting_attendees = DB::table('meeting_attendees')
                            ->select('meeting_attendees.*', 'employee_details.first_name', 'employee_details.last_name', 'employee_details.username', 'employee_details.email')
                            ->join('employees as employee_details', 'employee_details.id', '=', 'meeting_attendees.user_id')
                            ->whereRaw("meeting_attendees.meeting_id = $meeting_id")->get();
            $meeting_agendas = DB::table('tasks')
                    ->select('tasks.*', 'employee_details.first_name', 'employee_details.last_name', 'tasks.owner_id as attendee_id')
//                    ->leftjoin('meeting_attendees', 'meeting_attendees.meeting_id', '=', 'tasks.id')
                    ->leftjoin('employees as employee_details', 'employee_details.id', '=', 'tasks.owner_id')
                    ->whereRaw("tasks.task_type = 4 and parent_meeting_id = $meeting_id")
                    ->orderby('tasks.created_at', 'DESC')
                    ->get();
            return view('meeting/view', array('meeting_details' => $meeting_details, 'meeting_attendees' => $meeting_attendees, 'meeting_agendas' => $meeting_agendas));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('meeting/meeting_list');
        }
    }

    public function checkroomavailability() {

        $start_date = Input::get('start_date');
        $end_date = Input::get('end_date');
        $start_time = Input::get('start_time');
        $duration = Input::get('duration');
        $room_id = Input::get('meeting_room');


        $start_date = explode('-', $start_date);
        $start_date = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];

        $end_date = explode('-', $end_date);
        $end_date = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];


        $start_date = $start_date . ' ' . $start_time . ':00';
        $duration_exploded = explode(':', $duration);

        $duration_in_seconds = (($duration_exploded[0] * 60) + $duration_exploded[1]) * 60;
        $arrDates = array();
        if (Input::get('repeat_option') == 'Daily') {
            $endDate = strtotime($end_date);
            $daily_selected_days = array();
            if (!empty(Input::get('daily_selected_days'))) {
                $daily_selected_days = json_decode(Input::get('daily_selected_days'));
                foreach ($daily_selected_days as $daily_selected_day) {
                    for ($i = strtotime($daily_selected_day, strtotime($start_date)); $i <= $endDate; $i = strtotime('+1 week', $i)) {
                        $meeting_start_time = date('Y-m-d', $i);
                        $meeting_start_time = $meeting_start_time . ' ' . $start_time . ':00';
                        $meeting_end_time = strtotime($meeting_start_time) + $duration_in_seconds;
                        $meeting_end_time = date('Y-m-d H:i:s', $meeting_end_time);

//                        if ($start_date != $meeting_start_time) {
                        $arrDates[] = $meeting_start_time . "," . $meeting_end_time;
//                        }
                    }
                }
            }
        }


        if (Input::get('repeat_option') == 'Weekly') {
            $endDate = strtotime($end_date);
            $repeat_number_week = Input::get('repeat_number_week');
            $weekly_selected_days = array();
            if (!empty(Input::get('weekly_selected_days'))) {
                $weekly_selected_days = json_decode(Input::get('weekly_selected_days'));
                foreach ($weekly_selected_days as $weekly_selected_day) {
                    for ($i = strtotime($weekly_selected_day, strtotime($start_date)); $i <= $endDate; $i = strtotime("+$repeat_number_week week", $i)) {
                        $meeting_start_time = date('Y-m-d', $i);
                        $meeting_start_time = $meeting_start_time . ' ' . $start_time . ':00';
                        $meeting_end_time = strtotime($meeting_start_time) + $duration_in_seconds;
                        $meeting_end_time = date('Y-m-d H:i:s', $meeting_end_time);
//                        if ($start_date != $meeting_start_time) {
                        $arrDates[] = $meeting_start_time . "," . $meeting_end_time;
//                        }
                    }
                }
            }
        }


        if (Input::get('repeat_option') == 'Custom') {
            $endDate = strtotime($end_date);
            $selected_custom_dates = array();
            if (!empty(Input::get('selected_custom_dates'))) {
                $selected_custom_dates = json_decode(Input::get('selected_custom_dates'));
            }

            foreach ($selected_custom_dates as $selected_custom_date) {
                $selected_custom_date = explode('-', $selected_custom_date);
                $selected_custom_date = $selected_custom_date[2] . '-' . $selected_custom_date[1] . '-' . $selected_custom_date[0];
                $meeting_start_time = $selected_custom_date;
                $meeting_start_time = $meeting_start_time . ' ' . $start_time . ':00';
                $meeting_end_time = strtotime($meeting_start_time) + $duration_in_seconds;
                $meeting_end_time = date('Y-m-d H:i:s', $meeting_end_time);

                $arrDates[] = $meeting_start_time . "," . $meeting_end_time;
            }
        }

        $strWhere = "";
        $meetings = array();
        if (count($arrDates) > 0) {
            foreach ($arrDates as $value) {
                $arrFromTo = explode(",", $value);
                $datfrom = $arrFromTo[0];
                $datto = $arrFromTo[1];
                $strWhere = $strWhere . "((start_date<='$datfrom' AND end_date>='$datfrom') OR (start_date<='$datto' AND end_date>='$datto') OR (start_date<'$datfrom' AND end_date >'$datfrom')) OR ";
            }
            $strWhere = rtrim($strWhere, " OR ");

            $curDate = date('Y-m-d');
            $meetings = DB::table('tasks')
                    ->select('tasks.*')
                    ->whereRaw("($strWhere)")
                    ->whereRaw("date(start_date)>='$curDate' AND tasks.task_type=3 AND meeting_room=$room_id")
                    ->get();
        }

        $arrReturn = array();
        if (count($meetings) > 0) {
            foreach ($meetings as $value) {

                $arrReturn[] = array("title" => $value->title,
                    'start' => date("d-m-Y H:i:s", strtotime($value->start_date)),
                    'end' => date("d-m-Y H:i:s", strtotime($value->end_date)));
            }
        }

        return \Response::json(array('meetings' => $arrReturn));
    }

    public function checkemployeeavailability() {

        $start_date = Input::get('start_date');
        $end_date = Input::get('end_date');
        $start_time = Input::get('start_time');
        $duration = Input::get('duration');
        $empid = Input::get('empid');


        $start_date = explode('-', $start_date);
        $start_date = $start_date[2] . '-' . $start_date[1] . '-' . $start_date[0];

        $end_date = explode('-', $end_date);
        $end_date = $end_date[2] . '-' . $end_date[1] . '-' . $end_date[0];


        $start_date = $start_date . ' ' . $start_time . ':00';
        $duration_exploded = explode(':', $duration);

        $duration_in_seconds = (($duration_exploded[0] * 60) + $duration_exploded[1]) * 60;
        $arrDates = array();
        if (Input::get('repeat_option') == 'Daily') {
            $endDate = strtotime($end_date);
            $daily_selected_days = array();
            if (!empty(Input::get('daily_selected_days'))) {
                $daily_selected_days = json_decode(Input::get('daily_selected_days'));
                foreach ($daily_selected_days as $daily_selected_day) {
                    for ($i = strtotime($daily_selected_day, strtotime($start_date)); $i <= $endDate; $i = strtotime('+1 week', $i)) {
                        $meeting_start_time = date('Y-m-d', $i);
                        $meeting_start_time = $meeting_start_time . ' ' . $start_time . ':00';
                        $meeting_end_time = strtotime($meeting_start_time) + $duration_in_seconds;
                        $meeting_end_time = date('Y-m-d H:i:s', $meeting_end_time);

//                        if ($start_date != $meeting_start_time) {
                        $arrDates[] = $meeting_start_time . "," . $meeting_end_time;
//                        }
                    }
                }
            }
        }


        if (Input::get('repeat_option') == 'Weekly') {
            $endDate = strtotime($end_date);
            $repeat_number_week = Input::get('repeat_number_week');
            $weekly_selected_days = array();
            if (!empty(Input::get('weekly_selected_days'))) {
                $weekly_selected_days = json_decode(Input::get('weekly_selected_days'));
                foreach ($weekly_selected_days as $weekly_selected_day) {
                    for ($i = strtotime($weekly_selected_day, strtotime($start_date)); $i <= $endDate; $i = strtotime("+$repeat_number_week week", $i)) {
                        $meeting_start_time = date('Y-m-d', $i);
                        $meeting_start_time = $meeting_start_time . ' ' . $start_time . ':00';
                        $meeting_end_time = strtotime($meeting_start_time) + $duration_in_seconds;
                        $meeting_end_time = date('Y-m-d H:i:s', $meeting_end_time);
//                        if ($start_date != $meeting_start_time) {
                        $arrDates[] = $meeting_start_time . "," . $meeting_end_time;
//                        }
                    }
                }
            }
        }


        if (Input::get('repeat_option') == 'Custom') {
            $endDate = strtotime($end_date);
            $selected_custom_dates = array();
            if (!empty(Input::get('selected_custom_dates'))) {
                $selected_custom_dates = json_decode(Input::get('selected_custom_dates'));
            }

            foreach ($selected_custom_dates as $selected_custom_date) {
                $selected_custom_date = explode('-', $selected_custom_date);
                $selected_custom_date = $selected_custom_date[2] . '-' . $selected_custom_date[1] . '-' . $selected_custom_date[0];
                $meeting_start_time = $selected_custom_date;
                $meeting_start_time = $meeting_start_time . ' ' . $start_time . ':00';
                $meeting_end_time = strtotime($meeting_start_time) + $duration_in_seconds;
                $meeting_end_time = date('Y-m-d H:i:s', $meeting_end_time);

                $arrDates[] = $meeting_start_time . "," . $meeting_end_time;
            }
        }



        $strWhere = "";
        $meetings = array();
        if (count($arrDates) > 0) {
            foreach ($arrDates as $value) {
                $arrFromTo = explode(",", $value);
                $datfrom = $arrFromTo[0];
                $datto = $arrFromTo[1];
                $strWhere = $strWhere . "((start_date<='$datfrom' AND end_date>='$datfrom') OR (start_date<='$datto' AND end_date>='$datto') OR (start_date<'$datfrom' AND end_date >'$datfrom')) OR ";
            }
            $strWhere = rtrim($strWhere, " OR ");

            $curDate = date('Y-m-d');
            $meetings = DB::table('tasks')
                    ->select('tasks.*')
                    ->join('meeting_attendees', 'tasks.id', '=', 'meeting_attendees.meeting_id')
                    ->whereRaw("($strWhere)")
                    ->whereRaw("date(start_date)>='$curDate' AND tasks.task_type=3 AND (owner_id=$empid OR user_id=$empid)")
                    ->distinct("meeting_attendees.meeting_id")
                    ->get();
        }

        $arrReturn = array();
        if (count($meetings) > 0) {
            foreach ($meetings as $value) {

                $arrReturn[] = array("title" => $value->title,
                    'start' => date("d-m-Y H:i:s", strtotime($value->start_date)),
                    'end' => date("d-m-Y H:i:s", strtotime($value->end_date)));
            }
        }

        return \Response::json(array('meetings' => $arrReturn));
    }

    public function edit($meeting_id) {
        try {
            $meeting_id = \Crypt::decrypt($meeting_id);
            $meeting_details = DB::table('tasks')
                            ->select('tasks.*', 'master_resources.name as meeting_room', 'employee_details.first_name as owner_first_name', 'employee_details.last_name as owner_last_name', 'employee_details.username as owner_username', 'employee_details.email as owner_email')
                            ->leftjoin('master_resources', 'master_resources.id', '=', 'tasks.meeting_room')
                            ->leftjoin('employees as employee_details', 'employee_details.id', '=', 'tasks.owner_id')
                            ->whereRaw("tasks.id = $meeting_id")->first();

            return view('meeting/edit', array('meeting_details' => $meeting_details));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('meeting/meeting_list');
        }
    }

    public function update() {
        try {
            $id = Input::get('id');
            $title = Input::get('title');
            $description = Input::get('description');
            $update_meeting = DB::table('tasks')
                    ->where(['id' => $id])
                    ->update(['title' => $title, 'description' => $description]);
            return Redirect::to('meeting/meeting_list');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('meeting/meeting_list');
        }
    }

    public function updateparticipation() {
        try {
            $meeting_id = Input::get('meeting_id');
            $user_id = Session::get('login_id');
            $reason = Input::get('reason');

            $update_availability = DB::table('meeting_attendees')
                    ->where(['user_id' => $user_id, 'meeting_id' => $meeting_id])
                    ->update(['availability_status' => 2, 'comment' => $reason]);
            $meeting_id = \Crypt::encrypt($meeting_id);
            Toastr::success('Updated Successfully!', $title = null, $options = []);
            return Redirect::to("meeting/view/$meeting_id");
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('meeting/meeting_list');
        }
    }
    
     public function deletemeeting($id) {
        try {
            $taskid = \Crypt::decrypt($id);
            $tasks = DB::table('tasks')
                    ->where(['id' => $taskid])
                    ->update(['status' => 0]);
            
//            DB::table('tasks')->where('id', '=', $taskid)->delete();
            
            Toastr::success('Meeting Deleted Successfully', $title = null, $options = []);
            return Redirect::to('meeting/meeting_list');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('meeting/meeting_list');
        }
    }
    
    public function addnote() {
        try {
            $meeting_id = Input::get('meeting_id');
            $user_id = Session::get('login_id');
            $note = Input::get('note');

            $update_meeting = DB::table('tasks')
                    ->where(['id' => $meeting_id])
                    ->update(['note' => $note]);
            $meeting_id = \Crypt::encrypt($meeting_id);
            Toastr::success('Updated Successfully!', $title = null, $options = []);
            return Redirect::to("meeting/view/$meeting_id");
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('meeting/meeting_list');
        }
    }
  
}
