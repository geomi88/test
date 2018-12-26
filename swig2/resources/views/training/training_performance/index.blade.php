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
            win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;} .right {float: right;margin-right:20px;}</style>' +
                    '<div style="text-align:center;"><h1> Training Performance</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                    '<tr class="headingHolder">' +
                    '<td style="padding:10px 0;color:#fff;"> Sl No. </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Employee Name</td>' +
                    '<td style="padding:10px 0;color:#fff;"> Employee Type</td>' +
                    '<td style="padding:10px 0;color:#fff;"> Rating </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Valued By </td>' +
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
        var rating = $('#rating').val();

        var pagelimit = $('#page-limit').val();


        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {empcode: empcode,rating:rating,pagelimit: pagelimit},
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
    <form id="pdfgenerator" action="{{ url('training/training_performance/exportdata') }}" method="post">
        <header class="pageTitle">
            <h1>Training<span> Performance</span></h1>
        </header>	

        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-12">
                    <a href="{{ action('Training\TrainingperformanceController@add') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
            <div class="customClear"></div>
        </div>
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <!--<a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>-->

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
                                    Employee Name

                                </td>
                                <td>
                                    Employee Type

                                </td>

                                <td>
                                    Rating
                                </td>

                                <td>
                                    Valued By
                                </td>
                                
                                <td>
                                    Action
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
                                <td></td>
                                <td class="filterFields">
                                    <div class="custCol-12">
                                        <select id="rating" name="rating" class="rating">
                                            <option value="">All</option>
                                            <option value="1">Exceptional (90 - 100%)</option>
                                            <option value="2">Effective (70 - 90%)</option>
                                            <option value="3">Inconsistent (50 - 70%)</option>
                                            <option value="4">Unsatisfactory (40 - 50%)</option>
                                            <option value="5">Not Acceptable (Below 40%)</option>
                                        </select>
                                    </div>
                                </td>

                                <td class="filterFields">
                                    
                                </td>
                                
                                <td class="">
                                    
                                </td>

                            </tr>

                        </thead>

                        <tbody class="logbody" id='logbody'>
                            @include('training/training_performance/result')

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


    $('#empcode').bind('keyup', function () {
        search();
    });

    $('#rating').on("change", function () {
       search();
    });


    $('#page-limit').on("change", function () {
        search();
    });

    function search()
    {
      
        var empcode = $('#empcode').val();
        var rating = $('#rating').val();

        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'training_performance',
            data: {empcode: empcode,rating:rating,pagelimit: pagelimit},
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
            window.location.href = '{{url("training/training_performance")}}';
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
