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
            
        if(!$("#branchCode").is(':checked')&&!$("#namee").is(':checked')&&!$("#tarQuarter").is(':checked')&&!$("#tarAmount").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#branchCode").is(':checked')){
                strStyle+=' .tar_code {display:none;}';
         }
         if(!$("#namee").is(':checked')){
                strStyle+=' .tar_name {display:none;}';
         }
         if(!$("#tarQuarter").is(':checked')){
               strStyle+=' .tar_quarter {display:none;}';
         }
         if(!$("#tarAmount").is(':checked')){
              strStyle+=' .tar_amount {display:none;}';  
         } 
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Sales Target</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="tar_code"> Branch Code</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="tar_name"> Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="tar_quarter"> Target Quarter </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="tar_amount"> Target Amount </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.tbltarget')[0].outerHTML +'</table>');  
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
    });
    
    function getData(page) {
    
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var searchbyname = $('#searchbyname').val();
        var searchbycode = $('#searchbycode').val();
        var searchbyquarter = $('.searchbyquarter').val();
        var sortordname = $('#sortordname').val();
        var sortordcode=$('#sortordcode').val();
        var sortordquarter=$('#sortordquarter').val();
        var sortordamount=$('#sortordamount').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbyname: searchbyname,searchbycode:searchbycode,searchbyquarter:searchbyquarter,corder: corder, camount: camount,sortordname:sortordname,sortordcode:sortordcode,sortordquarter:sortordquarter,sortordamount:sortordamount, searchable: searchable,pagelimit: pagelimit},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".tbltarget").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('kpi/sales_target/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Sales <span>Target</span></h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-12">
                <a href="{{ action('Kpi\SalestargetController@add') }}" class="right commonBtn commonBtn bgGreen addBtn">Set Target</a>
            </div>
        </div>
        <div class="customClear"></div>
    </div>
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>

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
                                        <input id="namee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="tarQuarter" checked="" type="checkbox">
                                        <span></span>
                                        <em>Target Quarter</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="tarAmount" checked="" type="checkbox">
                                        <span></span>
                                        <em>Target Amount</em>
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
            <input type="hidden" value="" id="sortordname" name="sortordname">
            <input type="hidden" value="" id="sortordcode" name="sortordcode">
            <input type="hidden" value="" id="sortordquarter" name="sortordquarter">
            <input type="hidden" value="" id="sortordamount" name="sortordamount">
            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
            
            <div id="">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

                            <td>
                                Branch Code
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp codeup"></a>
                                    <a href="javascript:void(0)" class="btnDown codedown"></a>
                                </div>
                            </td>
                            <td>
                                Name
                                 <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp nameup"></a>
                                    <a href="javascript:void(0)" class="btnDown namedown"></a>
                                </div>
                            </td>
                            <td>
                                Target Quarter
                                 <div class="sort">
                                    <a href="#" class="btnUp quarterup"></a>
                                    <a href="#" class="btnDown quarterdown"></a>
                                </div>
                            </td>
                            <td>
                                Target Amount
                                 <div class="sort">
                                    <a href="#" class="btnUp amountup"></a>
                                    <a href="#" class="btnDown amountdown"></a>
                                </div>
                            </td>
<!--                            <td>
                                
                            </td>-->
                            
                        </tr>
                    </thead>
                    
                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">

                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Code">
                                    </div>
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbyname" name="searchbyname" placeholder="Enter Name">
                                    </div>
                                </div>
                            </td>

                           <td>
                                <div class="custCol-12">
                                    <select class="searchbyquarter" name="searchbyquarter">
                                        <option value="">All</option>
                                        <?php 
                                            $currentYear=date('Y')+1;
                                            for($startYear=2017;$startYear<=$currentYear;$startYear++){
                                        ?>
                                                <option value="1-{{$startYear}}">Q1-{{$startYear}}</option>
                                                <option value="2-{{$startYear}}">Q2-{{$startYear}}</option>
                                                <option value="3-{{$startYear}}">Q3-{{$startYear}}</option>
                                                <option value="4-{{$startYear}}">Q4-{{$startYear}}</option>
                                        <?php }?>
                                    </select>
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

                        </tr>

                    </thead>
                    
                    <tbody class="tbltarget" id='tbltarget'>
                        @include('kpi/sales_target/result')
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


    $('#searchbyname').bind('keyup', function () {
        search();
    });
    
    $('#searchbycode').bind('keyup', function () {
        search();
    });
    
    $('.searchbyquarter').on("change", function () {
        search();
    });
    
    $('.corder').on("change", function () {
        if ($('.corder').val() !== '') {
            search();
        }
    });
    
    $('#camount').bind('keyup', function () {
        if ($('.corder').val() !== '') {
            search();
        }
    });
    
    $(".codeup").on('click', function () {
        $('#sortordcode').val('ASC');
        search1();
    });
    $(".codedown").on('click', function () {
        $('#sortordcode').val('DESC');
        search1();
    });
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        search();
    });
    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        search();
    });
    
    $(".quarterup").on('click', function () {
        $('#sortordquarter').val('ASC');
        search2();
    });
    $(".quarterdown").on('click', function () {
        $('#sortordquarter').val('DESC');
        search2();
    });
    
    $(".amountup").on('click', function () {
        $('#sortordamount').val('ASC');
        search3();
    });
    $(".amountdown").on('click', function () {
        $('#sortordamount').val('DESC');
        search3();
    });
    


    function search()
    {
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var searchbyname = $('#searchbyname').val();
        var searchbycode = $('#searchbycode').val();
        var searchbyquarter = $('.searchbyquarter').val();
        var sortordname = $('#sortordname').val();
        $('#sortordcode').val('');
        $('#sortordquarter').val('');
        $('#sortordamount').val('');
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'sales_target',
            data: {searchbyname: searchbyname,searchbycode:searchbycode,searchbyquarter:searchbyquarter,corder: corder, camount: camount,sortordname:sortordname, searchable: searchable,pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.tbltarget').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.tbltarget').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
    function search1()
    {
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var searchbyname = $('#searchbyname').val();
        var searchbycode = $('#searchbycode').val();
        var searchbyquarter = $('.searchbyquarter').val();
        var sortordcode = $('#sortordcode').val();
        $('#sortordname').val('');
        $('#sortordquarter').val('');
        $('#sortordamount').val('');
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'sales_target',
            data: {searchbyname: searchbyname,searchbycode:searchbycode,searchbyquarter:searchbyquarter,corder: corder, camount: camount,sortordcode:sortordcode, searchable: searchable,pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.tbltarget').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.tbltarget').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
    function search2()
    {
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var searchbyname = $('#searchbyname').val();
        var searchbycode = $('#searchbycode').val();
        var searchbyquarter = $('.searchbyquarter').val();
        var sortordquarter = $('#sortordquarter').val();
        $('#sortordcode').val('');
        $('#sortordname').val('');
        $('#sortordamount').val('');
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'sales_target',
            data: {searchbyname: searchbyname,searchbycode:searchbycode,searchbyquarter:searchbyquarter,corder: corder, camount: camount,sortordquarter:sortordquarter, searchable: searchable,pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.tbltarget').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.tbltarget').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
    function search3()
    {
        var corder = $('.corder').val();
        var camount = $('#camount').val();
        var searchbyname = $('#searchbyname').val();
        var searchbycode = $('#searchbycode').val();
        var searchbyquarter = $('.searchbyquarter').val();
        var sortordamount = $('#sortordamount').val();
        $('#sortordcode').val('');
        $('#sortordquarter').val('');
        $('#sortordname').val('');
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'sales_target',
            data: {searchbyname: searchbyname,searchbycode:searchbycode,searchbyquarter:searchbyquarter,corder: corder, camount: camount,sortordamount:sortordamount, searchable: searchable,pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.tbltarget').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.tbltarget').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }

    $(function () {

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

            $('#searchbyname').val('');
            $('#searchbycode').val('');
            $('#camount').val('');
            $('#sortordcode').val('');
            $('#sortordquarter').val('');
            $('#sortordamount').val('');
            $('#sortordname').val('');
            $('#page-limit').val(10);
            //search();
            window.location.href = '{{url("kpi/sales_target")}}';
        });
    });
    
    function funExportData(strType)
    {
        if(strType=="PDF"){
            $('#excelorpdf').val('PDF');
        }else{
            $('#excelorpdf').val('Excel');
        }    
        
        document.getElementById("pdfgenerator").submit();
    }
</script>
@endsection
