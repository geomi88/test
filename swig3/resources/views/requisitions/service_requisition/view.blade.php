@extends('layouts.main')
@section('content')
<?php
$requisitiondata = $pageData['requisitiondata'];
$action_takers = $pageData['action_takers'];
$next_action_takers_list = $pageData['next_action_takers_list'];
$supplierdata = $pageData['supplier'];
$budgetdata = $pageData['budgetdata'];
?>
<script>
    $(document).ready(function(){
       $('#btnPrint').click(function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write( $('.printDiv').html());
            win.document.close();
            win.print();
            win.close();
            return false;
        }); 
    });
</script>
<div style="display: none" class="printDiv">
    <?php
    $strRegistred = "Registered";
    if ($supplierdata->registration_type == 0) {
        $strRegistred = "Not Registered";
    }
    ?>
    <table border="0" cellpadding="5" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica,sans-serif; font-size:12px;color:#000;border:3px solid #760000;">
        <tr >
            <td >
                <span style="color:#760000; font-size:25px;font-weight:bold; display:block;padding-bottom:15px;">Service Requisition</span>
                <p style="padding-bottom:5px;color:#000;margin: 0;"><strong style="padding-right:5px;">Date :</strong><span><?php echo date('d-m-Y',  strtotime($requisitiondata->created_at)); ?></span></p>
                <p style="margin: 0;"><strong style="padding-right:5px;color:#000;">Requisition Code :</strong><span>{{$requisitiondata->requisition_code}}</span></p>
            </td>
            <td style="text-align:right;vertical-align:top;">
                <img src="{{Url('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est." style="width:150px;">
            </td>
        </tr>
        <tr >
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr  style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Requisition Details</th>
                    </tr>
                    <tr >
                        <td width="33.33% " style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Requisition Title :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->title}}</strong>
                                    </td>
                                 </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Amount   :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->total_price}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Supplier  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->first_name}} {{$requisitiondata->alias_name}}</strong>
                                    </td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style=padding-bottom:5px;>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Created By :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->createdby}}</strong>
                                    </td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr >
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style=" font-family:Arial, Helvetica, sans-serif; font-size:12px;border:3px solid #760000;">
                    <tr  style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Supplier</th>
                    </tr>
                    <tr >
                        <td colspan="3">
                            <strong style="display:block;color:#760000;font-size:14px;font-weight:bold;">Basic</strong>
                        </td>
                    </tr>
                    <tr >
                        <td width="33.33%" style="vertical-align:top;border-bottom:1px solid #760000;">
                             <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Code  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->code}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                     <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Cell  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->mobile_number}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Email  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->email}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style=padding-bottom:5px;>
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Name  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->first_name}} {{$supplierdata->alias_name}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Country :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->nationality}}</strong>
                                    </td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                            </table>
                        </td>
                        
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Status  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$strRegistred}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Contact Info  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->contact_number}}</strong>
                                    </td>
                                </tr>
                                <tr><td>&nbsp;</td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr >
                        <td colspan="3">
                            <strong style="display:block;color:#760000;font-size:14px;font-weight:bold;">Budget</strong>
                        </td>
                    </tr>
                    <tr >
                        <?php if(count($budgetdata)>0){?>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                           <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Total :</span>
                                        <strong style="display:inline-block;color:#000;">{{$budgetdata->format_initial}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Used :</span>
                                        <strong style="display:inline-block;color:#000;">{{$budgetdata->format_used}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Pending :</span>
                                        <strong style="display:inline-block;color:#000;">{{$budgetdata->format_balance}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <?php } else { ?>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">No Records Found</td>
                    <?php } ?>
                    </tr>
                    <tr >
                        <td colspan="3">
                            <strong style="display:block;color:#760000;font-size:14px;font-weight:bold;">Bank</strong>
                        </td>
                    </tr>
                    <tr >
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                             <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Swift Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->bank_swift_code}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Ac No : </span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->bank_account_number}}</strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style=" vertical-align:top;border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Name : </span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->bank_branch_name}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Country : </span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->bankcountry}}</strong>
                                    </td>
                                </tr>
                            </table>        
                        </td>
                        <td width="33.33%" style="vertical-align:top; border-bottom:1px solid #760000;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Beneficiary :</span>
                                        <strong style="display:inline-block;color:#000;">{{$supplierdata->bank_beneficiary_name}}</strong>
                                    </td>
                                </tr>
                                <tr style="padding-bottom:5px;margin:0;">
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
        <tr >
            <td colspan="3" style="vertical-align:top;">
                <label style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;display:block;">Description</label>
                <article style="width:100%; min-height:40px;font-size:12px;border:3px solid #760000;box-sizing: border-box;padding:3px;">
                    {{$requisitiondata->description}}
                </article>
            </td>
        </tr>
        <tr >
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr  style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Requested By</th>
                    </tr>
                    <tr >
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->empcode}}</strong>
                                    </td>
                                </tr>
                             </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->createdby}}</strong>
                                    </td>
                                </tr>
                             </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                             <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Job Position:</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->jobposition}}</strong>
                                    </td>
                                </tr>
                             </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr >
            <td colspan="3" style="color:#760000;font-size:14px;font-weight:bold;">
                Approved By
            </td>
        </tr>
        <tr >
            <td colspan="3">
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; -webkit-appearance: none;-moz-appearance:none;appearance:none;">
                    <thead style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr >
                            <th>Action Taker</th>
                            <th width="80px">Date</th>
                            <th width="350px">Comments </th>
                            <th width="80px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($action_takers as $actor)
                        <?php if ($actor->action == "Rejected") {
                            $class = "bgRed";
                        } else {
                            $class = "bgGreen";
                        } ?>
                        <tr  class="<?php echo $class; ?>">
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo date('d-m-Y', strtotime($actor->created_at)); ?></td>
                            <td>{{$actor->comments}}</td>
                            <td>{{$actor->action}}</td>

                        </tr>
                        @endforeach

                        @foreach ($next_action_takers_list as $actor)
                        <tr  class="bgOrange">
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
        <tr >
            <td colspan="2" style="border-bottom:2px solid #760000; height:10px;"></td>
        </tr>
        <tr >
            <td colspan="2" style="padding-top:10px;text-align:center;">
                <img src="{{Url('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
            </td>
        </tr>
        <tr >
            <td colspan="2" style="padding:5px 0 10px; color:#760000;text-align:center;">
                P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new
            </td>
        </tr>
    </table>
