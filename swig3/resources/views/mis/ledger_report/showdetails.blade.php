@extends('layouts.main')
@section('content')

<div class="contentArea">
    <a class="btnAction action bgGreen" href="{{ URL::to('mis/ledger_report')}}">Back</a>
<div class="innerContent">

        <h4 class="blockHeadingV1">Requsition Details</h4>
            <div class="reportV1">
                <ul class="custRow">

                    <li class="custCol-6">
                         Ledger Name : 
                         <span > {{$ledger_details->ledger_name}}</span>
                     </li>

                    <li class="custCol-4">
                        Remaining Amount : 
                        <span class="price">{{Customhelper::numberformatter($ledger_details->remaing_amount)}}</span>
                    </li>
                </ul>
            </div>
        
            <!--<div class="selected_pos">-->
            <div class="listHolderType1">
                

                <div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Date</td>
                                <td>Requisition Title</td>
                                <td>Branch</td>
                                <td>Amount</td>
                                <td>Request By</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requisition_details as $requisition_detail)
                            <tr>
                                <td><?php echo date("d-m-Y", strtotime($requisition_detail->created_at));?></td>
                                <td>{{$requisition_detail->title}}</td>
                                <td>{{$requisition_detail->branch_name}}</td>
                                <td>{{Customhelper::numberformatter($requisition_detail->amount)}}</td>
                                <td>{{$requisition_detail->requested_by}}</td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>

            <!--</div>-->
    
</div>
</div>
@endsection