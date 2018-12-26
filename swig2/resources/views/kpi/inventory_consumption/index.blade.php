@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Inventory <span>Consumption</span></h1>
    </header>	
<form id="frmComsumption">
   <div class="fieldGroup" id="fieldSet1">
       <div class="custRow">
            <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Branch</label>
                        <select class="commoSelect" name="cmbbranch" id="cmbbranch">
                            <option value=''>Select Branch</option>
                            @foreach ($branches as $branch)
                            <option value='{{ $branch->branch_id }}'>{{ $branch->branch_code}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
            </div>
           <div class="custCol-4">
                <div class="inputHolder bgSelect">
                    <label>Select Product</label>
                    <select name="productid" id="productid">
                        <option value=''>Select Product</option>
                        @foreach ($products as $product) 
                        <option value='{{ $product->id }}'>{{ $product->product_code}} : {{ $product->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            </div>
            <div class="custRow collectionDate">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>From</label>
                        <input type="text" name="from_date"  id="from_date" readonly>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>To</label>
                        <input type="text" name="to_date"  id="to_date" readonly>
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-3 alignRight">
                    <input value="Search" id="btnSearch" class="commonBtn bgGreen addNext" type="button">
                </div>

            </div>


        </div>
        
    <div class="listHolderType1">

        <div class="listerType1 reportLister"> 

            <div id="tblProducts">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        
                        <td>
                            Product Code
                        </td>
                        <td>
                            Name
                        </td>
                        <td>
                            Consumption Rate
                        </td>
                        <td>
                            Consumption Price
                        </td>
                         
                    </tr>
                </thead>
              
                <tbody class="tblProducts" id='tblProductsBody'>
                   
                </tbody>

            </table>
            </div>
            <div class="commonLoaderV1"></div>
            
        </div>

    </div>

    </form>
</div>
<script>

$(document).ready(function ()
{
     var validatesearch = $("#frmComsumption").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                cmbbranch: {required: true},
                productid: {required: true},
                from_date: {required: true},
                to_date: {required: true},
            },
            messages: {
                cmbbranch: "Select Branch",
                productid: "Select Product",
                from_date: "Select From Date",
                to_date: "Select To Date",
            }
        });
        
    $('#btnSearch').on('click', function () {
        
        if(!$("#frmComsumption").valid()){
            return;
        }
      
        var branchid = $("#cmbbranch").val();
        var productid = $("#productid").val();
        var from_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        
        $.ajax({
            type: 'POST',
            url: '../kpi/inventory_consumption/search',
            data: '&branchid=' + branchid + '&productid=' + productid + '&from_date=' + from_date + '&to_date=' + to_date,
            success: function (return_data) {

                var data = return_data;
                var strHtml = '';
                if(data!=-1){
                    strHtml="<tr><td>"+data.productCode+"</td><td>"+data.name+"</td><td>"+data.consumptionRate+"</td><td>"+data.consumptionPrice+"</td></tr>"
                }else{
                    strHtml="<tr><td>No Results</td></tr>";
                }
                $("#tblProductsBody").html(strHtml);
            },
            
        });

    });
  
    
    $("#from_date").datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate: '+0D',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function(selected) {
        $("#to_date").datepicker("option","minDate", selected)
        }
    });

    $("#to_date").datepicker({
        dateFormat: 'dd-mm-yy',
        maxDate: '+0D',
        yearRange: '1950:c',
        changeMonth: true,
        changeYear: true,
        onSelect: function(selected) {
        $("#from_date").datepicker("option","maxDate", selected)
        }
    });
   
});
</script>
@endsection