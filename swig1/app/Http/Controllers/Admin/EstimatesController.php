<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use DB;
use App\Model\EstimateForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Kamaln7\Toastr\Facades\Toastr;

class EstimatesController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin_auth');
        //write athenitcate function for admin
    }

    public function estimates_listing()
    {
        try {
            $paginate = 10;
            $estimates = DB::table('estimate_form')
                ->select('estimate_form.*')
                ->where(['estimate_form.status' => 1])
                ->orderby('estimate_form.created_at', 'DESC')->paginate($paginate);
            return view('admin.estimates.estimates-listing', array('estimates' => $estimates));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/dashboard');
        }
    }

    public function view_estimate($id)
    {
        try {

            if ($id != "") {
                $id = \Crypt::decrypt($id);

                $render_params = array();

                $estimate_details = DB::table('estimate_form')
                    ->select('estimate_form.*')
                    ->where(['estimate_form.id' => $id])
                    ->first();

                

            }
            return view('admin.estimates.estimate-detail-view', array('estimate_details' => $estimate_details));

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    public function remove_estimate(Request $request, $estimate_id)
    {
        try {
            $estimate_id = \Crypt::decrypt($estimate_id);
            if ($estimate_id != "") {
                $estimates_model = new EstimateForm();
                $estimates_model->exists = true;
                $estimates_model->id = $estimate_id;
                $estimates_model->status = 0;
                $estimates_model->save();
            }

            Toastr::success('Successfully Removed Estimates Details!', $title = null, $options = []);
            return Redirect::to('admin/estimates-listing');
        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/estimates-listing');
        }
    }

}
