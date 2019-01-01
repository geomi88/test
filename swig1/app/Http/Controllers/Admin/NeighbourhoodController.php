<?php

namespace App\Http\Controllers\Admin;

use Analytics;
use App\Http\Controllers\Controller;
use App\Model\Neighbourhood;
use App\Model\NeighbourhoodImages;
use App\Services\Common;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Kamaln7\Toastr\Facades\Toastr;
use Spatie\Analytics\Period;

class NeighbourhoodController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin_auth');
        //write athenitcate function for admin
    }

    /**
     * Show the dashboard
     *
     * @param  null
     * @return View
     */
    public function get_list_neighbourhood(Request $request)
    {
        $paginate = '10';
        $neighbourhoodList = DB::table('neighbourhoods')
            ->select('neighbourhoods.*', 'municipalities.name as muncname', 'districts.name as districtname')
            ->leftjoin('municipalities', 'municipalities.id', '=', 'neighbourhoods.municipality_id')
            ->leftjoin('districts', 'districts.id', '=', 'neighbourhoods.district_id')
            ->where('neighbourhoods.status', '=', '1')
            ->paginate($paginate);

        $common = new Common();
       
        $districts = $common->districts();
        $municipalities = $common->municipalities();

        $pageData['neighbourhoodList'] = $neighbourhoodList;
        $pageData['districts'] = $districts;
        $pageData['city'] = $municipalities;

        if ($request->ajax()) {

            
            $district = Input::get('district');
            //$city = Input::get('city');
            $searchKey = Input::get('searchKey');

           $neighbourhoodList = DB::table('neighbourhoods')
                ->select('neighbourhoods.*', 'municipalities.name as muncname', 'districts.name as districtname')
                ->leftjoin('municipalities', 'municipalities.id', '=', 'neighbourhoods.municipality_id')
                ->leftjoin('districts', 'districts.id', '=', 'neighbourhoods.district_id')
                ->where('neighbourhoods.status', '=', '1')
                ->when($district, function ($query) use ($district) {
                    return $query->whereRaw("(neighbourhoods.district_id=$district)");
                })
//                ->when($city, function ($query) use ($city) {
//                    return $query->whereRaw("(neighbourhoods.municipality_id=$city)");
//                })
                ->when($searchKey, function ($query) use ($searchKey) {
                    return $query->whereRaw("(neighbourhoods.name_en like '%$searchKey%' OR municipalities.name like '%$searchKey%' OR districts.name like '%$searchKey%')");
                })
                ->paginate($paginate);

            $pageData['neighbourhoodList'] = $neighbourhoodList;

            return view('admin.neighbourhood.neighbor-list-result', array('pageData' => $pageData));
        }

        return view('admin.neighbourhood.neighbor-listing', array('pageData' => $pageData));
    }

    public function add_neighbourhood()
    {
        try {

           
            $neighborhoods = DB::table('neighbourhoods')
                ->select('id', 'name_en as name')
                ->where('status', '=', '1')
                ->get();

            $municipalities = DB::table('municipalities')
                ->select('*')
                ->where('status', '=', '1')
                ->get();

            $districts = DB::table('districts')
                ->select('*')
                ->where('status', '=', '1')
                ->get();
           
            
            $pageData['neighborhoods'] = $neighborhoods;
            $pageData['municipalities'] = $municipalities;
          
            $pageData['district'] = $districts;

            return view('admin.neighbourhood.add-neighbor', array('pageData' => $pageData));

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function save_neighbourhood(Request $request)
    {
        try {
            $input = Input::all();
            // print_r($input);
            // die();
            $objmodel = new Neighbourhood();
            
            $objmodel->name_en = Input::get('name_en');
            $objmodel->name_ka = Input::get('name_ka');
            $objmodel->name_ru = Input::get('name_ru');
            
            $objmodel->address_en = Input::get('address_en');
            $objmodel->address_ka = Input::get('address_ka');
            $objmodel->address_ru = Input::get('address_ru');
            
            $objmodel->description_en = Input::get('description_en');
            $objmodel->description_ka = Input::get('description_ka');
            $objmodel->description_ru = Input::get('description_ru');

            $objmodel->municipality_id = Input::get('city_en');
            $objmodel->district_id = Input::get('district_en');
            
            $objmodel->status = '1';

                $muncId = Input::get('city_en');
                $districtId = Input::get('district_en');
                $municipalityId = DB::table('municipalities')
                    ->select('municipalities.name')
                    ->whereRaw("municipalities.id=$muncId")
                    ->first();
                $districtId = DB::table('districts')
                    ->select('districts.name')
                    ->whereRaw("districts.id=$districtId")
                    ->first();
                $city = "";
                $district = "";
                if ($municipalityId != "") {
                    $city = $municipalityId->name;
                }
                if ($districtId != "") {
                    $district = $districtId->name;
                }

                $adrLine1 = Input::get('address_en');
                
               
                $address = $adrLine1. ',' . $city . ',' . $district;

                $prepAddr = str_replace(' ', '+', $address);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE&address=' . $prepAddr . '&sensor=false');
                $output = json_decode($geocode);

                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;

          

            $objmodel->latitude = $latitude;
            $objmodel->longitude = $longitude;

           
            $objmodel->save();
            $property_id = $objmodel->id;

             $galleryCount = Input::get('galleryCount');
            for ($i = 0; $i <= $galleryCount; $i++) {

                if ($request->file('gallery_upload_' . $i)) {

                    $image = $request->file('gallery_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "neighborhood_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/neighborhood';
                    $image->move($path, $filename);

                    $property = new NeighbourhoodImages();
                    $property->neighbourhood_id = $property_id;
                    $property->image = $filename;
                    $property->save();

                }
            }

            if ($request->hasFile('preview_upload')) {
                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $gallery = "neighborhood_" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/neighborhood';
                $file->move($path, $gallery);

                $mainImg = new NeighbourhoodImages();
                $mainImg->neighbourhood_id = $property_id;
                $mainImg->image = $gallery;
                $mainImg->main_image = '1';
                $mainImg->save();
            }

            Toastr::success('Successfully Added New Property!', $title = null, $options = []);
            return Redirect::to('admin/neighbourhood-listing');

        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/neighbourhood-listing');
        }
    }

    public function view_neighbourhood($p_id)
    {

        try {
            /* initialise
             *
             */
            //$slug is the name of property just showing in url
            $common = new Common();
            
            $render_params = array();
            $render_params['property'] = "";
            $render_params['gallery'] = "";

            if ($p_id != "") {

                $encrypted_id = $p_id;
                $p_id = \Crypt::decrypt($p_id);

                $render_params = array();

                $property_list =  DB::table('neighbourhoods')
                    ->select('neighbourhoods.*', 'municipalities.name as muncname', 'districts.name as districtname')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'neighbourhoods.municipality_id')
                    ->leftjoin('districts', 'districts.id', '=', 'neighbourhoods.district_id')
                    ->leftjoin('neighbourhood_images', function ($join) {
                        $join->on('neighbourhood_images.neighbourhood_id', '=', 'neighbourhoods.id');
                        $join->where('neighbourhood_images.main_image', '=', '1');
                        $join->where('neighbourhood_images.status', '=', '1');
                    })
                    ->whereRaw("neighbourhoods.id=$p_id")
                    ->where('neighbourhoods.status', '=', '1')
                    ->first();

               
                
                $gallery = DB::table('neighbourhood_images')
                    ->select('neighbourhood_images.*')
                    ->whereRaw("neighbourhood_id=$p_id")
                    ->where('neighbourhood_images.status', '=', '1')
                    ->get();

                $render_params['property'] = $property_list;
                $render_params['gallery'] = $gallery;
              

            }
            return view('admin.neighbourhood.neighbor-detail-view', array('render_params' => $render_params));

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    public function edit_neighbourhood($p_id)
    {
        try {
            
            $common = new Common();

            $pageData = array();
            $pageData['municipalities'] = "";
            $pageData['district'] = "";
            $pageData['property'] = "";
            $pageData['gallery'] = "";
            
            $pageData['neighborhoods'] = "";

            if ($p_id != "") {
                $p_id = \Crypt::decrypt($p_id);

                $render_params = array();

                $property_list = DB::table('neighbourhoods')
                    ->select('neighbourhoods.*', 'municipalities.name as muncname', 'districts.name as districtname','neighbourhood_images.main_image as mainimage')
                    ->leftjoin('municipalities', 'municipalities.id', '=', 'neighbourhoods.municipality_id')
                    ->leftjoin('districts', 'districts.id', '=', 'neighbourhoods.district_id')
                    ->leftjoin('neighbourhood_images', function ($join) {
                        $join->on('neighbourhood_images.neighbourhood_id', '=', 'neighbourhoods.id');
                        $join->where('neighbourhood_images.main_image', '=', '1');
                        $join->where('neighbourhood_images.status', '=', '1');
                    })
                    ->whereRaw("neighbourhoods.id=$p_id")
                    ->where('neighbourhoods.status', '=', '1')
                    ->first();

                $gallery = DB::table('neighbourhood_images')
                    ->select('neighbourhood_images.*')
                    ->whereRaw("neighbourhood_id=$p_id")
                    ->whereRaw("main_image IS NULL ")
                    ->whereRaw("status='1' ")
                    ->get();

                $render_params = $common->getBasics();
                $pageData['municipalities'] = $render_params['municipalities'];
                $pageData['district'] = $render_params['district'];
                $pageData['property'] = $property_list;
                $pageData['gallery'] = $gallery;

            }
            return view('admin.neighbourhood.edit-neighbor', array('pageData' => $pageData));

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    public function update_neighbourhood(Request $request)
    {
        try {

            $input = Input::all();

            $propertyid = \Crypt::decrypt($input['propertyId']);

            $objmodel = new Neighbourhood();
           
            $objmodel->exists = true;
            $objmodel->id = $propertyid;
            $objmodel->name_en = Input::get('name_en');
            $objmodel->name_ka = Input::get('name_ka');
            $objmodel->name_ru = Input::get('name_ru');
            
            $objmodel->address_en = Input::get('address_en');
            $objmodel->address_ka = Input::get('address_ka');
            $objmodel->address_ru = Input::get('address_ru');
            
            $objmodel->description_en = Input::get('description_en');
            $objmodel->description_ka = Input::get('description_ka');
            $objmodel->description_ru = Input::get('description_ru');

            $objmodel->municipality_id = Input::get('city_en');
            $objmodel->district_id = Input::get('district_en');
            
            $objmodel->status = '1';

                $muncId = Input::get('city_en');
                $districtId = Input::get('district_en');
                $municipalityId = DB::table('municipalities')
                    ->select('municipalities.name')
                    ->whereRaw("municipalities.id=$muncId")
                    ->first();
                $districtId = DB::table('districts')
                    ->select('districts.name')
                    ->whereRaw("districts.id=$districtId")
                    ->first();
                $city = "";
                $district = "";
                if ($municipalityId != "") {
                    $city = $municipalityId->name;
                }
                if ($districtId != "") {
                    $district = $districtId->name;
                }

                $adrLine1 = Input::get('address_en');
                
               
                $address = $adrLine1. ',' . $city . ',' . $district;

                $prepAddr = str_replace(' ', '+', $address);
                $geocode = file_get_contents('https://maps.google.com/maps/api/geocode/json?key=AIzaSyDJroiuXSJvDPo_3VqAwCDfc5GnThTLYvE&address=' . $prepAddr . '&sensor=false');
                $output = json_decode($geocode);

                $latitude = $output->results[0]->geometry->location->lat;
                $longitude = $output->results[0]->geometry->location->lng;

          

            $objmodel->latitude = $latitude;
            $objmodel->longitude = $longitude;

           
            $objmodel->save();
            $property_id = $objmodel->id;

             $galleryCount = Input::get('galleryCount');
            for ($i = 0; $i <= $galleryCount; $i++) {

                if ($request->file('gallery_upload_' . $i)) {

                    $image = $request->file('gallery_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "neighborhood_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/neighborhood';
                    $image->move($path, $filename);

                    $property = new NeighbourhoodImages();
                    $property->neighbourhood_id = $property_id;
                    $property->image = $filename;
                    $property->save();

                }
            }

            if ($request->hasFile('preview_upload')) {
                 
                    DB::table('neighbourhood_images')
                        ->whereRaw("neighbourhood_id=$property_id")
                        ->whereRaw("main_image='1'")
                        ->update(['status' => '0']);

                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $gallery = "neighborhood_" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/neighborhood';
                $file->move($path, $gallery);

                $mainImg = new NeighbourhoodImages();
                $mainImg->neighbourhood_id = $property_id;
                $mainImg->image = $gallery;
                $mainImg->main_image = '1';
                $mainImg->save();
            }
            
            Toastr::success('Updated Property Details Successfully', $title = null, $options = []);
            return Redirect::to('admin/neighbourhood-listing');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/neighbourhood-listing');

        }

    }

    public function remove_neighbourhood(Request $request, $propertyId)
    {
        try {
            $property_id = \Crypt::decrypt($propertyId);
            if ($property_id != "") {
                $objmodel = new Neighbourhood();
                $objmodel->exists = true;
                $objmodel->id = $property_id;
                $objmodel->status = 0;
                $objmodel->save();
            }

            Toastr::success('Successfully Removed Property Details!', $title = null, $options = []);
            return Redirect::to('admin/neighbourhood-listing');
        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/neighbourhood-listing');
        }
    }
    
    public function remove_neighbourhoodGallery(Request $request)
    {

        $imgId = Input::get('imgId');
        DB::table('neighbourhood_images')
            ->whereRaw("id=$imgId")
            ->update(['status' => '0']);
        return 1;

    }

    public function get_lat_long($address)
    {

        $address = str_replace(" ", "+", $address);

        $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
        $json = json_decode($json);

        $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
        $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
        return $lat . ',' . $long;
    }

}
