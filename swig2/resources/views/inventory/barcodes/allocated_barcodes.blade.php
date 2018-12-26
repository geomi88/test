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
            
        if(!$("#slno").is(':checked')&&!$("#allocatedDate").is(':checked')&&!$("#barcodeNum").is(':checked')&&!$("#productNum").is(':checked')&&!$("#productCode").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#slno").is(':checked')){
                strStyle+=' .alloc_slno {display:none;}';
         }
         if(!$("#allocatedDate").is(':checked')){
                strStyle+=' .alloc_date {display:none;}';
         }
         if(!$("#barcodeNum").is(':checked')){
               strStyle+=' .alloc_bar_num {display:none;}';
         }
         if(!$("#productNum").is(':checked')){
              strStyle+=' .alloc_prod_num {display:none;}';  
         } 
         if(!$("#productCode").is(':checked')){
              strStyle+=' .alloc_prod_code {display:none;}';   
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Allocated Barcode List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                    '<tr class="headingHolder">' +
                    '<td style="padding:10px 0;color:#fff;" class="alloc_slno"> Sl.No.</td>' +
                    '<td style="padding:10px 0;color:#fff;" class="alloc_date"> Allocated Date</td>' +
                    '<td style="padding:10px 0;color:#fff;" class="alloc_bar_num"> Barcode Number</td>' +
                    '<td style="padding:10px 0;color:#fff;" class="alloc_prod_num"> Product Name</td>' +
                    '<td style="padding:10px 0;color:#fff;" class="alloc_prod_code"> Product Code</td>' +
                    '</tr>' +
                    '</thead>' + $('.barcodebody')[0].outerHTML + '</table>'); 
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
    });

function getData(page) {

        
        var pagelimit = $('#page-limit').val();
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {pagelimit: pagelimit},
                    
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".barcodebody").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('inventory/barcode/exportallocatedbarcodes') }}" method="post">
        <header class="pageTitle">
            <h1>Allocated <span>Barcodes</span></h1>
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
                                        <em>Sl No.</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="allocatedDate" checked="" type="checkbox">
                                        <span></span>
                                        <em>Allocated Date</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="barcodeNum" checked="" type="checkbox">
                                        <span></span>
                                        <em>Barcode Number</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="productNum" checked="" type="checkbox">
                                        <span></span>
                                        <em>Product Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="productCode" checked="" type="checkbox">
                                        <span></span>
                                        <em>Product Code</em>
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
        
        <div class="customClear"></div>

        <div class="listHolderType1">
            <div class="listerType1 reportLister"> 
                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <div id="tblregion">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">

                                <td>
                                    Sl.No.
                                </td>
                                <td>
                                    Allocated Date
                                </td>
                                <td>
                                    Barcode Number
                                </td>
                                <td>
                                    Product Name
                                </td>
                                <td>
                                    Product Code
                                </td>
                                <td>

                                </td>
                            </tr>
                        </thead>
                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">


                                <td></td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <input type="text" id="allocated_date" name="allocated_date" value="">
                                        </div>


                                    </div>                                        
                                </td>
                                <td class="">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Barcode Number">

                                    </div>
                                </td>

                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbyproductname" name="searchbyproductname" placeholder="Enter Product Name">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbyproductcode" name="searchbyproductcode" placeholder="Enter Product Code">
                                        </div>
                                    </div>
                                </td>

                                

                                <td>
                                </td>

                            </tr>

                        </thead>
                        <tbody class="barcodebody" id='barcodebody'>
                            @include('inventory/barcodes/allocated_barcodes_result')

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

    $('#allocated_date').on("change", function () {
        if ($('#allocated_date').val() !== '')
        {

            search();
        }
    });
    $('#searchbyproductname').on("keyup", function () {
        search();
    });
    $('#searchbyproductcode').on("keyup", function () {
        search();
    });
    $('#searchbycode').bind('keyup', function () {
        search();
    });
    $('#page-limit').on("change", function () {
        search();
    });
    $(function () {
        $('.commonLoaderV1').hide();
        $("#allocated_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');


            $('#allocated_date').val('');
            $('#searchbycode').val('');
            $('#searchbyproductname').val('');
            $('#searchbyproductcode').val('');


            //search();
            window.location.href = '{{url("inventory/barcode/allocated_barcodes")}}';
        });
    });
    function search()
    {

        var pagelimit = $('#page-limit').val();
        var allocated_date = $('#allocated_date').val();
        var searchbycode = $('#searchbycode').val();
        var searchbyproductname = $('#searchbyproductname').val();
        var searchbyproductcode = $('#searchbyproductcode').val();

        $.ajax({
            type: 'POST',
            url: 'allocated_barcodes',
            data: {pagelimit: pagelimit,allocated_date : allocated_date,searchbycode : searchbycode,searchbyproductname : searchbyproductname,searchbyproductcode : searchbyproductcode},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.barcodebody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.barcodebody').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }

    
    function funExportData(strType)
    {
        if (strType == "PDF") {
            $('#excelorpdf').val('PDF');
        } else {
            $('#excelorpdf').val('Excel');
        }

        document.getElementById("pdfgenerator").submit();
    }

</script>

@endsection
