<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WebsiteContent;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Kamaln7\Toastr\Facades\Toastr;

class WebsitecontentsController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin_auth');
        //write athenitcate function for admin
    }

    public function index()
    {
        try {
            $website_contents = DB::table('website_contents')
                ->select('website_contents.*')
                ->orderby('website_contents.title', 'ASC')->get();
            return view('admin.website_contents.index', array('website_contents' => $website_contents));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/dashboard');
        }
    }

    public function getcontent()
    {
        $content_id = Input::get('content_id');
        $contents = WebsiteContent::select('*')->where('id', $content_id)->first();
        return $contents;
    }

    public function savecontent()
    {
        $id = Input::get('content_id');
        $content = Input::get('content');
        $object = new WebsiteContent;
        $object->exists = true;
        $object->id = $id;
        $object->content = $content;
        $save = $object->save();
        

    }

}
