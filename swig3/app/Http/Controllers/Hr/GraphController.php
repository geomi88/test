<?php

namespace App\Http\Controllers\Hr;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Module;
use App\Models\Usermodule;
use App\Models\Country;
use App\Models\Document;
use DB;
use Mail;

class GraphController extends Controller {

    public function getcountrywisegraph() {

        $employees = DB::table('employees')
                ->select('employees.nationality','country.name as country_name',db::raw("count(*) as empcount"))
                ->leftjoin('country', 'country.id', '=', 'employees.nationality')
                ->where('employees.status', '!=', 2)
                ->groupby('employees.nationality')
                ->orderby('empcount', 'DESC')
                ->get();
        
        $totalcount=$employees->sum("empcount");
        
        $arrcoutries = array();
        $arrcount = array();
        $public_path= url('/');
        foreach ($employees as $employe) {
            $arrcoutries[]=$employe->country_name;
            $Id=\Crypt::encrypt($employe->nationality);
            $arrcount[]=array('y'=>$employe->empcount,'url'=>$public_path.'/employeewithcountry/'.$Id);
        }
        
        $countrycount=count($arrcoutries);
        $countrycount=$countrycount*45;
        
        $arrcoutries = array_values($arrcoutries);
        $arrcoutries = json_encode($arrcoutries);
        
        $arrcount = array_values($arrcount);
        $arrcount = json_encode($arrcount);
        
        return view('hr/employeebycountry', array('arrcoutries' => $arrcoutries,'arrcount'=>$arrcount,'countrycount'=>$countrycount,'totalcount'=>$totalcount));
    }
    
    public function getjobwisegraph() {
        
        $job_positions = DB::table('employees')
                ->select('employees.job_position as job_id','master_resources.name as job_name',db::raw("count(*) as empcount"))
                ->leftjoin('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('employees.status', '!=', 2)
//                ->where('master_resources.status', '!=', 2)
                ->groupby('employees.job_position')
                ->orderby('empcount', 'DESC')
                ->get();
        
        $totalcount=$job_positions->sum("empcount");
         
        $arrjob = array();
        $arrcount = array();
        $public_path= url('/');
        foreach ($job_positions as $job) {
            $arrjob[]=  str_replace('_', ' ', $job->job_name);
            $Id=\Crypt::encrypt($job->job_id);
            $arrcount[]=array('y'=>$job->empcount,'url'=>$public_path.'/employeewithjobposition/'.$Id);
        }
        
        $jobcount=count($arrjob);
        $jobcount=$jobcount*45;
        
        $arrjob = array_values($arrjob);
        $arrjob = json_encode($arrjob);
        
        $arrcount = array_values($arrcount);
        $arrcount = json_encode($arrcount);

        return view('hr/employeebyjob', array('arrjob' => $arrjob,'arrcount'=>$arrcount,'jobcount'=>$jobcount,'totalcount'=>$totalcount));
    }
 
}
