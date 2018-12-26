<?php

namespace App\Http\Controllers\Mis;
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
class KpiController extends Controller
{
     public function index() {
                            $paginate=Config::get('app.PAGINATE');
                            $companies = Company::all();
                            $documents = DB::table('documents')
                            ->join('companies', 'documents.company_id', '=', 'companies.id')
                            ->select('documents.*', 'companies.name as company_name')
                            ->where('documents.document_type', '=', 'KPI')
                            ->where('documents.document_owner_type', '=', 'KPI')
                            ->where('documents.status', '!=', 2)                            
                            ->paginate($paginate);
                             return view('mis/kpi/index',array('companies' => $companies,'documents' => $documents));
   }
   
   
                                      
       public function Disable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('documents')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 0]);
                Toastr::success('Document Successfully Disabled', $title = null, $options = []);
                return Redirect::to('mis/kpi');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('mis/kpi');
            
           }
        } 
       public function Enable($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('documents')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 1]);
                Toastr::success('Document Successfully Enabled', $title = null, $options = []);
                return Redirect::to('mis/kpi');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('mis/kpi');
            
           }
        }
    
    public function delete($id)
        {
           try
           {
                $dn=\Crypt::decrypt($id);
                $companies = DB::table('documents')
                                ->where(['id' => $dn])                            
                                ->update(['status' => 2]);
                Toastr::success('Document Successfully Deleted', $title = null, $options = []);
                return Redirect::to('mis/kpi');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('mis/kpi');
            
           }
        }
}
