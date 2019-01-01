<?php

namespace App\Http\Controllers\Cms\Ads;

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
use DB;
use App\Models\User;

class AdsController extends Controller {

    public function index() {
        try {
            $paginate = 10;
            $ads = DB::table('ads')
                        ->select('ads.*','ad_locations.adLocation as adLocation')
                        ->leftjoin('ad_locations', 'ad_locations.id', '=', 'ads.adLocationId')
                        ->orderby('created_at', 'DESC')->paginate($paginate);
            return view('ads/index', array('ads' => $ads));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }

   
    public function show($id)
    {        
        $ad = DB::table('ads')
                ->select('ads.*','ad_locations.adLocation as adLocation')
                ->leftjoin('ad_locations', 'ad_locations.id', '=', 'ads.adLocationId')
                ->where(['ads.id' => $id])
                ->first();
   
        return view('ads/view', array('ad_details' => $ad));
    }
    
    public function edit($id)
    {
        $ad = DB::table('ads')
                ->select('ads.*','ad_locations.adLocation as adLocation')
                ->leftjoin('ad_locations', 'ad_locations.id', '=', 'ads.adLocationId')
                ->where(['ads.id' => $id])
                ->first();
   
        return view('ads/edit', array('ad_details' => $ad));
    }
    
    public function update()
    {
        $adId = Input::get('ad_id');
        $adUrl = Input::get('adUrl');
        $adImage = Input::file('adImage');     
        
        if(isset($adImage)) {
        /*$targ_w = Input::get('w');
        $targ_h = Input::get('h');
        $jpeg_quality = 90;

        $filename = time() . $adImage->getClientOriginalName();
        $ext = $adImage->getClientOriginalExtension();
        $path = public_path() . '/uploads/ads/';
        $adImage->move($path, $filename);
        $src = $path.$filename;
        if($ext == 'jpeg'|| $ext == 'jpg')
            $img_r = imagecreatefromjpeg($src);
        else if($ext == 'png')
            $img_r = imagecreatefrompng($src);
        
        $dst_r = ImageCreateTrueColor( $targ_w, $targ_h );

        imagecopyresampled($dst_r,$img_r,0,0,Input::get('x'),Input::get('y'),Input::get('w'),Input::get('h'),Input::get('w'),Input::get('h'));
        imagejpeg($dst_r, $src, $jpeg_quality);*/
          //      ,0,0,$_POST['x'],$_POST['y'],
            //$targ_w,$targ_h,$_POST['w'],$_POST['h']);
           // imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
        $filename = time() . $adImage->getClientOriginalName();
        $path = public_path() . '/uploads/ads/';
        $adImage->move($path, $filename);
        $update_ad = DB::table('ads')
                ->where(['id' => $adId])
                ->update(['url' => $adUrl,'image' => '/uploads/ads/'.$filename]);
        }
        else {
            $update_ad = DB::table('ads')
                ->where(['id' => $adId])
                ->update(['url' => $adUrl]);
        }
        Toastr::success('Successfully Edited the Ad', $title = null, $options = []);
        return Redirect::to("ads/edit/$adId");
    }

}
