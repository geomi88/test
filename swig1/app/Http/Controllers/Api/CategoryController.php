<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Encryption\EncryptionServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use App\Models\Category_wishlist;
use DB;
use Exception;
use Mail;

class CategoryController extends Controller {

    public function index() {

        try {
            $userId = Input::get('userId');
            if($userId > 0)
            {
                $categories = DB::table('categories')
                            ->select('categories.*', db::raw("(select count(*) from category_wishlist where categoryId=categories.id AND userId = $userId) as favouriteStatus"))
                            ->where(['categories.status' => 1])
                            ->orderby('name', 'asc')->get();
            }
            else
            {
                $categories = DB::table('categories')
                            ->select('categories.*')
                            ->where(['categories.status' => 1])
                            ->orderby('name', 'asc')->get();
            }

            $ads = DB::table('ads')
                    ->select('ads.id as adId', 'ads.image as adImage', 'ads.url as adUrl', 'ad_locations.adLocation as adLocation')
                    ->leftjoin('ad_locations', 'ad_locations.id', '=', 'ads.adLocationId')
                    ->get();
            $full_result['categoryList'] = $categories;
            $full_result['ads'] = $ads;

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('categories' => $full_result)
            );
            return \Response::json($arrReturn);
        } catch (\Exception $e) {
            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => 'Server Error')
            );

            return \Response::json($arrReturn);
        }
    }

    public function addtowishlist() {

        try {
            $userId = Input::get('userId');
            $categoryId = Input::get('categoryId');

            $wishlistStatus = 0;
            $category_wishlist = DB::table('category_wishlist')
                            ->select('category_wishlist.*')
                            ->where(['category_wishlist.categoryId' => $categoryId, 'category_wishlist.userId' => $userId])->first();
            if (count($category_wishlist) > 0) {
                DB::table('category_wishlist')->where('id', '=', $category_wishlist->id)->delete();
            } else {
                $category_wishlistmodel = new Category_wishlist();
                $category_wishlistmodel->categoryId = $categoryId;
                $category_wishlistmodel->userId = $userId;

                $category_wishlistmodel->save();
                $wishlistStatus = 1;
            }



            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('wishlistStatus' => $wishlistStatus)
            );
            return \Response::json($arrReturn);
        } catch (\Exception $e) {
            $arrReturn['Result'] = array(
                'status' => FALSE,
                'error' => array('message' => 'Server Error')
            );

            return \Response::json($arrReturn);
        }
    }

}
