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
            
        if(!$("#slnoo").is(':checked')&&!$("#ledgerCode").is(':checked')&&!$("#ledgerName").is(':checked')&&!$("#startDatee").is(':checked')&&!$("#endDatee").is(':checked')&&!$("#amountt").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#slnoo").is(':checked')){
                strStyle+=' .led_slno {display:none;}';
         }
         if(!$("#ledgerCode").is(':checked')){
                strStyle+=' .led_code {display:none;}';
         }
         if(!$("#ledgerName").is(':checked')){
               strStyle+=' .led_name {display:none;}';
         }
         if(!$("#startDatee").is(':checked')){
              strStyle+=' .led_start {display:none;}';  
         } 
         if(!$("#endDatee").is(':checked')){
              strStyle+=' .led_end {display:none;}';   
         }
         if(!$("#amountt").is(':checked')){
               strStyle+=' .led_amount {display:none;}';   
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Ledger Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
						'<td style="padding:10px 0;color:#fff;" class="led_slno"> Sl.No.</td>'+
						'<td style="padding:10px 0;color:#fff;" class="led_code"> Ledger Code</td>'+
						'<td style="padding:10px 0;color:#fff;" class="led_name"> Ledger Name</td>'+
						'<td style="padding:10px 0;color:#fff;" class="led_start"> Budget Start Date </td>'+
						'<td style="padding:10px 0;color:#fff;" class="led_end"> Budget End Date </td>'+
						'<td style="padding:10px 0;color:#fff;" class="led_amount"> Budget Amount </td>'+
					'</tr>'+
				'</thead>'+ $('.tblledgerreport')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
       //    $('.saveDoc').click(function () {
//      
//       svepdf();
//       
//        
//    });
});

function getData(page){
        var sorting = $('#sortsimp').val();
        var cashsorting = $('#cashsorting').val();
        
        var searchkey = $('#search').val();
        var startdatefrom = $('#start_date_from').val();
        var enddatefrom = $('#end_date_from').val();
        var startdateto = $('#start_date_to').val();
        var enddateto = $('#end_date_to').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        var ldgordname = $('#ldgordname').val();

        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {ldgordname:ldgordname,searchkey: searchkey, sorting: sorting,cashsorting:cashsorting,corder: corder,camount: camount, startdatefrom: startdatefrom, enddatefrom: enddatefrom,startdateto: startdateto, enddateto: enddateto, pagelimit: pagelimit, searchable: searchable},
            
            // {
            //     you can show your loader 
            // }
        })
        .done(function(data)
        {
            $(".tblledgerreport").empty().html(data);
            location.hash = page;
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              alert('No response from server');
        });
}


  </script>

