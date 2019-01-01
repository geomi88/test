<tr class="detailed_list_{{ $detailed_list[0]->stock_area_id }}">
    <td colspan="3" class="detailed_table">
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
                @forelse($detailed_list as $row)
                <tr>
                    <td>{{ $row->batch_code }}</td>
                    <td class="amountAlign"><?php echo Customhelper::numberformatter($row->stock_remaining / $conversion) . ' <span>' . $unit . '</span>'; ?></td>
                    <td>{{ $row->mfg_date }}</td>
                    <td>{{ $row->exp_date }}</td>
                </tr>
                @empty
                <tr>
                    <td></td>
                    <td></td>
                    <td>No record found</td>
                    <td></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </td>
</tr>