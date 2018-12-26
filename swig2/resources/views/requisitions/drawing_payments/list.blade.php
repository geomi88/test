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
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });

        $('.print').click(function () {
            var pageTitle = 'Page Title',
                stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
                win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);
                win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;} .right {float: right;margin-right:20px;}</style>'+
                                '<div style="text-align:center;"><h1>Owner Drawings Payment Advices</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> Payment Code </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Paid By </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Paid To </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Amount </td>' + 
                                            '<td style="padding:10px 0;color:#fff;"> Status </td>' +
                                        '</tr>'+
                                    '</thead>'+ $('.inbox_list')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });
        
    });
    function getData(page) {
       var searchbycode = $('#searchbycode').val();
        var searchpaidto = $('#searchpaidto').val();
        var searchpaidby = $('#searchpaidby').val();
        var status = $('#status').val();
        
        var sortpaidto = $('#sortpaidto').val();
        var sortpaidby = $('#sortpaidby').val();
        var sortordcode = $('#sortordcode').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                  //  data: { searchbycode:searchbycode,searchbyname: searchbyname,created_at_from:created_at_from,created_at_to:created_at_to,sortordcode:sortordcode, sortordtitle: sortordtitle,sortorddate:sortorddate, pagelimit: pagelimit},
                    data: { searchbycode:searchbycode,searchpaidto: searchpaidto,searchpaidby:searchpaidby,sortordcode:sortordcode, sortpaidto: sortpaidto,sortpaidby:sortpaidby,status:status, pagelimit: pagelimit},
           
   // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".inbox_list").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('requisitions')}}">Back</a>
    <form id="pdfgenerator" action="{{ url('requisitions/drawing_requsition_payment_advice/exportdata_paymentadvicelist') }}" method="post">
        <header class="pageTitle">
            <h1>Owner Drawings Payment <span>Advices</span></h1>
        </header>	
        
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>-->
        <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>

        <div class="listHolderType1">
            <div class="custRow">
            </div>
            <div class="listerType1 reportLister"> 

                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <input type="hidden" value="" id="sortordcode" name="sortordcode">
                <input type="hidden" value="" id="sortpaidby" name="sortpaidby">
                <input type="hidden" value="" id="sortpaidto" name="sortpaidto">
                <input type="hidden" value="" id="sortpaidto" name="sortpaidto">
                
                <div id="postable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>
                                    Payment Advice Code
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp codeup"></a>
                                        <a href="javascript:void(0)" class="btnDown codedown"></a>
                                    </div>
                                </td>
                                <td>
                                    Paid By
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp paidbyup"></a>
                                        <a href="javascript:void(0)" class="btnDown paidbydown"></a>
                                    </div>
                                </td>
                                <td>
                                    Paid To
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp paidtoup"></a>
                                        <a href="javascript:void(0)" class="btnDown paidtodown"></a>
                                    </div>
                                </td>
                                
                                <td class="amountAlign">
                                    Amount
                                </td> 
                                <td>
                                    Status
                                </td>
                                <td>
                                    Action
                                </td>
                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">
                                <td class="filterFields" style="min-width: 150px;">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Code">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchpaidby" name="searchpaidby" placeholder="Enter Name">
                                        </div>
                                    </div>
                                </td>
                               
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchpaidto" name="searchpaidto" placeholder="Enter Name">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields" style="min-width: 120px;"></td>
                                
                                <td class="filterFields" style="min-width: 120px;">
                                    <select id="status" name="status">
                                        <option value="">Choose Status</option>
                                        <option value="1">Pending</option>
                                        <option value="2">Approved</option>
                                        <option value="3">Rejected</option>
                                    </select>
                                </td>
                                <td></td>
                            </tr>

                        </thead>

                        <tbody class="inbox_list" id='inbox_list'>                  
                            @include('requisitions/drawing_payments/result')
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
    
    $('#searchpaidby').bind('keyup', function () {
        search();
    });
    $('#searchpaidto').bind('keyup', function () {
        search();
    });
    
    $('#status').on("change", function () {
        search();
    });
    
    
    $(".codeup").on('click', function () {
        $('#sortordcode').val('ASC');
        $('#sortpaidto').val('');
        $('#sortpaidby').val('');
        search();
    });
    $(".codedown").on('click', function () {
        $('#sortordcode').val('DESC');
        $('#sortpaidby').val('');
        $('#sortpaidto').val('');
        search();
    });
    
    $(".paidbyup").on('click', function () {
        $('#sortpaidby').val('ASC');
        $('#sortpaidto').val('');
        $('#sortordcode').val('');
        search();
    });
    $(".paidbydown").on('click', function () {
        $('#sortpaidby').val('DESC');
        $('#sortpaidto').val('');
        $('#sortordcode').val('');
        search();
    });
    
    $(".paidtoup").on('click', function () {
        $('#sortpaidto').val('ASC');
        $('#sortpaidby').val('');
        $('#sortordcode').val('');
        search();
    });
    $(".paidtodown").on('click', function () {
        $('#sortpaidto').val('DESC');
        $('#sortpaidby').val('');
        $('#sortordcode').val('');
        search();
    });
    
    $('#page-limit').on("change", function () {
        search();
    });
    
    function search()
    {
        var searchbycode = $('#searchbycode').val();
        var searchpaidto = $('#searchpaidto').val();
        var searchpaidby = $('#searchpaidby').val();
        var status = $('#status').val();
        
        var sortpaidto = $('#sortpaidto').val();
        var sortpaidby = $('#sortpaidby').val();
        var sortordcode = $('#sortordcode').val();
        
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'list',
            data: { searchbycode:searchbycode,searchpaidto: searchpaidto,searchpaidby:searchpaidby,sortordcode:sortordcode, sortpaidto: sortpaidto,sortpaidby:sortpaidby,status:status, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.inbox_list').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                
            }
        });



    }
    $(function () {
        $('.commonLoaderV1').hide();
        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

                $('#sortpaidto').val('');
                $('#sortpaidby').val('');
                $('#sortordcode').val('');
                $("#page-limit").val(10);
            search();
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
