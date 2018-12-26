<tr id="batchlist_{{$item_det->id}}">
    <td colspan="3">
        <table style="width: 100%; padding: 15px 0 15px 35px;" cellspacing="0" cellpadding="0">
            <thead class="clsbatchlisthead">
                <tr class="headingHolder">
                    <td>Batch Code</td>
                    <td class="amountAlign">Current Stock</td>
                    <td>Manufacture Date</td>
                    <td>Expiry Date</td>
                </tr>
            </thead>
            <tbody>
                <?php $totalStock=0;?>
                @forelse($batch_list as $row)
                <?php $totalStock+=$row->stock_remaining;?>
                <tr>
                    <td>{{ $row->batch_code }}</td>
                    <td class="amountAlign"><?php echo Customhelper::numberformatter($row->stock_remaining); ?> <span>{{$item_det->unitname}}</span></td>
                    <td><?php if($row->mfg_date){echo date('d-m-Y',  strtotime($row->mfg_date));}?></td>
                    <td><?php if($row->exp_date){echo date('d-m-Y',  strtotime($row->exp_date));}?></td>
                </tr>
                @empty
                <tr>
                    <td></td>
                    <td></td>
                    <td>No Data</td>
                    <td></td>
                </tr>
                @endforelse
                <tr class="clstrTotalseparator">
                    <td class="amountAlign"><strong>Total Stock :</strong></td>
                    <td class="amountAlign"><strong><?php echo Customhelper::numberformatter($totalStock); ?> <span>{{$item_det->unitname}}</span></strong></td>
                    <td></td>
                    <td></td>
                </tr>
                @if($totalStock!=0)
                    @foreach($altunits as $unit)
                        <?php $stockaltunit=$totalStock/$unit->conversion_value; ?>
                        <tr >
                            <td></td>
                            <td class="amountAlign"><strong><?php echo Customhelper::numberformatter($stockaltunit); ?> <span>{{$unit->altunitname}}</span></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </td>
</tr>