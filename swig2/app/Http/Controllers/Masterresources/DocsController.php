<?php

namespace App\Http\Controllers\Masterresources;
use Illuminate\Support\Facades\Config;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Http\Requests;
use Kamaln7\Toastr\Facades\Toastr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Models\Masterresources;
use App\Models\Company;
use App\Models\Document;
use DB;
class DocsController extends Controller {

    public function index() 
       {
                
   
       }

    public function add()
        {
                $companies = Company::all(); 
                $regions = DB::table('master_resources')
                ->join('companies', 'master_resources.company_id', '=', 'companies.id')
                ->select('master_resources.*', 'companies.name as company_name')
                ->where(['resource_type' => 'REGION'])->where('master_resources.status', '=', 1)->get();
                return view('masterresources/docs/add', array('title' => 'Region Listing', 'description' => '', 'page' => 'regions', 'companies' => $companies,'regions' => $regions));
   
        
        }

    public function store(request $request) 
        { 
         try{
              if (Session::get('company'))
            { 
            $company_id = Session::get('company');  
            }
                $documentmodel = new Document;
                $world=Config::get('app.WORLD');
                $s3 = \Storage::disk('s3');
                $image = Input::file('docs');
                $document_type=Input::get('document_type'); 
                $extension = time() . '.' . $image->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket').$world.'/';  
                $filePath = $filePath.$document_type .'/'. $extension;   
                $s3filepath = $s3->put($filePath, file_get_contents($image), 'public'); 
                $doc_url=Storage::disk('s3')->url($filePath);
                $documentmodel->name = Input::get('name');
                $documentmodel->alias_name = Input::get('alias_name');
                $documentmodel->document_type = Input::get('document_type'); 
                $documentmodel->document_owner_type = Input::get('document_type');         
                $documentmodel->company_id = $company_id;
                $documentmodel->file_url = $doc_url;
                $documentmodel->status = 1;         
                $documentmodel->save();
                Toastr::success('Successfully Uploaded!', $title = null, $options = []);
                return Redirect::to('masterresources/docs/add');
            }
            catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/docs/add');
            }
           
        }
   
                        
}
