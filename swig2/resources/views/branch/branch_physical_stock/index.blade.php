@extends('layouts.main')
@section('content')
<div class="innerContent">
    	
     <header class="pageTitle">
        <h1>Branch <span>Physical Stock</span></h1>
    </header>	
    <form id="frmanlystallocation">
  
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
             <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>Branch</label>
                        <select class="commoSelect" name="cmbbranch" id="cmbbranch">
                            <option value=''>Select Branch</option>
                            @foreach ($branch_details as $branch)
                                <option value='{{ $branch->branch_id }}'>{{ $branch->branch_code}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
            </div>
             <div class="custCol-8 alignRight">
                 <div class="inputHolder bgSelect" id="stockdate">
                    
                </div>
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
                         
                    </tr>
                </thead>
              
                 <tbody class="tblBranchBody" id='tblBranchBody'>
                   <tr><td>No Records Found</td></tr>
                </tbody>

            </table>
            </div>
            <div class="commonLoaderV1"></div>
            
        </div>

    </div>
        
           
    </form>
</div>
<script>
var arrProductsQty = [];
$(document).ready(function ()
{
    $('#cmbbranch').on('change', function () {
        if($("#cmbbranch").val()!=''){
            getphysicalstock($("#cmbbranch").val());
        }else{
            var strHtml='<tr><td>No Records Found</td></tr>';
            $("#tblBranchBody").html(strHtml);
        }
    });
    
});

function getphysicalstock(branchid){
    $.ajax({
        type: 'POST',
        url: '../branch/branch_physical_stock/getphysicalstock',
        data: '&branchid=' + branchid,
        success: function (return_data) {
            filldatatogrid(return_data);
        },
//        error: function (return_data) {
//            window.location.href = '{{url("branch")}}';
//        }
    });
}

function filldatatogrid(data){
    var strHtml='';

    if(data!=-1){
        for(var i=0;i<data.length;i++){
            
             strHtml+='<tr><td>'+(i+1)+'</td>'+
                '<td>'+data[i].productCode+'</td>'+
                '<td>'+data[i].name+'</td>'+
                '<td>'+data[i].quantity+'</td></tr>'
        }
       
    }else{
        strHtml='<tr><td>No Records Found</td></tr>';
    }
    $("#tblBranchBody").html(strHtml);
    if(data[0].strDate != ''){
        $("#stockdate").html('<label>Stock As On</label><b id="stockdate">'+data[0].strDate+'</b>');
    }else{
        $("#stockdate").html('');
    }    
}
</script>
@endsection