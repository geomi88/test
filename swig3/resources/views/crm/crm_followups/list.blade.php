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

        $('.takePrint').click(function () {

            if (!$("#slno").is(':checked') && !$("#customer_name").is(':checked') && !$("#log_date").is(':checked') && !$("#mobile_number").is(':checked') && !$("#statuss").is(':checked') && !$("#cus_branch").is(':checked') && !$("#feed_created_by").is(':checked') && !$("#customer_comments").is(':checked')) {
                toastr.error('Select required fields to print!');
            } else {

                win = window.open('', 'Print', 'width=1000,height=600');

                var strStyle = '<style>.paginationHolder {display:none;}';

                if (!$("#slno").is(':checked')) {
                    strStyle += ' .log_slno {display:none;}';
                }

                if (!$("#customer_name").is(':checked')) {
                    strStyle += ' .customer_name {display:none;}';
                }

                if (!$("#log_date").is(':checked')) {
                    strStyle += ' .log_date {display:none;}';
                }

                if (!$("#mobile_number").is(':checked')) {
                    strStyle += ' .mobile_number {display:none;}';
                }
                
                if (!$("#statuss").is(':checked')) {
                    strStyle += ' .customer_status {display:none;}';
                }


                if (!$("#cus_branch").is(':checked')) {
                    strStyle += ' .cus_branch {display:none;}';
                }

                if (!$("#feed_created_by").is(':checked')) {
                    strStyle += ' .feed_created_by {display:none;}';
                }

                if (!$("#customer_comments").is(':checked')) {
                    strStyle += ' .customer_comments {display:none;}';
                }

                strStyle += '.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';

                win.document.write(strStyle + '<div style="text-align:center;"><h1>CRM Followup List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                        '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                        '<tr class="headingHolder">' +
                        '<td style="padding:10px 0;color:#fff;" class="log_slno"> Sl.No.</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="customer_name"> Customer Name</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="log_date"> Date & time </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="mobile_number"> Mobile </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="cus_branch"> Branch </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="feed_created_by"> Created By </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="customer_comments"> Comments </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="customer_status"> Status </td>' +
                        '</tr>' +
                        '</thead>' + $('.logbody')[0].outerHTML + '</table>');
                win.document.close();
                win.print();
                win.close();
                return false;
            }
        });

    });

    function getData(page) {

        var searchbyname = $('#searchbyname').val();
        var mobile = $('#mobile').val();
        var status = $('#status').val();

        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();

        var pagelimit = $('#page-limit').val();


        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbyname: searchbyname, mobile: mobile, status:status, datefrom: datefrom, dateto: dateto, pagelimit: pagelimit},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    //console.log(data);

                    $(".logbody").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <a class="btnBack" href="{{ url('crm')}}">Back</a>
    <form id="pdfgenerator" action="{{ url('reception/visitors_list/exportdata') }}" method="post">
        <header class="pageTitle">
            <h1>CRM Followup <span>List</span></h1>
        </header>	


        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <!--a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a-->

        <div class="printChoose">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="slno" checked="" type="checkbox">
                            <span></span>
                            <em> Sl.No.</em>
                        </label>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="customer_name" checked="" type="checkbox">
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
                            <em>Mobile</em>
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
                            <input id="feed_created_by" checked="" type="checkbox">
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
                            <input id="customer_comments" checked="" type="checkbox">
                            <span></span>
                            <em>Comment</em>
                        </label>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="statuss" checked="" type="checkbox">
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

        <div class="fieldGroup" id="fieldSet1">
            <div class="customClear"></div>
        </div>

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
                                    Customer Name
                                </td>

                                <td>
                                    Date & time
                                </td>

                                <td>
                                    Mobile
                                </td>
                                <td>
                                    Branch
                                </td>
                                <td>
                                    Created By
                                </td>

                                <td style="min-width: 210px;">
                                    Comment
                                </td>
                                <td>
                                    Status
                                </td>
                                <td>
                                    Actions
                                </td>

                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">

                                <td></td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbyname" name="searchbyname" placeholder="Enter Customer Name">
                                        </div>
                                    </div>
                                </td>

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
                                    <div class="custRow">

                                        <div class="custCol-12">
                                            <input type="text" id="mobile" name="mobile" class="mobile" placeholder="Enter Mobile ">
                                        </div>

                                    </div>
                                </td>

                                <td>
                                </td>

                                <td>
                                </td>

                                <td>
                                </td>
                                
                                <td>
                                    <div class="custCol-12">
                                        <select name="status" id="status">
                                            <option value="">All</option>
                                            <option value="1">Pending</option>
                                            <option value="3">Following</option>
                                            <option value="-1">Closed</option>

                                        </select>
                                    </div>

                                </td>

                                <td>
                                </td>

                            </tr>

                        </thead>
                        <tbody class="logbody" id='logbody'>
                            @include('crm/crm_followups/result')
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

    $('#searchbyname').bind('keyup', function () {
        search();
    });

    $('#mobile').bind('keyup', function () {
        search();
    });
    
    $('#status').on("change", function () {
        search();
    });

    $('#datefrom').on("change", function () {
        if ($('#datefrom').val() !== '') {
            search();
        }
    });

    $('#dateto').on("change", function () {
        if ($('#dateto').val() !== '') {
            search();
        }
    });

    $('#page-limit').on("change", function () {
        search();
    });

    function search()
    {
        var searchbyname = $('#searchbyname').val();
        var mobile = $('#mobile').val();
        var status = $('#status').val();

        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();

        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'crm_followups',
            data: {searchbyname: searchbyname, mobile: mobile,status:status, datefrom: datefrom, dateto: dateto, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.logbody').html(return_data);
                    $(".commonLoaderV1").hide();
                } else
                {
                    $(".commonLoaderV1").hide();
                    $('.logbody').html('<tr><td colspan="3"><p class="noData">No Records Found</p></td></tr>');
                }
            }
        });
    }



    $(function () {

        $("#datefrom").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $("#dateto").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });


        $('#reset').click(function () {
            $('.printChoose').hide();
            $('#searchbyname').val('');
            $('#mobile').val('');
            $('#status').val('');
            $('#datefrom').val('');
            $('#dateto').val('');
            $('#page-limit').val(10);
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
