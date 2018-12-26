@extends('layouts.main')
@section('content')

<!--   Print section starts  -->

<script>
    $(document).ready(function ()
    {
        $('#btnPrint1').click(function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write($('.divprint').html());
            win.document.close();
            win.print();
            win.close();
            return false;
        });

    });
</script>
<div class="divprint" style="display: none;">
    <table border="0" cellpadding="5" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;color:#fff;border:3px solid #71699f;">
        <tr>
            <td>
                <span style="color:#34514e; font-size:25px;font-weight:bold; display:block;padding-bottom:10px;">GRN</span>
                <p style="margin: 0;padding-bottom:5px;"><strong style="padding-right:5px;color:#000;">PO Code :</strong><span style="color:#000;">{{$orderdata->order_code}}</span></p>
                <p style="margin: 0;"><strong style="padding-right:5px;color:#000;">Supplier :</strong><span style="color:#000;">{{$orderdata->suppliercode}} {{$orderdata->supplierfname}}</span></p>
            <td style="text-align:right;vertical-align:top;">
                <img src="{{ URL::asset('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est." style="width:150px;" >
            </td>
        </tr>
        <tr style="margin-bottom: 5px;">
            <td colspan="3">
                <table cellpadding="2" width="100%" height="100%" border="3" bordercolor="#71699f" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none; font-family:Arial, Helvetica, sans-serif; font-size:12px;" >
                    <thead style="padding:5px;background:#d5f2ef;font-size:14px;font-weight:bold;color:#34514e;text-align:left;">
                        <tr>
                            <th width="80px;">Item Code </th>
                            <th width="170px;">Item Name</th>
                            <th width="50px;">Qty</th>
                            <th width="50px;">Unit </th>
                            <th width="80px;">Price/Unit</th>
                            <th width="100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orderitems as $item)
                        <tr>
                            <td>{{$item->product_code}}</td>
                            <td>{{$item->productname}}</td>
                            <td class="amountAlign">{{$item->quantity}}</td>
                            <td>{{$item->unit}}</td>
                            <td class="amountAlign"><?php echo Customhelper::numberformatter($item->unit_price); ?></td>
                            <td class="amountAlign"><?php echo Customhelper::numberformatter($item->total_price); ?></td>

                        </tr>
                        @empty
                        <tr>
                            <td></td>
                            <td></td>
                            <td>No Data</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </td>
        </tr>
        <tr style="padding:5px;background:#fff;font-size:14px;font-weight:bold;color:#000;text-align:left;">
            <th colspan="3">PO Stock Summary</th>
        </tr> 
        <tr>
            <td colspan="2">
                <table cellpadding="2" width="100%" height="100%" border="3" bordercolor="#71699f" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none; font-family:Arial, Helvetica, sans-serif; font-size:12px;" >
                    <thead style="padding:5px;background:#71699f;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr >
                            <th width="80px;">Item Code </th>
                            <th width="170px;">Item Name</th>
                            <th width="50px;">Unit</th>
                            <th width="50px;">Qty</th>
                            <th width="100px;">Entered Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orderitems as $item)
                        <tr >
                            <td>{{$item->product_code}}</td>
                            <td>{{$item->productname}}</td>
                            <td>{{$item->unit}}</td>
                            <td class="amountAlign">{{$item->quantity}}</td>
                            <td class="amountAlign">{{$item->entered_stock}}</td>

                        </tr>
                        @empty
                        <tr>
                            <td></td>
                            <td></td>
                            <td>No Data</td>
                            <td></td>
                            <td></td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </td>
        </tr>
        <tr style="padding:5px;background:#fff;font-size:14px;font-weight:bold;color:#000;text-align:left;">
            <th colspan="3">Previous GRN'S</th>
        </tr> 
        @forelse($prevgrns as $grn)
        <tr>
            <td>
                <span style="color:#34514e; font-weight:bold; display:block;"><span style="color:#000;">GRN No :</span> {{$grn->batch_code}}</span>
            </td>
            <td>
                <span style="color:#34514e; font-weight:bold; display:block;;"><span style="color:#000;">Warehouse :</span> {{$grn->name}}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="2" width="100%" height="100%" border="3" bordercolor="#71699f" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none; font-family:Arial, Helvetica, sans-serif; font-size:12px;" >
                    <thead style="padding:5px;background:#71699f;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th >Unit</th>
                            <th class="amountAlign">Quantity</th>
                            <th>Mfd. Date</th>
                            <th>Expiry</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grn->items as $item)
                        <tr >
                            <td>{{$item->product_code}}</td>
                            <td>{{$item->productname}}</td>
                            <td>{{$item->unit}}</td>
                            <td class="amountAlign">{{$item->purchase_quantity}}</td>
                            <td><?php if ($item->mfg_date) {
    echo date('d-m-Y', strtotime($item->mfg_date));
} ?></td>
                            <td><?php if ($item->exp_date) {
    echo date('d-m-Y', strtotime($item->exp_date));
} ?></td>

                        </tr>
                        @empty 

                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
        @empty

        @endforelse
        <tr>
            <td colspan="2" style="border-bottom:2px solid #71699f; height:10px;"></td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:10px;text-align:center;">
                <img src="{{ URL::asset('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:5px 0 10px; color:#34514e;text-align:center;">
                P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new
            </td>
        </tr>
    </table>