<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('mis/ledger_report/exporttopdf') }}" method="post">
    <header class="pageTitle">
        <h1>Ledger <span>Report</span></h1>
    </header>	
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>

    <div class="printChoose">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="slnoo" checked="" type="checkbox">
                                        <span></span>
                                        <em>Sl No.</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
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
                                        <input id="ledgerName" checked="" type="checkbox">
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
                                        <input id="startDatee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Budget Start Date</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="endDatee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Budget End Date</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="amountt" checked="" type="checkbox">
                                        <span></span>
                                        <em>Budget Amount</em>
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
    
    <div class="listHolderType1">

        <div class="listerType1 reportLister"> 

            <input type="hidden" value="" id="sortsimp" name="sortsimp">
            <input type="hidden" value="" id="cashsorting" name="cashsorting">
             <input type="hidden" value="" id="ldgordname" name="ldgordname">
           
            <div id="tblledgerreporttable">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        
                        <td>
                            Sl.No.
                        </td>
                        <td>
                                Ledger Code
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp ldgup"></a>
                                    <a href="javascript:void(0)" class="btnDown ldgdown"></a>
                                </div>
                            </td>
                        <td>
                            Ledger Name
                            <div class="sort">
                                <a href="#" class="btnUp sortup"></a>
                                <a href="#" class="btnDown sortdown"></a>
                            </div>
                        </td>
                        
                        <td>
                            Budget Start Date
                        </td>
                         <td>
                            Budget End Date
                        </td>
                       
                        <td>
                            Budget Amount
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
                        
                       
                        <td></td> 
                        <td></td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search" name="searchkey" placeholder="Enter Ledger Name">
                                </div>
                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <input type="text" id="start_date_from" name="start_date_from" value="" placeholder="From ">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="end_date_from" name="end_date_from" value="" placeholder="To ">
                                </div>

                            </div>
                        </td>
                         <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <input type="text" id="start_date_to" name="start_date_to" value="" placeholder="From ">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="end_date_to" name="end_date_to" value="" placeholder="To ">
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
              
                <tbody class="tblledgerreport" id='tblledgerreport'>
                   @include('mis/ledger_report/ledger_report_result')
                   
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
     $(".ldgup").on('click', function () {
        $('#ldgordname').val('ASC');
        search();
    });
    $(".ldgdown").on('click', function () {
        $('#ldgordname').val('DESC');
        search();
    });
    
    $(".casdown").on('click', function () {
        $('#cashsorting').val('ASC');
        search1();
    });
    $(".cashup").on('click', function () {
        $('#cashsorting').val('DESC');
        search1();
    });
    
    
    $('#search').bind('keyup', function () {
        search();
    });
    $('#verifiedby').bind('keyup', function () {
        search();
    });
    
    
     $('.corder').on("change", function () {
         var camount = $('#camount').val();
        if ($('.corder').val() !== '' &&  $.isNumeric(camount))
        {
            search();
        }
    });
    
    $('#page-limit').on("change", function () {
        search();
    });
    
    $('#start_date_from').on("change", function () {
        if ($('#start_date_from').val() !== '')
        {

            search();
        }
    });
    $('#end_date_from').on("change", function () {
        if ($('#end_date_from').val() !== '')
        {

            search();
        }
    });
    
    $('#start_date_to').on("change", function () {
        if ($('#start_date_to').val() !== '')
        {

            search();
        }
    });
    $('#end_date_to').on("change", function () {
        if ($('#end_date_to').val() !== '')
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
       
    });
    
    
    function search()
    {
        
        var sorting = $('#sortsimp').val();
        $('#cashsorting').val('');
        var ldgordname = $('#ldgordname').val();
       
        var searchkey = $('#search').val();
        var startdatefrom = $('#start_date_from').val();
        var enddatefrom = $('#end_date_from').val();
        var startdateto = $('#start_date_to').val();
        var enddateto = $('#end_date_to').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'ledger_report',
            data: {ldgordname:ldgordname, searchkey: searchkey,sorting: sorting,corder: corder, camount: camount, startdatefrom: startdatefrom, enddatefrom: enddatefrom, startdateto: startdateto, enddateto: enddateto, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                   // alert(return_data);
                    $('.tblledgerreport').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.tblledgerreport').html('<tr><td colspan="5"><p >No Records Found</p></tr></td>');
                }
            }
        });
    }
    
    function search1()
    {
        
        $('#sortsimp').val('');
        var cashsorting = $('#cashsorting').val();
        var ldgordname = $('#ldgordname').val();
       
        var searchkey = $('#search').val();
        var startdatefrom = $('#start_date_from').val();
        var enddatefrom = $('#end_date_from').val();
        var startdateto = $('#start_date_to').val();
        var enddateto = $('#end_date_to').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'ledger_report',
            data: {ldgordname:ldgordname,searchkey: searchkey,cashsorting:cashsorting,corder: corder, camount: camount, startdatefrom: startdatefrom, enddatefrom: enddatefrom, startdateto: startdateto, enddateto: enddateto, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                   // alert(return_data);
                    $('.tblledgerreport').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.tblledgerreport').html('<tr><td colspan="5"><p >No Records Found</p></tr></td>');
                }
            }
        });
    }
    
    

    $(function () {
        $('.commonLoaderV1').hide();
        $("#start_date_from").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $("#end_date_from").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $("#start_date_to").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $("#end_date_to").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        
        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

            $('#sortsimp').val('');
            $('#cashsorting').val('');
            $('#ldgordname').val('');
            
            $('#search').val('');
            $('#camount').val('');
            $('.corder').val('');
            $('#start_date_from').val('');
            $('#end_date_from').val('');
            $('#start_date_to').val('');
            $('#end_date_to').val('');
            
            
            //search();
            window.location.href = '{{url("mis/ledger_report")}}';
        });
    });
    
    function savetopdf()
    {
        
         document.getElementById("pdfgenerator").submit();


    }
</script>
@endsection