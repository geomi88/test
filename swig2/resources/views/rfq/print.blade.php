<!DOCTYPE html>
<html>
<body style="margin: 0; padding: 0;font-family:DejaVu Sans;">
    <section id="container">
        <table border="0" cellpadding="5" width="100%" height="100%" border="0" cellspacing="0" style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;color:#000;border:3px solid #760000;">
                <tr>
                    <td>
                        <p style="padding-bottom:5px;color:#000;margin: 0;"><strong style="padding-right:5px;">Date :</strong><span><?php echo date('d-m-Y', strtotime($rfqdata->created_at)); ?></span></p>
                        <p style="margin: 0;"><strong style="padding-right:5px;color:#000;">RFQ. No :</strong><span>{{$rfqdata->rfq_code}}</span></p>
                    </td>
                    <td><span style="color:#760000; font-size:25px;font-weight:bold; display:block;padding-bottom:15px;text-align: center;">RFQ</span></td>
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
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->suppliercode}}</strong>
                                            </td>
                                         </tr>
                                          <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Name :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->supplierfname}} {{$rfqdata->supplieraname}}</strong>
                                            </td>
                                         </tr>

                                         <tr>
                                             <td>
                                                 <span style="padding-right:5px;display:inline-block;color:#000;">Country :</span>
                                                 <strong style="display:inline-block;color:#000;">{{$rfqdata->supnation}}</strong>
                                             </td>
                                         </tr>

                                    </table>
                                </td>
                                <td width="33.33%" style="vertical-align:top;">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                         <tr>
                                             <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Contact No :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->supmob}}</strong>
                                             </td>
                                         </tr>
                                         <tr>
                                             <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">CR No :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->cr_number}}</strong>
                                             </td>
                                         </tr>
                                         <tr>
                                             <td>
                                                 <span style="padding-right:5px;display:inline-block;color:#000;">VAT No:</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->supplier_pin}}</strong>
                                             </td>
                                         </tr>
                                    </table>

                                </td>
                                <td width="33.33%" style="vertical-align:top;">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr>
                                             <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Address :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->supplier_address}}</strong>
                                             </td>
                                         </tr>
                                         <tr>
                                             <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Email :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->supemail}}</strong>
                                             </td>
                                         </tr>
                                         <tr>
                                             <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Contact Person :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->contact_person}}</strong>
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
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->bank_beneficiary_name}}</strong>
                                            </td>
                                         </tr>

                                         <tr>
                                             <td>
                                                 <span style="padding-right:5px;display:inline-block;color:#000;">Branch Name :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->bank_branch_name}}</strong>
                                             </td>
                                        </tr>
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Bank Name :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->bank_name}}</strong>
                                            </td>
                                         </tr>

                                    </table>

                                </td>
                                <td width="33.33%" style="vertical-align:top;">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr>
                                             <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Swift Code :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->bank_swift_code}}</strong>
                                             </td>
                                         </tr>
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Account Number :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->bank_account_number}}</strong>
                                            </td>
                                         </tr>

                                         <tr>
                                             <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">IBAN Number :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->bank_iban_no}}</strong>
                                             </td>
                                         </tr>

                                    </table>
                                </td>
                                <td width="33.33%" style="vertical-align:top;">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr>
                                             <td>
                                                 <span style="padding-right:5px;display:inline-block;color:#000;">City :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->bank_city}}</strong>
                                             </td>
                                        </tr>
                                         <tr>
                                             <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Country :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->banknation}}</strong>
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
                                @foreach ($rfqitems as $item)
                                <?php $totalprice+=$item->total_price; ?>
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
                    <td   style="padding-left:30px;vertical-align:top;">
                        <table border="0" cellpadding="3"  border="0" cellspacing="0" >
                            <tr >
                                <td style="text-align:right;font-size:12px; white-space: nowrap;">Sub Total :</td>
                                <td style="width:120;text-align:right;font-size:14px;"><?php echo Customhelper::numberformatter($totalprice); ?></td>
                            </tr>
                            <tr>
                                <td style="text-align:right;font-size:12px;white-space: nowrap;">VAT Amount :</td>
                                <td style="width:120px;text-align:right;font-size:12px;"><?php echo Customhelper::numberformatter($rfqdata->total_vat); ?></td>
                            </tr>
                            <tr >
                                <td  style=" text-align:right;font-weight:bold;white-space: nowrap;font-size:14px;height:20px;color:#760000;">Total :</td>
                                <td style="width:120px;font-weight:bold;font-size:14px;color:#760000;height:20px;text-align:right;"><?php echo Customhelper::numberformatter($totalprice+$rfqdata->total_vat); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                            <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                                <th colspan="3">Created By</th>
                            </tr>
                            <tr>
                                <td width="33.33%">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->empcode}}</strong>
                                            </td>
                                         </tr>
                                    </table>
                                </td>
                                <td width="33.33%">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->empname}} {{$rfqdata->empaname}}</strong>
                                            </td>
                                         </tr>
                                    </table>
                                </td>
                                <td width="33.33%">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Job Position :</span>
                                                <strong style="display:inline-block;color:#000;"><?php echo str_replace("_", " ", $rfqdata->jobpos) ?></strong>
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
                        <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                            <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                                <th colspan="3">Confirmed By</th>
                            </tr>
                            <tr>
                                <td width="33.33%">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->approvercode}}</strong>
                                            </td>
                                         </tr>
                                    </table>
                                </td>
                                <td width="33.33%">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name :</span>
                                                <strong style="display:inline-block;color:#000;">{{$rfqdata->approvername}} {{$rfqdata->approveraname}}</strong>
                                            </td>
                                         </tr>
                                    </table>
                                </td>
                                <td width="33.33%">
                                    <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:DejaVu Sans, Helvetica, sans-serif; font-size:12px;">
                                        <tr style="padding-bottom:5px;margin:0;">
                                            <td>
                                                <span style="padding-right:5px;display:inline-block;color:#000;">Job Position :</span>
                                                <strong style="display:inline-block;color:#000;"><?php echo str_replace("_", " ", $rfqdata->approverjobpos) ?></strong>
                                            </td>
                                         </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="3" style="border-bottom:2px solid #760000; height:10px;"></td>
                </tr>

                <tr>
                    <td colspan="3" style="padding-top:10px;text-align:center;">
                        <img src="{{ URL::asset('images/imgPartners.png')}}" style="width: 500px;" alt="Partners">
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