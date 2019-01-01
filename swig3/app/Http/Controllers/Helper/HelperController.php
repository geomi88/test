<?php

namespace App\Http\Controllers\Helper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Kamaln7\Toastr\Facades\Toastr;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use App\Models\Module;
use App\Models\Usermodule;
use DB;
use Mail;
use Customhelper;


class HelperController extends Controller {

   
     public function calculateTaxAfterVat() {
        $cash_collection = Input::get('cash_collection');
        $pos_date = Input::get('pos_date');
        $pos_date = explode('-',$pos_date);
        $pos_date = $pos_date[2].'-'.$pos_date[1].'-'.$pos_date[0];
         
       
        
        $tax_details = DB::table('master_resources')
                    ->select('tax_percent','tax_applicable_from','name','tax_function')
                    ->where('resource_type', '=', "TAX")->where('status', '=', 1)
                     ->orderBy('id', 'desc')->first();
        
        if (count($tax_details) > 0) {
           
            $taxpercent= $tax_details->tax_percent;
            $taxdate=$tax_details->tax_applicable_from;
            if(strtotime($pos_date)>=strtotime($taxdate)){
                
                
                if($taxpercent==""){
                    $taxpercent=5;
                }
                
           
                
       // $sales_percent  =($cash_collection/$vatval);
                
                $helperfunction=$tax_details->tax_function;
                
                $sales_percent=Customhelper::$helperfunction($taxpercent,$cash_collection);
              
             
             //   $sales_percent=Customhelper::calculateTaxAfterVat($taxpercent,$cash_collection);
                
            $cash_percent=$cash_collection-$sales_percent;
           
           
      // bfre changng with static vat 1.05
      //      $cash_percent=($cash_collection*$taxpercent)/100 ;
         //  $sales_percent=$cash_collection-$cash_percent;
           
         //  $sales_amount=number_format($sales_percent,2);
         //  $tax_amount=number_format($cash_percent,2);
           $sales_amount=Customhelper::numberformatter($sales_percent);
           $tax_amount=Customhelper::numberformatter($cash_percent);
          
           
           
           return \Response::json(array('sales_amount' => $sales_amount, 'tax_amount'=> $tax_amount));
         }else{
             
              $sales_amount=number_format($cash_collection,2);
             return \Response::json(array('sales_amount' =>$sales_amount, 'tax_amount'=> 0));
    
         }
           
          
        } else {
            
             return \Response::json(array('sales_amount' => 0, 'tax_amount'=> 0));
    
            
            }
    }
    
    
   

}