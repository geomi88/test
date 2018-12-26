<?php

namespace App\Http\Controllers\Ledgers;

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
use App\Models\General_ledgers;
use App\Models\Accounts;
use Customhelper;
use DB;
use App;
use PDF;
use Excel;

class GeneralledgersController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        
        $groups = DB::table('ac_ledger_group')
                    ->select('ac_ledger_group.*')
                    ->where('status', '=', 1)
                    ->get();
      
        $general_ledgers = DB::table('ac_general_ledgers as ledger')
                    ->select('ledger.*','group.name as groupname')
                    ->leftjoin('ac_ledger_group as group','ledger.ledger_group_id','=','group.id')
                    ->where('ledger.status', '!=', 2)
                    ->orderby('ledger.code','desc')
                    ->paginate($paginate);


        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbycode = Input::get('searchbycode');
            $searchbyname = Input::get('search_name');
            $searchbygroup = Input::get('searchbygroup');
            $searchbytype = Input::get('searchbytype');
            
            $sortordname = Input::get('sortordname');
            $sortordcode = Input::get('sortordcode');
            
            $sortOrdDefault='';
            if($sortordname=='' && $sortordcode==''){
                $sortOrdDefault='DESC';
            }
            
            $general_ledgers = DB::table('ac_general_ledgers as ledger')
                    ->select('ledger.*','group.name as groupname')
                    ->leftjoin('ac_ledger_group as group','ledger.ledger_group_id','=','group.id')
                    ->where('ledger.status', '!=', 2)
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("ledger.code like '$searchbycode%'");
                    })
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("ledger.name like '$searchbyname%'");
                    })
                    ->when($searchbygroup, function ($query) use ($searchbygroup) {
                        return $query->whereRaw("ledger.ledger_group_id=$searchbygroup");
                    })
                    ->when($searchbytype, function ($query) use ($searchbytype) {
                        return $query->whereRaw("ledger.type='$searchbytype'");
                    })
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('ledger.name', $sortordname);
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('ledger.code', $sortordcode);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('ledger.code', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('ledgers/general_ledgers/result', array('ledgers' => $general_ledgers));
        }
        
        return view('ledgers/general_ledgers/index', array('ledgers' => $general_ledgers,'groups'=>$groups));
    }

    public function add() {
        try {
                
            $groups = DB::table('ac_ledger_group')
                ->select('ac_ledger_group.*')
                ->where('status', '=', 1)
                ->get();
            
            return view('ledgers/general_ledgers/add',array('parentgroups'=>$groups));
           
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers');
        }
    }

    public function store() {
        try {
                
            if (Session::get('company')){ 
                $companyid = Session::get('company');  
            }
        
            $maxid = DB::table('ac_general_ledgers')->max('id') + 100;
            $code="GL-".$maxid;
            $model = new General_ledgers();
            $model->code = $code;
            $model->name = Input::get('name');
            $model->company_id = $companyid;
            $model->alias_name = Input::get('alias_name');
            $model->ledger_group_id = Input::get('ledger_group_id');
//            $model->type = Input::get('type');
            $model->ac_nature = Input::get('nature');
            $model->opening_balance = Input::get('openingbalance');

            $model->save();

            $acmodel= new Accounts();
            $acmodel->type_id = $model->id;
            $acmodel->code = $code;
            $acmodel->ledger_group_id = Input::get('ledger_group_id');
            $acmodel->first_name = Input::get('name');
            $acmodel->alias_name = Input::get('alias_name');
            $acmodel->type = "General Ledger";
            $acmodel->save();
                
            Toastr::success('General Ledger Successfully Created', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers');

        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers/add');
        }
    }

    public function edit($id) {
        try {
           
            $ledgerid = \Crypt::decrypt($id);
            $general_ledgers = DB::table('ac_general_ledgers')
                    ->where(['id' => $ledgerid])
                    ->first();
            
            $groups = DB::table('ac_ledger_group')
                        ->select('ac_ledger_group.*')
                        ->where('status', '=', 1)
                        ->get();

            return view('ledgers/general_ledgers/edit', array('ledger' => $general_ledgers,'parentgroups'=>$groups));
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers/edit/' . $ledgerid);
        }
    }

    public function update() {
        try {
            
            $ledgerid = Input::get('ledgerid');

            DB::table('ac_general_ledgers')
                    ->where(['id' => $ledgerid])
                    ->update(['name' => Input::get('name'), 
                        'alias_name' => Input::get('alias_name'),
                        'ledger_group_id' => Input::get('ledger_group_id'),
//                        'type' => Input::get('type'),
                        'ac_nature' => Input::get('nature'),
                        'opening_balance' => Input::get('openingbalance')
                    ]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$ledgerid AND type='General Ledger'")
                    ->update(['ledger_group_id'=>Input::get('ledger_group_id'),'first_name'=>Input::get('name'),'alias_name'=>Input::get('alias_name')]);
            
            Toastr::success('General Ledger Successfully Updated', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers/edit/' . $dn);
        }
    }

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('ac_general_ledgers')
                            ->where(['id' => $dn])
                            ->update(['status' => 2]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$dn AND type='General Ledger'")
                    ->update(['status' => 2]);
            
            Toastr::success('General Ledger Successfully Deleted', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers');
        }
    }
    
    public function Disable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('ac_general_ledgers')
                    ->where(['id' => $dn])
                    ->update(['status' => 0]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$dn AND type='General Ledger'")
                    ->update(['status' => 0]);
                        
            Toastr::success('General Ledger Successfully Disabled', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers');
        }
    }

    public function Enable($id) {
        try {
            $dn = \Crypt::decrypt($id);
            DB::table('ac_general_ledgers')
                    ->where(['id' => $dn])
                    ->update(['status' => 1]);
            
            DB::table('ac_accounts')
                    ->whereRaw("type_id=$dn AND type='General Ledger'")
                    ->update(['status' => 1]);
            
            Toastr::success('General Ledger Successfully Enabled', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers');
        } catch (\Exception $e) {
            Toastr::error('Sorry There is Some Problem!', $title = null, $options = []);
            return Redirect::to('ledgers/general_ledgers');
        }
    }
       
    // Generate PDF funcion
    public function exportdata() {
        
        if (Session::get('company')){ 
            $companyid = Session::get('company');  
        }
        
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbyname = Input::get('search_name');
        $searchbygroup = Input::get('searchbygroup');
        $searchbytype = Input::get('searchbytype');

        $sortordname = Input::get('sortordname');
        $sortordcode = Input::get('sortordcode');

        $sortOrdDefault='';
        if($sortordname=='' && $sortordcode==''){
            $sortOrdDefault='DESC';
        }
        $general_ledgers = DB::table('ac_general_ledgers as ledger')
               ->select('ledger.*','group.name as groupname')
               ->leftjoin('ac_ledger_group as group','ledger.ledger_group_id','=','group.id')
               ->where('ledger.status', '!=', 2)
               ->when($searchbycode, function ($query) use ($searchbycode) {
                   return $query->whereRaw("ledger.code like '$searchbycode%'");
               })
               ->when($searchbyname, function ($query) use ($searchbyname) {
                   return $query->whereRaw("ledger.name like '$searchbyname%'");
               })
               ->when($searchbygroup, function ($query) use ($searchbygroup) {
                   return $query->whereRaw("ledger.ledger_group_id=$searchbygroup");
               })
               ->when($searchbytype, function ($query) use ($searchbytype) {
                   return $query->whereRaw("ledger.type='$searchbytype'");
               })
               ->when($sortordname, function ($query) use ($sortordname) {
                   return $query->orderby('ledger.name', $sortordname);
               })
               ->when($sortordcode, function ($query) use ($sortordcode) {
                   return $query->orderby('ledger.code', $sortordcode);
               })
               ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                   return $query->orderby('ledger.code', $sortOrdDefault);
               })
               ->get();
                    
                  
        if($excelorpdf=="Excel"){
            
            Excel::create('GeneralLedgerList', function($excel) use($general_ledgers){
                 // Set the title
                $excel->setTitle('General Ledger List');
                
                $excel->sheet('General Ledger List', function($sheet) use($general_ledgers){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('C3', 'General Ledger List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:G3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Code','Name','Ledger Group','Opening Balance','Alias Name'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($general_ledgers);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $general_ledgers[$i]->code);
                        $sheet->setCellValue('C'.$chrRow, $general_ledgers[$i]->name);
                        $sheet->setCellValue('D'.$chrRow, $general_ledgers[$i]->groupname);
//                        $sheet->setCellValue('E'.$chrRow, $general_ledgers[$i]->type);
                        $sheet->setCellValue('E'.$chrRow, round($general_ledgers[$i]->opening_balance,2));
                        $sheet->setCellValue('F'.$chrRow, $general_ledgers[$i]->alias_name);
                            
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
            <body style="margin: 0; padding: 0;font-family:DejaVu Sans;">
                <section id="container">
                <div style="text-align:center;"><h1>General Ledger List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                               <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                               <td style="padding:10px 5px;color:#fff;"> Code</td>
                               <td style="padding:10px 5px;color:#fff;"> Name</td>
                               <td style="padding:10px 5px;color:#fff;"> Ledger Group</td>
                              <td style="padding:10px 5px;color:#fff;"> Opening Balance</td>
                               <td style="padding:10px 5px;color:#fff;"> Alias Name</td>
                            </tr>
                        </thead>
                        <tbody class="ledgerbody" id="ledgerbody" >';
            $slno=0;
            foreach ($general_ledgers as $ledger) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' .  $ledger->code . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $ledger->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $ledger->groupname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $ledger->type . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 20px;text-align:right;">' . Customhelper::numberformatter($ledger->opening_balance) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $ledger->alias_name . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_gl_list.pdf');
        }
    }

}
