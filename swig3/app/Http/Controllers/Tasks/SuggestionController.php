<?php

namespace App\Http\Controllers\Tasks;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Models\Masterresources;
use Kamaln7\Toastr\Facades\Toastr;
use App\Models\Suggestions;
use DB;
use App;
use PDF;
use Excel;

class SuggestionController extends Controller {

    public function index(Request $request) {
        try {
            $paginate = Config::get('app.PAGINATE');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

            $suggestions = DB::table('suggestions')
                    ->select('suggestions.*')
//                    ->leftjoin('employees as submited', 'suggestions.submitted_to', '=', 'submited.id')
                    ->whereRaw("suggestions.status!=0 AND suggestions.created_by=$login_id")
                    ->orderby('suggestions.created_at', 'ASC')
                    ->paginate($paginate);

            if ($request->ajax()) {
                $paginate = Input::get('pagelimit');
                if ($paginate == "") {
                    $paginate = Config::get('app.PAGINATE');
                }

                $searchbytitle = Input::get('searchbytitle');
                $createdatfrom = Input::get('created_at_from');
                $createdatto = Input::get('created_at_to');
                $searchbystatus = Input::get('searchbystatus');
                $searchbysubmited = Input::get('searchbysubmited');

                $sortordtitle = Input::get('sortordtitle');
                $sortordercreated = Input::get('sortordercreated');

                if ($createdatfrom != '') {
                    $createdatfrom = explode('-', $createdatfrom);
                    $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
                }

                if ($createdatto != '') {
                    $createdatto = explode('-', $createdatto);
                    $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
                }

                $sortOrdDefault = '';
                if ($sortordercreated == '' && $sortordtitle == '') {
                    $sortOrdDefault = 'ASC';
                }

                $suggestions = DB::table('suggestions')
                        ->select('suggestions.*')
//                        ->leftjoin('employees as submited', 'suggestions.submitted_to', '=', 'submited.id')
                        ->whereRaw("suggestions.status!=0 AND suggestions.created_by=$login_id")
                        ->when($searchbytitle, function ($query) use ($searchbytitle) {
                            return $query->whereRaw("(suggestions.title like '$searchbytitle%')");
                        })
                        ->when($searchbystatus, function ($query) use ($searchbystatus) {
                            return $query->whereRaw("suggestions.status=$searchbystatus");
                        })
                        ->when($searchbysubmited, function ($query) use ($searchbysubmited) {
                            return $query->whereRaw("suggestions.submitted_to='$searchbysubmited'");
                        })
                        ->when($createdatfrom, function ($query) use ($createdatfrom) {
                            return $query->whereRaw("date(suggestions.created_at)>= '$createdatfrom' ");
                        })
                        ->when($createdatto, function ($query) use ($createdatto) {
                            return $query->whereRaw("date(suggestions.created_at)<= '$createdatto' ");
                        })
                        ->when($sortordtitle, function ($query) use ($sortordtitle) {
                            return $query->orderby('suggestions.title', $sortordtitle);
                        })
                        ->when($sortordercreated, function ($query) use ($sortordercreated) {
                            return $query->orderby('suggestions.created_at', $sortordercreated);
                        })
                        ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                            return $query->orderby('suggestions.created_at', $sortOrdDefault);
                        })
                        ->paginate($paginate);

                return view('dashboard/suggestion/result', array('suggestions' => $suggestions));
            }

