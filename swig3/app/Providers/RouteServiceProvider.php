<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    protected $adminnamespace = 'App\Http\Controllers\Admin';
    protected $loginnamespace = 'App\Http\Controllers\Login';
    protected $masterresources = 'App\Http\Controllers\Masterresources';
    protected $employeenamespace = 'App\Http\Controllers\Employee';
    protected $usermodulesnamespace = 'App\Http\Controllers\Usermodules';
    protected $mis = 'App\Http\Controllers\Mis';
    protected $inventory = 'App\Http\Controllers\Inventory';
    protected $branchsales = 'App\Http\Controllers\Branchsales';
    protected $hr = 'App\Http\Controllers\Hr';
    protected $operation = 'App\Http\Controllers\Operation';
    protected $requisition = 'App\Http\Controllers\Requisitions';
    protected $supervisors = 'App\Http\Controllers\Supervisors';
    protected $warehouse = 'App\Http\Controllers\Warehouse';
    protected $kpi = 'App\Http\Controllers\Kpi';
    protected $branch = 'App\Http\Controllers\Branch';
    protected $managementconsole = 'App\Http\Controllers\Managementconsole';
    protected $tasks = 'App\Http\Controllers\Tasks';


    protected $purchase = 'App\Http\Controllers\Purchase';
    protected $meeting = 'App\Http\Controllers\Meeting';
    protected $reception = 'App\Http\Controllers\Reception';
    protected $costcenter = 'App\Http\Controllers\Costcenter';
    protected $finance = 'App\Http\Controllers\Finance';
    protected $checklist = 'App\Http\Controllers\Checklist';
    protected $taxation = 'App\Http\Controllers\Taxation';
    protected $ledgers = 'App\Http\Controllers\Ledgers';
   
    protected $helper = 'App\Http\Controllers\Helper';
    
    
    protected $organizationchart = 'App\Http\Controllers\Organizationchart';    
    protected $training = 'App\Http\Controllers\Training';    
    protected $elegantclub = 'App\Http\Controllers\Elegantclub';    
    protected $crm = 'App\Http\Controllers\Crm';    
    protected $rfq = 'App\Http\Controllers\Rfq';    
