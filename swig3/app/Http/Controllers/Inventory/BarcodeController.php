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
use App\Models\Barcode;
use App;
use DB;
use Mail;
use PDF;
use Excel;

class BarcodeController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $barcodes = DB::table('barcodes')
                ->select('barcodes.*', 'employees.first_name as created_by_name')
                ->leftjoin('employees', 'employees.id', '=', 'barcodes.created_by')
                ->where(['barcodes.is_used' => 0, 'barcodes.status' => 1])
                ->orderby('barcodes.created_at', 'DESC')
                ->paginate($paginate);
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $created_date = Input::get('created_date');
            if ($created_date != '') {
                $created_date = explode('-', $created_date);
                $created_date = $created_date[2] . '-' . $created_date[1] . '-' . $created_date[0];
            }
            $searchbycode = Input::get('searchbycode');
            $searchbycreatedby = Input::get('searchbycreatedby');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }


            $barcodes = DB::table('barcodes')
                    ->select('barcodes.*', 'employees.first_name as created_by_name')
                    ->leftjoin('employees', 'employees.id', '=', 'barcodes.created_by')
                    ->where(['barcodes.is_used' => 0, 'barcodes.status' => 1])
                    ->when($created_date, function ($query) use ($created_date) {
                        return $query->whereRaw("(barcodes.created_at like '$created_date%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(barcodes.barcode_string like '$searchbycode%')");
                    })
                    ->when($searchbycreatedby, function ($query) use ($searchbycreatedby) {
                        return $query->whereRaw("(employees.first_name like '$searchbycreatedby%')");
                    })
                    ->orderby('barcodes.created_at', 'DESC')
                    ->paginate($paginate);

            return view('inventory/barcodes/barcodes_result', array('barcodes' => $barcodes));
        }


        return view('inventory/barcodes/index', array('barcodes' => $barcodes,
        ));
    }

    public function add() {
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        $user_id = Session::get('login_id');
        return view('inventory/barcodes/add', array('user_id' => $user_id));
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $company_id = Session::get('company');
            }

            $barcodemodel = new Barcode();
            $barcodemodel->barcode_string = Input::get('barcode_string');
            $barcodemodel->created_by = Session::get('login_id');

            $barcodemodel->save();
            Toastr::success('Barcode Successfully Created!', $title = null, $options = []);
            return Redirect::to('inventory/barcode/available_barcodes');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory/barcode/available_barcodes');
        }
    }

    public function checkbarcode() {

        $barcode_string = Input::get('barcode_string');
        $barcode_id = Input::get('barcode_id');
        $data = DB::table('barcodes')
                ->select('barcodes.*')
                ->where('barcode_string', '=', $barcode_string)
                ->where('id', '!=', $barcode_id)
                ->where('status', '!=', 2)
                ->get();
        if (count($data) == 0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));
    }

    public function allocated(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $barcodes = DB::table('inventory_barcodes')
                ->select('inventory_barcodes.id as inventory_barcode_id','inventory_barcodes.barcode_id', 'inventory_barcodes.created_at as allocated_date', 'barcodes.*', 'inventory.name as product_name', 'inventory.product_code')
                ->leftjoin('barcodes', 'inventory_barcodes.barcode_id', '=', 'barcodes.id')
                ->leftjoin('inventory', 'inventory_barcodes.inventory_id', '=', 'inventory.id')
                ->where(['inventory_barcodes.is_active' => 1])
                ->orderby('inventory_barcodes.created_at', 'DESC')
                ->paginate($paginate);
        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            $allocated_date = Input::get('allocated_date');
            if ($allocated_date != '') {
                $allocated_date = explode('-', $allocated_date);
                $allocated_date = $allocated_date[2] . '-' . $allocated_date[1] . '-' . $allocated_date[0];
            }
            $searchbycode = Input::get('searchbycode');
            $searchbyproductname = Input::get('searchbyproductname');
            $searchbyproductcode = Input::get('searchbyproductcode');
            $barcodes = DB::table('inventory_barcodes')
                    ->select('inventory_barcodes.id as inventory_barcode_id','inventory_barcodes.barcode_id', 'inventory_barcodes.created_at as allocated_date', 'barcodes.*', 'inventory.name as product_name', 'inventory.product_code')
                    ->leftjoin('barcodes', 'inventory_barcodes.barcode_id', '=', 'barcodes.id')
                    ->leftjoin('inventory', 'inventory_barcodes.inventory_id', '=', 'inventory.id')
                    ->where(['inventory_barcodes.is_active' => 1])
                    ->when($allocated_date, function ($query) use ($allocated_date) {
                        return $query->whereRaw("(inventory_barcodes.created_at like '$allocated_date%')");
                    })
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("(barcodes.barcode_string like '$searchbycode%')");
                    })
                    ->when($searchbyproductname, function ($query) use ($searchbyproductname) {
                        return $query->whereRaw("(inventory.name like '$searchbyproductname%')");
                    })
                    ->when($searchbyproductcode, function ($query) use ($searchbyproductcode) {
                        return $query->whereRaw("(inventory.product_code like '$searchbyproductcode%')");
                    })
                    ->orderby('inventory_barcodes.created_at', 'DESC')
                    ->paginate($paginate);

            return view('inventory/barcodes/allocated_barcodes_result', array('barcodes' => $barcodes));
        }


        return view('inventory/barcodes/allocated_barcodes', array('barcodes' => $barcodes,
        ));
    }

    // Generate PDF funcion
    public function exportavailablebarcodes() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $barcodes = DB::table('barcodes')
                ->select('barcodes.*', 'employees.first_name as created_by_name')
                ->leftjoin('employees', 'employees.id', '=', 'barcodes.created_by')
                ->where(['barcodes.is_used' => 0, 'barcodes.status' => 1])
                ->orderby('barcodes.created_at', 'DESC')
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('BarcodeList', function($excel) use($barcodes) {
                // Set the title
                $excel->setTitle('Barcode List');

                $excel->sheet('Barcode List', function($sheet) use($barcodes) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Barcode List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Date', 'Barcode Number', 'Created By'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:D5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($barcodes); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, date("d-m-Y", strtotime($barcodes[$i]->created_at)));
                        $sheet->setCellValue('C' . $chrRow, $barcodes[$i]->barcode_string);
                        $sheet->setCellValue('D' . $chrRow, $barcodes[$i]->created_by_name);

                        $sheet->cells('A' . $chrRow . ':D' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

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
                <div style="text-align:center;"><h1>Barcode List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Date</td>
                                <td style="padding:10px 5px;color:#fff;"> Barcode Number</td>
                                <td style="padding:10px 5px;color:#fff;"> Created By</td>
                            </tr>
                        </thead>
                        <tbody class="barcodebody" id="barcodebody" >';
            $slno = 0;
            foreach ($barcodes as $barcode) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($barcode->created_at)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $barcode->barcode_string . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $barcode->created_by_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_barcode_available_list.pdf');
        }
    }

    // Generate PDF funcion
    public function exportallocatedbarcodes() {

        if (Session::get('company')) {
            $company_id = Session::get('company');
        }

        $excelorpdf = Input::get('excelorpdf');


        $barcodes = DB::table('inventory_barcodes')
                ->select('inventory_barcodes.barcode_id', 'inventory_barcodes.created_at as allocated_date', 'barcodes.*', 'inventory.name as product_name', 'inventory.product_code')
                ->leftjoin('barcodes', 'inventory_barcodes.barcode_id', '=', 'barcodes.id')
                ->leftjoin('inventory', 'inventory_barcodes.inventory_id', '=', 'inventory.id')
                ->where(['inventory_barcodes.is_active' => 1])
                ->orderby('inventory_barcodes.created_at', 'DESC')
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('AllocatedBarcodeList', function($excel) use($barcodes) {
                // Set the title
                $excel->setTitle('Allocated Barcode List');

                $excel->sheet('Allocated Barcode List', function($sheet) use($barcodes) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Allocated Barcode List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Allocated Date', 'Barcode Number', 'Product Name', 'Product Code'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });

                    for ($i = 0; $i < count($barcodes); $i++) {
                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, date("d-m-Y", strtotime($barcodes[$i]->allocated_date)));
                        $sheet->setCellValue('C' . $chrRow, $barcodes[$i]->barcode_string);
                        $sheet->setCellValue('D' . $chrRow, $barcodes[$i]->product_name);
                        $sheet->setCellValue('E' . $chrRow, $barcodes[$i]->product_code);

                        $sheet->cells('A' . $chrRow . ':E' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
//                            $cells->setBackground('#E6E6FA');
//                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

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
                <div style="text-align:center;"><h1>Allocated Barcode List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Allocated Date</td>
                                <td style="padding:10px 5px;color:#fff;"> Barcode Number</td>
                                <td style="padding:10px 5px;color:#fff;"> Product Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Product Code</td>
                            </tr>
                        </thead>
                        <tbody class="barcodebody" id="barcodebody" >';
            $slno = 0;
            foreach ($barcodes as $barcode) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($barcode->allocated_date)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $barcode->barcode_string . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $barcode->product_name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $barcode->product_code . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_barcode_allocated_list.pdf');
        }
    }
    
    public function release($id) {

        try {
            $inventory_barcode_id = \Crypt::decrypt($id);
            $inventory_barcodes = DB::table('inventory_barcodes')
                    ->where(['id' => $inventory_barcode_id])
                    ->update(['is_active' => 0,'deallocated_date' => date('Y-m-d H:i:s')]);
            $barcode_details = DB::table('inventory_barcodes')
                ->select('inventory_barcodes.barcode_id')
                ->where(['inventory_barcodes.id' => $inventory_barcode_id])
                ->first();
            $barcode_id = $barcode_details->barcode_id;
            $barcode_status = DB::table('barcodes')
                    ->where(['id' => $barcode_id])
                    ->update(['is_used' => 0]);
            Toastr::success('Barcode Successfully Released', $title = null, $options = []);
            return Redirect::to('inventory/barcode/allocated_barcodes');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('inventory/barcode/allocated_barcodes');
        }
    }
    
    public function delete($id)
        {
           try
           {
                $barcode_id=\Crypt::decrypt($id);
                $delete_barcode = DB::table('barcodes')
                                ->where(['id' => $barcode_id])                            
                                ->update(['status' => 2]);
//                $inventory_barcodes = DB::table('inventory_barcodes')
//                    ->where(['barcode_id' => $barcode_id,'is_active' => 1])
//                    ->update(['is_active' => 0,'deallocated_date' => date('Y-m-d H:i:s')]);
                Toastr::success('Barcode Successfully Deleted', $title = null, $options = []);
                return Redirect::to('inventory/barcode/available_barcodes');
           }
           catch(\Exception $e)
           {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('inventory/barcode/available_barcodes');
            
           }
        }
        
        
       
    public function edit($id)
        {
        $barcode_id=\Crypt::decrypt($id);
        $barcode_details = DB::table('barcodes')
                 ->where(['id' => $barcode_id])                            
                                ->first();
        return view('inventory/barcodes/edit', array('barcode_details' => $barcode_details));
   
        }
        
        
       public function update()
        {
           try
           {
                $barcode_id = Input::get('barcode_id');
                $dn=\Crypt::encrypt($barcode_id);
                $barcode_string = Input::get('barcode_string');
                
                $barcode = DB::table('barcodes')
                                ->where(['id' => $barcode_id])                            
                                ->update(['barcode_string' => $barcode_string]);
                Toastr::success('Barcode Successfully Updated', $title = null, $options = []);
                return Redirect::to('inventory/barcode/available_barcodes');
            } 
            catch(\Exception $e)
            {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('inventory/barcode/edit/'.$dn);
            
            }
        }

}