</div>

<div class="innerContent">
    
    <header class="pageTitleV3">
        <h1>Service Requisitions</h1>
        <a class="btnAction print bgGreen" id="btnPrint" href="#">Print</a>
    </header>
    
    <div class=" inputAreaWrapper">
        <div class="custRow reqCodeDateHolder">
            <div class="custCol-6">
                <label>Requisition Date : <span><?php echo date('d-m-Y',  strtotime($requisitiondata->created_at)); ?></span></label>

            </div>
            <div class="custCol-6 alignRight">
                <label>Requisition Code : <span>{{$requisitiondata->requisition_code}}</span></label>  
            </div>
        </div>
        <div class="custRow ">
            <div class="custCol-4">
                <div class="inputView">
                     <span>Requisition Title :</span><strong>{{$requisitiondata->title}}</strong>
                </div>
                <div class="inputView">
                    <span>Supplier :</span><strong>{{$requisitiondata->first_name}} {{$requisitiondata->alias_name}}</strong>
                </div>
                <div class="inputView">
                    <span>Description :</span><strong>{{$requisitiondata->description}}</strong>
                </div>
                <div class="inputView">
                    <span>Amount :</span><strong>{{$requisitiondata->total_price}}</strong>
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
                                            <tr ><td>{{$supplierdata->code}}</td><td>{{$supplierdata->first_name}} {{$supplierdata->alias_name}}</td><td>{{$strRegistred}}</td></tr>
                                            <tr ><td>{{$supplierdata->nationality}}</td><td>{{$supplierdata->mobile_number}}</td><td>{{$supplierdata->email}}</td></tr>
                                            <tr ><td>Contact Info :</td><td>{{$supplierdata->contact_number}}</td><td>{{$supplierdata->contact_email}}</td></tr>
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
                                            <tr ><td>Total : {{$budgetdata->format_initial}}</td></tr>
                                            <tr ><td>Used : {{$budgetdata->format_used}}</td></tr>
                                            <tr ><td>Pending : {{$budgetdata->format_balance}}</td></tr>
                                            <?php }else{?>
                                            <tr ><td>No Records Found</td></tr>
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
                                            
                                            <tr ><td>Swift Code : {{$supplierdata->bank_swift_code}}</td><td> Name : {{$supplierdata->bank_branch_name}}</td></tr>
                                            <tr ><td>Beneficiary : {{$supplierdata->bank_beneficiary_name}}</td><td>Ac No : {{$supplierdata->bank_account_number}}</td></tr>
                                            <tr ><td>Country : {{$supplierdata->bankcountry}}</td><td>Credit Limit : {{$supplierdata->creditlimitformated}}</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="custRow">
                <div class="custCol-6">
                    <label>Created By : <span>{{$requisitiondata->createdby}}</span></label>
                </div>
            </div>
        </div>
        <div class="approverDetailsWrapper">
            <div class="tbleListWrapper">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="headingHolder ">
                        <tr >
                            <th>Action Taker</th>
                            <th>Date</th>
                            <th class="tbleComments">Comments</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($action_takers as $actor)
                        <?php if ($actor->action == "Rejected") {
                            $class = "bgRed";
                        } else {
                            $class = "bgGreen";
                        } ?>
                        <tr  class="<?php echo $class; ?>">
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo date('d-m-Y', strtotime($actor->created_at)); ?></td>
                            <td>{{$actor->comments}}</td>
                            <td>{{$actor->action}}</td>

                        </tr>
                        @endforeach

                        @foreach ($next_action_takers_list as $actor)
                        <tr  class="bgOrange">
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
            <a href="javascript:void(0);" class="btnModalClose">Close(X)</a>
            <div id="printHolder">
                <iframe id="frame" style="width:100%;height:100%;"></iframe>
            </div>
        </div>
    </div>
</div>
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