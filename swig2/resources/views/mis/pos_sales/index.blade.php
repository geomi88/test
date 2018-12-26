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
            
        if(!$("#supervisorName").is(':checked')&&!$("#branchName").is(':checked')&&!$("#shiftName").is(':checked')&&!$("#totalSale").is(':checked')&&!$("#cashCollection").is(':checked')&&!$("#difference").is(':checked')&&!$("#sd_ed").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#supervisorName").is(':checked')){
                strStyle+=' .pos_employee {display:none;}';
         }
         if(!$("#branchName").is(':checked')){
                strStyle+=' .pos_branch {display:none;}';
         }
         if(!$("#shiftName").is(':checked')){
               strStyle+=' .pos_jobshift {display:none;}';
         }
         if(!$("#totalSale").is(':checked')){
              strStyle+=' .pos_total {display:none;}';  
         } 
         if(!$("#cashCollection").is(':checked')){
              strStyle+=' .pos_cash {display:none;}';   
         }
         if(!$("#difference").is(':checked')){
              strStyle+=' .pos_diff {display:none;}';   
         }
         if(!$("#sd_ed").is(':checked')){
              strStyle+=' .pos_dat {display:none;}';   
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>POS Sales</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
						'<td style="padding:10px 0;color:#fff;" class="pos_employee"> Supervisor Name </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_branch"> Branch Name </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_jobshift"> Shift Name </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_total"> Total Sale </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_cash"> Cash Collection </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_diff"> Difference </td>'+
						'<td style="padding:10px 0;color:#fff;" class="pos_dat"> Date </td>'+
					'</tr>'+
				'</thead>'+ $('.pos')[0].outerHTML +'</table>');  
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
    
       $('.saveDoc').click(function () {
      
       svepdf();
       
        
    });
});
function getData(page){
    var sorting = $('#sortsimp').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var order = $('.order').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var amount = $('#amount').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var diffsorting=$('#diffsort').val();
        var bsorting = $('#bsort').val();
        var totsorting = $('#tot').val();
        // var sorting = $('#sortsimp').val();
        var cashsorting = $('#cashsort').val();
        var searchable = 'YES';
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {branch: branch, searchkey: searchkey, sorting: sorting, order: order, shift: shift, amount: amount, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable,diffsorting: diffsorting,cashsorting:cashsorting,bsorting:bsorting,totsorting:totsorting},
            
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

function svepdf(){
    var sorting = $('#sortsimp').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var order = $('.order').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var amount = $('#amount').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var diffsorting=$('#diffsort').val();
        var bsorting = $('#bsort').val();
        var totsorting = $('#tot').val();
        var download='pdf';
        // var sorting = $('#sortsimp').val();
        var cashsorting = $('#cashsort').val();
        var searchable = 'YES';
        $.ajax(
        {
            url: 'pos_sales/pdfs',
            type: "get",
            datatype: "html",
            data: {branch: branch, searchkey: searchkey, sorting: sorting, order: order, shift: shift, amount: amount, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable,diffsorting: diffsorting,cashsorting:cashsorting,bsorting:bsorting,totsorting:totsorting,download:download},
            
            // {
            //     you can show your loader 
            // }
        })
}

  </script>

<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('mis/pos_sales/exporttopdf') }}" method="post">
    <header class="pageTitle">
        <h1>POS <span>Sales Report</span></h1>
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
                                        <input id="supervisorName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Supervisor Name</em>
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
                                        <input id="shiftName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Shift Name</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="totalSale" checked="" type="checkbox">
                                        <span></span>
                                        <em>Total Sale</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="cashCollection" checked="" type="checkbox">
                                        <span></span>
                                        <em>Cash Collection</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="difference" checked="" type="checkbox">
                                        <span></span>
                                        <em>Difference</em>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                         <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="sd_ed" checked="" type="checkbox">
                                        <span></span>
                                        <em>Start Date-End Date</em>
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
            <input type="hidden" value="" id="diffsort" name="diffsort">
            <input type="hidden" value="" id="bsort" name="bsort">
            <input type="hidden" value="" id="tot" name="tot">
            <div id="postable">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        <td>
                            Supervisor Name
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
                            Total Sale
                            <div class="sort">
                                <a href="#" class="btnUp totup"></a>
                                <a href="#" class="btnDown totdown"></a>
                            </div>
                        </td>
                        <td>
                            Cash Collection
                            <div class="sort">
                                <a href="#" class="btnUp cashup"></a>
                                <a href="#" class="btnDown casdown"></a>
                            </div>
                        </td>
                        <td>
                            Difference
                            <div class="sort">
                                <a href="#" class="btnUp diffup"></a>
                                <a href="#" class="btnDown diffdown"></a>
                            </div>
                        </td>
                        <td>
                            Start Date-End Date
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
                                    <option value="{{$branch_name->branch_id}}">{{$branch_name->branch_name}}</option>
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
                                    <select class="order" name="order">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="amount" name="amount">
                                </div>

                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="cashorder" name="cashorder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="cashamount" name="cashamount">
                                </div>

                            </div>
                        </td>

                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="difforder" name="difforder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="diffamount" name="diffamount">
                                </div>

                            </div>
                        </td>
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
                        <td></td>
                    </tr>
                      
                </thead>
              
                <tbody class="pos" id='pos'>
                   @include('mis/pos_sales/searchresults')
                   
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
    $(".totup").on('click', function () {
        $('#tot').val('ASC');
      
        search3();
    });
    $(".totdown").on('click', function () {
        $('#tot').val('DESC');
        search3();
    });
    $(".diffup").on('click', function () {
        $('#diffsort').val('ASC');
        search4();
    });
    $(".diffdown").on('click', function () {
        $('#diffsort').val('DESC');
        search4();
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
        search1();
    });
    $(".cashup").on('click', function () {
        $('#cashsort').val('ASC');
        search1();
    });
    $(".bup").on('click', function () {
        //alert('in');
        $('#bsort').val('ASC');
        search2();
    });
    $(".bdown").on('click', function () {
        $('#bsort').val('DESC');
        search2();
    });
    $('#search').bind('keyup', function () {
        search();
    });
    $('.branch').on("change", function () {
        search();
    });
    $('.shift').on("change", function () {
        search();
    });
    $('.order').on("change", function () {
        if ($('.order').val() !== '')
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
    $('#amount').bind('keyup', function () {
        search();
    });
    $('#cashamount').bind('keyup', function () {
        search();
    });
    $('#diffamount').bind('keyup', function () {
        search();
    });
    function search()
    {
        // alert('asd');
        var sorting = $('#sortsimp').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var order = $('.order').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var amount = $('#amount').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        // alert(startdate)alert(enddate);

        $.ajax({
            type: 'POST',
            url: 'pos_sales',
            data: {branch: branch, searchkey: searchkey, sorting: sorting, order: order, shift: shift, amount: amount, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, pagelimit: pagelimit, searchable: searchable},
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
        var order = $('.order').val();
        var amount = $('#amount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();

        $.ajax({
            type: 'POST',
            url: 'pos_sales',
            data: {branch: branch, searchkey: searchkey, order: order, amount: amount, cashsorting: cashsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
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
        var shift = $('.shift').val();
        var sorting = $('#sortsimp').val();
        var cashsorting = $('#cashsort').val();
        var bsorting = $('#bsort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
        if (amount != '' && order != '')
        {
            var order = $('.order').val();
            var amount = $('#amount').val();
        }
        $.ajax({
            type: 'POST',
            url: 'pos_sales',
            data: {branch: branch, searchkey: searchkey, order: order, amount: amount, bsorting: bsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
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
        var order = $('.order').val();
        var amount = $('#amount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();
//        if (amount != '' && order != '')
//        {
//            var order = $('.order').val();
//            var amount = $('#amount').val();
//        }
        $.ajax({
            type: 'POST',
            url: 'pos_sales',
            data: {branch: branch, searchkey: searchkey, totsorting: totsorting, order: order, amount: amount, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
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
        var diffsorting = $('#diffsort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var order = $('.order').val();
        var amount = $('#amount').val();
        var pagelimit = $('#page-limit').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();

        $.ajax({
            type: 'POST',
            url: 'pos_sales',
            data: {branch: branch, searchkey: searchkey, order: order, amount: amount, diffsorting: diffsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate, searchable: searchable, pagelimit: pagelimit},
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
            $('.order').val('');
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
            window.location.href = '{{url("mis/pos_sales")}}';
        });
    });
    
    function savetopdf()
    {
        
         document.getElementById("pdfgenerator").submit();


    }
</script>



@endsection