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
            
        if(!$("#titlee").is(':checked')&&!$("#employee").is(':checked')&&!$("#warningType").is(':checked')&&!$("#branchNamee").is(':checked')&&!$("#descriptionn").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#titlee").is(':checked')){
                strStyle+=' .check_title {display:none;}';
         }
         if(!$("#employee").is(':checked')){
                strStyle+=' .check_name {display:none;}';
         }
         if(!$("#warningType").is(':checked')){
               strStyle+=' .check_warning {display:none;}';
         }
         if(!$("#branchNamee").is(':checked')){
              strStyle+=' .check_branch {display:none;}';  
         } 
         if(!$("#descriptionn").is(':checked')){
              strStyle+=' .check_description {display:none;}';   
         }
        strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Warnings</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_title"> Title</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_name"> Employee</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_warning"> Warning Type</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_branch"> Branch </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_description"> Description </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.categorybody')[0].outerHTML +'</table>'); 
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
    });
    
    function getData(page) {

        var searchbytitle = $('#searchbytitle').val();
        var searchbybranch = $('.searchbybranch').val();
        var searchbyemployee = $('#searchbyemployee').val();
        var searchbytype = $('.searchbytype').val();
        
        var sortordtitle = $('#sortordtitle').val();
        var sortordbranch = $('#sortordbranch').val();
        var sortordemployee = $('#sortordemployee').val();
        var sortordtype = $('#sortordtype').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                data: { searchbytitle:searchbytitle,searchbybranch:searchbybranch,searchbytype:searchbytype,searchbyemployee:searchbyemployee,sortordtitle:sortordtitle,sortordbranch:sortordbranch,sortordemployee:sortordemployee,sortordtype:sortordtype,pagelimit: pagelimit, searchable: searchable},
                // {
                //     you can show your loader 
                // }
            })
            .done(function (data)
            {
                console.log(data);

                $(".categorybody").empty().html(data);
                location.hash = page;
            })
            .fail(function (jqXHR, ajaxOptions, thrownError)
            {
                alert('No response from server');
            });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('checklist/warnings/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Warnings</h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-12">
                <a href="{{ action('Checklist\WarningsController@add') }}" id="btnNew" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
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
                                        <input id="titlee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Title</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="employee" checked="" type="checkbox">
                                        <span></span>
                                        <em>Employee</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="warningType" checked="" type="checkbox">
                                        <span></span>
                                        <em>Warning Type</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
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
                                        <input id="descriptionn" checked="" type="checkbox">
                                        <span></span>
                                        <em>Description</em>
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
            <input type="hidden" value="" id="sortordtitle" name="sortordtitle">
            <input type="hidden" value="" id="sortordbranch" name="sortordbranch">
            <input type="hidden" value="" id="sortordemployee" name="sortordemployee">
            <input type="hidden" value="" id="sortordtype" name="sortordtype">
            
            <div id="tblcategorytable">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td>
                                Title
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp titleup"></a>
                                    <a href="javascript:void(0)" class="btnDown titledown"></a>
                                </div>
                            </td>
                            <td>
                                Employee
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp empup"></a>
                                    <a href="javascript:void(0)" class="btnDown empdown"></a>
                                </div>
                            </td>
                            <td>
                                Warning Type
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp typeup"></a>
                                    <a href="javascript:void(0)" class="btnDown typedown"></a>
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
                                Description
                            </td>
                           
                            
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">

                           <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbytitle" name="searchbytitle" placeholder="Enter Title">
                                    </div>
                                </div>
                            </td>
                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbyemployee" name="searchbyemployee" placeholder="Enter Employee Name">
                                    </div>
                                </div>
                            </td>
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbytype" name="searchbytype">
                                        <option value="">All</option>
                                        @foreach ($warning_types as $warning_type)
                                        <option value="{{$warning_type->id}}">{{$warning_type->name}}</option>
                                        @endforeach
                                    </select>
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
                            </td>

                        </tr>

                    </thead>

                    <tbody class="categorybody" id='categorybody'>
                        @include('checklist/warnings/result')

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

    $('#searchbytitle').bind('keyup', function () {
        search();
    });
    $('#searchbyemployee').bind('keyup', function () {
        search();
    });
    
    $('.searchbybranch').on("change", function () {
        search();
    });
    
    $('.searchbytype').on("change", function () {
            search();
    });
    
    $(".titleup").on('click', function () {
        $('#sortordtitle').val('ASC');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".titledown").on('click', function () {
        $('#sortordtitle').val('DESC');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".branchup").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('ASC');
        $('#sortordemployee').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".branchdown").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('DESC');
        $('#sortordemployee').val('');
        $('#sortordtype').val('');
        search();
    });
    
    $(".typeup").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordtype').val('ASC');
        search();
    });
    
    $(".typedown").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('');
        $('#sortordtype').val('DESC');
        search();
    });
    
    $(".empup").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('ASC');
        $('#sortordtype').val('');
        search();
    });
    
    $(".empdown").on('click', function () {
        $('#sortordtitle').val('');
        $('#sortordbranch').val('');
        $('#sortordemployee').val('DESC');
        $('#sortordtype').val('');
        search();
    });

    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {
        var searchbytitle = $('#searchbytitle').val();
        var searchbybranch = $('.searchbybranch').val();
        var searchbyemployee = $('#searchbyemployee').val();
        var searchbytype = $('.searchbytype').val();
        
        var sortordtitle = $('#sortordtitle').val();
        var sortordbranch = $('#sortordbranch').val();
        var sortordemployee = $('#sortordemployee').val();
        var sortordtype = $('#sortordtype').val();
        
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'warnings',
            data: { searchbytitle:searchbytitle,searchbybranch:searchbybranch,searchbytype:searchbytype,searchbyemployee:searchbyemployee,sortordtitle:sortordtitle,sortordbranch:sortordbranch,sortordemployee:sortordemployee,sortordtype:sortordtype,pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    // alert(return_data);
                    $('.categorybody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.categorybody').html('<p class="noData">No Records Found</p>');
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
            
            $('#sortordtitle').val('');
            $('#sortordbranch').val('');
            $('#sortordemployee').val('');
            $('#sortordtype').val('');
            
            $('#page-limit').val(10);
            //search();
            window.location.href = '{{url("checklist/warnings")}}';
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
