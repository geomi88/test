@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Customer Detail</h1>
    </header>	
    <a class="btnAction print refresh bgGreen" href="#">Print</a>
    <a class="btnBack" href="{{ url('crm/all_customers') }}">Back</a>
    <body>
        <table cellpadding="0" cellspacing="0" border="0"  style=" table-layout: fixed;  
         width:100%; color:#454446; font-size:16px; line-height:22px;
             background: #fff; padding: 32px; font-family:'open_sansregular'; font-size: 12px;  
             padding: 8px;">
             <tbody>
                <tr>
                    <td cellpadding="3" cellspacing="3">
                        <table  cellpadding="3" width="100%" height="100%"  cellspacing="0" style="font-size:15px; border: 4px solid #e7e7e7;">
                            <tr>
                                <td style="border-right: 1px solid #e7e7e7; text-align: center;; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7; width: 25%;">
                                    <p style="font-weight: bold ; margin: 0">Time & Date </p>
                                    <p style="margin: 0;"><?php echo date("d-m-Y H:i", strtotime($customer_data->created_at)); ?></p>
                                </td>
                                <td style="border-right: 1px solid #e7e7e7; text-align: center;%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;  width: 25%;">
                                    <p style="font-weight: bold ; margin: 0"> Customer Name</p>
                                    <p style="margin: 0;">{{ $customer_data->cus_name }}</p>
                                </td>
                                <td style="border-right: 1px solid #e7e7e7; text-align: center;%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;  width: 25%;">
                                    <p style="font-weight: bold ; margin: 0">Mobile Number</p>
                                    <p style="margin: 0;">{{ $customer_data->mobile_number }}</p>
                                </td>
                                <td style="border-right: 1px solid #e7e7e7; text-align: center;%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;">
                                    <p style="font-weight: bold; margin: 0"> Repeat</p>
                                    <p style="margin: 0; ">{{ $customer_data->repeat_count }}</p>
                                </td>
                                
                            </tr>
                            <tr>
                                <td style="border-right: 1px solid #e7e7e7; text-align: center;%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;  width: 25%;">
                                    <p style="font-weight: bold ; margin: 0">Branch </p>
                                    <p style="margin: 0; ">{{ $customer_data->branch_name }}</p>
                                </td>
                                <td colspan="2" style="border-right: 1px solid #e7e7e7; text-align: center; width: 25%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;">
                                    <p style="font-weight: bold; margin: 0"> Created By </p>
                                    <p style="margin: 0; ">{{ $customer_data->cashier_name }}</p>
                                </td>
                                <td colspan="2" style="border-right: 1px solid #e7e7e7; text-align: center; width: 25%; vertical-align: top;padding: 8px 0 8px; border-bottom: 1px solid #e7e7e7;">
                                    <p style="font-weight: bold; margin: 0"></p>
                                    <p style="margin: 0; "></p>
                                </td>                            
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="commonLoaderV1"></div>
        </div>
<script>
     $(document).on("click",".print",function(){
        $(".commonLoaderV1").show();
        $.post("{{ url('crm/all_customers/print_view') }}",{mobile:"{{ $mobile }}",branch_id:"{{ $branch_id }}",created:"{{ $created }}"},function(data){
            
        }).done(function(data){
            try{
                win = window.open("","print","width=screen.width,height=screen.height");
                win.document.write(data);
                $(".commonLoaderV1").hide();
                win.document.close();
                win.print();
                win.close();
            }
            catch(e){
                $(".commonLoaderV1").hide();
                toastr.error("Something went wrong");
            }
        });
    })
</script>
@endsection