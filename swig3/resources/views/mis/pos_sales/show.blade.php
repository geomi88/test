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
                {{$sale_details_data->shift_name}}
            </li>
             <ul class="custRow">
                    <li class="custCol-6 alignCenter">
                        <b>Cash Collection</b>
                        {{Customhelper::numberformatter($sale_details_data->cash_collection)}}
                    </li>
                    
                </ul>
            <li class="custCol-4 alignCenter">
                <b>Total Sale</b>
                <span class="price">{{Customhelper::numberformatter($sale_details_data->total_sale)}}</span>
            </li>
            <li class="custCol-4 alignCenter">
                <b>Difference</b>
                <?php $difference=$sale_details_data->cash_collection-$sale_details_data->total_sale;?>
                <span class="price">{{Customhelper::numberformatter($difference)}}</span>
            </li>
        </ul>

        <ul class="custRow">
            <li class="custCol-7">
                <b>Supervisor Name</b>
                {{$sale_details_data->first_name}} {{$sale_details_data->alias_name}}
            </li>
            <li class="custCol-8">
                <b>Reason</b>
                {{$sale_details_data->reason}}
            </li>
            <li class="custCol-4 subChild">
               
            </li>
        </ul>
        <ul class="custRow">
            <li class="custCol-12">
                <b>Reason details</b>
                {{$sale_details_data->reason_details}}
            </li>
        </ul>
    </div>
</div>
@endsection