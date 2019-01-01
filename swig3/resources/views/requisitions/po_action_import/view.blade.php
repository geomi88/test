@extends('layouts.main')
@section('content')
<script>
    $(document).ready(function ()
    {
        $('.print').click(function () {
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
    <table border="0" cellpadding="5" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;color:#000;border:3px solid #760000;">
        <tr>
            <td>
                <p style="padding-bottom:5px;color:#000;margin: 0;"><strong style="padding-right:5px;">Order Type :</strong><span><?php if ($orderdata->order_type == 3) {
    $type = "Import";
    echo "IPO";
} else {
    $type = "Local";
    echo "LPO";
} ?></span></p>
                <p style="padding-bottom:5px;color:#000;margin: 0;"><strong style="padding-right:5px;">Date :</strong><span><?php echo date('d-m-Y', strtotime($orderdata->created_at)); ?></span></p>
                <p style="margin: 0;"><strong style="padding-right:5px;color:#000;">PO. No :</strong><span>{{$orderdata->order_code}}</span></p>
            </td>
            <td><span style="color:#760000; font-size:20px;font-weight:bold; display:block;padding-bottom:10px;">{{$type}} Purchase Order</span></td>
            <td style="text-align:right;vertical-align:top;">
                <img src="{{ URL::asset('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est." style="width:105px;" >
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Supplier</th>
                    </tr>

                    <tr>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->suppliercode}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->supplierfname}} {{$orderdata->supplieraname}}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Country :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->supnation}}</strong>
                                    </td>
                                </tr>

                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Contact No :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->supmob}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">CR No :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->cr_number}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">VAT No:</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->supplier_pin}}</strong>
                                    </td>
                                </tr>
                            </table>

                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Address :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->supplier_address}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Email :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->supemail}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Contact Person :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->contact_person}}</strong>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style=" font-family:Arial, Helvetica, sans-serif; font-size:12px;border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Supplier Bank </th>
                    </tr>

                    <tr>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Beneficiary Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->bank_beneficiary_name}}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Branch Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->bank_branch_name}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Bank Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->bank_name}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Other Reference :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->bank_other_reference}}</strong>
                                    </td>
                                </tr>
                            </table>

                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Swift Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->bank_swift_code}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Account Number :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->bank_account_number}}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">IBAN Number :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->bank_iban_no}}</strong>
                                    </td>
                                </tr>

                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">City :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->bank_city}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Country :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->banknation}}</strong>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Currency :</span>
                                        <strong style="display:inline-block;color:#000;"></strong>
                                    </td>
                                </tr>

                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; -webkit-appearance: none;-moz-appearance:none;appearance:none;" >
                    <thead style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr >
                            <th width="80px;">Item Code </th>
                            <th width="250px;">Item Name</th>
                            <th width="50px;">Qty</th>
                            <th width="50px;">Unit </th>
                            <th width="80px;">Price/Unit</th>
                            <th width="100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
<?php $totalprice = 0; ?>
                        @foreach ($requisitionitems as $item)
