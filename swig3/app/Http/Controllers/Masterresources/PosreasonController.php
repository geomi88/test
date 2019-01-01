<?php

namespace App\Http\Controllers\Masterresources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use App\Models\Company;
use DB;
class PosreasonController extends Controller {
    
    public function index() 
        {
          if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
            $paginate=Config::get('app.PAGINATE');
            $poss = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'POS_REASON')->where('status', '!=', 2)->where('company_id', '=', $company_id)
                ->paginate($paginate);
                 return view('masterresources/pos_reasons/index', array('poss' => $poss)); 
        }
    
    public function add() 
        {
            return view('masterresources/pos_reasons/add');
        }
        
         public function checkpos() {

        $name = Input::get('name');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.name')
                ->where('name', '=', $name)
                ->where('resource_type', '=', 'POS_REASON')
                ->where('id', '!=', $id)
                ->where('status', '!=', 2)
                ->get();
        if (count($data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function edit($id) {
        
        $dn = \Crypt::decrypt($id);

        $posreasons = DB::table('master_resources')
                ->where(['id' => $dn])
                ->first();

        return view('masterresources/pos_reasons/edit', array('posreasons' => $posreasons));
    }

    
    public function store() {
       
             if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
            $masterresourcemodel = new Masterresources;
            $masterresourcemodel->name = Input::get('name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->resource_type = 'POS_REASON';
            $masterresourcemodel->company_id = $company_id;
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();
            Toastr::success('POS Reason Successfully Added!', $title = null, $options = []);
            return Redirect::to('masterresources/pos_reasons');
        
    }
    
    public function update() {

        try {
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');

            $posreasons = DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $name, 'alias_name' => $alias_name]);

            Toastr::success('POS Reason Successfully Updated!', $title = null, $options = []);
            return Redirect::to('masterresources/pos_reasons');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/pos_reasons/edit/'.$dn);
        }
    }

    public function Disable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies= DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 0]);
                Toastr::success('POS Reason Successfully Disabled', $title = null, $options = []);
                return Redirect::to('masterresources/pos_reasons');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/pos_reasons');
            
           }
        } 
       public function Enable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 1]);
                Toastr::success('POS Reason Successfully Enabled', $title = null, $options = []);
                return Redirect::to('masterresources/pos_reasons');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/pos_reasons');
            
           }
        }
    
    public function delete($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('master_resources')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 2]);
                Toastr::success('POS Reason Successfully Deleted', $title = null, $options = []);
                return Redirect::to('masterresources/pos_reasons');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/pos_reasons');
            
           }
        }
     
        
}