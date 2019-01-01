@extends('layouts.main')
@section('content')
<script>
   
    $(document).ready(function ()
    {
        $('.takePrint').click(function () {
            
            if(!$("#collectionStatus").is(':checked')&&!$("#dateP").is(':checked')&&!$("#shiftP").is(':checked')&&!$("#openingP").is(':checked')
                    &&!$("#totBranchSale").is(':checked')&&!$("#saleAmt").is(':checked')&&!$("#taxAmt").is(':checked')&&!$("#totCashSale").is(':checked')&&!$("#cashCollection").is(':checked')&&!$("#cashDiff").is(':checked')
                    &&!$("#totBankSale").is(':checked')&&!$("#bankCollection").is(':checked')&&!$("#bankDiff").is(':checked')&&!$("#creditSale").is(':checked')&&!$("#netDiff").is(':checked')&&!$("#mealConsumption").is(':checked')
                    &&!$("#cashierNameP").is(':checked')&&!$("#editedBy").is(':checked')&&!$("#reason").is(':checked')) {
                toastr.error('Select required fields to print!');
            } else{                  
                var pageTitle = 'Page Title',

                stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
                win = window.open('', 'Print', 'width=500,height=300');

                var strStyle='<style>.paginationHolder {display:none;}';

                 if(!$("#collectionStatus").is(':checked')){
                        strStyle+=' .coll_stat {display:none;}';
                 }
                 if(!$("#dateP").is(':checked')){
                        strStyle+=' .date_pos {display:none;}';
                 }
                 if(!$("#shiftP").is(':checked')){
                       strStyle+=' .job_shift {display:none;}';
                 }
                 if(!$("#branchP").is(':checked')){
                      strStyle+=' .branch_nme {display:none;}';  
                 } 
                 if(!$("#openingP").is(':checked')){
                      strStyle+=' .opening_amt {display:none;}';  
                 }
                 if(!$("#totBranchSale").is(':checked')){
                      strStyle+=' .tot_sale {display:none;}';  
                 }
                 if(!$("#saleAmt").is(':checked')){
                      strStyle+=' .sale_amt {display:none;}';  
                 }
                 if(!$("#taxAmt").is(':checked')){
                      strStyle+=' .tax_amt {display:none;}';  
                 }
                 if(!$("#totCashSale").is(':checked')){
                      strStyle+=' .tot_cash_sale {display:none;}';  
                 }
                 if(!$("#cashCollection").is(':checked')){
                      strStyle+=' .cash_coll {display:none;}';  
                 }
                 if(!$("#cashDiff").is(':checked')){
                      strStyle+=' .cash_diff {display:none;}';  
                 }
                 if(!$("#totBankSale").is(':checked')){
                      strStyle+=' .tot_bank_slae {display:none;}';  
                 }
                 if(!$("#bankCollection").is(':checked')){
                      strStyle+=' .bank_coll {display:none;}';  
                 }
                 if(!$("#bankDiff").is(':checked')){
                      strStyle+=' .bank_diff {display:none;}';  
                 }
                 if(!$("#creditSale").is(':checked')){
                      strStyle+=' .credit_sale {display:none;}';  
                 }
                 if(!$("#netDiff").is(':checked')){
                      strStyle+=' .net_diff {display:none;}';  
                 }
                 if(!$("#mealConsumption").is(':checked')){
                      strStyle+=' .meal_cons {display:none;}';  
                 }
                 if(!$("#cashierNameP").is(':checked')){
                      strStyle+=' .cashier_name {display:none;}';  
                 }
                 if(!$("#editedBy").is(':checked')){
                      strStyle+=' .edited_by {display:none;}';  
                 }
                 if(!$("#reason").is(':checked')){
                      strStyle+=' .reason_c {display:none;}';  
                 }
                strStyle+='.actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>';

                win.document.write(strStyle+'<div style="text-align:center;"><h1>Collection Details</h1></div>'+ ($("#printTopp").html()).replace("</table>",'')+
                                        '<tr><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                            '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                                '<tr class="headingHolder">'+
                                                    '<td style="padding:10px 0;color:#fff;" class="coll_stat"> Collection Status</td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="date_pos"> Date</td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="job_shift"> Shift</td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="branch_nme"> Branch</td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="opening_amt"> Opening Amount </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="tot_sale"> Total Branch Sale </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="sale_amt"> Sale Amount </td>'+ 
                                                    '<td style="padding:10px 0;color:#fff;" class="tax_amt"> Tax Amount </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="tot_cash_sale"> Total Cash Sale </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="cash_coll"> Cash Collection </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="cash_diff"> Cash Difference </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="tot_bank_slae"> Total Bank Card Sale </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="bank_coll"> Bank Collection </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="bank_diff"> Bank Difference </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="credit_sale"> Credit/Free Sale </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="net_diff"> Net Difference </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="meal_cons"> Meal Consumption </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="cashier_name"> Cashier Name </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="edited_by"> Edited By </td>'+
                                                    '<td style="padding:10px 0;color:#fff;" class="reason_c"> Reason </td>'+
                                                '</tr>'+
                                            '</thead>'+ $('.resultbody')[0].outerHTML +'</table></tr>'+$("#printBottom").html()+'</table>');   
                win.document.close();
                win.print();
                win.close();
                return false;
            }
        });
        
    });
    
  


