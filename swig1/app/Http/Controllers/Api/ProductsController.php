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
use App\Models\Product;
use App\Models\Product_wishlist;
use App\Models\Product_images;
use App\Models\Order;
use App\Models\Product_view;
use App\Models\Conversations;
use App\Models\Product_offer;
use DB;
use Exception;
use Mail;

class ProductsController extends Controller {

    public function dashboard() {

        try {
            //SELECT c.id as cat_id, COUNT(*) AS magnitude FROM categories as c,products as p, product_wishlist as pw WHERE c.id = p.categoryId AND p.id=pw.productId and pw.userId=33 GROUP BY c.id ORDER BY magnitude DESC LIMIT 1
            $userId = Input::get('userId');

            $product_model = new Product();
            if ($userId > 0) {
                $products = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        //->leftjoin('product_images', 'product_images.productId', '=', 'products.id')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->orderby('products.created_at', 'DESC')
                        ->whereRaw("(products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                        ->limit(5)
                        ->get();


                /*                 * *Recommended Products** */
                $most_liked_category = DB::table('categories')
                        ->select('categories.id as categoryId', db::raw('COUNT(*) AS magnitude'))
                        ->leftjoin('products', 'products.categoryId', '=', 'categories.id')
                        ->leftjoin('product_wishlist', 'product_wishlist.productId', '=', 'products.id')
                        ->whereRaw("product_wishlist.userId = $userId and (products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                        ->groupby("categories.id")
                        ->orderby('magnitude', 'DESC')
                        ->limit(1)
                        ->first();
                $recommended_products = array();
                if (count($most_liked_category) > 0) {
                    $most_liked_category_id = $most_liked_category->categoryId;
                    $recommended_products = DB::table('products')
                            ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                            ->leftjoin('users', 'users.id', '=', 'products.userId')
                            ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                            ->orderby('products.created_at', 'DESC')
                            ->whereRaw("products.categoryId = $most_liked_category_id and (products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                            ->limit(5)
                            ->get();

                    $recommended_products = $product_model->productdetails($recommended_products);
                }
                if (count($recommended_products) < 1) {
                    $recommended_products = $products;
                }
                /*                 * *Recommended Products ends** */


                /*                 * * Trending Products** */
                $trending_products = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        //->leftjoin('product_images', 'product_images.productId', '=', 'products.id')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->orderby('products.created_at', 'DESC')
                        ->whereRaw("(products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0 and products.id IN (select productId from product_view group by productId order by count(productId) desc)")
                        ->limit(5)
                        ->get();

                $trending_products = $product_model->productdetails($trending_products);
                if (count($trending_products) < 1) {
                    $trending_products = $products;
                }
                /*                 * * Trending Products ends** */


                /*                 * * Recently Viewed Products** */
                $recent_products = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->leftjoin('product_view', 'product_view.productId', '=', 'products.id')
                        ->groupby('product_view.productId')
                        ->orderby('product_view.updated_at', 'DESC')
                        ->whereRaw("(products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0 and product_view.userId = $userId")
                        ->limit(5)
                        ->get();

                $recent_products = $product_model->productdetails($recent_products);
                if (count($recent_products) < 1) {
                    $recent_products = $products;
                }
                /*                 * * Recently Viewed Products ends** */

                /*                 * * Featured Products** */
                $featured_products = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->leftjoin('product_view', 'product_view.productId', '=', 'products.id')
                        ->groupby('product_view.productId')
                        ->orderby('product_view.updated_at', 'DESC')
                        ->whereRaw("(products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0 and products.featuredStatus = 1")
                        ->limit(5)
                        ->get();

                $featured_products = $product_model->productdetails($featured_products);
                if (count($featured_products) < 1) {
                    $featured_products = $products;
                }
                /*                 * * Featured Products ends** */
            } else {
                $products = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->orderby('products.created_at', 'DESC')
                        ->whereRaw("(products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                        ->limit(5)
                        ->get();
                $trending_products = $products;
                $recent_products = $products;
                $recommended_products = $products;
                $featured_products = $products;
            }
            //$result = array();
            /* foreach ($products as $product) {

              $key = $product->productId;
              if (key_exists($key, $result)) {
              $result[$key]['images'][] = $product->productImage;
              } else {
              $arrimage = array();
              $arrimage[] = $product->productImage;
              $result[$key] = array('productId' => $product->productId,
              'productName' => $product->productName,
              'productDescription' => $product->productDescription,
              'productPrice' => $product->productPrice,
              'sellerId' => $product->sellerId,
              'images' => $arrimage);
              }
              } */
            /* $dashboard['featuredProducts'] = array_values($result);
              $dashboard['trendingProducts'] = array_values($result);
              $dashboard['recentProducts'] = array_values($result);
              $dashboard['recommendedProducts'] = array_values($result); */



            $products = $product_model->productdetails($products);


            $dashboard['featuredProducts'] = $featured_products;
            $dashboard['trendingProducts'] = $trending_products;
            $dashboard['recentProducts'] = $recent_products;
            $dashboard['recommendedProducts'] = $recommended_products;
            $ads = DB::table('ads')
                    ->select('ads.id as adId', 'ads.image as adImage', 'ads.url as adUrl', 'ad_locations.adLocation as adLocation')
                    ->leftjoin('ad_locations', 'ad_locations.id', '=', 'ads.adLocationId')
                    ->get();
            $dashboard['ads'] = $ads;

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('products' => $dashboard)
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

    public function productsbycategory() {

        try {
            $categoryId = Input::get('categoryId');
            $userId = Input::get('userId');
            $product_model = new Product();
            if ($userId > 0) {
                $products = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        //->leftjoin('product_images', 'product_images.productId', '=', 'products.id')
                        ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->whereRaw("products.categoryId = $categoryId and (products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                        ->get();
            } else {
                $products = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        //->leftjoin('product_images', 'product_images.productId', '=', 'products.id')
                        ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->whereRaw("products.categoryId = $categoryId and (products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                        ->get();
            }
            //$result = array();
            /* foreach ($products as $product) {

              $key = $product->productId;
              if (key_exists($key, $result)) {
              $result[$key]['images'][] = $product->productImage;
              } else {
              $arrimage = array();
              $arrimage[] = $product->productImage;
              $result[$key] = array('productId' => $product->productId,
              'productName' => $product->productName,
              'productDescription' => $product->productDescription,
              'productPrice' => $product->productPrice,
              'sellerId' => $product->sellerId,
              'images' => $arrimage);
              }
              } */
            $products = $product_model->productdetails($products);
            //$category_products['products'] = array_values($result);

            $category_products['products'] = $products;
            $ads = DB::table('ads')
                    ->select('ads.id as adId', 'ads.image as adImage', 'ads.url as adUrl', 'ad_locations.adLocation as adLocation')
                    ->leftjoin('ad_locations', 'ad_locations.id', '=', 'ads.adLocationId')
                    ->get();
            $category_products['ads'] = $ads;

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('products' => $category_products)
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

    public function searchproducts() {

        try {
            $categoryId = Input::get('categoryId');
            $searchKey = Input::get('searchKey');
            $sortKey = Input::get('sortKey');
            $sort_value = '';
            $sort_order = '';
            if ($sortKey == 'price_low_to_high') {
                $sort_value = "products.price";
                $sort_order = "ASC";
            }
            if ($sortKey == 'price_high_to_low') {
                $sort_value = "products.price";
                $sort_order = "DESC";
            }
            if ($sortKey == 'recommended') {
                $sortKey = "";
                $sort_value = "";
                $sort_order = "";
            }
            if ($sortKey == 'old_to_new') {
                $sort_value = "products.created_at";
                $sort_order = "ASC";
            }
            if ($sortKey == 'new_to_old') {
                $sort_value = "products.created_at";
                $sort_order = "DESC";
            }
            $userId = Input::get('userId');
            $product_model = new Product();
            if ($userId > 0) {
                $products = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellerCompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->when($categoryId, function ($query) use ($categoryId) {
                            return $query->whereRaw("products.categoryId = $categoryId");
                        })
                        ->when($searchKey, function ($query) use ($searchKey) {
                            return $query->whereRaw("products.name like '$searchKey%'");
                        })
                        ->when($sortKey, function ($query) use ($sortKey, $sort_value, $sort_order) {
                            return $query->orderby($sort_value, $sort_order);
                        })
                        ->whereRaw("(products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                        ->get();
            } else {
                $products = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.userCode as sellerUserCode', 'users.companyName as sellerCompanyName', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->when($categoryId, function ($query) use ($categoryId) {
                            return $query->whereRaw("products.categoryId = $categoryId");
                        })
                        ->when($searchKey, function ($query) use ($searchKey) {
                            return $query->whereRaw("products.name like '$searchKey%'");
                        })
                        ->when($sortKey, function ($query) use ($sortKey, $sort_value, $sort_order) {
                            return $query->orderby($sort_value, $sort_order);
                        })
                        ->whereRaw("(products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                        ->get();
            }

            $products = $product_model->productdetails($products);

            $category_products['products'] = $products;
            $ads = DB::table('ads')
                    ->select('ads.id as adId', 'ads.image as adImage', 'ads.url as adUrl', 'ad_locations.adLocation as adLocation')
                    ->leftjoin('ad_locations', 'ad_locations.id', '=', 'ads.adLocationId')
                    ->get();
            $category_products['ads'] = $ads;

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('products' => $category_products)
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
            $productId = Input::get('productId');

            $wishlistStatus = 0;
            $product_wishlist = DB::table('product_wishlist')
                            ->select('product_wishlist.*')
                            ->where(['product_wishlist.productId' => $productId, 'product_wishlist.userId' => $userId])->first();
            if (count($product_wishlist) > 0) {
                DB::table('product_wishlist')->where('id', '=', $product_wishlist->id)->delete();
            } else {
                $product_wishlistmodel = new Product_wishlist();
                $product_wishlistmodel->productId = $productId;
                $product_wishlistmodel->userId = $userId;

                $product_wishlistmodel->save();
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

    public function wishlist() {

        try {
            $userId = Input::get('userId');
            $product_model = new Product();
            $product_wishlist = DB::table('product_wishlist')
                    ->select('product_wishlist.productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellerCompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('products', 'products.id', '=', 'product_wishlist.productId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->whereRaw("product_wishlist.userId = $userId and (products.isVerified = 1 or products.isVerified = 2) and products.delete_status = 0")
                    ->get();

            $product_wishlist = $product_model->productdetails($product_wishlist);

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('wishlist' => $product_wishlist)
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

    public function sell() {

        try {
            $userId = Input::get('userId');
            $name = Input::get('name');
            $description = Input::get('description');
            $price = Input::get('price');
            $offerPrice = Input::get('offerPrice');
            $categoryId = Input::get('categoryId');
            $dealStatus = Input::get('dealStatus');
            $quantity = Input::get('quantity');
            $acceptOfferStatus = Input::get('acceptOfferStatus');

            $productmodel = new Product();
            $productmodel->name = $name;
            $productmodel->description = $description;
            $productmodel->price = $price;
            $productmodel->offerPrice = $offerPrice;
            $productmodel->userId = $userId;
            $productmodel->categoryId = $categoryId;
            $productmodel->dealStatus = $dealStatus;
            $productmodel->quantity = $quantity;
            $productmodel->acceptOfferStatus = $acceptOfferStatus;

            $productmodel->save();
            $productId = $productmodel->id;

            $productImages = Input::get('images');
            //$productImages = json_decode($productImages,TRUE);

            foreach ($productImages as $productImage) {
                $product_images_model = new Product_images;
                $product_images_model->productId = $productId;
                $product_images_model->image = $productImage;
                $product_images_model->save();
            }

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('productId' => $productId)
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

    public function uploadimage() {

        try {
            $productImage = Input::file('image');
            $filename = time() . $productImage->getClientOriginalName();
            $path = public_path() . '/uploads/products/';
            $productImage->move($path, $filename);
            $productImage = '/uploads/products/' . $filename;


            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('image' => $productImage)
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

    public function order() {

        try {
            $userId = Input::get('userId');
            $productId = Input::get('productId');
            $fullName = Input::get('fullName');
            $buildingNumber = Input::get('buildingNumber');
            $streetNumber = Input::get('streetNumber');
            $zoneNumber = Input::get('zoneNumber');
            $phoneNumber = Input::get('phoneNumber');
            $comments = Input::get('comments');
            $paymentType = Input::get('paymentType');
            $sellingPrice = Input::get('sellingPrice');


            $product_quantity = DB::table('products')
                    ->select('products.quantity')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->orderby('products.created_at', 'DESC')
                    ->where(['products.id' => $productId])
                    ->first();
            if ($product_quantity->quantity < 1) {
                $arrReturn['Result'] = array(
                    'status' => FALSE,
                    'error' => array('message' => 'Sold Out')
                );

                return \Response::json($arrReturn);
            }

            $ordermodel = new Order();
            $ordermodel->orderNumber = time();
            $ordermodel->userId = $userId;
            $ordermodel->productId = $productId;
            $ordermodel->fullName = $fullName;
            $ordermodel->buildingNumber = $buildingNumber;
            $ordermodel->streetNumber = $streetNumber;
            $ordermodel->zoneNumber = $zoneNumber;
            $ordermodel->phoneNumber = $phoneNumber;
            $ordermodel->comments = $comments;
            $ordermodel->paymentType = $paymentType;
            $ordermodel->sellingPrice = $sellingPrice;
            $ordermodel->save();
            $ordreId = $ordermodel->id;

            Product::where('id', $productId)->decrement('quantity', 1);

            $product = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->orderby('products.created_at', 'DESC')
                    ->where(['products.id' => $productId])
                    ->first();

            $product_images = DB::table('product_images')
                    ->select('product_images.image')
                    ->where(['product_images.productId' => $productId])
                    ->get();
            $product->images = $product_images;

            $total_user_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $product->sellerId])->count();
            $total_up_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $product->sellerId, 'user_ratings.rateStatus' => 1])->count();
            if ($total_user_rating > 0) {
                $average_rating = ($total_up_rating / $total_user_rating) * 5;
                $product->sellerRating = $average_rating;
            } else {
                $product->sellerRating = 0;
            }


            $update_user = DB::table('users')
                    ->where(['id' => $userId])
                    ->update(['shippingFullName' => $fullName, 'shippingBuildingNumber' => $buildingNumber, 'shippingStreetNumber' => $streetNumber, 'shippingZoneNumber' => $zoneNumber, 'shippingPhoneNumber' => $phoneNumber]);



            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('product' => $product)
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

    public function deals() {

        try {
            $userId = Input::get('userId');
            $product_model = new Product();
            if ($userId > 0) {
                $deals = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->orderby('products.created_at', 'DESC')
                        ->whereRaw("(products.isVerified = 1 or products.isVerified = 2) and products.dealStatus = 1 and products.delete_status = 0")
                        ->get();
            } else {
                $deals = DB::table('products')
                        ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                        ->leftjoin('users', 'users.id', '=', 'products.userId')
                        ->orderby('products.created_at', 'DESC')
                        ->whereRaw("(products.isVerified = 1 or products.isVerified = 2) and products.dealStatus = 1 and products.delete_status = 0")
                        ->get();
            }

            $deals = $product_model->productdetails($deals);

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('deals' => $deals)
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

    public function update() {

        try {
            $productId = Input::get('productId');
            $userId = Input::get('userId');
            $name = Input::get('name');
            $description = Input::get('description');
            $price = Input::get('price');
            $offerPrice = Input::get('offerPrice');
            $categoryId = Input::get('categoryId');
            $dealStatus = Input::get('dealStatus');
            $quantity = Input::get('quantity');
            $acceptOfferStatus = Input::get('acceptOfferStatus');

            $productmodel = Product::find($productId);
            $productmodel->name = $name;
            $productmodel->description = $description;
            $productmodel->price = $price;
            $productmodel->offerPrice = $offerPrice;
            $productmodel->userId = $userId;
            $productmodel->categoryId = $categoryId;
            $productmodel->dealStatus = $dealStatus;
            $productmodel->quantity = $quantity;
            $productmodel->acceptOfferStatus = $acceptOfferStatus;
            $productmodel->isVerified = 0;
            $productmodel->save();

            $productImages = Input::get('images');
            //$productImages = json_decode($productImages,TRUE);
            DB::table('product_images')->where('productId', '=', $productId)->delete();
            foreach ($productImages as $productImage) {
                $product_images_model = new Product_images;
                $product_images_model->productId = $productId;
                $product_images_model->image = $productImage;
                $product_images_model->save();
            }

            $product = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->orderby('products.created_at', 'DESC')
                    ->where(['products.id' => $productId])
                    ->first();

            $product_images = DB::table('product_images')
                    ->select('product_images.image')
                    ->where(['product_images.productId' => $productId])
                    ->get();
            $product->images = $product_images;

            $total_user_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $product->sellerId])->count();
            $total_up_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $product->sellerId, 'user_ratings.rateStatus' => 1])->count();
            if ($total_user_rating > 0) {
                $average_rating = ($total_up_rating / $total_user_rating) * 5;
                $product->sellerRating = $average_rating;
            } else {
                $product->sellerRating = 0;
            }

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('product' => $product)
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

    public function delete() {

        try {
            $productId = Input::get('productId');

            $productmodel = Product::find($productId);

            $productmodel->delete_status = 1;

            $productmodel->save();

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('productId' => $productId)
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

    public function addview() {

        try {
            $userId = Input::get('userId');
            $productId = Input::get('productId');

            $product_view = DB::table('product_view')
                            ->select('product_view.*')
                            ->where(['product_view.productId' => $productId, 'product_view.userId' => $userId])->first();
            if (count($product_view) > 0) {
                $update_time = DB::table('product_view')
                        ->where(['id' => $product_view->id])
                        ->update(['updated_at' => date('Y-m-d H:i:s')]);
            } else {
                $product_viewmodel = new Product_view();
                $product_viewmodel->productId = $productId;
                $product_viewmodel->userId = $userId;

                $product_viewmodel->save();
            }


            $product = DB::table('products')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->orderby('products.created_at', 'DESC')
                    ->where(['products.id' => $productId])
                    ->first();

            $product_images = DB::table('product_images')
                    ->select('product_images.image')
                    ->where(['product_images.productId' => $productId])
                    ->get();
            $product->images = $product_images;

            $total_user_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $product->sellerId])->count();
            $total_up_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $product->sellerId, 'user_ratings.rateStatus' => 1])->count();
            if ($total_user_rating > 0) {
                $average_rating = ($total_up_rating / $total_user_rating) * 5;
                $product->sellerRating = $average_rating;
            } else {
                $product->sellerRating = 0;
            }




            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('product' => $product)
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

    public function addmessage() {

        try {
            $fromId = Input::get('fromId');
            $toId = Input::get('toId');
            $productId = Input::get('productId');
            $soldStatus = Input::get('soldStatus');
            $message = Input::get('message');


            $conversationsmodel = new Conversations();
            $conversationsmodel->productId = $productId;
            $conversationsmodel->fromId = $fromId;
            $conversationsmodel->toId = $toId;
            $conversationsmodel->soldStatus = $soldStatus;
            $conversationsmodel->message = $message;

            $conversationsmodel->save();

            $fromData = DB::table('users as u')
                    ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType')
                    ->where(['u.id' => $fromId])
                    ->first();
            
            $toDetails = DB::table('users')
                    ->select('users.deviceToken', 'users.deviceOS')
                    ->where(['users.id' => $toId])
                    ->first();
            $deviceToken = $toDetails->deviceToken;
            
            $message_array['notificationType'] = 7;
            $message_array['productId'] = $productId;
            $message_array['toId'] = $toId;
            $message_array['fromId'] = $fromId;
            $message_array['soldStatus'] = $soldStatus;
            $message_array['body'] = $message;
            $message_array['title'] = "You received a new message";
            $message_array['sound'] = "default";
            $message_array['color'] = "#203E78";
            $message_array['sellerUserCode'] = $fromData->userCode;
            $message_array['sellerUserType'] = $fromData->userType;
            $message_array['sellercompanyName'] = $fromData->companyName;

            $usermodel = new User();
            $usermodel->sendPush($deviceToken, $message_array);

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('message' => $message)
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

    public function productschatusers() {

        try {
            $userId = Input::get('userId');
            $productId = Input::get('productId');
            $soldStatus = Input::get('soldStatus');

//            $chatUsers = DB::table('conversations')
//                    ->select('conversations.id as conversationId', 'conversations.toId', 'conversations.fromId', 'conversations.message', 'conversations.created_at as createdAt', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'toUser.firstName as toFirstName', 'toUser.lastName as toLastName', 'toUser.userCode as toUserCode', 'fromUser.firstName as fromFirstName', 'fromUser.lastName as fromLastName', 'fromUser.userCode as fromUserCode', db::raw("(select count(*) from product_wishlist where productId=products.id AND userId = $userId) as favouriteStatus"), 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'products.quantity', 'products.acceptOfferStatus')
//                    ->leftjoin('products', 'products.id', '=', 'conversations.productId')
//                    ->leftjoin('users as toUser', 'toUser.id', '=', 'conversations.toId')
//                    ->leftjoin('users as fromUser', 'fromUser.id', '=', 'conversations.fromId')
//                    ->whereRaw("(conversations.toId = $userId or conversations.fromId = $userId) and conversations.soldStatus = 0")
//                    ->get();

            $chatUsers = DB::table('conversations')
                            ->select(db::raw("case when fromId = $userId then toId  else fromId END as userId"))
                            //->whereRaw("(conversations.toId = $userId or conversations.fromId = $userId) and conversations.soldStatus = $soldStatus and conversations.productId = $productId")
                            ->whereRaw("(conversations.toId = $userId or conversations.fromId = $userId) and conversations.productId = $productId")
                            ->orderby('conversations.created_at', 'DESC')
                            ->get()->unique('userId');
            $full_users = array();
            foreach ($chatUsers as $chatUser) {
                $chatuserId = $chatUser->userId;

                $userData = DB::table('users as u')
                        ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType', db::raw("(select count(*) from conversations where ((conversations.toId = $chatuserId and conversations.fromId = $userId) or (conversations.toId = $userId and conversations.fromId = $chatuserId)) and conversations.productId = $productId and conversations.readStatus = 0) as unreadMessagesCount "))
                        ->where(['u.id' => $chatuserId])
                        ->first();
                array_push($full_users, $userData);
            }
            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('chatUsers' => $full_users)
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

    public function productchats() {

        try {
            $toId = Input::get('toId');
            $fromId = Input::get('fromId');
            $productId = Input::get('productId');
            $soldStatus = Input::get('soldStatus');
            $limit = 20;
            $offset = Input::get('offset');
            if($offset > 0)
            {
            $chats = DB::table('conversations')
                    ->select('conversations.id as conversationId', 'conversations.toId', 'conversations.fromId', 'conversations.message', 'conversations.created_at as createdAt', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'toUser.firstName as toFirstName', 'toUser.lastName as toLastName', 'toUser.userCode as toUserCode', 'fromUser.firstName as fromFirstName', 'fromUser.lastName as fromLastName', 'fromUser.userCode as fromUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'products.quantity', 'products.acceptOfferStatus', 'toUser.companyName as toCompanyName', 'toUser.companyDescription as toCompanyDescription', 'toUser.companyLogo as toCompanyLogo', 'fromUser.companyName as fromCompanyName', 'fromUser.companyDescription as fromCompanyDescription', 'fromUser.companyLogo as fromCompanyLogo')
                    ->leftjoin('products', 'products.id', '=', 'conversations.productId')
                    ->leftjoin('users as toUser', 'toUser.id', '=', 'conversations.toId')
                    ->leftjoin('users as fromUser', 'fromUser.id', '=', 'conversations.fromId')
                    //->whereRaw("((conversations.toId = $toId and conversations.fromId = $fromId) or (conversations.toId = $fromId and conversations.fromId = $toId)) and conversations.soldStatus = $soldStatus and conversations.productId = $productId")
                    ->whereRaw("((conversations.toId = $toId and conversations.fromId = $fromId) or (conversations.toId = $fromId and conversations.fromId = $toId)) and conversations.productId = $productId and conversations.id < $offset")
                    ->orderby('conversations.id','DESC')
                    ->limit($limit)
                    ->get();
            }
            else
            {
            $chats = DB::table('conversations')
                    ->select('conversations.id as conversationId', 'conversations.toId', 'conversations.fromId', 'conversations.message', 'conversations.created_at as createdAt', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'toUser.firstName as toFirstName', 'toUser.lastName as toLastName', 'toUser.userCode as toUserCode', 'fromUser.firstName as fromFirstName', 'fromUser.lastName as fromLastName', 'fromUser.userCode as fromUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'products.quantity', 'products.acceptOfferStatus', 'toUser.companyName as toCompanyName', 'toUser.companyDescription as toCompanyDescription', 'toUser.companyLogo as toCompanyLogo', 'fromUser.companyName as fromCompanyName', 'fromUser.companyDescription as fromCompanyDescription', 'fromUser.companyLogo as fromCompanyLogo')
                    ->leftjoin('products', 'products.id', '=', 'conversations.productId')
                    ->leftjoin('users as toUser', 'toUser.id', '=', 'conversations.toId')
                    ->leftjoin('users as fromUser', 'fromUser.id', '=', 'conversations.fromId')
                    //->whereRaw("((conversations.toId = $toId and conversations.fromId = $fromId) or (conversations.toId = $fromId and conversations.fromId = $toId)) and conversations.soldStatus = $soldStatus and conversations.productId = $productId")
                    ->whereRaw("((conversations.toId = $toId and conversations.fromId = $fromId) or (conversations.toId = $fromId and conversations.fromId = $toId)) and conversations.productId = $productId")
                    ->orderby('conversations.id','DESC')
                    ->limit($limit)
                    ->get();
            }
            $update_read_status = DB::table('conversations')
                    ->whereRaw("(conversations.toId = $fromId and conversations.fromId = $toId) and conversations.productId = $productId")
                    ->update(['readStatus' => 1]);

            $fromData = DB::table('users as u')
                    ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType')
                    ->where(['u.id' => $fromId])
                    ->first();

            $toData = DB::table('users as u')
                    ->select('u.id as userId', 'u.firstName as firstName', 'u.lastName as lastName', 'u.email as email', 'u.phoneNumber as phoneNumber', 'u.otp_verified as otp_verified', 'u.userCode as userCode', 'u.companyName as companyName', 'u.companyDescription as companyDescription', 'u.companyLogo as companyLogo', 'u.user_type as userType')
                    ->where(['u.id' => $toId])
                    ->first();

            $offerDetails = DB::table('product_offers')
                    ->select('product_offers.id as offerId', 'product_offers.productId', 'product_offers.userId', 'product_offers.price as price', 'product_offers.acceptStatus')
                    ->whereRaw("(product_offers.userId = $fromId or  product_offers.userId = $toId)and product_offers.productId = $productId")
                    ->orderby('product_offers.id', 'DESC')
                    ->first();

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('chats' => $chats, 'fromData' => $fromData, 'toData' => $toData, 'offerDetails' => $offerDetails)
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

    public function addoffer() {

        try {
            $userId = Input::get('userId');
            $productId = Input::get('productId');
            $price = Input::get('price');

            $productoffermodel = new Product_offer();
            $productoffermodel->productId = $productId;
            $productoffermodel->userId = $userId;
            $productoffermodel->price = $price;

            $productoffermodel->save();

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('offerId' => $productoffermodel->id)
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

    public function changeofferstatus() {

        try {
            $userId = Input::get('userId');
            $offerId = Input::get('offerId');
            $acceptStatus = Input::get('acceptStatus');

            $update_status = DB::table('product_offers')
                    ->whereRaw("id = $offerId")
                    ->update(['acceptStatus' => $acceptStatus]);

            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('offerId' => $offerId)
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

    public function orderdetails() {
        try {
            $orderId = Input::get('orderId');
            $orderDetails = DB::table('orders')
                    ->select('products.id as productId', 'products.categoryId', 'products.name as productName', 'products.categoryId', 'products.description as productDescription', 'products.price as productPrice', 'products.userId as sellerId', 'users.firstName as sellerFirstName', 'users.lastName as sellerLastName', 'users.companyLogo as sellerCompanyLogo', 'users.companyName as sellercompanyName', 'users.companyName as sellercompanyName', 'users.userCode as sellerUserCode', 'products.isVerified', 'products.offerPrice', 'products.dealStatus', 'users.user_type as sellerUserType', 'products.quantity', 'products.acceptOfferStatus', 'orders.shippingStatus', 'orders.rateStatus', 'orders.id as orderId', 'orders.orderNumber')
                    ->leftjoin('products', 'products.id', '=', 'orders.productId')
                    ->leftjoin('categories', 'categories.id', '=', 'products.categoryId')
                    ->leftjoin('users', 'users.id', '=', 'products.userId')
                    ->where(['orders.id' => $orderId])
                    ->first();
            $product_images = DB::table('product_images')
                    ->select('product_images.image')
                    ->where(['product_images.productId' => $orderDetails->productId])
                    ->get();
            $orderDetails->images = $product_images;

            $total_user_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $orderDetails->sellerId])->count();
            $total_up_rating = DB::table('user_ratings')
                            ->select('user_ratings.*')
                            ->where(['user_ratings.ratedTo' => $orderDetails->sellerId, 'user_ratings.rateStatus' => 1])->count();
            if ($total_user_rating > 0) {
                $average_rating = ($total_up_rating / $total_user_rating) * 5;
                $orderDetails->sellerRating = $average_rating;
            } else {
                $orderDetails->sellerRating = 0;
            }
            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('orderDetails' => $orderDetails)
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

    public function deleteproductchats() {

        try {
            $userId = Input::get('userId');
            $productId = Input::get('productId');
            $soldStatus = Input::get('soldStatus');

            

            $delete_chat = DB::table('conversations')
                    ->whereRaw("(conversations.toId = $userId or conversations.fromId = $userId) and conversations.productId = $productId")
                    ->delete();
            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('productId' => $productId)
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
    
    public function deleteuserchats() {

        try {
            $fromId = Input::get('fromId');
            $toId = Input::get('toId');
            $productId = Input::get('productId');
            $soldStatus = Input::get('soldStatus');

            

            $delete_chat = DB::table('conversations')
                    ->whereRaw("((conversations.toId = $toId and conversations.fromId = $fromId) or (conversations.toId = $fromId and conversations.fromId = $toId)) and conversations.productId = $productId")
                    ->delete();
            $arrReturn['Result'] = array(
                'status' => TRUE,
                'response' => array('productId' => $productId)
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
