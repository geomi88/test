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
            
        if(!$("#empName").is(':checked')&&!$("#jobPosition").is(':checked')&&!$("#empCode").is(':checked')&&!$("#newTask").is(':checked')&&!$("#pendingTask").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#empName").is(':checked')){
                strStyle+=' .emp_name {display:none;}';
         }
         if(!$("#jobPosition").is(':checked')){
                strStyle+=' .emp_job {display:none;}';
         }
         if(!$("#empCode").is(':checked')){
               strStyle+=' .emp_code {display:none;}';
         }
         if(!$("#newTask").is(':checked')){
              strStyle+=' .emp_new {display:none;}';  
         } 
         if(!$("#pendingTask").is(':checked')){
              strStyle+=' .emp_pending {display:none;}';   
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Employee List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="emp_name"> Employee Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="emp_job"> Job Position</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="emp_code"> Employee Code </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="emp_new"> New Tasks </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="emp_pending"> Pending Tasks </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.employee_list')[0].outerHTML +'</table>');
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
        var sortordname=$('#sortordname').val();
        var sortordjob =$('#sortordjob').val();
        var sortordcode =$('#sortordcode').val();
        var pagelimit = $('#page-limit').val();
        
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {search_key: search_key, job_position: job_position, searchbycode: searchbycode,sortordname:sortordname,sortordjob:sortordjob,sortordcode:sortordcode, pagelimit: pagelimit},

        })
        .done(function (data)
        {
            console.log(data);

            $(".employee_list").empty().html(data);
            location.hash = page;
        })
        .fail(function (jqXHR, ajaxOptions, thrownError)
        {
            alert('No response from server');
        });
    }



</script>

<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('dashboard/view_todo/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Employee <span>List</span></h1>
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
                                        <input id="empName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Employee Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="jobPosition" checked="" type="checkbox">
                                        <span></span>
                                        <em>Job Position</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="empCode" checked="" type="checkbox">
                                        <span></span>
                                        <em>Employee Code</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="newTask" checked="" type="checkbox">
                                        <span></span>
                                        <em>New Tasks</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="pendingTask" checked="" type="checkbox">
                                        <span></span>
                                        <em>Pending Tasks</em>
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
            <input type="hidden" value="" id="sortordname" name="sortordname">
            <input type="hidden" value="" id="sortordjob" name="sortordjob">
            <input type="hidden" value="" id="sortordcode" name="sortordcode">
            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
            <div id="postable">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

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
                                Employee Code
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp codeup"></a>
                                    <a href="javascript:void(0)" class="btnDown codedown"></a>
                                </div>
                            </td>

                            <td>
                                New<br>Tasks
                            </td>
                            <td>
                                Pending<br>Tasks
                            </td>
                            <td>
                                Action
                            </td>
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">
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
                                    <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Employee Code">
                                </div>
                            </td>

                            <td>
                            </td>
                            
                            <td>
                            </td>
                            
                            <td>
                            </td>
                        </tr>

                    </thead>

                    <tbody class="employee_list" id='employee_list'>
                        @include('dashboard/view_todo/emp_result')
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
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        search();
    });
    
    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        $('#sortordjob').val('');
        $('#sortordcode').val('');
        search();
    });
    
    $(".jobup").on('click', function () {
        $('#sortordname').val('');
        $('#sortordjob').val('ASC');
        $('#sortordcode').val('');
        search();
    });
    
    $(".jobdown").on('click', function () {
        $('#sortordname').val('');
        $('#sortordjob').val('DESC');
        $('#sortordcode').val('');
        search();
    });
    
    $(".codeup").on('click', function () {
        $('#sortordname').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('ASC');
        search();
    });
    
    $(".codedown").on('click', function () {
        $('#sortordname').val('');
        $('#sortordjob').val('');
        $('#sortordcode').val('DESC');
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
        var sortordname=$('#sortordname').val();
        var sortordjob =$('#sortordjob').val();
        var sortordcode =$('#sortordcode').val();
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'view_todo',
            data: {search_key: search_key, job_position: job_position, searchbycode: searchbycode,sortordname:sortordname,sortordjob:sortordjob,sortordcode:sortordcode, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.employee_list').html('');
                    $('.employee_list').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.employee_list').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }

    $('#reset').click(function () {
//        $(':input')
//                .not(':button, :submit, :reset')
//                .val('')
//                .removeAttr('checked')
//                .removeAttr('selected');
//        $('#search_key').val('');
//        $('#job_position').val('');
//        $('#searchbycode').val('');
//        $('#page-limit').val(10);
//        search();
            window.location.href = '{{url("dashboard/view_todo")}}';
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