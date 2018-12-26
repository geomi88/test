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
use Illuminate\Support\Facades\Config;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Masterresources;
use App\Models\Company;
use DB;
use App;
use PDF;
use Excel;
class LedgerController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        $companyid = session('company');

        $companies = Company::all();
        $ledgers = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'LEDGER')
                ->where('status', '!=', 2)->where('company_id', '=', $companyid)
                ->orderby('master_resources.name', 'ASC')
                ->paginate($paginate);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $sortordname = Input::get('sortordname');
            $sortorderamount = Input::get('sortorderamount');
            $cashorder = Input::get('corder');
            $cashamount = Input::get('camount');
            $createdfrom = Input::get('startdatefrom');
            $endedfrom = Input::get('enddatefrom');
            $startdateto = Input::get('startdateto');
            $endedateto = Input::get('enddateto');
            $ldgordname = Input::get('ldgordname');
            
            
            
            if ($createdfrom != '') {
                $createdfrom = explode('-', $createdfrom);
                $createdfrom = $createdfrom[2] . '-' . $createdfrom[1] . '-' . $createdfrom[0];
            }
            if ($endedfrom != '') {
                $endedfrom = explode('-', $endedfrom);
                $endedfrom = $endedfrom[2] . '-' . $endedfrom[1] . '-' . $endedfrom[0];
            }
            
            if ($startdateto != '') {
                $startdateto = explode('-', $startdateto);
                $startdateto = $startdateto[2] . '-' . $startdateto[1] . '-' . $startdateto[0];
            }
            if ($endedateto != '') {
                $endedateto = explode('-', $endedateto);
                $endedateto = $endedateto[2] . '-' . $endedateto[1] . '-' . $endedateto[0];
            }
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortorderamount==''){
                $sortOrdDefault='ASC';
            }
            
            $ledgers = DB::table('master_resources')
                    ->select('master_resources.*')
                    ->where('resource_type', '=', 'LEDGER')
                    ->where('status', '!=', 2)
                    ->where('company_id', '=', $companyid)
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                    })
                    ->when($createdfrom, function ($query) use ($createdfrom) {
                        return $query->whereRaw("master_resources.start_time >= '$createdfrom' ");
                    })
                    ->when($endedfrom, function ($query) use ($endedfrom) {
                        return $query->whereRaw("master_resources.start_time<= '$endedfrom' ");
                    })
                    ->when($startdateto, function ($query) use ($startdateto) {
                        return $query->whereRaw("master_resources.end_time >= '$startdateto' ");
                    })
                    ->when($endedateto, function ($query) use ($endedateto) {
                        return $query->whereRaw("master_resources.end_time<= '$endedateto' ");
                    })
                    ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                        return $query->whereRaw("master_resources.amount $cashorder $cashamount ");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('master_resources.name', $sortordname);
                    })
                    ->when($sortorderamount, function ($query) use ($sortorderamount) {
                        return $query->orderby('master_resources.amount', $sortorderamount);
                    })
                    ->when($ldgordname, function ($query) use ($ldgordname) {
                        return $query->orderby('master_resources.ledger_code', $ldgordname);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('master_resources.name', $sortOrdDefault);
                    })
                    
                    ->paginate($paginate);

            return view('masterresources/ledger/ledger_result', array('ledgers' => $ledgers));
        }
        
        return view('masterresources/ledger/index', array('ledgers' => $ledgers));
    }

    public function add() {
        try {
            if (Session::get('company')) {

                return view('masterresources/ledger/add');
            } else {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/ledger');
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger');
        }
    }

    public function store() {
        try {
            if (Session::get('company')) {
                $ledger_from_date=Input::get('from_date');
                $ledger_from_date = explode('-',$ledger_from_date);
                $ledger_from_date = $ledger_from_date[2].'-'.$ledger_from_date[1].'-'.$ledger_from_date[0];
                
                $ledger_to_date=Input::get('to_date');
                $ledger_to_date = explode('-',$ledger_to_date);
                $ledger_to_date = $ledger_to_date[2].'-'.$ledger_to_date[1].'-'.$ledger_to_date[0];
                
                $companyid = Session::get('company');
                $masterresourcemodel = new Masterresources();
                $masterresourcemodel->resource_type = 'LEDGER';
                $masterresourcemodel->name = Input::get('name');
                $masterresourcemodel->ledger_code= Input::get('code');
                $masterresourcemodel->alias_name = Input::get('name');
                $masterresourcemodel->start_time = $ledger_from_date;
                $masterresourcemodel->end_time = $ledger_to_date;
                $masterresourcemodel->amount = Input::get('amount');
                $masterresourcemodel->description = Input::get('description');
                $masterresourcemodel->company_id = $companyid;
                $masterresourcemodel->status = 1;
                $masterresourcemodel->save();
                Toastr::success('Ledger Successfully Created', $title = null, $options = []);
                return Redirect::to('masterresources/ledger');
            } else {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/ledger/add');
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger/add');
        }
    }

    public function edit($id) {
        try {
            if (Session::get('company')) {
                $ledgerid = \Crypt::decrypt($id);
                $ledgers = DB::table('master_resources')
                        ->where(['id' => $ledgerid])
                        ->first();

                return view('masterresources/ledger/edit', array('ledger' => $ledgers));
            } else {
                Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
                return Redirect::to('masterresources/ledger');
            }
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger/edit/' . $ledgerid);
        }
    }

    public function update() {
        try {
            $companyid = Session::get('company');
            $masterresourcemodel = new Masterresources;
            
            $ledgerid = Input::get('ledgerid');
            $dn = \Crypt::encrypt($ledgerid);
            $name = Input::get('name');
            $code = Input::get('code');
            $ledger_from_date=Input::get('from_date');
            $ledger_from_date = explode('-',$ledger_from_date);
            $ledger_from_date = $ledger_from_date[2].'-'.$ledger_from_date[1].'-'.$ledger_from_date[0];
            $ledger_to_date=Input::get('to_date');
            $ledger_to_date = explode('-',$ledger_to_date);
            $ledger_to_date = $ledger_to_date[2].'-'.$ledger_to_date[1].'-'.$ledger_to_date[0];
            $amount = Input::get('amount');
            $description = Input::get('description');

            $ledgers = DB::table('master_resources')
                    ->where(['id' => $ledgerid])
                    ->update(['name' => $name, 
                        'ledger_code'=>$code,
                        'alias_name' => $name,
                        'company_id' => $companyid,
                        'start_time' => $ledger_from_date,
                        'end_time' => $ledger_to_date,
                        'amount' => $amount,
                        'description' => $description]);
            
            Toastr::success('Ledger Successfully Updated', $title = null, $options = []);
            return Redirect::to('masterresources/ledger');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger/edit/' . $dn);
        }
    }


   public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            Toastr::success('Ledger Successfully Disabled', $title = null, $options = []);
            return Redirect::to('masterresources/ledger');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            Toastr::success('Ledger Successfully Enabled', $title = null, $options = []);
            return Redirect::to('masterresources/ledger');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger');
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('master_resources')
                    ->where(['id' => $dn])
                    ->update(['status' => 2]);
            Toastr::success('Ledger Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/ledger');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger');
        }
    }
    
    public function checkledgername() 
       {
        
        $ledgername = Input::get('name');
        $id = Input::get('ledgerid');
        $data = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('name', '=', $ledgername)
                ->where('resource_type', '=', 'LEDGER')
                ->where('id', '!=', $id)
                ->where('status', '!=', 2)
                ->get();
      
        if(count($data)==0) {
            return \Response::json(array('msg' => 'false'));
        }
        return \Response::json(array('msg' => 'true'));

       }
       
    // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $companyid = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbyname = Input::get('searchbyname');
        $sortordname = Input::get('sortordname');
        $sortorderamount = Input::get('sortorderamount');
        $cashorder = Input::get('corder');
        $cashamount = Input::get('camount');
        $createdfrom = Input::get('startdatefrom');
        $endedfrom = Input::get('enddatefrom');
        $startdateto = Input::get('startdateto');
        $endedateto = Input::get('enddateto');
        $ldgordname=Input::get('ldgordname');

        if ($createdfrom != '') {
            $createdfrom = explode('-', $createdfrom);
            $createdfrom = $createdfrom[2] . '-' . $createdfrom[1] . '-' . $createdfrom[0];
        }
        if ($endedfrom != '') {
            $endedfrom = explode('-', $endedfrom);
            $endedfrom = $endedfrom[2] . '-' . $endedfrom[1] . '-' . $endedfrom[0];
        }

        if ($startdateto != '') {
            $startdateto = explode('-', $startdateto);
            $startdateto = $startdateto[2] . '-' . $startdateto[1] . '-' . $startdateto[0];
        }
        if ($endedateto != '') {
            $endedateto = explode('-', $endedateto);
            $endedateto = $endedateto[2] . '-' . $endedateto[1] . '-' . $endedateto[0];
        }

        $sortOrdDefault='';
        if($sortordname=='' && $sortorderamount==''){
            $sortOrdDefault='ASC';
        }

        $ledgers = DB::table('master_resources')
                ->select('master_resources.*')
                ->where('resource_type', '=', 'LEDGER')
                ->where('status', '!=', 2)
                ->where('company_id', '=', $companyid)
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("(master_resources.name like '$searchbyname%')");
                })
                ->when($createdfrom, function ($query) use ($createdfrom) {
                    return $query->whereRaw("master_resources.start_time >= '$createdfrom' ");
                })
                ->when($endedfrom, function ($query) use ($endedfrom) {
                    return $query->whereRaw("master_resources.start_time<= '$endedfrom' ");
                })
                ->when($startdateto, function ($query) use ($startdateto) {
                    return $query->whereRaw("master_resources.end_time >= '$startdateto' ");
                })
                ->when($endedateto, function ($query) use ($endedateto) {
                    return $query->whereRaw("master_resources.end_time<= '$endedateto' ");
                })
                ->when($cashamount, function ($query) use ($cashamount, $cashorder) {
                    return $query->whereRaw("master_resources.amount $cashorder $cashamount ");
                })
                ->when($sortordname, function ($query) use ($sortordname) {
                    return $query->orderby('master_resources.name', $sortordname);
                })
                ->when($sortorderamount, function ($query) use ($sortorderamount) {
                    return $query->orderby('master_resources.amount', $sortorderamount);
                })
                ->when($ldgordname, function ($query) use ($ldgordname) {
                        return $query->orderby('master_resources.ledger_code', $ldgordname);
                    })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('master_resources.name', $sortOrdDefault);
                })
                ->get();
                    
        if($excelorpdf=="Excel"){
            
            Excel::create('LedgerList', function($excel) use($ledgers){
                 // Set the title
                $excel->setTitle('Ledger List');
                
                $excel->sheet('Ledger List', function($sheet) use($ledgers){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('D3', 'Ledger List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No','Ledger Code', 'Ledger Name',"Budget Start Date",'Budget End Date','Budget Amount'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
//                        $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    });
                    
                    for($i=0;$i<count($ledgers);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $ledgers[$i]->ledger_code);
                        $sheet->setCellValue('C'.$chrRow, $ledgers[$i]->name);
                        $sheet->setCellValue('D'.$chrRow, date("d-m-Y", strtotime($ledgers[$i]->start_time)));
                        $sheet->setCellValue('E'.$chrRow, date("d-m-Y", strtotime($ledgers[$i]->end_time)));
                        $sheet->setCellValue('F'.$chrRow, $ledgers[$i]->amount);
                            
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
                <title>Project Name</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:Arial;">
                <section id="container">
                <div style="text-align:center;"><h1>Ledger List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Ledger Code</td>
                               <td style="padding:10px 5px;color:#fff;"> Ledger Name</td>
                                <td style="padding:10px 5px;color:#fff;"> Budget Start Date</td>
                                <td style="padding:10px 5px;color:#fff;"> Budget End Date</td>
                                <td style="padding:10px 5px;color:#fff;"> Budget Amount </td>
                            </tr>
                        </thead>
                        <tbody class="ledgerbody" id="ledgerbody" >';
            $slno=0;
            foreach ($ledgers as $ledger) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' .  $ledger->ledger_code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $ledger->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($ledger->start_time)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($ledger->end_time)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $ledger->amount . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_ledger_list.pdf');
        }
    }

}
