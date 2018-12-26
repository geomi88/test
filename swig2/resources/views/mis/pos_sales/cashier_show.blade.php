@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>POS Sale Report <span></span></h1>
    </header>	

    <div class="reportV1">
        <ul class="custRow">
            <li class="custCol-1 alignCenter">
                <b>Shift</b>
                {{$sale_details_data->jobshift_name}}
            </li>
                   <li class="custCol-3 alignCenter">
                        <b>Opening Amount</b>
                      <span class="price">{{$sale_details_data->opening_amount}}</span> 
                    </li>
                    
            <li class="custCol-3 alignCenter">
               <b>Cash Collection</b>
                        <span class="price"> {{$sale_details_data->cash_collection}}</span>
            </li>
            <li class="custCol-3 alignCenter">
                <b>Tips Collection</b>
                <span class="price">{{$sale_details_data->tips_collected}}</span>
            </li>
        </ul>

        <ul class="custRow">
            <li class="custCol-4">
                <b>Cashier Name</b>
                {{$sale_details_data->employee_fname}} {{$sale_details_data->employee_aname}}
            </li>
            <li class="custCol-4">
                <b>Supervisor Name</b>
                 {{$sale_details_data->supervisor_fname}} {{$sale_details_data->supervisor_aname}}
            </li>
            <li class="custCol-4">
              <b>Edited By</b>
                 {{$sale_details_data->editedby_name}} {{$sale_details_data->editedby_aname}}
             
            </li>
        </ul>
    </div>
</div>
@endsection