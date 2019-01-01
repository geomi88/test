<?php

namespace App\Http\Controllers\Ledgers;

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
use App\Models\Company;
use App\Models\Party;
use App\Models\Accounts;
use App\Models\Document;
use App\Models\Preferredproducts;
use DB;
use Mail;
use App;
use PDF;
use Excel;


class SuppliersController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        
        
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $suppliers = DB::table('ac_party')
                ->select('ac_party.*','country.name as country_name',db::raw("COALESCE(ac_party.email,ac_party.contact_email) as email"))
                ->leftjoin('country', 'country.id', '=', 'ac_party.nationality')
                ->where('ac_party.status', '!=', 2)
                ->where('ac_party.party_type', '=', "Supplier")
                ->orderby('ac_party.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_name = Input::get('search_name');
            $searchbyph = Input::get('searchbyph');
            $searchbycode = Input::get('searchbycode');
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
            
            $suppliers = DB::table('ac_party')
                    ->select('ac_party.*','country.name as country_name',db::raw("COALESCE(ac_party.email,ac_party.contact_email) as email"))
                    ->leftjoin('country', 'country.id', '=', 'ac_party.nationality')
                    ->where('ac_party.status', '!=', 2)
                    ->where('ac_party.party_type', '=', "Supplier")
                    ->when($search_name, function ($query) use ($search_name) {
                        return $query->whereRaw("(ac_party.first_name like '$search_name%' or concat(ac_party.first_name,' ',ac_party.alias_name,' ',ac_party.last_name) like '$search_name%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("ac_party.code like '$searchbycode%'");
                    })
                    ->when($country, function ($query) use ($country) {
                        return $query->where('ac_party.nationality', '=', $country);
                    })
                     ->when($searchbyemail, function ($query) use ($searchbyemail) {
                        return $query->whereRaw("(ac_party.email like '$searchbyemail%' OR ac_party.contact_email like '$searchbyemail%')");
                    })
                     ->when($searchbyph, function ($query) use ($searchbyph) {
                         if($searchbyph==';'){ $searchbyph=0;}
                        return $query->whereRaw("(ac_party.mobile_number like '$searchbyph%')");
                    })
                    ->when($status, function ($query) use ($status) {
                        if($status==-1){
                            $status=0;
                        }
                        return $query->where('ac_party.status', '=', $status);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('ac_party.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('ac_party.code', $sortordcode);
                    })
                    ->when($sortordcountry, function ($query) use ($sortordcountry) {
                        return $query->orderby('country.name', $sortordcountry);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('ac_party.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);
                    
            return view('ledgers/suppliers/searchresults', array('suppliers' => $suppliers));
        }
        
        
        return view('ledgers/suppliers/index', array('suppliers' => $suppliers, 'countries' => $countries));
    }

    public function add() {
        if (Session::get('company')) {
            $companies = Session::get('company');
        }
        
        Session::forget('supplier_id');
        
        $gender_list = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'GENDER', 'status' => 1])->get();
        
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $groups = DB::table('ac_ledger_group')
            ->select('ac_ledger_group.*')
            ->whereRaw("ac_ledger_group.parent_id!=0")
            ->where('status', '!=', 2)
            ->get();

        return view('ledgers/suppliers/add', array('gender_list' => $gender_list, 'countries' => $countries, 'parentgroups'=>$groups));
    }

    public function store() {
        try {
            $supplier_data = Input::all();

            $supplier_id = Session::get('supplier_id');
            unset($supplier_data['email_radio']);
            
            if (Session::get('supplier_id')) {
                if(!$supplier_data['bank_country']){
                    $supplier_data['bank_country']=NULL;
                }
                DB::table('ac_party')
                        ->where('id', $supplier_id)
                        ->update($supplier_data);
                
                DB::table('ac_accounts')
                        ->whereRaw("type_id=$supplier_id AND type='Supplier'")
                        ->update(['ledger_group_id'=>$supplier_data['ledger_group_id'],'first_name'=>$supplier_data['first_name'],'last_name'=>$supplier_data['last_name'],'alias_name'=>$supplier_data['alias_name']]);
            } else {
                $maxid = DB::table('ac_party')->max('id') + 100;
                $supplier_data['code'] = "SU-" . $maxid;
                $supplier_data['company_id'] = Session::get('company');
                $supplier_data['party_type'] = "Supplier";
                $supplier_data['last_name'] = "";

                $result = Party::create($supplier_data);

                Session::set('supplier_id', $result->id);
                
                $model= new Accounts();
                $model->type_id = $result->id;
                $model->code = $supplier_data['code'];
                $model->ledger_group_id = $supplier_data['ledger_group_id'];
                $model->first_name = $supplier_data['first_name'];
                $model->last_name = $supplier_data['last_name'];
                $model->alias_name = $supplier_data['alias_name'];
                $model->type = "Supplier";
                $model->save();
            }
            return 1;
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('ac_party')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$dn AND type='Supplier'")
                    ->update(['status' => 0]);
                        
            Toastr::success('Supplier Successfully Disabled', $title = null, $options = []);
            return Redirect::to('suppliers/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('suppliers/editlist');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('ac_party')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$dn AND type='Supplier'")
                    ->update(['status' => 1]);
            
            Toastr::success('Supplier Successfully Enabled', $title = null, $options = []);
            return Redirect::to('suppliers/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('suppliers/editlist');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('ac_party')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$dn AND type='Supplier'")
                    ->update(['status' => 2]);
            
            Toastr::success('Supplier Successfully Deleted', $title = null, $options = []);
            return Redirect::to('suppliers/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('suppliers/editlist');
        }
    }

    public function edit($id) {
        $id = \Crypt::decrypt($id);

        if (Session::get('company')) {
            $companies = Session::get('company');
        }
        
        $supplier_data = DB::table('ac_party')
                        ->select('ac_party.*')
                        ->where(['id' => $id])->first();
         
        $gender_list = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'GENDER', 'status' => 1])->get();
        
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $groups = DB::table('ac_ledger_group')
            ->select('ac_ledger_group.*')
            ->whereRaw("ac_ledger_group.parent_id!=0")
            ->where('status', '!=', 2)
            ->get();
                    
        $arrProducts = DB::table('ac_preferred_prducts')
                ->select('ac_preferred_prducts.product_id as product_id','inventory.name as product_name','inventory.product_code as product_code')
                ->leftjoin('inventory', 'ac_preferred_prducts.product_id', '=', 'inventory.id')
                ->where(['ac_preferred_prducts.supplier_id' => $id])
                ->whereraw('ac_preferred_prducts.status=1')
                ->get();
                    

       
        ///////////////////////////// VAT_CERTIFICATE //////////////////
        $vat_certificate = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'VAT_CERTIFICATE')
                ->first();
        
        if (count($vat_certificate)>0) {
            $vat_certificateurl = $vat_certificate->file_url;
        } else {
            $vat_certificateurl = '';
        }
        
        ///////////////////////////// COMPANY_PROFILE //////////////////
        $compprofile = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'COMPANY_PROFILE')
                ->first();
        
        if (count($compprofile)>0) {
            $compprofileurl = $compprofile->file_url;
        } else {
            $compprofileurl = '';
        }
        
        ///////////////////////////// VENDOR_CONTRACT //////////////////
        $vendorcontract = DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)->where('document_type', '=', 'VENDOR_CONTRACT')
                ->first();
        
        if (count($vendorcontract)>0) {
            $vendorcontracturl = $vendorcontract->file_url;
        } else {
            $vendorcontracturl = '';
        }
        
        
        Session::set('supplier_id', $id);
        
        return view('ledgers/suppliers/edit', array('gender_list' => $gender_list,'supplier_data' => $supplier_data, 'countries' => $countries,'parentgroups'=>$groups,
                    'vatcertificateurl' => $vat_certificateurl, 'companyprofileurl' => $compprofileurl, 'vendorcontracturl' => $vendorcontracturl,'arrProducts'=>$arrProducts));
    }


    public function saveproducts() {
        try {
            if (Session::get('supplier_id')) {
                $arrProductsList = Input::get('arrProductsList');
                $arrProductList = json_decode($arrProductsList);
                
                DB::table('ac_preferred_prducts')
                    ->where('supplier_id', '=',Session::get('supplier_id'))
                    ->update(['status'=>0]);
                                
                foreach ($arrProductList as $value) {
                    $model= new Preferredproducts();
                    $model->supplier_id = Session::get('supplier_id');
                    $model->product_id = $value->product_id;
                    $model->save();
                }
            }
            
            return 1;
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
           return -1;
        }
    }
 
    public function upload_documents() {
        try {
            if (Session::get('supplier_id')) {
                $supplier_id = Session::get('supplier_id');
                $supplier = DB::table('ac_party')
                        ->select('ac_party.code as code')
                        ->whereRaw("id=$supplier_id")
                        ->first();
            }
            
            $subfolder = $supplier->code;
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            $web_url = $_ENV['WEB_URL'];

            //////////////////////// vat_certificate //////////////////
            if (Input::hasFile('vat_certificate')) {
                $vat_certificate = Input::file('vat_certificate');
                $extension = time() . '.' . $vat_certificate->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/VAT_CERTIFICATE';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($vat_certificate), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'VAT_CERTIFICATE';
                $documentmodel->user_id = $supplier_id;
                $documentmodel->document_owner_id = $supplier_id;
                $documentmodel->document_owner_type = 'PARTY_LEDGER';
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            
            //////////////////////// company_profile //////////////////
            if (Input::hasFile('company_profile')) {
                $company_profile = Input::file('company_profile');
                $extension = time() . '.' . $company_profile->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/COMPANY_PROFILE';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($company_profile), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'COMPANY_PROFILE';
                $documentmodel->user_id = $supplier_id;
                $documentmodel->document_owner_id = $supplier_id;
                $documentmodel->document_owner_type = 'PARTY_LEDGER';
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            
            //////////////////////// vendor_contract //////////////////
            if (Input::hasFile('vendor_contract')) {
                $vendor_contract = Input::file('vendor_contract');
                $extension = time() . '.' . $vendor_contract->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/VENDOR_CONTRACT';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($company_profile), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'VENDOR_CONTRACT';
                $documentmodel->user_id = $supplier_id;
                $documentmodel->document_owner_id = $supplier_id;
                $documentmodel->document_owner_type = 'PARTY_LEDGER';
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
          
            Toastr::success('Supplier Successfully Created!', $title = null, $options = []);
            return Redirect::to('ledgers');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('ledgers');
            
        }
    }

    public function update_upload_documents() {
        try {
             if (Session::get('supplier_id')) {
                $supplier_id = Session::get('supplier_id');
                $supplier = DB::table('ac_party')
                        ->select('ac_party.code as code')
                        ->whereRaw("id=$supplier_id")
                        ->first();
            }
            
            $subfolder = $supplier->code;
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');

            //////////////////////// vat_certificate//////////////////
            if (Input::hasFile('vat_certificate')) {
                $indentpic = Input::file('vat_certificate');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/VAT_CERTIFICATE';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                
                $data = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $supplier_id)
                        ->where('document_type', 'VAT_CERTIFICATE')
                        ->where('document_owner_id', $supplier_id)
                        ->where('document_owner_type', 'PARTY_LEDGER')
                        ->first();
                
                if (count($data) > 0) {
                    DB::table('documents')
                            ->where('id', $data->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'VAT_CERTIFICATE';
                    $documentmodel->user_id = $supplier_id;
                    $documentmodel->document_owner_id = $supplier_id;
                    $documentmodel->document_owner_type = 'PARTY_LEDGER';
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
            
            //////////////////////// company_profile//////////////////
            if (Input::hasFile('company_profile')) {
                $indentpic = Input::file('company_profile');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/COMPANY_PROFILE';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                
                $data = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $supplier_id)
                        ->where('document_type', 'COMPANY_PROFILE')
                        ->where('document_owner_id', $supplier_id)
                        ->where('document_owner_type', 'PARTY_LEDGER')
                        ->first();
                
                if (count($data) > 0) {
                    DB::table('documents')
                            ->where('id', $data->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'COMPANY_PROFILE';
                    $documentmodel->user_id = $supplier_id;
                    $documentmodel->document_owner_id = $supplier_id;
                    $documentmodel->document_owner_type = 'PARTY_LEDGER';
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
            
            //////////////////////// vendor_contract//////////////////
            if (Input::hasFile('vendor_contract')) {
                $indentpic = Input::file('vendor_contract');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/VENDOR_CONTRACT';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                
                $data = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $supplier_id)
                        ->where('document_type', 'VENDOR_CONTRACT')
                        ->where('document_owner_id', $supplier_id)
                        ->where('document_owner_type', 'PARTY_LEDGER')
                        ->first();
                
                if (count($data) > 0) {
                    DB::table('documents')
                            ->where('id', $data->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'VENDOR_CONTRACT';
                    $documentmodel->user_id = $supplier_id;
                    $documentmodel->document_owner_id = $supplier_id;
                    $documentmodel->document_owner_type = 'PARTY_LEDGER';
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }


            Toastr::success('Supplier Successfully Updated!', $title = null, $options = []);
            return Redirect::to('suppliers/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('suppliers/editlist');
        }
    }
    
    public function editlist(Request $request) {
        
        $paginate = Config::get('app.PAGINATE');
        
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $suppliers = DB::table('ac_party')
                ->select('ac_party.*','country.name as country_name',db::raw("COALESCE(ac_party.email,ac_party.contact_email) as email"))
                ->leftjoin('country', 'country.id', '=', 'ac_party.nationality')
                ->where('ac_party.status', '!=', 2)
                ->where('ac_party.party_type', '=', "Supplier")
                ->orderby('ac_party.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $search_name = Input::get('search_name');
            $searchbyph = Input::get('searchbyph');
            $searchbycode = Input::get('searchbycode');
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
            
            $suppliers = DB::table('ac_party')
                    ->select('ac_party.*','country.name as country_name',db::raw("COALESCE(ac_party.email,ac_party.contact_email) as email"))
                    ->leftjoin('country', 'country.id', '=', 'ac_party.nationality')
                    ->where('ac_party.status', '!=', 2)
                    ->where('ac_party.party_type', '=', "Supplier")
                    ->when($search_name, function ($query) use ($search_name) {
                        return $query->whereRaw("(ac_party.first_name like '$search_name%' or concat(ac_party.first_name,' ',ac_party.alias_name,' ',ac_party.last_name) like '$search_name%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("ac_party.code like '$searchbycode%'");
                    })
                    ->when($country, function ($query) use ($country) {
                        return $query->where('ac_party.nationality', '=', $country);
                    })
                     ->when($searchbyemail, function ($query) use ($searchbyemail) {
                        return $query->whereRaw("(ac_party.email like '$searchbyemail%' OR ac_party.contact_email like '$searchbyemail%')");
                    })
                     ->when($searchbyph, function ($query) use ($searchbyph) {
                         if($searchbyph==';'){ $searchbyph=0;}
                        return $query->whereRaw("(ac_party.mobile_number like '$searchbyph%')");
                    })
                    ->when($status, function ($query) use ($status) {
                        if($status==-1){
                            $status=0;
                        }
                        return $query->where('ac_party.status', '=', $status);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('ac_party.first_name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('ac_party.code', $sortordcode);
                    })
                    ->when($sortordcountry, function ($query) use ($sortordcountry) {
                        return $query->orderby('country.name', $sortordcountry);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('ac_party.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);
                    
            return view('ledgers/suppliers/editresults', array('suppliers' => $suppliers));
        }
        
        return view('ledgers/suppliers/editindex', array('suppliers' => $suppliers, 'countries' => $countries));
    }
    
    public function autocompleteinventory() {

        $searchkey = Input::get('searchkey');
        $data = array();
        if($searchkey != ''){
            $data = DB::table('inventory')
                    ->select('product_code','name','id')
                    ->where('status', '=', '1')
                    ->whereRaw("(product_code like '$searchkey%' OR name like '$searchkey%')")
                    ->get();
        }
        
        return \Response::json($data);
    }
    
    // Generate PDF funcion
    public function exportdata() {
        
         ini_set('memory_limit', '-1');
         ini_set('max_execution_time', 3000);
         
        $excelorpdf = Input::get('excelorpdf');
        $search_name = Input::get('search_name');
        $searchbyph = Input::get('searchbyph');
        $searchbycode = Input::get('searchbycode');
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

        $suppliers = DB::table('ac_party')
                ->select('ac_party.*','country.name as country_name',db::raw("COALESCE(ac_party.email,ac_party.contact_email) as email"))
                ->leftjoin('country', 'country.id', '=', 'ac_party.nationality')
                ->where('ac_party.status', '!=', 2)
                ->where('ac_party.party_type', '=', "Supplier")
                ->when($search_name, function ($query) use ($search_name) {
                    return $query->whereRaw("(ac_party.first_name like '$search_name%' or concat(ac_party.first_name,' ',ac_party.alias_name,' ',ac_party.last_name) like '$search_name%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("ac_party.code like '$searchbycode%'");
                })
                ->when($country, function ($query) use ($country) {
                    return $query->where('ac_party.nationality', '=', $country);
                })
                 ->when($searchbyemail, function ($query) use ($searchbyemail) {
                    return $query->whereRaw("(ac_party.email like '$searchbyemail%' OR ac_party.contact_email like '$searchbyemail%')");
                })
                 ->when($searchbyph, function ($query) use ($searchbyph) {
                     if($searchbyph==';'){ $searchbyph=0;}
                    return $query->whereRaw("(ac_party.mobile_number like '$searchbyph%')");
                })
                ->when($status, function ($query) use ($status) {
                    if($status==-1){
                        $status=0;
                    }
                    return $query->where('ac_party.status', '=', $status);
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('ac_party.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('ac_party.code', $sortordcode);
                })
                ->when($sortordcountry, function ($query) use ($sortordcountry) {
                    return $query->orderby('country.name', $sortordcountry);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('ac_party.created_at', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('SupplierList', function($excel) use($suppliers){
                 // Set the title
                $excel->setTitle('Suppliers List');
                
                $excel->sheet('Suppliers List', function($sheet) use($suppliers){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('D3', 'Suppliers List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:G3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Supplier Code','Company Name',"Country","Email Id","Phone No","Status"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:G5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    $employee_name="";
                    for($i=0;$i<count($suppliers);$i++){
                        $employee_name =  $suppliers[$i]->first_name." ". $suppliers[$i]->alias_name;
                        if( $suppliers[$i]->status== 1){
                            $status="Enabled";
                        }else{
                            $status="Disabled";
                        }
                        
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $suppliers[$i]->code);
                        $sheet->setCellValue('C'.$chrRow, $employee_name);
                        $sheet->setCellValue('D'.$chrRow, $suppliers[$i]->country_name);
                        $sheet->setCellValue('E'.$chrRow, $suppliers[$i]->email);
                        $sheet->setCellValue('F'.$chrRow, $suppliers[$i]->mobile_number);
                        $sheet->setCellValue('G'.$chrRow, $status);
                            
                        $sheet->cells('A'.$chrRow.':G'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });
                        
                        $chrRow++;
                    }

                });
                
            })->export('xls');
            
        } else{

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Suppliers List</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Supplier Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Company Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Country </td>
                                <td style="padding:10px 5px;color:#fff;"> Email Id </td>
                                <td style="padding:10px 5px;color:#fff;"> Phone No </td>
                                <td style="padding:10px 5px;color:#fff;"> Status </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno=0;
            foreach ($suppliers as $employee) {
                 $employee_name = $employee->first_name." ". $employee->alias_name;
                        if($employee->status== 1){
                            $status="Enabled";
                        }else{
                            $status="Disabled";
                        }
                
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->code . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->country_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->email . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->mobile_number . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $status . '</td>
                               </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_supplier_list.pdf');
        }
    }   
    
    // Generate PDF funcion
    public function exportdataedit() {
        
         ini_set('memory_limit', '-1');
         ini_set('max_execution_time', 3000);
         
        $excelorpdf = Input::get('excelorpdf');
        $search_name = Input::get('search_name');
        $searchbyph = Input::get('searchbyph');
        $searchbycode = Input::get('searchbycode');
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

        $suppliers = DB::table('ac_party')
                ->select('ac_party.*','country.name as country_name',db::raw("COALESCE(ac_party.email,ac_party.contact_email) as email"))
                ->leftjoin('country', 'country.id', '=', 'ac_party.nationality')
                ->where('ac_party.status', '!=', 2)
                ->where('ac_party.party_type', '=', "Supplier")
                ->when($search_name, function ($query) use ($search_name) {
                    return $query->whereRaw("(ac_party.first_name like '$search_name%' or concat(ac_party.first_name,' ',ac_party.alias_name,' ',ac_party.last_name) like '$search_name%')");
                })
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("ac_party.code like '$searchbycode%'");
                })
                ->when($country, function ($query) use ($country) {
                    return $query->where('ac_party.nationality', '=', $country);
                })
                 ->when($searchbyemail, function ($query) use ($searchbyemail) {
                    return $query->whereRaw("(ac_party.email like '$searchbyemail%' OR ac_party.contact_email like '$searchbyemail%')");
                })
                 ->when($searchbyph, function ($query) use ($searchbyph) {
                     if($searchbyph==';'){ $searchbyph=0;}
                    return $query->whereRaw("(ac_party.mobile_number like '$searchbyph%')");
                })
                ->when($status, function ($query) use ($status) {
                    if($status==-1){
                        $status=0;
                    }
                    return $query->where('ac_party.status', '=', $status);
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('ac_party.first_name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('ac_party.code', $sortordcode);
                })
                ->when($sortordcountry, function ($query) use ($sortordcountry) {
                    return $query->orderby('country.name', $sortordcountry);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('ac_party.created_at', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('SupplierList', function($excel) use($suppliers){
                 // Set the title
                $excel->setTitle('Suppliers List');
                
                $excel->sheet('Suppliers List', function($sheet) use($suppliers){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('C3', 'Suppliers List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Supplier Code','Company Name',"Country","Email Id","Phone No"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    $employee_name="";
                    for($i=0;$i<count($suppliers);$i++){
                        $employee_name =  $suppliers[$i]->first_name." ". $suppliers[$i]->alias_name;
                        if( $suppliers[$i]->status== 1){
                            $status="Enabled";
                        }else{
                            $status="Disabled";
                        }
                        
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $suppliers[$i]->code);
                        $sheet->setCellValue('C'.$chrRow, $employee_name);
                        $sheet->setCellValue('D'.$chrRow, $suppliers[$i]->country_name);
                        $sheet->setCellValue('E'.$chrRow, $suppliers[$i]->email);
                        $sheet->setCellValue('F'.$chrRow, $suppliers[$i]->mobile_number);
                            
                        $sheet->cells('A'.$chrRow.':F'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });
                        
                        $chrRow++;
                    }

                });
                
            })->export('xls');
            
        } else{

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>MTG</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h2>Suppliers List</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Supplier Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Company Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Country </td>
                                <td style="padding:10px 5px;color:#fff;"> Email Id </td>
                                <td style="padding:10px 5px;color:#fff;"> Phone No </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
            $slno=0;
            foreach ($suppliers as $employee) {
                 $employee_name = $employee->first_name." ". $employee->alias_name;
                        if($employee->status== 1){
                            $status="Enabled";
                        }else{
                            $status="Disabled";
                        }
                
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->code . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->country_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->email . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $employee->mobile_number . '</td>
                               </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_supplier_list.pdf');
        }
    }
    
    public function view($id){
        try{
            $id = \Crypt::decrypt($id);
            
            $supplier_data = DB::table('ac_party')
                        ->select('ac_party.*')
                        ->where(['id' => $id])->first();
         
            $gender_list = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->where(['resource_type' => 'GENDER', 'status' => 1])->get();

            $countries = DB::table('country')
                            ->select('country.*')
                            ->orderby('name', 'ASC')->get();

            $groups = DB::table('ac_ledger_group')
                ->select('ac_ledger_group.*')
                ->whereRaw("ac_ledger_group.parent_id!=0")
                ->where('status', '!=', 2)
                ->get();

            $arrProducts = DB::table('ac_preferred_prducts')
                    ->select('ac_preferred_prducts.product_id as product_id','inventory.name as product_name','inventory.product_code as product_code')
                    ->leftjoin('inventory', 'ac_preferred_prducts.product_id', '=', 'inventory.id')
                    ->where(['ac_preferred_prducts.supplier_id' => $id])
                    ->whereraw('ac_preferred_prducts.status=1')
                    ->get();



            ///////////////////////////// VAT_CERTIFICATE //////////////////
            $vat_certificate = DB::table('documents')
                    ->select('documents.file_url')
                    ->where('user_id', '=', $id)->where('document_type', '=', 'VAT_CERTIFICATE')
                    ->first();

            if (count($vat_certificate)>0) {
                $vat_certificateurl = $vat_certificate->file_url;
            } else {
                $vat_certificateurl = '';
            }

            ///////////////////////////// COMPANY_PROFILE //////////////////
            $compprofile = DB::table('documents')
                    ->select('documents.file_url')
                    ->where('user_id', '=', $id)->where('document_type', '=', 'COMPANY_PROFILE')
                    ->first();

            if (count($compprofile)>0) {
                $compprofileurl = $compprofile->file_url;
            } else {
                $compprofileurl = '';
            }

            ///////////////////////////// VENDOR_CONTRACT //////////////////
            $vendorcontract = DB::table('documents')
                    ->select('documents.file_url')
                    ->where('user_id', '=', $id)->where('document_type', '=', 'VENDOR_CONTRACT')
                    ->first();

            if (count($vendorcontract)>0) {
                $vendorcontracturl = $vendorcontract->file_url;
            } else {
                $vendorcontracturl = '';
            }
            return view('ledgers/suppliers/view',array('gender_list' => $gender_list,'supplier' => $supplier_data, 'countries' => $countries,'parentgroups'=>$groups,
                    'vatcertificateurl' => $vat_certificateurl, 'companyprofileurl' => $compprofileurl, 'vendorcontracturl' => $vendorcontracturl,'arrProducts'=>$arrProducts));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
      
      
}