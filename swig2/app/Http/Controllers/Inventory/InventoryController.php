<?php

namespace App\Http\Controllers\Inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Hash;
use App\Models\Masterresources;
use Illuminate\Support\Facades\Config;
use App\Models\Company;
use App\Models\Module;
use App\Models\Inventory;
use App\Models\Inventory_alternate_units;
use App\Models\Usermodule;
use App\Models\Inventory_barcode;
use App\Models\Barcode;
use App;
use DB;
use Mail;

class InventoryController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $category = DB::table('master_resources')
                        ->select('master_resources.id', 'master_resources.name')
                        ->where(['resource_type' => 'INVENTORY_CATEGORY'])
                        ->where('status', '=', 1)
                        ->where('company_id', '=', $company_id)
                        ->orderby('name', 'ASC')->get();

        $group = DB::table('master_resources')
                        ->select('master_resources.id', 'master_resources.name')
                        ->where(['resource_type' => 'INVENTORY_GROUP'])
                        ->where('status', '=', 1)
                        ->orderby('name', 'ASC')->get();

        $warehouses = DB::table('master_resources')
                        ->select('master_resources.id', 'master_resources.name')
                        ->where(['resource_type' => 'WAREHOUSE'])
                        ->where('status', '=', 1)
                        ->where('company_id', '=', $company_id)
                        ->orderby('name', 'ASC')->get();

        $inventories = DB::table('inventory')
                ->select('inventory.*', 'category.name as category_name', 'group.name as grp_name','primary.name as primearyunit', DB::raw("group_concat(units.name,' (',altu.conversion_value,' ',primary.name,')') as altunits"))
                ->leftjoin('master_resources as category', 'inventory.inventory_category_id', '=', 'category.id')
                ->leftjoin('inventory_alternate_units as altu', 'inventory.id', '=', 'altu.inventory_item_id')
                ->leftjoin('units', 'altu.unit_id', '=', 'units.id')
                ->leftjoin('units as primary', 'inventory.primary_unit', '=', 'primary.id')
                ->leftjoin('master_resources as group', 'inventory.inventory_group_id', '=', 'group.id')
                ->where([ 'inventory.company_id' => $company_id])
                ->whereraw('inventory.status != 2')
                ->groupBy('inventory.id')
                ->orderby('inventory.created_at', 'DESC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $searchbyname = Input::get('searchbyname');
            $searchbypcode = Input::get('searchbypcode');
            $searchbyscode = Input::get('searchbyscode');
            $searchbygroup = Input::get('searchbygroup');
            $searchbycategory = Input::get('searchbycategory');
           


            $inventories = DB::table('inventory')
                    ->select('inventory.*', 'category.name as category_name', 'group.name as grp_name','primary.name as primearyunit', DB::raw("group_concat(units.name,' (',altu.conversion_value,' ',primary.name,')') as altunits"))
                    ->leftjoin('master_resources as category', 'inventory.inventory_category_id', '=', 'category.id')
                    ->leftjoin('inventory_alternate_units as altu', 'inventory.id', '=', 'altu.inventory_item_id')
                    ->leftjoin('units', 'altu.unit_id', '=', 'units.id')
                    ->leftjoin('units as primary', 'inventory.primary_unit', '=', 'primary.id')
                    ->leftjoin('master_resources as group', 'inventory.inventory_group_id', '=', 'group.id')
                    ->where([ 'inventory.company_id' => $company_id])
                    ->whereraw('inventory.status != 2')
                    ->when($searchbypcode, function ($query) use ($searchbypcode) {
                        return $query->whereRaw("(inventory.product_code like '%$searchbypcode%')");
                    })
                    ->when($searchbyscode, function ($query) use ($searchbyscode) {
                        return $query->whereRaw("(inventory.supplier_icode like '%$searchbyscode%')");
                    })
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(inventory.name like '%$searchbyname%')");
                    })
                    ->when($searchbycategory, function ($query) use ($searchbycategory) {
                        return $query->whereRaw("(inventory.inventory_category_id = $searchbycategory)");
                    })
                    ->when($searchbygroup, function ($query) use ($searchbygroup) {
                        return $query->whereRaw("(inventory.inventory_group_id = $searchbygroup)");
                    })
                    ->groupBy('inventory.id')
                    ->orderby('inventory.created_at', 'DESC')
                    ->paginate($paginate);

            return view('inventory/inventory_items/result', array('inventories' => $inventories));
        }

        return view('inventory/inventory_items/index', array('inventories' => $inventories,
            'groups' => $group,
            'warehouses' => $warehouses,
            'categories' => $category));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $inventory_groups = DB::table('master_resources')
                        ->select('master_resources.*')
                        ->where(['resource_type' => 'INVENTORY_GROUP'])->get();

        $parentcategory = DB::table('master_resources as m1')
                ->select('m1.id', 'm1.name as name', db::raw("(select count(id) from master_resources where inventory_category_id=m1.id) as childcount"))
                ->whereRaw("m1.status=1 AND m1.resource_type='INVENTORY_CATEGORY' AND m1.company_id=$company_id")
                ->whereRaw("m1.inventory_category_id IS NULL")
                ->get();


        $parentids = $parentcategory->pluck("id")->toArray();

        $childlevel1 = DB::table('master_resources as m')
                ->select('m.id', 'm.name as name', 'm.inventory_category_id as parentid', db::raw("(select count(id) from master_resources where inventory_category_id=m.id) as childcount "))
                ->whereRaw("m.status=1 AND m.resource_type='INVENTORY_CATEGORY' AND m.company_id=$company_id")
                ->whereRaw("m.inventory_category_id IS NOT NULL")
                ->whereIn("m.inventory_category_id", $parentids)
                ->get();

        $units = DB::table('units')
                ->select('units.*')->where('company_id', '=', $company_id)
                ->where('status', '=', 1)
                ->where('unit_type', '=', 'SIMPLE')
                ->orderby('name', 'ASC')
                ->get();

        $warehouse = DB::table('master_resources')
                ->select('master_resources.*')->where('company_id', '=', $company_id)
                ->where('status', '=', 1)
                ->where('resource_type', '=', 'WAREHOUSE')
                ->orderby('name', 'ASC')
                ->get();
        $barcodes = DB::table('barcodes')
                ->select('barcodes.*', 'employees.first_name as created_by_name')
                ->leftjoin('employees', 'employees.id', '=', 'barcodes.created_by')
                ->where(['barcodes.is_used' => 0, 'barcodes.status' => 1])
                ->orderby('barcodes.created_at', 'DESC')
                ->get();
        $companies = DB::table('companies')
                ->select('companies.*')
                ->where('status', '=', 1)
                ->orderby('name', 'ASC')
                ->get();

        return view('inventory/inventory_items/add', array(
            'inventory_groups' => $inventory_groups,
            'units' => $units,
            'parentcategory' => $parentcategory,
            'childlevel1' => $childlevel1,
            'warehouses' => $warehouse,
            'companies' => $companies,
            'barcodes' => $barcodes));
    }

    public function store() {
        try {
            $subfolder = Input::get('p_code');
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');

            $arrUnits = Input::get('altunitdetails');
            $arrUnits = json_decode($arrUnits);

            $inventorymodel = new Inventory;
            $inventorymodel->product_code = Input::get('p_code');
            $inventorymodel->name = Input::get('name');
            $inventorymodel->company_id = Input::get('company_id');
            $inventorymodel->inventory_group_id = Input::get('inventory_group_id');
            $inventorymodel->inventory_category_id = Input::get('inventory_category_id');
            $inventorymodel->primary_unit = Input::get('cmbprimaryunit');
            $inventorymodel->description = Input::get('description');
            $inventorymodel->supplier_icode = Input::get('supplier_icode');
            $inventorymodel->track_manufacturing = Input::get('trackmanufacturing');
            $inventorymodel->track_expiry = Input::get('trackexpiry');
            $inventorymodel->status = 1;

            $pic_url = '';

            if (Input::hasFile('productpic')) {
                $productpic = Input::file('productpic');
                $extension = time() . '.' . $productpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/pics/';
                $filePath = $filePath . $subfolder . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($productpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
            }

            $inventorymodel->pic_url = $pic_url;
            $inventorymodel->save();
            $latest_id = $inventorymodel->id;

            foreach ($arrUnits as $arrUnit) {
                $alternateUnits = new Inventory_alternate_units();
                $alternateUnits->inventory_item_id = $latest_id;
                $alternateUnits->unit_id = $arrUnit->unit_id;
                $alternateUnits->conversion_value = $arrUnit->conversion_value;

                $alternateUnits->save();
            }
            if (Input::get('barcode') > 0) {
                $inventory_barcode_model = new Inventory_barcode;
                $inventory_barcode_model->inventory_id = $latest_id;
                $inventory_barcode_model->barcode_id = Input::get('barcode');
                $inventory_barcode_model->save();
                $barcode_status = DB::table('barcodes')
                        ->where(['id' => Input::get('barcode')])
                        ->update(['is_used' => 1]);
            }


            Toastr::success('Inventory Successfully Added!', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        }
    }

    public function update() {
        try {
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            $subfolder = Input::get('p_code');
            $s3 = \Storage::disk('s3');
            $world = Config::get('app.WORLD');

            $arrUnits = Input::get('altunitdetails');
            $arrUnits = json_decode($arrUnits);

            $inventorymodel = new Inventory;
            $product_code = Input::get('p_code');
            $name = Input::get('name');
            $company_id = Input::get('company_id');
            $inventory_group_id = Input::get('inventory_group_id');
            $inventory_category_id = Input::get('inventory_category_id');
            $primary_unit = Input::get('cmbprimaryunit');

            $supplier_icode = Input::get('supplier_icode');
            $description = Input::get('description');
            $status = 1;

            $pic_url = '';
            if (Input::hasFile('productpic')) {
                $productpic = Input::file('productpic');
                $extension = time() . '.' . $productpic->getClientOriginalExtension();
                $filePath = config('filePath.s3.bucket') . $world . '/product_pics/';
                $filePath = $filePath . $subfolder . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($productpic), 'public');
                $pic_url = Storage::disk('s3')->url($filePath);
            }

            $inventory = DB::table('inventory')
                    ->where(['id' => $id])
                    ->update([
                'product_code' => $product_code,
                'name' => $name,
                'company_id' => $company_id,
                'inventory_group_id' => $inventory_group_id,
                'inventory_category_id' => $inventory_category_id,
                'supplier_icode' => $supplier_icode,
                'track_manufacturing' => Input::get('trackmanufacturing'),
                'track_expiry' => Input::get('trackexpiry'),
                'description' => $description
            ]);



            $delete = DB::table('inventory_alternate_units')
                    ->where('inventory_item_id', '=', $id)
                    ->delete();

            foreach ($arrUnits as $arrUnit) {
                $alternateUnits = new Inventory_alternate_units();
                $alternateUnits->inventory_item_id = $id;
                $alternateUnits->unit_id = $arrUnit->unit_id;
                $alternateUnits->conversion_value = $arrUnit->conversion_value;

                $alternateUnits->save();
            }

            if ($pic_url != '') {
                $inventory = DB::table('inventory')
                        ->where(['id' => $id])
                        ->update(['pic_url' => $pic_url]);
            }

            /* Inventory Barcode Updation */
            $inventory_barcode_details = DB::table('inventory_barcodes')
                    ->select('inventory_barcodes.barcode_id', 'inventory_barcodes.id')
                    ->where(['inventory_barcodes.inventory_id' => $id, 'inventory_barcodes.is_active' => 1])
                    ->first();
            if (count($inventory_barcode_details) > 0) {
                $inventory_barcode_id = $inventory_barcode_details->id;
                $released_barcode_id = $inventory_barcode_details->barcode_id;
                $barcode_status = DB::table('barcodes')
                        ->where(['id' => $released_barcode_id])
                        ->update(['is_used' => 0]);
                $inventory_barcodes = DB::table('inventory_barcodes')
                        ->where(['id' => $inventory_barcode_id])
                        ->update(['is_active' => 0, 'deallocated_date' => date('Y-m-d H:i:s')]);
            }
            if (Input::get('barcode') > 0) {
                $inventory_barcode_model = new Inventory_barcode;
                $inventory_barcode_model->inventory_id = $id;
                $inventory_barcode_model->barcode_id = Input::get('barcode');
                $inventory_barcode_model->save();
                $barcode_status = DB::table('barcodes')
                        ->where(['id' => Input::get('barcode')])
                        ->update(['is_used' => 1]);
            }
            /**/


            Toastr::success('Inventory Successfully Updated', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items/edit/' . $dn);
        }
    }

    public function edit($id) {
        try {

            $dn = \Crypt::decrypt($id);
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $inventory_groups = DB::table('master_resources')
                            ->select('master_resources.*')
                            ->where(['resource_type' => 'INVENTORY_GROUP'])->get();

            $parentcategory = DB::table('master_resources as m1')
                    ->select('m1.id', 'm1.name as name', db::raw("(select count(id) from master_resources where inventory_category_id=m1.id) as childcount"))
                    ->whereRaw("m1.status=1 AND m1.resource_type='INVENTORY_CATEGORY' AND m1.company_id=$company_id")
                    ->whereRaw("m1.inventory_category_id IS NULL")
                    ->get();


            $parentids = $parentcategory->pluck("id")->toArray();

            $childlevel1 = DB::table('master_resources as m')
                    ->select('m.id', 'm.name as name', 'm.inventory_category_id as parentid', db::raw("(select count(id) from master_resources where inventory_category_id=m.id) as childcount "))
                    ->whereRaw("m.status=1 AND m.resource_type='INVENTORY_CATEGORY' AND m.company_id=$company_id")
                    ->whereRaw("m.inventory_category_id IS NOT NULL")
                    ->whereIn("m.inventory_category_id", $parentids)
                    ->get();

            $units = DB::table('units')
                    ->select('units.*')->where('company_id', '=', $company_id)
                    ->where('status', '=', 1)
                    ->where('unit_type', '=', 'SIMPLE')
                    ->orderby('name', 'ASC')
                    ->get();

            $warehouse = DB::table('master_resources')
                    ->select('master_resources.*')->where('company_id', '=', $company_id)
                    ->where('status', '=', 1)
                    ->where('resource_type', '=', 'WAREHOUSE')
                    ->orderby('name', 'ASC')
                    ->get();

            $selectedUnits = DB::table('inventory_alternate_units')
                            ->select('inventory_alternate_units.unit_id')
                            ->where(['inventory_item_id' => $dn])
                            ->pluck('unit_id')->toArray();

            $inventory_data = DB::table('inventory')
                    ->select('inventory.*', 'category.name as category_name')
                    ->leftjoin('master_resources as category', 'inventory.inventory_category_id', '=', 'category.id')
                    ->where(['inventory.id' => $dn])
                    ->first();

            $unit_id = $inventory_data->primary_unit;


            $alternate_units = DB::table('units')
                    ->select('units.id as unit_id', 'units.name as unit_name', 'units.conversion_value')->where('company_id', '=', $company_id)
                    ->where('status', '=', 1)
                    ->where('unit_type', '=', 'SIMPLE')
                    ->where('id', '!=', $unit_id)
                    ->orderby('name', 'ASC')
                    ->get();
            $inventory_selectedUnits = DB::table('inventory_alternate_units')
                    ->select('inventory_alternate_units.unit_id', 'inventory_alternate_units.conversion_value', 'units.name as unit_name')
                    ->leftjoin('units', 'units.id', '=', 'inventory_alternate_units.unit_id')
                    ->where(['inventory_item_id' => $dn])
                    ->get();

            $inventory_barcode = DB::table('inventory_barcodes')
                    ->select('inventory_barcodes.barcode_id', 'barcodes.barcode_string')
                    ->leftjoin('barcodes', 'inventory_barcodes.barcode_id', '=', 'barcodes.id')
                    ->where(['inventory_barcodes.inventory_id' => $dn, 'inventory_barcodes.is_active' => 1])
                    ->first();
            $barcodes = DB::table('barcodes')
                    ->select('barcodes.*', 'employees.first_name as created_by_name')
                    ->leftjoin('employees', 'employees.id', '=', 'barcodes.created_by')
                    ->where(['barcodes.is_used' => 0, 'barcodes.status' => 1])
                    ->orderby('barcodes.created_at', 'DESC')
                    ->get();
            $companies = DB::table('companies')
                ->select('companies.*')
                ->where('status', '=', 1)
                ->orderby('name', 'ASC')
                ->get();
            return view('inventory/inventory_items/edit', array(
                'inventory_groups' => $inventory_groups,
                'parentcategory' => $parentcategory,
                'childlevel1' => $childlevel1,
                'units' => $units,
                'inventory_data' => $inventory_data,
                'alternate_units' => $alternate_units,
                'selectedUnits' => $selectedUnits,
                'warehouses' => $warehouse,
                'inventory_barcode' => $inventory_barcode,
                'barcodes' => $barcodes,
                'companies' => $companies,
                'inventory_selectedUnits' => $inventory_selectedUnits));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $inventory = DB::table('inventory')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Inventory Successfully Disabled', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $inventory = DB::table('inventory')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Inventory Successfully Enabled', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $inventory = DB::table('inventory')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Inventory Successfully Deleted', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory/inventory_items');
        }
    }

    public function checkproductcode() {
        $product_code = Input::get('p_code');
        $id = Input::get('cid');
        $inventory_data = DB::table('inventory')
                ->select('inventory.name')
                ->where('product_code', '=', $product_code)
                ->where('status', '!=', 2)
                ->where('id', '!=', $id)
                ->get();

        if (count($inventory_data) == 0) {
            return \Response::json(array('msg' => 'false'));
        } else {
            return \Response::json(array('msg' => 'true'));
        }
    }
    
    public function checksuppliericode() {
        $supplier_icode = Input::get('s_code');
        $id = Input::get('cid');
        $inventory_data = DB::table('inventory')
                ->select('inventory.name')
                ->where('supplier_icode', '=', $supplier_icode)
                ->where('status', '!=', 2)
                ->where('id', '!=', $id)
                ->get();

        if (count($inventory_data) == 0) {
            return \Response::json(array('msg' => 'false'));
        } else {
            return \Response::json(array('msg' => 'true'));
        }
    }

    public function getalternateunits() {
        $id = Input::get('unitsid');
        $primaryunit = Input::get('primaryunit');
        $cid = Input::get('cid');
 
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $alternate_units = DB::table('units')
                ->select('units.id as unit_id', 'units.name as unit_name', 'units.conversion_value')->where('company_id', '=', $company_id)
                ->where('status', '=', 1)
                ->where('unit_type', '=', 'SIMPLE')
                ->where('id', '!=', $id)
                ->orderby('name', 'ASC')
                ->get();

 
        if (count($alternate_units) != 0) {
            $strHtml = '';
            $strHtml.= '<div class="privilegeCont units">
                            <div class="custRow">';
            if ($primaryunit == $id) {
                foreach ($alternate_units as $units) {
                    $strHtml.= '<div class="custCol-3">
                                    <div class="commonCheckHolder">
                                        <label>
                            <input type="checkbox" name="selected_units[]" ';

                    $strHtml.= ' class="selectUnits" value="' . $units->unit_name . '" id="' . $units->unit_id . '"><span></span><em>' . $units->unit_name . '</em></label></div></div>';
                }
            } else {
                foreach ($alternate_units as $units) {
                    $strHtml.= '<div class="custCol-3"><div class="commonCheckHolder"><label><input type="checkbox" name="selected_units[]" class="selectUnits" value="' . $units->unit_name . '" id="' . $units->unit_id . '"><span></span><em>' . $units->unit_name . '</em></label></div></div>';
                }
            }


            echo $strHtml;
        } else {
            echo "No alternate units exist";
        }
    }

    public function getchilds() {
        $parentid = Input::get('parentid');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $childlevel1 = DB::table('master_resources as m')
                ->select('m.id', 'm.name as name', 'm.inventory_category_id as parentid', db::raw("(select count(id) from master_resources where inventory_category_id=m.id) as childcount"))
                ->whereRaw("m.status=1 AND m.resource_type='INVENTORY_CATEGORY' AND m.company_id=$company_id")
                ->whereRaw("m.inventory_category_id IS NOT NULL")
                ->whereRaw("m.inventory_category_id=$parentid")
                ->get();

        $strHtml = "";
        if (count($childlevel1) > 0) {
            foreach ($childlevel1 as $child) {
                $strHtml.='<li><span usr-id="' . $child->id . '" usr-count="' . $child->childcount . '" class="clschild">+</span><a id="' . $child->id . '">' . $child->name . '</a><ul id="ul-' . $child->id . '"></ul></li>';
            }
        }

        echo $strHtml;
    }

    // Generate PDF funcion
    public function exporttopdf() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $searchbyname = Input::get('searchbyname');
        $searchbygroup = Input::get('searchbygroup');
        $searchbycategory = Input::get('searchbycategory');
        $searchbywarehouse = Input::get('searchbywarehouse');


        $inventories = DB::table('inventory')
                ->select('inventory.*', 'category.name as category_name', 'group.name as grp_name', 'warehouse.name as wareh_name')
                ->leftjoin('master_resources as category', 'inventory.inventory_category_id', '=', 'category.id')
                ->leftjoin('master_resources as group', 'inventory.inventory_group_id', '=', 'group.id')
                ->leftjoin('master_resources as warehouse', 'inventory.warehouse_id', '=', 'warehouse.id')
                ->where(['inventory.company_id' => $company_id])
                ->whereraw('inventory.status != 2')
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(inventory.name like '$searchbyname%')");
                })
                ->when($searchbycategory, function ($query) use ($searchbycategory) {
                    return $query->whereRaw("(inventory.inventory_category_id = $searchbycategory)");
                })
                ->when($searchbygroup, function ($query) use ($searchbygroup) {
                    return $query->whereRaw("(inventory.inventory_group_id = $searchbygroup)");
                })
                ->when($searchbywarehouse, function ($query) use ($searchbywarehouse) {
                    return $query->whereRaw("(inventory.warehouse_id = $searchbywarehouse)");
                })
                ->orderby('inventory.created_at', 'DESC')
                ->get();

        $html_table = '<!DOCTYPE html>
        <html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
		<title>Project Name</title>
		<style>
			.listerType1 tr:nth-of-type(2n) {
				background: rgba(0, 75, 111, 0.12) none repeat 0 0;
			}
			
		</style>
	</head>
	<body style="margin: 0; padding: 0;font-family:Arial;">
		<section id="container">
                <div style="text-align:center;"><h1>Inventory List</h1></div>
			<table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
				<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
					<tr class="headingHolder">
						<td style="padding:10px 5px;color:#fff;"> Product Code</td>
						<td style="padding:10px 5px;color:#fff;"> Product Name </td>
						<td style="padding:10px 5px;color:#fff;"> Group </td>
						<td style="padding:10px 5px;color:#fff;"> Category</td>
						<td style="padding:10px 5px;color:#fff;"> Warehouse</td>
					</tr>
				</thead>

				<tbody class="tblinventorylist" id="tblinventorylist" >';
        foreach ($inventories as $inventorie) {

            $html_table .='<tr>
                                <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $inventorie->product_code . '</td>
                                <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $inventorie->name . '</td>
                                <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $inventorie->grp_name . '</td>
                                <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $inventorie->category_name . '</td>
                                <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $inventorie->wareh_name . '</td>
                                
                        </tr>';
        }
        $html_table .='</tbody>
			</table>
		</section>
	</body>
