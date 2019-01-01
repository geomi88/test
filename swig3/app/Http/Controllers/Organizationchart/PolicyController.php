<?php

namespace App\Http\Controllers\Organizationchart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Policy;
use DB;
use App;
use Excel;

class PolicyController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $policies = DB::table('policy')
                ->select('policy.*', 'master_resources.name','master_resources.alias_name')
                ->leftjoin('master_resources', 'policy.policy_master_id', '=', 'master_resources.id')
                ->where('policy.status', '=', 1)
                ->orderby('policy.created_at', 'DESC')
                ->paginate($paginate);
        
        $policymaster = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'POLICY_MASTER', 'status' => 1])
                ->orderby('name', 'ASC')->get();
                
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $searchbymaster = Input::get('searchbymaster');

            $policies = DB::table('policy')
                    ->select('policy.*', 'master_resources.name','master_resources.alias_name')
                    ->leftjoin('master_resources', 'policy.policy_master_id', '=', 'master_resources.id')
                    ->where('policy.status', '=', 1)
                    ->orderby('policy.created_at', 'DESC')
                    ->when($searchbymaster, function ($query) use ($searchbymaster) {
                        return $query->whereRaw("(policy.policy_master_id=$searchbymaster)");
                    })
                    ->paginate($paginate);

            return view('organizationchart/policy/result', array('policies' => $policies));
        }
        return view('organizationchart/policy/index', array('policies' => $policies,'policymaster'=>$policymaster));
    }
    
    public function listindex(Request $request) {
        $paginate = Config::get('app.PAGINATE');

        $policies = DB::table('policy')
                ->select('policy.*', 'master_resources.name','master_resources.alias_name')
                ->leftjoin('master_resources', 'policy.policy_master_id', '=', 'master_resources.id')
                ->where('policy.status', '=', 1)
                ->orderby('policy.created_at', 'DESC')
                ->paginate($paginate);
        
        $policymaster = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'POLICY_MASTER', 'status' => 1])
                ->orderby('name', 'ASC')->get();
                
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $searchbymaster = Input::get('searchbymaster');

            $policies = DB::table('policy')
                    ->select('policy.*', 'master_resources.name','master_resources.alias_name')
                    ->leftjoin('master_resources', 'policy.policy_master_id', '=', 'master_resources.id')
                    ->where('policy.status', '=', 1)
                    ->orderby('policy.created_at', 'DESC')
                    ->when($searchbymaster, function ($query) use ($searchbymaster) {
                        return $query->whereRaw("(policy.policy_master_id=$searchbymaster)");
                    })
                    ->paginate($paginate);

            return view('organizationchart/policy_list/result', array('policies' => $policies));
        }
        return view('organizationchart/policy_list/index', array('policies' => $policies,'policymaster'=>$policymaster));
    }

    public function add() {
        try {
            
            $policymaster = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'POLICY_MASTER', 'status' => 1])
                ->orderby('name', 'ASC')->get();

            return view('organizationchart/policy/add', array('policymaster' => $policymaster));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('organizationchart/policy');
        }
    }

    public function store() {
        try {
            $input=Input::all();
           
            $objmodel = new Policy();
            $objmodel->policy_master_id = $input['policy_id'];
            $objmodel->content = $input['content'];
            $objmodel->status = 1;
            $objmodel->save();

            Toastr::success('Policy Saved Successfully', $title = null, $options = []);
            return Redirect::to('organizationchart/policy');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('organizationchart/policy');
        }
    }

    public function update() {
        try {
            $input=Input::all();
            
            $objmodel = new Policy();
            $objmodel->exists = true;
            $objmodel->id = $input['editid'];
            $objmodel->policy_master_id = $input['policy_id'];
            $objmodel->content = $input['content'];

            $objmodel->save();

            Toastr::success('Policy Updated Successfully', $title = null, $options = []);
            return Redirect::to('organizationchart/policy');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('organizationchart/policy');
        }
    }

    public function edit($id) {
        try {
            $policyid = \Crypt::decrypt($id);
            $policy = DB::table('policy')
                    ->where(['id' => $policyid])
                    ->first();

            $policymaster = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'POLICY_MASTER', 'status' => 1])
                ->orderby('name', 'ASC')->get();

            return view('organizationchart/policy/edit', array('policydata' => $policy, 'policymaster' => $policymaster));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('organizationchart/policy');
        }
    }
    
    public function view($id) {
        try {
            $policyid = \Crypt::decrypt($id);
            $policy = DB::table('policy')
                    ->where(['id' => $policyid])
                    ->first();

            $policymaster = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'POLICY_MASTER', 'status' => 1])
                ->orderby('name', 'ASC')->get();

            return view('organizationchart/policy_list/view', array('policydata' => $policy, 'policymaster' => $policymaster));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('organizationchart/policy');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('policy')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);

            Toastr::success('Policy Deleted Successfully!', $title = null, $options = []);
            return Redirect::to('organizationchart/policy');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('organizationchart/policy');
        }
    }

    // Generate PDF funcion
    public function exportdata() {
        $excelorpdf = Input::get('excelorpdf');
        $searchbymaster = Input::get('searchbymaster');

        $policies = DB::table('policy')
                ->select('policy.*', 'master_resources.name','master_resources.alias_name')
                ->leftjoin('master_resources', 'policy.policy_master_id', '=', 'master_resources.id')
                ->where('policy.status', '=', 1)
                ->orderby('policy.created_at', 'DESC')
                ->when($searchbymaster, function ($query) use ($searchbymaster) {
                    return $query->whereRaw("(policy.policy_master_id=$searchbymaster)");
                })
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('policyList', function($excel) use($policies) {
                // Set the title
                $excel->setTitle('Policy List');

                $excel->sheet('Policy List', function($sheet) use($policies) {
                    // Sheet manipulation

                    $sheet->setCellValue('B3', 'Policy List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Policy Master', 'Content', 'Created Date'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($policies); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $policies[$i]->name);
                        $sheet->setCellValue('C' . $chrRow, strip_tags($policies[$i]->content));
                        $sheet->setCellValue('D' . $chrRow, date("d-m-Y", strtotime($policies[$i]->created_at)));

                        $sheet->cells('A' . $chrRow . ':D' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        }
    }

}