</script>
<div class="contentArea">
    <a class="btnAction action bgGreen" href="{{ URL::to('branchsales/cash_collection/topcashierfund')}}">Back</a>
<div class="innerContent">
    <div id="printTopp" style="display: none;">
    <table style="font-family:open_sansregular; font-size: 14px; height: auto;">
        <tr>
            <td style="vertical-align: middle; width: 25%">
                <p>
                    <p style="margin-right: 20px; font-weight: bold; color: #909090;"> Employee Name</p>
                    <p style="margin-right: 20px; font-weight: bold; color: #909090;"> Code</p>
                    <p style="margin-right: 20px; font-weight: bold; color: #909090;"> Designation</p>
                </p>
            </td>
             <td style="vertical-align: middle; margin-left: 30px; width: 25%">
                <p>
                    <p>- {{$cashcollectionid->first_name}}</p>
                    <p> - {{$cashcollectionid->username}}</p>
                    <p> - Supervisor</p>
                </p>
            </td>
            <td style="vertical-align: middle;">
                <p>
                    <p style="margin-right: 20px; font-weight: bold; color: #909090;"> Employee Name</p>
                    <p style="margin-right: 20px; font-weight: bold; color: #909090;"> Code</p>
                    <p style="margin-right: 20px; font-weight: bold; color: #909090;"> Designation</p>
                </p>
            </td>
             <td style="vertical-align: middle;">
                <p>
                    <p>- {{$collected_by_name->first_name}}</p>
                    <p> - {{$collected_by_name->username}}</p>
                    <p> - Top Cashier</p>
                </p>
            </td>
            <td style="vertical-align: top; border:0; text-align: right; margin-top: -33px; margin-right: 14px; width: 4%;">
                <img src="{{URL::asset('images/imgImtiyazatLogo.png')}}" style="width: 105px">
            </td>
        </tr>
    </table>
