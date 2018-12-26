<?php
$n = $pos_sales->perPage() * ($pos_sales->currentPage() - 1);
$n++;
?>   

@foreach ($pos_sales as $pos_sale)
<?php
$cashdifference = ($pos_sale->cash_sale) - ($pos_sale->cash_collection);
$bankdifference = $pos_sale->bank_sale - $pos_sale->bank_collection;
?>

<tr>
    <td class="sl_no">{{ $n++ }}</td>
    <td class="pos_date"><?php echo date("d-m-Y", strtotime($pos_sale->pos_date)); ?></td>
    <td class="pos_branch">{{$pos_sale->branch_code}}-{{$pos_sale->branch_name}}</td>
    <td class="pos_name">{{$pos_sale->employee_fname}} {{$pos_sale->employee_aname}}</td>
    <td class="pos_cashier">{{$pos_sale->cashier_fname}} {{$pos_sale->cashier_aname}}</td>
    <td class="pos_jobshift">{{$pos_sale->jobshift_name}}</td>                      
    <td class="pos_opening_amount">{{Customhelper::numberformatter(($pos_sale->opening_amount) ? $pos_sale->opening_amount : 0)}}</td> 
    <td class="pos_total_sale">{{Customhelper::numberformatter(($pos_sale->total_sale) ? $pos_sale->total_sale : 0)}}</td>
    <td class="pos_cash_collection">{{Customhelper::numberformatter(($pos_sale->cash_collection) ? $pos_sale->cash_collection : 0)}}</td> 
    <td class="pos_credit_sale">{{Customhelper::numberformatter(($pos_sale->credit_sale) ? $pos_sale->credit_sale : 0)}}</td>
    <td class="pos_bank_sale">{{Customhelper::numberformatter(($pos_sale->bank_sale) ? $pos_sale->bank_sale : 0)}}</td>
    <td class="pos_cash_difference">{{Customhelper::numberformatter(($cashdifference) ? $cashdifference : 0)}}</td> 
    <td class="pos_bank_difference">{{Customhelper::numberformatter(($bankdifference) ? $bankdifference : 0)}}</td> 
    <td class="pos_difference">{{Customhelper::numberformatter(($pos_sale->difference) ? $pos_sale->difference : 0)}}</td>
    <td class="pos_meals">{{Customhelper::numberformatter(($pos_sale->meal_consumption) ? $pos_sale->meal_consumption : 0)}}</td>
    <td class="pos_reason">{{$pos_sale->reason}}</td>
    <td class="editedby">{{$pos_sale->editedby_name}} {{$pos_sale->editedby_aname}}</td>
    <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('mis/pos_sales/supervisor_show', ['id' => Crypt::encrypt($pos_sale->id)]) }}">View</a>
        </div>
    </td>
</tr>
@endforeach
<tr>
    <td class="sl_no"></td>
    <td class="pos_date"></td>
    <td class="pos_branch"></td>
    <td class="pos_name"></td>
    <td class="pos_cashier"></td>
    <td class="pos_jobshift"></td>
    <td class="pos_opening_amount">Total</td>
    <!--<td class="pos_opening_amount">{{ Customhelper::numberformatter($total_list->t_opening_amount) }}</td>-->
    <td class="pos_total_sale">{{ Customhelper::numberformatter($total_list->t_total_sale) }}</td>
    <td class="pos_cash_collection">{{ Customhelper::numberformatter($total_list->t_cash_collection) }}</td>
    <td class="pos_credit_sale">{{ Customhelper::numberformatter($total_list->t_credit_sale) }}</td>
    <td class="pos_bank_sale">{{ Customhelper::numberformatter($total_list->t_bank_sale) }}</td>
    <td class="pos_cash_difference">{{ Customhelper::numberformatter($total_list->t_cashdifference) }}</td>
    <td class="pos_bank_difference">{{ Customhelper::numberformatter($total_list->t_bankdifference) }}</td>
    <td class="pos_difference">{{ Customhelper::numberformatter($total_list->t_difference) }}</td>
    <td class="pos_meals">{{ Customhelper::numberformatter($total_list->t_meal_consumption) }}</td>
    <td class="pos_reason"></td>
    <td class="editedby"></td>
    <td class="actionBtnSet"></td>
</td>
</tr>
<?php if ($pos_sales->lastPage() > 1) { ?>
    <tr class="paginationHolder"><th><div>   {!! $pos_sales->render() !!}</div> </th></tr>
<?php } ?>