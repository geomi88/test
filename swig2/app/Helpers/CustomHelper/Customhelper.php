<?php
//app/Helpers/Envato/User.php
namespace App\Helpers\CustomHelper;
 
use Illuminate\Support\Facades\DB;
 
class Customhelper {
    /**
     * @param int $user_id User-id
     * 
     * @return string
     */
    public static function calculate_vat($taxpercent,$cashamount) {
        
        
         if($taxpercent==""){
                    $taxpercent=5;
                }
                
                $taxval=$taxpercent/100;
                $vatval=1+$taxval;
                
           $cash_percent=($cashamount/$vatval);
          
            return $cash_percent;
    }
    
    
      public static function numberformatter($number) {
        
        
         $formatted_num=number_format($number,2);
          
            return $formatted_num;
    }
}