</div>
    <div id="printBottom"  style="display: none;">
        <tr>
            <td>
                <p style="text-align: right; font-size: 20px;">
                    Total:  <strong id="printTotal">Total : <?php echo $total_cash ?></strong> 
                </p>
            </td>
        </tr>
        <tr>
            <td>
                <p style="text-align: left;" id="printComment">
                    
                </p>
            </td>
        </tr>
    </div>    
    
 <form id="transfer_detail_collection" action="{{ url('branchsales/cash_collection/collectionexporttopdf') }}" method="post">
       
        <h4 class="blockHeadingV1">Collection Details</h4>
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <!--<a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>-->
        
        <div class="printChoose">
        <div class="custRow">
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="collectionStatus" checked="" type="checkbox">
                        <span></span>
                        <em>Collection Status</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="dateP" checked="" type="checkbox">
                        <span></span>
                        <em>Date</em>
                    </label>
                </div>
            </div>

            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="shiftP" checked="" type="checkbox">
                        <span></span>
                        <em>Shift</em>
                    </label>
                </div>
            </div>
        </div>

        <div class="custRow">
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="branchP" checked="" type="checkbox">
                        <span></span>
                        <em>Branch</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="openingP" checked="" type="checkbox">
                        <span></span>
                        <em>Opening Amount</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="totBranchSale" checked="" type="checkbox">
                        <span></span>
                        <em>Total Branch Sale</em>
                    </label>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="saleAmt" checked="" type="checkbox">
                        <span></span>
                        <em>Sale Amount</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="taxAmt" checked="" type="checkbox">
                        <span></span>
                        <em>Tax Amount</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="totCashSale" checked="" type="checkbox">
                        <span></span>
                        <em>Total Cash Sale</em>
                    </label>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="cashCollection" checked="" type="checkbox">
                        <span></span>
                        <em>Cash Collection</em>
                    </label>
                </div>
            </div>
           <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="cashDiff" checked="" type="checkbox">
                        <span></span>
                        <em>Cash Difference</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="totBankSale" checked="" type="checkbox">
                        <span></span>
                        <em>Total Bank Card Sale</em>
                    </label>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="bankCollection" checked="" type="checkbox">
                        <span></span>
                        <em>Bank Collection</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="bankDiff" checked="" type="checkbox">
                        <span></span>
                        <em>Bank Difference</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="creditSale" checked="" type="checkbox">
                        <span></span>
                        <em>Credit/Free Sale</em>
                    </label>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="netDiff" checked="" type="checkbox">
                        <span></span>
                        <em>Net Difference</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="mealConsumption" checked="" type="checkbox">
                        <span></span>
                        <em>Meal Consumption</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="cashierNameP" checked="" type="checkbox">
                        <span></span>
                        <em>Cashier Name</em>
                    </label>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="editedBy" checked="" type="checkbox">
                        <span></span>
                        <em>Edited By</em>
                    </label>
                </div>
            </div>
            <div class="custCol-4">
                <div class="commonCheckHolder checkRender">
                    <label>
                        <input id="reason" checked="" type="checkbox">
                        <span></span>
                        <em>Reason</em>
                    </label>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <a class="commonBtn takePrint bgDarkGreen">Print</a>
            </div>
        </div>
    </div>
                
                <input type="hidden" id="cashcollectionid" name="cashcollectionid"  value="{{$cashcollectionid->id}}">
                <input type="hidden" id="posids" name="posids" value="{{$posids}}">
            <div class="selected_pos">
                <div id="printTop">
                    <table>
                        <tr class="noHover">
                            <td style="vertical-align: top;">
                                <div class="custRow empDetails">
                                    <div class="custCol-12">
                                        <div class="detailsWrapper">
<!--                                            <figure class="empImg">
                                                <img src="<?php //echo $cashcollectionid->profilepic; ?>">
                                            </figure>-->
                                            <div class="detailsSection">
                                                <div class="singleLine">
                                                    <p> Employee Name :</p>
                                                    <span > {{$cashcollectionid->first_name}}</span>
                                                </div>
                                                <div class="singleLine">
                                                    <p> Code :</p>
                                                    <span > {{$cashcollectionid->username}}</span>
                                                </div>
                                                <div class="singleLine">
                                                    <p> Role :</p>
                                                    <span > Supervisor</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align: top;">
                                <div class="custRow empDetails">
                                    <div class="custCol-12">
                                        <div class="detailsWrapper">
