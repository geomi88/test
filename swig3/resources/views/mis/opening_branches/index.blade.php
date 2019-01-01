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
            
        if(!$("#codee").is(':checked')&&!$("#branchNamee").is(':checked')&&!$("#startDat").is(':checked')&&!$("#areaa").is(':checked')&&!$("#regionn").is(':checked')&&!$("#aliasName").is(':checked')&&!$("#addresss").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#codee").is(':checked')){
                strStyle+=' .new_code {display:none;}';
         }
         if(!$("#branchNamee").is(':checked')){
                strStyle+=' .new_name {display:none;}';
         }
         if(!$("#startDat").is(':checked')){
               strStyle+=' .new_date {display:none;}';
         }
         if(!$("#areaa").is(':checked')){
              strStyle+=' .new_area {display:none;}';  
         } 
         if(!$("#regionn").is(':checked')){
              strStyle+=' .new_region {display:none;}';   
         }
         if(!$("#aliasName").is(':checked')){
               strStyle+=' .new_alias {display:none;}';   
         }
         if(!$("#addresss").is(':checked')){
             strStyle+=' .new_address {display:none;}';     
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Newly Opening Branches</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="new_code"> Code </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="new_name"> Branch Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="new_date"> Start Date</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="new_area"> Area </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="new_region"> Region </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="new_alias"> Alias Name </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="new_address"> Address </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.reportbody')[0].outerHTML +'</table>');
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });

      });
    
    function getData(page) {
        var search_key = $('#search_key').val();
        var searchbyarea = $('#searchbyarea').val();
        var searchbycode = $('#searchbycode').val();
        
        var searchbyregion = $('.searchbyregion').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        
        var sortordname=$('#sortordname').val();
        var sortordarea =$('#sortordarea').val();
        var sortordcode =$('#sortordcode').val();
        var sortorddate = $('#sortorddate').val();
        var sortordregion = $('#sortordregion').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        
        $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                data: {search_key: search_key, searchbyarea: searchbyarea, searchbycode: searchbycode,searchbyregion:searchbyregion,start_date:start_date,end_date:end_date,sortordname:sortordname,sortordarea:sortordarea,sortordcode:sortordcode,sortorddate:sortorddate,sortordregion:sortordregion,pagelimit: pagelimit, searchable: searchable},
                // {
                //     you can show your loader 
                // }
            })
            .done(function (data)
            {
                console.log(data);

                $(".reportbody").empty().html(data);
                location.hash = page;
            })
            .fail(function (jqXHR, ajaxOptions, thrownError)
            {
                alert('No response from server');
            });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('mis/opening_branches/exporttopdf') }}" method="post">
    <header class="pageTitle">
        <h1>Newly Opening<span> Branches</span></h1>
    </header>
    

    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>

                     <div class="printChoose">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="codee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Code</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="branchNamee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Branch Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="startDat" checked="" type="checkbox">
                                        <span></span>
                                        <em>Start Date</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="areaa" checked="" type="checkbox">
                                        <span></span>
                                        <em>Area</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="regionn" checked="" type="checkbox">
                                        <span></span>
                                        <em>Region</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="aliasName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Alias Name</em>
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                         <div class="custRow">
                             <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="addresss" checked="" type="checkbox">
                                        <span></span>
                                        <em>Address</em>
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

            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
            <input type="hidden" value="" id="sortordname" name="sortordname">
            <input type="hidden" value="" id="sortordarea" name="sortordarea">
            <input type="hidden" value="" id="sortordcode" name="sortordcode">
            <input type="hidden" value="" id="sortordregion" name="sortordregion">
            <input type="hidden" value="" id="sortorddate" name="sortorddate">
            
            <div id="tblcategorytable">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td >
                                Code
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp codeup"></a>
                                    <a href="javascript:void(0)" class="btnDown codedown"></a>
                                </div>
                            </td>
                            <td>
                                Branch Name
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp branchup"></a>
                                    <a href="javascript:void(0)" class="btnDown branchdown"></a>
                                </div>
                            </td>
                            <td>
                                Start Date
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp dateup"></a>
                                    <a href="javascript:void(0)" class="btnDown datedown"></a>
                                </div>
                            </td>
                            <td>
                                Area
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp areaup"></a>
                                    <a href="javascript:void(0)" class="btnDown areadown"></a>
                                </div>
                            </td>

                            
                            <td>
                                Region
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp regionup"></a>
                                    <a href="javascript:void(0)" class="btnDown regiondown"></a>
                                </div>
                            </td>
                             <td>
                                Alias Name
                                
                            </td>
                             <td>
                                Address
                                
                            </td>
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">
                            <td class="filterFields" style="min-width: 120px;">
                                <div class="custCol-12">
                                    <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Code">
                                </div>
                            </td>
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <input type="text" id="search_key" name="search_key" placeholder="Enter Name">
                                </div>
                            </td>
                            
                            <td class="filterFields clsdate">
                               
                                <div class="custCol-6">
                                    <input type="text" id="start_date" name="start_date" value="" placeholder="From ">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="end_date" name="end_date" value="" placeholder="To ">
                                </div>
                                 
                            </td>
                            
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbyarea" name="searchbyarea" id="searchbyarea">
                                        <option value="">All</option>
                                        @foreach ($areas as $area)
                                        <option value="{{$area->id}}">{{$area->area_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>

                            
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbyregion" name="searchbyregion">
                                        <option value="">All</option>
                                        @foreach ($regions as $region)
                                        <option value="{{$region->id}}">{{$region->region_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            
                            <td class="">
                            </td>
                            
                            <td class="filterFields">
                            </td>

                        </tr>

                    </thead>

                    <tbody class="reportbody" id='reportbody'>
                        @include('mis/opening_branches/result')
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
    
    $('#search_key').bind('keyup', function () {
            search();
    });
    
    $('#searchbyarea').on('change', function () {
        search();
    });

    $('#searchbycode').bind('keyup', function () {
        search();
    });
        
    $('.searchbyregion').on("change", function () {
            search();
    });

    
    $('#start_date').on("change", function () {
            search();
    });
    
    $('#end_date').on("change", function () {
            search();
    });
    
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        $('#sortordarea').val('');
        $('#sortordcode').val('');
        
        $('#sortordregion').val('');
        $('#sortorddate').val('');
        search();
    });

    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        $('#sortordarea').val('');
        $('#sortordcode').val('');
        
        $('#sortordregion').val('');
        $('#sortorddate').val('');
        search();
    });

    $(".areaup").on('click', function () {
        $('#sortordname').val('');
        $('#sortordarea').val('ASC');
        $('#sortordcode').val('');
        
        $('#sortordregion').val('');
        $('#sortorddate').val('');
        search();
    });

    $(".areadown").on('click', function () {
        $('#sortordname').val('');
        $('#sortordarea').val('DESC');
        $('#sortordcode').val('');
        
        $('#sortordregion').val('');
        $('#sortorddate').val('');
        search();
    });

    $(".codeup").on('click', function () {
        $('#sortordname').val('');
        $('#sortordarea').val('');
        $('#sortordcode').val('ASC');
        
        $('#sortordregion').val('');
        $('#sortorddate').val('');
        search();
    });
        
    $(".codedown").on('click', function () {
       $('#sortordname').val('');
       $('#sortordarea').val('');
       $('#sortordcode').val('DESC');
       
        $('#sortordregion').val('');
        $('#sortorddate').val('');
       search();
   });
   
    $(".regionup").on('click', function () {
        $('#sortordregion').val('ASC');
        $('#sortorddate').val('');
        
        $('#sortordname').val('');
       $('#sortordarea').val('');
       $('#sortordcode').val('');
        search();
    });
    
    $(".regiondown").on('click', function () {
        $('#sortordregion').val('DESC');
        $('#sortorddate').val('');
        
        $('#sortordname').val('');
       $('#sortordarea').val('');
       $('#sortordcode').val('');
        search();
    });
    
    
    
    $(".dateup").on('click', function () {
        $('#sortorddate').val('ASC');
        $('#sortordregion').val('');
        
         $('#sortordname').val('');
       $('#sortordarea').val('');
       $('#sortordcode').val('');
        search();
    });
    
    $(".datedown").on('click', function () {
        $('#sortorddate').val('DESC');
        $('#sortordregion').val('');
        
         $('#sortordname').val('');
       $('#sortordarea').val('');
       $('#sortordcode').val('');
        search();
    });
    
   
    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {
        var search_key = $('#search_key').val();
        var searchbyarea = $('#searchbyarea').val();
        var searchbycode = $('#searchbycode').val();
        
        var searchbyregion = $('.searchbyregion').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        
        var sortordname=$('#sortordname').val();
        var sortordarea =$('#sortordarea').val();
        var sortordcode =$('#sortordcode').val();
        var sortorddate = $('#sortorddate').val();
        var sortordregion = $('#sortordregion').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'opening_branches',
            data: {search_key: search_key, searchbyarea: searchbyarea, searchbycode: searchbycode,searchbyregion:searchbyregion,start_date:start_date,end_date:end_date,sortordname:sortordname,sortordarea:sortordarea,sortordcode:sortordcode,sortorddate:sortorddate,sortordregion:sortordregion,pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                
                if (return_data != '')
                {
                    $('.reportbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.reportbody').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
     $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            onSelect: function(selected) {
            $("#end_date").datepicker("option","minDate", selected)
             search();
            }
        });
        $("#end_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy',
        });
        
    $(function () {

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');
            
            $('#search_key').val('');
            $('#searchbyarea').val('');
            $('#searchbycode').val('');
            $('#sortorddate').val('');
            $('#sortordregion').val('');
            
            $('#page-limit').val(10);
            //search();
            window.location.href = '{{url("mis/opening_branches")}}';
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
