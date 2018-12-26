@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Branch sale Report <span></span></h1>
    </header>	
    <a class="btnAction print refresh bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="funExportData('PDF')">PDF</a>
    <a class="btnAction saveDoc bgOrange" href="#" onclick="funExportData('Excel')">Excel</a>
    <form id="pdfgenerator" action="{{ url('mis/pos_sales/export_view') }}" method="post">
        <div class="reportV1" id="print_content">
            <input type="hidden" name="view_details" id="view_details" value="{{ $enc_id }}">
            <input type="hidden" value="" id="excelorpdf" name="excelorpdf">
            <ul class="custRow">
                <li class="custCol-1 alignCenter">
                    <b>Date</b>
                    <?php echo date("d-m-Y", strtotime($sale_details_data->pos_date)); ?>
                </li>

                <li class="custCol-3 alignCenter">
                    <b>Shift</b>
                    {{$sale_details_data->jobshift_name}}
                </li>

                <li class="custCol-2 alignCenter">
                    <b>Branch</b>
                    {{$sale_details_data->branch_name}}
                </li>
                <li class="custCol-2 alignCenter">
                    <b>Opening Amount</b>
                    <span class="price">{{Customhelper::numberformatter((((int)$sale_details_data->opening_amount) ? $sale_details_data->opening_amount : 0))}}</span>
                </li>
                <li class="custCol-2 alignCenter">
                    <b>Total Sale</b>
                    {{Customhelper::numberformatter((((int)$sale_details_data->total_sale) ? $sale_details_data->total_sale : 0))}}
                </li>
            </ul>
            <ul class="custRow">
                <li class="custCol-2 alignCenter">
                    <b>Cash Collection</b>
                    <span class="price"> {{Customhelper::numberformatter((((int)$sale_details_data->cash_collection) ? $sale_details_data->cash_collection : 0))}}</span>
                </li>
                <li class="custCol-2 alignCenter">
                    <b>Credit Sale</b>
                    <span class="price"> {{Customhelper::numberformatter((((int)$sale_details_data->credit_sale) ? $sale_details_data->credit_sale : 0))}}</span>
                </li>

                <li class="custCol-2 alignCenter">
                    <b>Bank Sale</b>
                    <span class="price">{{Customhelper::numberformatter((((int)$sale_details_data->bank_sale) ? $sale_details_data->bank_sale : 0))}}</span>
                </li>

                <li class="custCol-2 alignCenter">
                    <b>Difference</b>
                    <span class="price">{{Customhelper::numberformatter((((int)$sale_details_data->difference) ? $sale_details_data->difference : 0))}}</span>
                </li>
                <li class="custCol-2 alignCenter">
                    <b>Meal Consumption</b>
                    <span class="price">{{Customhelper::numberformatter((((int)$sale_details_data->meal_consumption) ? $sale_details_data->meal_consumption : 0))}}</span>
                </li>
            </ul>
            <ul class="custRow">
                <li class="custCol-4">
                    <b>Supervisor Name</b>
                    {{$sale_details_data->employee_fname}} {{$sale_details_data->employee_aname}}
                </li>
                <li class="custCol-4">
                    <b>Cashier Name</b>
                    {{$sale_details_data->cashier_fname}} {{$sale_details_data->cashier_aname}}
                </li>
                <li class="custCol-4 ">
                    <b>Edited By</b>
                    {{$sale_details_data->editedby_name}} {{$sale_details_data->editedby_aname}}

                </li>
            </ul>

            <ul class="custRow">

                <li class="custCol-8 alignCenter">
                    <b>Reason</b>
                    {{$sale_details_data->reason}}
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
        $.post("{{ url('mis/pos_sales/export_view') }}", {excelorpdf: "PRINT", view_details: $("#view_details").val()}, function (data) {
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