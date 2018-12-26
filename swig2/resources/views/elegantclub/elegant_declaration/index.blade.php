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

     $('.print').click(function () {
            var pageTitle = 'Page Title',
                win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);
                win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;} .right {float: right;margin-right:20px;}</style>'+
                                '<div style="text-align:center;"><h1>Elegant Declaration List</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;"> Sl No </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Title </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Content </td>' +
                                            '<td style="padding:10px 0;color:#fff;"> Created By </td>' +
                                        '</tr>'+
                                    '</thead>'+ $('.tbl_elegant_content')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });
       $('.saveDoc').click(function () {
      
//       svepdf();
       
        
    });
});

function getData(page){

        var searchbytitle = $('#searchbytitle').val();
        var searchbycontent = $('#searchbycontent').val();
        var pagelimit = $('#page-limit').val();

        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {searchbytitle: searchbytitle,searchbycontent:searchbycontent,pagelimit:pagelimit},
            
            // {
            //     you can show your loader 
            // }
        })
        .done(function(data)
        {
            $(".tbl_elegant_content").empty().html(data);
            location.hash = page;
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              alert('No response from server');
        });
}


</script>
<div class="innerContent">
    <form id="pdfgenerator" action="" method="post">
    <header class="pageTitle">
        <h1>Elegant Declaration<span> List</span></h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-12">
                <a href="{{ action('Elegantclub\ElegantDeclarationController@add') }}" id="btnNew" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
            </div>
        </div>
        <div class="customClear"></div>
    </div>
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>-->
                     <div class="printChoose">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="title" checked="" type="checkbox">
                                        <span></span>
                                        <em>Title</em>
                                    </label>
                                </div>
                            </div>

                            <div class="custCol-4">
                                <div class="commonCheckHolder checkRender">
                                    <label>
                                        <input id="declaration_content" checked="" type="checkbox">
                                        <span></span>
                                        <em>Declaration content</em>
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
            <div id="">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

                            <td>
                                Sl No
                            </td>
                            <td>
                                Title
                            </td>
                            <td>
                                Declaration Content
                            </td>
                            <td>
                                Created By
                            </td>
                            <td>
                                Action
                            </td>
                        </tr>
                    </thead>

                    <thead class="listHeaderBottom">

                        <tr class="headingHolder">

                            <td></td>
                            <td class="filterFields">
                                <div class="">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbytitle" name="searchbytitle" placeholder="Enter Title">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="custCol-12">
                                    <input type="text" id="searchbycontent" name="searchbycontent" placeholder="Enter Content">
                                </div>
                            </td>
                            <td></td>
                            
                            <td></td>
                        </tr>

                    </thead>

                    <tbody class="tbl_elegant_content" id='tbl_elegant_content'>
                        @include('elegantclub/elegant_declaration/result')
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

    $('#searchbycontent').bind('keyup', function () {
        search();
    });

    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {

        var searchbytitle = $('#searchbytitle').val();
        var searchbycontent = $('#searchbycontent').val();
        var pagelimit = $('#page-limit').val();

        $.ajax({
            type: 'POST',
            url: 'declaration',
            data: {searchbytitle: searchbytitle, searchbycontent: searchbycontent,pagelimit:pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.tbl_elegant_content').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.tbl_elegant_content').html('<p class="noData">No Records Found</p>');
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


            $('#searchbyemployee').val('');
            $('#searchbywarehouse').val('');
            $('#searchbystatus').val('');
           


            //search();
            window.location.href = '{{url("elegantclub/declaration")}}';
        });
    });

    function savetopdf()
    {

        document.getElementById("pdfgenerator").submit();


    }
</script>
@endsection
