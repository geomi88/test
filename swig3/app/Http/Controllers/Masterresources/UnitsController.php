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
use App\Models\Company;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use App\Models\Unit;
use DB;

class UnitsController extends Controller {

    public function index() {
        if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
            
        $paginate=Config::get('app.PAGINATE');
       if (Session::get('unit'))
            { 
            $unitses = Session::get('unit');  
            }
            else
            {
              $unitses = 'SIMPLE';  
            }
            $data['sess']=$unitses;
        ////////////////simple units////////////////////////
        $simple_units = DB::table('units')
                ->select('units.*')
                ->where('unit_type', '=', 'SIMPLE')->where('status', '!=', 2)->where('company_id', '=', $company_id)->orderby('name', 'ASC')
                ->paginate($paginate);
        //////////////////////////////////Compound units////////////////////
        
                $compound_units = DB::table('units')
                ->select('units.*')
                ->where('unit_type', '=', 'COMPOUND')->where('status', '!=', 2)->where('company_id', '=', $company_id)->orderby('name', 'ASC')
                ->get();
                $units =array();
                $units_from_array = array();
                $units_to_array = array();
                foreach($compound_units as $compound_unit){
                $unitfroms = DB::table('units')
                            ->select('units.*')->where('company_id', '=', $company_id)
                            ->where(['id' => $compound_unit->from])->first();
                $unit_row['id'] = $compound_unit->id;
                $unit_row['status'] = $compound_unit->status;
                $unit_row['name'] = $unitfroms->name;
                
                $unittos = DB::table('units')
                            ->select('units.*')->where('company_id', '=', $company_id)
                            ->where(['id' => $compound_unit->to])->first();
                $unit_row['to_name'] = $unittos->name;
                $unit_row['conversion_value'] = $compound_unit->conversion_value;
                array_push($units,$unit_row);
                }
           //print_r($unit_row);die('asd');
        
                
        return view('masterresources/units/index', array('simple_units' => $simple_units , 'units' => $units  ,'compound_units' => $compound_units,'data'=>$data));
    }

    public function add() {
         if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
        $all_units = DB::table('units')
                ->select('units.*')->where('company_id', '=', $company_id)
                ->where('status', '!=', 2)->orderby('name', 'ASC')
                ->get();
        $simple_units = DB::table('units')
                ->select('units.*')->where('company_id', '=', $company_id)
                ->where('unit_type', '=', 'SIMPLE')->where('status', '!=', 2)->orderby('name', 'ASC')
                ->get();
        $coumpound_units = DB::table('units')
                ->select('units.*')->where('company_id', '=', $company_id)
                ->where('unit_type', '=', 'COMPOUND')->where('status', '!=', 2)->orderby('name', 'ASC')
                ->get();
        return view('masterresources/units/add', array('all_units' => $all_units, 'simple_units' => $simple_units, 'coumpound_units' => $coumpound_units));
    }

    public function store() {
        try {
            $unitsmodel = new Unit;
            $unitsmodel->name = Input::get('name');
            $unitsmodel->formal_name = Input::get('formal_name');
            $unitsmodel->unit_type = Input::get('unit');
            if (Input::get('decimal_value') != '') {
            $unitsmodel->decimal_value = Input::get('decimal_value');
            }
            //print_r($unit);die('fsdfa');
            $unitsmodel->company_id = Session::get('company');
            if (Input::get('from') != '') {
                $unitsmodel->from = Input::get('from');
            }
            if (Input::get('to') != '') {
                $unitsmodel->to = Input::get('to');
            }
            if (Input::get('conversion_value') != '') {
                $unitsmodel->conversion_value = Input::get('conversion_value');
            }
            $unitsmodel->status = 1;
            $unitsmodel->save();
            Toastr::success('Successfully Added!', $title = null, $options = []);
            return Redirect::to('masterresources/units');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/units/add');
        }
    }

    public function checkunits() {
         if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
        $name = Input::get('name');
        $id = Input::get('cid');
        $data = DB::table('units')
                ->select('units.name')
                ->where('name', '=', $name)
                ->where('status', '=', 1)
                ->where('id', '!=', $id)
                ->get();
         if(count($data)==0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function edit($id) {
         if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
            $dn=\Crypt::decrypt($id);
         if (Session::get('unit'))
            { 
            $unitses = Session::get('unit');  
            }
            $data['sess']=$unitses;
            $all_units = DB::table('units')
                ->select('units.*')
                ->where('status', '!=', 2)->where('id', '=', $dn)->where('company_id', '=', $company_id)
                ->get();
            $simple_units = DB::table('units')
                ->select('units.*')->where('company_id', '=', $company_id)
                ->where('unit_type', '=', 'SIMPLE')->where('status', '!=', 2)->orderby('name', 'ASC')
                ->get();
            $coumpound_units = DB::table('units')
                ->select('units.*')->where('company_id', '=', $company_id)
                ->where('unit_type', '=', 'COMPOUND')->where('status', '!=', 2)->where('id', '=', $dn)
                ->get();
            return view('masterresources/units/edit', array('all_units' => $all_units, 'simple_units' => $simple_units, 'coumpound_units' => $coumpound_units,'data'=>$data));
    }

    public function cupdate() {
         
            $id = Input::get('ccid');            
            //$data['sess']=$unitses;
            $unitsmodel = new Unit;
            $formal_name = Input::get('formal_name');
            if (Input::get('from') != '') {
                $from = Input::get('from');
            }
            if (Input::get('to') != '') {
                $to = Input::get('to');
            }
            if (Input::get('conversion_value') != '') {
                $conversion_value = Input::get('conversion_value');
            }
            $units = DB::table('units')
                                ->where(['id' => $id])                            
                                ->update(['from' => $from,'to'=>$to,'conversion_value'=>$conversion_value]);               
            Toastr::success('Successfully Updated!', $title = null, $options = []);
            return Redirect::to('masterresources/units');
        
    }

public function supdate() {
        
            $id = Input::get('cid');
            $unitsmodel = new Unit;
            $formal_name = Input::get('formal_name');
            $name=Input::get('name');
            if (Input::get('decimal_value') != '') {
             $decimal_value = Input::get('decimal_value');
            }
            $units = DB::table('units')
                                ->where(['id' => $id])                            
                                ->update(['name' => $name,'formal_name'=>$formal_name,'decimal_value'=>$decimal_value]);               
            Toastr::success('Successfully Updated!', $title = null, $options = []);
            return Redirect::to('masterresources/units');
        
    }

    
       public function Disable($id)
        {
           try
           {
                
                $dn=\Crypt::decrypt($id);
               // $data['sess']=$unittype;
                $companies= DB::table('units')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 0]);
                Toastr::success('Unit Successfully Disabled', $title = null, $options = []);
                return Redirect::to('masterresources/units');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/units');
            
           }
        } 
       public function Enable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
               // $data['sess']=$unittype;
                $companies = DB::table('units')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 1]);
                Toastr::success('Unit Successfully Enabled', $title = null, $options = []);
                return Redirect::to('masterresources/units');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/units');
            
           }
        }
    
    public function delete($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
              //  $data['sess']=$unittype;
                $companies = DB::table('units')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 2]);
                Toastr::success('Unit Successfully Deleted', $title = null, $options = []);
                return Redirect::to('masterresources/units');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/units');
            
           }
        }
        
    public function setsession() {
        $sessval = Input::get('unit_type');
        Session::set('unit', $sessval);
         $unitses = Session::get('unit');  
       return $unitses;
         //return view('login/index', array('title' => '', 'description' => '', 'page' => '','companies' => $companies));
    }
     
       
}
