
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
                                    <input type="text" id="search">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="custCol-6">
                                <select class="branch">
                                    <option value="">All</option>
                                    @foreach ($branch_names as $branch_name)
                                    <option value="{{$branch_name->branch_id}}">{{$branch_name->branch_name}}</option>
                                    @endforeach
                                </select>

                            </div>
                        </td>
                        <td class="">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="shift">
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
                                    <select class="order">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="amount">
                                </div>

                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="cashorder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="cashamount">
                                </div>

                            </div>
                        </td>

                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="difforder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="diffamount">
                                </div>

                            </div>
                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <input type="text" id="start_date" value="">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="end_date" value="">
                                </div>

                            </div>
                        </td>
                        <td></td>
                    </tr>
                </thead>

                <tbody class="pos">
                     <?php $n = $pos_sales->perPage() * ($pos_sales->currentPage()-1); ?>   
                    @foreach ($pos_sales as $pos_sale)

                    <tr>
                        <td>{{$pos_sale->employee_name}}</td>
                        <td>{{$pos_sale->branch_name}}</td>
                        <td>{{$pos_sale->jobshift_name}}</td> 
                        <td>{{$pos_sale->total_sale}}</td>
                        <td>{{$pos_sale->cash_collection}}</td>                        
                        <td><?php echo (($pos_sale->total_sale) - ($pos_sale->cash_collection)); ?> </td>
                        <td>{{$pos_sale->pos_date}}</td>
                        <td class="btnHolder">
                            <div class="actionBtnSet">
                                <a class="btnAction action bgGreen" href="{{ URL::to('mis/pos_sales/show', ['id' => Crypt::encrypt($pos_sale->id)]) }}">View</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr><td colspan="7"><?php echo $pos_sales->render();  ?> </td></tr>
                </tbody>

            </table>

    <div class="pagesShow">
							<span>Showing 10 of 20</span>
							<select>
								<option>10</option>
								<option>25</option>
								<option>50</option>
								<option>100</option>
							</select>
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
        $('#cashsort').val('ASC');
        search1();
    });
    $(".cashup").on('click', function () {
        $('#cashsort').val('DESC');
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
        if ($('#end_date').val() !=='')
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
        // alert(startdate)alert(enddate);

        $.ajax({
            type: 'POST',
            url: 'pos_sales/search',
            data: {branch: branch, searchkey: searchkey, sorting: sorting, order: order, shift: shift, amount: amount, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate},
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
                    $(".commonLoaderV1").hide();
                    $('.pos').html('<p class="noData">No Records Found</p>');
                }
            }
        });



    }

    function search1()
    {
        //alert('asd');
        var cashsorting = $('#cashsort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var shift = $('.shift').val();
        var order = $('.order').val();
        var amount = $('#amount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();

        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();

        $.ajax({
            type: 'POST',
            url: 'pos_sales/search',
            data: {branch: branch, searchkey: searchkey, order: order, amount: amount, cashsorting: cashsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate},
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
        var shift = $('.shift').val();
        var sorting = $('#sortsimp').val();
        var cashsorting = $('#cashsort').val();
        var bsorting = $('#bsort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();

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
            url: 'pos_sales/search',
            data: {branch: branch, searchkey: searchkey, order: order, amount: amount, bsorting: bsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate},
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
        var shift = $('.shift').val();
        var totsorting = $('#tot').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var order = $('.order').val();
        var amount = $('#amount').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();

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
            url: 'pos_sales/search',
            data: {branch: branch, searchkey: searchkey, totsorting: totsorting, order: order, amount: amount, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate},
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
        var shift = $('.shift').val();
        var diffsorting = $('#diffsort').val();
        var searchkey = $('#search').val();
        var branch = $('.branch').val();
        var cashorder = $('.cashorder').val();
        var difforder = $('.difforder').val();
        var order = $('.order').val();
        var amount = $('#amount').val();

        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var cashamount = $('#cashamount').val();
        var diffamount = $('#diffamount').val();

        $.ajax({
            type: 'POST',
            url: 'pos_sales/search',
            data: {branch: branch, searchkey: searchkey, order: order, amount: amount, diffsorting: diffsorting, shift: shift, cashorder: cashorder, difforder: difforder, cashamount: cashamount, diffamount: diffamount, startdate: startdate, enddate: enddate},
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
            changeYear: true, dateFormat: 'yy-mm-dd'
        });
        $("#end_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'yy-mm-dd'
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
            search();
        });
    });
</script>