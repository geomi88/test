<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Usermodule extends Model
{
    protected $table = 'user_modules';
    protected $fillable = ['module_id', 'employee_id','filter_by_job_position'];
    
    public function getjobpositionsettings($employee_id,$module)
    {
        $module_permission = DB::table('user_modules')
                ->select('user_modules.*')
                ->leftjoin('modules','user_modules.module_id','=','modules.id')
                ->whereRaw("user_modules.employee_id=$employee_id AND modules.name='$module'")
                ->first();

        $arrJobPosSettings=array();
        if(count($module_permission)>0){
            $jobpositionsettings=$module_permission->filter_by_job_position;
            if($jobpositionsettings){
                $arrJobPosSettings=explode(",", $jobpositionsettings);
            }
        }
        
        return $arrJobPosSettings;
    }
}
