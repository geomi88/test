@extends('layouts.main')
@section('content')
<div class="innerContent">
    	
    <form id="frmphysicalstock" method="post">
   <header class="pageTitle">
        <h1>Warehouse <span>Physical Stock</span></h1>
    </header>	
    <?php if($empstatus != 1) {?>
  <div class="custRow">

        
   
 <div class="custCol-4 inputHolder bgSelect">
                    <label> Warehouse</label>
                    <?php  if(count($warehouse)>1){?>
                    <select  name="warehouseId" id="warehouseId" onchange="warehouseStock();">
                        <option selected value=''>Choose Warehouse </option>
                        @foreach ($warehouse as $warehouseDetails)
                        <option value='{{ $warehouseDetails->id }}'>{{ $warehouseDetails->name}}</option>
                        @endforeach
                    </select>
                    <?php } else{
                        ?>
                       
                       <b><?php echo $warehouse[0]->name;?></b>
                   <?php }?>
                    <span class="commonError"></span>
                </div>
<input type="hidden" id="savedStockId" value="{{$savedStockId}}">
      
        <div class="custCol-4">
           
        </div>
         <div class="custCol-4">
            <input type="hidden" name="job_shift" value="">
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
                  <?php  if(count($warehouse)>1){?>
                 <tr><td>No Records Found</td></tr> 
                   <?php } else{?> 
                    @include('warehouse/warehouse_physical_stock/search_results')
                   <?php }?>
                </tbody>

            </table>
            </div>
            <div class="commonLoaderV1"></div>
            
        </div>

    </div>
        <?php if($empstatus != 1) {?>
            <div class="custRow">
                <div class="custCol-4 btnSub">
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
            url: '../warehouse/warehouse_physical_stock/store',
            data: '&arraData=' + arraData + '&branch_id=' + branch_id + '&shift_id=' + shift_id + '&savedStockId=' + $("#savedStockId").val(),
            success: function (return_data) {
                window.location.href = '{{url("warehouse/warehouse_physical_stock")}}';
            },
            error: function (return_data) {
                console.log(return_data);
              //  window.location.href = '{{url("warehouse")}}';
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

function warehouseStock(){
    
        var warehouseId = $("#warehouseId").val();
        alert(warehouseId);
        $.ajax({
            type: 'POST',
            url: '../warehouse/get_warehouse_physical_stock',
            data: {warehouseId: warehouseId},
            success: function (return_data) {
                $('.tblProducts').html(return_data); 
             var subId=$('#subId').val();
             if(subId!=""){
                 $('#btnSaveStock').val("Update")
                 $('#savedStockId').val(subId)
                 
                 
             }
               // window.location.href = '{{url("warehouse/warehouse_physical_stock")}}';
            },
            error: function (return_data) {
                console.log(return_data);
              //  window.location.href = '{{url("warehouse")}}';
            }
        });
}
</script>
@endsection