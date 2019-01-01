<?php

namespace App\Http\Controllers\Masterresources;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use App\Models\Company;
use App\Models\Masterresources;
use DB;
use App;
use PDF;
use Excel;

class InventorycategoryController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $inventory_categories = DB::table('master_resources')
                ->select('master_resources.*', 'main_category.name as parent_name')
                ->leftjoin('master_resources as main_category', 'master_resources.inventory_category_id', '=', 'main_category.id')
                ->where('master_resources.resource_type', '=', 'INVENTORY_CATEGORY')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->orderby('master_resources.name', 'ASC')
                ->paginate($paginate);
        
         $inventory_all = DB::table('master_resources')
            ->select('master_resources.id','master_resources.name')
            ->where(['resource_type' => 'INVENTORY_CATEGORY'])
            ->where('status', '=', 1)
            ->where('company_id', '=', $company_id)
            ->orderby('name', 'ASC')->get();

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbycategory = Input::get('searchbycategory');
            $sortordname = Input::get('sortordname');
            $sortordermain = Input::get('sortordermain');
            $sortOrdDefault='';
            if($sortordname=='' && $sortordermain==''){
                $sortOrdDefault='ASC';
            }

            $inventory_categories = DB::table('master_resources')
                    ->select('master_resources.*', 'main_category.name as parent_name')
                    ->leftjoin('master_resources as main_category', 'master_resources.inventory_category_id', '=', 'main_category.id')
                    ->where('master_resources.resource_type', '=', 'INVENTORY_CATEGORY')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $company_id)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($searchbycategory, function ($query) use ($searchbycategory) {
                        return $query->whereRaw("(master_resources.inventory_category_id = $searchbycategory)");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortordermain, function ($query) use ($sortordermain) {
                        return $query->orderby('main_category.name', $sortordermain);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('masterresources/inventory_category/category_result', array('inventory_categories' => $inventory_categories,'inventory_all'=>$inventory_all));
        }

        return view('masterresources/inventory_category/index', array('inventory_categories' => $inventory_categories,'inventory_all'=>$inventory_all));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $parentcategory = DB::table('master_resources as m1')
                ->select('m1.id', 'm1.name as name',
                        db::raw("(select count(id) from master_resources where inventory_category_id=m1.id) as childcount"))
                ->whereRaw("m1.status=1 AND m1.resource_type='INVENTORY_CATEGORY' AND m1.company_id=$company_id")
                ->whereRaw("m1.inventory_category_id IS NULL")
                ->get();
        
        
        $parentids=$parentcategory->pluck("id")->toArray();
        
        $childlevel1 = DB::table('master_resources as m')
                ->select('m.id', 'm.name as name','m.inventory_category_id as parentid',
                        db::raw("(select count(id) from master_resources where inventory_category_id=m.id) as childcount "))
                ->whereRaw("m.status=1 AND m.resource_type='INVENTORY_CATEGORY' AND m.company_id=$company_id")
                ->whereRaw("m.inventory_category_id IS NOT NULL")
                ->whereIn("m.inventory_category_id",$parentids)
                ->get();
        
        return view('masterresources/inventory_category/add', array(
                                            'parentcategory' => $parentcategory,
                                            'childlevel1' => $childlevel1));
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $masterresourcemodel = new Masterresources;
            $masterresourcemodel->name = Input::get('name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->resource_type = 'INVENTORY_CATEGORY';

            if (Input::get('inventory_category_id') == '') {
                $masterresourcemodel->inventory_category_id = NULL;
            } else {
                $masterresourcemodel->inventory_category_id = Input::get('inventory_category_id');
            }

            $masterresourcemodel->company_id = $company_id;
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();
            Toastr::success('Inventory Category Successfully Added!', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category/add');
        }
    }

    public function checkcategories() {
        $name = Input::get('name');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.name')
                ->where('name', '=', $name)
                ->where('resource_type', '=', 'INVENTORY_CATEGORY')
                ->where('id', '!=', $id)
                ->where('status', '!=', 2)
                ->get();
        if (count($data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function edit($id) {
        $dn = \Crypt::decrypt($id);
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $categorie_data = DB::table('master_resources')
                ->select('master_resources.*','category.name as category_name')
                ->leftjoin('master_resources as category', 'master_resources.inventory_category_id', '=', 'category.id')
                ->where(['master_resources.id' => $dn])
                ->first();
        
        $parentcategory = DB::table('master_resources as m1')
                ->select('m1.id', 'm1.name as name',
                        db::raw("(select count(id) from master_resources where inventory_category_id=m1.id) as childcount"))
                ->whereRaw("m1.status=1 AND m1.resource_type='INVENTORY_CATEGORY' AND m1.company_id=$company_id")
                ->whereRaw("m1.inventory_category_id IS NULL")
                ->get();
        
        
        $parentids=$parentcategory->pluck("id")->toArray();
        
        $childlevel1 = DB::table('master_resources as m')
                ->select('m.id', 'm.name as name','m.inventory_category_id as parentid',
                        db::raw("(select count(id) from master_resources where inventory_category_id=m.id) as childcount "))
                ->whereRaw("m.status=1 AND m.resource_type='INVENTORY_CATEGORY' AND m.company_id=$company_id")
                ->whereRaw("m.inventory_category_id IS NOT NULL")
                ->whereIn("m.inventory_category_id",$parentids)
                ->get();
        
        return view('masterresources/inventory_category/edit', array('categorie_datas' => $categorie_data,
                                            'parentcategory' => $parentcategory,
                                            'childlevel1' => $childlevel1));
    }

    public function update() {
        try {
            $masterresourcemodel = new Masterresources;
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');

            if (Input::get('inventory_category_id') != '') {
                $inventory_category_id = Input::get('inventory_category_id');
            } else {
                $inventory_category_id = NULL;
            }

            $inventory_categories = DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $name,
                'alias_name' => $alias_name,
                'inventory_category_id' => $inventory_category_id]);

            Toastr::success('Inventory Category Successfully Updated', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category/edit/' . $dn);
        }
    }

    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Inventory Category Successfully Disabled', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Inventory Category Successfully Enabled', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Inventory Category Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/inventory_category');
        }
    }

        // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $searchbycategory = Input::get('searchbycategory');
        $sortordname = Input::get('sortordname');
        $sortordermain = Input::get('sortordermain');
        $sortOrdDefault='';
        if($sortordname=='' && $sortordermain==''){
            $sortOrdDefault='ASC';
        }

        $inventory_categories = DB::table('master_resources')
                ->select('master_resources.*', 'main_category.name as parent_name')
                ->leftjoin('master_resources as main_category', 'master_resources.inventory_category_id', '=', 'main_category.id')
                ->where('master_resources.resource_type', '=', 'INVENTORY_CATEGORY')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($searchbycategory, function ($query) use ($searchbycategory) {
                    return $query->whereRaw("(master_resources.inventory_category_id = $searchbycategory)");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortordermain, function ($query) use ($sortordermain) {
                    return $query->orderby('main_category.name', $sortordermain);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('InventoryCategoryList', function($excel) use($inventory_categories){
                 // Set the title
                $excel->setTitle('Inventory Category List');
                
                $excel->sheet('Inventory Category List', function($sheet) use($inventory_categories){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Inventory Category List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:D3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Inventory Category',"Main Category","Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($inventory_categories);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $inventory_categories[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $inventory_categories[$i]->parent_name);
                        $sheet->setCellValue('D'.$chrRow, $inventory_categories[$i]->alias_name);
                            
                        $sheet->cells('A'.$chrRow.':D'.$chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
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
                <title>Project Name</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:Arial;">
                <section id="container">
                <div style="text-align:center;"><h1>Inventory Category List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Inventory Category</td>
                                <td style="padding:10px 5px;color:#fff;"> Main Category</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($inventory_categories as $cat) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->parent_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_area_list.pdf');
        }
    }
}
