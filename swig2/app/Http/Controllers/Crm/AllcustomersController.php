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

class AllcustomersController extends Controller {

    public function index(Request $Request) {
        try {
            if ($Request->ajax()) {

                $paginate = Input::get("pagelimit");
                if (empty($paginate)) {
                    $paginate = Config::get("app.PAGINATE");
                }
                $searchbycustomername = Input::get("searchbycustomername");
                $searchbymobile = Input::get("searchbymobile");
                $datefrom = Input::get("datefrom");
                $dateto = Input::get("dateto");
                if ($datefrom != '') {
                    $datefrom = explode('-', $datefrom);
                    $datefrom = $datefrom[2] . '-' . $datefrom[1] . '-' . $datefrom[0];
                }

                if ($dateto != '') {
                    $dateto = explode('-', $dateto);
                    $dateto = $dateto[2] . '-' . $dateto[1] . '-' . $dateto[0];
                }
                $declarations = DB::table("crm_customers as a")
                        ->select(DB::raw("a.id,a.name,a.mobile_number,a.created_at,"
                                        . "(select b.name "
                                        . "FROM master_resources b where b.id = a.branch_id) as branch_name,"
                                        . "(select c.first_name FROM employees c where c.id = a.created_by) as cashier_name"),
                                DB::raw("count(id) as repeat_count"),"a.branch_id","a.created_by",
                                DB::raw("(select b.name FROM crm_customers b where b.mobile_number = a.mobile_number AND b.branch_id = a.branch_id AND b.created_by = a.created_by ORDER BY b.id DESC LIMIT 1) as cus_name"))
                        ->where("a.status", "!=", "2")
                        ->when($searchbycustomername, function($qry) use($searchbycustomername) {
                            return $qry->whereraw("(a.name like '%$searchbycustomername%')");
                        })
                        ->when($searchbymobile, function($qry) use($searchbymobile) {
                            return $qry->whereraw("(a.mobile_number like '%$searchbymobile%')");
                        })
                        ->when($datefrom, function($qry) use($datefrom) {
                            return $qry->whereraw("(date(a.created_at) >= '$datefrom')");
                        })
                        ->when($dateto, function($qry) use($dateto) {
                            return $qry->whereraw("(date(a.created_at) <= '$dateto')");
                        })
                        ->orderby("a.id", "Desc")
                        ->groupBy("a.mobile_number","a.branch_id","a.created_by")
                        ->paginate($paginate);
                return view("crm/all_customers/result", array("declarations" => $declarations));
            } else {
                $paginate = Config::get("app.PAGINATE");
                $declarations = DB::table("crm_customers as a")
                        ->select(DB::raw("a.id,a.name,a.mobile_number,a.created_at,"
                                        . "(select b.name "
                                        . "FROM master_resources b where b.id = a.branch_id) as branch_name,"
                                        . "(select c.first_name FROM employees c where c.id = a.created_by) as cashier_name"),
                                DB::raw("count(id) as repeat_count"),"a.branch_id","a.created_by",
                                DB::raw("(select b.name FROM crm_customers b where b.mobile_number = a.mobile_number AND b.branch_id = a.branch_id AND b.created_by = a.created_by ORDER BY b.id DESC LIMIT 1) as cus_name"))
                        ->where("a.status", "!=", "2")
                        ->orderby("a.id", "Desc")
                        ->groupBy("a.mobile_number","a.branch_id","a.created_by")
                        ->paginate($paginate);
                return view("crm/all_customers/index", array("declarations" => $declarations));
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('crm/all_customers');
        }
    }
    
    public function view($mobile,$branch_id,$created){
        $phone_number = \Crypt::decrypt($mobile);
        $branch = \Crypt::decrypt($branch_id);
        $created_by = \Crypt::decrypt($created);
        $customer_data = DB::table("crm_customers as a")
                ->select(DB::raw("a.id,a.mobile_number,a.created_at,"
                                        . "(select b.name "
                                        . "FROM master_resources b where b.id = a.branch_id) as branch_name,"
                                        . "(select c.first_name FROM employees c where c.id = a.created_by) as cashier_name"),
                                DB::raw("count(id) as repeat_count"),"a.branch_id","a.created_by",
                                DB::raw("(select b.name FROM crm_customers b where b.mobile_number = a.mobile_number AND b.branch_id = a.branch_id AND b.created_by = a.created_by ORDER BY b.id DESC LIMIT 1) as cus_name"))
                ->where("a.mobile_number","=",$phone_number)
                ->where("a.branch_id","=",$branch)
                ->where("a.created_by","=",$created_by)
                ->orderBy("a.id","DESC")
                ->first();
        return view("crm/all_customers/view",[
            "customer_data"=>$customer_data,
            "mobile"=>$mobile,
            "branch_id"=>$branch_id,
            "created"=>$created
        ]);
    }
    
    public function print_data(){
        try{
            $html = '';
            $phone_number = \Crypt::decrypt(Input::get("mobile"));
            $branch = \Crypt::decrypt(Input::get("branch_id"));
            $created_by = \Crypt::decrypt(Input::get("created"));
            $customer_data = DB::table("crm_customers as a")
                    ->select(DB::raw("a.id,a.mobile_number,a.created_at,"
                                            . "(select b.name "
                                            . "FROM master_resources b where b.id = a.branch_id) as branch_name,"
                                            . "(select c.first_name FROM employees c where c.id = a.created_by) as cashier_name"),
                                    DB::raw("count(id) as repeat_count"),"a.branch_id","a.created_by",
                                    DB::raw("(select b.name FROM crm_customers b where b.mobile_number = a.mobile_number AND b.branch_id = a.branch_id AND b.created_by = a.created_by ORDER BY b.id DESC LIMIT 1) as cus_name"))
                    ->where("a.mobile_number","=",$phone_number)
                    ->where("a.branch_id","=",$branch)
                    ->where("a.created_by","=",$created_by)
                    ->orderBy("a.id","DESC")
                    ->first();
            $html = '<!DOCTYPE html>
                        <html>
                            <head>
                                <title>All Customers</title>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            </head>
                            <body>
                                <table cellpadding="0" cellspacing="0" border="0"  style=" table-layout: fixed;  
                                 width:100%; color:#454446; font-size:16px; line-height:22px;
                                     background: #fff; padding: 32px; font-family:open_sansregular; font-size: 12px;  
                                     border:3px solid #760000; padding: 8px;">
                                     <tbody>
                                        <tr>
                                            <td valign="top">
                                                <table>
                                                    <tr>
                                                        <td style="vertical-align: top; box-sizing:border-box; border:0">
                                                            <h2 style="color: #760000; font-size: 25px; text-align: center; font-weight: bold; display: block; margin: 15px; ">Customer Detail</h2>
                                                        </td>
                                                        <td style="vertical-align: top; border:0; text-align: right; margin-top: -33px; margin-right: 14px; width: 4%;">
                                                            <img src="'. \url('images/imgImtiyazatLogo.png') .'" style="width: 105px">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td cellpadding="3" cellspacing="3" style=" display: block;">
                                                <table  cellpadding="3" width="100%" height="100%"  cellspacing="0" style="font-size:15px; border: 4px solid #e7e7e7;">
                                                    <tr>
                                                        <td style="border-right: 1px solid #e7e7e7; text-align: center;; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7; width: 25%;">
                                                            <p style="font-weight: bold ; margin: 0">Time & Date </p>
                                                            <p style="margin: 0;">'. date("d-m-Y H:i", strtotime($customer_data->created_at)) .'</p>
                                                        </td>
                                                        <td style="border-right: 1px solid #e7e7e7; text-align: center;%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;  width: 25%;">
                                                            <p style="font-weight: bold ; margin: 0"> Customer Name</p>
                                                            <p style="margin: 0;">'. $customer_data->cus_name .'</p>
                                                        </td>
                                                        <td style="border-right: 1px solid #e7e7e7; text-align: center;%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;  width: 25%;">
                                                            <p style="font-weight: bold ; margin: 0">Mobile Number</p>
                                                            <p style="margin: 0;">'. $customer_data->mobile_number .'</p>
                                                        </td>
                                                        <td style="border-right: 1px solid #e7e7e7; text-align: center;%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;">
                                                            <p style="font-weight: bold; margin: 0"> Repeat</p>
                                                            <p style="margin: 0; ">'. $customer_data->repeat_count .'</p>
                                                        </td>

                                                    </tr>
                                                    <tr>
                                                        <td style="border-right: 1px solid #e7e7e7; text-align: center;%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;  width: 25%;">
                                                            <p style="font-weight: bold ; margin: 0">Branch </p>
                                                            <p style="margin: 0; ">'. $customer_data->branch_name .'</p>
                                                        </td>
                                                        <td colspan="2" style="border-right: 1px solid #e7e7e7; text-align: center; width: 25%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;">
                                                            <p style="font-weight: bold; margin: 0"> Created By </p>
                                                            <p style="margin: 0; ">'. $customer_data->cashier_name .'</p>
                                                        </td>
                                                        <td colspan="2" style="border-right: 1px solid #e7e7e7; text-align: center; width: 25%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;">
                                                            <p style="font-weight: bold; margin: 0"></p>
                                                            <p style="margin: 0; "></p>
                                                        </td>                            
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </body>
                        </html>';
            return $html;
        }
        catch (\Exception $e){
            return -1;
        }
    }

}
