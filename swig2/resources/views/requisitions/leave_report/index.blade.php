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
                    win = window.open('', 'Print', 'height=' + screen.height, 'width=' + screen.width);
            win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;} .right {float: right;margin-right:20px;}</style>' +
                    '<div style="text-align:center;"><h1>Leave Requisition Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                    '<tr class="headingHolder">' +
                    '<td style="padding:10px 0;color:#fff;"> Requisition Code </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Title </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Date </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Status </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Leave Type </td>' +
                    '<td style="padding:10px 0;color:#fff;"> From Date </td>' +
                    '<td style="padding:10px 0;color:#fff;"> To Date </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Created By </td>' +
                    '<td style="padding:10px 0;color:#fff;"> System Position </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Leave Description </td>' +
                    '</tr>' +
                    '</thead>' + $('.inbox_list')[0].outerHTML + '</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });

    });
    function getData(page) {
        var searchbycode = $('#searchbycode').val();
        var searchbytitle = $('#searchbytitle').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();

        var status = $('#status').val();
        var sortordtitle = $('#sortordtitle').val();
        var sortordcode = $('#sortordcode').val();
        var sortorddate = $('#sortorddate').val();
        var pagelimit = $('#page-limit').val();

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbycode: searchbycode, searchbytitle: searchbytitle, status: status, created_at_from: created_at_from, created_at_to: created_at_to, sortordcode: sortordcode, sortordtitle: sortordtitle, sortorddate: sortorddate, pagelimit: pagelimit},

                })
                .done(function (data)
                {

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
    <form id="pdfgenerator" action="{{ url('requisitions/leave_report/exportdata') }}" method="post">
        <header class="pageTitle">
            <h1>Leave Requisition <span>Report</span></h1>
        </header>	

        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>

        <div class="listHolderType1">
            <div class="custRow">
            </div>
            <div class="listerType1 reportLister"> 

                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <input type="hidden" value="" id="sortordcode" name="sortordcode">
                <input type="hidden" value="" id="sortordtitle" name="sortordtitle">
                <input type="hidden" value="" id="sortorddate" name="sortorddate">

                <div id="postable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>
                                    Requisition Code
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp codeup"></a>
                                        <a href="javascript:void(0)" class="btnDown codedown"></a>
                                    </div>
                                </td>
                                <td>
                                    Title
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp titleup"></a>
                                        <a href="javascript:void(0)" class="btnDown titledown"></a>
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
                                    Status
                                </td>
                                <td>
                                    Leave Type
                                </td>
                                <td>
                                    From Date
                                </td>
                                <td>
                                    To Date
                                </td>
                                <td>
                                    Created By
                                </td>
                                <td>
                                    System Position
                                </td>
                                <td>
                                    Leave Description
                                </td>
                                <td>
                                </td>
                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">
                                <td class="filterFields" style="min-width: 143px;">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbycode" name="searchbycode" autocomplete="off" placeholder="Enter Code">
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbytitle" autocomplete="off" name="searchbytitle" placeholder="Enter Title">
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
                                <td>
                                    <select id="status" name="status">
                                        <option value="">All</option>
                                        <option value="1">Pending</option>
                                        <option value="4">Approved</option>
                                        <option value="5">Rejected</option>
                                    </select>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="min-width: 155px;"></td>
                                <td></td>
                            </tr>

                        </thead>

                        <tbody class="inbox_list" id='inbox_list'>                  
                            @include('requisitions/leave_report/results')
                        </tbody>

                    </table>
                </div>

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
<div class="commonLoaderV1"></div>
<script>

    $('#searchbycode').bind('keyup', function () {
        search();
    });

    $('#searchbytitle').bind('keyup', function () {
        search();
    });

    $('#created_at_from').on("change", function () {
        search();
    });

    $('#created_at_to').on("change", function () {
        search();
    });

    $('#status').on("change", function () {
        search();
    });

    $(".codeup").on('click', function () {
        $('#sortordcode').val('ASC');
        $('#sortordtitle').val('');
        $('#sortorddate').val('');
        search();
    });
    $(".codedown").on('click', function () {
        $('#sortordcode').val('DESC');
        $('#sortordtitle').val('');
        $('#sortorddate').val('');
        search();
    });

    $(".titleup").on('click', function () {
        $('#sortordtitle').val('ASC');
        $('#sortorddate').val('');
        $('#sortordcode').val('');
        search();
    });
    $(".titledown").on('click', function () {
        $('#sortordtitle').val('DESC');
        $('#sortorddate').val('');
        $('#sortordcode').val('');
        search();
    });

    $(".dateup").on('click', function () {
        $('#sortorddate').val('ASC');
        $('#sortordtitle').val('');
        $('#sortordcode').val('');
        $('#sortordtype').val('');
        search();
    });
    $(".datedown").on('click', function () {
        $('#sortorddate').val('DESC');
        $('#sortordtitle').val('');
        $('#sortordcode').val('');
        search();
    });



    $('#page-limit').on("change", function () {
        search();
    });

    function search()
    {
        var searchbycode = $('#searchbycode').val();
        var searchbytitle = $('#searchbytitle').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();

        var status = $('#status').val();
        var sortordtitle = $('#sortordtitle').val();
        var sortordcode = $('#sortordcode').val();
        var sortorddate = $('#sortorddate').val();


        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'leave_report',
            data: {searchbycode: searchbycode, searchbytitle: searchbytitle, status: status, created_at_from: created_at_from, created_at_to: created_at_to, sortordcode: sortordcode, sortordtitle: sortordtitle, sortorddate: sortorddate, pagelimit: pagelimit},
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
        $("#created_at_from").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy',
            onSelect: function (selected) {
                $("#created_at_to").datepicker("option", "minDate", selected)
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

            $('#sortordtitle').val('');
            $('#sortordtype').val('');
            $('#sortordcode').val('');
            $('#sortorddate').val('');
            $("#page-limit").val(10);
            search();
        });
    });
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
