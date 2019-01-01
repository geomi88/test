@extends('layouts.main')
@section('content')
<?php
$requisitiondata = $pageData['requisitiondata'];
$action_takers = $pageData['action_takers'];
$next_action_takers_list = $pageData['next_action_takers_list'];
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
    <table border="0" cellpadding="5" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;color:#000;border:3px solid #760000;">
        <tr>
            <td  colspan="2">
                <span style="color:#760000; font-size:25px;font-weight:bold; display:block;padding-bottom:15px;">Advance Payment Requisition</span>
                <p style="padding-bottom:5px;color:#000;margin: 0;"><strong style="padding-right:5px;">Date :</strong><span><?php echo date('d-m-Y', strtotime($requisitiondata->created_at)); ?></span></p>
                <p style="margin: 0;"><strong style="padding-right:5px;color:#000;">Requisition Code : </strong><span>{{$requisitiondata->requisition_code}}</span></p>
            </td>
            <td  style="text-align:right;vertical-align:top;">
                <img src="{{Url('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est."  style="width:150px;" >
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
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Requisition Title :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->title}}</strong>
                                    </td>
                                 </tr>
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Created By :</span>
                                        <strong style="display:inline-block;color:#000;"> {{$requisitiondata->createdby}} </strong>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Ledger :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->first_name}} {{$requisitiondata->alias_name}}</strong>
                                    </td>
                                 </tr>
                                 <tr><td>&nbsp;</td></tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Amount  :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->total_price}}</strong>
                                    </td>
                                 </tr>
                                <tr><td>&nbsp;</td></tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
        <tr>
            <td colspan="3"  style="vertical-align:top;">
                <label  style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;display:block;">Description</label> 
                <article  style="width:100%; min-height:40px;font-size:12px;border:3px solid #760000;box-sizing: border-box;padding:3px;">
                    {{$requisitiondata->description}}
                </article>
            </td>

        </tr>
        <tr>
            <td colspan="3">
                <table border="0" cellpadding="3" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; border:3px solid #760000;">
                    <tr style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <th colspan="3">Requested By</th>
                    </tr>
                    <tr>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Code :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->empcode}}</strong>
                                    </td>
                                 </tr>
                            </table>
                            
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
                                <tr style="padding-bottom:5px;margin:0;">
                                    <td>
                                        <span style="padding-right:5px;display:inline-block;color:#000;">Employee Name :</span>
                                        <strong style="display:inline-block;color:#000;">{{$requisitiondata->createdby}}</strong>
                                    </td>
                                 </tr>
                            </table>
                        </td>
                        <td width="33.33%" style="vertical-align:top;">
                            <table border="0" cellpadding="0" width="100%" height="100%" cellspacing="0"  style="font-family:Arial, Helvetica, sans-serif; font-size:12px;">
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
        <tr>
            <td colspan="3" style="color:#760000;font-size:14px;font-weight:bold;">
                Approved By
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px; -webkit-appearance: none;-moz-appearance:none;appearance:none;">
                    <thead  style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                        <tr>
                            <th>Action Taker</th>
                            <th>Date</th>
                            <th width="350px">Comments  </th>
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
                        <tr class="<?php echo $class; ?>">
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo date('d-m-Y', strtotime($actor->created_at)); ?></td>
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
            </td>
        </tr>
        <tr>
            <td colspan="3" style="border-bottom:2px solid #760000; height:10px;"></td>
        </tr>
        <tr>
            <td colspan="3" style="padding-top:10px; text-align: center;">
                <img src="{{Url('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
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

    <header class="pageTitleV3">
        <h1>Advance Payment Requisition</h1>
        <a class="btnAction print bgGreen" id="btnPrint" href="#">Print</a>
    </header>
    
    <div class=" inputAreaWrapper">
        <div class="custRow reqCodeDateHolder">
            <div class="custCol-6">
                <label>Requisition Date : <span><?php echo date('d-m-Y', strtotime($requisitiondata->created_at)); ?></span></label>

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
                    <span>Description :</span><strong>{{$requisitiondata->description}}</strong>
                </div>
                <div class="inputView">
                    <span>Ledger :</span><strong>{{$requisitiondata->first_name}} {{$requisitiondata->alias_name}}</strong>
                </div>
                <div class="inputView">
                    <span>Amount :</span><strong>{{$requisitiondata->total_price}}</strong>
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
                        <tr>
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
                        <tr class="<?php echo $class; ?>">
                            <td>{{$actor->action_taker}}</td>
                            <td><?php echo date('d-m-Y', strtotime($actor->created_at)); ?></td>
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