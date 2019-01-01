@php
    $total_qty = 0;
@endphp
@forelse($inv_report_list as $row)
<tr id="row_{{ $row->stock_area_id }}">
    <td width="2%" class="expandIcon">
        <input type="hidden" value="{{ $conv_value }}" id="conversion">
        <a class="view_details" href="javascript:void(0)" id="button_{{ $row->stock_area_id }}">+</a>
    </td>
    <td><strong>{{ $row->name . $row->branch_code }}</strong></td>
    <td class="amountAlign"><strong><?php echo Customhelper::numberformatter($row->stock_remaining / $conv_value) . ' <span>' . $unit . '</span>'; ?></strong></td>
</tr>
@php
    $total_qty += Customhelper::numberformatter($row->stock_remaining);
@endphp
@empty
<tr>
    <td></td>
    <td>No record found</td>
    <td></td>
</tr>
@endforelse
@php echo $total_val @endphp
@if($primary_total)
<tr class="clstrTotalseparator">
    <td class="total_td1"></td>
    <td style="text-align: right;"><strong>Total Stock: </strong></td>
    <td class="alignRight"><strong ><?= Customhelper::numberformatter($total_qty / $primary_total->conv_value) ?>  <span>{{ $primary_total->name }}</span></td>
</tr>
@endif
@forelse($Alt_total as $row)
    @if($row->conv_value)
    <tr>
        <td class="total_td1"></td>
        <td></td>
        <td class="alignRight"><strong><?= Customhelper::numberformatter($total_qty / $row->conv_value) ?>  <span> {{ $row->name }}</span></td>
    </tr>
    @else
    <tr>
        <td class="total_td1"></td>
        <td></td>
        <td class="alignRight"><strong>0.00 <span>{{ $row->name }}</span></strong> </td>
    </tr>
    @endif
@empty

@endforelse