@extends('layouts.main')
@section('content')
<div class="innerContent">
    <div class="fieldGroup meetingHolder">
        <header class="pageTitle">
            <h1><span>Item Wise </span> Stock Report</h1>
        </header>
        <a class="btnBack" href="{{ URL::to('inventory') }}">Back</a>
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="javascript:void(0);" onclick="javascript:print_report();">Print</a>
        <form id="stock_report_itemwise">
            <div class="custRow stock-search">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Inventory Item</label>
                        <select class='chosen-select' id="inventory_list" name="inventory_list" required>
                            <option value="">Select Inventory</option>
                            @forelse($inventory as $row)
                            <option value="{{$row->id }}">{{ $row->name .' ('. $row->product_code .')' }}</option>
                            @empty

                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Unit</label>
                        <select id="inventory_unit_list" name="inventory_unit_list" required>
                            <option value=''>Unit</option>
                        </select>
                    </div>
                </div>
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Location</label>
                        <select id="location_id">
                            <option value="" selected>All</option>
                            <option value="1">Warehouse</option>
                            <option value="2">Branches</option>
                        </select>
                    </div>
                </div>
                <div class="custCol-3">
                    <div class="inputHolder stock-report">
                        <input type="hidden" id="inv_unit">
                        <a class="addNext commonBtn bgGreen  stock-report-btn " id="get_stock_summary" href="javascript:void(0)">Search</a>
                    </div>
                </div>
            </div>
        </form>
        <div class="repeatSelect">
            <div class="listerType1 listTypeV3">
                <table style="width: 100%; padding: 0 0 0 0;" cellspacing="0" cellpadding="0">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td class="alignCenter"></td>
                            <td>Warehouse / Branch</td>
                            <td class="amountAlign">Current Stock</td>
                        </tr>
                    </thead>
                    <tbody id="stock_report_data">
                        @include("inventory/itemwisestockreport/view")
                    </tbody>
                </table>
                <div class="commonLoaderV1"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $("#inventory_list").change(function (event) {
        if ($(this).val() != '') {
            $(".commonLoaderV1").show();
            $.post("{{ url('inventory/itemwisestockreport/getitemunits') }}", {inventory_id: $(this).val()}, function (data) {
                if (data != '-1') {
                    $("#inventory_unit_list").html(data);
                    $('#inventory_unit_list').rules('remove', 'required');
                } else {
                    $("#inventory_unit_list").html("<option value=''>Unit</option>");
                }
            }).done(function(){
                $(".commonLoaderV1").hide();
            });
        } else {
            $("#inventory_unit_list").html("<option value=''>Unit</option>");
        }
    });
    $.validator.setDefaults({ignore: ":hidden:not(.chosen-select)"});
    $("#stock_report_itemwise").validate({
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
            inventory_list: {required: true},
            inventory_unit_list: {required: true},
        },
        messages: {
            inventory_list: "Select Inventory Item",
            inventory_unit_list: "Select Unit",
        }
    });
    $("#get_stock_summary").click(function () {
        if ($("#stock_report_itemwise").valid()) {
            $("#inv_unit").val($("#inventory_unit_list option:selected").text());
            var inv_id = $("#inventory_list").val();
            $(".commonLoaderV1").show();
            $.post("{{ url('inventory/itemwisestockreport/view') }}", {inventory_id: inv_id, location_id: $("#location_id").val(), unit_id: $("#inventory_unit_list").val(), unit: $("#inv_unit").val()}, function (data) {
                if (data == -1) {
                    toastr.error("Something went wrong");
                } else if (data == -2) {
                    toastr.error("Selected Unit has no Conversion value");
                } else {
                    $("#stock_report_data").html(data);
                }
            }).done(function () {
                $(".commonLoaderV1").hide();
            });
        }
    });
    $("body").on("click", ".view_details", function () {
        var row_id = $(this).attr("id");
        var id = row_id.replace('button_', '');
        if ($("tr").hasClass("detailed_list_" + id)) {
            $(".detailed_list_" + id).remove();
        } else {
            try {
                var inv_id = $("#inventory_list").val();
                $(".commonLoaderV1").show();
                $.post("{{ url('inventory/itemwisestockreport/detailed_view') }}", {inv_type: id, inventory: inv_id, unit_id: $("#inventory_unit_list").val(), conversion: $("#conversion").val(), unit: $("#inv_unit").val()}, function (data) {
                    if (data != -1) {
                        $("#row_" + id).after(data);
                    }
                }).done(function () {
                    $(".commonLoaderV1").hide();
                });
            } catch (e) {
                toastr.error("Something Went Wrong");
            }
        }
    });
    
    function print_report(){
        win = window.open('', 'Print', 'height='+screen.height+',width='+screen.width);
        win.document.write('<style> .total_td1{display:none;}.clstrTotalseparator td {border-top: 1px solid #364968;} .expandIcon{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;} .right {float: right;margin-right:20px;}.listerType1.listTypeV3 td {font-size: 13px;padding: 4px 18px;}.detailed_table table tbody tr td{font-size:12px;}.clsbatchlisthead {background-color: #298d74;} .clsbatchlisthead .headingHolder td {color: #fff; font-size: 12px; font-weight: bold;}.clsbatchlisthead .headingHolder td {padding: 4px 18px;}</style>'+
                                '<div style="text-align:center;"><h1>Item Wise Stock Report</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                            '<td style="padding:10px 0;color:#fff;">Warehouse / Branch</td>' +
                                            '<td style="padding:10px 0;color:#fff;">Current Stock</td>' +
                                        '</tr>'+
                                    '</thead>'+ $('#stock_report_data')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
    }
    
    $("#reset").click(function(event){
        location.reload(true);
    })
</script>
@endsection
