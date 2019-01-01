@extends('layouts.main')
@section('content')
<script>
$(document).ready(function ()
{
    $('#btnPrint').click(function () {
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
                <span style="color:#760000; font-size:20px;font-weight:bold; display:block;padding-bottom:10px;">Import Purchase Requisition</span>
                <p style="padding-bottom:5px;color:#000;margin: 0;"><strong style="padding-right:5px;">Date :</strong><span><?php echo date('d-m-Y', strtotime($requisitiondata->created_at)); ?></span></p>
                <p style="margin: 0;"><strong style="padding-right:5px;color:#000;">Requisition Code :</strong><span>{{$requisitiondata->requisition_code}}</span></p>
            </td>
            <td style="text-align:right;vertical-align:top;">
                <img src="{{ URL::asset('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est." style="width:150px;" >
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table border="0" cellpadding="2" width="100%" height="100%" border="0" cellspacing="0" style=" border:3px solid #760000;font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Requisition Details</th>
                    </tr>   
                    <tr>
                        <td width="33.33%" style="vertical-align:top;">
                             <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Title :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->title}}</strong>
                                    </td>
                                 </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Supplier  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->first_name}} {{$requisitiondata->alias_name}}</strong>
                                    </td>
                                 </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Created By :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->createdby}}</strong>
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
                <table border="0" cellpadding="2" width="100%" height="100%" border="0" cellspacing="0" style=" border:3px solid #760000;font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Supplier</th>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong style="display:block;color:#760000;font-size:14px;font-weight:bold;">Basic</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Code  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->code}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Country  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->nationality}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Name  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->first_name}} {{$supplierdata->alias_name}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Contact Info  :</span>
                                       <strong style="display:inline-block;color:#000;">{{$supplierdata->contact_number}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Mobile  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->mobile_number}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Email  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->email}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong style="display:block;color:#760000;font-size:14px;font-weight:bold;">Budget</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Total :</span>
                                        <strong style="display:inline-block;color:#000;">{{$budgetdata->format_initial}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Used :</span>
                                        <strong style="display:inline-block;color:#000;">{{$budgetdata->format_used}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Pending :</span>
                                        <strong style="display:inline-block;color:#000;">{{$budgetdata->format_balance}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <strong style="display:block;color:#760000;font-size:14px;font-weight:bold;">Bank</strong>
                        </td>
                    </tr>
                    <tr>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Name : </span>
                                <strong style="display:inline-block;color:#000;">{{$supplierdata->bank_branch_name}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Swift Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->bank_swift_code}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">

                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Ac No : </span>
                                <strong style="display:inline-block;color:#000;">{{$supplierdata->bank_account_number}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Country : </span>
                                <strong style="display:inline-block;color:#000;">{{$supplierdata->bankcountry}}</strong>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Beneficiary :</span>
                                <strong style="display:inline-block;color:#000;">{{$supplierdata->bank_beneficiary_name}}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Credit Limit :</span>
                                <strong style="display:inline-block;color:#000;">{{$supplierdata->creditlimitformated}}</strong>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table cellpadding="2" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none; font-family:Arial, Helvetica, sans-serif; font-size:12px;" >
                    <thead style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr >
                            <th width="80px;">Item Code </th>
                            <th width="170px;">Item Name</th>
                            <th width="80px;">RFQ</th>
                            <th width="50px;">Qty</th>
                            <th width="50px;">Unit </th>
                            <th width="80px;">Price/Unit</th>
                            <th width="100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php $totalprice=0; ?>
                        @foreach ($requisition_items as $item)
                        <?php $totalprice+=$item->total_price; ?>
                        <tr>
                            <td>{{$item->product_code}}</td>
                            <td>{{$item->product_name}}</td>
                            <td>{{$item->rfq_code}}</td>
                            <td style="text-align:right;">{{$item->quantity}}</td>
                            <td>{{$item->unitname}}</td>
                            <td style="text-align:right;"><?php echo Customhelper::numberformatter($item->unit_price); ?></td>
                            <td style="text-align:right;"><?php echo Customhelper::numberformatter($item->total_price); ?></td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </td>
        </tr>
        <tr>
            <td style="padding-right:30px;vertical-align:top;width:50%;">
                <label  style="padding:5px;background:#760000;font-size:12px;font-weight:bold;color:#fff;text-align:left;display:block;">Description</label> 
                <article  style="width:100%; min-height:40px;border:3px solid #760000;box-sizing: border-box;padding:3px;">
                    {{$requisitiondata->description}}
                </article>
            </td>
            <td  style="padding-left:30px;vertical-align:top;width:50%;font-size:12px;">
                <table border="0" cellpadding="2" width="100%" border="0" cellspacing="0" style="height:50px;">
                    <tr>
                        <td style="text-align:right;width:50%;font-size:12px;">Sub Total :</td>
                        <td style="width:50%;text-align:right;font-size:12px;"><?php echo Customhelper::numberformatter($totalprice); ?></td>
                    </tr>
                    <tr>
                        <td style="text-align:right;width:50%;font-size:12px;">VAT Amount (5%):</td>
                        <td style="width:50%;text-align:right;font-size:12px;"><?php echo Customhelper::numberformatter($requisitiondata->total_vat); ?></td>
                    </tr>
                    <tr >
                        <td  style=" text-align:right;width:50%;font-weight:bold;font-size:14px;height:20px;color:#760000;">Total Amount:</td>
                        <td style="width:50%;font-weight:bold;font-size:14px;color:#760000;height:20px;text-align:right;"><?php echo Customhelper::numberformatter($totalprice+$requisitiondata->total_vat); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr>
            <td colspan="3">
                <table border="0" cellpadding="2" width="100%" height="100%" border="0" cellspacing="0" style=" border:3px solid #760000;font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                   
                    <tr>
                        <td width="33.33%"  style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Mode of Payment :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->payment_mode}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%"  style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Delivery Place :</span>
                                    <strong style="display:inline-block;color:#000;">{{$requisitiondata->delivery_place}}</strong>
                                    </td>
                                </tr>
                            </table>
                           
                        </td>
                        <td width="33.33%"  style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Delivery Date :</span>
                                        <strong style="display:inline-block;color:#000;"><?php if($requisitiondata->delivery_date){ echo date("d-m-Y",  strtotime($requisitiondata->delivery_date));}?></strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td width="33.33%"  style="vertical-align:top;" colspan="2">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Terms of Payment :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->payment_terms}}</strong>
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
                <table border="0" cellpadding="2" width="100%" height="100%" border="0" cellspacing="0" style=" border:3px solid #760000;font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Requested By</th>
                    </tr>
                    <tr>
                        <td width="33.33%"  style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->empcode}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%"  style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name :</span>
                                    <strong style="display:inline-block;color:#000;">{{$requisitiondata->createdby}}</strong>
                                    </td>
                                </tr>
                            </table>
                           
                        </td>
                        <td width="33.33%"  style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                       <span style="padding-right:5px;display:inline-block;color:#000;">Job Position:</span>
                                        <strong style="display:inline-block;color:#000;"><?php echo str_replace("_", " ", $requisitiondata->jobposition)?></strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td  colspan="3" style="color:#760000;font-size:14px;font-weight:bold;">
                Approved By
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table cellpadding="2" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none;font-family:Arial, Helvetica, sans-serif; font-size:12px;" >
                    <thead style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr>
                            <th>Action Taker</th>
                            <th width="80px">Date</th>
                            <th width="350px">Comments  </th>
                            <th  width="80px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($action_takers as $actor)
                        <tr>
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo date('d-m-Y',  strtotime($actor->created_at));?></td>
                            <td>{{$actor->comments}}</td>
                            <td>{{$actor->action}}</td>
                        </tr>
                        @endforeach
                        @foreach ($next_action_takers_list as $actor)
                        <tr>
                            <td>{{$actor['name']}}</td>
                            <td></td>
                            <td></td>
                            <td>{{$actor['action']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-bottom:2px solid #760000; height:10px;"></td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:10px;text-align:center;">
                <img src="{{ URL::asset('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:5px 0 10px; color:#760000;text-align:center;">
                P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new
            </td>
        </tr>
    </table>

</div>
<div class="innerContent">
    
    <header class="pageTitleV3">
        <h1>Import Purchase Requisition</h1>
        <a class="btnAction print bgGreen" id="btnPrint" href="#">Print</a>
    </header>
        
    <div class="inputAreaWrapper">
        <div class="custRow reqCodeDateHolder">
            <div class="custCol-6">
                    <label>Date : <span><?php echo date('d-m-Y',  strtotime($requisitiondata->created_at)); ?></span></label>
            </div>
            <div class="custCol-6 alignRight">
                <label>Requisition Code : <span>{{$requisitiondata->requisition_code}}</span></label>
            </div>
        </div>
        <div class="custRow ">
            <div class="custCol-4 ">
                <div class="inputView">
                    <span>Requisition Title :</span><strong>{{$requisitiondata->title}}</strong>
                </div>
                <div class="inputView">
                    <span>Supplier :</span><strong>{{$requisitiondata->first_name}} {{$requisitiondata->alias_name}}</strong>
                </div>

            </div>
            <div class="custCol-8 suplierView">
                <div class="tabWrapper">
                    <ul>
                        <li rel="basic">Basic</li>
                        <li rel="budget">Budget</li>
                        <li rel="bank">Bank</li>
                    </ul>
                    <div class="tabDtls">
                        <div class="btnTab">Basic</div>
                        <div id="basic" class="tabContent">
                            <div class="tbleListWrapper ">
                                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                    <tbody class="tblbasic">
                                            <?php $strRegistred = "Registered";
                                            if ($supplierdata->registration_type == 0) {
                                            $strRegistred = "Not Registered";
                                            }?>
                                        <tr><td>{{$supplierdata->code}}</td><td>{{$supplierdata->first_name}} {{$supplierdata->alias_name}}</td><td>{{$strRegistred}}</td></tr>
                                        <tr><td>{{$supplierdata->nationality}}</td><td>{{$supplierdata->mobile_number}}</td><td>{{$supplierdata->email}}</td></tr>
                                        <tr><td>Contact Info :</td><td>{{$supplierdata->contact_number}}</td><td>{{$supplierdata->contact_email}}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="btnTab">Budget</div>
                        <div id="budget" class="tabContent">
                            <div class="tbleListWrapper ">
                                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                    <tbody class="tblbudget">
                                            <?php if(count($budgetdata)>0){?>
                                            <tr><td>Total : {{$budgetdata->format_initial}}</td></tr>
                                            <tr><td>Used : {{$budgetdata->format_used}}</td></tr>
                                            <tr><td>Pending : {{$budgetdata->format_balance}}</td></tr>
                                            <?php }else{?>
                                            <tr><td>No Records Found</td></tr>
                                            <?php }?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="btnTab">Bank</div>
                        <div id="bank" class="tabContent">
                            <div class="tbleListWrapper ">
                                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                    <tbody class="tblbank">

                                        <tr><td>Swift Code : {{$supplierdata->bank_swift_code}}</td><td> Name : {{$supplierdata->bank_branch_name}}</td></tr>
                                        <tr><td>Beneficiary : {{$supplierdata->bank_beneficiary_name}}</td><td>Ac No : {{$supplierdata->bank_account_number}}</td></tr>
                                        <tr><td>Country : {{$supplierdata->bankcountry}}</td><td>Credit Limit : {{$supplierdata->creditlimitformated}}</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<input type="hidden" name="createddate" id="createddate" value="{{$requisitiondata->created_at}}">
        
        <div class="tbleListWrapper ">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                <thead class="headingHolder">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>RFQ</th>
                        <th class="amountAlign">Quantity</th>
                        <th>Unit</th>
                        <th class="amountAlign">Price/Unit</th>
                        <th class="amountAlign">Total</th>
                        <th class="tbleActionSet"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $totalprice=0; ?>
                    @foreach ($requisition_items as $item)
                    <?php $totalprice+=$item->total_price; ?>
                    <tr>
                        <td>{{$item->product_code}}</td>
                        <td>{{$item->product_name}}</td>
                        @if($item->rfq_code)
                        <td><a href="{{ URL::to('requisitions/rfq/view', ['id' => Crypt::encrypt($item->rfq_id)]) }}" target="_blank">{{$item->rfq_code}}</a></td>
                        @else
                        <td></td>
                        @endif
                        <td class="amountAlign">{{$item->quantity}}</td>
                        <td>{{$item->unitname}}</td>
                        <td class="amountAlign"><?php echo Customhelper::numberformatter($item->unit_price); ?></td>
                        <td class="amountAlign"><?php echo Customhelper::numberformatter($item->total_price); ?></td>
                        <td class="tbleActionSet">
                             <div class="iconInfoWrapper">

                                <a href="javascript:budgetInfo({{$item->requisition_item_id}})" class="tbleInfo btnTooltip"  title="information"></a>
                                <div class="tooltipInfo">
                                     <a href="javascript:void(0);"class="infoClose" ></a>
                                    <strong>Budget Details</strong>
                                    <span>Intial Budget Amount:<strong class="initialBgt_{{$item->requisition_item_id}}"></strong> </span>
                                    <span>Balance Budget Amount:<strong class="balanceBgt_{{$item->requisition_item_id}}"></strong> </span>
                                    <span>Stock in Branch:<strong class="branchStk_{{$item->requisition_item_id}}"></strong> </span>
                                    <span>Stock in Warehouse:<strong class="warehouseStk_{{$item->requisition_item_id}}"></strong> </span>
                                    <span>Budget Quantity:<strong class="quantityBgt_{{$item->requisition_item_id}}"></strong> </span>
                                    <span>Remaining Quantity:<strong class="remQuantity_{{$item->requisition_item_id}}"></strong> </span>
                               
                                
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr><td></td><td></td><td></td><td></td><td></td><td class="amountAlign"><strong>Sub Total</strong></td><td class="amountAlign"><strong><?php echo Customhelper::numberformatter($totalprice); ?></strong></td><td></td></tr>
                    <tr><td></td><td></td><td></td><td></td><td></td><td class="amountAlign"><strong>Vat Amount (5%)</strong></td><td class="amountAlign"><strong><?php echo Customhelper::numberformatter($requisitiondata->total_vat); ?></strong></td><td></td></tr>
                    <tr><td></td><td></td><td></td><td></td><td></td><td class="amountAlign"><span><strong>Total Amount</strong></span></td><td class="amountAlign"><span><strong><?php echo Customhelper::numberformatter($totalprice+$requisitiondata->total_vat); ?></strong></span></td><td></td></tr>
                </tbody>
            </table>
        </div>
        
        <div class="custRow clsReqItemSeparator">
            <div class="custCol-3">
                <div class="inputView">
                    <span>Mode Of Payment :</span><strong>{{$requisitiondata->payment_mode}}</strong>
                </div>
            </div>
            <div class="custCol-3">
                <div class="inputView">
                    <span>Credit Days :</span><strong>{{$requisitiondata->creditdays}}</strong>
                </div>
            </div>
           <div class="custCol-3">
                <div class="inputView">
                    <span>Delivery Date :</span><strong><?php if($requisitiondata->delivery_date){ echo date("d-m-Y",  strtotime($requisitiondata->delivery_date));}?></strong>
                </div>
            </div>
             <div class="custCol-6">
                <div class="inputView">
                    <span>Delivery Place :</span><strong>{{$requisitiondata->delivery_place}}</strong>
                </div>
            </div>

        </div>

        <div class="custRow ">
           
            <div class="custCol-6">
                <div class="inputView">
                    <span>Terms of Payment :</span><strong>{{$requisitiondata->payment_terms}}</strong>
                </div>
            </div>
            
            <div class="custCol-6">
                <div class="inputView">
                    <span>Description :</span><strong>{{$requisitiondata->description}}</strong>
                </div>
            </div>

            </div>
       
        <div class="custRow clsRqCreatedBy">
            <div class="custCol-6">
                <label>Created By : <span>{{$requisitiondata->createdby}}</span></label>
            </div>
        </div>
        <div class="approverDetailsWrapper">
            <div class="tbleListWrapper">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="headingHolder ">
                        <tr>
                            <th>Action Taker</th>
                            <th>Date</th>
                            <th class="tbleComments">Comments</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($action_takers as $actor)
                        <?php if($actor->action=="Rejected"){$class="bgRed";}else{$class="bgGreen";}?>
                        <tr class="<?php echo $class;?>">
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo date('d-m-Y',  strtotime($actor->created_at));?></td>
                            <td>{{$actor->comments}}</td>
                            <td>{{$actor->action}}</td>
                            
                        </tr>
                        @endforeach
                        
                        @foreach ($next_action_takers_list as $actor)
                        <tr class="bgOrange">
                            <td>{{$actor['name']}}</td>
                            <td></td>
                            <td></td>
                            <td>{{$actor['action']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div> 
        <?php if(count($documents)>0){?>
            
            <div class="documentWrapper">
                <div class="tbleListWrapper">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="headingHolder ">
                            <tr>
                                <th>Date</th>
                                <th>Uploaded By</th>
                                <th>View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($documents as $document)
                            <tr>
                                <td><?php echo date('d-m-Y',  strtotime($document->created_at));?></td>
                                <td>{{$document->createdby}}</td>
                                <td >
                                    <a class="viewreqdocument btnViewModal bgDarkGreen" href="{{$document->doc_url}}">View</a>
                                </td>

                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div> 
        <?php } ?>
    </div>
        
    <div class="commonModalHolder">
        <div class="modalContent printModal">
            <a href="javascript:void(0);" class="btnAction print bgGreen btnImgPrint" style="display: none;">Print</a>
            <a href="javascript:void(0)" class="btnModalClose">Close(X)</a>
            <div id="printHolder">
                <iframe id="frame" style="width:100%;height:100%;"></iframe>
            </div>
        </div>
    </div>
</div>
<script>
    function budgetInfo(index){  
        $.ajax({
            type: 'POST',
            url: '../../getinventorydataQuarter',
            data: '&productid=' + index + '&createddate=' + $('#createddate').val(),
            async:false,
            success: function (return_data) {
                 console.log(return_data);
                if (return_data != -1) {
                    var inventorydata = return_data.inventorydata;
                    var altunits = return_data.altunits;
                    var budgetDetails=return_data.budgetDetails; 
                    var usedBudget=return_data.usedBudget; 
                    
                    if(isNaN(budgetDetails.quantity) || budgetDetails.quantity==null){
                         budgetDetails.quantity=0;
                          }
                         if(isNaN(budgetDetails.usedQuantity) || budgetDetails.usedQuantity==null){
                           budgetDetails.usedQuantity=0;
                          }
                    
                    $('.initialBgt_'+index).text(budgetDetails.format_initial);
                    $('.balanceBgt_'+index).text(budgetDetails.format_balance);
                    $('.branchStk_'+index).text(return_data.branchStock);
                    $('.warehouseStk_'+index).text(return_data.warehosueStock);
                    $('.quantityBgt_'+index).text(budgetDetails.quantity);
                    
                    
                      var remQuantity=parseFloat(budgetDetails.quantity)-parseFloat(budgetDetails.usedQuantity);
                         if(isNaN(remQuantity)){
                         remQuantity=0;
                        }
                     $('.remQuantity_'+index).text(remQuantity);
                }
            }
        });
    }
</script>
<script>
    $('.btnModalClose').on('click', function () {
        $('#frame').attr("src", "");
        $('.commonModalHolder').hide()
    });
    
    $('body').on('click', '.btnImgPrint', function () {
        win = window.open('', 'Print', 'width=720, height=1018');
        win.document.write($('#printHolder').html());
        win.document.close();
        win.print();
        win.close();
    });

</script>
@endsection

