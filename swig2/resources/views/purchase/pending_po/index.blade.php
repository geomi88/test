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
                                '<div style="text-align:center;"><h1>Pending PO List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> Order Code </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Requisition Code </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Supplier </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Date </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Total Amount </td>' +
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
        var searchbyrqcode = $('#searchbyrqcode').val();
        var searchbysupplier = $('#searchbysupplier').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var sortordpocode = $('#sortordpocode').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: { searchbyordcode:searchbyordcode,searchbyrqcode:searchbyrqcode,searchbysupplier: searchbysupplier,created_at_from:created_at_from,created_at_to:created_at_to,sortordpocode:sortordpocode, pagelimit: pagelimit},
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
    <a class="btnBack" href="{{ URL::to('purchase')}}">Back</a>
    <form id="pdfgenerator" action="#" method="post">
        <header class="pageTitle">
            <h1>Pending PO List</h1>
        </header>	
        
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <!--<a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>-->

        <div class="listHolderType1">
            <div class="custRow">
            </div>
            <div class="listerType1 reportLister"> 

                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <input type="hidden" value="" id="sortordpocode" name="sortordpocode">
               
                
                <div id="postable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>
                                    Order Code
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp pocodeup"></a>
                                        <a href="javascript:void(0)" class="btnDown pocodedown"></a>
                                    </div>
                                </td>
                                <td>
                                    Requisition Code
                                   
                                </td>
                                
                                <td>
                                    Supplier
                                   
                                </td>

                                <td>
                                    Date
                                   
                                </td>

                                <td class="amountAlign">
                                    Total Amount
                                </td>
                             
                                <td>
                                    
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
                               
                               
                                   <td class="filterFields" style="min-width: 130px;">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbyrqcode" name="searchbyrqcode" placeholder="Enter Code">
                                        </div>
                                    </div>
                                </td>
                                
                                 
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbysupplier" name="searchbysupplier" placeholder="Enter name">
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
                                
                                <td class="filterFields" style="min-width: 120px;"></td>
                                <td class="filterFields" style="min-width: 150px;"></td>
                            </tr>

                        </thead>

                        <tbody class="order_list" id='order_list'>                  
                            @include('purchase/pending_po/results')
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
    
    $('#searchbyrqcode').bind('keyup', function () {
        search();
    });
    
    $('#searchbysupplier').bind('keyup', function () {
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
       
        search();
    });
    
    $(".pocodedown").on('click', function () {
        $('#sortordpocode').val('DESC');
       
        search();
    });
    
    
    $('#page-limit').on("change", function () {
        search();
    });
    
    function search()
    {
        var searchbyordcode = $('#searchbyordcode').val();
        var searchbyrqcode = $('#searchbyrqcode').val();
        var searchbysupplier = $('#searchbysupplier').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var sortordpocode = $('#sortordpocode').val();
        
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'pending_po',
            data: { searchbyordcode:searchbyordcode,searchbyrqcode:searchbyrqcode,searchbysupplier: searchbysupplier,created_at_from:created_at_from,created_at_to:created_at_to,sortordpocode:sortordpocode, pagelimit: pagelimit},
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
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');
            
            $('#sortordpocode').val('');
           
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
