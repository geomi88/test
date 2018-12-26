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
            
        if(!$("#slno").is(':checked')&&!$("#date").is(':checked')&&!$("#barcodeNum").is(':checked')&&!$("#createdBy").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#slno").is(':checked')){
                strStyle+=' .avail_no {display:none;}';
         }
         if(!$("#date").is(':checked')){
                strStyle+=' .avail_date {display:none;}';
         }
         if(!$("#barcodeNum").is(':checked')){
               strStyle+=' .avail_code {display:none;}';
         }
         if(!$("#createdBy").is(':checked')){
              strStyle+=' .avail_by {display:none;}';  
         } 
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Barcode List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                    '<tr class="headingHolder">' +
                    '<td style="padding:10px 0;color:#fff;" class="avail_no"> Sl.No.</td>' +
                    '<td style="padding:10px 0;color:#fff;" class="avail_date"> Date</td>' +
                    '<td style="padding:10px 0;color:#fff;" class="avail_code"> Barcode Number</td>' +
                    '<td style="padding:10px 0;color:#fff;" class="avail_by"> Created BY</td>' +
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
    <form id="pdfgenerator" action="{{ url('inventory/barcode/exportavailablebarcodes') }}" method="post">
        <header class="pageTitle">
            <h1>Available <span>Barcodes</span></h1>
        </header>
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-12">
                    <a href="{{ action('Inventory\BarcodeController@add') }}" id="btnNew" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
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
                                        <em>Sl No.</em>
                                    </label>
                                </div>
                            </div>

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
                                        <input id="createdBy" checked="" type="checkbox">
                                        <span></span>
                                        <em>Created By</em>
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
                                    Date
                                </td>
                                <td>
                                    Barcode Number
                                </td>
                                <td>
                                    Created By
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
                                            <input type="text" id="created_date" name="created_date" value="">
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
                                            <input type="text" id="searchbycreatedby" name="searchbycreatedby" placeholder="Enter Name">
                                        </div>
                                    </div>
                                </td>

                                

                                <td>
                                </td>

                            </tr>

                        </thead>
                        <tbody class="barcodebody" id='barcodebody'>
                            @include('inventory/barcodes/barcodes_result')

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
    $('#created_date').on("change", function () {
        if ($('#created_date').val() !== '')
        {

            search();
        }
    });
    

    $('#searchbycreatedby').on("keyup", function () {
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
        $("#created_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');


            $('#created_date').val('');
            $('#searchbycode').val('');
            $('#searchbycreatedby').val('');


            //search();
            window.location.href = '{{url("inventory/barcode/available_barcodes")}}';
        });
    });
    function search()
    {

        var pagelimit = $('#page-limit').val();
        var created_date = $('#created_date').val();
        var searchbycode = $('#searchbycode').val();
        var searchbycreatedby = $('#searchbycreatedby').val();

        $.ajax({
            type: 'POST',
            url: 'available_barcodes',
            data: {pagelimit: pagelimit,created_date : created_date,searchbycode : searchbycode,searchbycreatedby : searchbycreatedby},
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
