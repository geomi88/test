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
            
        if(!$("#branchCode").is(':checked')&&!$("#branchName").is(':checked')&&!$("#quarterSale").is(':checked')&&!$("#quarterTarget").is(':checked')&&!$("#salesp").is(':checked')&&!$("#varience").is(':checked')&&!$("#variencep").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#branchCode").is(':checked')){
                strStyle+=' .branch_code {display:none;}';
         }
         if(!$("#branchName").is(':checked')){
                strStyle+=' .branch_name {display:none;}';
         }
         if(!$("#quarterSale").is(':checked')){
               strStyle+=' .quarter_sale {display:none;}';
         }
         if(!$("#quarterTarget").is(':checked')){
              strStyle+=' .quarter_target {display:none;}';  
         } 
         if(!$("#salesp").is(':checked')){
              strStyle+=' .sales_per {display:none;}';   
         }
         if(!$("#varience").is(':checked')){
               strStyle+=' .varience {display:none;}';   
         }
         if(!$("#variencep").is(':checked')){
             strStyle+=' .varience_per {display:none;}';     
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Sales Variance Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_code"> Branch Code</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_name"> Branch Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="quarter_sale"> Quarter Sale</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="quarter_target"> Quarter Target</td>'+
                                           '<td style="padding:10px 0;color:#fff;" class="sales_per"> Sales %</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="varience"> Variance</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="varience_per"> Variance % </td>'+
                                            '<td style="padding:10px 0;color:#fff;"></td>'+
                                            
                                        '</tr>'+
				'</thead>'+ $('.variancereport')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
    });

    function getData(page) {

        var region_id = $('#region_id').val();
        var branch_id = $('#branch_id').val();
        var cmbquarter = $('#cmbquarter').val();
        var cmbyear = $('#cmbyear').val();
        var searchbycode = $('#searchbycode').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {region_id: region_id, quarter: cmbquarter, year: cmbyear, branch_id: branch_id, branch_code: searchbycode, pagelimit: pagelimit},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    

                    $(".variancereport").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="contentArea">

    <div class="innerContent">
        <header class="pageTitle" style="margin-bottom: 30px;padding-bottom: 10px;">
            <h1>Sales Variance <span>Report</span></h1>
        </header>

        <form id="pdfgenerator" action="{{ url('finance/exportvariancereport') }}" method="post">
            <div class="leftSection" style="padding-bottom: 15px;">

                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Quarter</label>
                        <select  name="cmbquarter" id="cmbquarter">
                            <!--<option  value=''>All</option>-->
                            <option <?php if ($currentQuarter == '1') {echo "selected";} ?> value='1'>Q1</option>
                            <option <?php if ($currentQuarter == '2') {echo "selected";} ?> value='2'>Q2</option>
                            <option <?php if ($currentQuarter == '3') {echo "selected";} ?> value='3'>Q3</option>
                            <option <?php if ($currentQuarter == '4') { echo "selected";} ?> value='4'>Q4</option>

                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>

                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Year</label>
                        <select  name="cmbyear" id="cmbyear">

                        <?php
                        for ($startYear = 2017; $startYear <= $currentYear; $startYear++) {
                            ?>
                            <option  <?php if ($currentYear == $startYear) { echo "selected"; } ?> value='{{$startYear}}'>{{$startYear}}</option>
                        <?php } ?>

                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Region</label>
                        <select class="commoSelect" name="region_id" id="region_id">
                            <option value=''>Select Region</option>
                            @foreach ($regions as $region)
                            <option value='{{ $region->id }}'>{{$region->region_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div> 
            </div>


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
                                        <em>Branch Code</em>
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

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="quarterSale" checked="" type="checkbox">
                                        <span></span>
                                        <em>Quarter Sale</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="quarterTarget" checked="" type="checkbox">
                                        <span></span>
                                        <em>Quarter Target</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="salesp" checked="" type="checkbox">
                                        <span></span>
                                        <em>Sales %</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="varience" checked="" type="checkbox">
                                        <span></span>
                                        <em>Variance</em>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                         <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="variencep" checked="" type="checkbox">
                                        <span></span>
                                        <em>Variance %</em>
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
                <div class="listerType1 cash_collections"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Branch Code</td>
                                <td>Branch Name</td>
                                <td>Quarter Sale</td>
                                <td>Quarter Target</td>
                                <td>Sales %</td>
                                <td>Variance</td>
                                <td>Variance %</td>

                            </tr>

                        </thead>
                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">


                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Branch Code">
                                            <input type="hidden" id="" >
                                        </div>
                                    </div>
                                </td>


                                <td class="filterFields">
                                    <div class="custCol-12">
                                        <select class="branch_id" name="branch_id" id="branch_id">
                                            <option value="">All</option>
                                            @foreach ($branches as $branch)
                                            <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="filterFields"></td>
                                <td class="filterFields"></td>
                                <td class="filterFields"></td>
                                <td class="filterFields"></td>
                                <td class="filterFields"></td>
                            </tr>

                        </thead>

                        <tbody class="variancereport" id='variancereport'>
                            @include('finance/salesvariance_report/sales_result')
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

</div>
<script>

    $('#region_id').on('change', function () {

        search();
    });

    $('#cmbquarter').on('change', function () {

        search();
    });

    $('#cmbyear').on('change', function () {

        search();
    });

    $('#branch_id').on("change", function () {

        search();

    });

    $('#searchbycode').bind('keyup', function () {
        search();
    });


    $('#page-limit').on("change", function () {
        search();
    });


    $(function () {
        $('.commonLoaderV1').hide();

    });

    function search()
    {

        var region_id = $('#region_id').val();
        var branch_id = $('#branch_id').val();
        var cmbquarter = $('#cmbquarter').val();
        var cmbyear = $('#cmbyear').val();
        var searchbycode = $('#searchbycode').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'sales_variance_report',
            data: {region_id: region_id, quarter: cmbquarter, year: cmbyear, branch_id: branch_id, branch_code: searchbycode, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.variancereport').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.variancereport').html('<p class="noData">No Records Found</p>');
                }
            }
        });


    }


    $("#reset").click(function () {
        window.location.href = '{{url("finance/sales_variance_report")}}';
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
