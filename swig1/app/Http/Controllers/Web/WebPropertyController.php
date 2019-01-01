<?php

namespace App\Http\Controllers\Web;

use App;
use App\Http\Controllers\Controller;
use App\Model\Property;
use App\Services\Common;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class WebPropertyController extends Controller
{

    public $lang;

    public function __construct()
    {

        //this value should dynamic

        //  App::setLocale($this->lang);
        //set some global objects

    }

    /**
     * ajax post response
     * Show the website landing page .
     *
     * @param  for_type=(1,2),
     * @param appartment=(1,2,3,4)
     * @param price=(0-10000)
     * @param places_list=(1,2...)
     * @return view with search data
     * Render the view with parameters
     */
    public function searchList(Request $request)
    {
        try {
            $this->lang = App::getLocale();

            $paginate = 6; // Config::get('app.PAGINATE');
            $for_tenure = $request->input('for_tenure');
            $type = $request->input('building_type');
            $price = $request->input('price');
            $places_list = $request->input('places_list');
            $filer_array = array();
            $page_params = array();
            $filer_array['pricerange'] = "";
            if ($for_tenure != null) {
                $filer_array['tenure_type'] = $for_tenure;
            }
            if ($type != null) {
                $filer_array['building_type'] = $type;
            }
            if ($price != null && $price != "") {
                $priceArray = str_replace('-', ';', $price);
                $filer_array['pricerange'] = $priceArray;

            }
            if ($places_list != null) {
                $filer_array['muncipality_id'] = $places_list;
            }
            $page_params['filer_array'] = $filer_array;
            return view('web.property.property_list', $page_params);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

    }

    /**
     * ajax post response
     *
     *
     * @param
     * @param
     * @param  price=(0-10000)
     * @param places_list=(1,2...)
     * @return view with searhced html and pagination
     */
    public function ajax_searchList(Request $request)
    {
        try {
            $this->lang = App::getLocale();

            /* initialise
             *
             */
            $response = array();
            $response['error'] = 1;
            $response['html'] = "";
            $response['paginateHtml'] = "";
            $response['itemCount'] = 0;
            /* Eof initialise
             *
             */
            $paginate = 3; // Config::get('app.PAGINATE');
            $building_type = $request->input('building_type');
            $places = $request->input('places');
            $price_range_to = $request->input('price_range_to');
            $price_range_from = $request->input('price_range_from');
            $bed = $request->input('bed');
            $bath = $request->input('bath');
            $terrace = $request->input('terrace');
            $garden = $request->input('garden');
            $page = $request->input('page');
            $sort_by = $request->input('sort_by');
            $show_number = $request->input('show_number');
            $tenure_type = $request->input('tenure_type');

            $filer_array = array();
            $page_params = array();
            $propery_raws = array();
            //$property_raws[] = "GROUP_CONCAT(property_images.image) as images";
            //modified to get the main image
            $property_raws[] = "property_images.image as images";
            $property_raws[] = "properties.*";
            $property_raws[] = "properties.name_" . $this->lang . " as name ";
            $property_raws[] = "properties.description_" . $this->lang . " as description ";
            $property_raws[] = "properties.address_line1_" . $this->lang . " as address1 ";
            $property_raws[] = "properties.address_line2_" . $this->lang . " as address2 ";
            $property_raws[] = "properties.zip_" . $this->lang . " as zip ";

            $property_raws = implode(',', $property_raws);
            $property_list = Property::select(DB::raw($property_raws), 'municipalities.name as muncipality')
                ->leftjoin('municipalities', 'municipalities.id', '=', 'properties.municipality_id')
                ->leftjoin('property_images', function ($join) {
                    $join->on('property_images.property_id', '=', 'properties.id');
                    $join->where('property_images.main_image', '=', '1');
                    $join->where('property_images.status', '=', '1');
                    //added to get main image only
                    $join->orderby('property_images.main_image', 'DESC');
                });
            $property_list = $property_list->groupby('properties.id');
            $property_list = $property_list->where('properties.status', '=', '1');

            if ($building_type != null) {
                $building_type = implode(',', $building_type);
                $property_list = $property_list->whereRaw('building_type IN(' . $building_type . ')');
                $filer_array['building_type'] = $building_type;
            }

            if ($price_range_to != null) {
                $property_list = $property_list->whereRaw('estimated_price <= ' . $price_range_to);
                $filer_array['price_range_to'] = $price_range_to;
            }
            if ($price_range_from != null) {
                $property_list = $property_list->whereRaw('estimated_price >= ' . $price_range_from);
                $filer_array['price_range_from'] = $price_range_from;
            }
            if ($places != null) {
                $places = implode(',', $places);
                $property_list = $property_list->whereRaw('municipality_id IN(' . $places . ')');
                $filer_array['muncipality_id'] = $places;
            }
            if ($bed != null && $bed != 0) {
                $property_list = $property_list->whereRaw('no_of_beds >=' . $bed);
                $filer_array['no_of_beds'] = $bed;
            }
            if ($bath != null && $bath != 0) {
                $property_list = $property_list->whereRaw('no_of_baths >=' . $bath);
                $filer_array['no_of_baths'] = $bath;
            }

            if ($terrace == 1) {
                $property_list = $property_list->whereRaw('terrace = "1"');
                $filer_array['terrace'] = $terrace;
            }
            if ($tenure_type != "") {
                $property_list = $property_list->whereRaw("tenure_type = '$tenure_type'");
                $filer_array['tenure_type'] = $tenure_type;
            }
            if ($show_number > 0) {
                $paginate = $show_number;
                $filer_array['show_number'] = $paginate;
            }
            if ($garden == 1) {
                $filer_array['garden'] = 1;
                $property_list = $property_list->whereRaw('garden ="1"');
            }
            if ($sort_by != 0 || $sort_by != null) {
//                /$sort_by  1, price asc ,2 price desc
                if ($sort_by == 1) {
                    $property_list = $property_list->orderby('estimated_price', 'ASC');
                }
                if ($sort_by == 2) {
                    $property_list = $property_list->orderby('estimated_price', ' DESC');
                }
            } else {
                $property_list = $property_list->orderby('updated_at', 'ASC');
            }
            $property_list = $property_list->paginate($paginate);

            $render_params['filer_array'] = $filer_array;
            $render_params['property_list'] = $property_list;
            $render_params['paginate_list'] = $property_list;
            $response['error'] = 0;
            $response['html'] = View('web.property.property_grid', $render_params)->render();
            $response['paginateHtml'] = View('web.includes.paginate_content', $render_params)->render();
            $response['itemCount'] = $property_list->count();
            $response['message'] = 'List success for search';
        } catch (Exception $ex) {
            echo $response['message'] = $ex->getMessage();
            echo $response['message'] = $ex->getLine();
        }
        return response()->json($response);
        //Array ( [tenure_type] => 1 [building_type] => 1 [muncipality_id] => Array ( [0] => 2 [1] => 4 ) )
    }

    /**
     * Shows details of a property
     * @param
     * @param
     * @param  slug=(0-10000)
     * @param p_id=(1,2...)
     * @return view with searched html and pagination
     */
    public function property_view($slug, $p_id)
    {
        try {
            /* initialise
             *
             */
            //$slug is the name of property just showing in url
            $common = new Common();
            $render_params = array();
            $render_params['details'] = "";
            $render_params['gallery'] = "";
            $render_params['sliderGallery'] = "";
            $render_params['similar'] = "";
            $render_params['message'] = "No details";
            if ($p_id != "") {
                //$p_id = \Crypt::decrypt($p_id);
                $property_reference = DB::table('properties')
                    ->select('properties.id')
                    ->whereRaw("properties.reference_number='$p_id'")
                    ->first();
                $p_id = $property_reference->id;
                $pageData = $common->getProductDetails($p_id);
                $render_params['details'] = $pageData['property'];
                $render_params['gallery'] = $pageData['gallery'];
                $render_params['sliderGallery'] = $pageData['sliderGallery'];
                $render_params['similar'] = $pageData['similar'];
                $render_params['message'] = "Showing Details";
            }
        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }
        return view('web.property.property_details', array('render_params' => $render_params));
    }

}
