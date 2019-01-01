@forelse($stockdata as $item)
<tr id="parentrow_{{ $item->item_id }}" >
    <td width="2%" class="expandIcon">
        <a class="view_details" href="javascript:void(0)" attrid="{{ $item->item_id }}">+</a>
    </td>
    <td><strong>{{$item->product_code." : ".$item->name}}</strong></td>
    <td class="amountAlign"><strong><?php echo Customhelper::numberformatter($item->stock_remaining); ?> <span>{{$item->unitname}}</span></strong></td>
    
</tr>
@empty
<tr>
    <td></td>
    <td>No records found</td>
    <td></td>
</tr>
@endforelse
