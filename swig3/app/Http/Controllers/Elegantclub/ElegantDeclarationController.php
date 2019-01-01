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
use App\Models\Declaration;
use DB;

class ElegantDeclarationController extends Controller {

    public function index(Request $request) {
        try {
            if ($request->ajax()) {
                $paginate = Input::get('pagelimit');
                if (empty($paginate)) {
                    $paginate = Config::get('app.PAGINATE');
                }
                $searchbytitle = Input::get("searchbytitle");
                $searchbycontent = Input::get("searchbycontent");
                
                $declarations = DB::table('declaration as a')
                        ->select(DB::raw("a.id,a.title,a.declaration_content,a.created_at,a.updated_at, "
                                . "(select b.name FROM companies b where b.id=a.company_id) as company_name,"
                                . "(if(a.created_by > 0,(select c.first_name FROM employees c where c.id = a.created_by),'-')) as created_by"))
                        ->where('a.status', '=', 1)
                        ->when($searchbytitle, function($qry) use ($searchbytitle){
                            return $qry->whereraw("(a.title like '%$searchbytitle%')");
                        })
                        ->when($searchbycontent , function ($qry) use ($searchbycontent){
                            return $qry->whereraw("(a.declaration_content like '%$searchbycontent%')");
                        })
                        ->paginate($paginate);
                return view('elegantclub/elegant_declaration/result', array('declarations' => $declarations));
            } else {
                $paginate = Config::get('app.PAGINATE');
                $declarations = DB::table('declaration as a')
                        ->select(DB::raw("a.id,a.title,a.declaration_content,a.created_at,a.updated_at, "
                                . "(select b.name FROM companies b where b.id=a.company_id) as company_name,"
                                . "(if(a.created_by > 0,(select c.first_name FROM employees c where c.id = a.created_by),'-')) as created_by"))
                        ->where('a.status', '=', 1)
                        ->paginate($paginate);
                return view('elegantclub/elegant_declaration/index', array('declarations' => $declarations));
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            die();
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub');
        }
    }

    public function add() {
        try {
            return view('elegantclub/elegant_declaration/add');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('elegantclub/declaration');
        }
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $data = Input::all();
            $decl_obj = new Declaration;
            $decl_obj->title = $data['title'];
            $decl_obj->declaration_content = $data['content'];
            $decl_obj->created_by = Session::get("login_id");
            $decl_obj->company_id = $company_id;
            $decl_obj->status = 1;
            $save = $decl_obj->save();
            if ($save) {
                    Toastr::success('Declaration Added Successfully', $title = null, $options = []);
            } 
            else {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            }
            return Redirect::to('elegantclub/declaration');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!!', $title = null, $options = []);
            return Redirect::to('elegantclub/declaration');
        }
    }
    public function update(){
        try{
            $ele_dec_id = Input::get("decl_id");
            $dec_id = \Crypt::decrypt($ele_dec_id);
            $Declaration = new Declaration();
            $Declaration->id = $dec_id;
            $Declaration->exists = TRUE;
            $Declaration->title = Input::get("title");
            $Declaration->declaration_content = Input::get("content");
            $Declaration->created_by = Session::get("login_id");
            $Declaration->company_id = Session::get("company");
            $Declaration->status = 1;
            if($Declaration->save() === true){
                Toastr::success('Declaration Updated Successfully', $title = null, $options = []);
                return Redirect::to('elegantclub/declaration');
            }
            else{
                Toastr::error('Sorry Nothing to change!!', $title = null, $options = []);
                return Redirect::to('elegantclub/declaration');
            }
        }
        catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!!', $title = null, $options = []);
            return Redirect::to('elegantclub/declaration');
        }
    }
    public function edit($ele_dec_id){
        try{
            $dec_id = \Crypt::decrypt($ele_dec_id);
            $declarations = DB::table("declaration")
                    ->select(DB::raw("id,title,declaration_content"))
                    ->where("id",'=',$dec_id)
                    ->first();
            return view('elegantclub/elegant_declaration/edit', array('declarations' => $declarations));
        }
        catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!!', $title = null, $options = []);
            return Redirect::to('elegantclub/declaration');
        }
    }
    public function delete($dec_id){
        try{
            $dec_del_id = \Crypt::decrypt($dec_id);
            $delete_ed = DB::table("declaration")
                    ->where("id","=",$dec_del_id)
                    ->update(['status'=>'2']);
            if($delete_ed > 0){
                Toastr::success('Declaration Deleted Successfully', $title = null, $options = []);
                return Redirect::to('elegantclub/declaration');
            }
            else{
                Toastr::error('Sorry There Was Some Problem!!', $title = null, $options = []);
                return Redirect::to('elegantclub/declaration');
            }
        }
        catch(\Exception $e){
            Toastr::error('Sorry There Was Some Problem!!', $title = null, $options = []);
            return Redirect::to('elegantclub/declaration');
        }
    }

}
