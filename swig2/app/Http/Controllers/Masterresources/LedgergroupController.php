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
use App\Models\Ledger_group;
use DB;
use App;
use PDF;
use Excel;

class LedgergroupController extends Controller {

    public function index(Request $request) {
        $paginate = Config::get('app.PAGINATE');
        
        $groups = DB::table('ac_ledger_group')
                    ->select('ac_ledger_group.*')
                    ->where('status', '=', 1)
                    ->get();
        
      
        $ledger_groups = DB::table('ac_ledger_group as ledger')
                    ->select('ledger.*','parent.name as parentname',
                            db::raw("CASE WHEN ledger.group_type=0 THEN 'Primary' WHEN ledger.group_type=1 THEN 'Main Group' WHEN ledger.group_type=2 THEN 'Sub Group' END as type"))
                    ->leftjoin('ac_ledger_group as parent','ledger.parent_id','=','parent.id')
                    ->where('ledger.status', '=', 1)
                    ->whereRaw('ledger.parent_id!=0')
                    ->paginate($paginate);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }
            
            $searchbyname = Input::get('searchbyname');
            $searchbygroup = Input::get('searchbygroup');
            $sortordname = Input::get('sortordname');
            
            $sortOrdDefault='';
            if($sortordname==''){
                $sortOrdDefault='ASC';
            }
            
