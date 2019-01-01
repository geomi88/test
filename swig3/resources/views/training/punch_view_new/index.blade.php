@extends('layouts.main')
@section('content')
<script>

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
                    '<div style="text-align:center;"><h1>'+strRatingName+' New Employees</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                    '<tr class="headingHolder">' +
                    '<td style="padding:10px 0;color:#fff;"> Sl No. </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Employee Phone/Name</td>' +
                    '<td style="padding:10px 0;color:#fff;"> Date </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Valued By </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Reason </td>' +
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
        var cmbmonth = $('#cmbmonth').val();
        var cmbyear = $('#cmbyear').val();
        var rating = intRating;

        var pagelimit = $('#page-limit').val();


        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {empcode: empcode, rating: rating,cmbmonth:cmbmonth,cmbyear:cmbyear, pagelimit: pagelimit},
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
<div class="graphTypeV2 chartRegion">
    <div class="salesFilter">
        <form action="{{ url('training/getnewmonthwiseperformance') }}" method="post" id="frmperformancegraph">
            <div class="custRow">
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Month</label>
                        <select  name="cmbmonth" id="cmbmonth">
                            <option <?php if($currentMonth=='01'){ echo "selected";}?> value='01'>January</option>
                            <option <?php if($currentMonth=='02'){ echo "selected";}?> value='02'>February</option>
                            <option <?php if($currentMonth=='03'){ echo "selected";}?> value='03'>March</option>
                            <option <?php if($currentMonth=='04'){ echo "selected";}?> value='04'>April</option>
                            <option <?php if($currentMonth=='05'){ echo "selected";}?> value='05'>May</option>
                            <option <?php if($currentMonth=='06'){ echo "selected";}?> value='06'>June</option>
                            <option <?php if($currentMonth=='07'){ echo "selected";}?> value='07'>July</option>
                            <option <?php if($currentMonth=='08'){ echo "selected";}?> value='08'>August</option>
                            <option <?php if($currentMonth=='09'){ echo "selected";}?> value='09'>September</option>
                            <option <?php if($currentMonth=='10'){ echo "selected";}?> value='10'>October</option>
                            <option <?php if($currentMonth=='11'){ echo "selected";}?> value='11'>November</option>
                            <option <?php if($currentMonth=='12'){ echo "selected";}?> value='12'>December</option>
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Year</label>
                        <select  name="cmbyear" id="cmbyear">

                            <?php 
                                $Year=date('Y');
                                for($startYear=2017;$startYear<=$Year;$startYear++){
                            ?>
                                <option  <?php if($currentYear==$startYear){ echo "selected";}?> value='{{$startYear}}'>{{$startYear}}</option>
                            <?php }?>

                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
        </form>
        <div class="customClear"></div>
    </div>

    <div class="">
        <div class="custRow">
            <div id="region_graph" style=""></div>
        </div>
    </div>

    <div class="customClear"></div>
</div>

<div id="divbottom">
    <div class="innerContent">
        <form id="pdfgenerator" action="" method="post">
        <header class="pageTitle">
            <h1><span id="spnRating"> </span> Employees</h1>
        </header>
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <!--<a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>-->
        <div class="fieldGroup" id="fieldSet1">
            <div class="customClear"></div>
        </div>
        
        <div class="listHolderType1">
            <div class="listerType1 reportLister"> 


                <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <input type="hidden" value="" id="rating" name="rating">
                <input type="hidden" value="" id="ratingname" name="ratingname">
                <input type="hidden" value="" id="month" name="month">
                <input type="hidden" value="" id="year" name="year">

                <div id="tblregion">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">

                                <td>
                                    Sl.No.
                                </td>

                                <td>
                                    Employee Phone/Name

                                </td>
                                
                                <td>
                                    Date

                                </td>

                                <td>
                                    Valued By
                                </td>
                                
                                <td>
                                    Reason
                                </td>

                            </tr>
                        </thead>

                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">

                                <td>
                                </td>

                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="empcode" autocomplete="off" name="empcode" placeholder="Enter Phone">
                                        </div>
                                    </div>
                                </td>

                                <td class="filterFields">
                                    
                                </td>
                                
                                <td class="filterFields">
                                </td>

                                <td class="filterFields">
                                </td>

                            </tr>

                        </thead>

                        <tbody class="logbody" id='logbody'>
                            @include('training/punch_view_new/result')
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
</div>
<script>
    intRating = '';
    strRatingName = '';
    $(document).ready(function () {

        chart = new Highcharts.Chart('region_graph', {
            chart: {
                type: 'pie',
                backgroundColor: 'transparent'
            },
            title: {
                text: 'New Employee Training Performance'
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            plotOptions: {
                pie: {
                    shadow: false
                },
            },
            tooltip: {
                formatter: function () {
                    return '<b>' + this.point.name + '</b>: ' + this.y;
                }
            },
            series: [{
                    name: 'Performance',
                    data: <?php echo $full_pi_graph_data; ?>,
                    size: '100%',
                    innerSize: '40%',
                    showInLegend: true,
                    point: {
                        events: {
                            click: function (event) {
                                getemployees(this.id, this.name);
                            }
                        }
                    }
                }]
        });
        
        $('#cmbmonth').on('change', function () {
            $("#frmperformancegraph").submit();
            
        });

        $('#cmbyear').on('change', function () {
           
            $("#frmperformancegraph").submit();
        });

        $('#empcode').bind('keyup', function () {
            search();
        });

        $('#page-limit').on("change", function () {
            search();
        });
        
        $('#reset').click(function () {
            $('#empcode').val('');
            $('#page-limit').val(10);
            search();
        });
       
    });
    
    function getemployees(rating, rating_name) {
        $("#spnRating").html(rating_name);
        intRating = rating;
        strRatingName = rating_name;
        search();
    }

    function search()
    {

        var empcode = $('#empcode').val();
        var rating = intRating;
        
        var cmbmonth = $('#cmbmonth').val();
        var cmbyear = $('#cmbyear').val();

        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'getnewmonthwiseperformance',
            data: {empcode: empcode, rating: rating,cmbmonth:cmbmonth,cmbyear:cmbyear, pagelimit: pagelimit},
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
    
    function funExportData(strType)
    {
        if (strType == "PDF") {
            $('#excelorpdf').val('PDF');
        } else {
            $('#excelorpdf').val('Excel');
        }
        
        $("#rating").val(intRating);
        $("#ratingname").val(strRatingName);
        $("#month").val($("#cmbmonth").val());
        $("#year").val($("#cmbyear").val());
//        document.getElementById("pdfgenerator").submit();
    }



</script>

@endsection