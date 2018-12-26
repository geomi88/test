@extends('layouts.main')
@section('content')
<script>
    $(window).on('hashchange', function () {
        if (window.location.hash) {
            var page = window.location.hash.replace('#', '');
            if (page == Number.NaN || page <= 0) {
                return false;
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
        
        $('.takePrint').click(function () {

            if (!$("#datee").is(':checked') && !$("#supplierName").is(':checked') && !$("#supplierCode").is(':checked') && !$("#remittanceNo").is(':checked') && !$("#daycount").is(':checked') && !$("#modeofpay").is(':checked') && !$("#amount1").is(':checked')  && !$("#status").is(':checked') && !$("#dateRemitted").is(':checked')) {
                toastr.error('Select required fields to print!');
            } else {
                        win = window.open('', 'Print', 'width=1000,height=1100');

                var strStyle = '<style>.paginationHolder {display:none;}';

                if (!$("#payment_code").is(':checked')) {
                    strStyle += ' .paymentcodeC {display:none;}';
                }
                if (!$("#datee").is(':checked')) {
                    strStyle += ' .dateC {display:none;}';
                }
                if (!$("#dateRemitted").is(':checked')) {
                    strStyle += ' .dateR {display:none;}';
                }
                if (!$("#supplierCode").is(':checked')) {
                    strStyle += ' .clscode {display:none;}';
                }
                if (!$("#supplierName").is(':checked')) {
                    strStyle += ' .nameC {display:none;}';
                }
                if (!$("#modeofpay").is(':checked')) {
                    strStyle += ' .modeC {display:none;}';
                }
                if (!$("#remittanceNo").is(':checked')) {
                    strStyle += ' .remittanceC {display:none;}';
                }
                
                if (!$("#status").is(':checked')) {
                    strStyle += ' .statusC {display:none;}';
                }
                if (!$("#amount1").is(':checked')) {
                    strStyle += ' .amountC {display:none;}';
                }
                
                if (!$("#daycount").is(':checked')) {
                    strStyle += ' .datecountC {display:none;}';
                }
                strStyle += '.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';

                win.document.write(strStyle + '<div style="text-align:center;"><h1>Remittance Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td class="paymentcodeC" style="padding:10px 0;color:#fff;"> Payment Code </td>' +
                                            '<td class="dateC" style="padding:10px 0;color:#fff;"> Date </td>' +
                                            '<td class="dateR" style="padding:10px 0;color:#fff;"> Remitted Date </td>' +
                                            '<td class="clscode" style="padding:10px 0;color:#fff;"> Party Code </td>' +
                                            '<td class="nameC" style="padding:10px 0;color:#fff;"> Party Name </td>' +
                                            '<td class="modeC" style="padding:10px 0;color:#fff;"> Payment Mode </td>' +
                                            '<td class="remittanceC" style="padding:10px 0;color:#fff;"> Remittance No. </td>' +
                                            '<td class="statusC" style="padding:10px 0;color:#fff;"> Status </td>' +
                                            '<td class="amountC" style="padding:10px 0;color:#fff;"> Amount </td>' +
                                            '<td class="datecountC" style="padding:10px 0;color:#fff;"> Day Count </td>' +
                                        '</tr>'+
                                    '</thead>'+ $('.outbox_list')[0].outerHTML +'</table>');
                win.document.close();
                win.print();
                win.close();
                return false;
            }
        });
        
        
    });
    function getData(page) {
        var searchbycode = $('#searchbycode').val();
        var searchbysucode = $('#searchbysucode').val();
        var searchbyname = $('#searchbyname').val();
         var searchbymode = $('#searchbymode').val();
         var searchpaidstatus = $('#searchpaidstatus').val();
        var searchbyremitance = $('#searchbyremitance').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var sortordname = $('#sortordname').val();
        var sortorddate = $('#sortorddate').val();
        var remitteddate_from = $('#remitteddate_from').val();
        var remitteddate_to = $('#remitteddate_to').val();
        
        var pagelimit = $('#page-limit').val();
       $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbysucode:searchbysucode,searchbymode:searchbymode,sortorddate:sortorddate, sortordname:sortordname, created_at_to:created_at_to, created_at_from:created_at_from, remitteddate_from:remitteddate_from,remitteddate_to:remitteddate_to,searchbyremitance:searchbyremitance, searchbyname:searchbyname,searchbycode:searchbycode,searchpaidstatus:searchpaidstatus, pagelimit: pagelimit},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".outbox_list").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('finance')}}">Back</a>
    <form id="pdfgenerator" action="{{ url('finance/remittance_report/exportdata') }}" method="post">
        <header class="pageTitle">
            <h1>Remittance <span>Report</span></h1>
        </header>	
        
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>
        <?php if(isset($customdate)){ ?>
            <div class="pendingPayment">
                <label>Remitted On : <span><?php echo date("d-m-Y", strtotime($customdate)); ?></span></label>
            </div>
        <?php } else{?>
            <div class="pendingPayment">
                <label>Pending payments : <span>{{$pendingPayments}}</span></label>
            </div>
        <?php } ?>
        
        <div class="customClear"></div>
        
        <div class="printChoose">
                <div class="custRow">
                    
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="payment_code" checked="" type="checkbox">
                                <span></span>
                                <em>Payment Code</em>
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
                    
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="dateRemitted" checked="" type="checkbox">
                                <span></span>
                                <em>Remitted Date</em>
                            </label>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="supplierCode" checked="" type="checkbox">
                                <span></span>
                                <em>Party Code</em>
                            </label>
                        </div>
                    </div>
                    
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="supplierName" checked="" type="checkbox">
                                <span></span>
                                <em>Party Name</em>
                            </label>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="modeofpay" checked="" type="checkbox">
                                <span></span>
                                <em>Payment Mode</em>
                            </label>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="remittanceNo" checked="" type="checkbox">
                                <span></span>
                                <em>Remittance No.</em>
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
                                <input id="amount1" checked="" type="checkbox">
                                <span></span>
                                <em>Amount</em>
                            </label>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="daycount" checked="" type="checkbox">
                                <span></span>
                                <em>Day Count</em>
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

        <div class="listHolderType1">
            <div class="custRow">
            </div>
            <div class="listerType1 reportLister"> 

                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <input type="hidden" value="" id="sortordname" name="sortordname">
                <input type="hidden" value="" id="sortorddate" name="sortorddate">
                <input type="hidden" value="" id="sortordcode" name="sortordcode">
                
                
                <div id="postable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>
                                    Payment Code
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp codeup"></a>
                                        <a href="javascript:void(0)" class="btnDown codedown"></a>
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
                                    Remitted Date
                                </td>
                                <td>
                                    Party Code
                                    
                                </td>
                                <td>
                                    Party Name
                                    
                                </td>
                                <td>
                                    Payment Mode
                                </td>
                                
                                <td>
                                    Remittance No.
                                </td>
                                <td>
                                    Status
                                </td>
                                <td class="amountAlign">
                                    Amount
                                </td>
                                <td>
                                    Day Count
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
                                            <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Code" autocomplete="off">
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
                                <td class="filterFields clsdate">
                                    <?php if(isset($customdate)){ ?>
                                        <div class="custCol-6">
                                            <input type="text" id="remitteddate_from"  name="remitteddate_from" value="<?php echo date('d-m-Y',  strtotime($customdate));?>" disabled placeholder="From ">
                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="remitteddate_to" name="remitteddate_to" value="<?php echo date('d-m-Y',  strtotime($customdate));?>" disabled placeholder="To ">
                                        </div>
                                    <?php } else { ?>
                                        <div class="custCol-6">
                                            <input type="text" id="remitteddate_from" name="remitteddate_from" value="" placeholder="From ">
                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="remitteddate_to" name="remitteddate_to" value="" placeholder="To ">
                                        </div>
                                    <?php } ?>
                                </td>
                                <td class="filterFields" style="min-width: 150px;">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbysucode" name="searchbysucode" placeholder="Enter Code" autocomplete="off">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields" style="min-width: 150px;">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbyname" name="searchbyname" placeholder="Enter Name" autocomplete="off">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields" style="min-width: 120px;">
                                    <div class="custCol-12">
                                        <select class="searchbymode" name="searchbymode" id="searchbymode">
                                            <option value="">All</option>
                                            <option value="1">Cheque</option>
                                            <option value="2">Cash</option>
                                            <option value="3">Online</option>
                                           
                                        </select>
                                    </div>
                                </td>
                                
                                <td class="filterFields" style="min-width: 160px;">
                                    <div class="custRow txtbxCheck">
                                        <div class="custCol-8">
                                            <input type="text" id="searchbyremitance" name="searchbyremitance" placeholder="Enter Remittance No." autocomplete="off">
                                        </div>

                                    </div>
                                </td>
                                
                                <td class="filterFields" style="min-width: 120px;">
                                    <div class="custCol-12">
                                        <?php if(isset($customdate)){ ?>
                                        <select class="searchpaidstatus" name="searchpaidstatus" id="searchpaidstatus" disabled>
                                               <option value="">All</option>
                                               <option value="1" selected>Paid</option>
                                               <option value="2">Not Paid</option>

                                           </select>
                                        <?php } else if($type == 'notpaid'){ ?>
                                            <select class="searchpaidstatus" name="searchpaidstatus" id="searchpaidstatus" disabled>
                                               <option value="">All</option>
                                               <option value="1">Paid</option>
                                               <option value="2" selected>Not Paid</option>

                                           </select>
                                        <?php } else { ?>
                                            <select class="searchpaidstatus" name="searchpaidstatus" id="searchpaidstatus">
                                               <option value="">All</option>
                                               <option value="1">Paid</option>
                                               <option value="2">Not Paid</option>

                                           </select>
                                        <?php } ?>
                                       
                                    </div>
                                </td>
                                <td class="filterFields" style="min-width: 130px;"></td>
                                <td class="filterFields" style="min-width: 70px;"></td>
                                <td class="filterFields" style="min-width: 80px;"></td>
                                
                            </tr>

                        </thead>

                        <tbody class="outbox_list" id='outbox_list'>                  
                            @include('finance/remittance_report/remittance_list_results')
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
    
    $('#searchbysucode').bind('keyup', function () {
        search();
    });
    
    $('#searchbyname').bind('keyup', function () {
        search();
    });
    
    $('#searchbymode').on("change", function () {
            search();
    });
    $('#searchpaidstatus').on("change", function () {
            search();
    });
    
    $('#searchbyremitance').bind('keyup', function () {
        search();
    });
    
    $('#created_at_from').on("change", function () {
        search();
    });
    
    $('#created_at_to').on("change", function () {
        search();
    });
    
    $('#remitteddate_from').on("change", function () {
        search();
    });
    
    $('#remitteddate_to').on("change", function () {
        search();
    });
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        $('#sortorddate').val('');
        $('#sortordcode').val('');
        search();
    });
    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        $('#sortorddate').val('');
        $('#sortordcode').val('');
        search();
    });
    
    $(".dateup").on('click', function () {
        $('#sortorddate').val('ASC');
        $('#sortordname').val('');
        $('#sortordcode').val('');
        search();
    });
    $(".datedown").on('click', function () {
        $('#sortorddate').val('DESC');
        $('#sortordname').val('');
        $('#sortordcode').val('');
        search();
    });
    
    $(".codeup").on('click', function () {
        $('#sortordcode').val('ASC');
        $('#sortordname').val('');
        $('#sortorddate').val('');
        search();
    });
    $(".codedown").on('click', function () {
        $('#sortordcode').val('DESC');
        $('#sortordname').val('');
        $('#sortorddate').val('');
        search();
    });

        
    $('#page-limit').on("change", function () {
        search();
    });
    
    function search()
    {
        var searchbycode = $('#searchbycode').val(); 
        var searchbysucode = $('#searchbysucode').val();
        var searchbyname = $('#searchbyname').val(); 
        var searchbymode = $('#searchbymode').val();
        var searchpaidstatus = $('#searchpaidstatus').val();
        var searchbyremitance = $('#searchbyremitance').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var remitteddate_from = $('#remitteddate_from').val();
        var remitteddate_to = $('#remitteddate_to').val();
        var sortordname = $('#sortordname').val();
        var sortorddate = $('#sortorddate').val();
        var sortordcode = $('#sortordcode').val();
        
        
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'remittance_report',
            data: { sortordcode:sortordcode,searchbysucode:searchbysucode,sortorddate:sortorddate, sortordname:sortordname,searchbymode:searchbymode, created_at_to:created_at_to, created_at_from:created_at_from,remitteddate_from:remitteddate_from,remitteddate_to:remitteddate_to, searchbyremitance:searchbyremitance, searchbyname:searchbyname,searchbycode:searchbycode,searchpaidstatus:searchpaidstatus, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.outbox_list').html(return_data);
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
        
        $("#remitteddate_from").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy',
            onSelect: function(selected) {
            $("#remitteddate_to").datepicker("option","minDate", selected)
            search();
            }
        });

        $("#remitteddate_to").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        
        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');
            
            $('#sortordname').val('');
            $('#sortorddate').val('');
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
