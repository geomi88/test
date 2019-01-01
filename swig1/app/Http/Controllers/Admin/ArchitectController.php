<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Architect;
use App\Model\ArchitectProject;
use App\Model\ArchitectImage;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Kamaln7\Toastr\Facades\Toastr;

class ArchitectController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin_auth');
        //write athenitcate function for admin
    }
    public function add()
    {
        $projectsList = DB::table('projects')
            ->select('projects.name_en as name','projects.id')
            ->where('projects.status', '=', '1')
            ->get();
        $dataArray=array(
            'projectsList'=>$projectsList
        );
        return view('admin.architect.add',$dataArray);

    }

    public function architect_listing()
    {
        try {
            $paginate = 10;
            $architects = DB::table('architects')
                ->select('architects.*')
                ->where(['architects.status' => 1])
                ->orderby('architects.created_at', 'DESC')->paginate($paginate);
            return view('admin.architect.architect-listing', array('architects' => $architects));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/dashboard');
        }
    }
    public function save_architect(Request $request)
    {
        try {
            $input = Input::all();

            $architect_model = new Architect();

            $architect_model->name_en = Input::get('name_en');
            $architect_model->name_ka = Input::get('name_ka');
            $architect_model->name_ru = Input::get('name_ru');
            $architect_model->address_en = Input::get('address_en');
            $architect_model->address_ka = Input::get('address_ka');
            $architect_model->address_ru = Input::get('address_ru');
            $architect_model->address_en = Input::get('address_en');
            $architect_model->address_ka = Input::get('address_ka');
            $architect_model->address_ru = Input::get('address_ru');

            $architect_model->description_en = Input::get('description_en');
            $architect_model->description_ka = Input::get('description_ka');
            $architect_model->description_ru = Input::get('description_ru');

            $architect_model->additional_description_en = Input::get('additional_description_en');
            $architect_model->additional_description_ka = Input::get('additional_description_ka');
            $architect_model->additional_description_ru = Input::get('additional_description_ru');

            $architect_model->phone = Input::get('phone');
            $architect_model->email = Input::get('email');
            
            
            $projects=Input::get('projects'); 
            $architect_model->project_id=$projects;
            
            $architect_model->save();
            $architect_id = $architect_model->id;
            
            $projects=explode(',',$projects);
            
            foreach($projects as $architectProjects){
                
                $objModel = new ArchitectProject();
                $objModel->architect_id=$architect_id;
                $objModel->project_id=$architectProjects;
                $objModel->save();
                
            }

            $galleryCount = Input::get('galleryCount');
            for ($i = 0; $i <= $galleryCount; $i++) {

                if ($request->file('gallery_upload_' . $i)) {

                    $image = $request->file('gallery_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "gallery_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/architect-gallery';
                    $image->move($path, $filename);

                    $architectImage_model = new ArchitectImage();
                    $architectImage_model->architect_id = $architect_id;
                    $architectImage_model->image = $filename;
                    $architectImage_model->save();

                }
            }

            if ($request->hasFile('preview_upload')) {
                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $filename = "gallery_" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/architect-gallery';
                $file->move($path, $filename);

                $architectImage_model = new ArchitectImage();
                $architectImage_model->architect_id = $architect_id;
                $architectImage_model->image = $filename;
                $architectImage_model->main_image = '1';
                $architectImage_model->save();
            }

            Toastr::success('Successfully Added New Architect!', $title = null, $options = []);
            return Redirect::to('admin/add-architect');

        } catch (Exception $ex) {
            echo  $ex->getMessage();
          //  Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
         //   return Redirect::to('admin/add-architect');
        }
    }

    public function view_architect($id)
    {
        try {

            if ($id != "") {
                $id = \Crypt::decrypt($id);

                $render_params = array();

                $architect_details = DB::table('architects')
                    ->select('architects.*')
                    ->where(['architects.id' => $id])
                    ->first();

                $gallery = DB::table('architect_images')
                    ->select('architect_images.*')
                    ->whereRaw("architect_images.architect_id=$id")
                    ->where('architect_images.status', '=', '1')
                    ->get();

                $render_params['architect_details'] = $architect_details;
                $render_params['gallery'] = $gallery;

            }
            return view('admin.architect.architect-detail-view', array('render_params' => $render_params));

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    public function edit_architect($id)
    {
        try {

            if ($id != "") {
                $id = \Crypt::decrypt($id);

                $render_params = array();

                $architect_details = DB::table('architects')
                    ->select('architects.*')
                    ->where(['architects.id' => $id])
                    ->first();

                $gallery = DB::table('architect_images')
                    ->select('architect_images.*')
                    ->whereRaw("architect_images.architect_id=$id")
                    ->where('architect_images.status', '=', '1')
                    ->get();
                
                $projectsList = DB::table('projects')
                    ->select('projects.name_en as name','projects.id')
                    ->where('projects.status', '=', '1')
                    ->get();
        

                $render_params['architect_details'] = $architect_details;
                $render_params['gallery'] = $gallery;
                $render_params['projectsList'] = $projectsList;

            }
            return view('admin.architect.edit', array('render_params' => $render_params));

        } catch (Exception $ex) {
            $render_params['message'] = "Server Error : " . $ex->getMessage() . ' on ' . $ex->getLine();
        }

    }

    public function update_architect(Request $request)
    {
        try {

            $input = Input::all();

            $architect_id = \Crypt::decrypt($input['architect_id']);

            $architect_model = new Architect();
            $architect_model->exists = true;
            $architect_model->id = $architect_id;
            $architect_model->name_en = Input::get('name_en');
            $architect_model->name_ka = Input::get('name_ka');
            $architect_model->name_ru = Input::get('name_ru');
            $architect_model->address_en = Input::get('address_en');
            $architect_model->address_ka = Input::get('address_ka');
            $architect_model->address_ru = Input::get('address_ru');
            $architect_model->address_en = Input::get('address_en');
            $architect_model->address_ka = Input::get('address_ka');
            $architect_model->address_ru = Input::get('address_ru');

            $architect_model->description_en = Input::get('description_en');
            $architect_model->description_ka = Input::get('description_ka');
            $architect_model->description_ru = Input::get('description_ru');

            $architect_model->additional_description_en = Input::get('additional_description_en');
            $architect_model->additional_description_ka = Input::get('additional_description_ka');
            $architect_model->additional_description_ru = Input::get('additional_description_ru');

            $architect_model->phone = Input::get('phone');
            $architect_model->email = Input::get('email');
            
            $projects=Input::get('projects'); 
            $architect_model->project_id=$projects;
            
            $projects=explode(',',$projects);
            
            DB::table('architect_projects')
                    ->whereRaw("architect_id=$architect_id")
                    ->update(['status' => '0']);
             
            foreach($projects as $architectProjects){
                
                $objModel = new ArchitectProject();
                $objModel->architect_id=$architect_id;
                $objModel->project_id=$architectProjects;
                $objModel->save();
                
            }
            
            $architect_model->save();

            if ($request->hasFile('preview_upload')) {
                DB::table('architect_images')
                    ->whereRaw("architect_id=$architect_id AND status='1' AND main_image='1'")
                    ->update(['status' => '0']);

                $file = $request->file('preview_upload');
                $name = $file->getClientOriginalName();
                $filename = "gallery_" . strtotime(now()) . '.' . $file->getClientOriginalExtension();
                $path = public_path() . '/uploads/architect-gallery';
                $file->move($path, $filename);

                $architectImage_model = new ArchitectImage();
                $architectImage_model->architect_id = $architect_id;
                $architectImage_model->image = $filename;
                $architectImage_model->main_image = '1';
                $architectImage_model->save();
            }

            $galleryCount = Input::get('galleryCount');

            for ($i = 0; $i <= $galleryCount; $i++) {

                if ($request->file('gallery_upload_' . $i)) {

                    $image = $request->file('gallery_upload_' . $i);
                    $name = $image->getClientOriginalName();
                    $filename = "gallery_" . $i . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $path = public_path() . '/uploads/architect-gallery';
                    $image->move($path, $filename);

                    $architectImage_model = new ArchitectImage();
                    $architectImage_model->architect_id = $architect_id;
                    $architectImage_model->image = $filename;
                    $architectImage_model->save();

                }
            }

            Toastr::success('Updated Architect Details Successfully', $title = null, $options = []);
            return Redirect::to('admin/architect-listing');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/architect-listing');

        }

    }

    public function remove_architect(Request $request, $architect_id)
    {
        try {
            $architect_id = \Crypt::decrypt($architect_id);
            if ($architect_id != "") {
                $architect_model = new Architect();
                $architect_model->exists = true;
                $architect_model->id = $architect_id;
                $architect_model->status = 0;
                $architect_model->save();
            }

            Toastr::success('Successfully Removed Architect Details!', $title = null, $options = []);
            return Redirect::to('admin/architect-listing');
        } catch (Exception $ex) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('admin/architect-listing');
        }
    }

    public function remove_gallery(Request $request)
    {

        $imgId = Input::get('imgId');
        DB::table('architect_images')
            ->whereRaw("id=$imgId")
            ->update(['status' => '0']);
        return 1;

    }

}
