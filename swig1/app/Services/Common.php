<?php
namespace App\Services;

use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Config;
use Intervention\Image\ImageManagerStatic;
use DB;
use App\Model\Property;
use Exception;

class Common {
/*
 *  get details for a peoperty id
 * @param : Ecryp(id) 
 *  @response : araar object / null
 */
    
    public function getProductDetails($p_id=null) {
        $this->lang =Session::get('lang');
        
        if($p_id!=null){
        
            
            $property = array();
            
            $property[] = "properties.*";
            $property[] = "properties.name_" . $this->lang . " as name ";
            $property[] = "properties.description_" . $this->lang . " as description ";
            $property[] = "properties.address_line1_" . $this->lang . " as address1 ";
            $property[] = "properties.address_line2_" . $this->lang . " as address2 ";
            $property[] = "properties.zip_" . $this->lang . " as zip ";
            $property[] = "neighbourhoods.name_" . $this->lang . " as neighborname ";
            $property[] = "neighbourhoods.description_" . $this->lang . " as neighbordesc ";
            $property[] = "neighbourhoods.id  as neighborId ";
           

            $property = implode(',', $property);
            $property_list = Property::select(DB::raw($property), 'municipalities.name as municipality','property_images.image as mainimage','neighbourhood_images.image as neighborImage')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                   // ->leftjoin('neighbourhoods', 'neighbourhoods.id', '=', 'properties.neighborhood')
                    ->leftjoin('neighbourhoods', function($join) {
                         $join->on('neighbourhoods.id', '=', 'properties.neighborhood');
                         $join->where('neighbourhoods.status', '=', '1');
                     })
                    ->leftjoin('property_images', function($join) {
                         $join->on('property_images.property_id', '=', 'properties.id');
                         $join->where('property_images.main_image', '=', '1');
                         $join->where('property_images.status', '=', '1');
                     })
                   ->leftjoin('neighbourhood_images', function($join) {
                         $join->on('neighbourhood_images.neighbourhood_id', '=', 'properties.neighborhood');
                         $join->where('neighbourhood_images.main_image', '=', '1');
                         $join->where('neighbourhood_images.status', '=', '1');
                     })
                             
                    ->whereRaw("properties.id=$p_id")
                    ->whereRaw("properties.status='1'")
                    ->first();
            $neighborId='0';
            if($property_list!=""){
                $neighborId=$property_list->neighborhood;
            }
            $gallery= DB::table('property_images')
                ->select('property_images.*')
                ->whereRaw("property_id=$p_id")
                ->whereRaw("main_image is NULL")
                ->whereRaw("status ='1'")
                ->limit(4)
                ->get();
            $sliderGallery= DB::table('property_images')
                ->select('property_images.*')
                ->whereRaw("property_id=$p_id")
                ->whereRaw("status ='1'")
                ->get();
            
            $similar = Property::select(DB::raw($property), 'municipalities.name  as municipality','property_images.image as mainimage')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('neighbourhoods', 'neighbourhoods.id', '=', 'properties.neighborhood')
                    ->leftjoin('property_images', function($join) {
                         $join->on('property_images.property_id', '=', 'properties.id');
                         $join->where('property_images.main_image', '=', '1');
                     })
                    ->whereRaw("properties.id<>$p_id")
                    ->where('properties.status', '=', '1');
                    if($neighborId!=NULL){
                         $similar=   $similar->whereRaw("properties.neighborhood = $neighborId");
                    }
                    
                    //->whereRaw("property_images.main_image='1'")
                     $similar=  $similar->limit(3);
                   $similar=$similar->get();
         
        $pageData['property']=$property_list;
        $pageData['gallery']=$gallery;
        $pageData['sliderGallery']=$sliderGallery;
        $pageData['similar']=$similar;
        
        return $pageData;
    }

}
 
public function getBasics() {
    
            $pageData['buildings']="";
            $pageData['neighborhoods']="";
            $pageData['municipalities']="";
            $pageData['reference_number']="";
            $pageData['district']="";
    
            $buildings= DB::table('building_type')
                  ->select('*')
                  ->where('status', '=', '1')
                  ->get();
            $neighborhoods= DB::table('neighbourhoods')
                  ->select('id','name_en as name')
                  ->where('status', '=', '1')
                  ->get();
            
            $municipalities= DB::table('municipalities')
                  ->select('*')
                  ->where('status', '=', '1')
                  ->get();
            
            $districts= DB::table('districts')
                  ->select('*')
                  ->where('status', '=', '1')
                  ->get();
            
            $pageData['buildings']=$buildings;
            $pageData['neighborhoods']=$neighborhoods;
            $pageData['municipalities']=$municipalities;
            $pageData['district']=$districts;
            
            return $pageData;
}
 public function districts() {
        $districts=array();
        $districts = DB::table('districts')
                ->select('*')
                ->where('status', '=', '1')
                ->get();
         return $districts;
    }

    public function agents() {
       $agents=array();
       $agents = DB::table('agents')
                ->select('*')
                ->where('agents.status', '=', '1')
                ->get();
       return $agents;
    }
    
    public function municipalities() {
       $municipalities=array();
       $municipalities = DB::table('municipalities')
                ->select('*')
                ->where('municipalities.status', '=', '1')
                ->get();
       return $municipalities;
    }

}