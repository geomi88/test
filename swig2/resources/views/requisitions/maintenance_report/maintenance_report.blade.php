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

            if (!$("#branchCode").is(':checked') && !$("#branchName").is(':checked') && !$("#qOneT").is(':checked') && !$("#qTwoT").is(':checked') && !$("#qThreeT").is(':checked') && !$("#qFourT").is(':checked') && !$("#total").is(':checked')) {
                toastr.error('Select required fields to print!');
            } else {
                var pageTitle = 'Page Title',
                        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
                        win = window.open('', 'Print', 'width=500,height=300');

                var strStyle = '<style>.paginationHolder {display:none;}';

                if (!$("#branchCode").is(':checked')) {
                    strStyle += ' .sale_code {display:none;}';
                }
                if (!$("#branchName").is(':checked')) {
                    strStyle += ' .sale_branch {display:none;}';
                }
                if (!$("#qOneT").is(':checked')) {
                    strStyle += ' .sale_q1target {display:none;}';
                }
                if (!$("#qTwoT").is(':checked')) {
                    strStyle += ' .sale_q2target {display:none;}';
                }
                if (!$("#qThreeT").is(':checked')) {
                    strStyle += ' .sale_q3target {display:none;}';
                }
                strStyle += '.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';

                win.document.write(strStyle + '<div style="text-align:center;"><h1>Maintenance Requsition Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                        '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                        '<tr class="headingHolder">' +
                        '<td style="padding:10px 0;color:#fff;" class="sale_code"> Requisition Code</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="sale_branch"> Title</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="sale_q1target"> Date</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="sale_q2target"> Description</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="sale_q3target"> Status</td>' +
                        '<td style="padding:10px 0;color:#fff;"></td>' +
                        '</tr>' +
                        '</thead>' + $('.minsalesplan')[0].outerHTML + '</table>');
                win.document.close();
                win.print();
                win.close();
                return false;
            }
        });
    });

    function getData(page) {

        var searchbycode = $('#searchbycode').val();
        var searchbyname = $('#searchbyname').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var status = $('#status').val();
        var sortordcode = $('#sortordcode').val();
        var sortordtitle = $('#sortordtitle').val();
        var sortorddate = $('#sortorddate').val();
        var pagelimit = $('#page-limit').val();
        
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {pagelimit: pagelimit, searchbycode:searchbycode, searchbyname:searchbyname, created_at_from:created_at_from, created_at_to:created_at_to, status:status, sortordcode:sortordcode, sortordtitle:sortordtitle, sortorddate:sortorddate},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {


                    $(".minsalesplan").empty().html(data);
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
        <header class="pageTitle" style="margin-bottom: 30px;padding-bottom: 10px;">
            <h1>Maintenance Requisition Report</h1>
        </header>

        <form id="pdfgenerator" action="{{ url('requisition/maintenance_requisition_report/exportdata') }}" method="post">



            <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
            <a class="btnAction print bgGreen" href="#">Print</a>
            <a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf('PDF')">PDF</a>
            <a class="btnAction saveDoc bgOrange" href="#" onclick="savetopdf('EXCEL')">Excel</a>

            <div class="printChoose">
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="branchCode" checked="" type="checkbox">
                                <span></span>
                                <em>Requisition Code</em>
                            </label>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="branchName" checked="" type="checkbox">
                                <span></span>
                                <em>Title</em>
                            </label>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="qOneT" checked="" type="checkbox">
                                <span></span>
                                <em>Date</em>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="custRow">
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="qTwoT" checked="" type="checkbox">
                                <span></span>
                                <em>Description</em>
                            </label>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="qThreeT" checked="" type="checkbox">
                                <span></span>
                                <em>Status</em>
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

            <div class="listHolderType1 ">
                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <input type="hidden" value="" id="sortordcode" name="sortordcode">
                <input type="hidden" value="" id="sortordtitle" name="sortordtitle">
                <input type="hidden" value="" id="sortorddate" name="sortorddate">
                <div class="listerType1 cash_collections"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
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
                                <td>Description</td>
                                <td>Status</td>
                                <td>Action</td>
                            </tr>

                        </thead>
                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">


                                <td class="filterFields" style="min-width: 150px;">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input id="searchbycode" name="searchbycode" placeholder="Enter Code" type="text" autocomplete="off">
                                        </div>
                                    </div>
                                </td>


                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input id="searchbyname" name="searchbyname" placeholder="Enter Title" type="text" autocomplete="off">
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
                                <td class="filterFields"></td>
                                <td >
                                    <div class="custCol-12">
                                        <select name="status" id="status">
                                            <option value="">All</option>
                                            <option value="1">Pending</option>
                                            <option value="4">Approved</option>
                                            <option value="5">Rejected</option>
                                            
                                        </select>
                                    </div>
                                </td>
                                <td></td>
                            </tr>

                        </thead>

                        <tbody class="minsalesplan" id='minsalesplan'>
                            @include('requisitions/maintenance_report/maintenance_report_results')
                        </tbody>
                    </table>
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


    $('#searchbyname').bind('keyup', function () { 

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
    
    $('#page-limit').on("change", function () {
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
        search();
    });
    $(".datedown").on('click', function () {
        $('#sortorddate').val('DESC');
        $('#sortordtitle').val('');
        $('#sortordcode').val('');
        search();
    });

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

    });

    function search()
    {

        var searchbycode = $('#searchbycode').val();
        var searchbyname = $('#searchbyname').val();
        var created_at_from = $('#created_at_from').val();
        var created_at_to = $('#created_at_to').val();
        var status = $('#status').val();
        var sortordcode = $('#sortordcode').val();
        var sortordtitle = $('#sortordtitle').val();
        var sortorddate = $('#sortorddate').val();
        var pagelimit = $('#page-limit').val();
        
        $.ajax({
            type: 'POST',
            url: 'maintenance_requisition_report',
            data: {searchbycode: searchbycode, pagelimit: pagelimit, searchbyname:searchbyname, created_at_from:created_at_from, created_at_to:created_at_to,status : status, sortordcode:sortordcode, sortordtitle:sortordtitle, sortorddate:sortorddate},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.minsalesplan').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.minsalesplan').html('<p class="noData">No Records Found</p>');
                }
            }
        });


    }


    $("#reset").click(function () {
        window.location.href = '{{url("requisition/maintenance_requisition_report")}}';
    });


    function savetopdf(strType) {

        if (strType == "PDF") {
            $('#excelorpdf').val('PDF');
        } else {
            $('#excelorpdf').val('EXCEL');
        }

        document.getElementById("pdfgenerator").submit();

    }

</script>

@endsection
