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
                <p style="padding-bottom:5px;color:#000;margin: 0;"><strong style="padding-right:5px;">Date :</strong><span><?php echo date('d-m-Y', strtotime($rfqdata->created_at)); ?></span></p>
                <p style="margin: 0;"><strong style="padding-right:5px;color:#000;">RFQ. No :</strong><span>{{$rfqdata->rfq_code}}</span></p>
            </td>
            <td><span style="color:#760000; font-size:25px;font-weight:bold; display:block;padding-bottom:15px;">RFQ</span></td>
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
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
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
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
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
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
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
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
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

            <td colspan="4"  style="padding-left:30px;vertical-align:top;">
                <table border="0" cellpadding="3" width="300px" border="0" cellspacing="0" style="height:50px; float:right;">
                    <tr>
                        <td style="text-align:right;font-size:12px;">Sub Total :</td>
                        <td style="width:200px;text-align:right;font-size:14px;"><?php echo Customhelper::numberformatter($totalprice); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;font-size:12px;">VAT Amount :</td>
                        <td style="width:200px;text-align:right;font-size:12px;"><?php echo Customhelper::numberformatter($rfqdata->total_vat); ?></td>
                    </tr>
                    <tr >
                        <td  style=" text-align:right;font-weight:bold;font-size:14px;height:20px;color:#760000;">Total :</td>
                        <td style="width:200px;font-weight:bold;font-size:14px;color:#760000;height:20px;text-align:right;"><?php echo Customhelper::numberformatter($totalprice+$rfqdata->total_vat); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr>
            <td colspan="3">
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; -webkit-appearance: none;-moz-appearance:none;appearance:none;margin-bottom: 15px;background-color: #c7ea63;" >
                    <thead style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr >
                            <th width="33.33%" style="border-left: 0px solid;border-right: 0px solid;">Other Details</th>
                            <th width="33.33%" style="border-left: 0px solid;border-right: 0px solid;"></th>
                            <th width="33.33%" style="border-left: 0px solid;border-right: 0px solid;"></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        
                        <tr>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Mode of payment : <strong>{{$rfqdata->payment_mode}}</strong></td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Credit days : <strong>{{$rfqdata->creditdays}}</strong></td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Terms of payment : <strong>{{$rfqdata->payment_terms}}</strong></td>
                            
                        </tr>
                        <tr>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Delivery place : <strong>{{$rfqdata->delivery_place}}</strong></td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Delivery date : <strong><?php if($rfqdata->delivery_date){ echo date('d-m-Y',  strtotime($rfqdata->delivery_date));}?></strong></td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Purchase planing date : <strong><?php if($rfqdata->planning_date){ echo date('d-m-Y',  strtotime($rfqdata->planning_date));}?></strong></td>
                        </tr>
                        <?php
                        if($rfqdata->stock_period_from)
                            $date1 =date_create($rfqdata->stock_period_from);
                        else
                            $date1 = '';
                        
                        if($rfqdata->stock_period_to)
                            $date2 =date_create($rfqdata->stock_period_to);
                        else
                            $date2 = '';
                        
                        if(($date2 !='') && ($date1 != '')){
                            $date3 = date_diff($date1,$date2)->format("%a"); $date3++;
                        }
                        else{
                            $date3 = '0';
                        }
                        ?>
                        <tr>
                            <td style="border-left: 0px solid;border-right: 0px solid;">
                             <table style="font-size:12px; vertical-align: middle">
                                <tr>
                                   <td>Available stock in hand : <strong>{{$rfqdata->stock_in_hand}}</strong></td>
                                   <td style="border-left:1px solid #760000">In days :  <strong><?= $date3 ?></strong></td>
                                </tr>
                            </table>
                            </td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Available stock period from : <strong><?php if($rfqdata->stock_period_from){ echo date('d-m-Y',  strtotime($rfqdata->stock_period_from));}?></strong></td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Available stock period to : <strong><?php if($rfqdata->stock_period_to){ echo date('d-m-Y',  strtotime($rfqdata->stock_period_to));}?></strong></td>
                            
                        </tr>
                        <?php
                        if($rfqdata->forecast_from)
                            $date1 =date_create($rfqdata->forecast_from);
                        else
                            $date1 = '';
                        
                        if($rfqdata->forecast_to)
                            $date2 =date_create($rfqdata->forecast_to);
                        else
                            $date2 = '';
                        
                        if(($date2 !='') && ($date1 != '')){
                            $date3 = date_diff($date1,$date2)->format("%a"); $date3++;
                        }
                        else{
                            $date3 = '0';
                        }
                        ?>
                        <tr>
                            <td>
                            <table style="font-size:12px; vertical-align: middle">
                                <tr>
                                    <td >RFQ stock total : <strong>{{$rfqdata->stock_total}}</strong></td>
                                   <td style="border-left:1px solid #760000">In days :<strong><?= $date3 ?></strong></td>
                                </tr>
                            </table>
                            </td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">RFQ Order forecast period from : <strong><?php  if($rfqdata->forecast_from){ echo date('d-m-Y',  strtotime($rfqdata->forecast_from));}?></strong></td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">RFQ Order forecast period to : <strong><?php  if($rfqdata->forecast_to){ echo date('d-m-Y',  strtotime($rfqdata->forecast_to));}?></strong></td>
                        </tr>
                        <tr>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Last purchase date : <strong><?php if($rfqdata->last_purchase_date){ echo date('d-m-Y',  strtotime($rfqdata->last_purchase_date));}?></strong></td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Last purchase quantity : <strong>{{$rfqdata->last_qty}}</strong></td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Last purchase value : <strong>{{$rfqdata->last_value}}</strong></td>
                        </tr>
                        
                        <tr>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Product specification : <strong>{{$rfqdata->product_spec}}</strong></td>
                            <td style="border-left: 0px solid;border-right: 0px solid;">Approved supplier or not : <strong>{{$rfqdata->isapprovedsupplier}}</strong></td>
                            
                            
                        </tr>
                        
                    </tbody>

                </table>
            </td>
        </tr>
        
        
        <tr>
            <td colspan="4">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Created By</th>
                    </tr>
                    <tr>
                        <td width="33.33%">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$rfqdata->empcode}}</strong>
                                    </td>
                                 </tr>
                            </table>
                        </td>
                        <td width="33.33%">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$rfqdata->empname}} {{$rfqdata->empaname}}</strong>
                                    </td>
                                 </tr>
                            </table>
                        </td>
                        <td width="33.33%">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
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
            <td colspan="4">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Confirmed By</th>
                    </tr>
                    <tr>
                        <td width="33.33%">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$rfqdata->approvercode}}</strong>
                                    </td>
                                 </tr>
                            </table>
                        </td>
                        <td width="33.33%">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$rfqdata->approvername}} {{$rfqdata->approveraname}}</strong>
                                    </td>
                                 </tr>
                            </table>
                        </td>
                        <td width="33.33%">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
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
    <?php if($rfqdata->confirm_status==1){?>
        <a class="btnAction saveDoc bgBlue" href="#" id="btnsendemail" style="margin-left: 15px;">Send To Supplier</a>
    <?php }?>
    <input type="hidden" value="{{$rfqdata->id}}" id="rfq_id">
    <input type="hidden" value="{{$rfqdata->mailed_status}}" id="mailed_status">
    <div class="formViewWrapper">
        <div class="formContentHolder">

            <div class="formHeader">
                <div class="formTitle">
                    <p><strong>Date :</strong><span><?php echo date('d-m-Y', strtotime($rfqdata->created_at)); ?></span></p>
                    <p><strong>RFQ. No :</strong><span>{{$rfqdata->rfq_code}}</span></p>
                </div>
                <div class="textHeading"><h2>RFQ</h2></div>
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
                            <strong>{{$rfqdata->suppliercode}}</strong>
                        </p>
                        <p>
                            <span>Name :</span>
                            <strong>{{$rfqdata->supplierfname}} {{$rfqdata->supplieraname}}</strong>
                        </p>
                       
                        <p>
                            <span>Country :</span>
                            <strong>{{$rfqdata->supnation}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Contact No :</span>
                            <strong>{{$rfqdata->supmob}}</strong>
                        </p>
                        <p>
                            <span>CR No :</span>
                            <strong>{{$rfqdata->cr_number}}</strong>
                        </p>
                        
                        <p>
                            <span>VAT No:</span>
                            <strong>{{$rfqdata->supplier_pin}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                       <p>
                            <span>Address :</span>
                            <strong>{{$rfqdata->supplier_address}}</strong>
                        </p>
                        <p>
                            <span>Email :</span>
                            <strong>{{$rfqdata->supemail}}</strong>
                        </p>
                        <p>
                            <span>Contact Person :</span>
                            <strong>{{$rfqdata->contact_person}}</strong>
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
                            <strong>{{$rfqdata->bank_beneficiary_name}}</strong>
                        </p>
                       
                        <p>
                            <span>Branch Name :</span>
                            <strong>{{$rfqdata->bank_branch_name}}</strong>
                        </p>
                         <p>
                            <span>Bank Name :</span>
                            <strong>{{$rfqdata->bank_name}}</strong>
                        </p>
                        
                      
                    </div>
                    <div class="formDetails">
                         <p>
                            <span>Swift Code :</span>
                            <strong>{{$rfqdata->bank_swift_code}}</strong>
                        </p>
                        
                        <p>
                            <span>Account Number :</span>
                            <strong>{{$rfqdata->bank_account_number}}</strong>
                        </p>
                        <p>
                            <span>IBAN Number :</span>
                            <strong>{{$rfqdata->bank_iban_no}}</strong>
                        </p>
                       
                    </div>
                    <div class="formDetails">
                       
                        <p>
                            <span>City :</span>
                            <strong>{{$rfqdata->bank_city}}</strong>
                        </p>
                        <p>
                            <span>Country :</span>
                            <strong>{{$rfqdata->banknation}}</strong>
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
                        <div class="custCol-6 alignRight"><?php echo Customhelper::numberformatter($rfqdata->total_vat); ?></div>
                    </div>
                    <div class="custRow ">
                        <div class="custCol-6 total alignRight">Total :</div>
                        <div class="custCol-6 total alignRight"><?php echo Customhelper::numberformatter($totalprice+$rfqdata->total_vat); ?></div>
                    </div>
                </div>
            </div>
            
            
            <div class="formTableWrapper" style="margin-bottom: 15px;background-color: #c7ea63;">
                <table cellpadding="3" class="tblotherdetrfq" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0">
                    <thead>
                        <tr >
                            <th width="33.33%">Other Details</th>
                            <th width="33.33%"></th>
                            <th width="33.33%"></th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        
                        <tr>
                            <td >Mode of payment : <strong>{{$rfqdata->payment_mode}}</strong></td>
                            <td >Credit days : <strong>{{$rfqdata->creditdays}}</strong></td>
                            <td>Terms of payment : <strong>{{$rfqdata->payment_terms}}</strong></td>
                            
                        </tr>
                        <tr>
                            <td>Delivery place : <strong>{{$rfqdata->delivery_place}}</strong></td>
                            <td>Delivery date : <strong><?php if($rfqdata->delivery_date){ echo date('d-m-Y',  strtotime($rfqdata->delivery_date));}?></strong></td>
                            <td>Purchase planing date : <strong><?php if($rfqdata->planning_date){ echo date('d-m-Y',  strtotime($rfqdata->planning_date));}?></strong></td>
                        </tr>
                        <?php
                        if($rfqdata->stock_period_from)
                            $date1 =date_create($rfqdata->stock_period_from);
                        else
                            $date1 = '';
                        
                        if($rfqdata->stock_period_to)
                            $date2 =date_create($rfqdata->stock_period_to);
                        else
                            $date2 = '';
                        
                        if(($date2 !='') && ($date1 != '')){
                            $date3 = date_diff($date1,$date2)->format("%a"); $date3++;
                        }
                        else{
                            $date3 = '0';
                        }
                        ?>
                        <tr>
                            <td>
                             <table>
                                <tr>
                                   <td>Available stock in hand : <strong>{{$rfqdata->stock_in_hand}}</strong></td>
                                   <td style="border-left:1px solid #760000">In days : <strong><?= $date3 ?></strong></td>
                                </tr>
                            </table>
                            </td>
                            <td valign="top">Available stock period from : <strong><?php if($rfqdata->stock_period_from){ echo date('d-m-Y',  strtotime($rfqdata->stock_period_from));}?></strong></td>
                            <td valign="top">Available stock period to : <strong><?php if($rfqdata->stock_period_to){ echo date('d-m-Y',  strtotime($rfqdata->stock_period_to));}?></strong></td>
                        </tr>
                        <?php
                        if($rfqdata->forecast_from)
                            $date1 =date_create($rfqdata->forecast_from);
                        else
                            $date1 = '';
                        
                        if($rfqdata->forecast_to)
                            $date2 =date_create($rfqdata->forecast_to);
                        else
                            $date2 = '';
                        
                        if(($date2 !='') && ($date1 != '')){
                            $date3 = date_diff($date1,$date2)->format("%a"); $date3++;
                        }
                        else{
                            $date3 = '0';
                        }
                        ?>
                        <tr>
                            <td>
                            <table>
                                <tr>
                                    <td >RFQ stock total : <strong>{{$rfqdata->stock_total}}</strong></td>
                                   <td style="border-left:1px solid #760000">In days : <strong><?= $date3 ?></strong></td>
                                </tr>
                            </table>
                            </td>
                            <td >RFQ Order forecast period from : <strong><?php  if($rfqdata->forecast_from){ echo date('d-m-Y',  strtotime($rfqdata->forecast_from));}?></strong></td>
                            <td >RFQ Order forecast period to : <strong><?php  if($rfqdata->forecast_to){ echo date('d-m-Y',  strtotime($rfqdata->forecast_to));}?></strong></td>
                        </tr>
                        <tr>
                            <td >Last purchase date : <strong><?php if($rfqdata->last_purchase_date){ echo date('d-m-Y',  strtotime($rfqdata->last_purchase_date));}?></strong></td>
                            <td >Last purchase quantity : <strong>{{$rfqdata->last_qty}}</strong></td>
                            <td >Last purchase value : <strong>{{$rfqdata->last_value}}</strong></td>
                        </tr>
                        
                        <tr>
                            <td>Product specification : <strong>{{$rfqdata->product_spec}}</strong></td>
                            <td>Approved supplier or not : <strong>{{$rfqdata->isapprovedsupplier}}</strong></td>
                        </tr>
                        
                    </tbody>
                </table>

            </div>
            
            
            <div class="formDetailsWrapper">
                <span>Created By</span>
                <div class="formDetailsTable">
                    <div class="formDetails">
                        <p>
                            <span>Employee Code :</span>
                            <strong>{{$rfqdata->empcode}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Employee Name  :</span>
                            <strong>{{$rfqdata->empname}} {{$rfqdata->empaname}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Job Position :</span>
                            <strong><?php echo str_replace("_", " ", $rfqdata->jobpos) ?></strong>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="formDetailsWrapper">
                <span>Confirmed By</span>
                <div class="formDetailsTable">
                    <div class="formDetails">
                        <p>
                            <span>Employee Code :</span>
                            <strong>{{$rfqdata->approvercode}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Employee Name  :</span>
                            <strong>{{$rfqdata->approvername}} {{$rfqdata->approveraname}}</strong>
                        </p>
                    </div>
                    <div class="formDetails">
                        <p>
                            <span>Job Position :</span>
                            <strong><?php echo str_replace("_", " ", $rfqdata->approverjobpos) ?></strong>
                        </p>
                    </div>
                </div>
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
    
    $('body').on('click','#btnsendemail',function(){
       
       if($("#mailed_status").val()==1){
           var blnConfirm=confirm("Mail already sent to supplier, do you want to send again?");
           if(!blnConfirm){
               return;
           }
       }
       
        $.ajax({
            type: 'POST',
            url: '../mailto_supplier',
            data: {rfq_id: $('#rfq_id').val()},
            beforeSend: function () {
                $('.commonLoaderV1').show();
            },
            success: function (return_data) {
                if(return_data==1){
                    toastr.success('RFQ Has Been Send To Supplier');
                }
                if(return_data==-1){
                    toastr.error('Failed To Send Email');
                }
               
               $('.commonLoaderV1').hide();
            }
        });
    });
    
</script>
@endsection
