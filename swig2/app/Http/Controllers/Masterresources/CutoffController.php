<?php

namespace App\Http\Controllers\Masterresources;

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

class CutoffController extends Controller {

    public function index(Request $request, $id = '') {



        return view('masterresources/cutoffdate/index', array());
    }

    public function store() {
        $cuttoffDate = array();


        $masterresourcemodel = new Masterresources();
        $masterresourcemodel->resource_type = 'CUT_OFF_DATE';
        $cutoffDate = Input::get('cutoffdate');

        $cutoffDate = explode('-', $cutoffDate);
        $cutoffDate = $cutoffDate[2] . '-' . $cutoffDate[1] . '-' . $cutoffDate[0];


        $masterresourcemodel->created_at = $cutoffDate;

        $masterresourcemodel->status = 1;

        $cutoff_dup_data = DB::table('master_resources')
                ->select('id', 'resource_type')
                ->where('resource_type', '=', 'CUT_OFF_DATE')
                ->where('status', '=', 1)
                ->first();

        if (count($cutoff_dup_data) > 0) {
            $cuttoffDate['created_at'] = $cutoffDate;

            $id = $cutoff_dup_data->id;
            DB::table('master_resources')
                    ->where('id', $id)
                    ->update($cuttoffDate);

            Toastr::success('Updated the Cut Off Date!', $title = null, $options = []);
            return Redirect::to('masterresources/cutoffdate');
        } else {
            $saved = $masterresourcemodel->save();
            if ($saved) {
                Toastr::success('Successfully Added!', $title = null, $options = []);
                return Redirect::to('masterresources/cutoffdate');
            }
        }
    }

   
}
