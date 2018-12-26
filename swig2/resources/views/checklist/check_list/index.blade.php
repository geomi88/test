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
            
        if(!$("#checkPointt").is(':checked')&&!$("#catName").is(':checked')&&!$("#jobPos").is(':checked')&&!$("#days").is(':checked')&&!$("#aliass").is(':checked')) {
            toastr.error('Select required fields to print!');
        } else{                  
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');

        var strStyle='<style>.paginationHolder {display:none;}';
        
         if(!$("#checkPointt").is(':checked')){
                strStyle+=' .check_point {display:none;}';
         }
         if(!$("#catName").is(':checked')){
                strStyle+=' .check_name {display:none;}';
         }
         if(!$("#jobPos").is(':checked')){
               strStyle+=' .check_pos {display:none;}';
         }
         if(!$("#days").is(':checked')){
              strStyle+=' .check_days {display:none;}';  
         } 
         if(!$("#aliass").is(':checked')){
              strStyle+=' .check_alias {display:none;}';   
         }
         strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';
         
    win.document.write(strStyle+'<div style="text-align:center;"><h1>Check List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_point"> Check Point</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_name"> Category Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_pos"> Job Position </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_days"> Days </td>'+
                                            '<td style="padding:10px 0;color:#fff;" class="check_alias"> Alias </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.checkpoints')[0].outerHTML +'</table>');  
        win.document.close();
        win.print();
        win.close();
        return false;
    }
        });
        
    });
    
    function getData(page) {

        var searchbyquery = $('#searchbyquery').val();
        var searchbyjob = $('.searchbyjob').val();
        var searchbycategory = $('.searchbycategory').val();
        
        var sortordquery = $('#sortordquery').val();
        var sortordjob = $('#sortordjob').val();
        var sortordcategory = $('#sortordcategory').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
            {
                url: '?page=' + page,
                type: "get",
                datatype: "html",
                data: {searchbyquery: searchbyquery,searchbyjob:searchbyjob,searchbycategory:searchbycategory,sortordquery:sortordquery,sortordjob:sortordjob,sortordcategory:sortordcategory, pagelimit: pagelimit, searchable: searchable},
                // {
                //     you can show your loader 
                // }
            })
            .done(function (data)
            {
                console.log(data);

                $(".checkpoints").empty().html(data);
                location.hash = page;
            })
            .fail(function (jqXHR, ajaxOptions, thrownError)
            {
                alert('No response from server');
            });
    }


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('checklist/check_list/exportdata') }}" method="post">
    <header class="pageTitle">
        <h1>Check <span>List</span></h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-12">
                <a href="{{ action('Checklist\ChecklistController@add') }}" id="btnNew" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
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
                                        <input id="checkPointt" checked="" type="checkbox">
                                        <span></span>
                                        <em>Check Point</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="catName" checked="" type="checkbox">
                                        <span></span>
                                        <em>Category Name</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="jobPos" checked="" type="checkbox">
                                        <span></span>
                                        <em>Job Position</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="days" checked="" type="checkbox">
                                        <span></span>
                                        <em>Days</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="aliass" checked="" type="checkbox">
                                        <span></span>
                                        <em>Alias</em>
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
            <input type="hidden" value="" id="sortordquery" name="sortordquery">
            <input type="hidden" value="" id="sortordcategory" name="sortordcategory">
            <input type="hidden" value="" id="sortordjob" name="sortordjob">
            
            <div id="tblcategorytable">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

                            <td>
                                Check Point
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp queryup"></a>
                                    <a href="javascript:void(0)" class="btnDown querydown"></a>
                                </div>
                            </td>
                           
                            <td>
                                Category Name
                                <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp categoryup"></a>
                                    <a href="javascript:void(0)" class="btnDown categorydown"></a>
                                </div>
                            </td>
                            
                            <td>
                                Job Position
                                 <div class="sort">
                                    <a href="javascript:void(0)" class="btnUp jobup"></a>
                                    <a href="javascript:void(0)" class="btnDown jobdown"></a>
                                </div>
                            </td>
                            
                            <td>
                                Days
                            </td>
                            
                             <td>
                                Alias
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
                                        <input type="text" id="searchbyquery" name="searchbyquery" placeholder="Enter Check Point">
                                        <input type="text" style="display: none;">
                                    </div>
                                </div>
                            </td>
                                                       
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbycategory" name="searchbycategory">
                                        <option value="">All</option>
                                        @foreach ($categories as $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            
                            <td class="filterFields">
                                <div class="custCol-12">
                                    <select class="searchbyjob" name="searchbyjob">
                                        <option value="">All</option>
                                        @foreach ($job_positions as $job_position)
                                        <option value="{{$job_position->id}}"><?php echo str_replace("_", " ", $job_position->name)?></option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            
                            <td >
                            </td>
                            
                            <td>
                            </td>
                            
                            <td>
                            </td>

                        </tr>

                    </thead>

                    <tbody class="checkpoints" id='checkpoints'>
                        @include('checklist/check_list/result')
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

    $('#searchbyquery').bind('keyup', function () {
        search();
    });
    
    $('.searchbyjob').on("change", function () {
        search();
    });
    
    $('.searchbycategory').on("change", function () {
        search();
    });
    
    $(".queryup").on('click', function () {
        $('#sortordquery').val('ASC');
        $('#sortordjob').val('');
        $('#sortordcategory').val('');
        search();
    });
    
    $(".querydown").on('click', function () {
        $('#sortordquery').val('DESC');
        $('#sortordjob').val('');
        $('#sortordcategory').val('');
        search();
    });
    
    $(".categoryup").on('click', function () {
        $('#sortordquery').val('');
        $('#sortordjob').val('');
        $('#sortordcategory').val('ASC');
        search();
    });
    
    $(".categorydown").on('click', function () {
        $('#sortordquery').val('');
        $('#sortordjob').val('');
        $('#sortordcategory').val('DESC');
        search();
    });
    
    $(".jobup").on('click', function () {
        $('#sortordquery').val('');
        $('#sortordjob').val('ASC');
        $('#sortordcategory').val('');
        search();
    });
    
    $(".jobdown").on('click', function () {
        $('#sortordquery').val('');
        $('#sortordjob').val('DESC');
        $('#sortordcategory').val('');
        search();
    });

    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {
        var searchbyquery = $('#searchbyquery').val();
        var searchbyjob = $('.searchbyjob').val();
        var searchbycategory = $('.searchbycategory').val();
        
        var sortordquery = $('#sortordquery').val();
        var sortordjob = $('#sortordjob').val();
        var sortordcategory = $('#sortordcategory').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'check_list',
            data: {searchbyquery: searchbyquery,searchbyjob:searchbyjob,searchbycategory:searchbycategory,sortordquery:sortordquery,sortordjob:sortordjob,sortordcategory:sortordcategory, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.checkpoints').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.checkpoints').html('<p class="noData">No Records Found</p>');
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
            
            $('#sortordquery').val('');
            $('#sortordjob').val('');
            $('#sortordcategory').val('');
            $('#page-limit').val(10);
            //search();
            window.location.href = '{{url("checklist/check_list")}}';
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
