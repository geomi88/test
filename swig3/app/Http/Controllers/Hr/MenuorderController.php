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
use App\Models\Usermodule;
use DB;

class MenuorderController extends Controller {

    public function index(Request $request) {
            $modules = DB::table('modules')
                    ->select('modules.*')
                    ->where(['parent_id' => 0])
                    ->orderby('modules.menu_order','ASC')
                    ->get();
            
        return view('hr/menu_order', array( 'modules' => $modules));
    }

    public function change_priority() {
       
        $arrNewTaskPy=Input::get('arrModulePys');
        
        $arrModulePys=json_decode($arrNewTaskPy);
        
        foreach ($arrModulePys as $module) {
            $modules = DB::table('modules')
               ->where(['id' => $module->moduleid])
               ->update(['menu_order' => $module->moduleindex]);
        }
        
        Toastr::success('Menu Order Saved Successfully!', $title = null, $options = []);
        return 1;
    }
    
}
