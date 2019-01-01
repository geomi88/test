<?php

namespace App\Http\Controllers\Masterresources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use App\Models\Company;
use App\Models\Masterresources;
use DB;

class InventorysubcategoryController extends Controller {

    public function index() {
        $inventory_sub_categories = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'INVENTORY_SUB_CATEGORY')->where('status', '!=', 2)->orderby('name','ASC')
                ->paginate(4);
        return view('masterresources/inventory_sub_category/index', array('inventory_sub_categories' => $inventory_sub_categories));
    }

    public function add() {
       
        
        $inventory_categories = DB::table('master_resources')
                ->select('master_resources.*')
                ->where(['resource_type' => 'INVENTORY_CATEGORY'])->orderby('name','ASC')->get();
        return view('masterresources/inventory_sub_category/add',array('inventory_categories' => $inventory_categories));
    }

    public function store() {
        try {
            $masterresourcemodel = new Masterresources;
            $masterresourcemodel->name = Input::get('sub_cat_name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->resource_type = 'INVENTORY_SUB_CATEGORY';
            $masterresourcemodel->inventory_category_id = Input::get('inventory_category_id');
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();
            Toastr::success('Successfully Added!', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_sub_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_sub_category/add');
        }
    }

    public function checksubcategories() {
        $name = Input::get('subcatName');
        $id = Input::get('id');
        $data = DB::table('master_resources')
                ->select('master_resources.name')
                ->where('name', '=', $name)->where('resource_type', '=', 'INVENTORY_SUB_CATEGORY')->where('id', '!=', $id)
                ->get();
        echo count($data);
    }

    public function edit($id) {

    }

    public function update() {
       
    }

    public function Disable($id) {
        
    }

    public function Enable($id) {
        
    }

    public function delete($id) {
       
    }

}
