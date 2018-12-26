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

            if (!$("#slno").is(':checked') && !$("#visitorName").is(':checked') && !$("#logdate").is(':checked') && !$("#logemp").is(':checked') && !$("#lodpurp").is(':checked') && !$("#logmob").is(':checked')&& !$("#logemail").is(':checked')) {
                toastr.error('Select required fields to print!');
            } else {

                win = window.open('', 'Print', 'width=1000,height=600');

                var strStyle = '<style>.paginationHolder {display:none;}';

                if (!$("#slno").is(':checked')) {
                    strStyle += ' .log_slno {display:none;}';
                }

                if (!$("#visitorName").is(':checked')) {
                    strStyle += ' .log_name {display:none;}';
                }

                if (!$("#logdate").is(':checked')) {
                    strStyle += ' .log_date {display:none;}';
                }

                if (!$("#logemp").is(':checked')) {
                    strStyle += ' .log_emp {display:none;}';
                }

                if (!$("#lodpurp").is(':checked')) {
                    strStyle += ' .log_purp {display:none;}';
                }
                
                if (!$("#logmob").is(':checked')) {
                    strStyle += ' .log_mob {display:none;}';
                }
                
                if (!$("#logemail").is(':checked')) {
                    strStyle += ' .log_email {display:none;}';
                }

                strStyle += '.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';

                win.document.write(strStyle + '<div style="text-align:center;"><h1>Visitors List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                        '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                        '<tr class="headingHolder">' +
                        '<td style="padding:10px 0;color:#fff;" class="log_slno"> Sl.No.</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="log_name"> Visitor Name</td>' +
                        '<td style="padding:10px 0;color:#fff;" class="log_date"> Date & time </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="log_emp"> To Meet </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="log_purp"> Purpose </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="log_mob"> Mobile </td>' +
                        '<td style="padding:10px 0;color:#fff;" class="log_email"> Email </td>' +
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
        var empcode = $('#empcode').val();

        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();

        var pagelimit = $('#page-limit').val();


        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbyname: searchbyname, empcode: empcode, datefrom: datefrom, dateto: dateto, pagelimit: pagelimit},
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
    <form id="pdfgenerator" action="{{ url('reception/visitors_list/exportdata') }}" method="post">
        <header class="pageTitle">
            <h1><span>Visitors</span> List</h1>
        </header>	

      
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>

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
                            <input id="visitorName" checked="" type="checkbox">
                            <span></span>
                            <em>Visitor Name</em>
                        </label>
                    </div>
                </div>
            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="logdate" checked="" type="checkbox">
                            <span></span>
                            <em>Date & Time</em>
                        </label>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="logemp" checked="" type="checkbox">
                            <span></span>
                            <em>To Meet</em>
                        </label>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="lodpurp" checked="" type="checkbox">
                            <span></span>
                            <em>Purpose</em>
                        </label>
                    </div>
                </div>
                
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="logmob" checked="" type="checkbox">
                            <span></span>
                            <em>Mobile</em>
                        </label>
                    </div>
                </div>
                
                <div class="custCol-4">
                    <div class="commonCheckHolder checkRender">
                        <label>
                            <input id="logemail" checked="" type="checkbox">
                            <span></span>
                            <em>Email</em>
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
                                    Visitor Name

                                </td>

                                <td>
                                    Date & time
                                </td>

                                <td>
                                    To Meet
                                </td>

                                <td style="min-width: 210px;">
                                    Purpose

                                </td>
                                
                                <td style="min-width: 50px !important;">
                                    Mobile
                                </td>
                                
                                <td style="min-width: 60px !important;">
                                    Email

                                </td>

                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">

                                <td></td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbyname" name="searchbyname" placeholder="Enter Visitor Name">
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
                                            <input type="text" id="empcode" name="empcode" class="empcode" placeholder="Enter Code">
                                        </div>

                                    </div>
                                </td>

                                <td>
                                </td>
                                
                                <td>
                                </td>
                                
                                <td>
                                </td>


                            </tr>

                        </thead>

                        <tbody class="logbody" id='logbody'>
                            @include('reception/visitors_list/result')

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

    $('#empcode').bind('keyup', function () {
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
        var empcode = $('#empcode').val();

        var datefrom = $('#datefrom').val();
        var dateto = $('#dateto').val();

        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'visitors_list',
            data: {searchbyname: searchbyname, empcode: empcode, datefrom: datefrom, dateto: dateto, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.logbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
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
            window.location.href = '{{url("reception/visitors_list")}}';
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
