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
            
        if(!$("#slno").is(':checked')&&!$("#branchName").is(':checked')&&!$("#areaName").is(':checked')&&!$("#branchCode").is(':checked')&&!$("#alias").is(':checked')&&!$("#branchPhone").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#slno").is(':checked')){
                strStyle+=' .branch_slno {display:none;}';
         }
         if(!$("#branchName").is(':checked')){
                strStyle+=' .branch_name {display:none;}';
         }
         if(!$("#areaName").is(':checked')){
               strStyle+=' .branch_area {display:none;}';
         }
         if(!$("#branchCode").is(':checked')){
              strStyle+=' .branch_code {display:none;}';  
         } 
         if(!$("#alias").is(':checked')){
              strStyle+=' .branch_alias {display:none;}';   
         }
         if(!$("#branchPhone").is(':checked')){
               strStyle+=' .branch_code {display:none;}';   
         }
        strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Branch List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_slno"> Sl.No.</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_name"> Branch Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_area"> Area Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_code"> Branch Code</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_alias"> Alias </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="branch_code"> Branch Phone </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.branchbody')[0].outerHTML +'</table>');
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
    });
    
    function getData(page) {

        var searchbyname = $('#searchbyname').val();
        var searchbyarea = $('.searchbyarea').val();
        var searchbycode = $('#searchbycode').val();
        var sortordname = $('#sortordname').val();
        var sortorderarea=$('#sortorderarea').val();
        var sortordercode=$('#sortordercode').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbyname: searchbyname, searchbyarea:searchbyarea,searchbycode:searchbycode,sortordname: sortordname,sortorderarea:sortorderarea,sortordercode:sortordercode, pagelimit: pagelimit},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".branchbody").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
<form id="pdfgenerator" action="{{ url('masterresources/branch/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Branch <span>List</span></h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-12">
                <a href="{{ action('Masterresources\BranchController@add') }}" id="btnNew" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
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
                                        <input id="slno" checked="" type="checkbox">
                                        <span></span>
                                        <em>Sl No.</em>
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
                                        <input id="areaName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Area Name</em>
                                    </label>
                                </div>
                            </div>
                        </div>

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
                                        <input id="alias" checked="" type="checkbox">
                                        <span></span>
                                        <em>Alias</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="branchPhone" checked="" type="checkbox">
                                        <span></span>
                                        <em>Branch Phone</em>
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
            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
            <input type="hidden" value="" id="sortorderarea" name="sortorderarea">            
            <input type="hidden" value="" id="sortordercode" name="sortordercode">            
            <div id="tblregion">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

                            <td>
                                Sl.No.
                            </td>
                            <td>
                                Branch Name
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp sortup"></a>
                                    <a href="javascript:void(0)" class="btnDown sortdown"></a>
                                </div>
                            </td>
                            <td>
                                Area Name
                                <div class="sort">
                                    <a href="#" class="btnUp areaup"></a>
                                    <a href="#" class="btnDown areadown"></a>
                                </div>
                            </td>
                            <td>
                                Branch Code
                                <div class="sort">
                                    <a href="#" class="btnUp codeup"></a>
                                    <a href="#" class="btnDown codedown"></a>
                                </div>
                            </td>
                            <td>
                                Alias
                            </td>
                            <td>
                                Branch Phone
                            </td>
                            <td>

                            </td>
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">


                            <td></td>
                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbyname" name="searchbyname" placeholder="Enter Branch Name">
                                    </div>
                                </div>
                            </td>
                            <td class="">
                                <div class="custCol-12">
                                    <select class="searchbyarea" name="searchbyarea">
                                        <option value="">All</option>
                                        @foreach ($areas as $area)
                                        <option value="{{$area->id}}">{{$area->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbycode" name="searchbycode" placeholder="Enter Branch Code">
                                    </div>
                                </div>
                            </td>
                            
                            <td >
                            </td>
                            <td >
                            </td>

                            <td>
                            </td>

                        </tr>

                    </thead>

                    <tbody class="branchbody" id='branchbody'>
                        @include('masterresources/branch/branch_result')

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

    $('.searchbyarea').on("change", function () {
        search();
    });
    
    $('#searchbycode').bind('keyup', function () {
        search();
    });
    
    $(".sortup").on('click', function () {
        $('#sortordname').val('ASC');
        search();
    });
    $(".sortdown").on('click', function () {
        $('#sortordname').val('DESC');
        search();
    });
    
    $(".areaup").on('click', function () {
        $('#sortorderarea').val('ASC');
        search1();
    });
    $(".areadown").on('click', function () {
        $('#sortorderarea').val('DESC');
        search1();
    });
    
    $(".codeup").on('click', function () {
        $('#sortordercode').val('ASC');
        search2();
    });
    $(".codedown").on('click', function () {
        $('#sortordercode').val('DESC');
        search2();
    });
    
    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {

        var searchbyname = $('#searchbyname').val();
        var searchbyarea = $('.searchbyarea').val();
        var searchbycode = $('#searchbycode').val();
        var sortordname = $('#sortordname').val();
        $('#sortorderarea').val('');
        $('#sortordercode').val('');
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'branches',
            data: {searchbyname: searchbyname, searchbyarea:searchbyarea,searchbycode:searchbycode,sortordname: sortordname, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.branchbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.branchbody').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
    function search1()
    {

        var searchbyname = $('#searchbyname').val();
        var searchbyarea = $('.searchbyarea').val();
        var searchbycode = $('#searchbycode').val();
        var sortorderarea = $('#sortorderarea').val();
        $('#sortordname').val('');
        $('#sortordercode').val('');
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'branches',
            data: {searchbyname: searchbyname, searchbyarea:searchbyarea,searchbycode:searchbycode,sortorderarea: sortorderarea, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.branchbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.branchbody').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
    function search2()
    {

        var searchbyname = $('#searchbyname').val();
        var searchbyarea = $('.searchbyarea').val();
        var searchbycode = $('#searchbycode').val();
        var sortordercode = $('#sortordercode').val();
        $('#sortorderarea').val('');
        $('#sortordname').val('');
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'branches',
            data: {searchbyname: searchbyname, searchbyarea:searchbyarea,searchbycode:searchbycode,sortordercode: sortordercode, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.branchbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.branchbody').html('<p class="noData">No Records Found</p>');
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
            $('#sortordname').val('');
            $('#sortordercode').val('');
            $('#page-limit').val(10);
            //search();
            window.location.href = '{{url("masterresources/branches")}}';
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
