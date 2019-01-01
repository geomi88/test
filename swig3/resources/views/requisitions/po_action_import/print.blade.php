<!DOCTYPE html>
<html>
    <body style="margin: 0; padding: 0;font-family:DejaVu Sans;">
        <section id="container">
            <table border="0" cellpadding="5" width="100%" height="100%" border="0" cellspacing="0" style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;color:#000;border:3px solid #760000;">
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
                    <td ><span style="color:#760000; font-size:18px;font-weight:bold; display:block;padding-bottom:10px;text-align: center;">{{$type}} Po Action Import </span></td>
                    <td style="text-align:right;vertical-align:top;">
                        <img src="{{ URL::asset('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est." style="width:105px;" >
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                            <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                                <th colspan="3">Supplier</th>
                            </tr>

                            <tr>
                                <td width="33.33%" style="vertical-align:top;">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
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
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
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
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
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
                        <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style=" font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;border:3px solid #760000;">
                            <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                                <th colspan="3">Supplier Bank </th>
                            </tr>

                            <tr>
                                <td width="33.33%" style="vertical-align:top;">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
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
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
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
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
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
                        <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px; -webkit-appearance: none;-moz-appearance:none;appearance:none;" >
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
                    <td  style=""></td>
                    <td  style=""></td>
                    <td  style="padding-left:30px;vertical-align:top;">
                        <table border="0" cellpadding="3" border="0" cellspacing="0" >
                            <tr>
                                <td style="text-align:right;font-size:12px;white-space: nowrap;">Sub Total :</td>
                                <td style="width:120px;text-align:right;font-size:14px;"><?php echo Customhelper::numberformatter($totalprice); ?></td>
                            </tr>
                            <tr>
                                <td style="text-align:right;font-size:12px;white-space: nowrap;">VAT Amount :</td>
                                <td style="width:120px;text-align:right;font-size:12px;"><?php echo Customhelper::numberformatter($orderdata->total_vat); ?></td>
                            </tr>
                            <tr >
                                <td  style=" text-align:right;font-weight:bold;white-space: nowrap;font-size:14px;height:20px;color:#760000;">Total Amount:</td>
                                <td style="width:120px;font-weight:bold;font-size:14px;color:#760000;height:20px;text-align:right;"><?php echo Customhelper::numberformatter($totalprice + $orderdata->total_vat); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                            <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                                <th colspan="3">Order Details </th>
                            </tr>

                            <tr>
                                <td width="33.33%" style="vertical-align:top;">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
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
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
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
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">

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
                    <td colspan="3">
                        <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                            <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                                <th colspan="3">Requested By</th>
                            </tr>
                            <tr>
                                <td width="33.33%">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code :</span>
                                                <strong style="display:inline-block;color:#000;">{{$orderdata->empcode}}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="33.33%">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name :</span>
                                                <strong style="display:inline-block;color:#000;">{{$orderdata->empname}} {{$orderdata->empaname}}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td width="33.33%">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
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
                        <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px; -webkit-appearance: none;-moz-appearance:none;appearance:none;" >
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
                        <img src="{{ URL::asset('images/imgPartners.png')}}" style="width:500px;" alt="Partners">
                    </td>
                </tr>

                <tr>
                    <td colspan="3" style="padding:5px 0 10px; color:#760000;text-align:center;">
                        P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new
                    </td>
                </tr>
            </table>
        </section>
    </body>
</html>