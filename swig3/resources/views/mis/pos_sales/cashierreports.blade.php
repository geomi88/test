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
            
        if(!$("#date").is(':checked')&&!$("#cashierName").is(':checked')&&!$("#bName").is(':checked')&&!$("#sName").is(':checked')&&!$("#cashColl").is(':checked')&&!$("#tipColl").is(':checked')&&!$("#opAmt").is(':checked')&&!$("#supName").is(':checked')&&!$("#editedBy").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#date").is(':checked')){
                strStyle+=' .pos_date {display:none;}';
         }
         if(!$("#cashierName").is(':checked')){
                strStyle+=' .pos_cashier {display:none;}';
         }
         if(!$("#bName").is(':checked')){
               strStyle+=' .pos_branch {display:none;}';
         }
         if(!$("#sName").is(':checked')){
              strStyle+=' .pos_shift {display:none;}';  
         } 
         if(!$("#cashColl").is(':checked')){
              strStyle+=' .pos_cash {display:none;}';   
         }
         if(!$("#tipColl").is(':checked')){
               strStyle+=' .pos_tip {display:none;}';   
         }
         if(!$("#opAmt").is(':checked')){
             strStyle+=' .pos_amt {display:none;}';     
         }
         if(!$("#supName").is(':checked')){
             strStyle+=' .pos_supervisor {display:none;}';     
         }
         if(!$("#editedBy").is(':checked')){
             strStyle+=' .pos_editor {display:none;}';     
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>POS Cashier Sales Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
						'<td style="padding:10px 0;color:#fff;" class="pos_date"> Date</td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_cashier"> Cashier Name</td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_branch"> Branch Name </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_shift"> Shift Name </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_cash"> Cash Collection </td>'+
                                                '<td style="padding:10px 0;color:#fff;" class="pos_tip"> Tips Collection </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_amt"> Opening Amount </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_supervisor"> Supervisor Name </td>'+
                                                '<td style="padding:10px 0;color:#fff;" class="pos_editor"> Edited By </td>'+
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
        var searchkey = $('#search').val();
        var sup_searchkey = $('#sup_search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var corder = $('.corder').val();
        var aorder = $('.aorder').val(); 
        var torder = $('.torder').val();         
        var aamount = $('#aamount').val();
        var tamount = $('#tamount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var camount = $('#camount').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        var osort=$('#osort').val();
        var ssort=$('#ssort').val(); 
        var bsort=$('#bsort').val(); 
    
        var cashsort=$('#cashsort').val();
       var editedsort=$('#edited').val();
        var edit_searchkey = $('#edit_search').val();
        
        
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
          data: {edit_searchkey:edit_searchkey,editedsort:editedsort,bsort:bsort,branch: branch,sup_searchkey:sup_searchkey, searchkey: searchkey, sorting: sorting,aorder: aorder, aamount: aamount,corder: corder, shift: shift, camount: camount, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable,torder: torder, tamount: tamount, osort:osort,cashsort:cashsort,ssort:ssort},
             
            // {
            //     you can show your loader 
            // }
        })
        .done(function(data)
        {
            console.log(data);
            
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
    <form id="pdfgenerator" action="{{ url('mis/pos_sales/cashierreports/exporttopdf') }}" method="post">
    <header class="pageTitle">
        <h1>POS <span>Cashier Report</span></h1>
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
                                        <input id="cashierName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Cashier Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="bName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Branch Name</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="sName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Shift Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="cashColl" checked="" type="checkbox">
                                        <span></span>
                                        <em>Cash Collection</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="tipColl" checked="" type="checkbox">
                                        <span></span>
                                        <em>Tips Collection</em>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                         <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="opAmt" checked="" type="checkbox">
                                        <span></span>
                                        <em>Opening Amount</em>
                                    </label>
                                </div>
                            </div>
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="supName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Supervisor Name</em>
                                    </label>
                                </div>
                            </div>
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="editedBy" checked="" type="checkbox">
                                        <span></span>
                                        <em>Edited By</em>
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
        <div class="custRow">
            <div class="custCol-12">
                <h3>Branch Name</h3>
            </div>
        </div>
        <div class="listerType1 reportLister"> 

            <input type="hidden" value="" id="sortsimp" name="sortsimp">
            <input type="hidden" value="" id="cashsort" name="cashsort">
            <input type="hidden" value="" id="osort" name="osort">
            <input type="hidden" value="" id="bsort" name="bsort">            
            <input type="hidden" value="" id="ssort" name="ssort">
            <input type="hidden" value="" id="tot" name="tot">
            <input type="hidden" value="" id="tipssort" name="tipssort">
            <input type="hidden" value="" id="edited" name="edited">
           
            <div id="postable">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        
                        <td>
                            Start Date-End Date
                        </td>
                        <td>
                            Cashier Name
                            <div class="sort">
                                <a href="#" class="btnUp sortup"></a>
                                <a href="#" class="btnDown sortdown"></a>
                            </div>
                        </td>
                        <td>
                            Branch Name
                            <div class="sort">
                                <a href="#" class="btnUp bup"></a>
                                <a href="#" class="btnDown bdown"></a>
                            </div>
                        </td>
                        <td>
                            Shift Name
                        </td>
                        <td>
                            Cash Collection
                            <div class="sort">
                                <a href="#" class="btnUp cashup"></a>
                                <a href="#" class="btnDown casdown"></a>
                            </div>
                        </td> 
                        <td>
                            Tips Collection
                            <div class="sort">
                                <a href="#" class="btnUp tipsup"></a>
                                <a href="#" class="btnDown tipsdown"></a>
                            </div>
                        </td>
                        <td>
                            Opening Amount
                            <div class="sort">
                                <a href="#" class="btnUp oup"></a>
                                <a href="#" class="btnDown odown"></a>
                            </div>
                        </td>
                        <td>
                            Supervisor Name
                            <div class="sort">
                                <a href="#" class="btnUp sup"></a>
                                <a href="#" class="btnDown sdown"></a>
                            </div>
                        </td>
                        <td>
                            Edited By
                            <div class="sort">
                                <a href="#" class="btnUp editup"></a>
                                <a href="#" class="btnDown editdown"></a>
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
                                    <input type="text" id="start_date" name="start_date" value="">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="end_date" name="end_date" value="">
                                </div>

                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search" name="searchkey">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="custCol-12">
                                <select class="branch" name="branch">
                                    <option value="">All</option>
                                    @foreach ($branch_names as $branch_name)
                                    <option value="{{$branch_name->branch_id}}">{{$branch_name->branch_code}}-{{$branch_name->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td class="">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <select class="shift" name="shift">
                                        <option value="">All</option>
                                        @foreach ($shift_names as $shift_name)
                                        <option value="{{$shift_name->jobshift_id}}">{{$shift_name->jobshift_name}}</option>
                                        @endforeach
                                    </select>

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
                                    <input type="text" id="camount" name="camount" >
                                </div>

                            </div>
                        </td>
                        
                         <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="torder" name="torder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="tamount" name="tamount">
                                </div>

                            </div>
                        </td>
                        <td class="filterFields">
                           <div class="custRow">
                                <div class="custCol-6">
                                    <select class="aorder" name="aorder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="aamount" name="aamount">
                                </div>

                            </div>
                        </td>

                        <td class="filterFields">
                            <div class="custRow">
                                  <div class="custCol-12">
                                    <input type="text" id="sup_search" name="sup_searchkey">
                                </div>

                            </div>
                        </td>
                        
                          <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="edit_search" name="edit_searchkey">
                                </div>
                            </div>
                        </td>
                        <td></td>
                    </tr>
                      
                </thead>
              
                <tbody class="pos" id='pos'>
                   @include('mis/pos_sales/cashier_report_result')
                   
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
    $(".oup").on('click', function () {
        $('#osort').val('ASC');
        search();
    });
    $(".odown").on('click', function () {
        $('#osort').val('DESC');
        search();
    }); 
    $(".editup").on('click', function () {
        $('#edited').val('ASC');
        search();
    });
    $(".editdown").on('click', function () {
        $('#edited').val('DESC');
        search();
    });
     $(".sup").on('click', function () {
        $('#ssort').val('ASC');
        search();
    });
    $(".sdown").on('click', function () {
        $('#ssort').val('DESC');
        search();
    });
    $(".sortup").on('click', function () {
        $('#sortsimp').val('ASC');
        search();
    });
    $(".sortdown").on('click', function () {
        $('#sortsimp').val('DESC');
        search();
    });
    $(".casdown").on('click', function () {
        $('#cashsort').val('DESC');
        search();
    });
    $(".cashup").on('click', function () {
        $('#cashsort').val('ASC');
        search();
    });
    $(".bup").on('click', function () {
        //alert('in');
        $('#bsort').val('ASC');
        search();
    });
    $(".bdown").on('click', function () {
        $('#bsort').val('DESC');
        search();
    });
    $('#search').bind('keyup', function () {
        search();
    });
    $('#sup_search').bind('keyup', function () {
        search();
    }); 
    $('#edit_search').bind('keyup', function () {
        search();
    });
    $('.branch').on("change", function () {
        search();
    });
    $('.shift').on("change", function () {
        search();
    });
    $('.corder').on("change", function () {
         var camount = $('#camount').val();
        if ($('.corder').val() !== '' && $.isNumeric(camount))
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
     $('.torder').on("change", function () {
         var tamount = $('#tamount').val();
        if ($('.torder').val() !== ''&& $.isNumeric(tamount))
        {

            search();
        }
    });
    $('#page-limit').on("change", function () {
        // alert('dasd');
        search();

    });
    $('.cashorder').on("change", function () {
        if ($('.cashorder').val() !== '')
        {

            search();
        }
    });
    $('.difforder').on("change", function () {
        if ($('.difforder').val() !== '')
        {

            search();
        }
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
     $('#tamount').bind('keyup', function () {
         var torder = $('.torder').val();
         var tamount = $('#tamount').val();
         if(torder!="" &&  $.isNumeric(tamount)){
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
    $('#aamount').bind('keyup', function () {
        var aorder = $('.aorder').val();
         var aamount = $('#aamount').val();
         if(aorder!=""  && $.isNumeric(aamount)){
            search();
            }
        
        
       // search();
    });
    $('#cashamount').bind('keyup', function () {
        search();
    });
    $('#diffamount').bind('keyup', function () {
        search();
    });
    
    
    $(".tipsup").on('click', function () {
        $('#tipssort').val('ASC');
        search5();
    });
    $(".tipsdown").on('click', function () {
        $('#tipssort').val('DESC');
        search5();
    });
    
    function search()
    {
        // alert('asd');
        var sorting = $('#sortsimp').val();
        var searchkey = $('#search').val();
        var sup_searchkey = $('#sup_search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var corder = $('.corder').val();
        var aorder = $('.aorder').val(); 
        var torder = $('.torder').val();         
        var aamount = $('#aamount').val();
        var tamount = $('#tamount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var camount = $('#camount').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        var osort=$('#osort').val();
        var ssort=$('#ssort').val(); 
        var bsort=$('#bsort').val(); 
    
        var cashsort=$('#cashsort').val();
       var editedsort=$('#edited').val();
        var edit_searchkey = $('#edit_search').val();
        
       // alert(editedsort);
        // alert(startdate)alert(enddate);

        $.ajax({
            type: 'POST',
            url: 'cashierreports',
            data: {edit_searchkey:edit_searchkey,editedsort:editedsort,bsort:bsort,branch: branch,sup_searchkey:sup_searchkey, searchkey: searchkey, sorting: sorting,aorder: aorder, aamount: aamount,corder: corder, shift: shift, camount: camount, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable,torder: torder, tamount: tamount, osort:osort,cashsort:cashsort,ssort:ssort},
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
        //alert('asd');
        var searchable = 'YES';
        var cashsorting = $('#cashsort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var sup_searchkey = $('#sup_search').val();
        var aorder = $('.aorder').val();        
        var aamount = $('#aamount').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        var torder = $('.torder').val();        
        var tamount = $('#tamount').val();
        $.ajax({
            type: 'POST',
            url: 'cashierreports',
            data: {branch: branch,sup_searchkey:sup_searchkey, searchkey: searchkey,aorder: aorder, aamount: aamount, corder: corder, camount: camount,torder: torder, tamount: tamount, cashsorting: cashsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $('.pos').html('<p class="noData">No Records Found</p>');
                    $(".commonLoaderV1").hide();
                }
            }
        });



    }

    function search2()
    {
        //alert('asd');
        var searchable = 'YES';
        var sup_searchkey = $('#sup_search').val();
        var shift = $('.shift').val();
        var sorting = $('#sortsimp').val();
        var cashsorting = $('#cashsort').val();
        var bsorting = $('#bsort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var aorder = $('.aorder').val();        
        var aamount = $('#aamount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        var torder = $('.torder').val();        
        var tamount = $('#tamount').val();
        if (camount != '' && corder != '')
        {
            var corder = $('.corder').val();
            var camount = $('#camount').val();
        }
        $.ajax({
            type: 'POST',
            url: 'cashierreports',
            data: {branch: branch,sup_searchkey:sup_searchkey, searchkey: searchkey,aorder: aorder, aamount: aamount, corder: corder, camount: camount,torder: torder, tamount: tamount, bsorting: bsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $('.pos').html('<p class="noData">No Records Found</p>');
                    $(".commonLoaderV1").hide();
                }
            }
        });



    }

    function search3()
    {
        //alert('asd');
        var searchable = 'YES';
        var shift = $('.shift').val();
        var totsorting = $('#tot').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var aorder = $('.aorder').val();        
        var aamount = $('#aamount').val();
        var corder = $('.corder').val();
        var sup_searchkey = $('#sup_search').val();
        var camount = $('#camount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        var torder = $('.torder').val();
        var tamount = $('#tamount').val();
//        if (amount != '' && order != '')
//        {
//            var order = $('.order').val();
//            var amount = $('#amount').val();
//        }
        $.ajax({
            type: 'POST',
            url: 'cashierreports',
            data: {branch: branch,sup_searchkey:sup_searchkey, searchkey: searchkey, totsorting: totsorting,aorder: aorder, aamount: aamount, corder: corder, camount: camount,torder: torder, tamount: tamount, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $('.pos').html('<p class="noData">No Records Found</p>');
                    $(".commonLoaderV1").hide();
                }
            }
        });



    }


    function search4()
    {
        //alert('asd');
        var searchable = 'YES';
        var shift = $('.shift').val();
        var osorting = $('#osort').val();
        var sup_searchkey = $('#sup_search').val();
        var ssorting = $('#ssort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var aorder = $('.aorder').val();        
        var aamount = $('#aamount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var torder = $('.torder').val();
        var tamount = $('#tamount').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();

        $.ajax({
            type: 'POST',
            url: 'cashierreports',
            data: {branch: branch,sup_searchkey:sup_searchkey, searchkey: searchkey, corder: corder, camount: camount,aorder: aorder, aamount: aamount,torder: torder, tamount: tamount, osorting: osorting, ssorting: ssorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $('.pos').html('<p class="noData">No Records Found</p>');
                    $(".commonLoaderV1").hide();
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
            $('#cashsort').val('');
            $('#diffsort').val('');
            $('#bsort').val('');
            $('#tot').val('');
            $('#search').val('');
            $('.branch').val('');
            $('.corder').val('');
            $('.shift').val('');
            $('.cashorder').val('');
            $('.difforder').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $("#end_date").datepicker({
                changeMonth: true,
                changeYear: true, dateFormat: 'yy-mm-dd'
            }).datepicker("setDate", new Date());
            //search();
            window.location.href = '{{url("mis/pos_sales/cashierreports")}}';
        });
    });
    
    function search5()
    {
        //alert('asd');
        var searchable = 'YES';
        var tipssorting = $('#tipssort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var sup_searchkey = $('#sup_search').val();
        var aorder = $('.aorder').val();        
        var aamount = $('#aamount').val();
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        var torder = $('.torder').val();
        var tamount = $('#tamount').val();

        $.ajax({
            type: 'POST',
            url: 'cashierreports',
            data: {branch: branch,sup_searchkey:sup_searchkey, searchkey: searchkey,aorder: aorder, aamount: aamount, corder: corder, camount: camount,torder: torder, tamount: tamount, tipssorting: tipssorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $('.pos').html('<p class="noData">No Records Found</p>');
                    $(".commonLoaderV1").hide();
                }
            }
        });



    }
    
    function savetopdf()
    {
        
         document.getElementById("pdfgenerator").submit();


    }
</script>



@endsection