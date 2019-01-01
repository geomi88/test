@extends('layouts.main')
@section('content')
<div class="innerContent">
    <form id="pdfgenerator" action="{{ url('supervisors/inventory_request/exporttopdf') }}" method="post">
        <header class="pageTitle">
            <h1>Inventory Request <span>List</span></h1>
        </header>
        <?php if($usertype != 'Cashier') {?>
            <div class="fieldGroup" id="fieldSet1">
                <div class="custRow">
                    <div class="custCol-12">
                        <a href="{{ action('Supervisors\InventoryrequestController@add') }}" id="addItemRequest" class="right commonBtn commonBtn bgGreen addBtn">Make Request</a>
                    </div>
                </div>
                <div class="customClear"></div>
            </div>
        <?php } ?>
        <a class="btnAction refresh bgRed" id="reset" href="javascript:void(0)">Refresh</a>
        <!--<a class="btnAction print bgGreen" href="javascript:void(0)">Print</a>-->
        <a class="btnAction saveDoc bgBlue" href="javascript:void(0)" onclick="savetopdf()">Save</a>
        <div class="fieldGroup" id="fieldSet1">

            <div class="customClear"></div>
        </div>
        <div class="listHolderType1">
            <div class="custRow">
            </div>
            <div class="listerType1 reportLister"> 

                <input type="hidden" value="" id="sortsimp" name="sortsimp">

                <div id="postable">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>
                                    Request Id
                                    <div class="sort">
                                        <a href="javascript:void(0)" class="btnUp sortup"></a>
                                        <a href="javascript:void(0)" class="btnDown sortdown"></a>
                                    </div>
                                </td>
                                <td>
                                    Status
                                </td>
                                <td>
                                    Branch
                                </td>
                                <td>
                                    Warehouse
                                </td>
                                <td>
                                    Requested Date
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
                                            <input type="text" id="searchkey" name="searchkey" placeholder="Enter Request Id" >
                                            <input type="text" id="" name="" style="display: none;">
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                        </thead>

                        <tbody class="item_requests" id='item_requests'>                  
                            @include('supervisors/inventory_request/results')
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

    $('#searchkey').bind('keyup', function () {
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

    $('#page-limit').on("change", function () {

        search();

    });

    function search()
    {
        // alert('asd');
        var sorting = $('#sortsimp').val();
        var searchkey = $('#searchkey').val();

        var pagelimit = $('#page-limit').val();

        // alert(startdate)alert(enddate);

        $.ajax({
            type: 'POST',
            url: 'inventory_request',
            data: {searchkey: searchkey, sorting: sorting, pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {

                    $('.item_requests').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.item_requests').html('<p class="noData">No Records Found</p>');
                }
            }
        });



    }

    $(function () {
        $('.commonLoaderV1').hide();

        $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

            $('#sortsimp').val('');
            $('#searchkey').val('');

            search();
        });
    });
    function savetopdf()
    {

        document.getElementById("pdfgenerator").submit();


    }


</script>


@endsection
