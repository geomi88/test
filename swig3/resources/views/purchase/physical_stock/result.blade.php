@forelse($batches as $batch)
<tr >
    <td><input type="checkbox" checked attrid="{{$batch->stock_id}}" class="chkbatchstock"></td>
    <td style="width: 150px;">{{ $batch->batch_code }}</td>
    <td class="amountAlign" style="width: 150px;"><?php echo Customhelper::numberformatter($batch->purchase_quantity); ?> <span>{{$batch->unitname}}</span></td>
    <td class="amountAlign" style="width: 150px;"><?php echo Customhelper::numberformatter($batch->stock_remaining); ?> <span>{{$inventorybaseinfo->primaryunitname}}</span></td>
    <td style="width: 100px;"><select style="width: 100%;" id="cmbunit_{{$batch->stock_id}}"><?php echo $altunits;?></select></td>
    <td style="width: 120px;"><input type="text" id="txtqty_{{$batch->stock_id}}" class="clsquantity numberwithdot" autocomplete="off" onpaste="return false;" maxlength="15"></td>
</tr>
@empty
<tr>
    <td colspan="5">No records found</td>
</tr>
@endforelse