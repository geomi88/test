@extends('layouts.main')
@section('content')

<div class="contentArea">
    <a class="btnAction action bgBlue" href="{{ URL::to('warehouse/received_inventory_request')}}">Back</a>
<div class="innerContent">

    <h4 class="blockHeadingV1">Item Request Details</h4>

    <div class="reportV1">
        <ul class="custRow">
            <li class="custCol-3">
                <b>Request Id</b>
                {{$item_request->request_id}}
            </li>
            <li class="custCol-3">
                <b>Branch</b>
                {{$item_request->branch_name}}
            </li>
            <li class="custCol-3">
                <b>Warehouse</b>
                {{$item_request->warehouse}}
            </li>
            <li class="custCol-3">
                <b>Request Status</b>
                <span><?php echo str_replace('_',' ',$item_request->request_status);?></span>
            </li>
        </ul>

    </div>
    <div class="selected_pos">
        <div class="listHolderType1">
            <div class="listerType1"> 
                <table style="width: 100%;" cellspacing="0" cellpadding="0">
                    <thead class="listHeaderTop">
                        <tr class="headingHolder">
                            <td>Product Code</td>
                            <td>Name</td>
                            <td>Requested Qty.</td>
                            <td>Approval Qty.</td>
                            <?php if($item_request->request_status == 'Completed') { ?>
                                <td>Received Qty.</td>
                            <?php } ?>
                            <td>Unit</td>
                            <td>Item Status</td>
                            <?php if($item_request->request_status == 'In_Progress' || $item_request->request_status == 'Holded') { ?>
                                <td>Cancel</td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($item_request_details as $items)
                        <tr>
                            <td>{{$items->product_code}}</td>
                            <td>{{$items->product_name}}</td>
                            <td>{{$items->request_quantity}}</td>
                            <?php if($item_request->request_status == 'Completed') { ?>
                                <td>{{$items->approved_quantity}}</td>
                                <td>{{$items->received_quantity}}</td>
                            <?php } else if($item_request->request_status == 'In_Transit' || $item_request->request_status == 'Rejected'){?>
                                <td>{{$items->approved_quantity}}</td>
                            <?php } else {?>
                                <td><input type="text" style="width:120px;" usr-req-qty="{{$items->request_quantity}}" usr-pcode="{{$items->product_code}}" class="approvalqty number" <?php if($items->item_status == "Cancelled"){ echo "disabled";}?> maxlength="15" id="{{$items->itemdet_id}}" value="{{$items->approved_quantity}}"></td>
                            <?php }?>
                            <td>{{$items->units}}</td>
                            <td>{{$items->item_status}}</td>
                            <?php if($item_request->request_status == 'In_Progress' || $item_request->request_status == 'Holded') { ?>
                                <td><a class="btnAction action bgRed" href="javascript:cancelitem({{$items->itemdet_id}},'{{$items->item_status}}')" >Cancel</a></td>
                            <?php } ?>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                <div class="commonLoaderV1"></div>
            </div>					
        </div>

    </div>

    <div class="custRow">
        <input type="hidden" id="requestid" value="{{$item_request->id}}">
        <?php if($item_request->request_status == "In_Progress" || $item_request->request_status == "Holded") {?>
            <div class="custCol-3">
                <input type="button" value="Approve" id="btnApprove" class="commonBtn bgGreen addBtn" >
            </div>
        <?php } ?>
        <?php if($item_request->request_status == "In_Progress") {?>
            <div class="custCol-3 alignCenter">
                <input type="button" value="Hold" id="btnHold" class="commonBtn bgOrange addBtn" >
            </div>
        <?php } ?>
        <?php if($item_request->request_status == "In_Progress" || $item_request->request_status == "Holded") {?>
            <div class="custCol-3">
                <input type="button" value="Reject" id="btnReject" class="commonBtn bgLightRed addBtn" >
            </div>
        <?php } ?>
    </div>
</div>
    
</div>
<script>
var arrProductsQty = [];
$(document).ready(function ()
{
    
    $('.number').keypress(function(event) {

        if(event.which == 8 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.which == 0) {
             return true;
        } else if((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)){
             event.preventDefault();
        }

        if($(this).val() == parseFloat($(this).val()).toFixed(2))
        {
            event.preventDefault();
        }

        return true;
   });
   
   $('#btnApprove').on('click', function () {
        var blnQtyEmpty = 0;
        var strProducts = '';
        $(".approvalqty").each(function() {
            var txtid = $(this).attr("id");
            var intReqQty = $(this).attr("usr-req-qty");
            if(parseFloat($("#"+txtid).val())>parseFloat(intReqQty)){
                strProducts += $(this).attr("usr-pcode")+",";
                $("#"+txtid).addClass("errorBorder");
            }else{
                $("#"+txtid).removeClass("errorBorder");
            }
            
            if(($("#"+txtid).val()=='' || parseFloat($("#"+txtid).val())==0) && !$("#"+txtid).prop('disabled')){
              blnQtyEmpty = 1;
            }
            
            var arraData = {
                itemdet_id: txtid,
                quantity: $("#"+txtid).val(),
            }
            arrProductsQty.push(arraData);
        });
        
        if(blnQtyEmpty == 1){
            arrProductsQty = [];
            alert("Please Enter Approval Quantity")
            return;
        }
        
        if(strProducts != ''){
            arrProductsQty = [];
            strProducts = strProducts.slice(0, -1);
            alert("Cannot approve more than requested quantity, Please check products : "+strProducts)
            return;
        }
        
        var arraData = encodeURIComponent(JSON.stringify(arrProductsQty));
        var blnConfirm = confirm("Are you sure to approve the request ?");
        if(blnConfirm){
            $.ajax({
                type: 'POST',
                url: '../approverequest',
                data: '&arraData=' + arraData + '&requestid=' + $("#requestid").val(),
                success: function (return_data) {
                     window.location.href = '{{url("warehouse/received_inventory_request")}}';
                },
                error: function (return_data) {
                    window.location.href = '{{url("warehouse/received_inventory_request")}}';
                }

            });
        }
   });
   
   $('#btnHold').on('click', function () {
    var blnConfirm = confirm("Are you sure to hold the request ?");
    if(blnConfirm){
       $.ajax({
            type: 'POST',
            url: '../holdrequest',
            data: '&requestid=' + $("#requestid").val(),
            success: function (return_data) {
                 window.location.href = '{{url("warehouse/received_inventory_request")}}';
            },
            error: function (return_data) {
                window.location.href = '{{url("warehouse/received_inventory_request")}}';
            }

        });
    }
   });
   
   $('#btnReject').on('click', function () {
    var blnConfirm = confirm("Are you sure to reject the request ?");
    if(blnConfirm){
       $.ajax({
            type: 'POST',
            url: '../rejectrequest',
            data: '&requestid=' + $("#requestid").val(),
            async:false,
            success: function (return_data) {
                 window.location.href = '{{url("warehouse/received_inventory_request")}}';
            },
            error: function (return_data) {
                window.location.href = '{{url("warehouse/received_inventory_request")}}';
            }
        });
    }
   });
});

function cancelitem(pid,item_status){
    
    if(item_status == 'Cancelled'){
        alert("Item Already Cancelled");
        return;
    }
    
    var blnConfirm = confirm("Are you sure to cancel this item ?");
    if(blnConfirm){
        $.ajax({
            type: 'POST',
            url: '../cancelitem',
            data: '&requestid=' + $("#requestid").val() + '&pid=' + pid,
            async:false,
            success: function (return_data) {
                 window.location.reload();
            }
        });
    }
    
}
</script>
@endsection