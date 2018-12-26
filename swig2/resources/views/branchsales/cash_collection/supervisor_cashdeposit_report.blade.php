@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Cash Deposit <span>Report</span></h1>
    </header>	
    
    <div class="listHolderType1 themeV1">
        
        
        <div class="listerType1 not_selected_pos"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>No</td>
                                <td>Date</td>
                                <td>Deposited To</td>
                                <td>Top Cashier Name</td>
                                <td>Bank Name</td>
                                <td>Reference Number</td>
                                <td>Amount</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $n = $cash_collections->perPage() * ($cash_collections->currentPage()-1); ?>
                            @foreach ($cash_collections as $cash_collection)
                            <tr>
                                <td>{{$n}}</td>
                                <td><?php echo date("d-m-Y", strtotime($cash_collection->created_at));?></td>
                                <td>{{$cash_collection->submitted_to}}</td>
                                <td>{{$cash_collection->employee_fname}} {{$cash_collection->employee_aname}}</td>
                                <td>{{$cash_collection->bank_name}}</td>
                                <td>{{$cash_collection->ref_no}}</td>
                                <td>{{$cash_collection->amount}}</td>
                                
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    <div class="commonLoaderV1"></div>
        </div>	

        </div>

    <?php echo $cash_collections->render(); ?> 
</div>
@endsection