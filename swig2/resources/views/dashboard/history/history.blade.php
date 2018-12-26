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
            
        if(!$("#slno").is(':checked')&&!$("#title").is(':checked')&&!$("#startDate").is(':checked')&&!$("#endDate").is(':checked')&&!$("#status").is(':checked')&&!$("#createdDate").is(':checked')&&!$("#description").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#slno").is(':checked')){
                strStyle+=' .history_slno {display:none;}';
         }
         if(!$("#title").is(':checked')){
                strStyle+=' .history_title {display:none;}';
         }
         if(!$("#startDate").is(':checked')){
               strStyle+=' .history_start {display:none;}';
         }
         if(!$("#endDate").is(':checked')){
              strStyle+=' .history_end {display:none;}';  
         } 
         if(!$("#status").is(':checked')){
              strStyle+=' .history_status {display:none;}';   
         }
         if(!$("#createdDate").is(':checked')){
               strStyle+=' .history_date {display:none;}';   
         }
         if(!$("#description").is(':checked')){
             strStyle+=' .history_description {display:none;}';     
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Plans</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="history_slno"> Sl.No.</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="history_title"> Title</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="history_start"> Start Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="history_end"> End Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="history_status"> Status </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="history_date"> Created Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="history_description"> Description </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.historybody')[0].outerHTML +'</table>'); 
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
    });
    
    function getData(page) {

        var searchbytitle = $('#searchbytitle').val();
        var startdatefrom = $('#start_date_from').val();
        var enddatefrom = $('#end_date_from').val();
        var startdateto = $('#start_date_to').val();
        var enddateto = $('#end_date_to').val();
        var searchbystatus = $('.searchbystatus').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var sortorderstart = $('#sortorderstart').val();
        var sortorderend=$('#sortorderend').val();
        var sortordtitle = $('#sortordtitle').val();
        var sortordercreated = $('#sortordercreated').val();
        var empid=$('#empid').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbytitle: searchbytitle, startdatefrom: startdatefrom, enddatefrom: enddatefrom, startdateto: startdateto, enddateto: enddateto,searchbystatus:searchbystatus,created_at_from:created_at_from,created_at_to:created_at_to,sortordtitle: sortordtitle,sortorderstart:sortorderstart,sortorderend:sortorderend,sortordercreated:sortordercreated,empid:empid,pagelimit: pagelimit, searchable: searchable},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".historybody").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <?php if($backbutton!=''){ ?>
        <a class="btnBack" href="{{ URL::to('dashboard/assign_task/single_employee', ['id' => Crypt::encrypt($empid)]) }}">Back</a>
    <?php } ?>
    <form id="pdfgenerator" action="{{ url('tasks/history/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1> To Do <span>History</span></h1>
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
                                        <input id="slno" checked="" type="checkbox">
                                        <span></span>
                                        <em>Sl No</em>
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

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="startDate" checked="" type="checkbox">
                                        <span></span>
                                        <em>Start Date</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="endDate" checked="" type="checkbox">
                                        <span></span>
                                        <em>End Date</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="status" checked="" type="checkbox">
                                        <span></span>
                                        <em>Status</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="createdDate" checked="" type="checkbox">
                                        <span></span>
                                        <em>Created Date</em>
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

            <input type="hidden" value="{{$empid}}" id="empid" name="empid">
            <input type="hidden" value="" id="sortordtitle" name="sortordtitle">
            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
            <input type="hidden" value="" id="sortorderstart" name="sortorderstart">            
            <input type="hidden" value="" id="sortorderend" name="sortorderend">            
            <input type="hidden" value="" id="sortordercreated" name="sortordercreated">            
            <div id="tblregion">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

                            <td>
                                Sl.No.
                            </td>
                            <td>
                                Title
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp titleup"></a>
                                    <a href="javascript:void(0)" class="btnDown titledown"></a>
                                </div>
                            </td>
                            
                            <td>
                                Start Date
                                 <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp startup"></a>
                                    <a href="javascript:void(0)" class="btnDown startdown"></a>
                                </div>
                            </td>
                            <td>
                                End Date
                                 <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp endup"></a>
                                    <a href="javascript:void(0)" class="btnDown enddown"></a>
                                </div>
                            </td>
                            <td>
                                Status
                            </td>
                            <td>
                                Created Date
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp createdup"></a>
                                    <a href="javascript:void(0)" class="btnDown createddown"></a>
                                </div>
                            </td>
                            <td>
                                Description
                            </td>
                            
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">


                            <td></td>
                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbytitle" name="searchbytitle" placeholder="Enter Title">
                                    </div>
                                </div>
                            </td>

                            <td class="filterFields clsdate">
                               <div class="custRow">
                                    <div class="custCol-6">
                                        <input type="text" id="start_date_from" name="start_date_from" value="" placeholder="From ">
                                    </div>
                                    <div class="custCol-6">
                                        <input type="text" id="end_date_from" name="end_date_from" value="" placeholder="To ">
                                    </div>

                                </div>
                            </td>
                            
                            <td class="filterFields clsdate">
                                <div class="custRow">
                                    <div class="custCol-6">
                                        <input type="text" id="start_date_to" name="start_date_to" value="" placeholder="From ">
                                    </div>
                                    <div class="custCol-6">
                                        <input type="text" id="end_date_to" name="end_date_to" value="" placeholder="To ">
                                    </div>

                                </div>
                            </td>
                            <td>
                                <div class="custCol-12">
                                    <select class="searchbystatus" name="searchbystatus">
                                        <option value="">All</option>
                                        <option value="-1">Deleted</option>
                                        <option value="3">Completed</option>
                                    </select>
                                </div>
                            </td>
                            
                            <td class="filterFields clsdate">
                                <div class="custRow">
                                    <div class="custCol-6">
                                        <input type="text" id="created_at_from" name="created_at_from" value="" placeholder="From ">
                                    </div>
                                    <div class="custCol-6">
                                        <input type="text" id="created_at_to" name="created_at_to" value="" placeholder="To ">
                                    </div>

                                </div>
                            </td>
                            
                            <td class="filterFields">
                            </td>

                        </tr>

                    </thead>

                    <tbody class="historybody" id='historybody'>
                        @include('dashboard/history/history_result')
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

    $('#searchbytitle').bind('keyup', function () {
        search();
    });
    
    $('#end_date_from').on("change", function () {
            search();
    });
    
    $('#end_date_to').on("change", function () {
            search();
    });
    
    $('#created_at_to').on("change", function () {
            search();
    });
    
    $('.searchbystatus').on("change", function () {
            search();
    });
    
    $(".titleup").on('click', function () {
        $('#sortordtitle').val('ASC');
        $('#sortorderstart').val('');
        $('#sortordercreated').val('');
        $('#sortorderend').val('');
        search();
    });
    
    $(".titledown").on('click', function () {
        $('#sortordtitle').val('DESC');
        $('#sortorderstart').val('');
        $('#sortordercreated').val('');
        $('#sortorderend').val('');
        search();
    });
    
    $(".startup").on('click', function () {
        $('#sortorderstart').val('ASC');
        $('#sortordtitle').val('');
        $('#sortordercreated').val('');
        $('#sortorderend').val('');
        search();
    });
    
    $(".startdown").on('click', function () {
        $('#sortorderstart').val('DESC');
        $('#sortordtitle').val('');
        $('#sortordercreated').val('');
        $('#sortorderend').val('');
        search();
    });
    
    $(".endup").on('click', function () {
        $('#sortorderend').val('ASC');
        $('#sortorderstart').val('');
        $('#sortordtitle').val('');
        $('#sortordercreated').val('');
        search();
    });
    
    $(".enddown").on('click', function () {
        $('#sortorderend').val('DESC');
        $('#sortorderstart').val('');
        $('#sortordtitle').val('');
        $('#sortordercreated').val('');
        search();
    });
    
    $(".createdup").on('click', function () {
        $('#sortordercreated').val('ASC');
        $('#sortorderend').val('');
        $('#sortorderstart').val('');
        $('#sortordtitle').val('');
        search();
    });
    
    $(".createddown").on('click', function () {
        $('#sortordercreated').val('DESC');
        $('#sortorderend').val('');
        $('#sortorderstart').val('');
        $('#sortordtitle').val('');
        search();
    });
    
    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {

        var searchbytitle = $('#searchbytitle').val();
        var startdatefrom = $('#start_date_from').val();
        var enddatefrom = $('#end_date_from').val();
        var startdateto = $('#start_date_to').val();
        var enddateto = $('#end_date_to').val();
        var searchbystatus = $('.searchbystatus').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var sortorderstart = $('#sortorderstart').val();
        var sortorderend=$('#sortorderend').val();
        var sortordtitle=$('#sortordtitle').val();
        var sortordercreated=$('#sortordercreated').val();
        var empid=$('#empid').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'history',
            data: {searchbytitle: searchbytitle, startdatefrom: startdatefrom, enddatefrom: enddatefrom, startdateto: startdateto, enddateto: enddateto,created_at_from:created_at_from,created_at_to:created_at_to,sortorderstart: sortorderstart,sortorderend:sortorderend,sortordtitle:sortordtitle,sortordercreated:sortordercreated,searchbystatus:searchbystatus,empid:empid, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.historybody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.historybody').html('<tr><td class="noData" colspan="2">No Records Found</td></tr>');
                }
            }
        });
    }
    
    $(function () {

//        $('#reset').click(function () {
//            $(':input')
//                    .not(':button, :submit, :reset, :hidden')
//                    .val('')
//                    .removeAttr('checked')
//                    .removeAttr('selected');
//
//            $('#page-limit').val(10);
//            $('#sortordercreated').val('');
//            $('#sortorderend').val('');
//            $('#sortorderstart').val('');
//            $('#sortordtitle').val('');
//            search();
//        });
        
        $("#start_date_from").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function(selected) {
            $("#end_date_from").datepicker("option","minDate", selected)
             search();
            }
        });
        $("#end_date_from").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy',
        });
        
         $("#start_date_to").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy',
            onSelect: function(selected) {
            $("#end_date_to").datepicker("option","minDate", selected)
            search();
            }
        });
        
        $("#end_date_to").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
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
        
    });

    $('#reset').click(function () {
           window.location.href = '{{url("tasks/history")}}';
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
