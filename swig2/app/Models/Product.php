<?php

namespace App\Models;
use App\Models\Product_images;
use App\Models\User_rating;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $table = 'products';

    public function productdetails($products) {
        foreach ($products as $product) {
            $product_images = Product_images::where(['product_images.productId' => $product->productId])
                    ->select('product_images.image')
                    ->get();
            $product->images = $product_images;

            $total_user_rating = User_rating::where(['user_ratings.ratedTo' => $product->sellerId])
                            ->select('user_ratings.*')
                            ->count();
            $total_up_rating = User_rating::where(['user_ratings.ratedTo' => $product->sellerId, 'user_ratings.rateStatus' => 1])
                            ->select('user_ratings.*')
                            ->count();
            if ($total_user_rating > 0) {
                $average_rating = ($total_up_rating / $total_user_rating) * 5;
                $product->sellerRating = $average_rating;
            } else {
                $product->sellerRating = 0;
            }
        }
        return $products;
    }

}