/**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
        
        $this->mapAdminRoutes();

        $this->mapLoginRoutes();
        
        $this->mapMasterresourcesRoutes();
        
        $this->mapMisRoutes();
        
        
        $this->mapEmployeeRoutes();
        
        $this->mapUsermodulesRoutes();
        
        $this->mapInventoryRoutes();
        
        $this->mapBranchsalesRoutes();
        
        $this->mapHrRoutes();
        
        $this->mapOperationRoutes();
        
         $this->mapRequisitionsRoutes();
         
         $this->mapSupervisorsRoutes();
         
         $this->mapWarehouseRoutes();
         
         $this->mapKpiRoutes();
         
         $this->mapBranchRoutes();
         
         $this->mapManagementconsoleRoutes();
         
         $this->mapTasksRoutes();

         
         $this->mapPurchaseRoutes();
         
         $this->mapMeetingRoutes();
         
         $this->mapReceptionRoutes();
         
         $this->mapTrainingRoutes();
         
         $this->mapCostcenterRoutes();
         
         $this->mapFinanceRoutes();
         $this->mapChecklistRoutes();
         $this->mapTaxationRoutes();
         $this->mapHelperRoutes();
         $this->mapOrganizationchartRoutes();
         $this->mapLedgersRoutes();
         $this->mapElegantclubRoutes();
         $this->mapCrmRoutes();
         $this->mapRfqRoutes();
        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }
    
    protected function mapAdminRoutes()
    {
        Route::group(['namespace' => $this->adminnamespace], function ($router) {
            require app_path('../routes/admin.php');
            });
    }
    
    protected function mapLoginRoutes()
    {
        Route::group(['namespace' => $this->loginnamespace], function ($router) {
            require app_path('../routes/login.php');
            });
    }
    
    protected function mapMasterresourcesRoutes()
    {
        Route::group(['namespace' => $this->masterresources], function ($router) {
            require app_path('../routes/masterresources.php');
            });
    }
    
    protected function mapElegantclubRoutes()
    {
        Route::group(['namespace' => $this->elegantclub], function ($router) {
            require app_path('../routes/elegantclub.php');
            });
    }
    
    protected function mapCrmRoutes()
    {
        Route::group(['namespace' => $this->crm], function ($router) {
            require app_path('../routes/crm.php');
            });
    }
    
     protected function mapMisRoutes()
    {
        Route::group(['namespace' => $this->mis], function ($router) {
            require app_path('../routes/mis.php');
            });
    }
    
    protected function mapEmployeeRoutes()
    {
        Route::group(['namespace' => $this->employeenamespace], function ($router) {
            require app_path('../routes/employee.php');
            });
    }
    
    protected function mapUsermodulesRoutes()
    {
        Route::group(['namespace' => $this->usermodulesnamespace], function ($router) {
            require app_path('../routes/usermodules.php');
            });
    }
    protected function mapInventoryRoutes()
    {
        Route::group(['namespace' => $this->inventory], function ($router) {
            require app_path('../routes/inventory.php');
            });
    }
    
    protected function mapBranchsalesRoutes()
    {
        Route::group(['namespace' => $this->branchsales], function ($router) {
            require app_path('../routes/branchsales.php');
            });
    }
    
    protected function mapHrRoutes()
    {
        Route::group(['namespace' => $this->hr], function ($router) {
            require app_path('../routes/hr.php');
            });
    }
     protected function mapOperationRoutes()
    {
        Route::group(['namespace' => $this->operation], function ($router) {
            require app_path('../routes/operation.php');
            });
    }
    protected function mapRequisitionsRoutes()
    {
        Route::group(['namespace' => $this->requisition], function ($router) {
            require app_path('../routes/requisition.php');
            });
    }
    
    protected function mapSupervisorsRoutes()
    {
        Route::group(['namespace' => $this->supervisors], function ($router) {
            require app_path('../routes/supervisors.php');
            });
    }
    
    protected function mapWarehouseRoutes()
    {
        Route::group(['namespace' => $this->warehouse], function ($router) {
            require app_path('../routes/warehouse.php');
            });
    }
    
    protected function mapKpiRoutes()
    {
        Route::group(['namespace' => $this->kpi], function ($router) {
            require app_path('../routes/kpi.php');
            });
    }
    
    protected function mapBranchRoutes()
    {
        Route::group(['namespace' => $this->branch], function ($router) {
            require app_path('../routes/branch.php');
            });
    }
    
    protected function mapManagementconsoleRoutes()
    {
        Route::group(['namespace' => $this->managementconsole], function ($router) {
            require app_path('../routes/managementconsole.php');
            });
    }
    
    protected function mapTasksRoutes()
    {
        Route::group(['namespace' => $this->tasks], function ($router) {
            require app_path('../routes/tasks.php');
            });
    }


    
    protected function mapPurchaseRoutes()
    {
        Route::group(['namespace' => $this->purchase], function ($router) {
            require app_path('../routes/purchase.php');
            });
    }
    
    protected function mapMeetingRoutes()
    {
        Route::group(['namespace' => $this->meeting], function ($router) {
            require app_path('../routes/meeting.php');
            });
    }
    
    protected function mapReceptionRoutes()
    {
        Route::group(['namespace' => $this->reception], function ($router) {
            require app_path('../routes/reception.php');
            });
    }
    
    protected function mapTrainingRoutes()
    {
        Route::group(['namespace' => $this->training], function ($router) {
            require app_path('../routes/training.php');
            });
    }
    
    protected function mapCostcenterRoutes()
    {
        Route::group(['namespace' => $this->costcenter], function ($router) {
            require app_path('../routes/costcenter.php');
            });
    }
    protected function mapFinanceRoutes()
    {
        Route::group(['namespace' => $this->finance], function ($router) {
            require app_path('../routes/finance.php');
            });
    }
    
    protected function mapChecklistRoutes()
    {
        Route::group(['namespace' => $this->checklist], function ($router) {
            require app_path('../routes/checklist.php');
            });
    }
    
   protected function mapTaxationRoutes()
    {
        Route::group(['namespace' => $this->taxation], function ($router) {
            require app_path('../routes/taxation.php');
            });
    }
    
    protected function mapHelperRoutes()
    {
        Route::group(['namespace' => $this->helper], function ($router) {
            require app_path('../routes/helper.php');
            });
    }

    protected function mapOrganizationchartRoutes()
    {
        Route::group(['namespace' => $this->organizationchart], function ($router) {
            require app_path('../routes/organizationchart.php');
            });
    }
    
    protected function mapLedgersRoutes()
    {
        Route::group(['namespace' => $this->ledgers], function ($router) {
            require app_path('../routes/ledgers.php');
            });
    }
    protected function mapRfqRoutes()
    {
        Route::group(['namespace' => $this->rfq], function ($router) {
            require app_path('../routes/rfq.php');
            });
    }
}