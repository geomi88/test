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

        $('.print').click(function () {
            var pageTitle = 'Page Title',
                stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
                win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);
                win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>'+
                                '<div style="text-align:center;"><h1>Task List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> Sl.No.</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Title</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Start Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> End Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Status </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Created Date </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Assigned By </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Description </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.taskbody')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
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
        
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbytitle: searchbytitle, startdatefrom: startdatefrom, enddatefrom: enddatefrom, startdateto: startdateto, enddateto: enddateto,searchbystatus:searchbystatus,created_at_from:created_at_from,created_at_to:created_at_to,sortordtitle: sortordtitle,sortorderstart:sortorderstart,sortorderend:sortorderend,sortordercreated:sortordercreated,pagelimit: pagelimit, searchable: searchable},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".taskbody").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
   
    <header class="pageTitle">
        <h1>Training <span>List</span></h1>
    </header>

    
    <div class="fieldGroup" id="fieldSet1">
        <div class="customClear"></div>
    </div>

    <div class="listHolderType1">
        <div class="listerType1 reportLister"> 
            
                        
            <div id="tblregion">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

                            <td>
                                Sl.No.
                            </td>
                            <td>
                                Title
                                
                            </td>
                            
                            <td>
                                Start Date
                                 
                            </td>
                            <td>
                                End Date
                                 
                            </td>
                            
                            <td>
                                Created Date
                                
                            </td>
                            
                            
                            <td>
                                Description
                            </td>
                            
                            <td>
                            </td>
                        </tr>
                    </thead>


                    <tbody class="taskbody" id='taskbody'>
                        @include('training/training_result')
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
        var pagelimit = $('#page-limit').val();
        
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'training_list',
            data: {searchbytitle: searchbytitle, startdatefrom: startdatefrom, enddatefrom: enddatefrom, startdateto: startdateto, enddateto: enddateto,created_at_from:created_at_from,created_at_to:created_at_to,sortorderstart: sortorderstart,sortorderend:sortorderend,sortordtitle:sortordtitle,sortordercreated:sortordercreated,searchbystatus:searchbystatus, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.taskbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.taskbody').html('<tr><td class="noData" colspan="2">No Records Found</td></tr>');
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

            $('#page-limit').val(10);
            search();
        });
        
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
