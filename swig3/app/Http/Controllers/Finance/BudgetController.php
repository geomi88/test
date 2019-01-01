<?php

namespace App\Http\Controllers\Finance;

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
use App\Models\Budget;
use App;
use DB;
use Mail;
use App\Services\Commonfunctions;
use Excel;

class BudgetController extends Controller {

    public function index(Request $request) {
        
            $paginate = Config::get('app.PAGINATE');
            $cost_centres = Config::get('app.COST_CENTRE');
            if($request->ajax()){
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                $type = Input::get('type');
                $year = Input::get('year');
//                $quarter = Input::get('quarter');
                $search = Input::get('search');
                if($type=='Inventory'){
                    $accounts = DB::table('inventory')
                            ->select('inventory.name as first_name','inventory.product_code as code','inventory.id as type_id','ac_budget.price_budget as price','ac_budget.id as budget_id','ac_budget.quantity_budget as quantity','primary.name as primunit',DB::raw('"" as last_name'))
                            ->leftjoin('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'inventory.id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
                             })
                             ->leftjoin('units as primary', 'inventory.primary_unit', '=', 'primary.id')
                             ->where('inventory.status', '!=', 2)
                            ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(inventory.name like '%$search%' OR inventory.product_code like '%$search%')");
                            })
                            ->orderby('ac_budget.price_budget','asc')
                            ->orderby('inventory.name','asc')
                            ->paginate($paginate);
                }elseif($type=='Branch'||$type=='Warehouse'||$type=='Office'){
                    $accounts = DB::table('master_resources')
                            ->select('master_resources.name as first_name','master_resources.alias_name as last_name','master_resources.branch_code as code','master_resources.id as type_id','ac_budget.price_budget as price','ac_budget.id as budget_id','ac_budget.quantity_budget as quantity')
                            ->leftjoin('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'master_resources.id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
//                                    $join->where('ac_budget.quarter', '=', $quarter);
                             })
                            ->where('master_resources.resource_type', '=', $type)
                            ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(master_resources.name like '%$search%' OR master_resources.branch_code like '%$search%')");
                            })
                            ->orderby('ac_budget.price_budget','asc')
                            ->orderby('master_resources.name','asc')
                            ->paginate($paginate);
                }else{
                    $accounts = DB::table('ac_accounts')
                            ->select('ac_accounts.first_name as first_name','ac_accounts.alias_name as last_name','ac_accounts.code as code','ac_accounts.type_id as type_id','ac_budget.price_budget as price','ac_budget.id as budget_id','ac_budget.quantity_budget as quantity')
                            ->leftjoin('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'ac_accounts.type_id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
//                                    $join->where('ac_budget.quarter', '=', $quarter);
                             })
                            ->where('ac_accounts.type', '=', $type)
                            ->where('ac_accounts.status', '=', 1)
                            ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(ac_accounts.first_name like '%$search%' OR ac_accounts.code like '%$search%' OR concat(ac_accounts.first_name,' ',ac_accounts.alias_name,' ',ac_accounts.last_name) like '%$search%')");
                            })
                            ->orderby('ac_budget.price_budget','asc')
                            ->orderby('ac_accounts.first_name','asc')
                            ->paginate($paginate);            
                } 
                return view('finance/budget_creation/budgetcreationresults', array('accounts' => $accounts,'type' => $type,'year' => $year));
            }
            $accounts = DB::table('ac_accounts')
                        ->select('ac_accounts.*','ac_budget.price_budget as price','ac_budget.quantity_budget as quantity')
                        ->leftjoin('ac_budget', function($join){
                                $join->on('ac_budget.budget_type_id', '=', 'ac_accounts.type_id');
                                $join->where('ac_budget.status', '=', 1);
                                $join->where('ac_budget.budget_type', '=', '');
                         })
                        ->where('ac_accounts.type', '=', '')
                         ->where('ac_accounts.status', '=', 1)
                         ->paginate($paginate);  
            return view('finance/budget_creation/budgetcreation',array('accounts' => $accounts,'cost_centres' => $cost_centres));
        
    }
    
    public function add_budget(){
        try {
            $year = Input::get('year');
//            $quarter = Input::get('quarter');
            $type = Input::get('type'); 
            $arrayList = Input::get('arrayList');  
            $resp=array();
            foreach($arrayList as $array){
                $budget = new Budget();
                $budget->year = $year;
//                $budget->quarter = $quarter;
                $budget->budget_type = $type;
                $budget->budget_type_id = $array['type_id'];
                $budget->price_budget = $array['value'];
                $budget->quantity_budget=$array['quantity'];        
                $budget->status=1;
                if($array['value']!='' && $array['budget_id']==''){
                     $budget->save(); 
                } 
                if($array['budget_id']!=''){
                    if($array['value']==''){
                        $budget->price_budget = 0;
                    }
                    $budget->id=$array['budget_id'];
                    $budget->exists=true;
                    $budget->save();
                }
                if(isset($budget->id) ){
                   $resp['rel'][]=$budget->id;  
                }else{
                    $resp['rel'][]="";
                }
            }
            $resp['message']=1;

            return $resp;
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!');
            $resp['message']=-1;
            return $resp;
        }
    }
    
    public function budget_plan(Request $request) {
        try {
            $pageData = array();
            $paginate = Config::get('app.PAGINATE');
            $cost_centres = Config::get('app.COST_CENTRE');
            if($request->ajax()){
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }
                $type = Input::get('type');
                $year = Input::get('year');
                $search = Input::get('searchbyname');
                $startDate = $year.'-01-01';
                $endDate = $year.'-12-31';
                if($type=='Inventory'){
                    $accounts = DB::table('inventory')
                            ->select('inventory.name as first_name','inventory.product_code as code','inventory.id as type_id','ac_budget.price_budget',DB::raw('"" as last_name,SUM(requisition_items.total_price) AS used'))
                            ->join('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'inventory.id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
                                    $join->where('ac_budget.price_budget', '!=', 0);
                             })
                             ->leftjoin('requisition_items', function($join) use ($type,$startDate,$endDate){
                                    $join->on('requisition_items.requisition_item_id', '=', 'inventory.id')
                                            ->leftjoin('requisition', function($join1) use ($type,$startDate,$endDate){
                                                $join1->on('requisition.id','=','requisition_items.requisition_id');
                                                $join1->where('requisition.status', '=', 4);
                                                $join1->whereBetween('requisition.created_at', array($startDate, $endDate));
                                        });
                             })
                             ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(inventory.name like '$search%')");
                            })
                            ->groupby('inventory.id')
                            ->orderby('inventory.name','asc')
                            ->paginate($paginate);
                }elseif($type=='Branch'||$type=='Warehouse'||$type=='Office'){
                    $accounts = DB::table('master_resources')
                            ->select('master_resources.name as first_name','master_resources.alias_name as last_name','master_resources.branch_code as code','ac_budget.price_budget','requisition.total_price as used',
                                    DB::raw("(select sum(price_budget) from ac_budget where ac_budget.budget_type='$type' AND ac_budget.year=$year) as total,SUM(requisition.total_price) AS used"))
                                   
                            ->join('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'master_resources.id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
                                    $join->where('ac_budget.price_budget', '!=', 0);
                                    
                             })
                             ->leftjoin('requisition', function($join) use ($type,$startDate,$endDate){
                                    $join->on('requisition.party_id', '=', 'master_resources.id');
                                    $join->where('requisition.party_type', '=', $type);
                                    $join->where('requisition.status', '=', 4);
                                    $join->whereBetween('requisition.created_at', array($startDate, $endDate));
                                    
                             })
                            ->where('master_resources.resource_type', '=', $type)
                            ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(master_resources.name like '$search%')");
                            })
                            ->groupby('master_resources.id')
                            ->orderby('master_resources.name','asc')
                            ->paginate($paginate);
                            
                }else if($type=='General Ledger') {
                    $accounts = DB::table('ac_accounts')
                            ->select('ac_accounts.first_name as first_name','ac_accounts.alias_name as last_name','ac_accounts.code as code','ac_accounts.type_id as type_id','ac_budget.price_budget','ac_budget.quantity_budget as quantity',DB::raw('SUM(requisition.total_price) AS used'))
                            ->join('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'ac_accounts.type_id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
                                    $join->where('ac_budget.price_budget', '!=', 0);
                                    
                             })
                             ->leftjoin('requisition', function($join) use ($type,$startDate,$endDate){
                                    $join->on('requisition.general_ledger', '=', 'ac_accounts.type_id');
//                                    $join->where('requisition.party_type', '=', 'Employee');
                                    $join->where('requisition.status', '=', 4);
                                    $join->whereBetween('requisition.created_at', array($startDate, $endDate));
                                    
                             })
                            ->where('ac_accounts.type', '=', $type)
                            ->where('ac_accounts.status', '=', 1)
                            ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(ac_accounts.first_name like '$search%' or concat(ac_accounts.first_name,' ',ac_accounts.alias_name,' ',ac_accounts.last_name) like '$search%')");
                            })
                            ->groupby('ac_accounts.id')
                            ->orderby('ac_accounts.first_name','asc')
                            ->paginate($paginate);            
                
                }else{
                    $accounts = DB::table('ac_accounts')
                            ->select('ac_accounts.first_name as first_name','ac_accounts.alias_name as last_name','ac_accounts.code as code','ac_accounts.type_id as type_id','ac_budget.price_budget','ac_budget.quantity_budget as quantity',DB::raw('SUM(requisition.total_price) AS used'))
                            ->join('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'ac_accounts.type_id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
                                    $join->where('ac_budget.price_budget', '!=', 0);
                                    
                             })
                             ->leftjoin('requisition', function($join) use ($type,$startDate,$endDate){
                                    $join->on('requisition.party_id', '=', 'ac_accounts.type_id');
                                    $join->where('requisition.party_type', '=', $type);
                                    $join->where('requisition.status', '=', 4);
                                    $join->whereBetween('requisition.created_at', array($startDate, $endDate));
                                    
                             })
                            ->where('ac_accounts.type', '=', $type)
                            ->where('ac_accounts.status', '=', 1)
                            ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(ac_accounts.first_name like '$search%' or concat(ac_accounts.first_name,' ',ac_accounts.alias_name,' ',ac_accounts.last_name) like '$search%')");
                            })
                            ->groupby('ac_accounts.id')
                            ->orderby('ac_accounts.first_name','asc')
                            ->paginate($paginate);            
                } 
                return view('finance/budget_plan/budget_result', array('accounts' => $accounts));
            }
            $accounts = DB::table('ac_accounts')
                        ->select('ac_accounts.*','ac_budget.price_budget as price','ac_budget.quantity_budget as quantity')
                        ->leftjoin('ac_budget', function($join){
                                $join->on('ac_budget.budget_type_id', '=', 'ac_accounts.type_id');
                                $join->where('ac_budget.status', '=', 1);
                                $join->where('ac_budget.budget_type', '=', '');
                         })
                        ->where('ac_accounts.type', '=', '')
                         ->where('ac_accounts.status', '=', 1)
                         ->paginate($paginate); 
            return view('finance/budget_plan/budgetplan', array('accounts' => $accounts,'cost_centres'=>$cost_centres));
        } catch (\Exception $e) {          
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return -1;
        }
    }
    
    public function exportdata() {
        try {
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            $excelorpdf = Input::get('excelorpdf');
            $type = Input::get('ledger_id');
            $year = Input::get('cmbyear');
            $search = Input::get('searchbyname');
            $startDate = $year . '-01-01';
            $endDate = $year . '-12-31';
            if($type=='Inventory'){
                    $accounts = DB::table('inventory')
                            ->select('inventory.name as first_name','inventory.product_code as code','inventory.id as type_id','ac_budget.price_budget',DB::raw('"" as last_name,SUM(requisition_items.total_price) AS used'))
                            ->join('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'inventory.id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
                                    $join->where('ac_budget.price_budget', '!=', 0);
                             })
                             ->leftjoin('requisition_items', function($join) use ($type,$startDate,$endDate){
                                    $join->on('requisition_items.requisition_item_id', '=', 'inventory.id')
                                            ->leftjoin('requisition', function($join1) use ($type,$startDate,$endDate){
                                                $join1->on('requisition.id','=','requisition_items.requisition_id');
                                                $join1->where('requisition.status', '=', 4);
                                                $join1->whereBetween('requisition.created_at', array($startDate, $endDate));
                                        });
                             })
                             ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(inventory.name like '$search%')");
                            })
                            ->groupby('inventory.id')
                            ->orderby('inventory.name','asc')
                            ->get();  
                }elseif($type=='Branch'||$type=='Warehouse'||$type=='Office'){
                    $accounts = DB::table('master_resources')
                            ->select('master_resources.name as first_name','master_resources.alias_name as last_name','master_resources.branch_code as code','ac_budget.price_budget','requisition.total_price as used',
                                    DB::raw("(select sum(price_budget) from ac_budget where ac_budget.budget_type='$type' AND ac_budget.year=$year) as total,SUM(requisition.total_price) AS used"))
                                   
                            ->join('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'master_resources.id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
                                    $join->where('ac_budget.price_budget', '!=', 0);
                                    
                             })
                             ->leftjoin('requisition', function($join) use ($type,$startDate,$endDate){
                                    $join->on('requisition.party_id', '=', 'master_resources.id');
                                    $join->where('requisition.party_type', '=', $type);
                                    $join->where('requisition.status', '=', 4);
                                    $join->whereBetween('requisition.created_at', array($startDate, $endDate));
                                    
                             })
                            ->where('master_resources.resource_type', '=', $type)
                            ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(master_resources.name like '$search%')");
                            })
                            ->groupby('master_resources.id')
                            ->orderby('master_resources.name','asc')
                            ->get();  
                }else{
                    $accounts = DB::table('ac_accounts')
                            ->select('ac_accounts.first_name as first_name','ac_accounts.alias_name as last_name','ac_accounts.code as code','ac_accounts.type_id as type_id','ac_budget.price_budget','ac_budget.quantity_budget as quantity',DB::raw('SUM(requisition.total_price) AS used'))
                            ->join('ac_budget', function($join) use ($type,$year){
                                    $join->on('ac_budget.budget_type_id', '=', 'ac_accounts.type_id');
                                    $join->where('ac_budget.status', '=', 1);
                                    $join->where('ac_budget.budget_type', '=', $type);
                                    $join->where('ac_budget.year', '=', $year);
                                    $join->where('ac_budget.price_budget', '!=', 0);
                                    
                             })
                             ->leftjoin('requisition', function($join) use ($type,$startDate,$endDate){
                                    $join->on('requisition.party_id', '=', 'ac_accounts.type_id');
                                    $join->where('requisition.party_type', '=', $type);
                                    $join->where('requisition.status', '=', 4);
                                    $join->whereBetween('requisition.created_at', array($startDate, $endDate));
                                    
                             })
                            ->where('ac_accounts.type', '=', $type)
                            ->where('ac_accounts.status', '=', 1)
                            ->when($search, function ($query) use ($search) {
                                return $query->whereRaw("(ac_accounts.first_name like '$search%' or concat(ac_accounts.first_name,' ',ac_accounts.alias_name,' ',ac_accounts.last_name) like '$search%')");
                            })
                            ->groupby('ac_accounts.id')
                            ->orderby('ac_accounts.first_name','asc')
                            ->get();      
                }

            if ($excelorpdf == "EXCEL") {

                Excel::create('Budget Variance', function($excel) use($accounts) {
                    // Set the title
                    $excel->setTitle('Budget Plans');

                    $excel->sheet('Budget Variance', function($sheet) use($accounts) {
                        // Sheet manipulation

                        $sheet->setCellValue('D3', 'Budget Variance');
                        $sheet->setHeight(3, 20);
                        $sheet->cells('A3:G3', function($cells) {
                            $cells->setBackground('#00CED1');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(14);
                        });

                        $chrRow = 6;

                        $sheet->row(5, array('Sl No.', 'Name', 'Total Amount','Used Amount','Used Percent','Budget Variance','Variance Percent'));
                        $sheet->setHeight(5, 15);
                        $sheet->cells('A5:G5', function($cells) {
                            $cells->setBackground('#6495ED');
                            $cells->setFontWeight('bold');
                        });

                        for ($i = 0; $i < count($accounts); $i++) {
                            $slno = $i + 1;
                            $usedPercent = ($accounts[$i]->used*100)/$accounts[$i]->price_budget;
                            $balancePercent = (($accounts[$i]->price_budget-$accounts[$i]->used)*100)/$accounts[$i]->price_budget;
                            $sheet->setCellValue('A' . $chrRow, $slno);
                            $sheet->setCellValue('B' . $chrRow, $accounts[$i]->first_name);
                            $sheet->setCellValue('C' . $chrRow, $accounts[$i]->price_budget);
                            $sheet->setCellValue('D' . $chrRow, $accounts[$i]->used);
                            $sheet->setCellValue('E' . $chrRow, $usedPercent.' %');
                            $sheet->setCellValue('F' . $chrRow, ($accounts[$i]->price_budget-$accounts[$i]->used));
                            $sheet->setCellValue('G' . $chrRow, $balancePercent.' %');

                            $sheet->cells('A' . $chrRow . ':G' . $chrRow, function($cells) {
                                $cells->setFontSize(9);
                            });

                            $chrRow++;
                        }
                    });
                })->export('xls');
            } /* else{

              $html_table = '<!DOCTYPE html>
              <html>
              <head>
              <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
              <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
              <title>Requisition Outbox</title>
              <style>
              .listerType1 tr:nth-of-type(2n) {
              background: rgba(0, 75, 111, 0.12) none repeat 0 0;
              }

              </style>
              </head>
              <body style="margin: 0; padding: 0;font-family: DejaVu Sans;">
              <section id="container">
              <div style="text-align:center;"><h1>Requisition Inbox</h1></div>
              <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
              <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
              <tr class="headingHolder">
              <td style="padding:10px 10px;color:#fff;"> Requsition Code </td>
              <td style="padding:10px 10px;color:#fff;"> Title </td>
              <td style="padding:10px 5px;color:#fff;"> Type </td>
              <td style="padding:10px 5px;color:#fff;"> Date </td>
              <td style="padding:10px 5px;color:#fff;"> Description</td>
              </tr>
              </thead>
              <tbody class="categorybody" id="categorybody" >';
              $slno=0;
              foreach ($requisitions as $cat) {
              $html_table .='<tr>
              <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->requisition_code . '</td>
              <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->title . '</td>
              <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->name . '</td>
              <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($cat->created_at)) . '</td>
              <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $cat->description . '</td>
              </tr>';
              }
              $html_table .='</tbody>
              </table>
              </section>
              </body>
              </html>';

              $pdf = App::make('dompdf.wrapper');
              $pdf->loadHTML($html_table);
              return $pdf->download('mtg_requsition_outbox_report.pdf');
              } */
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('finance/budget_plan');
        }
    }

}