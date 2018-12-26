<?php if(count($requisitions) > 0){ ?>
<?php $n = $requisitions->perPage() * ($requisitions->currentPage() - 1); ?>   
@foreach ($requisitions as $requisition)

<tr>
    <td>{{$requisition->requisition_code}}</td>
    <td>{{$requisition->title}}</td>
    <td class="amountAlign"><?php echo Customhelper::numberformatter($requisition->total_price);?></td>
    <td class="amountAlign"><?php echo Customhelper::numberformatter($requisition->outstanding_amount);?></td>
    <td>
        <input type="hidden" id="balance_{{$requisition->id}}" value="{{$requisition->outstanding_amount}}">
        <input type="hidden" id="total_{{$requisition->id}}" value="{{$requisition->total_price}}">
        <input type="hidden" id="code_{{$requisition->id}}" value="{{$requisition->requisition_code}}">
        <input type="hidden" id="type_{{$requisition->id}}" value="{{$requisition->req_name}}">
        <input class="numberwithdot txtamount" maxlength="20" autocomplete="off" type="text" attrid="{{$requisition->id}}" id="amount_{{$requisition->id}}">
    </td>
    <td class="check">
        <div class="commonCheckHolder checkboxRender">
            <label>
                <input name="check" type="checkbox" id="{{$requisition->id}}" class="chkfullpayment" >
                <span></span>
            </label>
        </div>
    </td>
</tr>
@endforeach
<tr>
    <td></td><td></td><td class="amountAlign" colspan="2"><span class="lbltotalpayment">Total Amount</span></td><td><strong class="lbltotalpayment" id="totalpayment"></strong></td><td></td>
</tr>
<tr class="paginationHolder"><th><div>   {!! $requisitions->render() !!}</div> </th></tr>
<?php } else { ?>
    <tr><td colspan="2">No Records Found</td></tr>
<?php } ?>
                