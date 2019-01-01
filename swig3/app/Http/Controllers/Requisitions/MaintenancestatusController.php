<?php

namespace App\Http\Controllers\Requisitions;

use App\Events\RequisitionSubmitted;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Kamaln7\Toastr\Facades\Toastr;
use App\Helpers\CategoryHierarchy;
use Customhelper;
use Exception;
use DB;
use PDF;
use Excel;
use App;

class MaintenancestatusController extends Controller {

    public function index(Request $request) {

        $paginate = Config::get('app.PAGINATE');

        $requsitions = DB::table('requisition')
                ->select('requisition.*','master_resources.resource_type','master_resources.name as centername',db::raw("(case when maintenance_status=2 then 'Completed' else 'Pending' end) as maintenancestatus"))
                ->leftjoin('requisition_types', 'requisition.requisition_type', '=', 'requisition_types.id')
                ->leftjoin('master_resources', 'requisition.branch_id', '=', 'master_resources.id')
                ->whereRaw("requisition_types.name='Maintainance Requisition' AND requisition.status=4")
                ->orderby('requisition.created_at', 'DESC')
                ->paginate($paginate);

        if ($request->ajax()) {
            $paginate = Input::get('pagelimit');
            if ($paginate == "") {
                $paginate = Config::get('app.PAGINATE');
            }

            $searchbycode = Input::get('searchbycode');
            $searchbyname = Input::get('searchbyname');
            $searchbycenter = Input::get('searchbycenter');
            $createdatfrom = Input::get('created_at_from');
            $createdatto = Input::get('created_at_to');
            $resource = Input::get('resource');
            $status = Input::get('status');
            $sortordcode = Input::get('sortordcode');
            $sortordtitle = Input::get('sortordtitle');
            $sortorddate = Input::get('sortorddate');

            $sortOrdDefault = '';
            if ($sortordtitle == '' && $sortordcode == '' && $sortorddate== '') {
                $sortOrdDefault = 'DESC';
            }

            if ($createdatfrom != '') {
                $createdatfrom = explode('-', $createdatfrom);
                $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
            }
            if ($createdatto != '') {
                $createdatto = explode('-', $createdatto);
                $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
            }

            $requsitions = DB::table('requisition')
                    ->select('requisition.*','master_resources.resource_type','master_resources.name as centername',db::raw("(case when maintenance_status=2 then 'Completed' else 'Pending' end) as maintenancestatus"))
                    ->leftjoin('requisition_types', 'requisition.requisition_type', '=', 'requisition_types.id')
                    ->leftjoin('master_resources', 'requisition.branch_id', '=', 'master_resources.id')
                    ->whereRaw("requisition_types.name='Maintainance Requisition' AND requisition.status=4")
                    ->when($searchbycode, function ($query) use ($searchbycode) {
                        return $query->whereRaw("requisition.requisition_code like '%$searchbycode%' ");
                    })
                    ->when($searchbyname, function ($query) use ($searchbyname) {
                        return $query->whereRaw("requisition.title like '%$searchbyname%' ");
                    })
                    ->when($searchbycenter, function ($query) use ($searchbycenter) {
                        return $query->whereRaw("master_resources.name like '%$searchbycenter%' ");
                    })
                    ->when($createdatfrom, function ($query) use ($createdatfrom) {
                        return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                    })
                    ->when($createdatto, function ($query) use ($createdatto) {
                        return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                    })
                    ->when($resource, function ($query) use ($resource) {
                        return $query->whereRaw("master_resources.resource_type='$resource' ");
                    })
                    ->when($status, function ($query) use ($status) {
                        return $query->whereRaw("requisition.maintenance_status=$status ");
                    })
                    ->when($sortordcode, function ($query) use ($sortordcode) {
                        return $query->orderby('requisition.requisition_code', $sortordcode);
                    })
                    ->when($sortordtitle, function ($query) use ($sortordtitle) {
                        return $query->orderby('requisition.title', $sortordtitle);
                    })
                    ->when($sortorddate, function ($query) use ($sortorddate) {
                        return $query->orderby('requisition.created_at', $sortorddate);
                    })
                    ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                        return $query->orderby('requisition.created_at', $sortOrdDefault);
                    })
                    ->paginate($paginate);

