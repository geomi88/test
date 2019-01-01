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
            
        if(!$("#slno").is(':checked')&&!$("#ledgerCode").is(':checked')&&!$("#ledgername").is(':checked')&&!$("#totalamount").is(':checked')&&!$("#usedAmount").is(':checked')&&!$("#budgetvariance").is(':checked')&&!$("#usedPercent").is(':checked')&&!$("#variancePercent").is(':checked')) { 
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#slno").is(':checked')){
                strStyle+=' .sl_no {display:none;}';
         }
         if(!$("#ledgerCode").is(':checked')){
                strStyle+=' .ledger_code {display:none;}';
         }
         if(!$("#ledgername").is(':checked')){
                strStyle+=' .ledger_name {display:none;}';
         }
         if(!$("#totalamount").is(':checked')){
               strStyle+=' .total_amount {display:none;}';
         }
         if(!$("#usedAmount").is(':checked')){
              strStyle+=' .used_amount {display:none;}';  
         }
         if(!$("#usedPercent").is(':checked')){
              strStyle+=' .used_percent {display:none;}';  
         } 
         if(!$("#budgetvariance").is(':checked')){
              strStyle+=' .budget_variance {display:none;}';   
         }
         if(!$("#variancePercent").is(':checked')){
              strStyle+=' .variance_percent {display:none;}';   
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Budget Variance</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="sl_no"> Sl No.</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="ledger_code"> Ledger Code</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="ledger_name"> Ledger Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="total_amount" align="right"> Total Amount</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="used_amount" align="right"> Used Amount</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="used_percent"  align="right"> Used %</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="budget_variance" align="right"> Budget Variance </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="variance_percent"  align="right"> Variance % </td>'+
                                            '<td style="padding:10px 0;color:#fff;"></td>'+
                                            
                                        '</tr>'+
				'</thead>'+ $('.minsalesplan')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
        $('#ledger_id').change(function(){
            var ledger_name = $(this).val();
            if(ledger_name == 'Warehouse'||ledger_name == 'Office'){
                $('.ledger_code').hide();
            }else{
                $('.ledger_code').show();
            }
            if(ledger_name == ''){
                ledger_name = 'Ledger';
            }
            $('.ledg_code').html(ledger_name+' Code');
            $('.ledg_name').html(ledger_name+' Name');
            $('#searchbyname').attr('placeholder','Enter '+$(this).val()+' Name')
            search();
            
        });
        $('#cmbyear').change(function(){
            search();
            
        });
//        $('#ledger_id').trigger('change');
        
        $('#page-limit').on("change", function () {
            search();
        });
        $('#searchbyname').bind('keyup', function () {
            search();
        });
    });

    function getData(page) {

        var type = $('#ledger_id').val();
        var year = $('#cmbyear').val();
        var pagelimit = $('#page-limit').val();
        var searchbyname = $('#searchbyname').val();


        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {type: type,  year: year, pagelimit:pagelimit, searchbyname:searchbyname},
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
<div class="contentArea">

    <div class="innerContent">
        <header class="pageTitle" style="margin-bottom: 30px;padding-bottom: 10px;">
            <h1>Budget Variance</h1>
        </header>

        <form id="pdfgenerator" action="{{ url('finance/budget_plan/exportdata') }}" method="post">
            <div class="leftSection" style="padding-bottom: 15px;">

                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Year</label>
                        <select  name="cmbyear" id="cmbyear">

                        <?php
                        $currentYear = date('Y');
                        for ($startYear = $currentYear-1; $startYear <= $currentYear; $startYear++) {
                            ?>
                            <option  <?php if ($currentYear == $startYear) { echo "selected"; } ?> value='{{$startYear}}'>{{$startYear}}</option>
                        <?php } ?>

                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Select Ledger Type</label>
                        <select class="commoSelect" name="ledger_id" id="ledger_id">
                            <option value="">Select</option>
                            @foreach($cost_centres as $cost_centre)
                            <option value="{{$cost_centre}}">{{$cost_centre}}</option>
                            @endforeach
                            <option value="Asset">Asset</option>
                            <option value="Customer">Customer</option>
                            <option value="Employee">Employee</option>
                            <option value="Supplier">Supplier</option>
                            <option value="Inventory">Inventory</option>
                            <option value="General Ledger">General Ledger</option>
                        </select>
                    </div>
                </div> 
            </div>


            <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
            <a class="btnAction print bgGreen" href="#">Print</a>
            <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf('PDF')">PDF</a>-->
            <a class="btnAction saveDoc bgOrange" href="#" onclick="savetopdf('EXCEL')">Excel</a>

            <div class="printChoose">
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="slno" checked="" type="checkbox">
                                <span></span>
                                <em>Sl No.</em>
                            </label>
                        </div>
                    </div>

                    <div class="custCol-4 ledger_code">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="ledgerCode" checked="" type="checkbox">
                                <span></span>
                                <em>Ledger Code</em>
                            </label>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="ledgername" checked="" type="checkbox">
                                <span></span>
                                <em>Ledger Name</em>
                            </label>
                        </div>
                    </div>


                </div>

                <div class="custRow">

                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="totalamount" checked="" type="checkbox">
                                <span></span>
                                <em>Total Amount</em>
                            </label>
                        </div>
                    </div>

                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="usedAmount" checked="" type="checkbox">
                                <span></span>
                                <em>Used Amount</em>
                            </label>
                        </div>
                    </div>
                    
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="usedPercent" checked="" type="checkbox">
                                <span></span>
                                <em>Used %</em>
                            </label>
                        </div>
                    </div>

                    


                </div>
                
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="budgetvariance" checked="" type="checkbox">
                                <span></span>
                                <em>Budget Variance</em>
                            </label>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="commonCheckHolder checkRender">
                            <label>
                                <input id="variancePercent" checked="" type="checkbox">
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
                                <td>Sl No.</td>
                                <td class="ledg_code ledger_code">Ledger Code</td>
                                <td class="ledg_name">Ledger Name</td>
                                <td align="right">Total Amount</td>
                                <td align="right">Used Amount</td>
                                <td class="usedPercent"  align="right">Used %</td>
                                <td align="right">Budget Variance</td>
                                <td class="balancePercent"  align="right">Variance %</td>
                                
                                
                            </tr>

                        </thead>
                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">

                                <td ></td>
                                <td class="ledger_code"></td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <input type="text" id="searchbyname" name="searchbyname" placeholder="Enter Branch Code" autocomplete="off">
                                            <input type="hidden" id="" >
                                        </div>
                                    </div>
                                </td>
                                <td class="filterFields" style="min-width: 150px;"></td>
                                <td class="filterFields" style="min-width: 150px;"></td>
                                <td class="filterFields" style="min-width: 70px;"></td>
                                <td class="filterFields" style="min-width: 150px;"></td>
                                <td class="filterFields" style="min-width: 70px;"></td>
                            </tr>

                        </thead>

                        <tbody class="minsalesplan" id='minsalesplan'>
                            @include('finance/budget_plan/budget_result')
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

    

    function search()
    {

        var type = $('#ledger_id').val();
        var year = $('#cmbyear').val();
        var searchbyname = $('#searchbyname').val();
        var pagelimit = $('#page-limit').val();
        
        $.ajax({
            type: 'POST',
            url: 'budget_plan',
            data: {type: type, year: year, pagelimit: pagelimit, searchbyname:searchbyname},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {  console.log(return_data);
                if (return_data != -1 && return_data!= '')
                {
                    // alert(return_data);
                    $('.minsalesplan').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.minsalesplan').html('<tr><td colspan="2">No Records Found<td></tr>');
                }
            }
        });


    }


    $("#reset").click(function () {
        window.location.href = '{{url("finance/budget_plan")}}';
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
