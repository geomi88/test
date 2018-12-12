<?php

namespace App\Http\Controllers\Cms\Category;

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
use App\Models\Category;

class CategoryController extends Controller {

    public function index() {
        try {
            $paginate = 10;
            $categories = DB::table('categories')
                            ->select('categories.*')
                            ->orderby('created_at', 'DESC')->paginate($paginate);
            return view('category/index', array('categories' => $categories));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }

    public function delete($id) {
        try {
            $categories_delete = DB::table('categories')
                    ->where(['id' => $id])
                    ->update(['status' => 0]);


            Toastr::success('Successfully Disabled the Category', $title = null, $options = []);
            return Redirect::to('users');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('users');
        }
    }

    

    public function edit($id) {

        $category_details = DB::table('categories')
                ->where(['id' => $id])
                ->first();

        return view('category/edit', array('category_details' => $category_details));
    }

    

    

    public function update() {
        $category_id = Input::get('category_id');
        $categoryName = Input::get('categoryName');
        $description = Input::get('description');
        $categoryImage = Input::file('categoryImage');     
        
        if(isset($categoryImage)) {
        $filename = time() . $categoryImage->getClientOriginalName();
        $path = public_path() . '/uploads/category/';
        $categoryImage->move($path, $filename);
        $categoryImage = '/uploads/category/'.$filename;
        $update_category = DB::table('categories')
                ->where(['id' => $category_id])
                ->update(['name' => $categoryName, 'description' => $description, 'image' => $categoryImage]);
        }
        else {
            $update_category = DB::table('categories')
                ->where(['id' => $category_id])
                ->update(['name' => $categoryName, 'description' => $description]);
        }
        Toastr::success('Successfully Edited the Category', $title = null, $options = []);
        return Redirect::to("category/edit/$category_id");
    }

    public function add() {

        return view('category/add');
    }

    public function store() {
        $categoryName = Input::get('categoryName');
        $description = Input::get('description');
        $categoryImage = Input::file('categoryImage');
        $filename = time() . $categoryImage->getClientOriginalName();
        $path = public_path() . '/uploads/category/';
        $categoryImage->move($path, $filename);
        $categoryImage = '/uploads/category/'.$filename;
        
        
        $categorymodel = new Category();
        $categorymodel->name = $categoryName;
        $categorymodel->description = $description;
        $categorymodel->image = $categoryImage;
        $categorymodel->status = 1;
        $categorymodel->save();
        Toastr::success('Successfully Added the Category', $title = null, $options = []);
        return Redirect::to("category");
    }
    
    public function checkname() {
        $category_id = Input::get('category_id');
        $categoryName = Input::get('categoryName');
        if($category_id > 0)
        {
            $category_details = DB::table('categories')
                ->whereRaw("name = '$categoryName' and id != $category_id")
                ->first();
        }
        else
        {
            $category_details = DB::table('categories')
                ->whereRaw("name = '$categoryName'")
                ->first();
        }
        if (count($category_details) > 0) {
            return 1;
        } else {
            return 0;
        }
    }

}
