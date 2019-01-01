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
            win = window.open('', 'Print', 'height=' + screen.height, 'width=' + screen.width);
            win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;} .right {float: right;margin-right:20px;}'+
                    '.exceptionalcount{width: 20px;margin: 2px 15px 2px 0px;float: left; background-color: #029346;border: none; }'+
                    '.effectivecount{ width: 20px;margin: 2px 15px 2px 0px;float: left; background-color: #42fb1d;border: none; }'+
                    '.inconsistentcount{width: 20px;margin: 2px 15px 2px 0px;float: left; background-color: #f5f516;border: none;}'+
                    '.unsatisfactorycount{width: 20px;margin: 2px 15px 2px 0px;float: left; background-color: #f87c31;border: none;}'+
                    '.notacceptablecount{ width: 20px;margin: 2px 15px 2px 0px;float: left; background-color: #d60f06;border: none;}</style>' +
                    '<div style="text-align:center;"><h1> Performance Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                    '<tr class="headingHolder">' +
                    '<td style="padding:10px 0;color:#fff;"> Sl No. </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Employee Name</td>' +
                    '<td style="padding:10px 0;color:#fff;"> Exceptional (90 - 100%) </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Effective (70 - 90%) </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Inconsistent (50 - 70%) </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Unsatisfactory (40 - 50%) </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Not Acceptable (Below 40%) </td>' +
                    
                    '</tr>' +
                    '</thead>' + $('.logbody')[0].outerHTML + '</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });
        
    });

    function getData(page) {

        var empcode = $('#empcode').val();
        var sortordexceptional = $('#sortordexceptional').val();
        var sortordeffective = $('#sortordeffective').val();
        var sortordinconsis = $('#sortordinconsis').val();
        var sortordunsatisfact = $('#sortordunsatisfact').val();
        var sortordnotaccept = $('#sortordnotaccept').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        
        var pagelimit = $('#page-limit').val();

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {empcode: empcode,to_date:to_date,from_date:from_date,sortordexceptional:sortordexceptional,sortordeffective:sortordeffective,sortordinconsis:sortordinconsis,sortordunsatisfact:sortordunsatisfact,sortordnotaccept:sortordnotaccept,pagelimit: pagelimit},
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
    <form id="pdfgenerator" action="{{ url('organizationchart/punch_performance/exportdata') }}" method="post">
        <header class="pageTitle">
            <h1>Performance<span> Report</span></h1>
        </header>	

        
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>

        <div class="fieldGroup" id="fieldSet1">
            <div class="customClear"></div>
        </div>
<div class="leftSection">
            <div class="dates_div custCol-8">
                <div class="custCol-5">
                    <div class="inputHolder">
                        <label>From</label>
                        <input type="text" name="from_date" id="from_date" value="" placeholder="Select From Date" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-5">
                    <div class="inputHolder">
                        <label>To</label>
                        <input  type="text" name="to_date" id="to_date" value="" placeholder="Select To Date" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="listHolderType1">
            <div class="listerType1 reportLister"> 

              
                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <input type="hidden" value="" id="sortordexceptional" name="sortordexceptional">
                <input type="hidden" value="" id="sortordeffective" name="sortordeffective">
                <input type="hidden" value="" id="sortordinconsis" name="sortordinconsis">
                <input type="hidden" value="" id="sortordunsatisfact" name="sortordunsatisfact">
                <input type="hidden" value="" id="sortordnotaccept" name="sortordnotaccept">
                
                <div id="tblregion">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">

                                <td>
                                    Sl.No.
                                </td>

                                <td>
                                    Employee Name

                                </td>

                                <td>
                                    Exceptional (90 - 100%)
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp exceptup"></a>
                                        <a href="javascript:void(0)" class="btnDown exceptdown"></a>
                                    </div>
                                </td>

                                <td>
                                    Effective (70 - 90%)
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp effectiveup"></a>
                                        <a href="javascript:void(0)" class="btnDown effectivedown"></a>
                                    </div>
                                </td>
                                
                                <td>
                                    Inconsistent (50 - 70%)
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp inconsistup"></a>
                                        <a href="javascript:void(0)" class="btnDown inconsistdown"></a>
                                    </div>
                                </td>
                                
                                <td>
                                    Unsatisfactory (40 - 50%)
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp unsatisfactup"></a>
                                        <a href="javascript:void(0)" class="btnDown unsatisfactdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Not Acceptable (Below 40%)
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp notacceptup"></a>
                                        <a href="javascript:void(0)" class="btnDown notacceptdown"></a>
                                    </div>
                                </td>

                              
                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">

                                <td></td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="empcode" autocomplete="off" name="empcode" placeholder="Enter Code">
                                        </div>
                                    </div>
                                </td>

                                <td class="">
                                   
                                </td>

                                <td class="">
                                    
                                </td>
                                <td class="">
                                    
                                </td>
                                <td class="">
                                    
                                </td>
                                <td class="">
                                    
                                </td>

                               

                            </tr>

                        </thead>

                        <tbody class="logbody" id='logbody'>
                            @include('organizationchart/punch_report/result')

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
    <div class="commonLoaderV1"></div>
</div>

<script>


    $('#empcode').bind('keyup', function () {
        search();
    });

    $(".exceptup").on('click', function () {
        $('#sortordexceptional').val('ASC');
        $('#sortordeffective').val('');
        $('#sortordinconsis').val('');
        $('#sortordunsatisfact').val('');
        $('#sortordnotaccept').val('');
        search();
    });
    
    $(".exceptdown").on('click', function () {
        $('#sortordexceptional').val('DESC');
        $('#sortordeffective').val('');
        $('#sortordinconsis').val('');
        $('#sortordunsatisfact').val('');
        $('#sortordnotaccept').val('');
        search();
    });
    
    $(".effectiveup").on('click', function () {
        $('#sortordexceptional').val('');
        $('#sortordeffective').val('ASC');
        $('#sortordinconsis').val('');
        $('#sortordunsatisfact').val('');
        $('#sortordnotaccept').val('');
       
        search();
    });
    
    $(".effectivedown").on('click', function () {
        $('#sortordexceptional').val('');
        $('#sortordeffective').val('DESC');
        $('#sortordinconsis').val('');
        $('#sortordunsatisfact').val('');
        $('#sortordnotaccept').val('');
       
        search();
    });
    
    $(".inconsistup").on('click', function () {
        $('#sortordexceptional').val('');
        $('#sortordeffective').val('');
        $('#sortordinconsis').val('ASC');
        $('#sortordunsatisfact').val('');
        $('#sortordnotaccept').val('');
        search();
    });
    
    $(".inconsistdown").on('click', function () {
        $('#sortordexceptional').val('');
        $('#sortordeffective').val('');
        $('#sortordinconsis').val('DESC');
        $('#sortordunsatisfact').val('');
        $('#sortordnotaccept').val('');
       
        search();
    });
    
    $(".unsatisfactup").on('click', function () {
        $('#sortordexceptional').val('');
        $('#sortordeffective').val('');
        $('#sortordinconsis').val('');
        $('#sortordunsatisfact').val('ASC');
        $('#sortordnotaccept').val('');
        search();
    });
    
    $(".unsatisfactdown").on('click', function () {
        $('#sortordexceptional').val('');
        $('#sortordeffective').val('');
        $('#sortordinconsis').val('');
        $('#sortordunsatisfact').val('DESC');
        $('#sortordnotaccept').val('');
        search();
    });
    
    $(".notacceptup").on('click', function () {
        $('#sortordexceptional').val('');
        $('#sortordeffective').val('');
        $('#sortordinconsis').val('');
        $('#sortordunsatisfact').val('');
        $('#sortordnotaccept').val('ASC');
        search();
    });
    
    $(".notacceptdown").on('click', function () {
        $('#sortordexceptional').val('');
        $('#sortordeffective').val('');
        $('#sortordinconsis').val('');
        $('#sortordunsatisfact').val('');
        $('#sortordnotaccept').val('DESC');
        search();
    });


    $('#page-limit').on("change", function () {
        search();
    });

    function search()
    {
      
        var empcode = $('#empcode').val();
        var sortordexceptional = $('#sortordexceptional').val();
        var sortordeffective = $('#sortordeffective').val();
        var sortordinconsis = $('#sortordinconsis').val();
        var sortordunsatisfact = $('#sortordunsatisfact').val();
        var sortordnotaccept = $('#sortordnotaccept').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();

        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'punchreport',
            data: {empcode: empcode,to_date:to_date,from_date:from_date,sortordexceptional:sortordexceptional,sortordeffective:sortordeffective,sortordinconsis:sortordinconsis,sortordunsatisfact:sortordunsatisfact,sortordnotaccept:sortordnotaccept,pagelimit: pagelimit},
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

        $('#reset').click(function () {
            window.location.href = '{{url("organizationchart/punchreport")}}';
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

    $("#from_date").datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function (selected) {
            $("#to_date").datepicker("option", "minDate", selected)
            search();
        }
    });

    $("#to_date").datepicker({
        dateFormat: 'dd-mm-yy',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function (selected) {
            $("#from_date").datepicker("option", "maxDate", selected)
            search();
        }
    });
</script>
@endsection
