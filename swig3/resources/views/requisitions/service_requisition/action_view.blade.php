@extends('layouts.main')
@section('content')
<?php
$requisitiondata = $pageData['requisitiondata'];
$suppliers = $pageData['suppliers'];
$budgetdata = $pageData['budgetdata'];
$supplierdata = $pageData['supplierdata'];
$action_takers = $pageData['action_takers'];
$next_action_takers_list = $pageData['next_action_takers_list'];
?>


    <div class = "innerContent">
        <a class="btnBack" href="{{ URL::to('requisitions/inbox')}}">Back</a>
        <header class = "pageTitleV3">
            <h1>Service Requisitions</h1>
        </header>
        <div class = " inputAreaWrapper">
            
              <div class="statusMessage">
		<span class="errorMsg" ></span>
            </div>
            
            <form id="frmaddrequisition" method="post" enctype="multipart/form-data">
                <div class = "custRow reqCodeDateHolder">
                    <div class = "custCol-6">
                        <label>Requisition Date : <span><?php echo date('d-m-Y', strtotime($requisitiondata->created_at)); ?></span></label>

                    </div>
                    <div class = "custCol-6 alignRight">
                        <label>Requisition Code : <span>{{$requisitiondata->requisition_code}}</span></label>
                    </div>
                </div>
                <div class = "custRow ">
                    <div class = "custCol-4">
                        <div class="inputHolder">
                            <label>Requisition Title</label>
                            <input type="text" id="title" name="title" value="{{$requisitiondata->title}}" autocomplete="off" placeholder="Enter Title" disabled maxlength="250">
                            <input type="hidden" value="{{$requisitiondata->requisition_code}}" id="requisition_code" name="requisition_code">
                            <input type="hidden" value="{{$requisitiondata->id}}" id="requisition_id" name="requisition_id">
                            <span class="commonError"></span>
                        </div>
                        <div class="inputHolder">
                            <label>Select Supplier</label>
                            <select id="supplier_id" name="supplier_id" class="supplier_id" disabled>
                                <option value=''>Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" <?php
                                if ($supplier->id == $requisitiondata->party_id) {
                                    echo "selected";
                                }
                                ?> >{{ $supplier->code}}:{{$supplier->first_name}}</option>
                                @endforeach
                            </select>
                            <span class="commonError"></span>
                        </div>
                        <div class="inputHolder  ">
                            <label>Amount </label>
                            <input type="text" placeholder="Enter Amount" id="amount" name="amount" class="numberwithdot" maxlength="15" value="">
                            <input type="hidden" id="hiddenAmount" value="{{$requisitiondata->total_price}}">
                            <span class="commonError"></span>
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
                                                <?php
                                                $strRegistred = "Registered";
                                                if ($supplierdata->registration_type == 0) {
                                                    $strRegistred = "Not Registered";
                                                }
                                                ?>
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
                                                <?php if (count($budgetdata) > 0) { ?>
                                                    <tr><td>Total : {{$budgetdata->format_initial}}</td></tr>
                                                    <tr><td>Used : {{$budgetdata->format_used}}</td></tr>
                                                    <tr><td>Pending : {{$budgetdata->format_balance}}</td></tr>
                                                <?php } else { ?>
                                                    <tr><td>No Records Found</td></tr>
                                                <?php } ?>
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

                </form>
                
                <form id="frmdocs" name="frmdocs" method="post" enctype="multipart/form-data">
                    <div class="custRow" >
                        <div class="custCol-4 ">
                            <div class="inputHolder">
                                <label>Upload Document</label>
                                <input type="file" name="req_doc" class="reqdocument" id="req_doc" accept="image/*,.doc,.docx,.txt,.pdf,application/msword">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                </form>
            
                <div class = "custRow ">
                    <div class = "custCol-6">
                        <div class="inputHolder  ">
                            <label>Description</label>
                            <textarea id="description" placeholder="Enter Description">{{$requisitiondata->description}}</textarea>
                            <span class="commonError"></span>
                        </div>
                    </div>

                </div>
                <div class="custRow">
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
                                    <th>Comments</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                               
                                @foreach ($action_takers as $actor)
                                <?php
                                if ($actor->action == "Rejected") {
                                    $class = "bgRed";
                                } else {
                                    $class = "bgGreen";
                                }
                                
                                ?>
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

            
            <?php if(count($documents)>0){ ?>
            
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
        
            <?php if($isactiontaken=="Yes"){?>
            
             <div class="custRow">
                <div class="custCol-6">
                    
                     <form id="frmreject">
                            <div class="inputHolder  ">
                                <label>Comments</label>
                                <textarea placeholder="Enter Reason" id="reject_reason" name="reject_reason"></textarea>
                                <span class="commonError"></span>
                            </div>
                        </form>
                    
                </div>
            </div>
            
        
              <div class="bottomBtnsHolder">
                <input type="button" id="btnapprove" class="btnIcon bgGreen" value="Approve">
                <input type="button" id="btnreject" class="btnIcon bgRed" value="Reject">
                 
                <div class="customClear "></div>
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
<div class="commonLoaderV1"></div>

<input type="hidden" name="pending" id="pending" value="{{$budgetdata->pending}}" >
<script>
    $('.statusMessage').hide();
    $('#btnapprove').removeAttr('disabled');
    $('#btnreject').removeAttr('disabled');
    $(function () {
        $('#amount').val(amountformat($('#hiddenAmount').val()));
        
        var v = jQuery("#frmaddrequisition").validate({
            rules: {
                title: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                supplier_id: {
                    required: true,
                },
                amount: {
                    required: true,
                },
            },
            messages: {
                title: "Enter Title",
                supplier_id: "Select Supplier",
                amount: "Enter Amount",
            },
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
        });
        
        $("#frmreject").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                reject_reason: {required: true},
            },
            messages: {
                reject_reason: "Enter Reason",
            }
        });
   
  $('#amount').bind('keyup', function () {
              $('.statusMessage').hide();
    });
      
        $("#btnapprove").click(function () {
            
            $("#reject_reason").removeClass("valErrorV1");
            $("#reject_reason-error").html('');
            
            if (!$("#frmaddrequisition").valid()) {
                return;
            }
            
            
              var pending=  $("#pending").val();
               var requisitionTotalPrice=  $("#amount").val();
            
                 if(isNaN(pending) || pending==""){
                   pending=0;
                 }
                 
            if(parseFloat($("#amount").val())==0){
                $('.errorMsg').text("Amount should not be zero"); 
                $('.statusMessage').show();
                return;
            }else{
                $('.statusMessage').hide();
            }
            
            if(parseFloat(requisitionTotalPrice)>parseFloat(pending)){
                
                  $('.errorMsg').text("Total price exceeds your user budget"); 
                  $('.statusMessage').show();
                return;
            }
            
            
            var blnConfirm = confirm("Are you sure to approve requisition ?");
            if(!blnConfirm){
               return; 
            }
            
            
            var arraData = {
                requisition_code: $('#requisition_code').val(),
                requisition_id: $('#requisition_id').val(),
                title: $('#title').val(),
                description: $('#description').val(),
                supplier_id: $("#supplier_id").val(),
                price: amountformat($("#amount").val()),
                comments: $('#reject_reason').val(),
            }

            var arrData = JSON.stringify(arraData);
            var documents = new FormData($('#frmdocs')[0]);
            documents.append('arrData', arrData);
            
            $('.commonLoaderV1').show();
            $('#btnapprove').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../approve_requisition',
                data:documents, 
                contentType: false,  
                processData: false,
                success: function (return_data) {
                    $('.commonLoaderV1').hide();
                    $('#btnapprove').removeAttr('disabled');
                    window.location.href = '{{url("requisitions/inbox")}}';
                }
            });
            
            $('#btnapprove').removeAttr('disabled');
           
        });
        
        $("#btnreject").click(function () {
            
            if(!$("#frmreject").valid()){
                return;
            }
            
            var blnConfirm = confirm("Are you sure to reject requisition ?");
            if(!blnConfirm){
               return; 
            }
            
            
            
            
            $('.commonLoaderV1').show();
            $('#btnreject').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: '../reject_requisition',
                data: '&requisition_id=' + $('#requisition_id').val() +
                        '&requisition_code=' + $('#requisition_code').val()+
                        '&reject_reason=' + $('#reject_reason').val(),
                success: function (return_data) {
                    $('.commonLoaderV1').hide();
                    $('#btnreject').removeAttr('disabled');
                    window.location.href = '{{url("requisitions/inbox")}}';
                }
            });
            
            $('#btnreject').removeAttr('disabled');
          
            
        });
        
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
        
    });
</script>
@endsection