<?php $totalprice += $item->total_price; ?>
                        <tr>
                            <td>{{$item->product_code}}</td>
                            <td>{{$item->productname}}</td>
                            <td style="text-align:right;">{{$item->quantity}}</td>
                            <td>{{$item->unit}}</td>
                            <td style="text-align:right;"><?php echo Customhelper::numberformatter($item->unit_price); ?></td>
                            <td style="text-align:right;"><?php echo Customhelper::numberformatter($item->total_price); ?></td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </td>
        </tr>
        <tr>

            <td colspan="4"  style="padding-left:30px;vertical-align:top;">
                <table border="0" cellpadding="3" width="300px" border="0" cellspacing="0" style="height:50px; float:right;">
                    <tr>
                        <td style="text-align:right;font-size:12px;">Sub Total :</td>
                        <td style="width:200px;text-align:right;font-size:14px;"><?php echo Customhelper::numberformatter($totalprice); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;font-size:12px;">VAT Amount :</td>
                        <td style="width:200px;text-align:right;font-size:12px;"><?php echo Customhelper::numberformatter($orderdata->total_vat); ?></td>
                    </tr>
                    <tr >
                        <td  style=" text-align:right;font-weight:bold;font-size:14px;height:20px;color:#760000;">Total Amount:</td>
                        <td style="width:200px;font-weight:bold;font-size:14px;color:#760000;height:20px;text-align:right;"><?php echo Customhelper::numberformatter($totalprice + $orderdata->total_vat); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Order Details </th>
                    </tr>

                    <tr>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Suppliers Quotation :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->quotation}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Delivery Place :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->delivery_destination}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Terms of Delivery 1  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->delivery_terms1}}</strong>
                                    </td>
                                </tr>

                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr >
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Quotation Date:</span>
                                        <strong style="display:inline-block;color:#000;"><?php if (isset($orderdata->quotation_date)) {
    echo date("d-m-Y", strtotime($orderdata->quotation_date));
} else {
    echo "";
} ?></strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Delivery Date:</span>
                                        <strong style="display:inline-block;color:#000;"><?php if (isset($orderdata->delivery_date)) {
    echo date("d-m-Y", strtotime($orderdata->delivery_date));
} else {
    echo "";
} ?></strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Terms of Delivery 2  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->delivery_terms2}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">

                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Mode of Payment :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->payment_mode}}</strong>
                                    </td>
                                </tr>
                                <?php if ($orderdata->payment_mode == "Credit") { ?>
                                    <tr>
                                        <td>
                                            <span style="padding-right:5px;display:inline-block;color:#000;">Credit Days :</span>
                                            <strong style="display:inline-block;color:#000;">{{$orderdata->credit_days}}</strong>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <span style="padding-right:5px;display:inline-block;color:#000;">Terms of Payment :</span>
                                            <strong style="display:inline-block;color:#000;">{{$orderdata->payment_term}}</strong>
                                        </td>
                                    </tr>
<?php } else { ?>
                                    <tr>
                                        <td>
                                            <span style="padding-right:5px;display:inline-block;color:#000;">Terms of Payment :</span>
                                            <strong style="display:inline-block;color:#000;">{{$orderdata->payment_term}}</strong>
                                        </td>
                                    </tr>
                                    <tr><td>&nbsp;</td></tr>
<?php } ?>



                            </table>

                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        <tr>
            <td colspan="4">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Requested By</th>
                    </tr>
                    <tr>
                        <td width="33.33%">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->empcode}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$orderdata->empname}} {{$orderdata->empaname}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Job Position :</span>
                                        <strong style="display:inline-block;color:#000;"><?php echo str_replace("_", " ", $orderdata->jobpos) ?></strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td colspan="3" style="color:#760000;font-size:14px;font-weight:bold;">
                Approved By
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; -webkit-appearance: none;-moz-appearance:none;appearance:none;" >
                    <thead style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr>
                            <th width="100px">Code</th>
                            <th>Name</th>
                            <th >Job Position </th>
                            <th>Sign</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($action_takers as $actor)
                        <tr>
                            <td>{{$actor->code}}</td>
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo str_replace("_", " ", $actor->jobpos); ?></td>
                            <td></td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="border-bottom:2px solid #760000; height:10px;"></td>
        </tr>

        <tr>
            <td colspan="3" style="padding-top:10px;text-align:center;">
                <img src="{{ URL::asset('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
            </td>
        </tr>

        <tr>
            <td colspan="3" style="padding:5px 0 10px; color:#760000;text-align:center;">
                P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new
            </td>
        </tr>
    </table>
</div>
<div class="innerContent">

    <a class="btnAction print bgGreen" href="#" style="margin-bottom: 15px;">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" id="<?php (($orderdata->mailed_status == 1) ? '' : 'btnsendemail')  ?>" style="margin-left: 15px;" <?php (($orderdata->mailed_status == 1) ? 'disabled' : '')  ?>>Send To Supplier</a>
    <a class="btnAction saveDoc bgBlue" href="#" id="btnsendemail" style="margin-left: 15px;">Send To Warehouse</a>
    <input type="hidden" value="{{$orderdata->id}}" id="po_id" name="po_id">
    <input type="hidden" value="{{$orderdata->mailed_status}}" id="mailed_status">
    <div class="formViewWrapper">
        <div class="formContentHolder">

            <div class="formHeader">
                <div class="formTitle">
                    <p><strong>Order Type :</strong><span><?php if ($orderdata->order_type == 3) {
    $type = "Import";
    echo "IPO";
} else {
    $type = "Local";
    echo "LPO";
} ?></span></p>
                    <p><strong>Date :</strong><span><?php echo date('d-m-Y', strtotime($orderdata->created_at)); ?></span></p>
                    <p><strong>PO. No :</strong><span>{{$orderdata->order_code}}</span></p>
                </div>
                <div class="textHeading"><h2>{{$type}} Purchase Order</h2></div>
                <div class="companyLogo">
                    <img src="{{ URL::asset('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est.">
                </div>
            </div>
            <div class="formDetailsWrapper">
                <span>Supplier</span>
                <div class="formDetailsTable">
                    <div class="formDetails">
                        <p>
                            <span>Code :</span>
                            <strong>{{$orderdata->suppliercode}}</strong>
                        </p>
                        <p>
                            <span>Name :</span>
                            <strong>{{$orderdata->supplierfname}} {{$orderdata->supplieraname}}</strong>
                        </p>

                        <p>
                            <span>Country :</span>
                            <strong>{{$orderdata->supnation}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Contact No :</span>
                            <strong>{{$orderdata->supmob}}</strong>
                        </p>
                        <p>
                            <span>CR No :</span>
                            <strong>{{$orderdata->cr_number}}</strong>
                        </p>

                        <p>
                            <span>VAT No:</span>
                            <strong>{{$orderdata->supplier_pin}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Address :</span>
                            <strong>{{$orderdata->supplier_address}}</strong>
                        </p>
                        <p>
                            <span>Email :</span>
                            <strong>{{$orderdata->supemail}}</strong>
                        </p>
                        <p>
                            <span>Contact Person :</span>
                            <strong>{{$orderdata->contact_person}}</strong>
                        </p>
                    </div>
                </div>
            </div>


            <div class="formDetailsWrapper">
                <span>Supplier Bank</span>
                <div class="formDetailsTable">
                    <div class="formDetails">
                        <p>
                            <span>Beneficiary Name :</span>
                            <strong>{{$orderdata->bank_beneficiary_name}}</strong>
                        </p>

                        <p>
                            <span>Branch Name :</span>
                            <strong>{{$orderdata->bank_branch_name}}</strong>
                        </p>
                        <p>
                            <span>Bank Name :</span>
                            <strong>{{$orderdata->bank_name}}</strong>
                        </p>

                        <p>
                            <span>Other Reference :</span>
                            <strong>{{$orderdata->bank_other_reference}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Swift Code :</span>
                            <strong>{{$orderdata->bank_swift_code}}</strong>
                        </p>

                        <p>
                            <span>Account Number :</span>
                            <strong>{{$orderdata->bank_account_number}}</strong>
                        </p>
                        <p>
                            <span>IBAN Number :</span>
                            <strong>{{$orderdata->bank_iban_no}}</strong>
                        </p>

                    </div>
                    <div class="formDetails">

                        <p>
                            <span>City :</span>
                            <strong>{{$orderdata->bank_city}}</strong>
                        </p>
                        <p>
                            <span>Country :</span>
                            <strong>{{$orderdata->banknation}}</strong>
                        </p>
                        <p>
                            <span>Currency :</span>
                            <strong></strong>
                        </p>
                    </div>
                </div>
            </div>
            <div class="formTableWrapper">
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="100px;">Item Code</th>
                            <th width="250px;">Item Name</th>
                            <th width="50px;">Qty </th>
                            <th width="50px;">Unit</th>
                            <th width="80px;">Price/Unit</th>
                            <th width="100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
<?php $totalprice = 0; ?>
                        @foreach ($requisitionitems as $item)
<?php $totalprice += $item->total_price; ?>
                        <tr>
                            <td>{{$item->product_code}}</td>
                            <td>{{$item->productname}}</td>
                            <td style="text-align:right;">{{$item->quantity}}</td>
                            <td>{{$item->unit}}</td>
                            <td style="text-align:right;"><?php echo Customhelper::numberformatter($item->unit_price); ?></td>
                            <td style="text-align:right;"><?php echo Customhelper::numberformatter($item->total_price); ?></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            <div class="custRow">
                <div class="custCol-8"></div>
                <div class="custCol-4 totalValueHolder">
                    <div class="custRow ">
                        <div class="custCol-6 alignRight">Sub Total :</div>
                        <div class="custCol-6 alignRight"><?php echo Customhelper::numberformatter($totalprice); ?></div>
                    </div>
                    <div class="custRow ">
                        <div class="custCol-6 alignRight">VAT Amount :</div>
                        <div class="custCol-6 alignRight"><?php echo Customhelper::numberformatter($orderdata->total_vat); ?></div>
                    </div>
                    <div class="custRow ">
                        <div class="custCol-6 total alignRight">Total Amount:</div>
                        <div class="custCol-6 total alignRight"><?php echo Customhelper::numberformatter($totalprice + $orderdata->total_vat); ?></div>
                    </div>
                </div>
            </div>
            <div class="formDetailsWrapper">
                <span>Order Details </span>
                <div class="formDetailsTable">
                    <div class="formDetails" style="padding-right:10px;">
                        <p>
                            <span>Suppliers Quotation No:</span>
                            <strong>{{$orderdata->quotation}}</strong>

                        </p>
                        <p>
                            <span>Delivery Place :</span>
                            <strong>{{$orderdata->delivery_destination}}</strong>

                        </p>
                        <p>
                            <span>Terms of Delivery 1:</span>
                            <strong>{{$orderdata->delivery_terms1}}</strong>
                        </p>



                    </div>
                    <div class="formDetails" style="padding:0 10px;">

                        <p>
                            <span>Quotation Date :</span>
                            <strong><?php if (isset($orderdata->quotation_date)) {
    echo date("d-m-Y", strtotime($orderdata->quotation_date));
} else {
    echo "";
} ?></strong>
                        </p>
                        <p>
                            <span>Delivery Date :</span>
                            <strong><?php if (isset($orderdata->delivery_date)) {
    echo date("d-m-Y", strtotime($orderdata->delivery_date));
} else {
    echo "";
} ?></strong>
                        </p>

                        <p>
                            <span>Terms of Delivery 2:</span>
                            <strong>{{$orderdata->delivery_terms2}}</strong>
                        </p>
                    </div>
                    <div class="formDetails" style="padding-left:10px;">
                        <p>
                            <span>Mode of Payment  :</span>
                            <strong>{{$orderdata->payment_mode}}</strong>
                        </p>
<?php if ($orderdata->payment_mode == "Credit") { ?>
                            <p>
                                <span>Credit Days  :</span>
                                <strong>{{$orderdata->credit_days}}</strong>
                            </p>
<?php } ?>

                        <p>
                            <span>Terms of Payment  :</span>
                            <strong>{{$orderdata->payment_term}}</strong>
                        </p>

                    </div>
                </div>
            </div>
            <div class="formDetailsWrapper">
                <span>Requested By</span>
                <div class="formDetailsTable">
                    <div class="formDetails">
                        <p>
                            <span>Employee Code :</span>
                            <strong>{{$orderdata->empcode}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Employee Name  :</span>
                            <strong>{{$orderdata->empname}} {{$orderdata->empaname}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Job Position :</span>
                            <strong><?php echo str_replace("_", " ", $orderdata->jobpos) ?></strong>
                        </p>
                    </div>
                </div>
            </div>
            <div class="formTableWrapper">
                <span>Approved By</span>
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none;">
                    <thead>
                        <tr>
                            <th width="100px">Code</th>
                            <th>Name</th>
                            <th>Job Position</th>
                            <th>Sign</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($action_takers as $actor)
                        <tr>
                            <td>{{$actor->code}}</td>
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo str_replace("_", " ", $actor->jobpos); ?></td>
                            <td></td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
        <div class="formFooter">
            <figure>
                <img src="{{ URL::asset('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
            </figure>
            <article>

                P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new

            </article>
        </div>
    </div>
    <div class="commonLoaderV1"></div>
</div>

<script>
    $('body').on('click', '#btnsendemail', function () {

        if ($("#mailed_status").val() == 1) {
            var blnConfirm = confirm("Mail already sent to supplier, do you want to send again?");
            if (!blnConfirm) {
                return;
            }
        }

        $.ajax({
            type: 'POST',
            url: '../mailto_supplier',
            data: {po_id: $('#po_id').val()},
            beforeSend: function () {
                $('.commonLoaderV1').show();
            },
            success: function (return_data) {
                if (return_data == 1) {
                    toastr.success('PO Has Been Send To Supplier');
                }
                if (return_data == -1) {
                    toastr.error('Failed To Send Email');
                }

                $('.commonLoaderV1').hide();
            }
        });
    });
</script>
@endsection
