@extends('layouts.main')
@section('content')
<script>
 $(window).on('hashchange', function() {
        if (window.location.hash) {
            var page = window.location.hash.replace('#', '');
            if (page == Number.NaN || page <= 0) {
                return false;
            }else{
                getData(page);
            }
        }
    });
$(document).ready(function()
{
     $(document).on('click', '.pagination a',function(event)
    {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        //var myurl = $(this).attr('href');
       var page=$(this).attr('href').split('page=')[1];
       getData(page);
    });
    
     $('.takePrint').click(function () {
            
        if(!$("#date").is(':checked')&&!$("#depositedBy").is(':checked')&&!$("#depositorName").is(':checked')&&!$("#verifiedbyy").is(':checked')&&!$("#bankName").is(':checked')&&!$("#referenceNo").is(':checked')&&!$("#amount").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#date").is(':checked')){
                strStyle+=' .verifiy_date {display:none;}';
         }
         if(!$("#depositedBy").is(':checked')){
                strStyle+=' .verify_depo_by {display:none;}';
         }
         if(!$("#depositorName").is(':checked')){
               strStyle+=' .verify_depo_name {display:none;}';
         }
         if(!$("#verifiedbyy").is(':checked')){
              strStyle+=' .verify_by {display:none;}';  
         } 
         if(!$("#bankName").is(':checked')){
              strStyle+=' .verify_bank {display:none;}';   
         }
         if(!$("#referenceNo").is(':checked')){
               strStyle+=' .verify_reference {display:none;}';   
         }
         if(!$("#amount").is(':checked')){
             strStyle+=' .verify_amount {display:none;}';     
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Verified Accounts Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
						'<td style="padding:10px 0;color:#fff;" class="verifiy_date"> Date</td>'+
						'<td style="padding:10px 0;color:#fff;" class="verify_depo_by"> Deposited By</td>'+
						'<td style="padding:10px 0;color:#fff;" class="verify_depo_name"> Depositor Name</td>'+
						'<td style="padding:10px 0;color:#fff;" class="verify_by"> Verified  By </td>'+
						'<td style="padding:10px 0;color:#fff;" class="verify_bank"> Bank Name </td>'+
						'<td style="padding:10px 0;color:#fff;" class="verify_reference"> Reference No </td>'+
						'<td style="padding:10px 0;color:#fff;" class="verify_amount"> Amount </td>'+
					'</tr>'+
				'</thead>'+ $('.pos')[0].outerHTML +'</table>');  
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
    
       $('.saveDoc').click(function () {
      
//       svepdf();
       
        
    });
});

function getData(page){
        var sorting = $('#sortsimp').val();
        var vsorting = $('#vsorting').val();
        var bsort = $('#bsort').val();
        var cashsorting = $('#cashsorting').val();
        
        var searchkey = $('#search').val();
        var verifiedsearchkey = $('#verifiedby').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var bank = $('.bank').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var ref_searchkey = $('#ref_search').val();
        var searchable = 'YES';

        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {bank: bank,verifiedsearchkey:verifiedsearchkey,ref_searchkey:ref_searchkey, searchkey: searchkey, sorting: sorting,vsorting:vsorting,bsort:bsort,cashsorting:cashsorting,corder: corder,camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
            
            // {
            //     you can show your loader 
            // }
        })
        .done(function(data)
        {
            $(".pos").empty().html(data);
            location.hash = page;
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              alert('No response from server');
        });
}


  </script>

<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('mis/verified_accounts/exporttopdf') }}" method="post">
    <header class="pageTitle">
        <h1>Verified <span>Accounts</span> With <span>Bank</span></h1>
    </header>	
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>
    <!--<a class="btnAction savedirectDoc bgBlue" href="{{ route('export',['download'=>'pdf','searchkey'=>'']) }}">Save</a>-->
    <!--<a class="btnAction savedirectDoc bgBlue"  id="btnExport" onclick="savetopdf()">Save</a>-->
    
                     <div class="printChoose">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="date" checked="" type="checkbox">
                                        <span></span>
                                        <em>Date</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="depositedBy" checked="" type="checkbox">
                                        <span></span>
                                        <em>Deposited By</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="depositorName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Depositor Name</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="verifiedbyy" checked="" type="checkbox">
                                        <span></span>
                                        <em>Verified By</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="bankName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Bank Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="referenceNo" checked="" type="checkbox">
                                        <span></span>
                                        <em>Reference No</em>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                         <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="amount" checked="" type="checkbox">
                                        <span></span>
                                        <em>Amount</em>
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

            <input type="hidden" value="" id="sortsimp" name="sortsimp">
            <input type="hidden" value="" id="vsorting" name="vsorting">
            <input type="hidden" value="" id="bsort" name="bsort">            
            <input type="hidden" value="" id="cashsorting" name="cashsorting">
            
            <div id="postable">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        
                        <td>
                            Start Date-End Date
                        </td>
                         <td>
                            Depositor By
                           
                        </td>
                        <td>
                            Depositor Name
                            <div class="sort">
                                <a href="#" class="btnUp sortup"></a>
                                <a href="#" class="btnDown sortdown"></a>
                            </div>
                        </td>
                        <td>
                            Verified By
                            <div class="sort">
                                <a href="#" class="btnUp vup"></a>
                                <a href="#" class="btnDown vdown"></a>
                            </div>
                        </td>
                         <td>
                            Bank Name
                            <div class="sort">
                                <a href="#" class="btnUp bup"></a>
                                <a href="#" class="btnDown bdown"></a>
                            </div>
                        </td>
                        <td>
                            Reference No
                        </td>
                        <td>
                            Amount
                            <div class="sort">
                                <a href="#" class="btnUp cashup"></a>
                                <a href="#" class="btnDown casdown"></a>
                            </div>
                        </td>
                        
                        <td>
                            Action
                        </td>
                    </tr>
                </thead>

                <thead class="listHeaderBottom">
                
                    <tr class="headingHolder">
                        
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <input type="text" id="start_date" name="start_date" value="" placeholder="Start Date">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="end_date" name="end_date" value="">
                                </div>

                            </div>
                        </td>
                        <td class="filterFields">
                            
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search" name="searchkey" placeholder="Depositor Name">
                                </div>
                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="verifiedby" name="verifiedby" placeholder="Verified By">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="custCol-12">
                                <select class="bank" name="bank">
                                    <option value="">All</option>
                                    @foreach ($bank_names as $bank_name)
                                    <option value="{{$bank_name->id}}">{{$bank_name->bank_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="ref_search" name="ref_search" placeholder="Enter Reference No.">
                                </div>
                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="corder" name="corder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="camount" name="camount" placeholder="Amount">
                                </div>

                            </div>
                        </td>
                        
                        <td></td>
                    </tr>
                      
                </thead>
              
                <tbody class="pos" id='pos'>
                   @include('mis/verified_accounts/verified_accounts_result')
                   
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
    
     
    $(".sortup").on('click', function () {
        $('#sortsimp').val('ASC');
        search();
    });
    $(".sortdown").on('click', function () {
        $('#sortsimp').val('DESC');
        search();
    });
    $(".vup").on('click', function () {
        $('#vsorting').val('ASC');
        search();
    });
    $(".vdown").on('click', function () {
        $('#vsorting').val('DESC');
        search();
    });
    $(".bup").on('click', function () {
        $('#bsort').val('ASC');
        search();
    });
    $(".bdown").on('click', function () {
        $('#bsort').val('DESC');
        search();
    });
    
    $(".casdown").on('click', function () {
        $('#cashsorting').val('ASC');
        search();
    });
    $(".cashup").on('click', function () {
        $('#cashsorting').val('DESC');
        search();
    });
    
    
    $('#search').bind('keyup', function () {
        search();
    });
    $('#verifiedby').bind('keyup', function () {
        search();
    });
    $('#ref_search').bind('keyup', function () {
        search();
    });
    $('.bank').on("change", function () {
        search();
    });
    
    
    
    $('#page-limit').on("change", function () {
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
  
    
   
      $('.corder').on("change", function () {
         var camount = $('#camount').val();
        if ($('.corder').val() !== '' && $.isNumeric(camount))
        {

            search();
        }
    });
    
    $('#camount').bind('keyup', function () {
        var corder = $('.corder').val();
         var camount = $('#camount').val();
         if(corder!=""  && $.isNumeric(camount)){
            search();
            }
        
        
       // search();
    });
    
    
    function search()
    {
         var searchkey = $('#search').val();
         var ref_searchkey = $('#ref_search').val();
         var verifiedsearchkey = $('#verifiedby').val();
         var sorting = $('#sortsimp').val();
         var vsorting =  $('#vsorting').val();
         var bsort =  $('#bsort').val();
         var cashsorting =  $('#cashsorting').val();
         var bank =  $('.bank').val();
         var corder = $('.corder').val();
         var camount = $('#camount').val();
         var startdate = $('#start_date').val();
         var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
       
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'verified_accounts',
            data: {bank: bank,ref_searchkey:ref_searchkey, searchkey: searchkey,verifiedsearchkey:verifiedsearchkey, sorting: sorting,corder: corder, camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable,vsorting:vsorting},
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
    
    function search1()
    {
        
        $('#sortsimp').val('');
        $('#vsorting').val('');
        $('#bsort').val('');
        var cashsorting = $('#cashsorting').val();
        
        var searchkey = $('#search').val();
        var verifiedsearchkey = $('#verifiedby').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var bank = $('.bank').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var ref_searchkey = $('#ref_search').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'verified_accounts',
            data: {bank: bank,ref_searchkey:ref_searchkey, searchkey: searchkey,verifiedsearchkey:verifiedsearchkey,cashsorting:cashsorting,corder: corder, camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
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
    
    function search2()
    {
        
        $('#sortsimp').val('');
        $('#vsorting').val('');
        $('#cashsorting').val('');
        var bsort = $('#bsort').val();
        
        var searchkey = $('#search').val();
        var verifiedsearchkey = $('#verifiedby').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var bank = $('.bank').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var ref_searchkey = $('#ref_search').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'verified_accounts',
            data: {bank: bank,sup_searchkey:ref_searchkey, searchkey: searchkey,verifiedsearchkey:verifiedsearchkey,bsort:bsort,corder: corder, camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
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
    
    function search3()
    {
       
        var vsorting = $('#vsorting').val();
        $('#sortsimp').val('');
        $('#bsort').val('');
        $('#cashsorting').val('');
        
        var searchkey = $('#search').val();
        var verifiedsearchkey = $('#verifiedby').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var bank = $('.bank').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var ref_searchkey = $('#ref_search').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'verified_accounts',
            data: {bank: bank,ref_searchkey:ref_searchkey, searchkey: searchkey,verifiedsearchkey:verifiedsearchkey,vsorting:vsorting,corder: corder, camount: camount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
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
        
        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

            $('#sortsimp').val('');
            $('#cashsorting').val('');
            $('#vsorting').val('');
            $('#bsort').val('');
            
            $('#search').val('');
            $('#verifiedby').val('');
            $('#camount').val('');
            $('.bank').val('');
            $('.corder').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            
            $("#end_date").datepicker({
                changeMonth: true,
                changeYear: true, dateFormat: 'dd-mm-yy'
            }).datepicker("setDate", new Date());
            //search();
            window.location.href = '{{url("mis/verified_accounts")}}';
        });
    });
    
    function savetopdf()
    {
        
         document.getElementById("pdfgenerator").submit();


    }
</script>



@endsection