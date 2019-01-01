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
            
        if(!$("#employeeCode").is(':checked')&&!$("#employeeName").is(':checked')&&!$("#branch").is(':checked')&&!$("#region").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#employeeCode").is(':checked')){
                strStyle+=' .mor_code {display:none;}';
         }
         if(!$("#employeeName").is(':checked')){
                strStyle+=' .mor_name {display:none;}';
         }
         if(!$("#branch").is(':checked')){
               strStyle+=' .mor_branch {display:none;}';
         }
         if(!$("#region").is(':checked')){
              strStyle+=' .mor_region {display:none;}';  
         } 
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Barista In Duty Morning Shift</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="mor_code"> Employee Code</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="mor_name"> Employee Name </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="mor_branch"> Branch </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="mor_region"> Region </td>'+
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

        var searchbycode = $('#searchbycode').val();
        var searchbyname = $('#searchbyname').val();
        var searchbybranch = $('.searchbybranch').val();
        var searchbyregion = $('.searchbyregion').val();
    
        var sortordbranch = $('#sortordbranch').val();
        var sortordcode = $('#sortordcode').val();
        var sortordregion = $('#sortordregion').val();
        var sortordname = $('#sortordname').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        
        $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                data: {searchbyregion:searchbyregion,searchbycode:searchbycode,searchbyname:searchbyname,searchbybranch:searchbybranch,sortordbranch:sortordbranch,sortordcode:sortordcode,sortordregion:sortordregion,sortordname:sortordname,pagelimit: pagelimit, searchable: searchable},
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
    <form id="pdfgenerator" action="{{ url('branchsales/barista_morning_shift/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Barista In Duty <span>Morning Shift</span></h1>
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
                                        <input id="employeeCode" checked="" type="checkbox">
                                        <span></span>
                                        <em>Employee Code</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="employeeName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Employee Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="branch" checked="" type="checkbox">
                                        <span></span>
                                        <em>Branch</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="region" checked="" type="checkbox">
                                        <span></span>
                                        <em>Region</em>
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
            <input type="hidden" value="" id="sortordcode" name="sortordcode">
            <input type="hidden" value="" id="sortordname" name="sortordname">
            <input type="hidden" value="" id="sortordbranch" name="sortordbranch">
            <input type="hidden" value="" id="sortordregion" name="sortordregion">
            
            <div id="tblcategorytable">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

                           
                            <td>
                                Employee Code
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp codeup"></a>
                                    <a href="javascript:void(0)" class="btnDown codedown"></a>
                                </div>
                            </td>
                            <td>
                                Employee Name
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp nameup"></a>
                                    <a href="javascript:void(0)" class="btnDown namedown"></a>
                                </div>
                            </td>
                            
                            <td>
                                Branch
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp branchup"></a>
                                    <a href="javascript:void(0)" class="btnDown branchdown"></a>
                                </div>
                            </td>
                            
                            <td>
                                Region
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp regionup"></a>
                                    <a href="javascript:void(0)" class="btnDown regiondown"></a>
                                </div>
                            </td>
                           
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
                                        <input type="text" id="searchbyname" name="searchbyname" placeholder="Enter Employee Name">
                                    </div>
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbybranch" name="searchbybranch">
                                        <option value="">All</option>
                                        @foreach ($allbranches as $branch)
                                        <option value="{{$branch->branch_id}}">{{$branch->code}} : {{$branch->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbyregion" name="searchbyregion">
                                        <option value="">All</option>
                                        @foreach ($allregions as $category)
                                        <option value="{{$category->region_id}}">{{$category->region_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>

                        </tr>

                    </thead>

                    <tbody class="reportbody" id='reportbody'>
                        @include('branchsales/barista_reports/morning_result')
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

    $('#searchbycode').bind('keyup', function () {
            search();
    });
    
    $('#searchbyname').bind('keyup', function () {
            search();
    });
    
    $('.searchbybranch').on("change", function () {
            search();
    });
    
    $('.searchbyregion').on("change", function () {
            search();
    });
    
    $(".nameup").on('click', function () {
        $('#sortordname').val('ASC');
        $('#sortordregion').val('');
        $('#sortordcode').val('');
        $('#sortordbranch').val('');
        search();
    });
    
    $(".namedown").on('click', function () {
        $('#sortordname').val('DESC');
        $('#sortordregion').val('');
        $('#sortordcode').val('');
        $('#sortordbranch').val('');
        search();
    });
    
    $(".branchup").on('click', function () {
        $('#sortordbranch').val('ASC');
        $('#sortordcode').val('');
        $('#sortordregion').val('');
        $('#sortordname').val('');
        search();
    });
    
    $(".branchdown").on('click', function () {
        $('#sortordbranch').val('DESC');
        $('#sortordcode').val('');
        $('#sortordregion').val('');
        $('#sortordname').val('');
        search();
    });
    
     $(".regionup").on('click', function () {
        $('#sortordregion').val('ASC');
        $('#sortordcode').val('');
        $('#sortordbranch').val('');
        $('#sortordname').val('');
        search();
    });
    
    $(".regiondown").on('click', function () {
        $('#sortordregion').val('DESC');
        $('#sortordcode').val('');
        $('#sortordbranch').val('');
        $('#sortordname').val('');
        search();
    });
    
    $(".codeup").on('click', function () {
        $('#sortordcode').val('ASC');
        $('#sortordbranch').val('');
        $('#sortordregion').val('');
        $('#sortordname').val('');
        search();
    });
    
    $(".codedown").on('click', function () {
        $('#sortordcode').val('DESC');
        $('#sortordbranch').val('');
        $('#sortordregion').val('');
        $('#sortordname').val('');
        search();
    });

    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {
        var searchbycode = $('#searchbycode').val();
        var searchbyname = $('#searchbyname').val();
        var searchbybranch = $('.searchbybranch').val();
        var searchbyregion = $('.searchbyregion').val();
    
        var sortordbranch = $('#sortordbranch').val();
        var sortordcode = $('#sortordcode').val();
        var sortordregion = $('#sortordregion').val();
        var sortordname = $('#sortordname').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'barista_morning_shift',
            data: {searchbyregion:searchbyregion,searchbycode:searchbycode,searchbyname:searchbyname,searchbybranch:searchbybranch,sortordbranch:sortordbranch,sortordcode:sortordcode,sortordregion:sortordregion,sortordname:sortordname,pagelimit: pagelimit, searchable: searchable},
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
        
    $(function () {

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');
            
            $('#sortordname').val('');
            $('#sortordbranch').val('');
            $('#sortordcode').val('');
            $('#sortordregion').val('');
            
            $('#page-limit').val(10);
            //search();
            window.location.href = '{{url("branchsales/barista_morning_shift")}}';
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