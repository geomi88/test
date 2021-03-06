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

class ChartcategoryController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $chart_categories = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('master_resources.resource_type', '=', 'CHART_CATEGORY')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->orderby('master_resources.name', 'ASC')
                ->paginate($paginate);
        
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $sortordname = Input::get('sortordname');
            $sortOrdDefault='';
            if($sortordname==''){
                $sortOrdDefault='ASC';
            }

            $chart_categories = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('master_resources.resource_type', '=', 'CHART_CATEGORY')
                    ->where('master_resources.status', '!=', 2)
                    ->where('master_resources.company_id', '=', $company_id)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('masterresources/chartcategory/result', array('chart_categories' => $chart_categories));
        }

        return view('masterresources/chartcategory/index', array('chart_categories' => $chart_categories));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        return view('masterresources/chartcategory/add', array());
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }
            $masterresourcemodel = new Masterresources;
            $masterresourcemodel->name = Input::get('name');
            $masterresourcemodel->alias_name = Input::get('alias_name');
            $masterresourcemodel->resource_type = 'CHART_CATEGORY';
            $masterresourcemodel->company_id = $company_id;
            $masterresourcemodel->status = 1;
            $masterresourcemodel->save();
            
            Toastr::success('Chart Category Successfully Added!', $title = null, $options = []);
            return Redirect::to('masterresources/chartcategory');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/chartcategory/add');
        }
    }

    public function checkcategories() {
        $name = Input::get('name');
        $id = Input::get('cid');
        $data = DB::table('master_resources')
                ->select('master_resources.name')
                ->where('name', '=', $name)
                ->where('resource_type', '=', 'CHART_CATEGORY')
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
                            ->select('master_resources.*')
                            ->where(['master_resources.id' => $dn])
                            ->first();
        
        return view('masterresources/chartcategory/edit', array('categorie_datas' => $categorie_data));
    }

    public function update() {
        try {
            $masterresourcemodel = new Masterresources;
            $id = Input::get('cid');
            $dn = \Crypt::encrypt($id);
            $name = Input::get('name');
            $alias_name = Input::get('alias_name');

            $categories = DB::table('master_resources')
                    ->where(['id' => $id])
                    ->update(['name' => $name,
                            'alias_name' => $alias_name,
                            ]);

            Toastr::success('Chart Category Successfully Updated', $title = null, $options = []);
            return Redirect::to('masterresources/chartcategory');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/chartcategory/edit/' . $dn);
        }
    }

    
    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Chart Category Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/chartcategory');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/chartcategory');
        }
    }

        // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $company_id = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $sortordname = Input::get('sortordname');
        $sortOrdDefault='';
        if($sortordname==''){
            $sortOrdDefault='ASC';
        }

        $chart_categories = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('master_resources.resource_type', '=', 'CHART_CATEGORY')
                ->where('master_resources.status', '!=', 2)
                ->where('master_resources.company_id', '=', $company_id)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('WarningType', function($excel) use($chart_categories){
                 // Set the title
                $excel->setTitle('Chart Category');
                
                $excel->sheet('Chart Category', function($sheet) use($chart_categories){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('B3', 'Chart Categories');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:C3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Category',"Alias"));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:C5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($chart_categories);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $chart_categories[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $chart_categories[$i]->alias_name);
                            
                        $sheet->cells('A'.$chrRow.':C'.$chrRow, function($cells) {
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
                <title>Project Name</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:Arial;">
                <section id="container">
                <div style="text-align:center;"><h1>Chart Categories</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;">Category</td>
                                <td style="padding:10px 5px;color:#fff;"> Alias </td>
                            </tr>
                        </thead>
                        <tbody class="categorybody" id="categorybody" >';
            $slno=0;
            foreach ($chart_categories as $cat) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->name . '</td>
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
            return $pdf->download('mtg_chartcategory.pdf');
        }
    }
}