</html>';


        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($html_table);
        return $pdf->download('mtg_inventory_list.pdf');
    }
    public function updatealtunits() {
        $altunits = DB::table('inventory_alternate_units')
                ->select('inventory_alternate_units.*')
                ->whereRaw("conversion_value is NULL")
                ->get();
        foreach($altunits as $altunit){
           $inventory_id = $altunit->inventory_item_id;
           $alt_unit_id =  $altunit->unit_id;
           $inventory = DB::table('inventory')
                ->select('inventory.primary_unit')
                ->where(['inventory.id' => $inventory_id])
                ->first();
           $primary_unit = $inventory->primary_unit;
           $unit = DB::table('units')
                ->select('units.conversion_value')
                ->where(['units.from' => $primary_unit, 'units.to' => $alt_unit_id])
                ->orderby('units.id', 'DESC')->first();
           if(count($unit) < 0)
           {
               $unit = DB::table('units')
                ->select('units.conversion_value')
                ->where(['units.to' => $primary_unit, 'units.from' => $alt_unit_id])
                ->orderby('units.id', 'DESC')->first();
           }
           if(count($unit) > 0)
           {
                $conversion_value = $unit->conversion_value;
                
                $update_conversion_value = DB::table('inventory_alternate_units')
                        ->where(['id' => $altunit->id])
                        ->update(['conversion_value' => $conversion_value]);
           }
        }
    }

}
