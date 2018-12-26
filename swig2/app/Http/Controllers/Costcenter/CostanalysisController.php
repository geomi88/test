<?php

namespace App\Http\Controllers\Costcenter;

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
use App\Models\Usermodule;
use App;
use DB;
use Mail;

class CostanalysisController extends Controller {

    public function index(Request $request) {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $currentMonth = date('m');
        $currentYear = date('Y');

        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
                ->whereraw("EXTRACT(MONTH FROM pos_date)=$currentMonth AND EXTRACT(YEAR FROM pos_date)=$currentYear and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');
                
        // monthly sale data for pi graph
        $region_sales = DB::table('pos_sales')
                ->select("region.id as region_id","region.name as region", db::raw('sum(pos_sales.total_sale) as region_sale'))
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereRaw("EXTRACT(MONTH FROM pos_date)=$currentMonth AND EXTRACT(YEAR FROM pos_date)=$currentYear and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->groupBy(['region.id','region.name'])
                ->get();
        
        
        $graph_pi = array();
        $full_row = array();
        $full_pi_graph_data = array();

        foreach ($region_sales as $sale) {

            $percent = ($sale->region_sale/$actualsale)*100;
            $percent = round($percent,2);
            $row = array("name"=>"$sale->region","y"=>$percent,"id"=>$sale->region_id);
            array_push($graph_pi, $row);
        }

        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);
        //  Pi graph ends
        
        return view('costcenter/cost_analysis/index',
                array(  
                        "currentMonth"=>$currentMonth,
                        "currentYear"=>$currentYear,
                        "full_pi_graph_data" => $full_pi_graph_data,

                        ));
    }
    
    
    public function getmonthlysale() {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $currentMonth = Input::get('cmbmonth');
        $currentYear = Input::get('cmbyear');

        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
                ->whereraw("EXTRACT(MONTH FROM pos_date)=$currentMonth AND EXTRACT(YEAR FROM pos_date)=$currentYear and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');
                
        // monthly sale data for pi graph
        $region_sales = DB::table('pos_sales')
                ->select("region.id as region_id","region.name as region", db::raw('sum(pos_sales.total_sale) as region_sale'))
                ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                ->whereRaw("EXTRACT(MONTH FROM pos_date)=$currentMonth AND EXTRACT(YEAR FROM pos_date)=$currentYear and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                ->groupBy(['region.id','region.name'])
                ->get();
        
        
        $graph_pi = array();
        $full_row = array();
        $full_pi_graph_data = array();

        foreach ($region_sales as $sale) {

            $percent = ($sale->region_sale/$actualsale)*100;
            $percent = round($percent,2);
            $row = array("name"=>"$sale->region","y"=>$percent,"id"=>$sale->region_id);
            array_push($graph_pi, $row);
        }

        $full_pi_graph_data = array_values($graph_pi);
        $full_pi_graph_data = json_encode($full_pi_graph_data);
        //  Pi graph ends
        
        return view('costcenter/cost_analysis/index',
                array(  
                        "currentMonth"=>$currentMonth,
                        "currentYear"=>$currentYear,
                        "full_pi_graph_data" => $full_pi_graph_data,

                        ));
    }
    
    public function getbranchgraph() {
        
        if (Session::get('company')) {
            $company_id = Session::get('company');
        }
        
        $currentMonth = Input::get('month');
        $currentYear = Input::get('year');
        $branchid = Input::get('branchid');
        
        $actualsale = DB::table('pos_sales')
                ->select(DB::raw('COALESCE(sum(pos_sales.total_sale),0) as total_sale'))
                ->whereraw("EXTRACT(MONTH FROM pos_date)=$currentMonth AND EXTRACT(YEAR FROM pos_date)=$currentYear and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1 AND pos_sales.branch_id=$branchid")
                ->where('company_id', '=', $company_id)
                ->value('total_sale');
        
         
        $arrcost = DB::table('branch_fixed_cost as b')
                ->select('m.name as cost_name', db::raw("sum(b.cost_amount) as cost_amount"))
                ->leftjoin('master_resources as m', 'b.cost_id', '=', 'm.id')
                ->whereraw("b.branch_id=$branchid")
//                ->whereraw("b.branch_id=$branchid AND EXTRACT(MONTH FROM created_at)=$currentMonth AND EXTRACT(YEAR FROM created_at)=$currentYear")
                ->groupBy(['b.cost_id'])
                ->get();
        
        $branch = DB::table('master_resources as m')
                ->select('m.name as br_name','m.branch_code as branch_code')
                ->whereRaw("m.id=$branchid")
                ->first();
                
        
       
        $totalCost=$arrcost->sum('cost_amount');
        if($totalCost==0){
            $totalCost=1;
        }
        
        $salepercent=round($actualsale/$totalCost*100,2);

        $arrCategory[]='Sales (Achieved '.$salepercent.' % of Expense)';
        $arrSeries[]=round($actualsale,2);
       
        if(count($arrcost)>0){
            $arrCategory[]='Expense (100 %)';
            $arrSeries[]=$totalCost;
            foreach ($arrcost as $value) {
                $arrCategory[]= str_replace("_", " ", $value->cost_name)." (".round($value->cost_amount/$totalCost*100,2)."%)";
                $arrSeries[]= round($value->cost_amount,2);
            }
        }
        
        $arrCategory = array_values($arrCategory);
        $arrSeries = array_values($arrSeries);
        //  Pi graph ends
        
        return \Response::json(array('arrCategory' => $arrCategory,'arrSeries'=>$arrSeries,'branch'=>$branch->branch_code." : ".$branch->br_name));
        
    }

    public function getbranches() {
        try {
            $region_id = Input::get('region_id');
            $region_name = Input::get('region_name');
            $currentMonth = Input::get('month');
            $currentYear = Input::get('year');



            $branches = DB::table('master_resources as branch')
                    ->select("branch.id as branchid","branch.name as br_name")
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereRaw("area.region_id=$region_id AND branch.status=1")
                    ->orderby("br_name","ASC")
                    ->get();

            $arrAllBranch=array();
            if(count($branches)!=0){
                $arrAllBranch= $branches->pluck("branchid")->toArray();
            }

            $branch_sales = DB::table('pos_sales')
                    ->select("pos_sales.branch_id","branch.name as br_name", db::raw('sum(pos_sales.total_sale) as total'))
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->whereRaw("(EXTRACT(MONTH FROM pos_date)=$currentMonth AND EXTRACT(YEAR FROM pos_date)=$currentYear) and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->whereIn('pos_sales.branch_id',$arrAllBranch)
                    ->groupBy(['pos_sales.branch_id','branch.name'])
                    ->orderby("total","DESC")
                    ->get();

            $regionTotalSale=0;
            if(count($branch_sales)>0){
                $regionTotalSale=$branch_sales->sum('total');
            }

            $branchOrderOfSale = array();
            foreach ($branch_sales as $branch) {
                $branchOrderOfSale[$branch->branch_id]=array("branch_id"=>$branch->branch_id,"br_name"=>$branch->br_name,"totSale"=>$branch->total,"targetSale"=>'');
            }

           
            //******************************* ends *************************************     

            foreach ($branches as $branch) {
                if(!key_exists($branch->branchid, $branchOrderOfSale)){
                    $branchOrderOfSale[$branch->branchid]=array("branch_id"=>$branch->branchid,"br_name"=>$branch->br_name,"totSale"=>0,"targetSale"=>0);
                }
            }

            $public_path= url('/');
            if (count($branchOrderOfSale) != 0) {
                $strHtml = '<h2>'.$region_name.'<input type="text" placeholder="search" name="search" id="search" onkeyup="filterbranch()">'
                            . '<input type="hidden" name="regionid" id="regionid" value='.$region_id.'>'
                            . '<input type="hidden" name="regionname" id="regionname" value='.$region_name.'>'
                            . '<span style="float: right;padding-right:28px;">'.number_format($regionTotalSale,2).'</span></h2><div id="branchpop"><ul class="listTypeV2">';

                foreach ($branchOrderOfSale as $branch) {
                    $strHtml.= '<li class=""> <a href="javascript:showbranchgraph('.$branch['branch_id'].')" class="scrollBtmGraph">'.$branch['br_name'].'<span style="float: right;padding-right:10px;">'.number_format($branch['totSale'],2).'</span></a></li>';
                }

                $strHtml.='</ul></div>';

                echo $strHtml;
            } else {
                echo "No Branches";
            }
        } catch (\Exception $e) {
            return -1;
        }
    }
    
    public function filterbranch() {
        try {
            $region_id = Input::get('region_id');
            $region_name = Input::get('region_name');
            $currentMonth = Input::get('month');
            $currentYear = Input::get('year');
            $search = Input::get('search');



            $branches = DB::table('master_resources as branch')
                    ->select("branch.id as branchid","branch.name as br_name")
                    ->leftjoin('master_resources as area', 'branch.area_id', '=', 'area.id')
                    ->leftjoin('master_resources as region', 'area.region_id', '=', 'region.id')
                    ->whereRaw("area.region_id=$region_id AND branch.status=1")
                    ->when($search,function($query) use($search){
                        return $query->whereRaw("branch.name like '%$search%'");
                    })
                    ->orderby("br_name","ASC")
                    ->get();

            $arrAllBranch=array();
            if(count($branches)!=0){
                $arrAllBranch= $branches->pluck("branchid")->toArray();
            }

            $branch_sales = DB::table('pos_sales')
                    ->select("pos_sales.branch_id","branch.name as br_name", db::raw('sum(pos_sales.total_sale) as total'))
                    ->leftjoin('master_resources as branch', 'pos_sales.branch_id', '=', 'branch.id')
                    ->whereRaw("(EXTRACT(MONTH FROM pos_date)=$currentMonth AND EXTRACT(YEAR FROM pos_date)=$currentYear) and pos_sales.added_by_user_type = 'Supervisor' and pos_sales.status=1")
                    ->whereIn('pos_sales.branch_id',$arrAllBranch)
                    ->when($search,function($query) use($search){
                        return $query->whereRaw("branch.name like '%$search%'");
                    })
                    ->groupBy(['pos_sales.branch_id','branch.name'])
                    ->orderby("total","DESC")
                    ->get();

            $regionTotalSale=0;
            if(count($branch_sales)>0){
                $regionTotalSale=$branch_sales->sum('total');
            }

            $branchOrderOfSale = array();
            foreach ($branch_sales as $branch) {
                $branchOrderOfSale[$branch->branch_id]=array("branch_id"=>$branch->branch_id,"br_name"=>$branch->br_name,"totSale"=>$branch->total,"targetSale"=>'');
            }

           
            //******************************* ends *************************************     

            foreach ($branches as $branch) {
                if(!key_exists($branch->branchid, $branchOrderOfSale)){
                    $branchOrderOfSale[$branch->branchid]=array("branch_id"=>$branch->branchid,"br_name"=>$branch->br_name,"totSale"=>0,"targetSale"=>0);
                }
            }

            $public_path= url('/');
            if (count($branchOrderOfSale) != 0) {
                $strHtml = '<div id="branchpop"><ul class="listTypeV2">';

                foreach ($branchOrderOfSale as $branch) {
                    $strHtml.= '<li class=""> <a href="javascript:showbranchgraph('.$branch['branch_id'].')" class="scrollBtmGraph">'.$branch['br_name'].'<span style="float: right;padding-right:10px;">'.number_format($branch['totSale'],2).'</span></a></li>';
                }

                $strHtml.='</ul></div>';

                echo $strHtml;
            } else {
                echo "No Branches";
            }
        } catch (\Exception $e) {
            return -1;
        }
    }
}
