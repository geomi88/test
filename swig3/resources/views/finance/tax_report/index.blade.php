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
            
        if(!$("#branchCode").is(':checked')&&!$("#branchName").is(':checked')&&!$("#totalSale").is(':checked')&&!$("#netSale").is(':checked')&&!$("#taxAmount").is(':checked')) {
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
         if(!$("#totalSale").is(':checked')){
               strStyle+=' .total_sale {display:none;}';
         }
         if(!$("#netSale").is(':checked')){
              strStyle+=' .net_sale {display:none;}';  
         } 
         if(!$("#taxAmount").is(':checked')){
              strStyle+=' .tax_amount {display:none;}';   
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Tax Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_code"> Branch Code</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_name"> Branch Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="total_sale"> Total Sale</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="net_sale"> Net Sale</td>'+
                                           '<td style="padding:10px 0;color:#fff;" class="tax_amount"> Tax Amount</td>'+
                                           '</tr>'+
				'</thead>'+ $('.pos')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
    });

    function getData(page) {


        var region_id = $('#region_id').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {region_id: region_id, start_date: start_date, end_date: end_date, pagelimit: pagelimit, searchable: searchable},
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
<div class="contentArea">

    <div class="innerContent">

        <form id="pdfgenerator" action="{{ url('finance/exporttaxreport') }}" method="post">
            <div class="leftSection">
                <div class="dates_div custCol-12">
                    <div class="custCol-3">
                        <div class="inputHolder">
                            <label>From</label>
                            <input type="text" name="start_date" id="start_date" value="{{$periodStartDate}}" readonly="readonly">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-3">
                        <div class="inputHolder">
                            <label>To</label>
                            <input  type="text" name="end_date" id="end_date" value="{{$periodEndDate}}" readonly="readonly">
                            <span class="commonError"></span>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="inputHolder">
                            <label>Region</label>
                            <select class="commoSelect" name="region_id" id="region_id">
                                <option value=''>Select Region</option>
                                @foreach ($regions as $region)
                                <option <?php echo ($region->id == $region_id) ? "selected" : "" ?>  value='{{ $region->id }}'>{{$region->region_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div> 
                </div>
            </div>
        
        <header class="pageTitle">
            <h1>Tax <span>Report</span></h1>
        </header>
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf('PDF')">PDF</a>-->
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
                                        <input id="totalSale" checked="" type="checkbox">
                                        <span></span>
                                        <em>Total Sale</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="netSale" checked="" type="checkbox">
                                        <span></span>
                                        <em>Net Sale</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="taxAmount" checked="" type="checkbox">
                                        <span></span>
                                        <em>Tax Amount</em>
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
                            <td>Total Sale</td>
                            <td>Net Sale</td>
                            <td>Tax Amount</td>

                        </tr>

                    </thead>
                    <thead class="listHeaderBottom">
                
                    <tr class="headingHolder">
                        
                       
                        <td>
                            <div class="custCol-12">
                                <select class="branch_id" name="branch_id" id="branch_id">
                                    <option value="">All</option>
                                    @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}">{{$branch->branch_code}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        
                        
                        <td>
                            <div class="custCol-12">
                                <select class="branch_code" name="branch_code" id="branch_code">
                                    <option value="">All</option>
                                    @foreach ($branches as $branch)
                                    <option value="{{$branch->id}}">{{$branch->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                      
                </thead>

                    <tbody class="pos" id='pos'>
                        @include('finance/tax_report/sales_result')



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

    $('#start_date').on("change", function () {
       
        if ($('#start_date').val() !== '')
        {

            search();
        }
    });
    
    $('#end_date').on("change", function () {

        search();

    });
    
    $('#branch_id').on("change", function () {

        search();

    });
    
    $('#branch_code').on("change", function () {

        search();

    });


    $('#page-limit').on("change", function () {
        search();
    });


    $(function () {
            $('.commonLoaderV1').hide();
            $("#start_date").datepicker({
                changeMonth: true,
                changeYear: true, dateFormat: 'dd-mm-yy',
                onSelect: function (selected) {
                    $("#end_date").datepicker("option", "minDate", selected);
                    search();
                }
            });
            $("#end_date").datepicker({
                changeMonth: true,
                changeYear: true, dateFormat: 'dd-mm-yy',
                onSelect: function (selected) {
                    $("#start_date").datepicker("option", "maxDate", selected);
                    search();
                }
            });



        });
    
    function search()
    {

        var region_id = $('#region_id').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var branch_id = $('#branch_id').val();
        var branch_code = $('#branch_code').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'tax_report',
            data: {region_id: region_id, start_date: start_date, end_date: end_date, pagelimit: pagelimit, searchable: searchable, branch_id: branch_id, branch_code: branch_code},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.pos').html('<p class="noData">No Records Found</p>');
                }
            }
        });


    }


    $("#reset").click(function () {
        window.location.href = '{{url("finance/tax_report")}}';


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