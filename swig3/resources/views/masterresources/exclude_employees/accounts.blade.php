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

        $('.print').click(function () {
           
                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');
//        win.document.write('<style>.headingHolder{color:blue;}</style><html><head><title>' + pageTitle + '</title>' +
//            '<link rel="stylesheet" href="' + stylesheet + '">' +
//            '</head><body>' + $('.pos')[0].outerHTML + '</body></html>');
        win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>'+
	
			'<div style="text-align:center;"><h1>Top Cashier Cash Collections</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
						'<td style="padding:10px 0;color:#fff;"> Date</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Employee Code</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Deposited By</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Depositor Name</td>'+
                                           '<td style="padding:10px 0;color:#fff;"> Bank</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Reference No:</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Amount </td>'+
                                            '<td style="padding:10px 0;color:#fff;"></td>'+
                                            
                                        '</tr>'+
				'</thead>'+ $('.pos')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
        });
    });
    
    function getData(page) {


        var searchkey = $('#search').val();
        var aorder = $('.aorder').val(); 
        var aamount = $('#aamount').val();
        var bank_id = $('#bank_id').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        // alert(startdate)alert(enddate);

        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                   data: { searchkey: searchkey,bank_id: bank_id,aorder: aorder, aamount: aamount , startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
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
        
        <form id="pdfgenerator" action="{{ url('branchsales/cash_collection/exportaccounts') }}" method="post">
 
         <header class="pageTitle">
        <h1>Accounts <span>Reconciliation</span></h1>
    </header>
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf('PDF')">PDF</a>
 <a class="btnAction saveDoc bgOrange" href="#" onclick="savetopdf('EXCEL')">Excel</a>

            <div class="listHolderType1 ">
                 <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
                <div class="listerType1 cash_collections"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Date</td>
                                <td>Employee Code</td>
                                <td>Deposited By</td>
                                <td>Depositor Name</td>
                                <td>Bank</td>
                                <td>Reference Number</td>
                                <td>Amount</td>
                                <td>Verify</td>
                                <td>Action</td>
                                
                            </tr>
                             <tr class="headingHolder">
                         
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <input type="text" id="start_date" name="start_date" value="">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="end_date" name="end_date" value="">
                                </div>

                            </div>
                        </td>
                       <!-- <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search" name="searchkey">
                                </div>
                            </div>
                        </td>-->
                        <td class="filterFields">
                             </td>
                              <td class="filterFields">
                            
                        </td>
                       
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search" name="searchkey">
                                </div>
                            </div>
                        </td>
                       
                       
                       <td class="">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                             <select name="bank_id" id="bank_id">
                                        <option value=''>Select Bank</option>
                                        @foreach ($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->name}}</option>
                                        @endforeach
                                    </select>

                                        </div>
                                    </div>
                                </td>
                                 

                        <td></td>
                         <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="aorder" name="aorder" id="aorder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="aamount" name="aamount" >
                                </div>

                            </div>
                        </td>
                     

                        <td >
                             </td>
                        <td></td>
                    </tr>
                        </thead>
                        
                            
                            <tbody class="pos" id='pos'>
                             @include('branchsales/cash_collection/accounts_result')
                           
                       
                            
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
            

        </section>
    </div>
</div>
<script>
    $(function () {        
       $('.cash_collections').on("click", "a.fund_id", function () {
           
        var fund_id = $(this).attr("value");
            $.ajax({
                type: 'POST',
                url: '../cash_collection/changeverificationstatus',
                data: {fund_id:fund_id},
                async: false,
                cache: false,
                timeout: 30000,
                success: function (return_data) {
                    $('.cash_collections').html('');
                    $('.cash_collections').html(return_data);
                     window.location.href = '{{url("branchsales/cash_collection/accounts")}}';
                   
                }
            });
            
        
    });
        
        
        
    });
    
  $(function () {
        $('.commonLoaderV1').hide();
        $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $("#end_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        }).datepicker("setDate", new Date());
  
  
            
    });
    
    $('#search').bind('keyup', function () {
        search();
    });
      $('#start_date').on("change", function () {
        if ($('#start_date').val() !== '')
        {

            search();
        }
    });
    $('#end_date').on("change", function () {
        if ($('#end_date').val() !== '')
        {

            search();
        }
    });
    $('.aorder').on("change", function () {
         var aamount = $('#aamount').val();
        if ($('.aorder').val() !== '' && $.isNumeric(aamount))
        {

            search();
        }
    });
    
    $('#aamount').bind('keyup', function () {
        var aorder = $('.aorder').val();
         var aamount = $('#aamount').val();
         if(aorder!=""  && $.isNumeric(aamount)){
            search();
            }
        
        
       // search();
    });
        $('#bank_id').on("change", function () {
        search();
    });
    
     $('#page-limit').on("change", function () {
        search();
    });

     function search()
    {
         

        var searchkey = $('#search').val();
        var aorder = $('.aorder').val(); 
        var aamount = $('#aamount').val();
        var bank_id = $('#bank_id').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        // alert(startdate)alert(enddate);

        $.ajax({
            type: 'POST',
            url: 'accounts',
            data: { searchkey: searchkey,bank_id: bank_id,aorder: aorder, aamount: aamount , startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
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
 
 
    $("#reset").click(function(){
        window.location.href = '{{url("branchsales/cash_collection/accounts")}}';
               
       
    });
    
    
     function savetopdf(strType){
      
         if(strType=="PDF"){
            $('#excelorpdf').val('PDF');
        }else{
            $('#excelorpdf').val('EXCEL');
        }    
        
         document.getElementById("pdfgenerator").submit();

 }
  
</script>
@endsection
