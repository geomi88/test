<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Model\Architects;
use App\Model\ArchitectProject;
use App\Model\Property;
use App\Model\Projects;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Support\Facades\Config;
use App;
use DB;
use App\Services\Common;

class WebArchitectController extends Controller {

    Public $lang;

    public function __construct() {
        
   
      
    }

    public function architects(Request $request) {
        try {
            $this->lang = App::getLocale();
              
            $architect = array();
            
            $architect[] = "architect_images.image as images";
            $architect[] = "architects.*";
            $architect[] = "architects.name_" . $this->lang . " as name ";
            $architect[] = "architects.address_" . $this->lang . " as address ";
            $architect[] = "architects.description_" . $this->lang . " as description ";
            $architect[] = "architects.additional_description_" . $this->lang . " as additional_decription ";
            
            //$architect[] = "architects.zip_" . $this->lang . " as zip ";
            

            $architect = implode(',', $architect);
            $architect_list = Architects::select(DB::raw($architect))
                ->leftjoin('architect_images', function($join) {
                $join->on('architect_images.architect_id', '=', 'architects.id');
                $join->where('architect_images.main_image', '=', '1');
                $join->where('architect_images.status', '=', '1');
               
            });
            $architect_list = $architect_list->groupby('architects.id');
            $architect_list = $architect_list->where('architects.status', '=', '1');
            $architect_list = $architect_list->get();
         
             
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
                'architect_list'=>$architect_list,
                'similar'=>$similar
            );
             return view('web.architect.architectlist',$dataArray);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
    }
    
    public function view_architects($p_id) {
        try {
           
            $this->lang =Session::get('lang');
            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);
                
            
            $architect = array();
            
            $architect[] = "architect_images.image as images";
            $architect[] = "architects.*";
            $architect[] = "architects.name_" . $this->lang . " as name ";
            $architect[] = "architects.address_" . $this->lang . " as address ";
            $architect[] = "architects.description_" . $this->lang . " as description ";
            $architect[] = "architects.additional_description_" . $this->lang . " as additional_description ";
            
            //$architect[] = "architects.zip_" . $this->lang . " as zip ";
            

            $architect = implode(',', $architect);
            $architect_detail= Architects::select(DB::raw($architect))
                    ->leftjoin('architect_images', function($join) {
                $join->on('architect_images.architect_id', '=', 'architects.id');
                $join->where('architect_images.main_image', '=', '1');
                $join->where('architect_images.status', '=', '1');
               
            })
            ->where('architects.status', '=', '1')
            ->where('architects.id', '=', $p_id)
            ->first();
            
            $projects=$architect_detail->project_id;
            if($projects==""){
              $architectProjects=array();
              
            }else{
              
                  $project = array();
            
            $project[] = "project_gallery.image as images";
            $project[] = "projects.*";
            $project[] = "projects.name_" . $this->lang . " as name ";
            $project[] = "projects.address_line1_" . $this->lang . " as address1 ";
            $project[] = "projects.address_line2_" . $this->lang . " as address2 ";
            $project[] = "projects.zip_" . $this->lang . " as zip ";
            $project[] = "projects.description_" . $this->lang . " as description ";
            
            $project = implode(',', $project);
                
              $architectProjects=Projects::select(DB::raw($project))
                           // ->select('projects.*', 'municipalities.name as muncname')
                            ->leftjoin('municipalities', 'municipalities.id', '=', 'projects.municipality_id')
                            ->leftjoin('project_gallery', function($join) {
                                    $join->on('project_gallery.project_id', '=', 'projects.id');
                                    $join->where('project_gallery.main_image', '=', '1');
                                    $join->where('project_gallery.status', '=', '1');

                        })
                      ->where('projects.status', '=', '1')
                            ->whereRaw("projects.id IN ($projects)")
                            ->limit(3)->get();
             }
            
            $gallery= DB::table('architect_images')
                ->select('architect_images.*')
                ->whereRaw("architect_id=$p_id")
                ->where('architect_images.status', '=', '1')
                ->get();
       
           }
           $dataArray=array(
               'architect_detail'=>$architect_detail,
               'gallery'=>$gallery,
               'architectProjects'=>$architectProjects
           );
            return view('web.architect.architect-detail',$dataArray);
   
        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }
        
        
      }
   

}
