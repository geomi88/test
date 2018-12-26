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
            
        if(!$("#date").is(':checked')&&!$("#depositBy").is(':checked')&&!$("#amt").is(':checked')&&!$("#bankName").is(':checked')&&!$("#topCashier").is(':checked')&&!$("#topCashierBank").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#date").is(':checked')){
                strStyle+=' .sup_date {display:none;}';
         }
         if(!$("#depositBy").is(':checked')){
                strStyle+=' .sup_deposit {display:none;}';
         }
         if(!$("#amt").is(':checked')){
               strStyle+=' .sup_amount {display:none;}';
         }
         if(!$("#bankName").is(':checked')){
              strStyle+=' .sup_bank {display:none;}';  
         } 
         if(!$("#topCashier").is(':checked')){
              strStyle+=' .sup_cashier {display:none;}';   
         }
         if(!$("#topCashierBank").is(':checked')){
               strStyle+=' .sup_cashier_bank {display:none;}';   
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Supervisor Cash Deposit Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">' +
                    '<td style="padding:10px 0;color:#fff;" class="sup_date"> Date</td>' +
                    '<td style="padding:10px 0;color:#fff;" class="sup_deposit"> Deposited By</td>' +
                    '<td style="padding:10px 0;color:#fff;" class="sup_amount"> Amount </td>' +
                    '<td style="padding:10px 0;color:#fff;" class="sup_bank"> Bank Name </td>' +
                    '<td style="padding:10px 0;color:#fff;" class="sup_cashier"> Top Cashier </td>' +
                    '<td style="padding:10px 0;color:#fff;" class="sup_cashier_bank"> Top Cashier/Bank </td>' +
                    '</tr>' +
                    '</thead>' + $('.pos')[0].outerHTML + '</table>');
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });

        $('.saveDoc').click(function () {

//            svepdf();


        });
    });

    function getData(page) {
        var depositbysort = $('#depositbysort').val();
        var deposittosort = $('#deposittosort').val();
        var cashsorting = $('#cashsorting').val();
        var bsort = $('#bsort').val();

        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var depositedbysearch = $('#depositedbysearch').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var bank = $('#bank').val();
        var depositedtosearch = $('#depositedtosearch').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {bank: bank, depositedtosearch: depositedtosearch, depositedbysearch: depositedbysearch, depositbysort: depositbysort,deposittosort:deposittosort,cashsorting:cashsorting,bsort:bsort, corder: corder, camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    $(".pos").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>

<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('mis/supervisor_cash_deposit_report/exporttopdf') }}" method="post">
        <header class="pageTitle">
            <h1>Supervisor Cash Deposit <span>Report</span></h1>
        </header>	
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>
        
                             <div class="printChoose">
                        <div class="custRow">
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
                                        <input id="depositBy" checked="" type="checkbox">
                                        <span></span>
                                        <em>Deposited By</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="amt" checked="" type="checkbox">
                                        <span></span>
                                        <em>Amount</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="bankName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Bank Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="topCashier" checked="" type="checkbox">
                                        <span></span>
                                        <em>Top Cashier</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="topCashierBank" checked="" type="checkbox">
                                        <span></span>
                                        <em>Top Cashier/Bank</em>
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

                <input type="hidden" value="" id="depositbysort" name="depositbysort">
                <input type="hidden" value="" id="deposittosort" name="deposittosort">
                <input type="hidden" value="" id="cashsorting" name="cashsorting">
                <input type="hidden" value="" id="bsort" name="bsort">      

                <div id="postable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">

                                <td>
                                    Start Date-End Date
                                </td>
                                <td>
                                    Deposited By
                                    <div class="sort">
                                        <a href="#" class="btnUp depobyup"></a>
                                        <a href="#" class="btnDown depobydown"></a>
                                    </div>
                                </td>
                                <td>
                                    Amount
                                    <div class="sort">
                                        <a href="#" class="btnUp cashup"></a>
                                        <a href="#" class="btnDown casdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Bank Name
                                    <div class="sort">
                                        <a href="#" class="btnUp bup"></a>
                                        <a href="#" class="btnDown bdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Top Cashier
                                    <div class="sort">
                                        <a href="#" class="btnUp depotoup"></a>
                                        <a href="#" class="btnDown depotodown"></a>
                                    </div>
                                </td>

                                <td>
                                    Top Cashier/Bank
                                </td>

                                <td>
                                    Action
                                </td>
                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">

                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <input type="text" id="start_date" name="start_date" value="">
                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="end_date" name="end_date" value="">
                                        </div>

                                    </div>
                                </td>

                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="depositedbysearch" name="depositedbysearch">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="corder" name="corder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="camount" name="camount">
                                        </div>

                                    </div>
                                </td>
                                <td>
                                    <div class="custCol-12">
                                        <select id="bank" name="bank">
                                            <option value="">All</option>
                                            @foreach ($bank_names as $bank_name)
                                            <option value="{{$bank_name->id}}">{{$bank_name->bank_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="depositedtosearch" name="depositedtosearch">
                                        </div>
                                    </div>
                                </td>

                                <td></td>
                                <td></td>
                            </tr>

                        </thead>

                        <tbody class="pos" id='pos'>

                            @include('mis/supervisor_cash_deposit_report/supervisor_cash_deposit_result')

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


    $(".depobyup").on('click', function () {
        $('#depositbysort').val('ASC');
        search();
    });
    $(".depobydown").on('click', function () {
        $('#depositbysort').val('DESC');
        search();
    });
    $(".depotoup").on('click', function () {
        $('#deposittosort').val('ASC');
        search3();
    });
    $(".depotodown").on('click', function () {
        $('#deposittosort').val('DESC');
        search3();
    });

    $(".bup").on('click', function () {
        $('#bsort').val('ASC');
        search2();
    });
    $(".bdown").on('click', function () {
        $('#bsort').val('DESC');
        search2();
    });

    $(".casdown").on('click', function () {
        $('#cashsorting').val('ASC');
        search1();
    });
    $(".cashup").on('click', function () {
        $('#cashsorting').val('DESC');
        search1();
    });

    $('#bank').on("change", function () {
        search();
    });

    $('#depositedbysearch').bind('keyup', function () {
        search();
    });
    $('#depositedtosearch').bind('keyup', function () {
        search();
    });

    $('.corder').on("change", function () {
         var camount = $('#camount').val();
        if ($('.corder').val() !== '' &&  $.isNumeric(camount))
        {
            search();
        }
    });

    $('#page-limit').on("change", function () {
        search();
    });

    $('#start_date').on("change", function () {
        if ($('#start_date').val() !== '')
        {
            search();
        }
    });
    $('#end_date').on("change", function () {
        if ($('#end_date').val() !== '')
        {
            search();
        }
    });
   
    
     $('#camount').bind('keyup', function () {
        var corder = $('.corder').val();
         var camount = $('#camount').val();
         if(corder!=""  && $.isNumeric(camount)){
            search();
            }
       
    });

    function search()
    {

        var depositbysort = $('#depositbysort').val();
        $('#deposittosort').val('');
        $('#cashsorting').val('');
        $('#bsort').val('');

        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var depositedbysearch = $('#depositedbysearch').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var bank = $('#bank').val();
        var depositedtosearch = $('#depositedtosearch').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'supervisor_cash_deposit_report',
            data: {bank: bank, depositedtosearch: depositedtosearch, depositedbysearch: depositedbysearch, depositbysort: depositbysort, corder: corder, camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                   
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.pos').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }

    function search1()
    {

        $('#depositbysort').val('');
        $('#deposittosort').val('');
        $('#bsort').val('');

        var cashsorting = $('#cashsorting').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var bank = $('#bank').val();
        var depositedbysearch = $('#depositedbysearch').val();
        var depositedtosearch = $('#depositedtosearch').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'supervisor_cash_deposit_report',
            data: {bank: bank, depositedtosearch: depositedtosearch, depositedbysearch: depositedbysearch, cashsorting: cashsorting, corder: corder, camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.pos').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }

    function search2()
    {

        $('#depositbysort').val('');
        $('#deposittosort').val('');
        $('#cashsorting').val('');

        var bsort = $('#bsort').val();

        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var bank = $('#bank').val();
        var depositedbysearch = $('#depositedbysearch').val();
        var depositedtosearch = $('#depositedtosearch').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'supervisor_cash_deposit_report',
            data: {bank: bank, depositedtosearch: depositedtosearch, depositedbysearch: depositedbysearch, bsort: bsort, corder: corder, camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.pos').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }



    function search3()
    {

        var deposittosort = $('#deposittosort').val();
        $('#depositbysort').val('');
        $('#cashsorting').val('');

        var depositedbysearch = $('#depositedbysearch').val();
        var depositedtosearch = $('#depositedtosearch').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var bank = $('#bank').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'supervisor_cash_deposit_report',
            data: {bank: bank, depositedtosearch: depositedtosearch, depositedbysearch: depositedbysearch, deposittosort: deposittosort, corder: corder, camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.pos').html('<p class="noData">No Records Found</p>');
                }
            }
        });

    }

    $(function () {
        $('.commonLoaderV1').hide();
        $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $("#end_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        }).datepicker("setDate", new Date());

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

            $('#depositbysort').val('');
            $('#cashsorting').val('');
            $('#deposittosort').val('');
            $('#bsort').val('');

            $('#search').val('');
            $('#depositedtosearch').val('');
            $('#camount').val('');
            $('.bank').val('');
            $('.corder').val('');
            $('#start_date').val('');
            $('#end_date').val('');

            $("#end_date").datepicker({
                changeMonth: true,
                changeYear: true, dateFormat: 'dd-mm-yy'
            }).datepicker("setDate", new Date());
            //search();
            window.location.href = '{{url("mis/supervisor_cash_deposit_report")}}';
        });
    });

    function savetopdf()
    {

        document.getElementById("pdfgenerator").submit();


    }
</script>



@endsection