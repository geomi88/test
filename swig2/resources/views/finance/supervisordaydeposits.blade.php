@extends('layouts.main')
@section('content')

<div class="contentArea">

    <div class="innerContent">
        <header class="pageTitle">
        <h1>{{$employee_details->name}} <span>Cash Deposit</span></h1>
    </header>	
        <section class="contentHolderV1">
            <div class="empList type1">
                <figure class="imgHolder">
                    <img src="{{$employee_details->profilepic}}" alt="Profile">
                </figure>
                <div class="details">
                    <b>{{$employee_details->first_name}} {{$employee_details->alias_name}}</b>
                    <p>Designation : <span>{{$employee_details->name}}</span></p>
                </div>
                <div class="customClear"></div>
                <figure class="flagHolder">
                    <img src="{{ URL::asset('images/flags/'.$employee_details->flag_name) }}" alt="Flag">
                    <figcaption>{{$employee_details->country_name}}</figcaption>
                </figure>
            </div>

            

            <?php if(count($pos_sales)>0) { ?>
            <h4 class="blockHeadingV1">Cash Deposit List</h4>	
            <div class="listHolderType1 themeV1">
                
                <div class="listerType1 not_selected_pos"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Date</td>
                                <td>Branch Name</td>
                                <td>Shift</td>
                                <td>Total Sale</td>
                                <td>Collected Cash</td>
                                <td>Credit Sale</td>
                                <td>Bank Sale</td>
                                <td>Difference</td>
                                <td>Staff Meal Consumption</td>
                                <td>Reason</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pos_sales as $pos_sale)
                            <tr>
                                <td><?php echo date("d-m-Y", strtotime($pos_sale->pos_date));?></td>
                                <td>{{$pos_sale->branch_name}}</td>
                                <td>{{$pos_sale->jobshift_name}}</td>
                                <td>{{$pos_sale->total_sale}}</td>
                                <td>{{$pos_sale->cash_collection}}</td>
                                <td>{{$pos_sale->credit_sale}}</td>
                                <td>{{$pos_sale->bank_sale}}</td>
                                <td>{{$pos_sale->difference}}</td>
                                <td>{{$pos_sale->meal_consumption}}</td>
                                <td>{{$pos_sale->reason}}</td>
                                
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>
            <?php } else { ?>
            <h4 class="blockHeadingV1">No Result Found</h4>
            <?php  } ?>
            

        </section>
    </div>
</div>

@endsection
