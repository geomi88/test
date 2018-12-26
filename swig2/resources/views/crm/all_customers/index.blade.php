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

            if (!$("#slno").is(':checked') && !$("#p_searchbycustomername").is(':checked') &&
                    !$("#log_date").is(':checked') && !$("#mobile_number").is(':checked') &&
                    !$("#cus_branch").is(':checked') && !$("#p_createdby").is(':checked')) {
                toastr.error('Select required fields to print!');
            } else {

                win = window.open('', 'Print', 'width=1000,height=600');

                var strStyle = '<style>.paginationHolder {display:none;}';

                if (!$("#slno").is(':checked')) {
                    strStyle += ' .log_slno {display:none;}';
                }

                if (!$("#p_searchbycustomername").is(':checked')) {
                    strStyle += ' .p_searchbycustomername {display:none;}';
                }

                if (!$("#log_date").is(':checked')) {
                    strStyle += ' .log_date {display:none;}';
                }

                if (!$("#mobile_number").is(':checked')) {
                    strStyle += ' .mobile_number {display:none;}';
                }

                if (!$("#cus_branch").is(':checked')) {
                    strStyle += ' .cus_branch {display:none;}';
                }

                if (!$("#p_createdby").is(':checked')) {
                    strStyle += ' .p_createdby {display:none;}';
                }

                if (!$("#cus_repeat").is(':checked')) {
                    strStyle += ' .cus_repeat {display:none;}';
                }

                strStyle += '.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';

                win.document.write(strStyle + '<div style="text-align:center;"><h1>All Customers List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                        '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                        '<tr class="headingHolder">' +
                        '<td style="padding:10px 0;color:#fff;" class="log_slno"> Sl.No.</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="log_date"> Date & time </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="p_searchbycustomername"> Customer Name</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="mobile_number"> Mobile Number</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="cus_repeat"> Repeat </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="cus_branch"> Branch </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="p_createdby"> Created By </td>' +
                        '</tr>' +
                        '</thead>' + $('.tbl_elegant_content')[0].outerHTML + '</table>');
                win.document.close();
                win.print();
                win.close();
                return false;
            }
        });
        $('.saveDoc').click(function () {

//       svepdf();


        });
    });

    function getData(page) {

        var searchbytitle = $('#searchbytitle').val();
        var searchbycontent = $('#searchbycontent').val();
        var pagelimit = $('#page-limit').val();

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbytitle: searchbytitle, searchbycontent: searchbycontent, pagelimit: pagelimit},

                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    $(".tbl_elegant_content").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="" method="post">
        <header class="pageTitle">
            <h1>All Customers<span> List</span></h1>
        </header>
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>-->
        <div class="printChoose">
            <div class="custRow">

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="slno" checked="" type="checkbox">
                            <span></span>
                            <em>Sl no</em>
                        </label>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="p_searchbycustomername" checked="" type="checkbox">
                            <span></span>
                            <em>Customer Name</em>
                        </label>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="mobile_number" checked="" type="checkbox">
                            <span></span>
                            <em>Mobile Number</em>
                        </label>
                    </div>
                </div>
            </div>

            <div class="custRow">

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="log_date" checked="" type="checkbox">
                            <span></span>
                            <em>Date & Time</em>
                        </label>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="cus_branch" checked="" type="checkbox">
                            <span></span>
                            <em>Branch</em>
                        </label>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="p_createdby" checked="" type="checkbox">
                            <span></span>
                            <em>Created By</em>
                        </label>
                    </div>
                </div>

            </div>

            <div class="custRow">

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="cus_repeat" checked="" type="checkbox">
                            <span></span>
                            <em>Repeat</em>
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
                <div id="">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">

                                <td>
                                    Sl No
                                </td>
                                <td>
                                    Time & Date
                                </td>
                                <td>
                                    Customer Name
                                </td>
                                <td>
                                    Mobile Number
                                </td>
                                <td>
                                    Repeat
                                </td>
                                <td style="min-width: 200px;">
                                    Branch
                                </td>
                                <td>
                                    Created By
                                </td>
                                <td></td>

                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">

                                <td></td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <input type="text" id="datefrom" name="datefrom" value="" placeholder="From ">
                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="dateto" name="dateto" value="" placeholder="To ">
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbycustomername" name="searchbycustomername" placeholder="Enter Customer Name">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields" style="max-width: 50px!important;">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbymobile" name="searchbymobile" placeholder="Enter Mobile Number">
                                    </div>
                                </td>
                                <td class="">
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                        </thead>

                        <tbody class="tbl_elegant_content" id='tbl_elegant_content'>
                            @include('crm/all_customers/result')
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

    $('#searchbycustomername').bind('keyup', function () {
        search();
    });

    $('#searchbymobile').bind('keyup', function () {
        search();
    });

    $('#datefrom').bind('change', function () {
        search();
    });

    $('#dateto').bind('change', function () {
        search();
    });

    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {
        var searchbycustomername = $('#searchbycustomername').val();
        var searchbymobile = $('#searchbymobile').val();
        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'all_customers',
            data: {searchbycustomername: searchbycustomername, searchbymobile: searchbymobile, datefrom: datefrom, dateto: dateto, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.tbl_elegant_content').html(return_data);
                    $(".commonLoaderV1").hide();
                } else
                {
                    $(".commonLoaderV1").hide();
                    $('.tbl_elegant_content').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }

    $("#datefrom").datepicker({
        changeMonth: true,
        changeYear: true, dateFormat: 'dd-mm-yy'
    });
    $("#dateto").datepicker({
        changeMonth: true,
        changeYear: true, dateFormat: 'dd-mm-yy'
    });


    $(function () {

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');


            $('#searchbycustomername').val('');
            $('#searchbymobile').val('');
            $('#datefrom').val('');
            $('#dateto').val('');



            //search();
            window.location.href = '{{url("crm/all_customers")}}';
        });
    });

    function savetopdf()
    {

        document.getElementById("pdfgenerator").submit();


    }
</script>
@endsection
