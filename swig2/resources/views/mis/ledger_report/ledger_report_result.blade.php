<?php  $n = $ledger_reports->perPage() * ($ledger_reports->currentPage() - 1); ?> 
    @foreach ($ledger_reports as $ledger_report)
    <?php  $n++; ?>
    <tr>
        <td class="led_slno">{{ $n }}</td>
        <td class="led_code">{{$ledger_report->ledger_code}}</td>
        <td class="led_name">{{$ledger_report->name}}</td>
        <td class="led_start"> <?php echo date("d-m-Y", strtotime($ledger_report->start_time)); ?></td>
        <td class="led_end"> <?php echo date("d-m-Y", strtotime($ledger_report->end_time)); ?></td>
        <td class="led_amount">{{Customhelper::numberformatter($ledger_report->amount)}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
                <a class="btnAction action bgGreen" href="{{ URL::to('mis/ledger_report/showdetails', ['id' => Crypt::encrypt($ledger_report->id)]) }}">View</a>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if($ledger_reports->lastPage() > 1){ ?>
    <tr class="paginationHolder"><th><div>   {!! $ledger_reports->render() !!}</div> </th></tr>
    <?php } ?>