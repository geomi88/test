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
use App\Models\Company;
use App\Models\Masterresources;
use DB;
use App;
use PDF;
use Excel;

class PolicymasterController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $policymaster = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('master_resources.resource_type', '=', 'POLICY_MASTER')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->orderby('master_resources.name', 'ASC')
                ->paginate($paginate);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $searchbyname = Input::get('searchbyname');
            

            $policymaster = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'POLICY_MASTER')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $company_id)
                    ->orderby('master_resources.name', 'ASC')
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->paginate($paginate);

            return view('masterresources/policy_master/result', array('policymaster' => $policymaster));
        }

        return view('masterresources/policy_master/index', array('policymaster' => $policymaster));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        return view('masterresources/policy_master/add', array());
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $masterresourcemodel = new Masterresources;
            $masterresourcemodel->name = Input::get('name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->resource_type = 'POLICY_MASTER';
            $masterresourcemodel->company_id = $company_id;
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();

            Toastr::success('Policy Master Added Successfully!', $title = null, $options = []);
            return Redirect::to('masterresources/policy_master');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/policy_master/add');
        }
    }

    public function checknameunique() {
        $name = Input::get('name');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.name')
                ->where('name', '=', $name)
                ->where('resource_type', '=', 'POLICY_MASTER')
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
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $policydata = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['master_resources.id' => $dn])
                ->first();

        return view('masterresources/policy_master/edit', array('policydata' => $policydata));
    }

    public function update() {
        try {
            $masterresourcemodel = new Masterresources;
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');

            DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $name,'alias_name' => $alias_name]);

            Toastr::success('Policy Master Updated Successfully!', $title = null, $options = []);
            return Redirect::to('masterresources/policy_master');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/policy_master/edit/' . $dn);
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Policy Master Deleted Successfully!', $title = null, $options = []);
            return Redirect::to('masterresources/policy_master');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/policy_master');
        }
    }

}
