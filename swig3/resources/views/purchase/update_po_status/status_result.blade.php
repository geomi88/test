@forelse($order_history as $row)
<tr >
    <td>{{ $row->po_status }}</td>
    <td>{{ $row->updated_date }}</td>
    <td>{{ $row->name }}</td>

</tr>
@empty
<tr>
    <td>No Data</td>
</tr>
@endforelse