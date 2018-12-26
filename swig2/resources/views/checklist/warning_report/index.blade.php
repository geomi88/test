@extends('layouts.main')
@section('content')
<script>
    $(window).on('hashchange', function () {
        if (window.location.hash) {
            var page = window.location.hash.replace('#', '');
            if (page == Number.NaN || page <= 0) {
                return false;
            } else {
                getData(page);
            }
        }
    });
    $(document).ready(function ()
    {
        $(document).on('click', '.pagination a', function (event)
        {

            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            //var myurl = $(this).attr('href');
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });

     $('.takePrint').click(function () {
            
        if(!$("#code").is(':checked')&&!$("#empName").is(':checked')&&!$("#nationality").is(':checked')&&!$("#jobPositionn").is(':checked')&&!$("#warningType").is(':checked')&&!$("#title").is(':checked')&&!$("#date").is(':checked')&&!$("#branch").is(':checked')&&!$("#reportedBy").is(':checked')&&!$("#description").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#code").is(':checked')){
                strStyle+=' .war_code {display:none;}';
         }
         if(!$("#empName").is(':checked')){
                strStyle+=' .war_name {display:none;}';
         }
         if(!$("#nationality").is(':checked')){
               strStyle+=' .war_nation {display:none;}';
         }
         if(!$("#jobPositionn").is(':checked')){
              strStyle+=' .war_job {display:none;}';  
         } 
         if(!$("#warningType").is(':checked')){
              strStyle+=' .war_type {display:none;}';   
         }
         if(!$("#title").is(':checked')){
               strStyle+=' .war_title {display:none;}';   
         }
         if(!$("#date").is(':checked')){
             strStyle+=' .war_date {display:none;}';     
         }
         if(!$("#branch").is(':checked')){
             strStyle+=' .war_branch {display:none;}';     
         }
         if(!$("#reportedBy").is(':checked')){
             strStyle+=' .war_by {display:none;}';     
         }
         if(!$("#description").is(':checked')){
             strStyle+=' .war_description {display:none;}';     
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Warnings Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_code"> Code</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_name"> Employee Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_nation"> Nationality</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_job"> Job Position</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_type"> Warning Type</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_title"> Title</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_date"> Date</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_branch"> Branch </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_by"> Reported By </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="war_description"> Description </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.categorybody')[0].outerHTML +'</table>'); 
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
    });
    
    function getData(page) {

        var searchbytitle = $('#searchbytitle').val();
        var searchbybranch = $('.searchbybranch').val();
        var searchbyemployee = $('#searchbyemployee').val();
        var job_position = $('#job_position').val();
        var searchbycode = $('#searchbycode').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var searchbytype = $('.searchbytype').val();
        var searchbycountry = $('.searchbycountry').val();
        var searchbyreportedby = $('#searchbyreportedby').val();
        
        var sortordtitle = $('#sortordtitle').val();
        var sortordbranch = $('#sortordbranch').val();
        var sortordemployee = $('#sortordemployee').val();
        var sortordjob =$('#sortordjob').val();
        var sortordcode =$('#sortordcode').val();
        var sortordercreated=$('#sortordercreated').val();
        var sortordercountry=$('#sortordercountry').val();
        var sortordtype = $('#sortordtype').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                data: { searchbytitle:searchbytitle,job_position: job_position,searchbybranch:searchbybranch,
                        searchbyemployee:searchbyemployee,sortordtitle:sortordtitle,searchbycode:searchbycode,
                        created_at_from:created_at_from,created_at_to:created_at_to,searchbytype:searchbytype,
                        searchbycountry:searchbycountry,searchbyreportedby:searchbyreportedby,sortordcode:sortordcode,
                        sortordercreated:sortordercreated,sortordercountry:sortordercountry,sortordtype:sortordtype,
                        sortordbranch:sortordbranch,sortordemployee:sortordemployee,sortordjob:sortordjob,
                        pagelimit: pagelimit, searchable: searchable},
            })
            .done(function (data)
            {
                console.log(data);

                $(".categorybody").empty().html(data);
                location.hash = page;
            })
            .fail(function (jqXHR, ajaxOptions, thrownError)
            {
                alert('No response from server');
            });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('checklist/warnings_report/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Warnings Report</h1>
    </header>
    

    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>
                     <div class="printChoose">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="code" checked="" type="checkbox">
                                        <span></span>
                                        <em>Code</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="empName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Employee Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="nationality" checked="" type="checkbox">
                                        <span></span>
                                        <em>Nationality</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="jobPositionn" checked="" type="checkbox">
                                        <span></span>
                                        <em>Job Position</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="warningType" checked="" type="checkbox">
                                        <span></span>
                                        <em>Warning Type</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="title" checked="" type="checkbox">
                                        <span></span>
                                        <em>Title</em>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                         <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="date" checked="" type="checkbox">
                                        <span></span>
                                        <em>Date</em>
                                    </label>
                                </div>
                            </div>
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="branch" checked="" type="checkbox">
                                        <span></span>
                                        <em>Branch</em>
                                    </label>
                                </div>
                            </div>
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="reportedBy" checked="" type="checkbox">
                                        <span></span>
                                        <em>Reported By</em>
                                    </label>
                                </div>
                            </div>
                         </div>
                         <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="description" checked="" type="checkbox">
                                        <span></span>
                                        <em>Description</em>
                                    </label>
                                </div>
                            </div>
                         </div>
                        <div class="custRow">
                            <div class="custCol-4">
                                <a class="commonBtn takePrint bgDarkGreen">Print</a>
                            </div>
                        </div>
                    </div>
    <div class="fieldGroup" id="fieldSet1">
        <div class="customClear"></div>
    </div>
    
    <div class="listHolderType1">
    <div class="listerType1 reportLister"> 

            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
            <input type="hidden" value="" id="sortordtitle" name="sortordtitle">
            <input type="hidden" value="" id="sortordbranch" name="sortordbranch">
            <input type="hidden" value="" id="sortordemployee" name="sortordemployee">
            <input type="hidden" value="" id="sortordjob" name="sortordjob">
            <input type="hidden" value="" id="sortordcode" name="sortordcode">
            <input type="hidden" value="" id="sortordercreated" name="sortordercreated">            
            <input type="hidden" value="" id="sortordercountry" name="sortordercountry">   
            <input type="hidden" value="" id="sortordtype" name="sortordtype">
            
            <div id="tblcategorytable">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                             <td>
                                Code
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp codeup"></a>
                                    <a href="javascript:void(0)" class="btnDown codedown"></a>
                                </div>
                            </td>
                            
                            <td>
                                Employee Name
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp empup"></a>
                                    <a href="javascript:void(0)" class="btnDown empdown"></a>
                                </div>
                            </td>
                            
                             <td>
                                Nationality
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp countryup"></a>
                                    <a href="javascript:void(0)" class="btnDown countrydown"></a>
                                </div>
                            </td>
                            
                            <td>
                                Job Position
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp jobup"></a>
                                    <a href="javascript:void(0)" class="btnDown jobdown"></a>
                                </div>
                            </td>
                            <td>
                                Warning Type
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp typeup"></a>
                                    <a href="javascript:void(0)" class="btnDown typedown"></a>
                                </div>
                            </td>
                            <td>
                                Title
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp titleup"></a>
                                    <a href="javascript:void(0)" class="btnDown titledown"></a>
                                </div>
                            </td>
                            
                            <td>
                                Date
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp createdup"></a>
                                    <a href="javascript:void(0)" class="btnDown createddown"></a>
                                </div>
                            </td>
                            <td>
                                Branch
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp branchup"></a>
                                    <a href="javascript:void(0)" class="btnDown branchdown"></a>
                                </div>
                            </td>
                            
                             <td>
                                Reported By
                               
                            </td>
                            <td>
                                Description
                            </td>
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">
                            <td class="filterFields" style="min-width: 120px;">
                                <div class="custCol-12">
                                    <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Code">
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbyemployee" name="searchbyemployee" placeholder="Enter Employee Name">
                                    </div>
                                </div>
                            </td>
                           
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbycountry" name="searchbycountry" id="searchbycountry">
                                        <option value="">All</option>
                                        @foreach ($countries as $country)
                                            <option value="{{$country->id}}" >{{$country->name}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </td>
                           
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="job_position" name="job_position" id="job_position">
                                        <option value="">All</option>
                                        @foreach ($job_positions as $job_position)
                                            <option value="{{$job_position->id}}"><?php echo str_replace('_', ' ', $job_position->name); ?></option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbytype" name="searchbytype">
                                        <option value="">All</option>
                                        @foreach ($warning_types as $warning_type)
                                        <option value="{{$warning_type->id}}" <?php if($warning_type->id==$category_id){ echo "selected";}?>>{{$warning_type->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbytitle" name="searchbytitle" placeholder="Enter Title">
                                    </div>
                                </div>
                            </td>
                            
                             <td class="filterFields clsdate">
                                <div class="custCol-6">
                                    <input type="text" id="created_at_from" name="created_at_from" value="" placeholder="From ">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="created_at_to" name="created_at_to" value="" placeholder="To ">
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbybranch" name="searchbybranch">
                                        <option value="">All</option>
                                        @foreach ($allbranches as $branch)
                                        <option value="{{$branch->branch_id}}">{{$branch->code}} : {{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            
                             <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbyreportedby" name="searchbyreportedby" placeholder="Enter Name">
                                    </div>
                                </div>
                            </td>
                            
                            <td  class="filterFields">
                            </td>
                            
                        </tr>

                    </thead>

                    <tbody class="categorybody" id='categorybody'>
                        @include('checklist/warning_report/result')

                    </tbody>

                </table>
            </div>
            <div class="commonLoaderV1"></div>
        </div>

    </div>
    <div class="pagesShow">
        <span>Showing 10 of 20</span>
        <select id="page-limit">

            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
    </div>
    </form>
</div>

<script>
    
    $('#searchbycode').bind('keyup', function () {
        search();
    });
    
     $('#searchbyemployee').bind('keyup', function () {
        search();
    });
    
    $('#country').on('change', function () {
        search();
    });
    
    $('#job_position').on('change', function () {
        search();
    });
    
    $('.searchbytype').on("change", function () {
            search();
    });
    
    $('.searchbycountry').on("change", function () {
            search();
    });
    
    $('#searchbytitle').bind('keyup', function () {
        search();
    });
    
    $('#searchbyreportedby').bind('keyup', function () {
        search();
    });
    
    $('#created_at_from').on("change", function () {
            search();
    });
    
    $('#created_at_to').on("change", function () {
            search();
    });
    
    $('.searchbybranch').on("change", function () {
            search();
    });
    
    $(".titleup").on('click', function () {
        $('#sortordtitle').val('ASC');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".titledown").on('click', function () {
        $('#sortordtitle').val('DESC');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".branchup").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('ASC');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".branchdown").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('DESC');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".empup").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('ASC');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".empdown").on('click', function () {
       $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('DESC');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
    
     $(".jobup").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('ASC');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });

    $(".jobdown").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('DESC');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".createdup").on('click', function () {
       $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('ASC');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".createddown").on('click', function () {
       $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('DESC');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".codeup").on('click', function () {
       $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('ASC');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
        search();
    });
        
    $(".codedown").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('DESC');
        $('#sortordercreated').val('');
        $('#sortordercountry').val('');
        $('#sortordtype').val('');
       search();
    });
   
   $(".typeup").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordtype').val('ASC');
        $('#sortordercountry').val('');
        search();
    });
    
    $(".typedown").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordtype').val('DESC');
        $('#sortordercountry').val('');
        search();
    });
    
    $(".countryup").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordtype').val('');
        $('#sortordercountry').val('ASC');
        search();
    });
    
    $(".countrydown").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        $('#sortordercreated').val('');
        $('#sortordtype').val('');
        $('#sortordercountry').val('DESC');
        search();
    });
   
    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {
        var searchbytitle = $('#searchbytitle').val();
        var searchbybranch = $('.searchbybranch').val();
        var searchbyemployee = $('#searchbyemployee').val();
        var job_position = $('#job_position').val();
        var searchbycode = $('#searchbycode').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var searchbytype = $('.searchbytype').val();
        var searchbycountry = $('.searchbycountry').val();
        var searchbyreportedby = $('#searchbyreportedby').val();
        
        var sortordtitle = $('#sortordtitle').val();
        var sortordbranch = $('#sortordbranch').val();
        var sortordemployee = $('#sortordemployee').val();
        var sortordjob =$('#sortordjob').val();
        var sortordcode =$('#sortordcode').val();
        var sortordercreated=$('#sortordercreated').val();
        var sortordercountry=$('#sortordercountry').val();
        var sortordtype = $('#sortordtype').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'warnings_report',
            data: { searchbytitle:searchbytitle,job_position: job_position,searchbybranch:searchbybranch,
                searchbyemployee:searchbyemployee,sortordtitle:sortordtitle,searchbycode:searchbycode,
                created_at_from:created_at_from,created_at_to:created_at_to,searchbytype:searchbytype,
                searchbycountry:searchbycountry,searchbyreportedby:searchbyreportedby,sortordcode:sortordcode,
                sortordercreated:sortordercreated,sortordercountry:sortordercountry,sortordtype:sortordtype,
                sortordbranch:sortordbranch,sortordemployee:sortordemployee,sortordjob:sortordjob,
                pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                
                if (return_data != '')
                {
                   
                    $('.categorybody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.categorybody').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }

    $(function () {

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');
            
            $('#sortordtitle').val('');
            $('#sortordbranch').val('');
            $('#sortordemployee').val('');
            $('#sortordjob').val('');
            $('#sortordcode').val('');
            $('#sortordercreated').val('');
            $('#sortordercountry').val('');
            $('#sortordtype').val('');
            
            $('#page-limit').val(10);
            //search();
            window.location.href = '{{url("checklist/warnings_report")}}';
        });
    });

    $("#created_at_from").datepicker({
        changeMonth: true,
        changeYear: true, dateFormat: 'dd-mm-yy',
        onSelect: function(selected) {
        $("#created_at_to").datepicker("option","minDate", selected)
        search();
        }
    });

    $("#created_at_to").datepicker({
        changeMonth: true,
        changeYear: true, dateFormat: 'dd-mm-yy'
    });
    
    function funExportData(strType)
    {
        if(strType=="PDF"){
            $('#excelorpdf').val('PDF');
        }else{
            $('#excelorpdf').val('Excel');
        }    
        
        document.getElementById("pdfgenerator").submit();
    }
</script>
@endsection