            return view('requisitions/maintenance_status/results', array('requsitions' => $requsitions));
        }
        return view('requisitions/maintenance_status/index', array('requsitions' => $requsitions));
    }

    
    public function view($id) {
        try {
            $id = \Crypt::decrypt($id);

            $pageData = array();
            $requisitiondata = DB::table('requisition')
                            ->select('requisition.*','requisition.id as req_id',DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"), 'master_resources.name', 'master_resources.branch_code', 'master.name as job_position', 'employees.username as empcode')
                            ->leftjoin('employees', 'requisition.created_by', '=', 'employees.id')
                            ->leftjoin('master_resources', 'master_resources.id', '=', 'requisition.branch_id')
                            ->leftjoin('master_resources as master', 'master.id', '=', 'employees.job_position')
                            ->where(['requisition.id' => $id])->first();

            $center_type = DB::table('master_resources')
                            ->select('resource_type')
                            ->where(['id' => $requisitiondata->branch_id])->first();
            
            $pageData['center_type'] = $center_type->resource_type;

            $pageData['action_takers'] = DB::table('requisition_activity as req_a')
                            ->select('req_a.created_at', 'req_a.action', 'req_a.comments', DB::raw("concat(empl.first_name,' ' ,empl.alias_name) as action_taker"), DB::raw("case when action=1 then 'Approved' when action=3 then 'Rejected' end as action"))
                            ->leftjoin('employees as empl', 'req_a.actor_id', '=', 'empl.id')
                            ->where('req_a.requisition_id', '=', $id)->where('req_a.status', '=', 1)->get();

            $documents = DB::table('requisition_docs')
                            ->select('requisition_docs.*', DB::raw("concat(employees.first_name,' ' ,employees.alias_name) as createdby"))
                            ->leftjoin('employees', 'requisition_docs.created_by', '=', 'employees.id')
                            ->where(['requisition_docs.requisition_id' => $id])->get();

            $next_action_takers_list = array();
            if ($requisitiondata->status != 5) {
                $pending_actions = array();
                if ($requisitiondata->next_level != NULL) {
                    $pending_actions = DB::table('requisition_hierarchy as rh')
                            ->select('rh.approver_type', 'rh.approver_id', 'rh.level')
                            ->where('rh.requisition_type_id', '=', $requisitiondata->requisition_type)
                            ->where('rh.level', '>=', $requisitiondata->next_level)
                            ->where('rh.status', '=', 1)
                            ->orderby('rh.level', 'ASC')
                            ->get();
                }

                $topmanager_details = DB::table("employees as empl")
                        ->select('empl.top_manager_id as top_manager_id', DB::raw("concat(tm.first_name, ' ', tm.alias_name) as name"))
                        ->join('employees as tm', 'empl.top_manager_id', '=', 'tm.id')
                        ->where('empl.id', '=', $requisitiondata->created_by)
                        ->first();

                foreach ($pending_actions as $pending_action) {

                    if ($pending_action->approver_type == 'TOP_MANAGER') { // Top manager details we have already fetch on top
                        $pending_actions_takers['name'] = $topmanager_details->name;
                        $pending_actions_takers['id'] = $topmanager_details->top_manager_id;
                        $pending_actions_takers['action'] = "Waiting";
                    } else if ($pending_action->approver_type == 'EMPLOYEE') {
                        $emp_data = DB::table('employees as empl')
                                        ->select(DB::raw("concat(empl.first_name, ' ', empl.alias_name) as name"), 'empl.id as empl_id')
                                        ->where('empl.id', '=', $pending_action->approver_id)->first();

                        $pending_actions_takers['name'] = $emp_data->name;
                        $pending_actions_takers['id'] = $emp_data->empl_id;
                        $pending_actions_takers['action'] = "Waiting";
                    }

                    array_push($next_action_takers_list, $pending_actions_takers);
                }
            }
            
            $requisitiondata->expenditure= Customhelper::numberformatter($requisitiondata->expenditure);
            $pageData['next_action_takers_list'] = $next_action_takers_list;
            $pageData['requisitiondata'] = $requisitiondata;

            return view('requisitions/maintenance_status/view', array('pageData' => $pageData, 'documents' => $documents));
        } catch (\Exception $e) {
            Toastr::error('Sorry There Was Some Problem!', $title = null, $options = []);
            return Redirect::to('requisitions/maintenance_status');
        }
    }

    public function exportdata() {
        $excelorpdf = Input::get('excelorpdf');
        $searchbycode = Input::get('searchbycode');
        $searchbyname = Input::get('searchbyname');
        $searchbycenter = Input::get('searchbycenter');
        
        $createdatfrom = Input::get('created_at_from');
        $createdatto = Input::get('created_at_to');
        $resource = Input::get('resource');
        $status = Input::get('status');
        $sortordcode = Input::get('sortordcode');
        $sortordtitle = Input::get('sortordtitle');
        $sortorddate = Input::get('sortorddate');

        $sortOrdDefault = '';
        if ($sortordtitle == '' && $sortordcode == '' && $sortorddate== '') {
            $sortOrdDefault = 'DESC';
        }

        if ($createdatfrom != '') {
            $createdatfrom = explode('-', $createdatfrom);
            $createdatfrom = $createdatfrom[2] . '-' . $createdatfrom[1] . '-' . $createdatfrom[0];
        }
        if ($createdatto != '') {
            $createdatto = explode('-', $createdatto);
            $createdatto = $createdatto[2] . '-' . $createdatto[1] . '-' . $createdatto[0];
        }

        $requsitions = DB::table('requisition')
                ->select('requisition.*','master_resources.resource_type','master_resources.name as centername',db::raw("(case when maintenance_status=2 then 'Completed' else 'Pending' end) as maintenancestatus"))
                ->leftjoin('requisition_types', 'requisition.requisition_type', '=', 'requisition_types.id')
                ->leftjoin('master_resources', 'requisition.branch_id', '=', 'master_resources.id')
                ->whereRaw("requisition_types.name='Maintainance Requisition' AND requisition.status=4")
                ->when($searchbycode, function ($query) use ($searchbycode) {
                    return $query->whereRaw("requisition.requisition_code like '%$searchbycode%' ");
                })
                ->when($searchbyname, function ($query) use ($searchbyname) {
                    return $query->whereRaw("requisition.title like '%$searchbyname%' ");
                })
                ->when($searchbycenter, function ($query) use ($searchbycenter) {
                    return $query->whereRaw("master_resources.name like '%$searchbycenter%' ");
                })
                ->when($createdatfrom, function ($query) use ($createdatfrom) {
                    return $query->whereRaw("date(requisition.created_at)>= '$createdatfrom' ");
                })
                ->when($createdatto, function ($query) use ($createdatto) {
                    return $query->whereRaw("date(requisition.created_at)<= '$createdatto' ");
                })
                ->when($resource, function ($query) use ($resource) {
                    return $query->whereRaw("master_resources.resource_type='$resource' ");
                })
                ->when($status, function ($query) use ($status) {
                    return $query->whereRaw("requisition.maintenance_status=$status ");
                })
                ->when($sortordcode, function ($query) use ($sortordcode) {
                    return $query->orderby('requisition.requisition_code', $sortordcode);
                })
                ->when($sortordtitle, function ($query) use ($sortordtitle) {
                    return $query->orderby('requisition.title', $sortordtitle);
                })
                ->when($sortorddate, function ($query) use ($sortorddate) {
                    return $query->orderby('requisition.created_at', $sortorddate);
                })
                ->when($sortOrdDefault, function ($query) use ($sortOrdDefault) {
                    return $query->orderby('requisition.created_at', $sortOrdDefault);
                })
                ->get();


        if ($excelorpdf == "EXCEL") {

            Excel::create('Maintenance_Work_Status', function($excel) use($requsitions) {
                // Set the title
                $excel->setTitle('Maintenance Work Status');

                $excel->sheet('Maintenance Work Status', function($sheet) use($requsitions) {
                    // Sheet manipulation

                    $sheet->setCellValue('B3', 'Maintenance Work Status');
                    $sheet->setHeight(3, 20);
                    $sheet->cells('A3:F3', function($cells) {
                        $cells->setBackground('#00CED1');
                        $cells->setFontWeight('bold');
                        $cells->setFontSize(14);
                    });

                    $chrRow = 6;

                    $sheet->row(5, array('Requisition Code', 'Title', 'Date', 'Center Type','Center Name','Status'));
                    $sheet->setHeight(5, 15);
                    $sheet->cells('A5:F5', function($cells) {
                        $cells->setBackground('#6495ED');
                        $cells->setFontWeight('bold');
                    });

                    for ($i = 0; $i < count($requsitions); $i++) {
                        
                        if ($requsitions[$i]->resource_type == "BRANCH") {
                            $text = "Branch";
                        }
                        if ($requsitions[$i]->resource_type == "OFFICE") {
                            $text = "Office";
                        }
                        if ($requsitions[$i]->resource_type == "WAREHOUSE") {
                            $text = "Warehouse";
                        }
    
                        $sheet->setCellValue('A' . $chrRow, $requsitions[$i]->requisition_code);
                        $sheet->setCellValue('B' . $chrRow, $requsitions[$i]->title);
                        $sheet->setCellValue('C' . $chrRow, date("d-m-Y", strtotime($requsitions[$i]->created_at)));
                        $sheet->setCellValue('D' . $chrRow, $text);
                        $sheet->setCellValue('E' . $chrRow, $requsitions[$i]->centername);
                        $sheet->setCellValue('F' . $chrRow, $requsitions[$i]->maintenancestatus);

                        $sheet->cells('A' . $chrRow . ':F' . $chrRow, function($cells) {
                            $cells->setFontSize(9);
                        });

                        $chrRow++;
                    }
                });
            })->export('xls');
        }
    }

}