<!--                                            <figure class="empImg">
                                                <img src="<?php //echo $collected_by_name->profilepic; ?>">
                                            </figure>-->
                                            <div class="detailsSection">
                                                <div class="singleLine">
                                                    <p> Employee Name :</p>
                                                    <span > {{$collected_by_name->first_name}}</span>
                                                </div>
                                                <div class="singleLine">
                                                    <p> Code :</p>
                                                    <span > {{$collected_by_name->username}}</span>
                                                </div>
                                                <div class="singleLine">
                                                    <p> Role :</p>
                                                    <span > Top Cashier</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            <div class="listHolderType1 ">
                <div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Collection Status</td>
                                <td>Date</td>
                                <td>Shift</td>
                                <td>Branch Name</td>
                                <td>Opening Amount</td>
                                <td>Total Branch Sale</td>
                                <td>Sale Amount</td>
                                <td>Tax Amount</td>
                                <td>Total Cash Sale</td>
                                <td>Collected Cash</td>
                                <td>Cash Difference</td>
                                <td>Bank Sale</td>
                                <td>Bank Collection</td>
                                <td>Bank Difference</td>
                                <td>Credit sale</td>
                                <td>Net Difference</td>
                                <td>Meal Consumption</td> 
                                <!--<td>Collection Status <br><input type="checkbox" id="selectAll" class="chkallsales"> Select all</td>-->
                                <td>Cashier Name</td> 
                                <td>Edited By</td> 
                                <td>Reason</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        
                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">
                                <td>
                                    <label><input type="checkbox" id="selectAll" class="chkallsales"> Select all</label>

                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <input type="text" id="start_date" name="start_date" value="" placeholder="Start Date">
                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="end_date" name="end_date" value="End Date">
                                        </div>

                                    </div>
                                </td>
                                
                                <td class="">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <select class="shift" name="shift" id="shift">
                                                <option value="">All</option>
                                                @foreach ($shift_names as $shift_name)
                                                <option value="{{$shift_name->jobshift_id}}">{{$shift_name->jobshift_name}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                </td>
                               <td>
                                    <div class="custCol-12">
                                        <select class="branch" name="branch" id="branch">
                                            <option value="">All</option>
                                            @foreach ($branch_names as $branch_name)
                                            <option value="{{$branch_name->branch_id}}">{{$branch_name->branch_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="openingorder" name="openingorder" id="openingorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="openingamount" name="openingamount" >
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="torder" name="torder" id="torder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="tamount" name="tamount" >
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="saleorder" name="saleorder" id="saleorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="saleamount" name="saleamount" >
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="taxorder" name="taxorder" id="taxorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="taxamount" name="taxamount" >
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="cashsaleorder" name="cashsaleorder" id="cashsaleorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="cashsaleamount" name="cashsaleamount" >
                                        </div>

                                    </div>
                                </td>
                                
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="ccorder" name="ccorder" id="ccorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="ccamount" name="ccamount" >
                                        </div>

                                    </div>
                                </td>
                                
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="cashdifforder" name="cashdifforder" id="cashdifforder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="cashdiffamount" name="cashdiffamount" >
                                        </div>

                                    </div>
                                </td>
                         
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="border" name="border" id="border">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="bamount" name="bamount" >
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="bankcollectionorder" name="bankcollectionorder" id="bankcollectionorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="bankcollectionamount" name="bankcollectionamount" >
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="bankdifforder" name="bankdifforder" id="bankdifforder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="bankdiffamount" name="bankdiffamount" >
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="crorder" name="crorder" id="crorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="cramount" name="cramount" >
                                        </div>

                                    </div>
                                </td>
                           
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="dorder" name="dorder" id="dorder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="damount" name="damount" >
                                        </div>

                                    </div>
                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <select class="morder" name="morder" id="morder">
                                                <option value="">Select</option>
                                                <option value=">">></option>
                                                <option value="<"><</option>
                                                <option value="=">=</option>
                                            </select>

                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="mamount" name="mamount" >
                                        </div>

                                    </div>
                                </td>
                                <td>
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <input type="text" id="cashiername" name="cashiername" placeholder="Enter Cashier Code">
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                        </thead>
                        
                        <tbody class="resultbody">
                            @include('branchsales/cash_collection/filter_result')
                        </tbody>
                        
                    </table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>

                <h4 class="blockHeadingV1 spacingBtm2 alignRight" id="total">Total : <?php echo $total_cash ?></h4>	
            </div>
            <div class="empComments">
                <textarea rows="4" cols="50" placeholder="comments"  id="comment"></textarea>
            </div>
                <div class="custRow">
                    <input value="Submit" class="commonBtn bgRed addBtn" type="button" id="btnSaveCollection">
                </div>
            </form>
    
</div>
</div>
<script>

$(document).ready(function ()
{
    $('body').on('click', '.chkpossales', function() {
        
        if($('input:checkbox.chkpossales:not(:checked)').length==0){
            $('#selectAll').prop("checked", true);
        }else{
            $('#selectAll').prop("checked", false);
        }
      
    });
    
    $('#selectAll').click(function() {
        
        if ($('#selectAll').prop("checked")) {
            $('.chkpossales').prop("checked", true);
        } else {
            $('.chkpossales').prop("checked", false);
        }
      
    });
    
    $('#btnSaveCollection').click(function() {
        var arrSalesId = '';
       arrSalesId=$('input:checkbox.chkpossales:checked').map(function () {return this.id;}).get();
        
        if(arrSalesId==''){
            alert("Please select atleast one checkbox");
            return;
        }
        
        var blnConfirm = confirm("Are you sure to submit");
        if(blnConfirm){
            $.ajax({
                type: 'POST',
                url: '../collect_cash',
                data: '&arrSalesId=' + arrSalesId + '&posids=' + $("#posids").val()+
                    '&cashcollectionid='+$("#cashcollectionid").val(),
                success: function (return_data) {
                    window.location.reload();
                },
                error: function (return_data) {
                    window.location.href = '{{url("branchsales/cash_collection/topcashierfund")}}';
                }
            });
        }
       
    });
    
    $("#start_date").datepicker({
        changeMonth: true,
        changeYear: true, dateFormat: 'dd-mm-yy'
    });

    $("#end_date").datepicker({
        changeMonth: true,
        changeYear: true, dateFormat: 'dd-mm-yy'
    }).datepicker("setDate", new Date());
        
    $('.branch').on("change", function () {
        search();
    });
    $('.shift').on("change", function () {
        search();
    });
   
   $('#start_date').on("change", function () {
        if ($('#start_date').val() !== '')
        {

            search();
        }
    });
    $('#end_date').on("change", function () {
        if ($('#end_date').val() !== '')
        {

            search();
        }
    }); 
    
    $('.openingorder').on("change", function () {
         var openingamount = $('#openingamount').val();
        if ($('.openingorder').val() !== '' && $.isNumeric(openingamount))
        {

            search();
        }
    });
    $('#openingamount').bind('keyup', function () {
        var openingorder = $('.openingorder').val();
         var openingamount = $('#openingamount').val();
         if(openingorder!=""  && $.isNumeric(openingamount)){
            search();
            }
      
    });
    
    $('.torder').on("change", function () {
         var tamount = $('#tamount').val();
        if ($('.torder').val() !== '' && $.isNumeric(tamount))
        {

            search();
        }
    });
    $('#tamount').bind('keyup', function () {
        var torder = $('.torder').val();
         var tamount = $('#tamount').val();
         if(torder!=""  && $.isNumeric(tamount)){
            search();
            }
      
    });
    
    $('.saleorder').on("change", function () {
         var saleamount = $('#saleamount').val();
        if ($('.saleorder').val() !== '' && $.isNumeric(saleamount))
        {

            search();
        }
    });
    $('#saleamount').bind('keyup', function () {
        var saleorder = $('.saleorder').val();
         var saleamount = $('#saleamount').val();
         if(saleorder!=""  && $.isNumeric(saleamount)){
            search();
            }
      
    });
    
    $('.taxorder').on("change", function () {
         var taxamount = $('#taxamount').val();
        if ($('.taxorder').val() !== '' && $.isNumeric(taxamount))
        {

            search();
        }
    });
    $('#taxamount').bind('keyup', function () {
        var taxorder = $('.taxorder').val();
         var taxamount = $('#taxamount').val();
         if(taxorder!=""  && $.isNumeric(taxamount)){
            search();
            }
      
    });
    
    $('.cashsaleorder').on("change", function () {
         var cashsaleamount = $('#cashsaleamount').val();
        if ($('.cashsaleorder').val() !== '' && $.isNumeric(cashsaleamount))
        {

            search();
        }
    });
    $('#cashsaleamount').bind('keyup', function () {
        var cashsaleorder = $('.cashsaleorder').val();
         var cashsaleamount = $('#cashsaleamount').val();
         if(cashsaleorder!=""  && $.isNumeric(cashsaleamount)){
            search();
            }
      
    });
    
    $('.ccorder').on("change", function () {
         var ccamount = $('#ccamount').val();
        if ($('.ccorder').val() !== '' && $.isNumeric(ccamount))
        {

            search();
        }
    });
    $('#ccamount').bind('keyup', function () {
        var ccorder = $('.ccorder').val();
         var ccamount = $('#ccamount').val();
         if(ccorder!=""  && $.isNumeric(ccamount)){
            search();
            }
      
    });
    
    $('.cashdifforder').on("change", function () {
         var cashdiffamount = $('#cashdiffamount').val();
        if ($('.cashdifforder').val() !== '' && $.isNumeric(cashdiffamount))
        {

            search();
        }
    });
    $('#cashdiffamount').bind('keyup', function () {
        var cashdifforder = $('.cashdifforder').val();
         var cashdiffamount = $('#cashdiffamount').val();
         if(cashdifforder!=""  && $.isNumeric(cashdiffamount)){
            search();
            }
      
    });
    
    $('.crorder').on("change", function () {
         var cramount = $('#cramount').val();
        if ($('.crorder').val() !== '' && $.isNumeric(cramount))
        {

            search();
        }
    });
    $('#cramount').bind('keyup', function () {
        var crorder = $('.crorder').val();
         var cramount = $('#cramount').val();
         if(crorder!=""  && $.isNumeric(cramount)){
            search();
            }
      
    });
    
    $('.border').on("change", function () {
         var bamount = $('#bamount').val();
        if ($('.border').val() !== '' && $.isNumeric(bamount))
        {

            search();
        }
    });
    $('#bamount').bind('keyup', function () {
        var border = $('.border').val();
         var bamount = $('#bamount').val();
         if(border!=""  && $.isNumeric(bamount)){
            search();
            }
      
    });
    
    $('.bankcollectionorder').on("change", function () {
         var bankcollectionamount = $('#bankcollectionamount').val();
        if ($('.bankcollectionorder').val() !== '' && $.isNumeric(bankcollectionamount))
        {

            search();
        }
    });
    $('#bankcollectionamount').bind('keyup', function () {
        var bankcollectionorder = $('.bankcollectionorder').val();
         var bankcollectionamount = $('#bankcollectionamount').val();
         if(bankcollectionorder!=""  && $.isNumeric(bankcollectionamount)){
            search();
            }
      
    });
    
    $('.bankdifforder').on("change", function () {
         var bankdiffamount = $('#bankdiffamount').val();
        if ($('.bankdifforder').val() !== '' && $.isNumeric(bankdiffamount))
        {

            search();
        }
    });
    $('#bankdiffamount').bind('keyup', function () {
        var bankdifforder = $('.bankdifforder').val();
         var bankdiffamount = $('#bankdiffamount').val();
         if(bankdifforder!=""  && $.isNumeric(bankdiffamount)){
            search();
            }
      
    });
    
     $('.dorder').on("change", function () {
         var damount = $('#damount').val();
        if ($('.dorder').val() !== '' && $.isNumeric(damount))
        {

            search();
        }
    });
    $('#damount').bind('keyup', function () {
        var dorder = $('.dorder').val();
         var damount = $('#damount').val();
         if(dorder!=""  && $.isNumeric(damount)){
            search();
            }
      
    });
    
    $('.morder').on("change", function () {
         var mamount = $('#mamount').val();
        if ($('.morder').val() !== '' && $.isNumeric(mamount))
        {

            search();
        }
    });
    $('#mamount').bind('keyup', function () {
        var morder = $('.morder').val();
         var mamount = $('#mamount').val();
         if(morder!=""  && $.isNumeric(mamount)){
            search();
            }
      
    });
    
    $('#cashiername').bind('keyup', function () {
        search();
    });
    
    function search()
    {

        $('#total').html('');
        $('#printTotal').html('');
        
        var branch = $('.branch').val();
        var shift = $('.shift').val();
 
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var posids = $('#posids').val();
        var torder = $('.torder').val(); 
        var tamount = $('#tamount').val();
        var saleorder = $('.saleorder').val(); 
        var saleamount = $('#saleamount').val();
        var taxorder = $('.taxorder').val(); 
        var taxamount = $('#taxamount').val();
        var cashsaleorder = $('.cashsaleorder').val(); 
        var cashsaleamount = $('#cashsaleamount').val();
        var openingorder = $('.openingorder').val(); 
        var openingamount = $('#openingamount').val();
        var ccorder = $('.ccorder').val(); 
        var ccamount = $('#ccamount').val();
        var cashdifforder = $('.cashdifforder').val(); 
        var cashdiffamount = $('#cashdiffamount').val();
        var crorder = $('.crorder').val(); 
        var cramount = $('#cramount').val();
        var border = $('.border').val(); 
        var bamount = $('#bamount').val();
        var bankcollectionorder = $('.bankcollectionorder').val(); 
        var bankcollectionamount = $('#bankcollectionamount').val();
        var bankdifforder = $('.bankdifforder').val(); 
        var bankdiffamount = $('#bankdiffamount').val();
        var dorder = $('.dorder').val(); 
        var damount = $('#damount').val();
        var morder = $('.morder').val(); 
        var mamount = $('#mamount').val();
        var cashiername = $('#cashiername').val();
        
//        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: '../filter_result',
            data: {branch: branch, shift: shift, startdate: startdate, enddate: enddate,posids:posids,torder:torder,tamount:tamount,saleorder:saleorder,saleamount:saleamount,taxorder:taxorder,taxamount:taxamount,cashsaleorder:cashsaleorder,cashsaleamount:cashsaleamount,openingorder:openingorder,openingamount:openingamount,ccorder:ccorder,ccamount:ccamount,cashdifforder:cashdifforder,cashdiffamount:cashdiffamount,crorder:crorder,cramount:cramount,border:border,bamount:bamount,bankcollectionorder:bankcollectionorder,bankcollectionamount:bankcollectionamount,bankdifforder:bankdifforder,bankdiffamount:bankdiffamount,dorder:dorder,damount:damount,morder:morder,mamount:mamount,cashiername:cashiername},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                   // alert(return_data);
                    $('.resultbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.resultbody').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
    $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

            search();
        });
    
     $('#comment').bind('keyup', function () {
        var comment = $('#comment').val();
        $('#printComment').html(comment);
        
    });
});

 function savetopdf(){
      
        
         document.getElementById("transfer_detail_collection").submit();

 }

</script>
@endsection