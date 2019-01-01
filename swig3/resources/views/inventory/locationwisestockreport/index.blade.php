@extends('layouts.main')
@section('content')
<script>
   $(document).ready(function ()
    {

        $('.print').click(function () {
            win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);
            win.document.write('<style>.paginationHolder,.expandIcon {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}'+
                    ' .right {float: right;margin-right:20px;} .clsbatchlisthead{background-color: #298d74;color: #fff} .clstrTotalseparator td{border-top: 1px solid #364968;}</style>'+
                            '<div style="text-align:center;"><h1>Location Wise Stock Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                    '<tr class="headingHolder">'+
                                        '<td style="padding:10px 0;color:#fff;"> Inventory Item </td>' +
                                        '<td style="padding:10px 0;color:#fff;"> Current Stock </td>' +
                                    '</tr>'+
                                '</thead>'+ $('.tblstockreport')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });
        
    });
</script>
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('inventory')}}">Back</a>
    <div class="meetingHolder">
        <header class="pageTitle">
            <h1><span>Location Wise </span>Stock Report</h1>
        </header>
        <a class="btnAction refresh bgRed" href="{{ URL::to('inventory/locationwisestockreport')}}">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <div class="inputAreaWrapper">

            <form id="frmstockreport">
                <div class="invertoryFieldHolder clsparentdiv">
                    <div class="custRow">
                        <div class="inputHolder checkHolder">
                            <div class="commonCheckHolder radioRender">
                                <label>
                                    <input  name="stockearea" id="chkwarehouse" checked value="1" type="radio" >
                                    <span></span>
                                    <em>Warehouse</em>
                                </label>
                            </div>
                            <div class="commonCheckHolder radioRender">
                                <label>
                                    <input  name="stockearea" id="chkbranch" value="2" type="radio">
                                    <span></span>
                                    <em>Branch</em>
                                </label>
                            </div>
                        </div>
                        <div class="customClear "></div>
                        <div class="custCol-4 clswarehouse">
                            <div class="inputHolder">
                                <label>Stock Area</label>
                                <select id="warehouse_id" name="warehouse_id" class="chosen-select">
                                    <option value=''>Select Warehouse</option>
                                    @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" >{{$warehouse->name}}</option>
                                    @endforeach
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4 clsbranch" style="display: none;">
                            <div class="inputHolder">
                                <label>Stock Area</label>
                                <select id="branch_id" name="branch_id" class="chosen-select">
                                    <option value=''>Select Branch</option>
                                    @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" >{{$branch->branch_code}} : {{$branch->name}}</option>
                                    @endforeach
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Inventory Item</label>
                                <select id="item_id" name="item_id" class="chosen-select">
                                    <option value=''>Select Item</option>
                                    @foreach ($inventorydata as $item)
                                    <option value="{{ $item->id }}" >{{$item->product_code}} : {{$item->name}}</option>
                                    @endforeach
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-2 addBtnHolder" style="padding-left: 12px;">
                            <a  class="btnIcon lightGreenV3" id="btngetstockreport">Search</a>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <div class="repeatSelect">

            <div class="listerType1 listTypeV3">
                <table style="width: 100%; padding: 0 0 0 0;" cellspacing="0" cellpadding="0">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td></td>
                            <td>Inventory Item</td>
                            <td class="amountAlign">Current Stock</td>
                        </tr>
                    </thead>

                    <tbody id="tblstockreport" class="tblstockreport">
                        @include("inventory/locationwisestockreport/result")
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <div class="commonLoaderV1"></div>
</div>

<script>
    $(function () {

        $("#frmstockreport").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                var id = $(element).attr("id") + "_chosen";
                if ($(element).hasClass('valErrorV1')) {
                    $("#" + id).find('.chosen-single').addClass('chosen_error');
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                warehouse_id: {
                    required: function () {
                        return $("#chkwarehouse").prop('checked');
                    }
                },
                branch_id: {
                    required: function () {
                        return $("#chkbranch").prop('checked');
                    }
                },
            },
            messages: {
                warehouse_id: "Select Warehouse",
                branch_id: "Select Branch",
            }
        });

        $("#chkwarehouse").click(function () {
            $(".clsbranch").hide();
            $(".clswarehouse").show();
            $('#branch_id').val('').trigger('chosen:updated');
        });

        $("#chkbranch").click(function () {
            $(".clsbranch").show();
            $(".clswarehouse").hide();
            $('#warehouse_id').val('').trigger('chosen:updated');
        });

        $("#btngetstockreport").click(function () {
            if ($("#frmstockreport").valid()) {
                var stockareaid = '';
                if ($("#chkwarehouse").prop('checked')) {
                    stockareaid = $('#warehouse_id').val();
                } else {
                    stockareaid = $('#branch_id').val();
                }

                $(".commonLoaderV1").show();
                $.post("{{ url('inventory/locationwisestockreport/getstocklist') }}", {stockareaid: stockareaid, itemid: $("#item_id").val()}, function (data) {
                    if (data != -1) {
                        $("#tblstockreport").html(data);
                    }
                }).done(function () {
                    $(".commonLoaderV1").hide();
                });
            } else {
                $("#tblstockreport").html('<tr><td></td><td>No records found</td><td></td></tr>');
            }
        });

        $('body').on('click', '.view_details', function () {
            var item_id = $(this).attr("attrid");
            var stockareaid = '';
            if ($("#chkwarehouse").prop('checked')) {
                stockareaid = $('#warehouse_id').val();
            } else {
                stockareaid = $('#branch_id').val();
            }

            if (!$(this).parent().hasClass('rotateIcon')) {
                $("#batchlist_" + item_id).remove();
            } else {
                $(".commonLoaderV1").show();
                $.post("{{ url('inventory/locationwisestockreport/getbatchlist') }}", {item_id: item_id, stockareaid: stockareaid}, function (data) {
                    if (data != -1) {
                        $("#parentrow_" + item_id).after(data);
                    }
                }).done(function () {
                    $(".commonLoaderV1").hide();
                });
            }

        });

    });

</script>
@endsection
