<?php

namespace App\Http\Controllers\Crm;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use App\Masterresources;
use App\Models\Crm_feedback;
use DB;

class CrmfeedbackController extends Controller {

    public function index() {
        
    }

    public function add() {
        $branch = DB::table("master_resources")
                ->select("name", "id", "branch_code")
                ->whereraw("resource_type='BRANCH' AND status = '1'")
                ->get();
        return view("crm/crm_feedback/add", array("branch" => $branch));
    }

    public function store() {
        try {
            $data = Input::get();
            if (!empty($data)) {
                $Crm_feedback = new Crm_feedback();
                $Crm_feedback->customer_name = Input::get("customer_name");
                $Crm_feedback->mobile_number = Input::get("customer_mobile");
                $Crm_feedback->customer_comment = Input::get("customer_comment");
                $Crm_feedback->branch_id = Input::get("branch");
                $Crm_feedback->created_by = session("login_id");
                $Crm_feedback->status = 1;
                if ($Crm_feedback->save()) {
                    Toastr::success('Customer Feedback Saved Successfully', $title = null, $options = []);
                    return Redirect::to('crm/crm_feedback/add');
                } else {
                    Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                    return Redirect::to('crm/crm_feedback/add');
                }
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('crm/crm_feedback/add');
        }
    }

}
