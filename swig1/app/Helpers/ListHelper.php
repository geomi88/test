<?php

namespace App\Helpers;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


class ListHelper {
    /**
     * @param   
     * Directly called in views
     * @return muncipalities object array
     */
    public static function municipalityList() {
         $lang=Session::get('lang');
        /*SELECT 
   m.name , count(p.id)
FROM
   muncipalities m
LEFT OUTER JOIN 
   properties p ON m.id=p.muncipality_id
GROUP BY p.muncipality_id 
 */
       $list=DB::table('municipalities as m')
               ->select( 'm.*',"m.name as name",DB::raw('count(p.municipality_id) as property_count') )
               ->leftjoin('properties  as p','m.id','=','p.municipality_id')
               ->groupby('m.id')
               ->orderby('property_count','DESC')
               ->orderby('m.name','ACS')
               ->where('m.status',1)->get(); 
       return $list;
    }

}
