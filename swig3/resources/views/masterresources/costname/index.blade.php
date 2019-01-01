@extends('layouts.main')
@section('content')
<script>
    
    $(document).ready(function ()
    {
        $(document).on('click', '.pagination a', function (event)
        {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];
            getData(page);
        });

        $('.print').click(function () {
            win = window.open('', 'Print', 'height=' + screen.height, 'width=' + screen.width);
            win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;} .right {float: right;margin-right:20px;}</style>' +
                    '<div style="text-align:center;"><h1>Cost Names</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">' +
                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">' +
                    '<tr class="headingHolder">' +
                    '<td style="padding:10px 0;color:#fff;"> Sl No. </td>' +
                    '<td style="padding:10px 0;color:#fff;"> Name</td>' +
                    '<td style="padding:10px 0;color:#fff;"> Alias Name </td>' +
                    '</tr>' +
                    '</thead>' + $('.costbody')[0].outerHTML + '</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });

    });

    function getData(page) {

        var searchbyname = $('#searchbyname').val();
        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';
        $.ajax(
                {
                    url: '?page=' + page,
                    type: "get",
                    datatype: "html",
                    data: {searchbyname: searchbyname, pagelimit: pagelimit},
                    // {
                    //     you can show your loader 
                    // }
                })
                .done(function (data)
                {
                    console.log(data);

                    $(".costbody").empty().html(data);
                    location.hash = page;
                })
                .fail(function (jqXHR, ajaxOptions, thrownError)
                {
                    alert('No response from server');
                });
    }


</script>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Cost Names</h1>
    </header>
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-12">
                <a href="{{ action('Masterresources\CostnameController@add') }}" id="btnNew" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
            </div>
        </div>
        <div class="customClear"></div>
    </div>

    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>

    <div class="listHolderType1">
        <div class="listerType1 reportLister"> 

            <div id="tblcategorytable">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">

                            <td>
                                Sl.No.
                            </td>
                            <td>
                                Name

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


                            <td>
                            </td>

                            <td class="filterFields">
                                <div class="custRow">
                                    <div class="custCol-12">
                                        <input type="text" id="searchbyname" name="searchbyname" placeholder="Enter Name">
                                        <input type="text" style="display: none;">
                                    </div>
                                </div>
                            </td>

                            <td >

                            </td>

                            <td>
                            </td>

                        </tr>

                    </thead>

                    <tbody class="costbody" id='costbody'>
                        @include('masterresources/costname/result')

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

</div>

<script>

    $('#searchbyname').bind('keyup', function () {
        search();
    });

    $('#page-limit').on("change", function () {
        search();
    });


    function search()
    {
        var searchbyname = $('#searchbyname').val();

        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: 'costname',
            data: {searchbyname: searchbyname, pagelimit: pagelimit, searchable: searchable},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                  
                    $('.costbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.costbody').html('<p class="noData">No Records Found</p>');
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
            $('#page-limit').val(10);
            window.location.href = '{{url("masterresources/costname")}}';
        });
    });

</script>
@endsection
