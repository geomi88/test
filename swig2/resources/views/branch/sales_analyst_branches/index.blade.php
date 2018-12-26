@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Sales Analyst <span>Branch Allocation</span></h1>
    </header>	
    <form id="frmanlystallocation">
  
    <div class="fieldGroup" id="fieldSet1">
        <div class="custRow">
            <div class="custCol-4">
                <div class="inputHolder bgSelect">
                    <label>Sales Analyst</label>
                    <select  name="analystid" id="analystid">
                        <option selected value=''>Select Analyst</option>
                        @foreach ($salesanalysts as $analyst)
                        <option value='{{ $analyst->id }}'>{{ $analyst->first_name}} {{ $analyst->alias_name}}</option>
                        @endforeach
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>
        </div>
    </div>
        
    <div class="listHolderType1">

        <div class="listerType1 reportLister"> 

            <div id="tblanalyst">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        
                        <td>
                            Sl.No.
                        </td>
                        <td>
                            Branch Code
                            
                        </td>
                        <td>
                            Name
                        </td>
                       
                        <td>
                            Select
                        </td>
                        <td>
                            Allocated to
                        </td>
                        <td>
                            Status
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
    $('#analystid').on('change', function () {
        if($("#analystid").val()!=''){
            getanalystbranchlist($("#analystid").val());
        }else{
            var strHtml='<tr><td>No Records Found</td></tr>';
            $("#tblBranchBody").html(strHtml);
        }
    });
    
    
    $('body').on('click', '.chkanalyst', function() {
        var branchid = $(this).attr("id");
        var branchname = $(this).attr("br-name");
        var analystid = $("#analystid").val();
        
        var status;
        var strMsg='';
        if($("#"+branchid).prop('checked') == true){
            status = 1;
            strMsg = "Are you sure to allocate branch "+branchname+" to "+$("#analystid :selected").text();
        }else{
            status = 0;
            strMsg = "Are you sure to deallocate branch "+branchname+" from "+$("#analystid :selected").text();
        }
        var blnConfirm = confirm(strMsg);
        
        if(blnConfirm){
            $.ajax({
                type: 'POST',
                url: '../branch/sales_analyst_branches/store',
                data: '&branchid=' + branchid + '&analystid=' + analystid + '&status=' + status,
                success: function (return_data) {
                    getanalystbranchlist(analystid);
                },
                error: function (return_data) {
                    window.location.href = '{{url("branch")}}';
                }
            });
        }else{
            getanalystbranchlist(analystid);
        }
            
    });
    
   
});

function getanalystbranchlist(analystid){
    $.ajax({
        type: 'POST',
        url: '../branch/sales_analyst_branches/getanalystbranches',
        data: '&analystid=' + analystid,
        success: function (return_data) {
            
            filldatatogrid(return_data);
        },
        error: function (return_data) {
            window.location.href = '{{url("branch")}}';
        }
    });
}

function filldatatogrid(data){
    var strHtml='';

    if(data!=-1){
        for(var i=0;i<data.length;i++){
            
             strHtml+='<tr><td>'+(i+1)+'</td>'+
                '<td>'+data[i].branch_code+'</td>'+
                '<td>'+data[i].name+'</td>'+
                '<td><input type="checkbox" br-name='+ data[i].branch_code +' id='+ data[i].id +' class="chkanalyst"'+data[i].strChkProperty+'></td>'+
                '<td>'+data[i].empName+'</td>'+
                '<td>'+data[i].strStatus+'</td></tr>'
        }
       
    }else{
        strHtml='<tr><td>No Records Found</td></tr>';
    }
    $("#tblBranchBody").html(strHtml);
}
</script>
@endsection