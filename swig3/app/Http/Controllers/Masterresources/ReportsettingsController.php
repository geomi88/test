<?php

namespace App\Http\Controllers\Masterresources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use App\Models\Reportsettings;
use App\Models\Masterresources;
use DB;
use App;
use PDF;
use Excel;

class ReportsettingsController extends Controller {

    public function index(Request $request) {
       
        try {
            $paginate = Config::get('app.PAGINATE');

            $report_settings = DB::table('report_settings')
                    ->select('report_settings.*')
                    ->where('status', '=', '1')
                    ->paginate($paginate);

            if ($request->ajax()) {
                $paginate = Input::get('pagelimit');

                $report_settings = DB::table('report_settings')
                        ->select('report_settings.*')
                        ->where('status', '=', '1')
                        ->paginate($paginate);

                return view('masterresources/report_settings/result', array('report_settings' => $report_settings));
            }

            return view('masterresources/report_settings/index', array('report_settings' => $report_settings));
        
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/report_settings');
        }
    }

    public function add() {
        try {
            $arrReports = Config::get('app.REPORTS');

            return view('masterresources/report_settings/add', array("arrReports"=>$arrReports));

        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/report_settings');
        }
    }

    public function autocompleteemployee() {

        $searchkey = Input::get('searchkey');
        $data = array();
        if($searchkey != ''){
            $data = DB::table('employees')
                    ->select('username','first_name','id','email','contact_email')
                    ->where('status', '=', '1')
                    ->whereRaw("(username like '$searchkey%' OR first_name like '$searchkey%')")
                    ->get();
        }
        
        return \Response::json($data);
    }
    
    public function checksettingsexistornot() {

        $report_name = Input::get('report_name');
        $editid = Input::get('editid');
        $data = DB::table('report_settings')
                    ->select('report_settings.id')
                    ->where('status', '=', '1')
                    ->where('id', '!=', $editid)
                    ->where(['report_settings.report_name' => $report_name])
                    ->first();
        
        if(count($data)>0){
            return 1;
        }else{
            return 0;
        }
        
    }
    
    public function store() {
        try {

            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            
            $arrRecepients = Input::get('arrRecepients');
            $arrRecepients = json_decode($arrRecepients);
            
            $arrExempteds = Input::get('arrExempteds');
            $arrExempteds = json_decode($arrExempteds);
            
            $arrMailReceivers = array_map(create_function('$o', 'return $o->recipient_id;'), $arrRecepients);
            $strMailReceiver=implode(",", $arrMailReceivers);
            
            $strExemEmps='';
            if(count($arrExempteds)>0){
                $arrExemEmps = array_map(create_function('$o', 'return $o->exempted_id;'), $arrExempteds);
                $strExemEmps=implode(",", $arrExemEmps);
            }
            
            $settings = DB::table('report_settings')
                ->where(['report_name' => $arraData->report_name])
                ->update(['status' => 2]);
                  
            $function_name="";
            if($arraData->report_name=="No_Activity_Report"){
                $function_name="getNoAcivityReport";
            }else if($arraData->report_name=="Delayed_Activity_Report"){
                $function_name="getDelayedActivityReport";
            }
            
            $model = new Reportsettings();
            $model->report_name = $arraData->report_name;
            $model->function_name = $function_name;
            $model->type = $arraData->send_option;
            $model->day = $arraData->cmbday;
            $model->time = $arraData->sendtime;
            $model->send_to_emps = $strMailReceiver;
            $model->exempted_emps = $strExemEmps;
            $model->save();
                
            Toastr::success("Report Settings Saved Successfully!", $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }



    public function edit($id) {
        try {
            $dn = \Crypt::decrypt($id);

            $arrReports = Config::get('app.REPORTS');

            $report_settings = DB::table('report_settings')
                                ->select('report_settings.*')
                                ->where('status', '=', '1')
                                ->where(['report_settings.id' => $dn])
                                ->first();

            $arrMailRecevers=explode(",", $report_settings->send_to_emps);
            $arrExempteds=explode(",", $report_settings->exempted_emps);

            $arrMailRecipients = DB::table('employees')
                        ->select('username as recipient_code','first_name as recipient_name','id as recipient_id',db::raw("case when email='' then contact_email when email=null then contact_email else email end as recipient_email"))
                        ->where('status', '=', '1')
                        ->whereIn('id',$arrMailRecevers)
                        ->get();

            $arrExemptedEmps=array();
            $arrExemptedEmps = DB::table('employees')
                        ->select('username as exempted_code','first_name as exempted_name','id as exempted_id')
                        ->where('status', '=', '1')
                        ->whereIn('id',$arrExempteds)
                        ->get();

            return view('masterresources/report_settings/edit', array('report_settings' => $report_settings,'arrMailRecipients'=>$arrMailRecipients,'arrExemptedEmps'=>$arrExemptedEmps,'arrReports'=>$arrReports));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/report_settings');
        }
    }

     public function update() {
        try {

            $arraData = Input::get('arraData');
            $arraData = json_decode($arraData);
            
            $arrRecepients = Input::get('arrRecepients');
            $arrRecepients = json_decode($arrRecepients);
            
            $arrExempteds = Input::get('arrExempteds');
            $arrExempteds = json_decode($arrExempteds);
            
            $arrMailReceivers = array_map(create_function('$o', 'return $o->recipient_id;'), $arrRecepients);
            $strMailReceiver=implode(",", $arrMailReceivers);
            
            $strExemEmps='';
            if(count($arrExempteds)>0){
                $arrExemEmps = array_map(create_function('$o', 'return $o->exempted_id;'), $arrExempteds);
                $strExemEmps=implode(",", $arrExemEmps);
            }
                
            $function_name="";
            if($arraData->report_name=="No_Activity_Report"){
                $function_name="getNoAcivityReport";
            }else if($arraData->report_name=="Delayed_Activity_Report"){
                $function_name="getDelayedActivityReport";
            }
            
            $settings = DB::table('report_settings')
                            ->where(['report_name' => $arraData->report_name])
                            ->where('id' ,'!=', $arraData->editid)
                            ->update(['status' => 2]);
                        
            $settings = DB::table('report_settings')
                            ->where(['id' => $arraData->editid])
                            ->update([
                                'report_name' => $arraData->report_name,
                                'type' => $arraData->send_option,
                                'day' => $arraData->cmbday,
                                'time' => $arraData->sendtime,
                                'function_name' => $function_name,
                                'send_to_emps' => $strMailReceiver,
                                'exempted_emps' => $strExemEmps,
                            ]);
            
            Toastr::success("Report Settings Updated Successfully!", $title = null, $options = []);
            return 1;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('report_settings')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Report Settings Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/report_settings');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/report_settings');
        }
    }

}
