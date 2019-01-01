<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notifications\TodoNotifications;
use App\Models\Notification;
use DB;

class NotifyTodo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'NotifyTodo:sendnotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Todo Notification';

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
    public function handle()
    {
        $currentDate = date('Y-m-d');
        $tasks = DB::table('tasks')
                ->select('tasks.owner_id as owner_id',db::raw('count(*) as count'))
                ->whereRaw("date(start_date)='$currentDate'")
                ->groupBy("tasks.owner_id")
                ->get();

        foreach ($tasks as $todo) {

            $message = "You have ".$todo->count." more to do's for today. Please click to view more";
            $type = 'dashboard/todo';
            $category = 'To do Notification';
            $from = '';
            $to = $todo->owner_id;
            
            $arrData=array(
                        'to' => $to,
                        'from' => $from,
                        'message' => $message,
                        'category' => $category,
                        'notifiable_id' => '',
                        'type' => $type,
                    );
            
            $arrToJson=  json_encode($arrData);
            $curTime=time();
            $timeStr=substr($curTime,-10);
            
            $model = new Notification();
            $model->id = str_random(20).'-'.$timeStr;
            $model->notifiable_id = 1;
            $model->data = $arrToJson;
            $model->save();
        }
    }
}
