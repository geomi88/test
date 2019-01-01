@extends('layouts.main')
@section('content')

<div class="contentArea">

    <div class="innerContent">
        <header class="pageTitle">
        <h1>{{$loggedin_employee_details->name}} <span>Cash Collection</span></h1>
    </header>	
        <section class="contentHolderV1">
            <div class="empList type1">
                <figure class="imgHolder">
                    <img src="{{$loggedin_employee_details->profilepic}}" alt="Profile">
                </figure>
                <div class="details">
                    <b>{{$loggedin_employee_details->first_name}} {{$loggedin_employee_details->alias_name}}</b>
                    <p>Designation : <span>{{$loggedin_employee_details->name}}</span></p>
                </div>
                <div class="customClear"></div>
                <figure class="flagHolder">
                    <img src="{{ URL::asset('images/flags/'.$loggedin_employee_details->flag_name) }}" alt="Flag">
                    <figcaption>{{$loggedin_employee_details->country_name}}</figcaption>
                </figure>
            </div>

            <form action="{{ url('branchsales/cash_collection/add') }}" id="pos_date_search" method="post">
            <div class="custRow spacingBtm1 collectionDate">
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>From</label>
                        <input type="text" name="from_date" value="{{$from_date}}" id="from_date" readonly placeholder="From Date">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder bgSelect">
                        <label>To</label>
                        <input type="text" name="to_date" value="{{$to_date}}" id="to_date" readonly placeholder="To Date">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <input value="Search" class="commonBtn bgGreen addNext" type="submit">
                </div>
            </div>
            </form>

            <?php if($from_date!='' && $to_date!='') {?>
            <?php if(count($pos_sales)>0) { ?>
            <h4 class="blockHeadingV1">Collection List</h4>	
            <div class="listHolderType1 themeV1">
                
                <div class="listerType1 not_selected_pos"> 
                    <table style="width: 190%;" cellspacing="0" cellpadding="0">
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
                                <td class="alignCenter"><label class="listSelectAll"><input type="checkbox" >Select All</label></td>
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
                                <td  class="alignCenter">
                                    <input type="checkbox" class="alltSelected" name="selected_pos[]" value="{{$pos_sale->id}}" id="selected_pos[]">
                                </td>
                            </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>
            <a class="btnAction action bgGreen spacingBtm3 addposcollection" href="javascript:void(0)"> Add</a>
            <?php } else { ?>
            <h4 class="blockHeadingV1">No Result Found</h4>
            <?php  } }?>
            <form action="{{ url('branchsales/cash_collection/store') }}" method="post" id="add_cash_collection" class="selected_pos_section">
            

            <h4 class="blockHeadingV1">Collection List</h4>
            
            <div class="selected_pos">
            <div class="listHolderType1">
                

                <div class="listerType1"> 
                    <table style="width: 190%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Date</td>
                                <td>Branch Name</td>
                                <td>Shift</td>
                                <td>Collected Cash</td>
                                <td>Remove</td>
                            </tr>
                        </thead>

                    </table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>

            <!--<h4 class="blockHeadingV1 spacingBtm2 alignRight">Total : $250.00</h4>	-->
            </div>
            

            <h4 class="blockHeadingV1">Submitted To</h4>	
            <div class="cashPay">
                <div class="commonCheckHolder radioRender">
                    <label>
                        <input name="payment" id="cashierPay" value="TOP_CASHIER" checked type="radio">
                        <span></span>
                        <em>Top Cashier</em>
                    </label>
                </div>
                <div class="commonCheckHolder radioRender">
                    <label>
                        <input name="payment" id="bankPay" value="BANK" type="radio">
                        <span></span>
                        <em>Bank</em>
                    </label>
                </div>
                <div class="cashPayContent">
                    <div class="cashierPay">
                        <div class="">
                            <div class="custCol-4">
                                <div class="inputHolder">
                                    <label>Select Cashier</label>
                                    <select name="top_cashier_id" id="top_cashier_id">
                                        <option value=''>Select Cashier</option>
                                        @foreach ($cashieremployees as $cashieremployee)
                                        <option value="{{$cashieremployee->id}}">{{$cashieremployee->first_name}} {{$cashieremployee->alias_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="custCol-6">
                            <div class="empList topcashierdetails">
                                <figure class="imgHolder">
                                    <img src="images/imgProfile.jpg" alt="Profile">
                                </figure>
                                <div class="details">
                                    <b>Name 1</b>
                                    <p>Designation : <span>Designation</span></p>
                                    <p>Region : <span>--</span></p>
                                </div>
                                <div class="customClear"></div>
                                <figure class="flagHolder">
                                    <img src="images/imgFlagUsa.jpg" alt="Flag">
                                    <figcaption>USA</figcaption>
                                </figure>
                            </div>
                            </div>
                        </div>
                        <input value="Submit" class="commonBtn bgRed addBtn" type="submit">
                    </div>
                    <div class="bankPay">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="inputHolder">
                                    <label>Select Bank</label>
                                    <select name="bank_id" id="bank_id">
                                        <option value=''>Select Bank</option>
                                        @foreach ($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="custCol-4">
                                <div class="inputHolder">
                                    <label>Reference Number</label>
                                    <input type="text" name="ref_no" id="ref_no" placeholder="Enter Reference Number">
                                </div>
                            </div>
                        </div>
                        <input value="Submit" class="commonBtn bgRed addBtn" type="submit">
                    </div>
                </div>
            </div>
            </form>

        </section>
    </div>
</div>
<script>
    $(function () {        
        
        
        var v = jQuery("#pos_date_search").validate({
            rules: {
                from_date: {
                    required: true,
                },
                to_date: {
                    required: true,
                },
            },
            messages: {
                from_date: "Enter From Date",
                to_date: "Enter To Date",
                
            },
            // submitHandler: function() {  form.submit(); },  
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
        });
        
        var s = jQuery("#add_cash_collection").validate({
            rules: {
                top_cashier_id: {
                    required: true,
                },
                bank_id: {
                    required: true,
                },
                ref_no: {
                    required: true,
                },
                
            },
            messages: {
                top_cashier_id: "Select Cashier",
                bank_id: "Select Bank",
                ref_no: "Enter Reference Number",
            },
            // submitHandler: function() {  form.submit(); },  
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
        });
        
        
        $("#from_date").datepicker({
            dateFormat: 'dd-mm-yy',
            maxDate: '+0D',
            yearRange: '1950:c',
            changeMonth: true,
            changeYear: true,
            onSelect: function(selected) {
            $("#to_date").datepicker("option","minDate", selected)
            }
        });
        $("#to_date").datepicker({
            dateFormat: 'dd-mm-yy',
            maxDate: '+0D',
            yearRange: '1950:c',
            changeMonth: true,
            changeYear: true,
            onSelect: function(selected) {
            $("#from_date").datepicker("option","maxDate", selected)
            }
        });
        
        $('.addposcollection').on("click", function () {
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        var selected_pos =  $("input[name='selected_pos[]']:checked");
        var values = new Array();
        $.each($("input[name='selected_pos[]']:checked"), function() {
           values.push($(this).val());
        });
        selected_pos = JSON.stringify(values);
            $.ajax({
                type: 'POST',
                url: '../cash_collection/changecollectionstatus',
                data: {selected_pos:selected_pos,from_date: from_date ,to_date:to_date, status:1},
                async: false,
                cache: false,
                timeout: 30000,
                success: function (return_data) {
                    $('.selected_pos').html(return_data);
                    $('.selected_pos_section').show();
                }
            });
            $.ajax({
                type: 'POST',
                url: '../cash_collection/notcollectedpos',
                async: false,
                cache: false,
                timeout: 30000,
                data: {from_date: from_date ,to_date:to_date},
                success: function (return_data) {
                    $('.not_selected_pos').html(return_data);
                   
                }
            });
        
    });
    $('.selected_pos').on("click", "a.remove_pos_collected", function () {
         var from_date = $('#from_date').val();
         var to_date = $('#to_date').val();
         var selected_pos = $(this).attr("value");
         var values = new Array();
             values.push(selected_pos);
             selected_pos = JSON.stringify(values);
            $.ajax({
                type: 'POST',
                url: '../cash_collection/changecollectionstatus',
                async: false,
                cache: false,
                timeout: 30000,
                data: {selected_pos: selected_pos,from_date: from_date ,to_date:to_date, status:0},
                success: function (return_data) {
                    $('.selected_pos').html(return_data);
                }
            });
            $.ajax({
                type: 'POST',
                url: '../cash_collection/notcollectedpos',
                async: false,
                cache: false,
                timeout: 30000,
                data: {from_date: from_date ,to_date:to_date},
                success: function (return_data) {
                    $('.not_selected_pos').html(return_data);
                   
                }
            });
    });
    $('.topcashierdetails').hide();
    $('#top_cashier_id').on("change", function () {
        var top_cashier_id = $(this).val();
        if (top_cashier_id != '')
        {
            $.ajax({
                type: 'POST',
                url: '../cash_collection/getcashierdetails',
                data: 'top_cashier_id=' + top_cashier_id,
                success: function (return_data) {

                    $('.topcashierdetails').html(return_data);
                    $('.topcashierdetails').show();
                }
            });
        }
        else
        {
             $('.topcashierdetails').hide();
        }
    });
    
    $('.selected_pos_section').hide();
    });
    
    
</script>
@endsection
