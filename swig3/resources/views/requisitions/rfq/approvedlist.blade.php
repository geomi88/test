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
            
                win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);
                win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;} .right {float: right;margin-right:20px;}</style>'+
                                '<div style="text-align:center;"><h1>RFQ List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> RFQ Code </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Title </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Date </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Supplier Code </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Supplier Name </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Total Amount </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Status </td>' +
                                        '</tr>'+
                                    '</thead>'+ $('.order_list')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });
        
    });
    
    function getData(page) {
        var searchbyordcode = $('#searchbyordcode').val();
        var searchbytitle = $('#searchbytitle').val();
        var searchbysupcode = $('#searchbysupcode').val();
        var searchbysupname = $('#searchbysupname').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var sortordpocode = $('#sortordpocode').val();
        var sortorddate = $('#sortorddate').val();
        var sortordsupcode = $('#sortordsupcode').val();
        
        var pagelimit = $('#page-limit').val();
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: { searchbyordcode:searchbyordcode,searchbytitle: searchbytitle,searchbysupcode:searchbysupcode,searchbysupname:searchbysupname,created_at_from:created_at_from,created_at_to:created_at_to,sortordpocode:sortordpocode,sortorddate:sortorddate,sortordsupcode:sortordsupcode, pagelimit: pagelimit},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".order_list").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('rfq')}}">Back</a>
    <form id="pdfgenerator" action="">
        <header class="pageTitle">
            <h1>Approved RFQ List</h1>
        </header>	
        
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>-->
        <!--<a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>-->

        <div class="listHolderType1">
            <div class="custRow">
            </div>
            <div class="listerType1 reportLister"> 

                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <input type="hidden" value="" id="sortordpocode" name="sortordpocode">
                <input type="hidden" value="" id="sortorddate" name="sortorddate">
                <input type="hidden" value="" id="sortordsupcode" name="sortordsupcode">
                <input type="hidden" value="" id="sortordsupname" name="sortordsupname">
                
                <div id="postable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>
                                    RFQ Code
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp pocodeup"></a>
                                        <a href="javascript:void(0)" class="btnDown pocodedown"></a>
                                    </div>
                                </td>
                               
                                <td>
                                    Title
                                    
                                </td>
                                <td>
                                    Date
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp dateup"></a>
                                        <a href="javascript:void(0)" class="btnDown datedown"></a>
                                    </div>
                                </td>
                                <td>
                                    Supplier Code
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp supcodeup"></a>
                                        <a href="javascript:void(0)" class="btnDown supcodedown"></a>
                                    </div>
                                </td>
                                <td>
                                    Supplier Name
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp supnameup"></a>
                                        <a href="javascript:void(0)" class="btnDown supnamedown"></a>
                                    </div>
                                </td>
                              
                                <td class="amountAlign">
                                    Total Amount
                                </td>
                                
                                <td >
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
                                            <input type="text" id="searchbyordcode" name="searchbyordcode" placeholder="Enter Code">
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="filterFields" >
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbytitle" name="searchbytitle" placeholder="Enter title">
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
                                <td class="filterFields" style="min-width: 150px;">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbysupcode" name="searchbysupcode" placeholder="Enter Code">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields" style="min-width: 150px;">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbysupname" name="searchbysupname" placeholder="Enter Code">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields" style="min-width: 120px;"></td>
                                <td></td>
                                <td></td>
                            </tr>

                        </thead>

                        <tbody class="order_list" id='order_list'>                  
                            @include('requisitions/rfq/approvedresult')
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

    $('#searchbyordcode').bind('keyup', function () {
        search();
    });
    
    $('#searchbytitle').bind('keyup', function () {
        search();
    });
    
    $('#searchbysupcode').bind('keyup', function () {
        search();
    });
    
    $('#searchbysupname').bind('keyup', function () {
        search();
    });
    
    $('#created_at_from').on("change", function () {
        search();
    });
    
    $('#created_at_to').on("change", function () {
        search();
    });
    
    $(".pocodeup").on('click', function () {
        $('#sortordpocode').val('ASC');
        $('#sortorddate').val('');
        $('#sortordsupcode').val('');
        $('#sortordsupname').val('');
        search();
    });
    
    $(".dateup").on('click', function () {
        $('#sortorddate').val('ASC');
        $('#sortordpocode').val('');
        $('#sortordsupcode').val('');
        $('#sortordsupname').val('');
        search();
    });
    
    $(".datedown").on('click', function () {
        $('#sortorddate').val('DESC');
        $('#sortordpocode').val('');
        $('#sortordsupcode').val('');
        $('#sortordsupname').val('');
        search();
    });
    
    $(".supcodeup").on('click', function () {
        $('#sortordsupcode').val('ASC');
        $('#sortorddate').val('');
        $('#sortordpocode').val('');
        $('#sortordsupname').val('');
        search();
    });
    
    $(".supcodedown").on('click', function () {
        $('#sortordsupcode').val('DESC');
        $('#sortorddate').val('');
        $('#sortordpocode').val('');
        $('#sortordsupname').val('');
        search();
    });
    
    $(".supnameup").on('click', function () {
        $('#sortordsupname').val('ASC');
        $('#sortordsupcode').val('');
        $('#sortorddate').val('');
        $('#sortordpocode').val('');
        search();
    });
    
    $(".supnamedown").on('click', function () {
        $('#sortordsupname').val('DESC');
        $('#sortordsupcode').val('');
        $('#sortorddate').val('');
        $('#sortordpocode').val('');
        search();
    });
    
    $('#page-limit').on("change", function () {
        search();
    });
    
    function search()
    {
        var searchbyordcode = $('#searchbyordcode').val();
        var searchbytitle = $('#searchbytitle').val();
        var searchbysupcode = $('#searchbysupcode').val();
        var searchbysupname = $('#searchbysupname').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var sortordpocode = $('#sortordpocode').val();
        var sortorddate = $('#sortorddate').val();
        var sortordsupcode = $('#sortordsupcode').val();
        
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'approvedlist',
            data: { searchbyordcode:searchbyordcode,searchbytitle: searchbytitle,searchbysupcode:searchbysupcode,searchbysupname:searchbysupname,created_at_from:created_at_from,created_at_to:created_at_to,sortordpocode:sortordpocode,sortorddate:sortorddate,sortordsupcode:sortordsupcode, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.order_list').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                
            }
        });

    }
    $(function () {
        $('.commonLoaderV1').hide();
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
        
        $('#reset').click(function () {
            window.location.href = '{{url("requisitions/approvedlist")}}';
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
