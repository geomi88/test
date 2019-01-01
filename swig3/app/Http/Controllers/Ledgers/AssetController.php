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
use App\Models\Assets;
use App\Models\Accounts;
use App\Models\Document;
use DB;
use Mail;
use App;
use PDF;
use Excel;


class AssetController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        
        
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $assets = DB::table('ac_assets')
                ->select('ac_assets.*',db::raw("concat(ac_party.first_name,' ',ac_party.alias_name) as supplier_name"))
                ->leftjoin('ac_party', 'ac_assets.supplier_id', '=', 'ac_party.id')
                ->where('ac_assets.status', '!=', 2)
                ->orderby('ac_assets.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $searchbycode = Input::get('searchbycode');
            $search_name = Input::get('search_name');
            $search_sname = Input::get('search_sname');
            $purchase_from = Input::get('purchase_from');
            $purchase_to = Input::get('purchase_to');
            $status = Input::get('status');
            
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortordsname = Input::get('sortordsname');
            $sortorddate = Input::get('sortorddate');
            
            if ($purchase_from != '') {
                $purchase_from = explode('-', $purchase_from);
                $purchase_from = $purchase_from[2] . '-' . $purchase_from[1] . '-' . $purchase_from[0];
            }
            if ($purchase_to != '') {
                $purchase_to = explode('-', $purchase_to);
                $purchase_to = $purchase_to[2] . '-' . $purchase_to[1] . '-' . $purchase_to[0];
            }
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortordcode=='' && $search_sname=='' && $sortorddate==''){
                $sortOrdDefault='DESC';
            }
            
            $assets = DB::table('ac_assets')
                    ->select('ac_assets.*',db::raw("concat(ac_party.first_name,' ',ac_party.alias_name) as supplier_name"))
                    ->leftjoin('ac_party', 'ac_assets.supplier_id', '=', 'ac_party.id')
                    ->where('ac_assets.status', '!=', 2)
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("ac_assets.code like '$searchbycode%'");
                    })
                    ->when($search_name, function ($query) use ($search_name) {
                        return $query->whereRaw("(ac_assets.name like '$search_name%' or concat(ac_assets.name,' ',ac_assets.alias_name) like '$search_name%')");
                    })
                    ->when($search_sname, function ($query) use ($search_sname) {
                        return $query->whereRaw("(ac_party.first_name like '$search_sname%' or concat(ac_party.first_name,' ',ac_party.alias_name,' ',ac_party.last_name) like '$search_sname%')");
                    })
                    ->when($purchase_from, function ($query) use ($purchase_from) {
                        return $query->whereRaw("ac_assets.purchase_date >= '$purchase_from' ");
                    })
                    ->when($purchase_to, function ($query) use ($purchase_to) {
                        return $query->whereRaw("ac_assets.purchase_date<= '$purchase_to' ");
                    })
                    ->when($status, function ($query) use ($status) {
                        if($status==-1){
                            $status=0;
                        }
                        return $query->where('ac_assets.status', '=', $status);
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('ac_assets.name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('ac_assets.code', $sortordcode);
                    })
                    ->when($sortordsname, function ($query) use ($sortordsname) {
                        return $query->orderby('ac_party.first_name', $sortordsname);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('ac_assets.purchase_date', $sortorddate);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('ac_assets.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);
                    
            return view('ledgers/assets/searchresults', array('assets' => $assets));
        }
        
        
        return view('ledgers/assets/index', array('assets' => $assets, 'countries' => $countries));
    }

    public function add() {
        if (Session::get('company')) {
            $companies = Session::get('company');
        }
        
        Session::forget('asset_id');
        
        $branches = DB::table('master_resources')
                        ->select('master_resources.name','id','master_resources.branch_code')
                        ->where(['resource_type' => 'BRANCH', 'status' => 1])
                        ->get();
        
        $groups = DB::table('ac_ledger_group')
            ->select('ac_ledger_group.*')
            ->whereRaw("ac_ledger_group.parent_id!=0")
            ->where('status', '!=', 2)
            ->get();
        
        $barcodes = DB::table('barcodes')
                ->select('barcodes.*', 'employees.first_name as created_by_name')
                ->leftjoin('employees', 'employees.id', '=', 'barcodes.created_by')
                ->where(['barcodes.is_used' => 0, 'barcodes.status' => 1])
                ->orderby('barcodes.created_at', 'DESC')
                ->get();
        
        $suppliers = DB::table('ac_party')
                ->select('ac_party.first_name','ac_party.code','ac_party.id','ac_party.alias_name')
                ->where(['ac_party.party_type' => 'Supplier', 'ac_party.status' => 1])
                ->orderby('ac_party.first_name', 'ASC')
                ->get();
        
        $employees = DB::table('employees')
                ->select('employees.first_name','employees.username','employees.id','employees.alias_name')
                ->whereRaw("employees.status=1 AND id!=1")
                ->orderby('employees.first_name', 'ASC')
                ->get();
        
        return view('ledgers/assets/add', array('suppliers' => $suppliers,'employees'=>$employees ,'branches'=>$branches, 'parentgroups'=>$groups,'barcodes'=>$barcodes));
    }

    public function store() {
        try {
            $asset_data = Input::all();

            $asset_id = Session::get('asset_id');
            
            if (Session::get('asset_id')) {
                
                $purchase_date=$asset_data['purchase_date'];
                if($purchase_date != ''){
                    $purchase_date = explode('-',$purchase_date);
                    $purchase_date = $purchase_date[2].'-'.$purchase_date[1].'-'.$purchase_date[0];
                }else{
                    $purchase_date = NULL;
                }
                $asset_data['purchase_date'] = $purchase_date;
                
                if(isset($asset_data['barcodeold'])){
                    DB::table('barcodes')
                        ->where(['id' => $asset_data['barcodeold']])
                        ->update(['is_used' => 0]);
                
                    unset($asset_data['barcodeold']);
                }
               
                
                DB::table('ac_assets')
                        ->where('id', $asset_id)
                        ->update($asset_data);
                
                DB::table('barcodes')
                        ->where(['id' => $asset_data['barcode_id']])
                        ->update(['is_used' => 1]);
                                
                DB::table('ac_accounts')
                        ->whereRaw("type_id=$asset_id AND type='Asset'")
                        ->update(['ledger_group_id'=>$asset_data['ledger_group_id'],'first_name'=>$asset_data['name'],'alias_name'=>$asset_data['alias_name']]);

            } else {
                $maxid = DB::table('ac_assets')->max('id') + 100;
                $asset_data['code'] = "A-" . $maxid;
                $asset_data['company_id'] = Session::get('company');
                
                $purchase_date=$asset_data['purchase_date'];
                if($purchase_date != ''){
                    $purchase_date = explode('-',$purchase_date);
                    $purchase_date = $purchase_date[2].'-'.$purchase_date[1].'-'.$purchase_date[0];
                }else{
                    $purchase_date = NULL;
                }
                $asset_data['purchase_date'] = $purchase_date;
                
                $result = Assets::create($asset_data);

                Session::set('asset_id', $result->id);
                
                DB::table('barcodes')
                        ->where(['id' => $asset_data['barcode_id']])
                        ->update(['is_used' => 1]);
                
                $model= new Accounts();
                $model->type_id = $result->id;
                $model->code = $asset_data['code'];
                $model->ledger_group_id = $asset_data['ledger_group_id'];
                $model->first_name = $asset_data['name'];
                $model->alias_name = $asset_data['alias_name'];
                $model->type = "Asset";
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
            $companies = DB::table('ac_assets')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$dn AND type='Asset'")
                    ->update(['status' => 0]);
                        
            Toastr::success('Asset Successfully Disabled', $title = null, $options = []);
            return Redirect::to('assets/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('assets/editlist');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('ac_assets')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$dn AND type='Asset'")
                    ->update(['status' => 1]);
            
            Toastr::success('Asset Successfully Enabled', $title = null, $options = []);
            return Redirect::to('assets/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('assets/editlist');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('ac_assets')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$dn AND type='Asset'")
                    ->update(['status' => 2]);
            
            Toastr::success('Asset Successfully Deleted', $title = null, $options = []);
            return Redirect::to('assets/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('assets/editlist');
        }
    }

    public function edit($id) {
        $id = \Crypt::decrypt($id);

        if (Session::get('company')) {
            $companies = Session::get('company');
        }
        
        $asset_data = DB::table('ac_assets')
                        ->select('ac_assets.*')
                        ->where(['id' => $id])->first();
         
        $branches = DB::table('master_resources')
                        ->select('master_resources.name','id','master_resources.branch_code')
                        ->where(['resource_type' => 'BRANCH', 'status' => 1])
                        ->get();
        
        $groups = DB::table('ac_ledger_group')
                ->select('ac_ledger_group.*')
                ->whereRaw("ac_ledger_group.parent_id!=0")
                ->where('status', '!=', 2)
                ->get();
        
        $barcodes = DB::table('barcodes')
                ->select('barcodes.*', 'employees.first_name as created_by_name')
                ->leftjoin('employees', 'employees.id', '=', 'barcodes.created_by')
                ->where(['barcodes.status' => 1])
                ->whereRaw("(barcodes.is_used=0 OR barcodes.id=$asset_data->barcode_id)")
                ->orderby('barcodes.created_at', 'DESC')
                ->get();
        
        $suppliers = DB::table('ac_party')
                ->select('ac_party.first_name','ac_party.code','ac_party.id','ac_party.alias_name')
                ->where(['ac_party.party_type' => 'Supplier', 'ac_party.status' => 1])
                ->orderby('ac_party.first_name', 'ASC')
                ->get();
        
        $employees = DB::table('employees')
                ->select('employees.first_name','employees.username','employees.id','employees.alias_name')
                ->where(['employees.status' => 1])
                ->orderby('employees.first_name', 'ASC')
                ->get();
       
        ///////////////////////////// ASSET_IMAGE_1 //////////////////
        $assetimage1= DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)
                ->where('document_type', '=', 'ASSET_IMAGE_1')
                ->where('document_owner_id', $id)
                ->where('document_owner_type', 'ASSETS')
                ->first();
        
        if (count($assetimage1)>0) {
            $assetimage1 = $assetimage1->file_url;
        } else {
            $assetimage1 = '';
        }
        
        ///////////////////////////// ASSET_IMAGE_2 //////////////////
        $assetimage2= DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)
                ->where('document_owner_id', $id)
                ->where('document_owner_type', 'ASSETS')
                ->where('document_type', '=', 'ASSET_IMAGE_2')
                ->first();
        
        if (count($assetimage2)>0) {
            $assetimage2 = $assetimage2->file_url;
        } else {
            $assetimage2 = '';
        }
        
        ///////////////////////////// ASSET_IMAGE_3 //////////////////
        $assetimage3= DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)
                ->where('document_type', '=', 'ASSET_IMAGE_3')
                ->where('document_owner_id', $id)
                ->where('document_owner_type', 'ASSETS')
                ->first();
        
        if (count($assetimage3)>0) {
            $assetimage3 = $assetimage3->file_url;
        } else {
            $assetimage3 = '';
        }
        
        ///////////////////////////// ASSET_DOC_1 //////////////////
        $assetdoc1= DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)
                ->where('document_type', '=', 'ASSET_DOC_1')
                 ->where('document_owner_id', $id)
                ->where('document_owner_type', 'ASSETS')
                ->first();
        
        if (count($assetdoc1)>0) {
            $assetdoc1 = $assetdoc1->file_url;
        } else {
            $assetdoc1 = '';
        }
        
        
        ///////////////////////////// ASSET_DOC_2 //////////////////
        $assetdoc2= DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)
                ->where('document_type', '=', 'ASSET_DOC_2')
                ->where('document_owner_id', $id)
                ->where('document_owner_type', 'ASSETS')
                ->first();
        
        if (count($assetdoc2)>0) {
            $assetdoc2 = $assetdoc2->file_url;
        } else {
            $assetdoc2 = '';
        }
        
        ///////////////////////////// ASSET_DOC_3 //////////////////
        $assetdoc3= DB::table('documents')
                ->select('documents.file_url')
                ->where('user_id', '=', $id)
                ->where('document_type', '=', 'ASSET_DOC_3')
                ->where('document_owner_id', $id)
                ->where('document_owner_type', 'ASSETS')
                ->first();
        
        if (count($assetdoc3)>0) {
            $assetdoc3 = $assetdoc3->file_url;
        } else {
            $assetdoc3 = '';
        }
        
        Session::set('asset_id', $id);
        
        return view('ledgers/assets/edit', array('asset_data' => $asset_data, 'branches' => $branches,'parentgroups'=>$groups,'barcodes'=>$barcodes,'suppliers'=>$suppliers,
                    'employees' => $employees,'assetimage1'=>$assetimage1,'assetimage2'=>$assetimage2,'assetimage3'=>$assetimage3,'assetdoc1'=>$assetdoc1,'assetdoc2'=>$assetdoc2,'assetdoc3'=>$assetdoc3));
    }



 
    public function upload_documents() {
        try {
            if (Session::get('asset_id')) {
                $asset_id = Session::get('asset_id');
                $supplier = DB::table('ac_assets')
                        ->select('ac_assets.code as code')
                        ->whereRaw("id=$asset_id")
                        ->first();
            }
            
            $subfolder = $supplier->code;
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');
            $web_url = $_ENV['WEB_URL'];

            //////////////////////// asset_image_1 //////////////////
            if (Input::hasFile('asset_image_1')) {
                $vat_certificate = Input::file('asset_image_1');
                $extension = time() . '.' . $vat_certificate->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_IMAGE_1';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($vat_certificate), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'ASSET_IMAGE_1';
                $documentmodel->user_id = $asset_id;
                $documentmodel->document_owner_id = $asset_id;
                $documentmodel->document_owner_type = 'ASSETS';
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            
            //////////////////////// asset_image_2 //////////////////
            if (Input::hasFile('asset_image_2')) {
                $vat_certificate = Input::file('asset_image_2');
                $extension = time() . '.' . $vat_certificate->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_IMAGE_2';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($vat_certificate), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'ASSET_IMAGE_2';
                $documentmodel->user_id = $asset_id;
                $documentmodel->document_owner_id = $asset_id;
                $documentmodel->document_owner_type = 'ASSETS';
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            
            //////////////////////// asset_image_3 //////////////////
            if (Input::hasFile('asset_image_3')) {
                $vat_certificate = Input::file('asset_image_3');
                $extension = time() . '.' . $vat_certificate->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_IMAGE_3';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($vat_certificate), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'ASSET_IMAGE_3';
                $documentmodel->user_id = $asset_id;
                $documentmodel->document_owner_id = $asset_id;
                $documentmodel->document_owner_type = 'ASSETS';
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            
            //////////////////////// asset_doc_1 //////////////////
            if (Input::hasFile('asset_doc_1')) {
                $vat_certificate = Input::file('asset_doc_1');
                $extension = time() . '.' . $vat_certificate->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_DOC_1';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($vat_certificate), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'ASSET_DOC_1';
                $documentmodel->user_id = $asset_id;
                $documentmodel->document_owner_id = $asset_id;
                $documentmodel->document_owner_type = 'ASSETS';
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            
            //////////////////////// asset_doc_2 //////////////////
            if (Input::hasFile('asset_doc_2')) {
                $vat_certificate = Input::file('asset_doc_2');
                $extension = time() . '.' . $vat_certificate->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_DOC_2';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($vat_certificate), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'ASSET_DOC_2';
                $documentmodel->user_id = $asset_id;
                $documentmodel->document_owner_id = $asset_id;
                $documentmodel->document_owner_type = 'ASSETS';
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            
            //////////////////////// asset_doc_3 //////////////////
            if (Input::hasFile('asset_doc_3')) {
                $vat_certificate = Input::file('asset_doc_3');
                $extension = time() . '.' . $vat_certificate->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_DOC_3';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($vat_certificate), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);

                $documentmodel = new Document();
                $documentmodel->document_type = 'ASSET_DOC_3';
                $documentmodel->user_id = $asset_id;
                $documentmodel->document_owner_id = $asset_id;
                $documentmodel->document_owner_type = 'ASSETS';
                $documentmodel->file_url = $pic_url;
                $documentmodel->save();
            }
            
           
            Toastr::success('Asset Successfully Created!', $title = null, $options = []);
            return Redirect::to('ledgers');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('ledgers');
            
        }
    }

    public function update_upload_documents() {
        try {
            if (Session::get('asset_id')) {
                $asset_id = Session::get('asset_id');
                $assets = DB::table('ac_assets')
                        ->select('ac_assets.code as code')
                        ->whereRaw("id=$asset_id")
                        ->first();
            }
            
            $subfolder = $assets->code;
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');

            //////////////////////// ASSET_IMAGE_1//////////////////
            if (Input::hasFile('asset_image_1')) {
                $indentpic = Input::file('asset_image_1');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_IMAGE_1';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                
                $data = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $asset_id)
                        ->where('document_type', 'ASSET_IMAGE_1')
                        ->where('document_owner_id', $asset_id)
                        ->where('document_owner_type', 'ASSETS')
                        ->first();
                
                if (count($data) > 0) {
                    DB::table('documents')
                            ->where('id', $data->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'ASSET_IMAGE_1';
                    $documentmodel->user_id = $asset_id;
                    $documentmodel->document_owner_id = $asset_id;
                    $documentmodel->document_owner_type = 'ASSETS';
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
            
            //////////////////////// ASSET_IMAGE_2//////////////////
            if (Input::hasFile('asset_image_2')) {
                $indentpic = Input::file('asset_image_2');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_IMAGE_2';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                
                $data = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $asset_id)
                        ->where('document_type', 'ASSET_IMAGE_2')
                        ->where('document_owner_id', $asset_id)
                        ->where('document_owner_type', 'ASSETS')
                        ->first();
                
                if (count($data) > 0) {
                    DB::table('documents')
                            ->where('id', $data->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'ASSET_IMAGE_2';
                    $documentmodel->user_id = $asset_id;
                    $documentmodel->document_owner_id = $asset_id;
                    $documentmodel->document_owner_type = 'ASSETS';
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
            //////////////////////// ASSET_IMAGE_3//////////////////
            if (Input::hasFile('asset_image_3')) {
                $indentpic = Input::file('asset_image_3');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_IMAGE_3';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                
                $data = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $asset_id)
                        ->where('document_type', 'ASSET_IMAGE_3')
                        ->where('document_owner_id', $asset_id)
                        ->where('document_owner_type', 'ASSETS')
                        ->first();
                
                if (count($data) > 0) {
                    DB::table('documents')
                            ->where('id', $data->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'ASSET_IMAGE_3';
                    $documentmodel->user_id = $asset_id;
                    $documentmodel->document_owner_id = $asset_id;
                    $documentmodel->document_owner_type = 'ASSETS';
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
            //////////////////////// ASSET_DOC_1//////////////////
            if (Input::hasFile('asset_doc_1')) {
                $indentpic = Input::file('asset_doc_1');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_DOC_1';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                
                $data = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $asset_id)
                        ->where('document_type', 'ASSET_DOC_1')
                        ->where('document_owner_id', $asset_id)
                        ->where('document_owner_type', 'ASSETS')
                        ->first();
                
                if (count($data) > 0) {
                    DB::table('documents')
                            ->where('id', $data->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'ASSET_DOC_1';
                    $documentmodel->user_id = $asset_id;
                    $documentmodel->document_owner_id = $asset_id;
                    $documentmodel->document_owner_type = 'ASSETS';
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
            //////////////////////// ASSET_DOC_2//////////////////
            if (Input::hasFile('asset_doc_2')) {
                $indentpic = Input::file('asset_doc_2');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_DOC_2';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                
                $data = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $asset_id)
                        ->where('document_type', 'ASSET_DOC_2')
                        ->where('document_owner_id', $asset_id)
                        ->where('document_owner_type', 'ASSETS')
                        ->first();
                
                if (count($data) > 0) {
                    DB::table('documents')
                            ->where('id', $data->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'ASSET_DOC_2';
                    $documentmodel->user_id = $asset_id;
                    $documentmodel->document_owner_id = $asset_id;
                    $documentmodel->document_owner_type = 'ASSETS';
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
            //////////////////////// ASSET_DOC_3//////////////////
            if (Input::hasFile('asset_doc_3')) {
                $indentpic = Input::file('asset_doc_3');
                $extension = time() . '.' . $indentpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/ASSET_DOC_3';
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($indentpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
                
                $data = DB::table('documents')
                        ->select('documents.id')
                        ->where('user_id', $asset_id)
                        ->where('document_type', 'ASSET_DOC_3')
                        ->where('document_owner_id', $asset_id)
                        ->where('document_owner_type', 'ASSETS')
                        ->first();
                
                if (count($data) > 0) {
                    DB::table('documents')
                            ->where('id', $data->id)
                            ->update(['file_url' => $pic_url]);
                } else {
                    $documentmodel = new Document();
                    $documentmodel->document_type = 'ASSET_DOC_3';
                    $documentmodel->user_id = $asset_id;
                    $documentmodel->document_owner_id = $asset_id;
                    $documentmodel->document_owner_type = 'ASSETS';
                    $documentmodel->file_url = $pic_url;
                    $documentmodel->save();
                }
            }
            
            
            Toastr::success('Asset Successfully Updated!', $title = null, $options = []);
            return Redirect::to('assets/editlist');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('assets/editlist');
        }
    }
    
    public function editlist(Request $request) {
        
        $paginate = Config::get('app.PAGINATE');
        
        
        $countries = DB::table('country')
                        ->select('country.*')
                        ->orderby('name', 'ASC')->get();
        
        $assets = DB::table('ac_assets')
                ->select('ac_assets.*',db::raw("concat(ac_party.first_name,' ',ac_party.alias_name) as supplier_name"))
                ->leftjoin('ac_party', 'ac_assets.supplier_id', '=', 'ac_party.id')
                ->where('ac_assets.status', '!=', 2)
                ->orderby('ac_assets.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $searchbycode = Input::get('searchbycode');
            $search_name = Input::get('search_name');
            $search_sname = Input::get('search_sname');
            $purchase_from = Input::get('purchase_from');
            $purchase_to = Input::get('purchase_to');
            $status = Input::get('status');
            
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            $sortordsname = Input::get('sortordsname');
            $sortorddate = Input::get('sortorddate');
            
            if ($purchase_from != '') {
                $purchase_from = explode('-', $purchase_from);
                $purchase_from = $purchase_from[2] . '-' . $purchase_from[1] . '-' . $purchase_from[0];
            }
            if ($purchase_to != '') {
                $purchase_to = explode('-', $purchase_to);
                $purchase_to = $purchase_to[2] . '-' . $purchase_to[1] . '-' . $purchase_to[0];
            }
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortordcode=='' && $search_sname=='' && $sortorddate==''){
                $sortOrdDefault='DESC';
            }
            
            $assets = DB::table('ac_assets')
                    ->select('ac_assets.*',db::raw("concat(ac_party.first_name,' ',ac_party.alias_name) as supplier_name"))
                    ->leftjoin('ac_party', 'ac_assets.supplier_id', '=', 'ac_party.id')
                    ->where('ac_assets.status', '!=', 2)
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("ac_assets.code like '$searchbycode%'");
                    })
                    ->when($search_name, function ($query) use ($search_name) {
                        return $query->whereRaw("(ac_assets.name like '$search_name%' or concat(ac_assets.name,' ',ac_assets.alias_name) like '$search_name%')");
                    })
                    ->when($search_sname, function ($query) use ($search_sname) {
                        return $query->whereRaw("(ac_party.first_name like '$search_sname%' or concat(ac_party.first_name,' ',ac_party.alias_name,' ',ac_party.last_name) like '$search_sname%')");
                    })
                    ->when($purchase_from, function ($query) use ($purchase_from) {
                        return $query->whereRaw("ac_assets.purchase_date >= '$purchase_from' ");
                    })
                    ->when($purchase_to, function ($query) use ($purchase_to) {
                        return $query->whereRaw("ac_assets.purchase_date<= '$purchase_to' ");
                    })
                   
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('ac_assets.name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('ac_assets.code', $sortordcode);
                    })
                    ->when($sortordsname, function ($query) use ($sortordsname) {
                        return $query->orderby('ac_party.first_name', $sortordsname);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('ac_assets.purchase_date', $sortorddate);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('ac_assets.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);
                    
            return view('ledgers/assets/editresults', array('assets' => $assets));
        }
        
        return view('ledgers/assets/editindex', array('assets' => $assets, 'countries' => $countries));
    }

    
    // Generate PDF funcion
    public function exportdata() {
        
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
         
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $search_name = Input::get('search_name');
        $search_sname = Input::get('search_sname');
        $purchase_from = Input::get('purchase_from');
        $purchase_to = Input::get('purchase_to');
        $status = Input::get('status');

        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortordsname = Input::get('sortordsname');
        $sortorddate = Input::get('sortorddate');

        if ($purchase_from != '') {
            $purchase_from = explode('-', $purchase_from);
            $purchase_from = $purchase_from[2] . '-' . $purchase_from[1] . '-' . $purchase_from[0];
        }
        if ($purchase_to != '') {
            $purchase_to = explode('-', $purchase_to);
            $purchase_to = $purchase_to[2] . '-' . $purchase_to[1] . '-' . $purchase_to[0];
        }

        $sortOrdDefault='';
        if($sortordname=='' && $sortordcode=='' && $search_sname=='' && $sortorddate==''){
            $sortOrdDefault='DESC';
        }

        $assets = DB::table('ac_assets')
                ->select('ac_assets.*',db::raw("concat(ac_party.first_name,' ',ac_party.alias_name) as supplier_name"))
                ->leftjoin('ac_party', 'ac_assets.supplier_id', '=', 'ac_party.id')
                ->where('ac_assets.status', '!=', 2)
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("ac_assets.code like '$searchbycode%'");
                })
                ->when($search_name, function ($query) use ($search_name) {
                    return $query->whereRaw("(ac_assets.name like '$search_name%' or concat(ac_assets.name,' ',ac_assets.alias_name) like '$search_name%')");
                })
                ->when($search_sname, function ($query) use ($search_sname) {
                    return $query->whereRaw("(ac_party.first_name like '$search_sname%' or concat(ac_party.first_name,' ',ac_party.alias_name,' ',ac_party.last_name) like '$search_sname%')");
                })
                ->when($purchase_from, function ($query) use ($purchase_from) {
                    return $query->whereRaw("ac_assets.purchase_date >= '$purchase_from' ");
                })
                ->when($purchase_to, function ($query) use ($purchase_to) {
                    return $query->whereRaw("ac_assets.purchase_date<= '$purchase_to' ");
                })
                ->when($status, function ($query) use ($status) {
                        if($status==-1){
                            $status=0;
                        }
                        return $query->where('ac_assets.status', '=', $status);
                    })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('ac_assets.name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('ac_assets.code', $sortordcode);
                })
                ->when($sortordsname, function ($query) use ($sortordsname) {
                    return $query->orderby('ac_party.first_name', $sortordsname);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('ac_assets.purchase_date', $sortorddate);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('ac_assets.created_at', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('AssetsList', function($excel) use($assets){
                 // Set the title
                $excel->setTitle('Assets List');
                
                $excel->sheet('Assets List', function($sheet) use($assets){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('D3', 'Assets List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:H3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Asset Code','Asset Name',"Supplier Name","Purchase Date","Purchase Value",'Asset Value',"Status"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:H5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    $asset_name="";
                    for($i=0;$i<count($assets);$i++){
                        $asset_name = $assets[$i]->name." ".$assets[$i]->alias_name;
                        if( $assets[$i]->status== 1){
                            $status="Enabled";
                        }else{
                            $status="Disabled";
                        }
                        
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $assets[$i]->code);
                        $sheet->setCellValue('C'.$chrRow, $asset_name);
                        $sheet->setCellValue('D'.$chrRow, $assets[$i]->supplier_name);
                        $sheet->setCellValue('E'.$chrRow, date("d-m-Y", strtotime($assets[$i]->purchase_date)));
                        $sheet->setCellValue('F'.$chrRow, $assets[$i]->purchase_value);
                        $sheet->setCellValue('G'.$chrRow, $assets[$i]->asset_value);
                        $sheet->setCellValue('H'.$chrRow, $status);
                            
                        $sheet->cells('A'.$chrRow.':H'.$chrRow, function($cells) {
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
                <div style="text-align:center;"><h2>Assets List</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Asset Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Asset Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Supplier Name </td>
                                <td style="padding:10px 5px;color:#fff;"> Purchase Date </td>
                                <td style="padding:10px 5px;color:#fff;"> Purchase Value </td>
                                <td style="padding:10px 5px;color:#fff;"> Asset Value </td>
                                <td style="padding:10px 5px;color:#fff;"> Status </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
           $slno=0;
            foreach ($assets as $asset) {
                 $asset_name = $asset->name." ". $asset->alias_name;
                        if($asset->status== 1){
                            $status="Enabled";
                        }else{
                            $status="Disabled";
                        }
                
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset->code . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset->supplier_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . date("d-m-Y", strtotime($asset->purchase_date)) . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset->purchase_value . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset->asset_value . '</td>
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
            return $pdf->download('mtg_asset_list.pdf');
        }
    }   
    
    // Generate PDF funcion
    public function exportdataedit() {
        
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 3000);
         
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $search_name = Input::get('search_name');
        $search_sname = Input::get('search_sname');
        $purchase_from = Input::get('purchase_from');
        $purchase_to = Input::get('purchase_to');
        $status = Input::get('status');

        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');
        $sortordsname = Input::get('sortordsname');
        $sortorddate = Input::get('sortorddate');

        if ($purchase_from != '') {
            $purchase_from = explode('-', $purchase_from);
            $purchase_from = $purchase_from[2] . '-' . $purchase_from[1] . '-' . $purchase_from[0];
        }
        if ($purchase_to != '') {
            $purchase_to = explode('-', $purchase_to);
            $purchase_to = $purchase_to[2] . '-' . $purchase_to[1] . '-' . $purchase_to[0];
        }

        $sortOrdDefault='';
        if($sortordname=='' && $sortordcode=='' && $search_sname=='' && $sortorddate==''){
            $sortOrdDefault='DESC';
        }

        $assets = DB::table('ac_assets')
                ->select('ac_assets.*',db::raw("concat(ac_party.first_name,' ',ac_party.alias_name) as supplier_name"))
                ->leftjoin('ac_party', 'ac_assets.supplier_id', '=', 'ac_party.id')
                ->where('ac_assets.status', '!=', 2)
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("ac_assets.code like '$searchbycode%'");
                })
                ->when($search_name, function ($query) use ($search_name) {
                    return $query->whereRaw("(ac_assets.name like '$search_name%' or concat(ac_assets.name,' ',ac_assets.alias_name) like '$search_name%')");
                })
                ->when($search_sname, function ($query) use ($search_sname) {
                    return $query->whereRaw("(ac_party.first_name like '$search_sname%' or concat(ac_party.first_name,' ',ac_party.alias_name,' ',ac_party.last_name) like '$search_sname%')");
                })
                ->when($purchase_from, function ($query) use ($purchase_from) {
                    return $query->whereRaw("ac_assets.purchase_date >= '$purchase_from' ");
                })
                ->when($purchase_to, function ($query) use ($purchase_to) {
                    return $query->whereRaw("ac_assets.purchase_date<= '$purchase_to' ");
                })

                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('ac_assets.name', $sortordname);
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('ac_assets.code', $sortordcode);
                })
                ->when($sortordsname, function ($query) use ($sortordsname) {
                    return $query->orderby('ac_party.first_name', $sortordsname);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('ac_assets.purchase_date', $sortorddate);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('ac_assets.created_at', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('AssetsList', function($excel) use($assets){
                 // Set the title
                $excel->setTitle('Assets List');
                
                $excel->sheet('Assets List', function($sheet) use($assets){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('D3', 'Assets List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:G3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Asset Code','Asset Name',"Supplier Name","Purchase Date","Purchase Value",'Asset Value'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:G5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    $asset_name="";
                    for($i=0;$i<count($assets);$i++){
                        $asset_name = $assets[$i]->name." ".$assets[$i]->alias_name;
                        
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $assets[$i]->code);
                        $sheet->setCellValue('C'.$chrRow, $asset_name);
                        $sheet->setCellValue('D'.$chrRow, $assets[$i]->supplier_name);
                        $sheet->setCellValue('E'.$chrRow, date("d-m-Y", strtotime($assets[$i]->purchase_date)));
                        $sheet->setCellValue('F'.$chrRow, $assets[$i]->purchase_value);
                        $sheet->setCellValue('G'.$chrRow, $assets[$i]->asset_value);
                            
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
                <div style="text-align:center;"><h2>Assets List</h2></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Asset Code</td>
                                <td style="padding:10px 5px;color:#fff;"> Asset Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Supplier Name </td>
                                <td style="padding:10px 5px;color:#fff;"> Purchase Date </td>
                                <td style="padding:10px 5px;color:#fff;"> Purchase Value </td>
                                <td style="padding:10px 5px;color:#fff;"> Asset Value </td>
                            </tr>
                        </thead>
                        <tbody class="branchbody" id="branchbody" >';
           $slno=0;
            foreach ($assets as $asset) {
                 $asset_name = $asset->name." ". $asset->alias_name;
                
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset->code . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset->supplier_name . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . date("d-m-Y", strtotime($asset->purchase_date)) . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset->purchase_value . '</td>
                                    <td style="color: #535352; font-size: 12px;padding: 10px 5px;">' . $asset->asset_value . '</td>
                               </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_asset_list.pdf');
        }
    }   
    

      
      
}