@extends('layouts.main')
@section('content')
<?php
$edited_by = '';
if($details->edited_code != ''){
    $edited_by = $details->edited_code.' : '.$details->edited_fname;
}
?>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Collection Details<span></span></h1>
    </header>	
    <a class="btnAction print refresh bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>
    <form id="pdfgenerator" action="{{ url('branchsales/cash_collection/export_view') }}" method="post">
        <div class="reportV1" id="print_content">
            <input type="hidden" name="view_details" id="view_details" value="{{$details->id}}">
            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
            <ul class="custRow">
                <li class="custCol-1 alignCenter">
                    <b>Collection Status</b>
                    <?php if($details->collection_status==1){ echo "Collected";}else{ echo "Pending";?><?php }?>
                </li>

                <li class="custCol-3 alignCenter">
                    <b>Date</b>
                    <?php echo date("d-m-Y", strtotime($details->pos_date));?>
                </li>

                <li class="custCol-2 alignCenter">
                    <b>Shift</b>
                    {{$details->jobshift_name}}
                </li>
                <li class="custCol-2 alignCenter">
                    <b>Branch</b>
                    {{$details->branch_name}}
                </li>
                <li class="custCol-2 alignCenter">
                    <b>Opening Amount</b>
                    <span class="price">{{$details->opening_amount}}</span>
                </li>
            </ul>
            <ul class="custRow">
                <li class="custCol-2 alignCenter">
                    <b>Total Branch Sale</b>
                    <span class="price"> {{$details->total_sale}}</span>
                </li>
                <li class="custCol-2 alignCenter">
                    <b>Sale Amount</b>
                    <span class="price"> {{$details->total_sale-$details->tax_in_mis}}</span>
                </li>

                <li class="custCol-2 alignCenter">
                    <b>Tax Amount</b>
                    <span class="price">{{$details->tax_in_mis}}</span>
                </li>

                <li class="custCol-2 alignCenter">
                    <b>Total Cash Sale</b>
                    <span class="price">{{$details->cash_sale}}</span>
                </li>
            </ul>
            <ul class="custRow">
                <li class="custCol-2 alignCenter">
                    <b>Cash Collection</b>
                    <span class="price">{{$details->cash_collection}}</span>
                </li>
                <li class="custCol-4 alignCenter">
                    <b>Cash Difference</b>
                    <span class="price">{{$details->cash_sale-$details->cash_collection}}</span>
                </li>
                <li class="custCol-4 alignCenter">
                    <b>Total Bank Card Sale</b>
                    <span class="price">{{$details->bank_sale}}</span>
                </li>
                <li class="custCol-4 ">
                    <b>Bank Collection</b>
                    <span class="price">{{$details->bank_collection}}</span>
                </li>
            </ul>
            <ul class="custRow">
                <li class="custCol-4 alignCenter">
                    <b>Bank Difference</b>
                    <span class="price">{{$details->bank_sale-$details->bank_collection}}</span>
                </li>
                <li class="custCol-4 alignCenter">
                    <b>Credit/Free Sale</b>
                    <span class="price">{{$details->credit_sale}}</span>
                </li>
                <li class="custCol-2 alignCenter">
                    <b>Net Difference</b>
                    <span class="price">{{$details->difference}}</span>
                </li>
                <li class="custCol-2 alignCenter">
                    <b>Meal Consumption</b>
                    <span class="price">{{$details->meal_consumption}}</span>
                </li>
            </ul>
            <ul class="custRow">
                <li class="custCol-2 alignCenter">
                    <b>Cashier Name</b>
                    {{$details->cashier_code}} : {{$details->cashier_fname}}
                </li>

                <li class="custCol-2 alignCenter">
                    <b>Edited By</b>
                    <span class="price">{{$edited_by}}</span>
                </li>
            </ul>
            <ul class="custRow">

                <li class="custCol-8 alignCenter">
                    <b>Reason</b>
                    {{$details->reason}}
                </li>

            </ul>
        </div>
    </form>
</div>
<script>
    function funExportData(strType)
    {
        if (strType == "PDF") {
            $('#excelorpdf').val('PDF');
        } else {
            $('#excelorpdf').val('Excel');
        }

        document.getElementById("pdfgenerator").submit();
    }

    $('.print').click(function () {
        $.post("{{ url('branchsales/cash_collection/export_view') }}", {excelorpdf: "PRINT", view_details: $("#view_details").val()}, function (data) {
            try {
                var new_data = JSON.parse(data);
                win = window.open('', 'Print', 'height=' + screen.height, 'width=' + screen.width);
                win.document.write(new_data.data);
                win.document.close();
                win.print();
                win.close();
                return false;
            } catch (e) {
                toastr.error('Sorry There Was Some Problem!');
                return false;
            }
        });
    });
</script>
@endsection