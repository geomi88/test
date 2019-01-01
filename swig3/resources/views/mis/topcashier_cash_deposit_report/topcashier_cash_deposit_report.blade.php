@extends('layouts.main')
@section('content')

<div class="contentArea">
    <a class="btnAction action bgGreen" href="{{ URL::to('mis/topcashier_cash_deposit_report')}}">Back</a>
<div class="innerContent">

        <h4 class="blockHeadingV1">POS Sales Details</h4>
            
            <div class="selected_pos listHolderCont">
            <div class="listHolderType1 v2">
                

                <div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Date</td>
                                <td>Branch Name</td>
                                <td>Shift</td>
                                <td>Total Sale</td>
                                <td>Collected Cash</td>
                                <td>Credit sale</td>
                                <td>Bank Sale</td>
                                <td>Difference</td>
                                <td>Reason</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total_cash = 0;?>
                            @foreach ($sale_details_data as $sale_details)
                            <?php $total_cash = $total_cash+$sale_details->cash_collection;?>
                            <tr>
                                <td><?php echo date("d-m-Y", strtotime($sale_details->pos_date));?></td>
                                <td>{{$sale_details->branch_name}}</td>
                                <td>{{$sale_details->jobshift_name}}</td>
<!--                                <td>{{$sale_details->total_sale}}</td>
                                <td>{{$sale_details->cash_collection}}</td>
                                <td>{{$sale_details->credit_sale}}</td>
                                <td>{{$sale_details->bank_sale}}</td> 
                                -->
                                <td>{{Customhelper::numberformatter($sale_details->total_sale)}}</td>
                                <td>{{Customhelper::numberformatter($sale_details->cash_collection)}}</td>
                                <td>{{Customhelper::numberformatter($sale_details->credit_sale)}}</td>
                                <td>{{Customhelper::numberformatter($sale_details->bank_sale)}}</td>
                                <td>{{Customhelper::numberformatter($sale_details->difference)}}</td>
                                <td>{{$sale_details->reason}}</td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>

             </div>
    <h4 style="margin-top: 18px;" class="blockHeadingV1 spacingBtm2 alignRight">Total : <?php echo $total_cash ?></h4>	
           
</div>
</div>
@endsection