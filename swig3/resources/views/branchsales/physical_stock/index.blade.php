@extends('layouts.main')
@section('content')
<div class="innerContent">
    	
    <form id="frmphysicalstock" method="post">
   <header class="pageTitle">
        <h1>Physical <span>Stock</span></h1>
    </header>	
    <?php if($empstatus != 1) {?>
    <div class="custRow">
        <div class="custCol-4">
            <input type="hidden" id="branch_id" value="{{$branch_details->branch_id}}">
            <input type="hidden" id="savedStockId" value="{{$savedStockId}}">
            <div class="inputHolder bgSelect">
                <label>Branch Name</label>
                <b>{{$branch_details->branch_name}}</b>
                <span class="commonError"></span>
            </div>
        </div>
        <div class="custCol-4">
            <input type="hidden" id="job_shift" value="{{$branch_details->job_shift_id}}">
            <div class="inputHolder bgSelect">
                <label>Shift Name</label>
                <b>{{$branch_details->shift_name}}</b>
                <span class="commonError"></span>
            </div>
        </div>
         <div class="custCol-4">
            <input type="hidden" name="job_shift" value="{{$branch_details->job_shift_id}}">
            <div class="inputHolder bgSelect">
                <label>Today's Date</label>
                <b><?php echo date('d-m-Y');?></b>
                <span class="commonError"></span>
            </div>
        </div>

    </div>
    <?php }?>
        
    <div class="listHolderType1">

        <div class="listerType1 reportLister"> 

            <div id="tblProducts">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        
                        <td>
                            Sl.No.
                        </td>
                        <td>
                            Product Code
                            
                        </td>
                        <td>
                            Name
                        </td>
                        <td>
                            Quantity
                        </td>
                        <td>
                            Maximum Branch Stock
                        </td>
                         
                    </tr>
                </thead>
              
                <tbody class="tblProducts" id='tblProducts'>
                   <?php  $n = 0;?> 
                        @foreach ($products as $product)
                        <?php
                        if(key_exists($product->id, $stockSavedData)){
                            $txtvalue = $stockSavedData[$product->id];
                        }else if(key_exists($product->id, $stockPreviousData)){
                            $txtvalue = $stockPreviousData[$product->id];
                        }else{
                            $txtvalue = 0;
                        }
                        
                        $n++;
                        ?>
                        <tr>
                            <td>{{ $n }}</td>
                            <td>{{$product->product_code}}</td>
                            <td>{{$product->name}}</td>
                            <td><input type="text" id="{{$product->id}}" class="clsproductqty number" usr-attr="<?php echo $txtvalue;?>" maxlength="15" usr-max="{{$product->max_branch_stock}}" usr-pcode="{{$product->product_code}}" style="width: 170px;" value="<?php echo $txtvalue;?>"></td>
                            <td>{{$product->max_branch_stock}}</td>
                        </tr>
                        @endforeach
                       
                   
                </tbody>

            </table>
            </div>
            <div class="commonLoaderV1"></div>
            
        </div>

    </div>
        <?php if($empstatus != 1) {?>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="button" value="<?php if($savedStockId != '') echo 'Update'; else echo 'Save'; ?>" id="btnSaveStock" class="commonBtn bgGreen addBtn" >
                </div>
            </div>
        <?php } ?>
    </form>
</div>
<script>
var arrProductsQty = [];
$(document).ready(function ()
{
    $('#btnSaveStock').on('click', function () {
        var updateFlg = 0; 
        var strProducts = '';
        $(".clsproductqty").each(function() {
            var txtid = $(this).attr("id");
            if($(this).attr("usr-attr") != $("#"+txtid).val()){
                updateFlg=1;
            }
            
            var intMaxQty = $(this).attr("usr-max");
            
            if(parseFloat($("#"+txtid).val())>parseFloat(intMaxQty)){
                strProducts += $(this).attr("usr-pcode")+",";
                $("#"+txtid).addClass("errorBorder");
            }else{
                $("#"+txtid).removeClass("errorBorder");
            }
            
            var arraData = {
                product_id: txtid,
                quantity: $("#"+txtid).val(),
            }
            arrProductsQty.push(arraData);
        });
        
        if(updateFlg==0 && $("#savedStockId").val()!=''){
          alert("No change in data");
          return;
        }
        
        if(strProducts != ''){
            arrProductsQty = [];
            strProducts = strProducts.slice(0, -1);
            alert("Quantity shold not exceed maximum branch stock, Please check products : "+strProducts)
            return;
        }
        
        var arraData = encodeURIComponent(JSON.stringify(arrProductsQty));
        var branch_id = $("#branch_id").val();
        var shift_id = $("#job_shift").val();
        $.ajax({
            type: 'POST',
            url: '../branchsales/physicalstock/store',
            data: '&arraData=' + arraData + '&branch_id=' + branch_id + '&shift_id=' + shift_id + '&savedStockId=' + $("#savedStockId").val(),
            success: function (return_data) {
                window.location.href = '{{url("branchsales/physicalstock")}}';
            },
            error: function (return_data) {
                window.location.href = '{{url("branchsales")}}';
            }
        });

    });
    
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
});
</script>
@endsection