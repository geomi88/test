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

            if (!$("#sl_no").is(':checked') && !$("#supervisorName").is(':checked') && !$("#branchName").is(':checked') && !$("#shiftName").is(':checked') && !$("#cashCollection").is(':checked') && !$("#openingAmount").is(':checked') && !$("#branchSale").is(':checked') && !$("#creditSale").is(':checked') && !$("#bankSale").is(':checked') && !$("#difference").is(':checked') && !$("#meals").is(':checked') && !$("#reason").is(':checked') && !$("#cashierName").is(':checked') && !$("#editedby").is(':checked')) {
                toastr.error('Select required fields to print!');
            } else {
                var pageTitle = 'Page Title',
                        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
                        win = window.open('', 'Print', 'width=500,height=300');

                var strStyle = '<style>.paginationHolder {display:none;}';

                if (!$("#sl_no").is(':checked')) {
                    strStyle += ' .sl_no {display:none;}';
                }

                if (!$("#editedby").is(':checked')) {
                    strStyle += ' .editedby {display:none;}';
                }

                if (!$("#date").is(':checked')) {
                    strStyle += ' .pos_date {display:none;}';
                }
                if (!$("#supervisorName").is(':checked')) {
                    strStyle += ' .pos_name {display:none;}';
                }
                if (!$("#branchName").is(':checked')) {
                    strStyle += ' .pos_branch {display:none;}';
                }
                if (!$("#shiftName").is(':checked')) {
                    strStyle += ' .pos_jobshift {display:none;}';
                }
                if (!$("#cashCollection").is(':checked')) {
                    strStyle += ' .pos_cash_collection {display:none;}';
                }
                if (!$("#openingAmount").is(':checked')) {
                    strStyle += ' .pos_opening_amount {display:none;}';
                }
                if (!$("#branchSale").is(':checked')) {
                    strStyle += ' .pos_total_sale {display:none;}';
                }
                if (!$("#creditSale").is(':checked')) {
                    strStyle += ' .pos_credit_sale {display:none;}';
                }
                if (!$("#bankSale").is(':checked')) {
                    strStyle += ' .pos_bank_sale {display:none;}';
                }
                if (!$("#difference").is(':checked')) {
                    strStyle += ' .pos_difference {display:none;}';
                }
                if (!$("#meals").is(':checked')) {
                    strStyle += ' .pos_meals {display:none;}';
                }
                if (!$("#reason").is(':checked')) {
                    strStyle += ' .pos_reason {display:none;}';
                }
                if (!$("#cashierName").is(':checked')) {
                    strStyle += ' .pos_cashier {display:none;}';
                }
                if (!$("#pos_cash_difference").is(':checked')) {
                    strStyle += ' .pos_cash_difference {display:none;}';
                }
                if (!$("#pos_bank_difference").is(':checked')) {
                    strStyle += ' .pos_bank_difference {display:none;}';
                }
                strStyle += '.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';

                win.document.write(strStyle + '<div style="text-align:center;"><h1>POS Supervisor Sales Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                        '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                        '<tr class="headingHolder">' +
                        '<td style="padding:10px 0;color:#fff;" class="sl_no"> Sl No</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_date"> Date</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_branch"> Branch Name</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_name"> Supervisor Name </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_cashier"> Cashier Name </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_jobshift"> Shift </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_opening_amount"> Opening Amount </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_total_sale"> Total Sale </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_cash_collection"> Cash Collection </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_credit_sale"> Credit Sale </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_bank_sale"> Bank Sale </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_cash_difference"> Cash Difference </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_bank_difference"> Bank Difference </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_difference"> Net Difference </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_meals"> Meals </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="pos_reason"> Reason </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="editedby"> Edited By </td>' +
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
        var sorting = $('#sortsimp').val();
        var cashsort = $('#cashsort').val();
        var opensort = $('#opensort').val();
        var totsort = $('#totsort').val();
        var creditsort = $('#creditsort').val();
        var banksort = $('#banksort').val();
        var diffsort = $('#diffsort').val();
        var cashiersort = $('#cashiersort').val();
        var mealsort = $('#mealsort').val();



        var searchkey = $('#search').val();
        var sup_searchkey = $('#sup_search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var corder = $('.corder').val();
        var oorder = $('.oorder').val();
        var torder = $('.torder').val();
        var crorder = $('.crorder').val();
        var border = $('.border').val();
        var dorder = $('.dorder').val();
        var morder = $('.morder').val();


        var camount = $('#camount').val();
        var oamount = $('#oamount').val();
        var tamount = $('#tamount').val();
        var cramount = $('#cramount').val();
        var bamount = $('#bamount').val();
        var damount = $('#damount').val();
        var mamount = $('#mamount').val();



        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';


        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {sorting: sorting, cashsort: cashsort, opensort: opensort, totsort: totsort, mealsort: mealsort, diffsort: diffsort, banksort: banksort, creditsort: creditsort, cashiersort: cashiersort, searchkey: searchkey, branch: branch, sup_searchkey: sup_searchkey, branch: branch, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable, shift: shift, corder: corder, crorder: crorder, oorder: oorder, torder: torder, border: border, dorder: dorder, morder: morder, camount: camount, oamount: oamount, tamount: tamount, cramount: cramount, bamount: bamount, damount: damount, mamount: mamount},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

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
    <form id="pdfgenerator" action="{{ url('mis/pos_sales/supervisorreports/exporttopdf') }}" method="post">
        <header class="pageTitle">
            <h1>Branch Sales<span> Report</span></h1>
        </header>	
        @php
        if($custom_frm_date != ''){
        $sp_id = 'load_it';
        }
        else
        {
        $sp_id = '';
        }
        @endphp
        <a class="btnAction refresh bgRed {{ $sp_id }}" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>
        <!--<a class="btnAction savedirectDoc bgBlue" href="{{ route('export',['download'=>'pdf','searchkey'=>'']) }}">Save</a>-->
        <!--<a class="btnAction savedirectDoc bgBlue"  id="btnExport" onclick="savetopdf()">Save</a>-->

        <div class="printChoose">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="sl_no" checked="" type="checkbox">
                            <span></span>
                            <em>Sl No</em>
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
                            <input id="branchName" checked="" type="checkbox">
                            <span></span>
                            <em>Branch Name</em>
                        </label>
                    </div>
                </div>
            </div>

            <div class="custRow">

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="supervisorName" checked="" type="checkbox">
                            <span></span>
                            <em>Supervisor Name</em>
                        </label>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="shiftName" checked="" type="checkbox">
                            <span></span>
                            <em>Shift Name</em>
                        </label>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="cashCollection" checked="" type="checkbox">
                            <span></span>
                            <em>Cash Collection</em>
                        </label>
                    </div>
                </div>

            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="openingAmount" checked="" type="checkbox">
                            <span></span>
                            <em>Opening Amount</em>
                        </label>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="branchSale" checked="" type="checkbox">
                            <span></span>
                            <em>Total Branch Sale</em>
                        </label>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="creditSale" checked="" type="checkbox">
                            <span></span>
                            <em>Credit Sale</em>
                        </label>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="bankSale" checked="" type="checkbox">
                            <span></span>
                            <em>Bank Sale</em>
                        </label>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="difference" checked="" type="checkbox">
                            <span></span>
                            <em>Difference</em>
                        </label>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="pos_cash_difference" checked="" type="checkbox">
                            <span></span>
                            <em>Cash Difference</em>
                        </label>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="meals" checked="" type="checkbox">
                            <span></span>
                            <em>Meal Consumption</em>
                        </label>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="reason" checked="" type="checkbox">
                            <span></span>
                            <em>Reason</em>
                        </label>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="cashierName" checked="" type="checkbox">
                            <span></span>
                            <em>Cashier Name</em>
                        </label>
                    </div>
                </div> 
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="editedby" checked="" type="checkbox">
                            <span></span>
                            <em>Edited By</em>
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
            <div class="custRow">
                <div class="custCol-12">
                    <h3>Branch Name</h3>
                </div>
            </div>
            <div class="listerType1 reportLister"> 

                <input type="hidden" value="" id="sortsimp" name="sortsimp">
                <input type="hidden" value="" id="brsort" name="brsort">
                <input type="hidden" value="" id="cashsort" name="cashsort"> 
                <input type="hidden" value="" id="opensort" name="opensort">
                <input type="hidden" value="" id="totsort" name="totsort">
                <input type="hidden" value="" id="creditsort" name="creditsort">
                <input type="hidden" value="" id="banksort" name="banksort">
                <input type="hidden" value="" id="diffsort" name="diffsort">
                <input type="hidden" value="" id="mealsort" name="mealsort">
                <input type="hidden" value="" id="cashiersort" name="cashiersort">
                <input type="hidden" value="" id="edited" name="edited">
                <input type="hidden" value="" id="cashdiffsort" name="cashdiffsort">
                <input type="hidden" value="" id="bankdiffsort" name="bankdiffsort">


                <div id="postable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>S.no</td>
                                <td>
                                    Start Date-End Date
                                </td>
                                <td>
                                    Branch Name

                                </td>
                                <td>
                                    Supervisor Name
                                    <div class="sort">
                                        <a href="#" class="btnUp sortup"></a>
                                        <a href="#" class="btnDown sortdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Cashier Name
                                    <div class="sort">
                                        <a href="#" class="btnUp sup"></a>
                                        <a href="#" class="btnDown sdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Shift Name
                                </td>
                                <td>
                                    Opening Amount
                                    <div class="sort">
                                        <a href="#" class="btnUp oup"></a>
                                        <a href="#" class="btnDown odown"></a>
                                    </div>
                                </td>

                                <td>
                                    Total Branch Sale
                                    <div class="sort">
                                        <a href="#" class="btnUp tup"></a>
                                        <a href="#" class="btnDown tdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Cash Collection
                                    <div class="sort">
                                        <a href="#" class="btnUp cashup"></a>
                                        <a href="#" class="btnDown cashdown"></a>
                                    </div>
                                </td>

                                <td>
                                    Credit Sale
                                    <div class="sort">
                                        <a href="#" class="btnUp crup"></a>
                                        <a href="#" class="btnDown crdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Bank Sale
                                    <div class="sort">
                                        <a href="#" class="btnUp bup"></a>
                                        <a href="#" class="btnDown bdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Cash Difference
                                    <div class="sort">
                                        <a href="#" class="btnUp cdup"></a>
                                        <a href="#" class="btnDown cddown"></a>
                                    </div>
                                </td>

                                <td>
                                    Bank Difference
                                    <div class="sort">
                                        <a href="#" class="btnUp bdup"></a>
                                        <a href="#" class="btnDown bddown"></a>
                                    </div>
                                </td>
                                <td>
                                    Net Difference
                                    <div class="sort">
                                        <a href="#" class="btnUp dup"></a>
                                        <a href="#" class="btnDown ddown"></a>
                                    </div>
                                </td>
                                <td>
                                    Meal Consumption
                                    <div class="sort">
                                        <a href="#" class="btnUp mealup"></a>
                                        <a href="#" class="btnDown mealdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Reason
                                </td>

                                <td>
                                    Edited By
                                    <div class="sort">
                                        <a href="#" class="btnUp editup"></a>
                                        <a href="#" class="btnDown editdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Action
                                </td>
                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">
                                @php
                                if($custom_frm_date != ''){
                                $f_disable = 'disabled';
                                }
                                else{
                                $f_disable = '';
                                }
                                if($custom_to_date != ''){
                                $t_disable = 'disabled';
                                }
                                else{
                                $t_disable = '';
                                }
                                if($custom_branch != ''){
                                $b_disable = 'disabled';
                                }
                                else{
                                $b_disable = '';
                                }
                                @endphp

                                <td></td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <input type="text" id="start_date"  name="start_date" value="{{ $custom_frm_date }}" {{ $f_disable }}>
                                            <input type="hidden" value = "{{ $custom_frm_date }}" name="from_date_hidden" id="from_date_hidden">
                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="end_date" name="end_date" value="{{ $custom_to_date }}" {{ $t_disable }}>
                                            <input type="hidden" value = "{{ $custom_to_date }}" name="to_date_hidden" id="to_date_hidden">
                                        </div>

                                    </div>
                                </td>
                                <td>
                                    <div class="custCol-12">
                                        <select id="branch" class="branch" name="branch" {{ $b_disable }}>
                                            <option value="">All</option>
                                            @foreach ($branch_names as $branch_name)
                                            @php
                                            if($custom_branch == $branch_name->branch_id){
                                            $b_sel = 'selected';
                                            }
                                            else{
                                            $b_sel = '';
                                            }
                                            @endphp
                                            <option value="{{$branch_name->branch_id}}" {{ $b_sel }}>{{$branch_name->branch_code}}-{{$branch_name->branch_name}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" value="{{ $custom_branch }}" id="hidden_br_id" name="hidden_br_id">
                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="search" name="searchkey">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="sup_search" name="sup_searchkey">
                                        </div>

                                    </div>
                                </td>
                                <td class="">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <select class="shift" name="shift">
                                                <option value="">All</option>
                                                @foreach ($shift_names as $shift_name)
                                                <option value="{{$shift_name->jobshift_id}}">{{$shift_name->jobshift_name}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="oorder" name="oorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="oamount" name="oamount">
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="torder" name="torder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="tamount" name="tamount">
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
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="crorder" name="crorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="cramount" name="cramount">
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="border" name="border">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="bamount" name="bamount">
                                        </div>

                                    </div>
                                </td>

                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="cdorder" name="cdorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="cdamount" name="cdamount">
                                        </div>

                                    </div>
                                </td>

                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="bdorder" name="bdorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="bdamount" name="bdamount">
                                        </div>

                                    </div>
                                </td>

                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="dorder" name="dorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="damount" name="damount">
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="morder" name="morder" id="morder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" class="mamount" id="mamount" name="mamount">
                                        </div>

                                    </div>
                                </td>

                                <td></td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="edit_search" name="edit_searchkey">
                                        </div>

                                    </div>
                                </td>
                                <td></td>
                            </tr>

                        </thead>

                        <tbody class="pos" id='pos'>
                            @include('mis/pos_sales/supervisor_report_result')

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

    $(".sortup").on('click', function () {
        $('#sortsimp').val('ASC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');

        search();
    });
    $(".sortdown").on('click', function () {
        $('#sortsimp').val('DESC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        search();
    });

    $(".cashup").on('click', function () {
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#cashsort').val('ASC');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".cashdown").on('click', function () {
        $('#cashsort').val('DESC');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');

        search();
    });
    $(".oup").on('click', function () {
        $('#opensort').val('ASC');
        $('#cashsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');

        search();
    });
    $(".odown").on('click', function () {
        $('#opensort').val('DESC');

        $('#cashsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".tup").on('click', function () {
        $('#totsort').val('ASC');

        $('#cashsort').val('');
        $('#opensort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');

        search();
    });
    $(".tdown").on('click', function () {
        $('#totsort').val('DESC');

        $('#cashsort').val('');
        $('#opensort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".crup").on('click', function () {
        $('#creditsort').val('ASC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".crdown").on('click', function () {
        $('#creditsort').val('DESC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".bup").on('click', function () {
        $('#banksort').val('ASC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".bdown").on('click', function () {
        $('#banksort').val('DESC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });

    $(".dup").on('click', function () {
        $('#diffsort').val('ASC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });

    $(".ddown").on('click', function () {
        $('#diffsort').val('DESC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".cdup").on('click', function () {
        $('#diffsort').val('');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#cashdiffsort').val('ASC');
        $('#bankdiffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });

    $(".cddown").on('click', function () {
        $('#diffsort').val('DESC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#cashdiffsort').val('DESC');
        $('#bankdiffsort').val('');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".bdup").on('click', function () {
        $('#diffsort').val('ASC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('ASC');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });

    $(".bddown").on('click', function () {
        $('#diffsort').val('DESC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('DESC');
        $('#cashiersort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });



    $(".mealup").on('click', function () {
        $('#mealsort').val('ASC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".mealdown").on('click', function () {
        $('#mealsort').val('DESC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashiersort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".sup").on('click', function () {
        $('#cashiersort').val('ASC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });
    $(".sdown").on('click', function () {
        $('#cashiersort').val('DESC');
        $('#cashsort').val('');
        $('#opensort').val('');
        $('#totsort').val('');
        $('#creditsort').val('');
        $('#banksort').val('');
        $('#diffsort').val('');
        $('#cashdiffsort').val('');
        $('#bankdiffsort').val('');
        $('#mealsort').val('');
        $('#sortsimp').val('');
        search();
    });

    $('#search').bind('keyup', function () {
        search();
    });
    $('#sup_search').bind('keyup', function () {
        search();
    });
    $('.branch').on("change", function () {
        $("#hidden_br_id").val($("#branch").val());
        search();
    });
    $('.shift').on("change", function () {
        search();
    });

    $(".editup").on('click', function () {
        $('#edited').val('ASC');
        search();
    });
    $(".editdown").on('click', function () {
        $('#edited').val('DESC');
        search();
    });
    $('#page-limit').on("change", function () {
        // alert('dasd');
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


    $('.corder').on("change", function () {
        var camount = $('#camount').val();
        if ($('.corder').val() !== '' && $.isNumeric(camount))
        {

            search();
        }
    });

    $('#camount').bind('keyup', function () {
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        if (corder != "" && $.isNumeric(camount)) {
            search();
        }

    });
    $('.oorder').on("change", function () {
        var oamount = $('#oamount').val();
        if ($('.oorder').val() !== '' && $.isNumeric(oamount))
        {

            search();
        }
    });

    $('#oamount').bind('keyup', function () {
        var oorder = $('.oorder').val();
        var oamount = $('#oamount').val();
        if (oorder != "" && $.isNumeric(oamount)) {
            search();
        }

    });


    $('.torder').on("change", function () {
        var tamount = $('#tamount').val();
        if ($('.torder').val() !== '' && $.isNumeric(tamount))
        {

            search();
        }
    });

    $('#tamount').bind('keyup', function () {
        var torder = $('.torder').val();
        var tamount = $('#tamount').val();
        if (torder != "" && $.isNumeric(tamount)) {
            search();
        }

    });


    $('.crorder').on("change", function () {
        var cramount = $('#cramount').val();
        if ($('.crorder').val() !== '' && $.isNumeric(cramount))
        {

            search();
        }
    });

    $('#cramount').bind('keyup', function () {
        var crorder = $('.crorder').val();
        var cramount = $('#cramount').val();
        if (crorder != "" && $.isNumeric(cramount)) {
            search();
        }

    });

    $('.border').on("change", function () {
        var bamount = $('#bamount').val();
        if ($('.border').val() !== '' && $.isNumeric(bamount))
        {

            search();
        }
    });

    $('#bamount').bind('keyup', function () {
        var border = $('.border').val();
        var bamount = $('#bamount').val();
        if (border != "" && $.isNumeric(bamount)) {
            search();
        }

    });

    $('.dorder').on("change", function () {
        var damount = $('#damount').val();
        if ($('.dorder').val() !== '' && $.isNumeric(damount))
        {

            search();
        }
    });

    $('#damount').bind('keyup', function () {
        var dorder = $('.dorder').val();
        var damount = $('#damount').val();
        if (dorder != "" && $.isNumeric(damount)) {
            search();
        }

    });
    $('.cdorder').on("change", function () {
        var cdamount = $('#cdamount').val();
        if ($('.cdorder').val() !== '' && $.isNumeric(cdamount))
        {

            search();
        }
    });

    $('#cdamount').bind('keyup', function () {
        var cdorder = $('.cdorder').val();
        var cdamount = $('#cdamount').val();
        if (cdorder != "" && $.isNumeric(cdamount)) {
            search();
        }

    });

    $('.bdorder').on("change", function () {
        var bdamount = $('#bdamount').val();
        if ($('.bdorder').val() !== '' && $.isNumeric(bdamount))
        {

            search();
        }
    });

    $('#bdamount').bind('keyup', function () {
        var bdorder = $('.bdorder').val();
        var bdamount = $('#bdamount').val();
        if (bdorder != "" && $.isNumeric(bdamount)) {
            search();
        }

    });


    $('.morder').on("change", function () {
        var mamount = $('#mamount').val();
        if ($('.morder').val() !== '' && $.isNumeric(mamount))
        {

            search();
        }
    });
    $('#mamount').bind('keyup', function () {
        var morder = $('.morder').val();
        var mamount = $('#mamount').val();
        if (morder != "" && $.isNumeric(mamount)) {
            search();
        }
    });


    $('.aorder').on("change", function () {
        var aamount = $('#aamount').val();
        if ($('.aorder').val() !== '' && $.isNumeric(aamount))
        {

            search();
        }
    });

    $('#aamount').bind('keyup', function () {
        var aorder = $('.aorder').val();
        var aamount = $('#aamount').val();
        if (aorder != "" && $.isNumeric(aamount)) {
            search();
        }

    });

    $('#edit_search').bind('keyup', function () {
        search();
    });

    function search()
    {
        // alert('asd');
        var sorting = $('#sortsimp').val();
        var cashsort = $('#cashsort').val();
        var opensort = $('#opensort').val();
        var totsort = $('#totsort').val();
        var creditsort = $('#creditsort').val();
        var banksort = $('#banksort').val();
        var diffsort = $('#diffsort').val();
        var cashdiffsort = $('#cashdiffsort').val();
        var bankdiffsort = $('#bankdiffsort').val();
        var cashiersort = $('#cashiersort').val();
        var mealsort = $('#mealsort').val();


        var searchkey = $('#search').val();
        var sup_searchkey = $('#sup_search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var corder = $('.corder').val();
        var oorder = $('.oorder').val();
        var torder = $('.torder').val();
        var crorder = $('.crorder').val();
        var border = $('.border').val();
        var dorder = $('.dorder').val();
        var cdorder = $('.cdorder').val();
        var bdorder = $('.bdorder').val();
        var morder = $('.morder').val();


        var camount = $('#camount').val();
        var oamount = $('#oamount').val();
        var tamount = $('#tamount').val();
        var cramount = $('#cramount').val();
        var bamount = $('#bamount').val();
        var damount = $('#damount').val();
        var cdamount = $('#cdamount').val();
        var bdamount = $('#bdamount').val();
        var mamount = $('#mamount').val();



        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        var editedsort = $('#edited').val();
        var edit_searchkey = $('#edit_search').val();


        $.ajax({
            type: 'POST',
            url: 'supervisorreports',
            data: {editedsort: editedsort, edit_searchkey: edit_searchkey, sorting: sorting, cashsort: cashsort, opensort: opensort, totsort: totsort, mealsort: mealsort, diffsort: diffsort, banksort: banksort, creditsort: creditsort, cashiersort: cashiersort, searchkey: searchkey, branch: branch, sup_searchkey: sup_searchkey, branch: branch, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable, shift: shift, corder: corder, crorder: crorder, oorder: oorder, torder: torder, border: border, dorder: dorder, cdorder: cdorder, bdorder: bdorder, morder: morder, camount: camount, oamount: oamount, tamount: tamount, cramount: cramount, bamount: bamount, damount: damount, cdamount: cdamount, bdamount: bdamount, mamount: mamount, cashdiffsort: cashdiffsort, bankdiffsort: bankdiffsort},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                $(".commonLoaderV1").hide();
                console.log(return_data);
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                } else
                {
                    $(".commonLoaderV1").hide();
                    $('.pos').html('<p class="noData">No Records Found</p>');
                }
            }
        });



    }

    function search1()
    {
        //alert('asd');
        var searchable = 'YES';
        var cashsorting = $('#cashsort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var sup_searchkey = $('#sup_search').val();
        var aorder = $('.aorder').val();
        var aamount = $('#aamount').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();

        $.ajax({
            type: 'POST',
            url: 'supervisorreports',
            data: {branch: branch, sup_searchkey: sup_searchkey, searchkey: searchkey, aorder: aorder, aamount: aamount, corder: corder, camount: camount, cashsorting: cashsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                } else
                {
                    $('.pos').html('<p class="noData">No Records Found</p>');
                    $(".commonLoaderV1").hide();
                }
            }
        });



    }

    function search2()
    {
        //alert('asd');
        var searchable = 'YES';
        var sup_searchkey = $('#sup_search').val();
        var shift = $('.shift').val();
        var sorting = $('#sortsimp').val();
        var cashsorting = $('#cashsort').val();
        var bsorting = $('#bsort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var aorder = $('.aorder').val();
        var aamount = $('#aamount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        if (camount != '' && corder != '')
        {
            var corder = $('.corder').val();
            var camount = $('#camount').val();
        }
        $.ajax({
            type: 'POST',
            url: 'supervisorreports',
            data: {branch: branch, sup_searchkey: sup_searchkey, searchkey: searchkey, aorder: aorder, aamount: aamount, corder: corder, camount: camount, bsorting: bsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                } else
                {
                    $('.pos').html('<p class="noData">No Records Found</p>');
                    $(".commonLoaderV1").hide();
                }
            }
        });



    }

    function search3()
    {
        //alert('asd');
        var searchable = 'YES';
        var shift = $('.shift').val();
        var totsorting = $('#tot').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var aorder = $('.aorder').val();
        var aamount = $('#aamount').val();
        var corder = $('.corder').val();
        var sup_searchkey = $('#sup_search').val();
        var camount = $('#camount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
//        if (amount != '' && order != '')
//        {
//            var order = $('.order').val();
//            var amount = $('#amount').val();
//        }
        $.ajax({
            type: 'POST',
            url: 'supervisorreports',
            data: {branch: branch, sup_searchkey: sup_searchkey, searchkey: searchkey, totsorting: totsorting, aorder: aorder, aamount: aamount, corder: corder, camount: camount, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                } else
                {
                    $('.pos').html('<p class="noData">No Records Found</p>');
                    $(".commonLoaderV1").hide();
                }
            }
        });



    }


    function search4()
    {
        //alert('asd');
        var searchable = 'YES';
        var shift = $('.shift').val();
        var osorting = $('#osort').val();
        var sup_searchkey = $('#sup_search').val();
        var ssorting = $('#ssort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var aorder = $('.aorder').val();
        var aamount = $('#aamount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();

        $.ajax({
            type: 'POST',
            url: 'supervisorreports',
            data: {branch: branch, sup_searchkey: sup_searchkey, searchkey: searchkey, corder: corder, camount: camount, aorder: aorder, aamount: aamount, osorting: osorting, ssorting: ssorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {

                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                } else
                {
                    $('.pos').html('<p class="noData">No Records Found</p>');
                    $(".commonLoaderV1").hide();
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
        });
        $('#reset').click(function () {
            if ($(this).hasClass("load_it") === true) {
                location.reload();
            } else {
                $(':input')
                        .not(':button, :submit, :reset, :hidden')
                        .val('')
                        .removeAttr('checked')
                        .removeAttr('selected');

                $('#sortsimp').val('');
                $('#cashsort').val('');
                $('#diffsort').val('');
                $('#bsort').val('');
                $('#tot').val('');
                $('#search').val('');
                $('.branch').val('');
                $('.corder').val('');
                $('.shift').val('');
                $('.cashorder').val('');
                $('.difforder').val('');
                $('#start_date').val('');
                $('#end_date').val('');
                $("#end_date").datepicker({
                    changeMonth: true,
                    changeYear: true, dateFormat: 'yy-mm-dd'
                }).datepicker("setDate", new Date());
                //search();
                window.location.href = '{{url("mis/pos_sales/supervisorreports")}}';
            }
        });
    });

    function savetopdf()
    {

        document.getElementById("pdfgenerator").submit();


    }
    $("#start_date").change(function(date){
        var start = $("#start_date").val();
        $("#from_date_hidden").val(start);
    })
    $("#end_date").change(function(date){
        var end = $("#end_date").val();
        $("#to_date_hidden").val(end);
    })
</script>



@endsection