</div>

<!--   Print section ends  -->

<div class="innerContent">

    <a class="btnAction print bgGreen " id="btnPrint1" href="javascript:void(0);">Print</a>
    <a class="btnBack" href="{{ URL::to('purchase/received_grn')}}">Back</a>

    <header class="pageTitleV3">
        <h1>View GRN</h1>
    </header>

    <div class="inputAreaWrapper">
        <div class="custRow reqCodeDateHolder">

            <div class="custCol-6 ">
                <label>PO Code : <span>{{$orderdata->order_code}}</span></label>
                <input type="hidden" id="po_id" name="po_id" value="{{$orderdata->id}}">
                <input type="hidden" id="po_code" name="po_code" value="{{$orderdata->order_code}}">
                <input type="hidden" id="grn_code" name="grn_code" value="{{$batchcode}}">
                <input type="hidden" id="stock_entered" name="stock_entered" value="{{$orderdata->stock_entered}}">
            </div>
            <div class="custCol-6 alignRight">
                <label>Date : <span><?php echo date('d-m-Y', strtotime($orderdata->created_at)); ?></span></label>
            </div>
        </div>

        <div class="custRow">
            <div class="custCol-6">
                <label><strong>Supplier : {{$orderdata->suppliercode}} {{$orderdata->supplierfname}}</strong></label>
            </div>

        </div>

        <div class="tbleListWrapper ">
            <table cellpadding="0" cellspacing="0" style="width: 100%;padding-bottom: 15px;">
                <thead class="headingHolder">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th class="amountAlign">Quantity</th>
                        <th>Unit</th>
                        <th class="amountAlign">Price/Unit</th>
                        <th class="amountAlign">Total</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($orderitems as $item)
                    <tr>
                        <td>{{$item->product_code}}</td>
                        <td>{{$item->productname}}</td>
                        <td class="amountAlign">{{$item->quantity}}</td>
                        <td>{{$item->unit}}</td>
                        <td class="amountAlign"><?php echo Customhelper::numberformatter($item->unit_price); ?></td>
                        <td class="amountAlign"><?php echo Customhelper::numberformatter($item->total_price); ?></td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
            <?php if (count($orderitems) > 0) { ?>

            <div class="custRow reqCodeDateHolder">

                <div class="custCol-6 ">
                    <label>PO Stock Summary</label>
                </div>

            </div>
            <div class="approverDetailsWrapper">
                <div class="tbleListWrapper">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="headingHolder ">
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th >Unit</th>
                                <th class="amountAlign">Quantity</th>
                                <th class="amountAlign">Entered Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderitems as $item)
                            <tr >
                                <td>{{$item->product_code}}</td>
                                <td>{{$item->productname}}</td>
                                <td>{{$item->unit}}</td>
                                <td class="amountAlign">{{$item->quantity}}</td>
                                <td class="amountAlign">{{$item->entered_stock}}</td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            <?php } ?>

        <div style="padding-top: 30px;">
            
            <?php if (count($prevgrns) > 0) { ?>   

            <div class="custRow reqCodeDateHolder" style="padding-bottom: 10px;">

                <div class="custCol-6 ">
                    <label>Previous GRN'S</label>
                </div>

            </div>
            @foreach($prevgrns as $grn)

            <div class="custRow">
                <div class="custCol-6">
                    <label>GRN No : <span>{{$grn->batch_code}}</span></label>
                </div>
                <div class="custCol-6">
                    <label>Warehouse : <span>{{$grn->name}}</span></label>
                </div>
            </div>

            <div class="approverDetailsWrapper" style="padding-bottom: 20px;">
                <div class="tbleListWrapper">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="headingHolder ">
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th >Unit</th>
                                <th class="amountAlign">Quantity</th>
                                <th>Mfd. Date</th>
                                <th>Expiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grn->items as $item)
                            <tr >
                                <td>{{$item->product_code}}</td>
                                <td>{{$item->productname}}</td>
                                <td>{{$item->unit}}</td>
                                <td class="amountAlign">{{$item->purchase_quantity}}</td>
                                <td><?php if ($item->mfg_date) {
                                    echo date('d-m-Y', strtotime($item->mfg_date));
                                } ?>
                                </td>
                                <td><?php if ($item->exp_date) {
                                    echo date('d-m-Y', strtotime($item->exp_date));
                                } ?>
                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="customClear"></div>
            @endforeach
        <?php } ?>

        </div>
    </div>
