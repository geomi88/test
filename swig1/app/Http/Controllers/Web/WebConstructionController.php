<?php

namespace App\Http\Controllers\Web;

use App;
use App\Http\Controllers\Controller;
use App\Model\InterestForm;
use App\Model\Projects;
use App\Model\Property;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use URL;
class WebConstructionController extends Controller
{

    public $lang;

    public function __construct()
    {

    }

    public function newconstruction(Request $request)
    {
        try {
            $this->lang = App::getLocale();

            $construction = array();
            $construction[] = "project_gallery.image as images";
            $construction[] = "projects.name_" . $this->lang . " as name ";
            $construction[] = "projects.url";
            $construction[] = "projects.id";

            $construction = implode(',', $construction);
            $construction_list = Projects::select(DB::raw($construction))
                ->leftjoin('project_gallery', function ($join) {
                    $join->on('project_gallery.project_id', '=', 'projects.id');
                    $join->where('project_gallery.main_image', '=', '1');
                    $join->where('project_gallery.status', '=', '1');

                })
                ->where('projects.status', '=', '1');
            $construction_list = $construction_list->groupby('projects.id');
            $construction_list = $construction_list->get();

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

            $similar = Property::select(DB::raw($property), 'municipalities.name  as municipality', 'property_images.image as mainimage')
                ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                ->leftjoin('neighbourhoods', 'neighbourhoods.id', '=', 'properties.neighborhood')
                ->leftjoin('property_images', function ($join) {
                    $join->on('property_images.property_id', '=', 'properties.id');
                    $join->where('property_images.main_image', '=', '1');
                    $join->where('property_images.status', '=', '1');
                })
            //->whereRaw("properties.id<>$p_id")
                ->where('properties.status', '=', '1')
                ->where('properties.new_property', '=', '1');
            $similar = $similar->limit(3);
            $similar = $similar->get();

            $dataArray = array(
                'construction_list' => $construction_list,
                'similar' => $similar,
            );

            return view('web.construction.constructionlist', $dataArray);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

    }

    public function viewConstruction($pid)
    {
        $this->lang = App::getLocale();
        if ($pid != "") {

            $construction = array();
            $construction[] = "project_gallery.image as images";
            $construction[] = "projects.name_" . $this->lang . " as name ";
            $construction[] = "projects.description_" . $this->lang . " as description ";
            $construction[] = "projects.id";
            $construction[] = "projects.url";

            $construction = implode(',', $construction);
            $construction_list = Projects::select(DB::raw($construction))
                ->leftjoin('project_gallery', function ($join) {
                    $join->on('project_gallery.project_id', '=', 'projects.id');
                    $join->where('project_gallery.main_image', '=', '1');
                    $join->where('project_gallery.status', '=', '1');

                })
                ->where('projects.status', '=', '1')
                ->where('projects.url', '=', "$pid")
                ->first();

            $projectId = $construction_list->id;
            if ($construction_list->id == "") {
                $projectId = 0;
            }

            $sliderImages = DB::table('project_slider')
                ->select('project_slider.*')
                ->where('project_slider.status', '=', '1')
                ->where('project_slider.project_id', '=', $projectId)
                ->get();

            $galleryImages = DB::table('project_gallery')
                ->select('project_gallery.*')
                ->where('project_gallery.status', '=', '1')
                ->where('project_gallery.project_id', '=', $projectId)
                ->get();

            $dataArray = array(
                'constructionDetails' => $construction_list,
                'sliderImages' => $sliderImages,
                'galleryImages' => $galleryImages,
                //'gallery'=>$gallery
            );

        }
        return view('web.projects.project_details', $dataArray);

    }

    public function constructionPlan($pid)
    {
        $this->lang = App::getLocale();
        if ($pid != "") {

            $construction = array();
            $construction[] = "project_gallery.image as images";
            $construction[] = "projects.name_" . $this->lang . " as name ";
            $construction[] = "projects.description_" . $this->lang . " as description ";
            $construction[] = "projects.id";
            $construction[] = "projects.floorplan";
            $construction[] = "projects.url";

            $construction = implode(',', $construction);
            $construction_list = Projects::select(DB::raw($construction))
                ->leftjoin('project_gallery', function ($join) {
                    $join->on('project_gallery.project_id', '=', 'projects.id');
                    $join->where('project_gallery.main_image', '=', '1');
                    $join->where('project_gallery.status', '=', '1');

                })
                ->where('projects.status', '=', '1')
                ->where('projects.url', '=', "$pid")
                ->first();

            $projectId = $construction_list->id;
            if ($construction_list->id == "") {
                $projectId = 0;
            }

            $sliderImages = DB::table('project_slider')
                ->select('project_slider.*')
                ->where('project_slider.status', '=', '1')
                ->where('project_slider.project_id', '=', $projectId)
                ->get();

            $galleryImages = DB::table('project_gallery')
                ->select('project_gallery.*')
                ->where('project_gallery.status', '=', '1')
                ->where('project_gallery.project_id', '=', $projectId)
                ->get();

            $floors = array();
            $floors[] = "project_floors.name_" . $this->lang . " as name ";
            $floors = implode(',', $floors);
            $floors = DB::table('project_floors')
                ->select('project_floors.*', DB::raw($floors))
                ->where('project_floors.status', '=', '1')
                ->where('project_floors.project_id', '=', $projectId)
                ->where('project_floors.floor_num', '=', '1')
                ->get();
            $floor_nums = DB::table('project_floors')
                ->select('project_floors.floor_num')
                ->where('project_floors.status', '=', '1')
                ->where('project_floors.project_id', '=', $projectId)
                ->groupby('project_floors.floor_num')
                ->get();
            $dataArray = array(
                'constructionDetails' => $construction_list,
                'sliderImages' => $sliderImages,
                'galleryImages' => $galleryImages,
                'floors' => $floors,
                'floor_nums' => $floor_nums,
                //'gallery'=>$gallery
            );

        }
        return view('web.projects.project_plan', $dataArray);

    }

    public function constructionSurrounding($pid)
    {
        $this->lang = App::getLocale();
        if ($pid != "") {

            $construction = array();
            $construction[] = "project_gallery.image as images";
            $construction[] = "projects.name_" . $this->lang . " as name ";
            $construction[] = "projects.surrounding_description_" . $this->lang . " as description ";
            $construction[] = "projects.id";
            $construction[] = "projects.latitude";
            $construction[] = "projects.longitude";
            $construction[] = "projects.url";

            $construction = implode(',', $construction);
            $construction_list = Projects::select(DB::raw($construction))
                ->leftjoin('project_gallery', function ($join) {
                    $join->on('project_gallery.project_id', '=', 'projects.id');
                    $join->where('project_gallery.main_image', '=', '1');
                    $join->where('project_gallery.status', '=', '1');

                })
                ->where('projects.status', '=', '1')
                ->where('projects.url', '=', "$pid")
                ->first();

            $projectId = $construction_list->id;
            if ($construction_list->id == "") {
                $projectId = 0;
            }

            $sliderImages = DB::table('project_slider')
                ->select('project_slider.*')
                ->where('project_slider.status', '=', '1')
                ->where('project_slider.project_id', '=', $projectId)
                ->get();

            $surroundingImages = DB::table('project_surrounding')
                ->select('project_surrounding.*')
                ->where('project_surrounding.status', '=', '1')
                ->where('project_surrounding.project_id', '=', $projectId)
                ->get();

            $dataArray = array(
                'constructionDetails' => $construction_list,
                'sliderImages' => $sliderImages,
                'galleryImages' => $surroundingImages,

            );

        }
        return view('web.projects.project_surrounding', $dataArray);

    }

    public function constructionGarden($pid)
    {
        $this->lang = App::getLocale();
        if ($pid != "") {

            $construction = array();
            $construction[] = "project_gallery.image as images";
            $construction[] = "projects.name_" . $this->lang . " as name ";
            $construction[] = "projects.garden_description_" . $this->lang . " as description ";
            $construction[] = "projects.id";
            $construction[] = "projects.latitude";
            $construction[] = "projects.longitude";
            $construction[] = "projects.url";

            $construction = implode(',', $construction);
            $construction_list = Projects::select(DB::raw($construction))
                ->leftjoin('project_gallery', function ($join) {
                    $join->on('project_gallery.project_id', '=', 'projects.id');
                    $join->where('project_gallery.main_image', '=', '1');
                    $join->where('project_gallery.status', '=', '1');

                })
                ->where('projects.status', '=', '1')
                ->where('projects.url', '=', "$pid")
                ->first();

            $projectId = $construction_list->id;
            if ($construction_list->id == "") {
                $projectId = 0;
            }

            $sliderImages = DB::table('project_slider')
                ->select('project_slider.*')
                ->where('project_slider.status', '=', '1')
                ->where('project_slider.project_id', '=', $projectId)
                ->get();

            $gardenImages = DB::table('project_garden')
                ->select('project_garden.*')
                ->where('project_garden.status', '=', '1')
                ->where('project_garden.project_id', '=', $projectId)
                ->get();

            $dataArray = array(
                'constructionDetails' => $construction_list,
                'sliderImages' => $sliderImages,
                'galleryImages' => $gardenImages,
            );

        }
        return view('web.projects.project_garden', $dataArray);

    }
    public function constructionComfort($pid)
    {
        $this->lang = App::getLocale();
        if ($pid != "") {

            $construction = array();
            $construction[] = "project_gallery.image as images";
            $construction[] = "projects.name_" . $this->lang . " as name ";
            $construction[] = "projects.comfort_description_" . $this->lang . " as description ";
            $construction[] = "projects.id";
            $construction[] = "projects.latitude";
            $construction[] = "projects.longitude";
            $construction[] = "projects.url";

            $construction = implode(',', $construction);
            $construction_list = Projects::select(DB::raw($construction))
                ->leftjoin('project_gallery', function ($join) {
                    $join->on('project_gallery.project_id', '=', 'projects.id');
                    $join->where('project_gallery.main_image', '=', '1');
                    $join->where('project_gallery.status', '=', '1');

                })
                ->where('projects.status', '=', '1')
                ->where('projects.url', '=', "$pid")
                ->first();

            $projectId = $construction_list->id;
            if ($construction_list->id == "") {
                $projectId = 0;
            }

            $sliderImages = DB::table('project_slider')
                ->select('project_slider.*')
                ->where('project_slider.status', '=', '1')
                ->where('project_slider.project_id', '=', $projectId)
                ->get();

            $galleryImages = DB::table('project_gallery')
                ->select('project_gallery.*')
                ->where('project_gallery.status', '=', '1')
                ->where('project_gallery.project_id', '=', $projectId)
                ->get();

            $dataArray = array(
                'constructionDetails' => $construction_list,
                'sliderImages' => $sliderImages,
                'galleryImages' => $galleryImages,
            );

        }
        return view('web.projects.project_perfectcomfort', $dataArray);

    }

    public function constructionContact($pid)
    {
        $this->lang = App::getLocale();
        if ($pid != "") {

            $construction = array();
            $construction[] = "project_gallery.image as images";
            $construction[] = "projects.name_" . $this->lang . " as name ";
            $construction[] = "projects.comfort_description_" . $this->lang . " as description ";
            $construction[] = "projects.id";
            $construction[] = "projects.latitude";
            $construction[] = "projects.longitude";
            $construction[] = "projects.url";

            $construction = implode(',', $construction);
            $construction_list = Projects::select(DB::raw($construction))
                ->leftjoin('project_gallery', function ($join) {
                    $join->on('project_gallery.project_id', '=', 'projects.id');
                    $join->where('project_gallery.main_image', '=', '1');
                    $join->where('project_gallery.status', '=', '1');

                })
                ->where('projects.status', '=', '1')
                ->where('projects.url', '=', "$pid")
                ->first();

            $projectId = $construction_list->id;
            if ($construction_list->id == "") {
                $projectId = 0;
            }

            $sliderImages = DB::table('project_slider')
                ->select('project_slider.*')
                ->where('project_slider.status', '=', '1')
                ->where('project_slider.project_id', '=', $projectId)
                ->get();

            $galleryImages = DB::table('project_gallery')
                ->select('project_gallery.*')
                ->where('project_gallery.status', '=', '1')
                ->where('project_gallery.project_id', '=', $projectId)
                ->get();

            $dataArray = array(
                'constructionDetails' => $construction_list,
                'sliderImages' => $sliderImages,
                'galleryImages' => $galleryImages,
            );

        }
        return view('web.projects.project_contactus', $dataArray);

    }
    public function savecontactform()
    {
        try {
            $contact_model = new InterestForm();
            $contact_model->first_name = Input::get('first_name');
            $contact_model->last_name = Input::get('last_name');
            $contact_model->email = Input::get('email');
            $contact_model->phone = Input::get('phone');
            $contact_model->message = Input::get('message');
            $contact_model->stay_informed = Input::get('stay_informed');
            $contact_model->save();
            return 1;
        } catch (Exception $ex) {
            return 0;
        }
    }
    public function floordetails()
    {
        try {
            $project_id = Input::get('project_id');
            $floor_num = Input::get('floor_num');
            $base_url = URL::to('/');
            $floors = DB::table('project_floors')
                ->select('project_floors.*')
                ->where('project_floors.status', '=', '1')
                ->where('project_floors.project_id', '=', $project_id)
                ->where('project_floors.floor_num', '=', $floor_num)
                ->get();
                $return_data = '';
            foreach($floors as $floor){
                $return_data = $return_data.'<div class="small-12 medium-6 large-6 column mgContent">
                                  <div class="pdfContent">
                                    <div class="pdfContentText">
                                      <p>'.env('CURRENCY').' '.$floor->price.' App.: '.$floor->approximate_area.' m² - Terras: '.$floor->terrace.' m² 
                                      Bedrooms: '.$floor->no_of_beds.' - Bathrooms: '.$floor->no_of_baths.' </p>                                            
                                      <span>'.env('CURRENCY').' '.$floor->price.' </span>
                                    </div>
                                      <a href="'.$base_url.'/uploads/floor-plan/"'.$floor->floor_plan.' download >
                                    <figure>
                                        <img src="'.$base_url.'/web/images/pdfFile.png" alt="download">
                                    </figure>
                                      </a>
                                  </div>
                                </div>';
            }
            return $return_data;
        } catch (Exception $ex) {
            return '';
        }
    }

}
