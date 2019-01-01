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
            
        if(!$("#employeeCodee").is(':checked')&&!$("#employeeNamee").is(':checked')&&!$("#jobPositionn").is(':checked')&&!$("#categoryNamee").is(':checked')&&!$("#checkpointt").is(':checked')&&!$("#datee").is(':checked')&&!$("#branchh").is(':checked')&&!$("#ratingg").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#employeeCodee").is(':checked')){
                strStyle+=' .check_code {display:none;}';
         }
         if(!$("#employeeNamee").is(':checked')){
                strStyle+=' .check_name {display:none;}';
         }
         if(!$("#jobPositionn").is(':checked')){
               strStyle+=' .check_job {display:none;}';
         }
         if(!$("#categoryNamee").is(':checked')){
              strStyle+=' .check_category {display:none;}';  
         } 
         if(!$("#checkpointt").is(':checked')){
              strStyle+=' .check_point {display:none;}';   
         }
         if(!$("#datee").is(':checked')){
               strStyle+=' .check_date {display:none;}';   
         }
         if(!$("#branchh").is(':checked')){
             strStyle+=' .check_branch {display:none;}';     
         }
         if(!$("#ratingg").is(':checked')){
             strStyle+=' .check_rating {display:none;}';     
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Check List Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_code"> Employee Code </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_name"> Employee Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_job"> Job Position</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_category"> Category Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_point"> Check Point </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_date"> Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_branch"> Branch </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_rating"> Rating </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.reportbody')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
    });
    
    function getData(page) {
        var search_key = $('#search_key').val();
        var job_position = $('#job_position').val();
        var searchbycode = $('#searchbycode').val();
        
        var searchbycategory = $('.searchbycategory').val();
        var searchbypoint = $('#searchbypoint').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var searchbybranch = $('.searchbybranch').val();
        var searchbyrating = $('.searchbyrating').val();
    
        var sortordname=$('#sortordname').val();
        var sortordjob =$('#sortordjob').val();
        var sortordcode =$('#sortordcode').val();
        
        var sortordbranch = $('#sortordbranch').val();
        var sortorddate = $('#sortorddate').val();
        var sortordpoint = $('#sortordpoint').val();
        var sortordcategory = $('#sortordcategory').val();
        var sortordrating = $('#sortordrating').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        
        $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                data: {search_key: search_key, job_position: job_position, searchbycode: searchbycode,searchbycategory:searchbycategory,searchbypoint:searchbypoint,start_date:start_date,end_date:end_date,searchbybranch:searchbybranch,searchbyrating:searchbyrating,sortordname:sortordname,sortordjob:sortordjob,sortordcode:sortordcode,sortordbranch:sortordbranch,sortorddate:sortorddate,sortordpoint:sortordpoint,sortordcategory:sortordcategory,sortordrating:sortordrating,pagelimit: pagelimit, searchable: searchable},
                // {
                //     you can show your loader 
                // }
            })
            .done(function (data)
            {
                console.log(data);

                $(".reportbody").empty().html(data);
                location.hash = page;
            })
            .fail(function (jqXHR, ajaxOptions, thrownError)
            {
                alert('No response from server');
            });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('checklist/checklist_report/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Check List <span>Report</span></h1>
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
                                        <input id="employeeCodee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Employee Code</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="employeeNamee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Employee Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="jobPositionn" checked="" type="checkbox">
                                        <span></span>
                                        <em>Job Position</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="categoryNamee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Category Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="checkpointt" checked="" type="checkbox">
                                        <span></span>
                                        <em>Check Point</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="datee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Date</em>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                         <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="branchh" checked="" type="checkbox">
                                        <span></span>
                                        <em>Branch</em>
                                    </label>
                                </div>
                            </div>
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="ratingg" checked="" type="checkbox">
                                        <span></span>
                                        <em>Rating</em>
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
            <input type="hidden" value="" id="sortordname" name="sortordname">
            <input type="hidden" value="" id="sortordjob" name="sortordjob">
            <input type="hidden" value="" id="sortordcode" name="sortordcode">
            <input type="hidden" value="" id="sortordcategory" name="sortordcategory">
            <input type="hidden" value="" id="sortordpoint" name="sortordpoint">
            <input type="hidden" value="" id="sortorddate" name="sortorddate">
            <input type="hidden" value="" id="sortordbranch" name="sortordbranch">
            <input type="hidden" value="" id="sortordrating" name="sortordrating">
            
            <div id="tblcategorytable">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td>
                                Employee Code
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp codeup"></a>
                                    <a href="javascript:void(0)" class="btnDown codedown"></a>
                                </div>
                            </td>
                            <td>
                                Employee Name
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp nameup"></a>
                                    <a href="javascript:void(0)" class="btnDown namedown"></a>
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
                                Category Name
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp categoryup"></a>
                                    <a href="javascript:void(0)" class="btnDown categorydown"></a>
                                </div>
                            </td>
                            <td>
                                Check Point
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp pointup"></a>
                                    <a href="javascript:void(0)" class="btnDown pointdown"></a>
                                </div>
                            </td>
                            <td>
                                Date
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp dateup"></a>
                                    <a href="javascript:void(0)" class="btnDown datedown"></a>
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
                               Rating
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp ratingup"></a>
                                    <a href="javascript:void(0)" class="btnDown ratingdown"></a>
                                </div>
                            </td>
                           
                            <td>
                                View
                            </td>
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Employee Code">
                                </div>
                            </td>
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <input type="text" id="search_key" name="search_key" placeholder="Enter Employee Name">
                                </div>
                            </td>
                            <td>
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
                                    <select class="searchbycategory" name="searchbycategory">
                                        <option value="">All</option>
                                        @foreach ($checklist_categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbypoint" name="searchbypoint" placeholder="Enter Category Name">
                                    </div>
                                </div>
                            </td>
                           
                             <td class="filterFields clsdate">
                               
                                <div class="custCol-6">
                                    <input type="text" id="start_date" name="start_date" value="" placeholder="From ">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="end_date" name="end_date" value="" placeholder="To ">
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
                            
                            <td class="filterFields" style="min-width: 116px;">
                                <div class="custCol-12">
                                    <select class="searchbyrating" name="searchbyrating">
                                        <option value="">All</option>
                                        <option value="Good" <?php if($rating_id=="Good"){ echo "selected";}?>>Good</option>
                                        <option value="Average" <?php if($rating_id=="Average"){ echo "selected";}?>>Average</option>
                                        <option value="Bad" <?php if($rating_id=="Bad"){ echo "selected";}?>>Bad</option>
                                       
                                    </select>
                                </div>
                            </td>
                            
                            <td>
                            </td>

                        </tr>

                    </thead>

                    <tbody class="reportbody" id='reportbody'>
                        @include('checklist/checklist_report/result')
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
    
    $('#search_key').bind('keyup', function () {
            search();
    });
    
    $('#job_position').on('change', function () {
        search();
    });

    $('#searchbycode').bind('keyup', function () {
        search();
    });
        
    $('.searchbycategory').on("change", function () {
            search();
    });

    $('#searchbypoint').bind('keyup', function () {
            search();
    });
    
    $('#start_date').on("change", function () {
            search();
    });
    
    $('#end_date').on("change", function () {
            search();
    });
    
    $('.searchbybranch').on("change", function () {
            search();
    });
    
    $('.searchbyrating').on("change", function () {
            search();
    });
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        
        $('#sortordcategory').val('');
        $('#sortordpoint').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        search();
    });

    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        
        $('#sortordcategory').val('');
        $('#sortordpoint').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        search();
    });

    $(".jobup").on('click', function () {
        $('#sortordname').val('');
        $('#sortordjob').val('ASC');
        $('#sortordcode').val('');
        
        $('#sortordcategory').val('');
        $('#sortordpoint').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        search();
    });

    $(".jobdown").on('click', function () {
        $('#sortordname').val('');
        $('#sortordjob').val('DESC');
        $('#sortordcode').val('');
        
        $('#sortordcategory').val('');
        $('#sortordpoint').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        search();
    });

    $(".codeup").on('click', function () {
        $('#sortordname').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('ASC');
        
        $('#sortordcategory').val('');
        $('#sortordpoint').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        search();
    });
        
    $(".codedown").on('click', function () {
       $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('DESC');
       
        $('#sortordcategory').val('');
        $('#sortordpoint').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
       search();
   });
   
    $(".categoryup").on('click', function () {
        $('#sortordcategory').val('ASC');
        $('#sortordpoint').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        
        $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('');
        search();
    });
    
    $(".categorydown").on('click', function () {
        $('#sortordcategory').val('DESC');
        $('#sortordpoint').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        
         $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('');
        search();
    });
    
    $(".pointup").on('click', function () {
        $('#sortordpoint').val('ASC');
        $('#sortordcategory').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        
         $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('');
        search();
    });
    
    $(".pointdown").on('click', function () {
        $('#sortordpoint').val('DESC');
        $('#sortordcategory').val('');
        $('#sortorddate').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        
         $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('');
        search();
    });
    
    $(".dateup").on('click', function () {
        $('#sortorddate').val('ASC');
        $('#sortordpoint').val('');
        $('#sortordcategory').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        
         $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('');
        search();
    });
    
    $(".datedown").on('click', function () {
        $('#sortorddate').val('DESC');
        $('#sortordpoint').val('');
        $('#sortordcategory').val('');
        $('#sortordbranch').val('');
        $('#sortordrating').val('');
        
         $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('');
        search();
    });
    
    $(".branchup").on('click', function () {
        $('#sortordbranch').val('ASC');
        $('#sortorddate').val('');
        $('#sortordpoint').val('');
        $('#sortordcategory').val('');
        $('#sortordrating').val('');
        search();
    });
    
    $(".branchdown").on('click', function () {
        $('#sortordbranch').val('DESC');
        $('#sortorddate').val('');
        $('#sortordpoint').val('');
        $('#sortordcategory').val('');
        $('#sortordrating').val('');
        
         $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('');
        search();
    });
    $(".ratingup").on('click', function () {
        $('#sortordrating').val('ASC');
        $('#sortordbranch').val('');
        $('#sortorddate').val('');
        $('#sortordpoint').val('');
        $('#sortordcategory').val('');
        
         $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('');
        search();
    });
    
    $(".ratingdown").on('click', function () {
        $('#sortordrating').val('DESC');
        $('#sortordbranch').val('');
        $('#sortorddate').val('');
        $('#sortordpoint').val('');
        $('#sortordcategory').val('');
        
         $('#sortordname').val('');
       $('#sortordjob').val('');
       $('#sortordcode').val('');
        search();
    });

    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {
        var search_key = $('#search_key').val();
        var job_position = $('#job_position').val();
        var searchbycode = $('#searchbycode').val();
        
        var searchbycategory = $('.searchbycategory').val();
        var searchbypoint = $('#searchbypoint').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var searchbybranch = $('.searchbybranch').val();
        var searchbyrating = $('.searchbyrating').val();
        
        var sortordname=$('#sortordname').val();
        var sortordjob =$('#sortordjob').val();
        var sortordcode =$('#sortordcode').val();
        var sortordbranch = $('#sortordbranch').val();
        var sortorddate = $('#sortorddate').val();
        var sortordpoint = $('#sortordpoint').val();
        var sortordcategory = $('#sortordcategory').val();
        var sortordrating = $('#sortordrating').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'checklist_report',
            data: {search_key: search_key, job_position: job_position, searchbycode: searchbycode,searchbycategory:searchbycategory,searchbypoint:searchbypoint,start_date:start_date,end_date:end_date,searchbybranch:searchbybranch,searchbyrating:searchbyrating,sortordname:sortordname,sortordjob:sortordjob,sortordcode:sortordcode,sortordbranch:sortordbranch,sortorddate:sortorddate,sortordpoint:sortordpoint,sortordcategory:sortordcategory,sortordrating:sortordrating,pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                
                if (return_data != '')
                {
                    $('.reportbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.reportbody').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
     $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function(selected) {
            $("#end_date").datepicker("option","minDate", selected)
             search();
            }
        });
        $("#end_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy',
        });
        
    $(function () {

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');
            
            $('#search_key').val('');
            $('#job_position').val('');
            $('#searchbycode').val('');
            $('#sortordrating').val('');
            $('#sortordbranch').val('');
            $('#sortorddate').val('');
            $('#sortordpoint').val('');
            $('#sortordcategory').val('');
            
            $('#page-limit').val(10);
            //search();
            window.location.href = '{{url("checklist/checklist_report")}}';
        });
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
