<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tasks;
use DB;
use Mail;
use Excel;

class ReportEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ReportEmails:sendreportemails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report Emails';

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
        $currentTime = date("H:i");
        $reports = DB::table('report_settings')
                ->select('report_settings.*')
                ->where('status', '=', '1')
                ->whereRaw("time='$currentTime'")
                ->get();

        foreach ($reports as $report) {
            $funName = $report->function_name;
            $this->$funName($report);
        }
    }

    public function getNoAcivityReport($report) {
        try {

            $datStart = ""; $datEnd = "";
            if ($report->type == "Daily") {
                $datStart = date('Y-m-d', strtotime("-1 days"));
                $datEnd = date('Y-m-d', strtotime("-1 days"));
            } else if ($report->type == "Weekly") {
                $day_num = date('w', strtotime(date('Y-m-d'))) + 1;
                if ($report->day == $day_num) {
                    $datStart = date('Y-m-d', strtotime("-7 days"));
                    $datEnd = date('Y-m-d', strtotime("-1 days"));
                }
            } else {
                if (date("t") == date("d")) {
                    $datStart = date('Y-m-01');
                    $datEnd = date('Y-m-t', strtotime("-1 days"));
                }
            }

            if ($datStart != "" && $datEnd != "") {

                $arrExemptedEmps = explode(",", $report->exempted_emps);
                
                $arrActiveEmps = DB::table('tasks')
                                ->select('tasks.owner_id')
                                ->whereRaw("((date(start_date) BETWEEN '$datStart' AND '$datEnd') OR (date(end_date) BETWEEN '$datStart' AND '$datEnd') OR (date(start_date) < '$datStart' AND date(end_date) > '$datEnd'))")
                                ->whereRaw("tasks.status!=0")->distinct()
                                ->get()->pluck("owner_id")->toarray();
                
                $inactiveEmps = DB::table('employees')
                        ->select('id', 'username', 'first_name', 'alias_name', 'mobile_number', db::raw("case when email='' then contact_email when email=null then contact_email else email end as email"))
                        ->where('status', '=', '1')
                        ->whereNotIn('employees.id', $arrExemptedEmps)
                        ->whereNotIn('employees.id', $arrActiveEmps)
                        ->get();
                
                /*****************Excel generation starts here *****************/
                $datStartFormated = date('d-m-Y', strtotime($datStart));
                $datEndFormated = date('d-m-Y', strtotime($datEnd));
                $reportname = "noactivityemployees" . date("d-m-Y");
                $result = Excel::create($reportname, function($excel) use($inactiveEmps, $datStartFormated, $datEndFormated) {
                            // Set the title
                            $excel->setTitle('title');
                            $excel->sheet('noactivityemployees', function($sheet) use($inactiveEmps, $datStartFormated, $datEndFormated) {
                                // Sheet manipulation
                                $sheet->setCellValue('C3', "Employees With No Activity From $datStartFormated To $datEndFormated");
                                $sheet->setHeight(3, 20);

                                $sheet->cells('A3:E3', function($cells) {
                                    $cells->setBackground('#00CED1');
                                    $cells->setFontWeight('bold');
                                    $cells->setFontSize(14);
                                });

                                $sheet->setWidth(array('A' => 10,'B' => 10,'C' => 50,'D' => 15,'E' => 30,));

                                $chrRow = 6;

                                $sheet->row(5, array('Sl No.', 'Code', 'Name', 'Mobile No.', 'Email'));
                                $sheet->setHeight(5, 15);
                                $sheet->cells('A5:E5', function($cells) {
                                    $cells->setBackground('#6495ED');
                                    $cells->setFontWeight('bold');
                                });

                                for ($i = 0; $i < count($inactiveEmps); $i++) {

                                    $empname = $inactiveEmps[$i]->first_name . " " . $inactiveEmps[$i]->alias_name;

                                    $sheet->setCellValue('A' . $chrRow, ($i + 1));
                                    $sheet->setCellValue('B' . $chrRow, $inactiveEmps[$i]->username);
                                    $sheet->setCellValue('C' . $chrRow, $empname);
                                    $sheet->setCellValue('D' . $chrRow, $inactiveEmps[$i]->mobile_number);
                                    $sheet->setCellValue('E' . $chrRow, $inactiveEmps[$i]->email);

                                    $sheet->cells('A' . $chrRow . ':E' . $chrRow, function($cells) {
                                        $cells->setFontSize(9);
                                    });

                                    $chrRow++;
                                }
                            });
                        })->store('xls', storage_path('excel/noacivityreport'), true);

                $public_path = url('/');
                $basepath = str_replace("public", "", $public_path);
                $excelfilepath = $basepath . "storage/excel/noacivityreport/" . $result['file'];
                /*****************Excel generation ends here *****************/
                
                $strMailRecipients = $report->send_to_emps;
                $arrMailRecipients = explode(",", $strMailRecipients);
                $recipients = DB::table('employees')
                        ->select('employees.email', 'employees.contact_email')
                        ->whereIn('id', $arrMailRecipients)
                        ->get();

                foreach ($recipients as $recipient) {
                    if ($recipient->email != "" && $recipient->email != null) {
                        $emails[] = $recipient->email;
                    } else {
                        $emails[] = $recipient->contact_email;
                    }
                }

                Mail::send('emailtemplates.noactivityreport', ['text' => "Please see the attached file"], function($message) use ($emails, $excelfilepath) {
                    $message->to($emails)->subject('No Activity Report');
                    $message->attach($excelfilepath);
                });
            }
            echo "Success";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
    
    public function getDelayedActivityReport($report) {
        try {

            $datStart = ""; $datEnd = "";
            if ($report->type == "Daily") {
                $datStart = date('Y-m-d', strtotime("-1 days"));
                $datEnd = date('Y-m-d', strtotime("-1 days"));
            } else if ($report->type == "Weekly") {
                $day_num = date('w', strtotime(date('Y-m-d'))) + 1;
                if ($report->day == $day_num) {
                    $datStart = date('Y-m-d', strtotime("-7 days"));
                    $datEnd = date('Y-m-d', strtotime("-1 days"));
                }
            } else {
                if (date("t") == date("d")) {
                    $datStart = date('Y-m-01');
                    $datEnd = date('Y-m-t', strtotime("-1 days"));
                }
            }

            if ($datStart != "" && $datEnd != "") {

                $arrExemptedEmps = explode(",", $report->exempted_emps);
                
                $arrDelayedEmps = DB::table('tasks')
                                ->select('tasks.title','tasks.end_date','hist.created_at as action_date','hist.status','emp.username', 'emp.first_name', 'emp.alias_name', 'emp.mobile_number', db::raw("case when email='' then contact_email when email=null then contact_email else email end as email"))
                                ->leftjoin('task_history as hist', 'hist.task_id', '=', 'tasks.id')
                                ->leftjoin('employees as emp', 'tasks.owner_id', '=', 'emp.id')
                                ->whereRaw("date(hist.created_at)>date(tasks.end_date) AND hist.status!=0")
                                ->whereRaw("(date(hist.created_at) BETWEEN '$datStart' AND '$datEnd')")
                                ->whereNotIn('tasks.owner_id', $arrExemptedEmps)
                                ->whereRaw("tasks.status!=0 AND tasks.task_type!=3")->distinct()
                                ->get();
                
                        
                /*****************Excel generation starts here *****************/
                $datStartFormated = date('d-m-Y', strtotime($datStart));
                $datEndFormated = date('d-m-Y', strtotime($datEnd));
                $reportname = "delayedactivityemployees" . date("d-m-Y");
                $result = Excel::create($reportname, function($excel) use($arrDelayedEmps, $datStartFormated, $datEndFormated) {
                            // Set the title
                            $excel->setTitle('title');
                            $excel->sheet('delayedactivityemployees', function($sheet) use($arrDelayedEmps, $datStartFormated, $datEndFormated) {
                                // Sheet manipulation
                                $sheet->setCellValue('C3', "Employees With Delayed Activity From $datStartFormated To $datEndFormated");
                                $sheet->setHeight(3, 20);

                                $sheet->cells('A3:I3', function($cells) {
                                    $cells->setBackground('#00CED1');
                                    $cells->setFontWeight('bold');
                                    $cells->setFontSize(14);
                                });

                                $sheet->setWidth(array('A' => 10,'B' => 30,'C' => 10,'D'=>10,'E' => 10,'F' => 10,'G' => 50,'H' => 15,'I' => 30,));

                                $chrRow = 6;

                                $sheet->row(5, array('Sl No.', 'Task Title','End Date','Actvity Date','Status','Code', 'Name', 'Mobile No.', 'Email'));
                                $sheet->setHeight(5, 15);
                                $sheet->cells('A5:I5', function($cells) {
                                    $cells->setBackground('#6495ED');
                                    $cells->setFontWeight('bold');
                                });

                                for ($i = 0; $i < count($arrDelayedEmps); $i++) {
                                    $status="";
                                    if($arrDelayedEmps[$i]->status==3){
                                        $status="Completed";
                                    }else if($arrDelayedEmps[$i]->status==2){
                                        $status="Pending";
                                    }
                                    $empname = $arrDelayedEmps[$i]->first_name . " " . $arrDelayedEmps[$i]->alias_name;

                                    $sheet->setCellValue('A' . $chrRow, ($i + 1));
                                    $sheet->setCellValue('B' . $chrRow, $arrDelayedEmps[$i]->title);
                                    $sheet->setCellValue('C' . $chrRow, date('d-m-Y',  strtotime($arrDelayedEmps[$i]->end_date)));
                                    $sheet->setCellValue('D' . $chrRow, date('d-m-Y',  strtotime($arrDelayedEmps[$i]->action_date)));
                                    $sheet->setCellValue('E' . $chrRow, $status);
                                    $sheet->setCellValue('F' . $chrRow, $arrDelayedEmps[$i]->username);
                                    $sheet->setCellValue('G' . $chrRow, $empname);
                                    $sheet->setCellValue('H' . $chrRow, $arrDelayedEmps[$i]->mobile_number);
                                    $sheet->setCellValue('I' . $chrRow, $arrDelayedEmps[$i]->email);

                                    $sheet->cells('A' . $chrRow . ':I' . $chrRow, function($cells) {
                                        $cells->setFontSize(9);
                                    });

                                    $chrRow++;
                                }
                            });
                        })->store('xls', storage_path('excel/delayedacivityreport'), true);

                $public_path = url('/');
                $basepath = str_replace("public", "", $public_path);
                $excelfilepath = $basepath . "storage/excel/delayedacivityreport/" . $result['file'];
                /*****************Excel generation ends here *****************/
                
                $strMailRecipients = $report->send_to_emps;
                $arrMailRecipients = explode(",", $strMailRecipients);
                $recipients = DB::table('employees')
                        ->select('employees.email', 'employees.contact_email')
                        ->whereIn('id', $arrMailRecipients)
                        ->get();

                foreach ($recipients as $recipient) {
                    if ($recipient->email != "" && $recipient->email != null) {
                        $emails[] = $recipient->email;
                    } else {
                        $emails[] = $recipient->contact_email;
                    }
                }

                Mail::send('emailtemplates.noactivityreport', ['text' => "Please see the attached file"], function($message) use ($emails, $excelfilepath) {
                    $message->to($emails)->subject('Delayed Activity Report');
                    $message->attach($excelfilepath);
                });
            }
            
           echo "Success";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