</div>

<script>
    $(function () {

        $("#frmwarehouse").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                warehouse: {required: true},

            },
            messages: {
                warehouse: "Select warehouse",

            }
        });

        $("#btnUpdateStock").click(function () {
            if (!$("#frmwarehouse").valid()) {
                return;
            }

            if ($('input:checkbox.chkinventory:checked').length == 0) {
                alert("Please select atleast one item to enter stock");
                return
            }

            var stockData = {
                warehouse: $('#warehouse').val(),
                po_id: $('#po_id').val(),
                po_code: $('#po_code').val(),
                grn_code: $('#grn_code').val(),
                stock_entered: $('#stock_entered').val(),
            }

            $(".chkinventory:checked").each(function () {
                var itemid = $(this).attr('attrid');

                if ($('#txtqty_' + itemid).val() == '') {
                    $('#txtqty_' + itemid).addClass("notentered");
                } else {
                    $('#txtqty_' + itemid).removeClass("notentered");
                }

                if ($('#txtmfd_' + itemid).val() == '' && $(this).attr('attrmfd') == 1) {
                    $('#txtmfd_' + itemid).addClass("notentered");
                } else {
                    $('#txtmfd_' + itemid).removeClass("notentered");
                }

                if ($('#txtexp_' + itemid).val() == '' && $(this).attr('attrexp') == 1) {
                    $('#txtexp_' + itemid).addClass("notentered");
                } else {
                    $('#txtexp_' + itemid).removeClass("notentered");
                }

            });

            if ($('.notentered').length != 0) {
                $('.errorMsg').text("Please fill the required fields");
                $('.statusMessage').show();
                return false;
            } else {
                $('.statusMessage').hide();
            }

            var stockitems = [];
            $(".chkinventory:checked").each(function () {
                var itemid = $(this).attr('attrid');
                var unitid = $(this).attr('attrunitid');
                var isprimary = $(this).attr('attrisprimary');
                var unitprice = $(this).attr('attrunitprice');
                var itemcompany = $(this).attr('attritemcompany');

                var arraData = {
                    itemid: itemid,
                    unitid: unitid,
                    isprimary: isprimary,
                    unitprice: unitprice,
                    itemcompany: itemcompany,
                    quantity: parseFloat($('#txtqty_' + itemid).val()),
                    qtyinprimary: '',
                    mfddate: $('#txtmfd_' + itemid).val(),
                    expdate: $('#txtexp_' + itemid).val(),
                }

                stockitems.push(arraData);
            });

            var stockData = JSON.stringify(stockData);
            var stockitems = JSON.stringify(stockitems);

            $('.commonLoaderV1').show();
            $('#btnUpdateStock').attr('disabled', 'disabled');
            $.ajax({
                type: 'POST',
                url: '../updatestock',
                data: {stockData: stockData, stockitems: stockitems},
            }).done(function (data) {
                $('.commonLoaderV1').hide();
                $('#btnUpdateStock').removeAttr('disabled');
                window.location.href = data.redirecturl;
            });


        });

        $(".clsmanfdate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy'
        });

        $(".clsexpdate").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy',
            minDate: '0'
        });
    });
</script>
@endsection

