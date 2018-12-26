<?php

namespace App\Http\Controllers\Elegantclub;
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
use App\Models\Company;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Usermodule;
use DB;

class ElegantClubController extends Controller {

    public function index() {
        Session::forget('elegant_customer_id');
        $employee_details = Employee::where('id', Session::get('login_id'))->first();
        
        $loggedin_details = DB::table('employees')
                ->select('employees.*','master_resources.name as job_position')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('employees.id', '=', Session::get('login_id'))
                ->first();
        $user_sub_modules = Usermodule::where('employee_id', Session::get('login_id'))->join('modules', 'modules.id', '=', 'user_modules.module_id')->whereRaw("modules.parent_id in (select id from modules where name='Elegant Club')")->get();
        
        return view('elegantclub/index', array('employee_details' => $employee_details,'loggedin_details' => $loggedin_details, 'user_sub_modules' => $user_sub_modules));
    }
    
    public function addCorporateCustomer(){
        if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $loggedin_employee_details = DB::table('employees')
                ->select('employees.*', 'master_resources.name')
                ->join('master_resources', 'master_resources.id', '=', 'employees.job_position')
                ->where('employees.id', '=', $login_id)
                ->first();
        $groups = DB::table('ac_ledger_group')
            ->select('ac_ledger_group.*')
            ->whereRaw("ac_ledger_group.parent_id!=0")
            ->where('status', '!=', 2)
            ->get();
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        $maxid = DB::table('customers')->max('id') + 1; 
        if($maxid < 10)
            $customer_code = "ELC-0" . $maxid;
        else
            $customer_code = "ELC-" . $maxid;
        
        return view('elegantclub/add',array('parentgroups'=>$groups, 'countries' => $countries, 'customer_code' => $customer_code, 'loggedin_employee_details' => $loggedin_employee_details));
    }
    
