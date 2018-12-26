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
            
        if(!$("#slno").is(':checked')&&!$("#title").is(':checked')&&!$("#submittedTo").is(':checked')&&!$("#status").is(':checked')&&!$("#createdDate").is(':checked')&&!$("#description").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#slno").is(':checked')){
                strStyle+=' .suggestion_no {display:none;}';
         }
         if(!$("#title").is(':checked')){
                strStyle+=' .suggestion_title {display:none;}';
         }
         if(!$("#submittedTo").is(':checked')){
               strStyle+=' .suggestion_to {display:none;}';
         }
         if(!$("#status").is(':checked')){
              strStyle+=' .suggestion_status {display:none;}';  
         } 
         if(!$("#createdDate").is(':checked')){
              strStyle+=' .suggestion_date {display:none;}';   
         }
         if(!$("#description").is(':checked')){
               strStyle+=' .suggestion_description {display:none;}';   
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Suggestions</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="suggestion_no"> Sl.No.</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="suggestion_title"> Title</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="suggestion_to"> Submitted To</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="suggestion_status"> Status</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="suggestion_date"> Created Date</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="suggestion_description"> Description </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.planbody')[0].outerHTML +'</table>');   
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
    });
    
    function getData(page) {

         var searchbytitle = $('#searchbytitle').val();
        
        var searchbystatus = $('.searchbystatus').val();
        var searchbysubmited = $('.searchbysubmited').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        
        var sortordtitle=$('#sortordtitle').val();
        var sortordercreated=$('#sortordercreated').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbytitle: searchbytitle, searchbysubmited:searchbysubmited,created_at_from:created_at_from,created_at_to:created_at_to,sortordtitle:sortordtitle,sortordercreated:sortordercreated,searchbystatus:searchbystatus, pagelimit: pagelimit, searchable: searchable},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".planbody").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
   
    <form id="pdfgenerator" action="{{ url('dashboard/suggestion/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1> <span>Suggestions/Complaints</span></h1>
    </header>
    
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-12">
                    <a href="{{ action('Tasks\SuggestionController@create') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
            <div class="customClear"></div>
        </div>
   

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
                                        <input id="submittedTo" checked="" type="checkbox">
                                        <span></span>
                                        <em>Submitted To</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
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
            
            <input type="hidden" value="" id="sortordtitle" name="sortordtitle">
            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
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
                                Submitted To
                                 
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
                            <td>
                                
                            </td>
                           
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">

                            <td>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <input type="text" id="searchbytitle" name="searchbytitle" placeholder="Enter Title">
                                </div>
                            </td>
                            
                            <td>
                                <div class="custCol-12">
                                    <select class="searchbysubmited" name="searchbysubmited">
                                        <option value="">All</option>
                                        <option value="Owner">Owner</option>
                                        <option value="CEO">CEO</option>
                                        <option value="Owner/CEO">Owner/CEO</option>
                                    </select>
                                </div>
                            </td>
                            
                            <td>
                                <div class="custCol-12">
                                    <select class="searchbystatus" name="searchbystatus">
                                        <option value="">All</option>
                                        <option value="1">New</option>
                                        <option value="2">Noted</option>
                                    </select>
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
                            </td>
                            
                            <td>
                            </td>
                            
                        </tr>

                    </thead>

                    <tbody class="planbody" id='planbody'>
                        @include('dashboard/suggestion/result')
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
    
    $('.searchbystatus').on("change", function () {
            search();
    });
    
    $('.searchbysubmited').on("change", function () {
            search();
    });
    
    $(".titleup").on('click', function () {
        $('#sortordtitle').val('ASC');
        $('#sortordercreated').val('');
        search();
    });
    
    $(".titledown").on('click', function () {
        $('#sortordtitle').val('DESC');
        $('#sortordercreated').val('');
        search();
    });
    
    $(".createdup").on('click', function () {
        $('#sortordercreated').val('ASC');
        $('#sortordtitle').val('');
        search();
    });
    
    $(".createddown").on('click', function () {
        $('#sortordercreated').val('DESC');
        $('#sortordtitle').val('');
        search();
    });
    
    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {

        var searchbytitle = $('#searchbytitle').val();
        
        var searchbystatus = $('.searchbystatus').val();
        var searchbysubmited = $('.searchbysubmited').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        
        var sortordtitle=$('#sortordtitle').val();
        var sortordercreated=$('#sortordercreated').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'suggestion',
            data: {searchbytitle: searchbytitle, searchbysubmited:searchbysubmited,created_at_from:created_at_from,created_at_to:created_at_to,sortordtitle:sortordtitle,sortordercreated:sortordercreated,searchbystatus:searchbystatus, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.planbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.planbody').html('<tr><td class="noData" colspan="2">No Records Found</td></tr>');
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
            $('#sortordercreated').val('');
        
            $('#page-limit').val(10);
            //search();
            window.location.href = '{{url("dashboard/suggestion")}}';
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
