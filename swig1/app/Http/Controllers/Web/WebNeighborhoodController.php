<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Model\Property;
use App\Model\Neighbourhood;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Support\Facades\Config;
use App;
use DB;
use App\Services\Common;

class WebNeighborhoodController extends Controller {

    Public $lang;

    public function __construct() {
        
     //this value should dynamic

      //  App::setLocale($this->lang);
        //set some global objects
      
    }

  public function neighborhoods(Request $request) {
         try {
            $this->lang = App::getLocale();
              
            $neighborhood = array();
            
            $neighborhood[] = "neighbourhood_images.image as images";
            $neighborhood[] = "neighbourhoods.*";
            $neighborhood[] = "neighbourhoods.name_" . $this->lang . " as name ";
           // $neighborhood[] = "architects.address_" . $this->lang . " as address ";
            $neighborhood[] = "neighbourhoods.description_" . $this->lang . " as description ";
           

            $neighborhood = implode(',', $neighborhood);
            $neighborhood_list = Neighbourhood::select(DB::raw($neighborhood))
                        ->leftjoin('neighbourhood_images', function($join) {
                        $join->on('neighbourhood_images.neighbourhood_id', '=', 'neighbourhoods.id');
                        $join->where('neighbourhood_images.main_image', '=', '1');
                        $join->where('neighbourhood_images.status', '=', '1');

                        })
                ->where('neighbourhoods.status', '=', '1');
                $neighborhood_list = $neighborhood_list->groupby('neighbourhoods.id');
                $neighborhood_list = $neighborhood_list->get();
         
             $property = array();
            
            $property[] = "properties.*";
            $property[] = "properties.name_" . $this->lang . " as name ";
            $property[] = "properties.description_" . $this->lang . " as description ";
            $property[] = "properties.address_line1_" . $this->lang . " as address1 ";
            $property[] = "properties.address_line2_" . $this->lang . " as address2 ";
            $property[] = "properties.zip_" . $this->lang . " as zip ";
            $property[] = "neighbourhoods.name_" . $this->lang . " as neighborname ";
            $property[] = "neighbourhoods.description_" . $this->lang . " as neighbordesc ";
           

            $property = implode(',', $property);
                
            $similar = Property::select(DB::raw($property), 'municipalities.name  as municipality','property_images.image as mainimage')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('neighbourhoods', 'neighbourhoods.id', '=', 'properties.neighborhood')
                    ->leftjoin('property_images', function($join) {
                         $join->on('property_images.property_id', '=', 'properties.id');
                         $join->where('property_images.main_image', '=', '1');
                         $join->where('property_images.status', '=', '1');
                     })
                    //->whereRaw("properties.id<>$p_id")
                    ->where('properties.status', '=', '1')
                    ->where('properties.new_property', '=', '1');
                   $similar=  $similar->limit(3);
                   $similar=$similar->get();
                
            $dataArray=array(
                'neighborhood_list'=>$neighborhood_list,
                'similar'=>$similar
            );
             return view('web.neighborhood.neighborhoodlist',$dataArray);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
    }
    
    public function view_neighborhood($p_id) {
        try {
            
            $neighborhood = array();
            $nerabyNeigborhood=array();
            $gallery=array();
            $similar=array();
            $sliderGallery=array();
            $this->lang =Session::get('lang');
            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);
                
           
            
            $neighborhood[] = "neighbourhood_images.image as images";
            $neighborhood[] = "neighbourhoods.*";
            $neighborhood[] = "neighbourhoods.name_" . $this->lang . " as name ";
           // $neighborhood[] = "architects.address_" . $this->lang . " as address ";
            $neighborhood[] = "neighbourhoods.description_" . $this->lang . " as description ";
           

            $neighborhood = implode(',', $neighborhood);
            $neighborhood_detail = Neighbourhood::select(DB::raw($neighborhood))
                    ->leftjoin('neighbourhood_images', function($join) {
                        $join->on('neighbourhood_images.neighbourhood_id', '=', 'neighbourhoods.id');
                        $join->where('neighbourhood_images.main_image', '=', '1');
                        $join->where('neighbourhood_images.status', '=', '1');

                        })
                ->where('neighbourhoods.status', '=', '1')
                ->where('neighbourhoods.id', '=',$p_id)
                ->first();
                        
           
              
                $neighborId='0';
                if($neighborhood_detail!=""){
                    $neighborId=$neighborhood_detail->municipality_id;
                }
                
            $nerabyNeigborhood = Neighbourhood::select(DB::raw($neighborhood))
                    ->leftjoin('neighbourhood_images', function($join) {
                        $join->on('neighbourhood_images.neighbourhood_id', '=', 'neighbourhoods.id');
                        $join->where('neighbourhood_images.main_image', '=', '1');
                        $join->where('neighbourhood_images.status', '=', '1');

                        })
                ->where('neighbourhoods.status', '=', '1')
                ->whereRaw("neighbourhoods.id<>$p_id");
                    if($neighborId!=NULL){
                         $nerabyNeigborhood=   $nerabyNeigborhood->whereRaw("neighbourhoods.municipality_id = $neighborId");
                    }
                $nerabyNeigborhood=  $nerabyNeigborhood->limit(3);
                $nerabyNeigborhood=$nerabyNeigborhood->get();
         
            
            $gallery= DB::table('neighbourhood_images')
                ->select('neighbourhood_images.*')
                ->whereRaw("neighbourhood_id=$p_id")
                ->where('neighbourhood_images.status', '=', '1')
                //->whereRaw('neighbourhood_images.main_image IS NULL')
                ->limit(4)->get();
            
            
            
            $property = array();
            
            $property[] = "properties.*";
            $property[] = "properties.name_" . $this->lang . " as name ";
            $property[] = "properties.description_" . $this->lang . " as description ";
            $property[] = "properties.address_line1_" . $this->lang . " as address1 ";
            $property[] = "properties.address_line2_" . $this->lang . " as address2 ";
            $property[] = "properties.zip_" . $this->lang . " as zip ";
            $property[] = "neighbourhoods.name_" . $this->lang . " as neighborname ";
            $property[] = "neighbourhoods.description_" . $this->lang . " as neighbordesc ";
            
            $property = implode(',', $property);
            $similar = Property::select(DB::raw($property), 'municipalities.name  as municipality','property_images.image as mainimage')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                    ->leftjoin('neighbourhoods', 'neighbourhoods.id', '=', 'properties.neighborhood')
                    ->leftjoin('property_images', function($join) {
                         $join->on('property_images.property_id', '=', 'properties.id');
                         $join->where('property_images.main_image', '=', '1');
                         $join->where('property_images.status', '=', '1');
                     })
                     ->whereRaw("properties.status='1'");
                    if($neighborId!=NULL){
                         $similar=   $similar->whereRaw("properties.neighborhood = $p_id");
                    }
                    
                   $similar=  $similar->limit(3);
                   $similar=$similar->get();
                   
             $sliderGallery= DB::table('neighbourhood_images')
                ->select('neighbourhood_images.*')
                ->whereRaw("neighbourhood_id=$p_id")
                ->whereRaw("status ='1'")
                ->get();
             }
            $dataArray=array(
              'neighborhood_detail'=>$neighborhood_detail,
              'gallery'=>$gallery,
              'nerabyNeigborhood'=>$nerabyNeigborhood,
              'similar'=>$similar,
              'sliderGallery'=>$sliderGallery
            );
            return view('web.neighborhood.neighborhood-detail',$dataArray);
     
        } catch (Exception $ex) {
            //echo $ex->getLine().$ex->getMessage();
           $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }
        
        
      }

}
