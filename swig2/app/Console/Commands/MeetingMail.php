<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tasks;
use DB;
use Mail;

class MeetingMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MeetingMail:sendmeetingmail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Meeting Mail';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
    
        $currentTime = date("Y-m-d H:i:s");
        $afterOnehr = date("Y-m-d H:i:s", strtotime("+1 hours"));

        $tasks = DB::table('tasks')
                ->select('tasks.id as id','m.user_id as user_id','tasks.owner_id as owner_id',
                        'tasks.title','tasks.description',
                        'tasks.start_date','tasks.end_date','e1.first_name as ownername',
                        'e2.first_name as attendename','room.name as meetingroom',
                        'e2.email as attendemail','e1.email as owneremail')
                ->leftjoin('meeting_attendees as m', 'm.meeting_id', '=', 'tasks.id')
                ->leftjoin('master_resources as room', 'tasks.meeting_room', '=', 'room.id')
                ->leftjoin('employees as e1', 'tasks.owner_id', '=', 'e1.id')
                ->leftjoin('employees as e2', 'm.user_id', '=', 'e2.id')
                ->whereRaw("start_date >='$currentTime' AND start_date <='$afterOnehr' AND task_type=3 AND tasks.status=1 AND mail_sent=0 AND m.user_id IS NOT NULL")
                ->get();
        
        if (count($tasks)) {
            //send mail to attendees
            $arrMeetings=array();
            $arrAttendees=array();
            foreach ($tasks as $value) {
                $email=$value->attendemail;
                Mail::send('emailtemplates.meeting_details', ['name' => $value->ownername, 'title' => $value->title, 'description' => $value->description, 'start_time' => date('d-m-Y H:i:s',  strtotime($value->start_date)), 'end_date' => date('d-m-Y H:i:s',  strtotime($value->end_date)), 'meeting_room' => $value->meetingroom], function($message)use ($email) {
                    $message->to($email)->subject('Meeting Schedule');
                });
                
                $arrMeetings[]=$value->id;
                $arrAttendees[]=$value->user_id;
            }
            
            //update mail send status in task table
            $updatetasks = DB::table('tasks')
                    ->whereIn('id',$arrMeetings)                            
                    ->update(['mail_sent'=>1]);
            
            //send mail to owner
            $value=$tasks[0];
            $email=$value->owneremail;
            if(!in_array($value->owner_id, $arrAttendees)){
                Mail::send('emailtemplates.meeting_details', ['name' => $value->attendename, 'title' => $value->title, 'description' => $value->description, 'start_time' => date('d-m-Y H:i:s',  strtotime($value->start_date)), 'end_date' => date('d-m-Y H:i:s',  strtotime($value->end_date)), 'meeting_room' => $value->meetingroom], function($message)use ($email) {
                    $message->to($email)->subject('Meeting Schedule');
                });
            }
            
        }
    }
}