        $ledger_groups = DB::table('ac_ledger_group as ledger')
                    ->select('ledger.*','parent.name as parentname',
                            db::raw("CASE WHEN ledger.group_type=0 THEN 'Primary' WHEN ledger.group_type=1 THEN 'Main Group' WHEN ledger.group_type=2 THEN 'Sub Group' END as type"))
                    ->leftjoin('ac_ledger_group as parent','ledger.parent_id','=','parent.id')
                    ->where('ledger.status', '=', 1)
                    ->whereRaw('ledger.parent_id!=0')
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("ledger.name like '$searchbyname%'");
                    })
                    ->when($searchbygroup, function ($query) use ($searchbygroup) {
                        return $query->whereRaw("ledger.parent_id=$searchbygroup");
                    })
                    
                    ->when($sortordname, function ($query) use ($sortordname) {
                        return $query->orderby('ledger.name', $sortordname);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('ledger.name', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('masterresources/ledger_group/result', array('ledgers' => $ledger_groups));
        }
        
        return view('masterresources/ledger_group/index', array('ledgers' => $ledger_groups,'groups'=>$groups));
    }

    public function add() {
        try {
                
            $groups = DB::table('ac_ledger_group')
                ->select('ac_ledger_group.*')
                ->where('status', '!=', 2)
                ->get();
            
            return view('masterresources/ledger_group/add',array('groups'=>$groups));
           
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger_group');
        }
    }

    public function store() {
        try {
                $parentid=Input::get('parentgroup');
                $parentnature=DB::table('ac_ledger_group')->select('ac_ledger_group.group_nature')->where('id', '!=', $parentid)->first();
                
                $model = new Ledger_group();
                $model->name = Input::get('name');
                $model->alias_name = Input::get('alias_name');
                $model->group_type = Input::get('grouptype');
                $model->group_nature = $parentnature->group_nature;
                $model->parent_id = Input::get('parentgroup');
                
                $model->save();
                
            Toastr::success('Account Group Successfully Created', $title = null, $options = []);
            return Redirect::to('masterresources/ledger_group');

        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger_group/add');
        }
    }

    public function edit($id) {
        try {
           
            $ledgerid = \Crypt::decrypt($id);
            $ledger_groups = DB::table('ac_ledger_group')
                    ->where(['id' => $ledgerid])
                    ->first();
            
            if($ledger_groups->group_type==1){
                $strwhere="ac_ledger_group.parent_id=0";
            }else{
                $strwhere="ac_ledger_group.parent_id!=0";
            }

            $groups = DB::table('ac_ledger_group')
                        ->select('ac_ledger_group.*')
                        ->whereRaw($strwhere)
                        ->where('status', '!=', 2)
                        ->get();

            return view('masterresources/ledger_group/edit', array('ledger_groups' => $ledger_groups,'parentgroups'=>$groups));
            
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger_group/edit/' . $ledgerid);
        }
    }

    public function update() {
        try {
            
            $ledgerid = Input::get('ledgerid');
            $dn = \Crypt::encrypt($ledgerid);

            $ledger_groups = DB::table('ac_ledger_group')
                    ->where(['id' => $ledgerid])
                    ->update(['name' => Input::get('name'), 
                        'alias_name' => Input::get('alias_name'),
                  ]);
            
            Toastr::success('Account Group Successfully Updated', $title = null, $options = []);
            return Redirect::to('masterresources/ledger_group');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger_group/edit/' . $dn);
        }
    }

    public function gerparentgroups() {
        $grouptype = Input::get('grouptype');
        
        if($grouptype==1){
            $strwhere="ac_ledger_group.parent_id=0";
        }else{
            $strwhere="ac_ledger_group.parent_id!=0";
        }
        
        $groups = DB::table('ac_ledger_group')
                    ->select('ac_ledger_group.*')
                    ->whereRaw($strwhere)
                    ->where('status', '!=', 2)
                    ->get();
     
        $strHtml='<option value="">Select Parent Group</option>';
        if (count($groups) != 0) {
            foreach ($groups as $group) {
                $strHtml.= '<option value="'.$group->id.'">'.$group->name.'</option>';
            }
        }
        
        echo $strHtml;
        
    }
    

   

    public function delete($id) {
        try {
            $dn = \Crypt::decrypt($id);
            $companies = DB::table('ac_ledger_group')
                            ->where(['id' => $dn])
                            ->update(['status' => 0]);
            
            Toastr::success('Account Group Successfully Deleted', $title = null, $options = []);
            return Redirect::to('masterresources/ledger_group');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('masterresources/ledger_group');
        }
    }
    
    public function checkledgername() 
    {

     $ledgername = Input::get('name');
     $id = Input::get('ledgerid');
     $data = DB::table('ac_ledger_group')
             ->select('ac_ledger_group.*')
             ->where('name', '=', $ledgername)
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
        $searchbygroup = Input::get('searchbygroup');
        $sortordname = Input::get('sortordname');

        $sortOrdDefault='';
        if($sortordname==''){
            $sortOrdDefault='ASC';
        }

       $ledger_groups = DB::table('ac_ledger_group as ledger')
                   ->select('ledger.*','parent.name as parentname',
                           db::raw("CASE WHEN ledger.group_type=0 THEN 'Primary' WHEN ledger.group_type=1 THEN 'Main Group' WHEN ledger.group_type=2 THEN 'Sub Group' END as type"))
                   ->leftjoin('ac_ledger_group as parent','ledger.parent_id','=','parent.id')
                   ->where('ledger.status', '=', 1)
                   ->whereRaw('ledger.parent_id!=0')
                   ->when($searchbyname, function ($query) use ($searchbyname) {
                       return $query->whereRaw("ledger.name like '$searchbyname%'");
                   })
                   ->when($searchbygroup, function ($query) use ($searchbygroup) {
                       return $query->whereRaw("ledger.parent_id=$searchbygroup");
                   })

                   ->when($sortordname, function ($query) use ($sortordname) {
                       return $query->orderby('ledger.name', $sortordname);
                   })
                   ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                       return $query->orderby('ledger.name', $sortOrdDefault);
                   })
                    ->get();
                    
                  
        if($excelorpdf=="Excel"){
            
            Excel::create('AccountGroupList', function($excel) use($ledger_groups){
                 // Set the title
                $excel->setTitle('Account Group List');
                
                $excel->sheet('Account Group List', function($sheet) use($ledger_groups){
                    // Sheet manipulation
                    
                    $sheet->setCellValue('C3', 'Account Group List');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:E3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });
                    
                    $chrRow=6;
                    
                    $sheet->row(5, array('Sl No', 'Group Name','Parent Group','Group Type','Alias'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:E5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });
                    
                    for($i=0;$i<count($ledger_groups);$i++){
                        $sheet->setCellValue('A'.$chrRow, ($i+1));
                        $sheet->setCellValue('B'.$chrRow, $ledger_groups[$i]->name);
                        $sheet->setCellValue('C'.$chrRow, $ledger_groups[$i]->parentname);
                        $sheet->setCellValue('D'.$chrRow, $ledger_groups[$i]->type);
                        $sheet->setCellValue('E'.$chrRow, $ledger_groups[$i]->alias_name);
                            
                        $sheet->cells('A'.$chrRow.':E'.$chrRow, function($cells) {
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
                <div style="text-align:center;"><h1>Account Group List</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                               <td style="padding:10px 5px;color:#fff;"> Group Name</td>
                               <td style="padding:10px 5px;color:#fff;"> Parent Group</td>
                               <td style="padding:10px 5px;color:#fff;"> Group Type</td>
                               <td style="padding:10px 5px;color:#fff;"> Alias</td>
                            </tr>
                        </thead>
                        <tbody class="ledgerbody" id="ledgerbody" >';
            $slno=0;
            foreach ($ledger_groups as $ledger) {
                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' .  $ledger->name . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $ledger->parentname . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $ledger->type . '</td>
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
            return $pdf->download('mtg_acc_group.pdf');
        }
    }

}