    public function store(){
        try {
            $customer_data = Input::all();  
            $customer_model = new Customer;
            if($customer_data['customer_id']){
                $customer_model->exists = TRUE;
                $customer_model->id = $customer_data['customer_id'];
            }if (Session::get('elegant_customer_id')) { 
                $customer_model->exists = TRUE;
                $customer_model->id = Session::get('elegant_customer_id');
            } else { 
                $customer_model->account_group = $customer_data['account_group'];
                $customer_model->customer_code = $customer_data['customer_code'];
                $customer_model->name_english = $customer_data['name_english'];
                $customer_model->name_arabic = $customer_data['name_arabic'];
                $customer_model->customer_type = $customer_data['customer_type'];
                $customer_model->nationality = $customer_data['nationality'];
                $customer_model->cr_number = $customer_data['cr_number'];
                $customer_model->vat_number = $customer_data['vat_number'];
                $customer_model->po_box = $customer_data['po_box'];
                $customer_model->building_name = $customer_data['building_name'];
                $customer_model->street_name = $customer_data['street_name'];
                $customer_model->city = $customer_data['city'];
                $customer_model->detail_address = $customer_data['detail_address'];
                $customer_model->latitude = $customer_data['latitude'];
                $customer_model->longitude = $customer_data['longitude'];
                $customer_model->website = $customer_data['website'];
                $customer_model->business_type = $customer_data['business_type'];
                $customer_model->nature_of_current_business = $customer_data['nature_of_current_business'];
                $customer_model->contact_person = $customer_data['contact_person'];
                $customer_model->mobile_1 = $customer_data['mobile_1'];
                $customer_model->mobile_2 = $customer_data['mobile_2'];
                $customer_model->land_phone = $customer_data['land_phone'];
                $customer_model->nationality_contact_person = $customer_data['nationality_contact_person'];
                $customer_model->email_1 = $customer_data['email_1'];
                $customer_model->email_2 = $customer_data['email_2'];
                $customer_model->job_position = $customer_data['job_position'];
                $customer_model->id_number = $customer_data['id_number'];
                $customer_model->busines_target_per_month = $customer_data['busines_target_per_month'];
                $customer_model->interested_products = $customer_data['interested_products'];
                $customer_model->comments_from_customer = $customer_data['comments_from_customer'];
                $customer_model->comments_about_customer = $customer_data['comments_about_customer'];
                $customer_model->created_by = $customer_data['created_by_id'];
                $result = $customer_model->save();
            }
            if($result){ 
                Session::set('elegant_customer_id', $customer_model->id);
                return 1;
            }else{
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return -1;
            }
        } catch (\Exception $e) { 
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function upload_documents(){
        try { 
            $customermodel = new Customer;
            $data = Input::get('upload_customer_id');
            if (Session::get('elegant_customer_id')) {
                $customer_id = Session::get('elegant_customer_id');
                $customer = DB::table('customers')
                        ->select('customers.customer_code as code')
                        ->whereRaw("id=$customer_id")
                        ->first();
                $customermodel->exists = TRUE;
                $customermodel->id = Session::get('elegant_customer_id');
            
            
                $subfolder = $customer->code;
                $s3 = \Storage::disk('s3');
                $world = Config::get('app.WORLD');
                $web_url = $_ENV['WEB_URL'];
                
                $vat=''; $company =''; $vendor='';
                //////////////////////// vat_certificate //////////////////
                if (Input::hasFile('vat_certificate')) {
                    $vat_certificate = Input::file('vat_certificate');
                    $extension = time() . '.' . $vat_certificate->getClientOriginalExtension();
                    $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/VAT_CERTIFICATE';
                    $filePath = $filePath . '/' . $extension;
                    $s3filepath = $s3->put($filePath, file_get_contents($vat_certificate), 'public');
                    $vat = Storage::disk('s3')->url($filePath);

                    
                }

                //////////////////////// company_profile //////////////////
                if (Input::hasFile('company_profile')) {
                    $company_profile = Input::file('company_profile');
                    $extension = time() . '.' . $company_profile->getClientOriginalExtension();
                    $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/COMPANY_PROFILE';
                    $filePath = $filePath . '/' . $extension;
                    $s3filepath = $s3->put($filePath, file_get_contents($company_profile), 'public');
                    $company = Storage::disk('s3')->url($filePath);

                    
                }

                //////////////////////// vendor_contract //////////////////
                if (Input::hasFile('vendor_contract_copy')) {
                    $vendor_contract = Input::file('vendor_contract_copy');
                    $extension = time() . '.' . $vendor_contract->getClientOriginalExtension();
                    $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/VENDOR_CONTRACT';
                    $filePath = $filePath . '/' . $extension;
                    $s3filepath = $s3->put($filePath, file_get_contents($company_profile), 'public');
                    $vendor = Storage::disk('s3')->url($filePath);

                    
                }
                $customermodel->vat_certificate = $vat;
                $customermodel->company_profile = $company;
                $customermodel->vendor_contract_copy = $vendor;
                $save = $customermodel->save();
                Session::forget('elegant_customer_id');
                if($save){
                    if($data == ''){
                        Toastr::success('Customer Successfully Created!', $title = null, $options = []);
                    }else{
                        Toastr::success('Customer Successfully Updated!', $title = null, $options = []);
                    }
                }else{
                    Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
                }
                return Redirect::to('elegantclub');
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub');
            
        }
    }
    
    public function listcustomers(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $elegantcustomers = DB::table('customers')
                ->select('customers.*','country.name as country_name','employees.username as empCode','employees.first_name as empName')
                ->leftjoin('country', 'country.id', '=', 'customers.nationality')
                ->leftjoin('employees', 'employees.id', '=', 'customers.created_by')
                ->where('customers.status', '!=', 2)
                ->orderby('customers.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_name = Input::get('search_name');
            $searchbyph = Input::get('searchbyph');
            $searchbycode = Input::get('searchbycode');
            $searchbyempcode = Input::get('searchbyempcode');
            $searchbyemail = Input::get('searchbyemail');
            $country = Input::get('country');
            $status = Input::get('status');
            
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortordcountry = Input::get('sortordcountry');
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortordcode=='' && $sortordcountry==''){
                $sortOrdDefault='DESC';
            }
            
            $elegantcustomers = DB::table('customers')
                    ->select('customers.*','country.name as country_name','employees.username as empCode','employees.first_name as empName')
                    ->leftjoin('country', 'country.id', '=', 'customers.nationality')
                    ->leftjoin('employees', 'employees.id', '=', 'customers.created_by')
                    ->where('customers.status', '!=', 2)
                    ->when($search_name, function ($query) use ($search_name) {
                        return $query->whereRaw("customers.name_english like '%$search_name%'");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("customers.customer_code like '%$searchbycode%'");
                    })
                    ->when($searchbyempcode, function ($query) use ($searchbyempcode) {
                        return $query->whereRaw("employees.username like '%$searchbyempcode%'");
                    })
                    ->when($country, function ($query) use ($country) {
                        return $query->whereRaw("customers.city like '%$country%'");
                    })
                     ->when($searchbyemail, function ($query) use ($searchbyemail) {
                        return $query->whereRaw("customers.email_1 like '%$searchbyemail%'");
                    })
                     ->when($searchbyph, function ($query) use ($searchbyph) {
                         if($searchbyph==';'){ $searchbyph=0;}
                        return $query->whereRaw("customers.mobile_1 like '%$searchbyph%'");
                    })
                    ->when($status, function ($query) use ($status) {
                        if($status==-1){
                            $status=0;
                        }
                        return $query->where('customers.status', '=', $status);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('customers.name_english', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('customers.customer_code', $sortordcode);
                    })
                    ->when($sortordcountry, function ($query) use ($sortordcountry) {
                        return $query->orderby('customers.city', $sortordcountry);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('customers.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);
                    
            return view('elegantclub/customer_list/searchresults', array('elegantcustomers' => $elegantcustomers));
        }
        
        
        return view('elegantclub/customer_list/index', array('elegantcustomers' => $elegantcustomers, 'countries' => $countries));
    }
    
    public function deletelist(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $elegantcustomers = DB::table('customers')
                ->select('customers.*','country.name as country_name','employees.username as empCode','employees.first_name as empName')
                ->leftjoin('country', 'country.id', '=', 'customers.nationality')
                ->leftjoin('employees', 'employees.id', '=', 'customers.created_by')
                ->where('customers.status', '!=', 2)
                ->orderby('customers.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_name = Input::get('search_name');
            $searchbyph = Input::get('searchbyph');
            $searchbycode = Input::get('searchbycode');
            $searchbyempcode = Input::get('searchbyempcode');
            $searchbyemail = Input::get('searchbyemail');
            $country = Input::get('country');
            $status = Input::get('status');
            
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortordcountry = Input::get('sortordcountry');
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortordcode=='' && $sortordcountry==''){
                $sortOrdDefault='DESC';
            }
            
            $elegantcustomers = DB::table('customers')
                    ->select('customers.*','country.name as country_name','employees.username as empCode','employees.first_name as empName')
                    ->leftjoin('country', 'country.id', '=', 'customers.nationality')
                    ->leftjoin('employees', 'employees.id', '=', 'customers.created_by')
                    ->where('customers.status', '!=', 2)
                    ->when($search_name, function ($query) use ($search_name) {
                        return $query->whereRaw("customers.name_english like '%$search_name%'");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("customers.customer_code like '%$searchbycode%'");
                    })
                    ->when($searchbyempcode, function ($query) use ($searchbyempcode) {
                        return $query->whereRaw("employees.username like '%$searchbyempcode%'");
                    })
                    ->when($country, function ($query) use ($country) {
                        return $query->whereRaw("customers.city like '%$country%'");
                    })
                     ->when($searchbyemail, function ($query) use ($searchbyemail) {
                        return $query->whereRaw("customers.email_1 like '%$searchbyemail%'");
                    })
                     ->when($searchbyph, function ($query) use ($searchbyph) {
                         if($searchbyph==';'){ $searchbyph=0;}
                        return $query->whereRaw("customers.mobile_1 like '%$searchbyph%'");
                    })
                    ->when($status, function ($query) use ($status) {
                        if($status==-1){
                            $status=0;
                        }
                        return $query->where('customers.status', '=', $status);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('customers.name_english', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('customers.customer_code', $sortordcode);
                    })
                    ->when($sortordcountry, function ($query) use ($sortordcountry) {
                        return $query->orderby('customers.city', $sortordcountry);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('customers.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);
                    
            return view('elegantclub/customer_list/deleteresults', array('elegantcustomers' => $elegantcustomers));
        }
        
        
        return view('elegantclub/customer_list/deleteindex', array('elegantcustomers' => $elegantcustomers, 'countries' => $countries));
    }
   
    public function delete_customer($id) {
        try {
            $dn = \Crypt::decrypt($id);
            
            DB::table('customers')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            
            Toastr::success('Customer Deleted Successfully!', $title = null, $options = []);
            return Redirect::to('elegantclub/delete_corporate_customer');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/delete_corporate_customer');
        }
    }
    
    public function edit_customer_list(Request $request){
        try{
            $paginate = Config::get('app.PAGINATE');
        
            $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
            $elegantcustomers = DB::table('customers')
                ->select('customers.*','country.name as country_name','employees.username as empCode','employees.first_name as empName')
                ->leftjoin('country', 'country.id', '=', 'customers.nationality')
                ->leftjoin('employees', 'employees.id', '=', 'customers.created_by')
                ->where('customers.status', '!=', 2)
                ->orderby('customers.created_at', 'DESC')
                ->paginate($paginate);
            if ($request->ajax()) {
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                $search_name = Input::get('search_name');
                $searchbyph = Input::get('searchbyph');
                $searchbycode = Input::get('searchbycode');
                $searchbyempcode = Input::get('searchbyempcode');
                $searchbyemail = Input::get('searchbyemail');
                $country = Input::get('country');
                $status = Input::get('status');

                $sortordname = Input::get('sortordname');
                $sortordcode = Input::get('sortordcode');
                $sortordcountry = Input::get('sortordcountry');

                $sortOrdDefault='';
                if($sortordname=='' && $sortordcode=='' && $sortordcountry==''){
                    $sortOrdDefault='DESC';
                }

                $elegantcustomers = DB::table('customers')
                        ->select('customers.*','country.name as country_name','employees.username as empCode','employees.first_name as empName')
                        ->leftjoin('country', 'country.id', '=', 'customers.nationality')
                        ->leftjoin('employees', 'employees.id', '=', 'customers.created_by')
                        ->where('customers.status', '!=', 2)
                        ->when($search_name, function ($query) use ($search_name) {
                            return $query->whereRaw("customers.name_english like '%$search_name%'");
                        })
                        ->when($searchbycode, function ($query) use ($searchbycode) {
                            return $query->whereRaw("customers.customer_code like '%$searchbycode%'");
                        })
                        ->when($searchbyempcode, function ($query) use ($searchbyempcode) {
                            return $query->whereRaw("employees.username like '%$searchbyempcode%'");
                        })
                        ->when($country, function ($query) use ($country) {
                            return $query->whereRaw("customers.city like '%$country%'");
                        })
                         ->when($searchbyemail, function ($query) use ($searchbyemail) {
                            return $query->whereRaw("customers.email_1 like '%$searchbyemail%'");
                        })
                         ->when($searchbyph, function ($query) use ($searchbyph) {
                             if($searchbyph==';'){ $searchbyph=0;}
                            return $query->whereRaw("customers.mobile_1 like '%$searchbyph%'");
                        })
                        ->when($status, function ($query) use ($status) {
                            if($status==-1){
                                $status=0;
                            }
                            return $query->where('customers.status', '=', $status);
                        })
                        ->when($sortordname, function ($query) use ($sortordname) {
                            return $query->orderby('customers.name_english', $sortordname);
                        })
                        ->when($sortordcode, function ($query) use ($sortordcode) {
                            return $query->orderby('customers.customer_code', $sortordcode);
                        })
                        ->when($sortordcountry, function ($query) use ($sortordcountry) {
                            return $query->orderby('customers.city', $sortordcountry);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('customers.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);

                return view('elegantclub/edit_list/searchresults', array('elegantcustomers' => $elegantcustomers));
        }
            return view('elegantclub/edit_list/index', array('elegantcustomers' => $elegantcustomers,'countries' => $countries));

       } catch (\Exception $e) { 
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub');
        }
    }
    
    public function edit_customer($id){
        try{
            $customer_id = \Crypt::decrypt($id);
            $groups = DB::table('ac_ledger_group')
                    ->select('ac_ledger_group.*')
                    ->whereRaw("ac_ledger_group.parent_id!=0")
                    ->where('status', '!=', 2)
                    ->get();
            $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
            $elegantcustomer = DB::table('customers')
                        ->select('customers.*','employees.username as empCode','employees.first_name as empName')
                        ->leftjoin('employees','employees.id','=','customers.created_by')
                        ->where('customers.id','=',$customer_id)
                        ->first();
            $employees = DB::table('employees')
                    ->select('id','username','first_name')
                    ->where('status', '!=', 2)
                    ->get();
            return view('elegantclub/edit_list/edit', array('elegantcustomer' => $elegantcustomer, 'parentgroups' => $groups, 'countries' => $countries, 'employees' => $employees));

        } catch (\Exception $e) { 
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/edit_corporate_customer');
        }
    }
    
    public function disable_customer($id){
        try {
            $dn = \Crypt::decrypt($id);
            
            DB::table('customers')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
                        
            Toastr::success('Customer Successfully Disabled', $title = null, $options = []);
            return Redirect::to('elegantclub/edit_corporate_customer');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/edit_corporate_customer');
        }
    }
    
    public function enable_customer($id){
        try {
            $dn = \Crypt::decrypt($id);
            
            DB::table('customers')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
                        
            Toastr::success('Customer Successfully Enbled', $title = null, $options = []);
            return Redirect::to('elegantclub/edit_corporate_customer');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/edit_corporate_customer');
        }
    }
    
    public function view_customer($id){
        try{
            $customer_id = \Crypt::decrypt($id);
            $groups = DB::table('ac_ledger_group')
                    ->select('ac_ledger_group.*')
                    ->whereRaw("ac_ledger_group.parent_id!=0")
                    ->where('status', '!=', 2)
                    ->get();
            $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
            $elegantcustomer = DB::table('customers')
                        ->select('customers.*','employees.username as empCode','employees.first_name as empName')
                        ->leftjoin('employees','employees.id','=','customers.created_by')
                        ->where('customers.id','=',$customer_id)
                        ->first();
            $employees = DB::table('employees')
                    ->select('id','username','first_name')
                    ->where('status', '!=', 2)
                    ->get();
            return view('elegantclub/view', array('elegantcustomer' => $elegantcustomer, 'parentgroups' => $groups, 'countries' => $countries, 'employees' => $employees));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/list_corporate_customer');
        }
    }
}