            return view('dashboard/suggestion/index', array('suggestions' => $suggestions));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard');
        }
    }

    public function create() {
//        $jobpositions = DB::table('employees')
//                ->select('employees.id as emp_id','m.name as position')
//                ->leftjoin('master_resources as m', 'employees.job_position', '=', 'm.id')
//                ->whereRaw("employees.status!=2 AND m.name IN('CEO','Owner')")
//                ->get();


        return view('dashboard/suggestion/add', array());
    }

    public function save() {
        try {

            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }

          

         
        
         $subfolder = $login_id;
         $s3 = \Storage::disk('s3');
         $world = Config::get('app.WORLD');
    
     $attachment_url="";
        if (Input::hasFile('attachment')) {
             
                $qprofilepic = Input::file('attachment');
                $extension = time() . '.' . $qprofilepic->getClientOriginalExtension();
              
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/SUGGESTION';
                   
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($qprofilepic), 'public');
                
                $attachment_url = Storage::disk('s3')->url($filePath);

              
            }
            
             $model = new Suggestions();
            $model->title = Input::get('title');
            $model->description = Input::get('description');
            $model->submitted_to = Input::get('cmbsumbitto');
            $model->created_by = $login_id;
            $model->attachment = $attachment_url;

            $model->save();
            
            
            Toastr::success('Suggestion Added Successfully!', $title = null, $options = []);
            return Redirect::to('dashboard/suggestion');
        } catch (\Exception $e) {
            
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/suggestion');
        }
    }

    public function edit($id) {
        $dn = \Crypt::decrypt($id);
        $suggestion = DB::table('suggestions')
                ->where(['id' => $dn])
                ->first();

        return view('dashboard/suggestion/edit', array('suggestion' => $suggestion));
    }

    public function update() {
        try {
            $id = Input::get('suggestionid');
            if (Session::get('login_id')) {
                $login_id = Session::get('login_id');
            }
            
            
            
         $subfolder = $login_id;
         $s3 = \Storage::disk('s3');
         $world = Config::get('app.WORLD');
    
     $attachment_url="";
        if (Input::hasFile('attachment')) {
             
                $qprofilepic = Input::file('attachment');
                $extension = time() . '.' . $qprofilepic->getClientOriginalExtension();
              
                $filePath = config('filePath.s3.bucket') . $world . '/' . $subfolder . '/SUGGESTION';
                   
                $filePath = $filePath . '/' . $extension;
                $s3filepath = $s3->put($filePath, file_get_contents($qprofilepic), 'public');
                
                $attachment_url = Storage::disk('s3')->url($filePath);

              $update=array(
                    'title' => Input::get('title'),
                'description' => Input::get('description'),
                'submitted_to' => Input::get('cmbsumbitto'),
                'attachment'=>$attachment_url,
              );
            }else{
               $update=array(
                 'title' => Input::get('title'),
                'description' => Input::get('description'),
                'submitted_to' => Input::get('cmbsumbitto'),
              );  
            }
            
           
            

            $suggestions = DB::table('suggestions')
                    ->where(['id' => $id])
                    ->update($update);

            Toastr::success('Suggestion Updated Successfully!', $title = null, $options = []);
            return Redirect::to('dashboard/suggestion');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/suggestion');
        }
    }

    public function delete($id) {
        try {
            $taskid = \Crypt::decrypt($id);
            $suggestions = DB::table('suggestions')
                    ->where(['id' => $taskid])
                    ->update(['status' => 0]);

            Toastr::success('Task Deleted Successfully', $title = null, $options = []);
            return Redirect::to('dashboard/suggestion');
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('dashboard/suggestion');
        }
    }

    // Generate PDF funcion
    public function exportdata() {

        if (Session::get('login_id')) {
            $login_id = Session::get('login_id');
        }

        $excelorpdf = Input::get('excelorpdf');
        $searchbytitle = Input::get('searchbytitle');
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $searchbystatus = Input::get('searchbystatus');
        $searchbysubmited = Input::get('searchbysubmited');

        $sortordtitle = Input::get('sortordtitle');
        $sortordercreated = Input::get('sortordercreated');

        if ($createdatfrom != '') {
            $createdatfrom = explode('-', $createdatfrom);
            $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
        }

        if ($createdatto != '') {
            $createdatto = explode('-', $createdatto);
            $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
        }

        $sortOrdDefault = '';
        if ($sortordercreated == '' && $sortordtitle == '') {
            $sortOrdDefault = 'ASC';
        }

        $suggestions = DB::table('suggestions')
                ->select('suggestions.*')
//                ->leftjoin('employees as submited', 'suggestions.submitted_to', '=', 'submited.id')
                ->whereRaw("suggestions.status!=0 AND suggestions.created_by=$login_id")
                ->when($searchbytitle, function ($query) use ($searchbytitle) {
                    return $query->whereRaw("(suggestions.title like '$searchbytitle%')");
                })
                ->when($searchbystatus, function ($query) use ($searchbystatus) {
                    return $query->whereRaw("suggestions.status=$searchbystatus");
                })
                ->when($searchbysubmited, function ($query) use ($searchbysubmited) {
                    return $query->whereRaw("suggestions.submitted_to='$searchbysubmited'");
                })
                ->when($createdatfrom, function ($query) use ($createdatfrom) {
                    return $query->whereRaw("date(suggestions.created_at)>= '$createdatfrom' ");
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(suggestions.created_at)<= '$createdatto' ");
                })
                ->when($sortordtitle, function ($query) use ($sortordtitle) {
                    return $query->orderby('suggestions.title', $sortordtitle);
                })
                ->when($sortordercreated, function ($query) use ($sortordercreated) {
                    return $query->orderby('suggestions.created_at', $sortordercreated);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('suggestions.created_at', $sortOrdDefault);
                })
                ->get();

        if ($excelorpdf == "Excel") {

            Excel::create('Suggestions', function($excel) use($suggestions) {
                // Set the title
                $excel->setTitle('Suggestions');

                $excel->sheet('Suggestions', function($sheet) use($suggestions) {
                    // Sheet manipulation

                    $sheet->setCellValue('C3', 'Suggestions');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Sl No', 'Title', 'Submitted To','Status', 'Created Date', 'Description'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($suggestions); $i++) {

                        if ($suggestions[$i]->status == 2) {
                            $status = "Noted";
                        } else {
                            $status = "New";
                        }

                        $sheet->setCellValue('A' . $chrRow, ($i + 1));
                        $sheet->setCellValue('B' . $chrRow, $suggestions[$i]->title);
                        $sheet->setCellValue('C' . $chrRow, $suggestions[$i]->submitted_to);
                        $sheet->setCellValue('D' . $chrRow, $status);
                        $sheet->setCellValue('E' . $chrRow, date("d-m-Y", strtotime($suggestions[$i]->created_at)));
                        $sheet->setCellValue('F' . $chrRow, $suggestions[$i]->description);

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        } else {

            $html_table = '<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
                <title>Project Name</title>
                <style>
                        .listerType1 tr:nth-of-type(2n) {
                                background: rgba(0, 75, 111, 0.12) none repeat 0 0;
                        }

                </style>
            </head>
            <body style="margin: 0; padding: 0;font-family:Arial;">
                <section id="container">
                <div style="text-align:center;"><h1>Suggestions</h1></div>
                    <table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">
                        <thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">
                            <tr class="headingHolder">
                                <td style="padding:10px 10px;color:#fff;"> Sl. No.</td>
                                <td style="padding:10px 5px;color:#fff;"> Title</td>
                                <td style="padding:10px 5px;color:#fff;"> Submitted To</td>
                                <td style="padding:10px 5px;color:#fff;"> Status </td>
                                <td style="padding:10px 5px;color:#fff;"> Created Date </td>
                                <td style="padding:10px 5px;color:#fff;"> Description </td>
                            </tr>
                        </thead>
                        <tbody class="planbody" id="planbody" >';
            $slno = 0;
            foreach ($suggestions as $suggestion) {
                if ($suggestion->status == 2) {
                    $status = "Noted";
                } else {
                    $status = "New";
                }

                $slno++;
                $html_table .='<tr>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 10px;">' . $slno . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $suggestion->title . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $suggestion->submitted_to . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $status . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . date("d-m-Y", strtotime($suggestion->created_at)) . '</td>
                                    <td style="color: #535352; font-size: 14px;padding: 10px 5px;">' . $suggestion->description . '</td>
                                </tr>';
            }
            $html_table .='</tbody>
                            </table>
                        </section>
                    </body>
            </html>';


            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($html_table);
            return $pdf->download('mtg_sugestions.pdf');
        }
    }

}
