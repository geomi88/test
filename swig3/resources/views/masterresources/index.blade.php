@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>
    <a class="dashboardItems" href="{{ action('Masterresources\CompaniesController@index') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconCompany.png" alt="Company"></figure>
                <figcaption>Company</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ action('Masterresources\RegionController@index') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconNationality.png" alt="Region"></figure>
                <figcaption>Region</figcaption>
            </div>
        </div>
    </a>
     <a class="dashboardItems" href="{{ url('masterresources/areas') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconCountry.png" alt="Units"></figure>
                <figcaption>Areas</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ action('Masterresources\BranchController@index') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconBranches.png" alt="Branch"></figure>
                <figcaption>Branch</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('masterresources/warehouse') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconWarehouse.png" alt="Warehouse"></figure>
                <figcaption>Warehouse</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('masterresources/spot') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconLocation.png"></figure>
                <figcaption>Spot</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ action('Masterresources\DocsController@add') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconBranches.png" alt="Branch"></figure>
                <figcaption>Upload Planning</figcaption>
            </div>
        </div>
    </a>
      <a class="dashboardItems" href="{{ url('masterresources/job_positions') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconJobposition.png" alt="Job Positions"></figure>
                <figcaption>Job Positions</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ action('Masterresources\DepartmentController@index') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconDepartment.png" alt="Department"></figure>
                <figcaption>Department</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ action('Masterresources\DivisionController@index') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconDivision.png" alt="Department"></figure>
                <figcaption>Division</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('masterresources/inventory_category') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconInventoryGroups.png" alt="Inventory Category"></figure>
                <figcaption>Inventory Category</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('masterresources/units') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconUom.png" alt="Units"></figure>
                <figcaption>Units</figcaption>
            </div>
        </div>
    </a>

  
    <a class="dashboardItems" href="{{ url('masterresources/job_shifts') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/imgMaintenance.png" alt="Units"></figure>
                <figcaption>Job Shifts</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('masterresources/pos_reasons') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconPlanning.png" alt="Units"></figure>
                <figcaption>POS Reasons</figcaption>
            </div>
        </div>
    </a>
   
    <a class="dashboardItems" href="{{ url('masterresources/bank') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconBanks.png" alt="Bank"></figure>
                <figcaption>Bank</figcaption>
            </div>
        </div>
    </a>    
    
    <a class="dashboardItems" href="{{ url('masterresources/warning_type') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconWarningType.png" alt="Warning Types"></figure>
                <figcaption>Warning Types</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('masterresources/meeting_room') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconMeetingRoom.png" alt="Meeting Rooms"></figure>
                <figcaption>Meeting Rooms</figcaption>
            </div>
        </div>
    </a>
    

    <a class="dashboardItems" href="{{ url('masterresources/ledger') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconLedger.png" alt="Ledger"></figure>
                <figcaption>Ledger</figcaption>
            </div>
        </div>
    </a>



    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconVisaDesignation.png" alt="Visa Designation"></figure>
                <figcaption>Visa Designation</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconGender.png" alt="Gender"></figure>
                <figcaption>Gender</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconEmployeeStatus.png" alt="Employment Status"></figure>
                <figcaption>Employment Status</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconUom.png" alt="UOM"></figure>
                <figcaption>UOM</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconBloodGroups.png" alt="Blood Groups"></figure>
                <figcaption>Blood Groups</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconReligions.png" alt="Religions"></figure>
                <figcaption>Religions</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconVisaPlace.png" alt="Visa issue place"></figure>
                <figcaption>Visa issue place</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconVisaType.png" alt="Visa Type"></figure>
                <figcaption>Visa Type</figcaption>
            </div>
        </div>
    </a>

    

    

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconpayrol.png" alt="Payroll"></figure>
                <figcaption>Payroll</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconInsurance.png" alt="Insurance"></figure>
                <figcaption>Insurance</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconVehicles.png" alt="Vehicles"></figure>
                <figcaption>Vehicles</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconEligibility.png" alt="Eligibility"></figure>
                <figcaption>Eligibility</figcaption>
            </div>
        </div>
    </a>

 <a class="dashboardItems" href="{{ url('masterresources/excepted') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/exceptedEmployees.png" alt="Excluded Employee"></figure>
                <figcaption>Excepted Employee</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('masterresources/chartcategory') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconChartCategory.png" alt="Chart Category"></figure>
                <figcaption>Chart Category</figcaption>
            </div>
        </div>
    </a> 


<a class="dashboardItems" href="{{ url('masterresources/cutoffdate') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconCutofDate.png" alt="Cut Off Date"></figure>
                <figcaption>Cut Off Date</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('masterresources/ledger_group') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconAccountGroup.png" alt="Ledger Group"></figure>
                <figcaption>Account Group</figcaption>
            </div>
        </div>
    </a>


    <a class="dashboardItems" href="{{ url('masterresources/id_professionals') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconIDProfession.png" alt="ID Professional"></figure>
                <figcaption>ID Profession</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('masterresources/report_settings') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconReportSettings.png" alt="Report Settings"></figure>
                <figcaption>Report Settings</figcaption>

            </div>
        </div>
    </a>
    <a class="dashboardItems" href="{{ url('masterresources/office') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconOffice.png" alt="Office"></figure>
                <figcaption>Office</figcaption>

            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('masterresources/staff_house') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconBranches.png" alt="Staff House"></figure>
                <figcaption>Staff House</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('masterresources/costname') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconChartCategory.png" alt="Cost Name"></figure>
                <figcaption>Cost Name</figcaption>
            </div>
        </div>
    </a> 
    
    <a class="dashboardItems" href="{{ url('masterresources/policy_master') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconPlanning.png" alt="Policy Master"></figure>
                <figcaption>Policy Master</figcaption>
            </div>
        </div>
    </a> 
    
<?php
    } else {
        foreach ($user_sub_modules as $user_sub_module) {
            ?>
            <a class="dashboardItems" href="{{ url($user_sub_module->url) }}">
                <div class="dashboardTable">
                    <div class="dashboardTd">
                        <figure><img src="{{ URL::asset('images/'.$user_sub_module->logo) }}" alt="Warehouse"></figure>
                        <figcaption>{{$user_sub_module->name}}</figcaption>
                    </div>
                </div>
            </a>
    <?php }} ?>

</div>
@endsection
