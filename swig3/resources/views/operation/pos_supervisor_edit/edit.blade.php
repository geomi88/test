@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1><span>POS Cash Sales</span></h1>
    </header>	
    

    <form method="post" id="posalesinsertion">
        
       
            
            <div class="fieldGroup" id="fieldSet1">
                
                <div class="listContainerV1">
                    <div class="empList">
                        <figure class="imgHolder">
<!--                            <img src="<?php echo $branch_details->profilepic; ?>" alt="">-->
                        </figure>
                        <div class="details">
                            <b>{{$branch_details->first_name}} {{$branch_details->alias_name}}</b>
                            <p>Designation : <span>Cashier</span></p>
                            <figure class="flagHolder">
                                <img src="{{ URL::asset('images/flags/'.$branch_details->flag_name) }}" alt="Flag">
                                <figcaption>{{$branch_details->country_name}}</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>
                    </div>
                    
                </div>

                <div class="boxWrapper">
                <div class="custRow">
                    <div class="custCol-4">
                        <input type="hidden" name="branch_id" value="{{$branch_details->branch_id}}">
                        <div class="inputHolder bgSelect">
                            <label>Branch Name</label>
                            <b>{{$branch_details->branch_name}}</b>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <input type="hidden" name="job_shift" value="{{$branch_details->job_shift_id}}">
                        <div class="inputHolder bgSelect">
                            <label>Shift Name</label>
                            <b>{{$branch_details->shift_name}}</b>
                            <span class="commonError"></span>
                        </div>
                    </div>
                     <div class="custCol-4">
                        <input type="hidden" name="job_shift" value="{{$branch_details->job_shift_id}}">
                        <div class="inputHolder bgSelect">
                            <label>For Date</label>
                            <b><?php echo $pos_date;?></b>
                            <span class="commonError"></span>
                        </div>
                    </div>
                     </div>
                </div>
                <div class="boxWrapper">
                    <div class="custRow">
                        <div class="custCol-4 clsfont">
                            <div class="inputHolder bgSelect">
                                <label>Opening Amount</label>
                                <input type="text" name="open_amount" id="open_amount" value="{{$branch_details->opening_fund}}" autocomplete="off" class="sales" placeholder="Enter Opening Amount">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="boxWrapper">
                    <div class="custRow">
                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Cash Submission</label>
                                <input type="text" name="cash_collection" id="cash_collection" autocomplete="off" class="sales" placeholder="Enter Cash Submission">
                                <span class="commonError"></span>
                            </div></div>

                    </div>
                </div>
                <div class="custRow">
                    <div class="custCol-6">
                        <div class="commonCheckHolder clsChkPadding">
                            <label >
                                <input id="chkAgree" class="selectUnits" type="checkbox">
                                <span></span><em>I agree the cash submission 100% correct & honest</em>
                            </label>
                        </div>
                    </div>
                </div>
                
       
        <div class="custRow">
            <div class="custCol-4">
                <input type="submit" value="Create" onclick="return funBeforeSubmission();" class="commonBtn bgGreen addBtn" name="submit">
            </div>
        </div>

</div>
</form>
</div>

<script>
    $(document).ready(function ()
    {
        /*var employee_type = $('#employee_type').val();
         if (employee_type == 'Cashier')
         {
         $('.pos_total_sale').hide();
         $('.pos_difference').hide();
         $('.pos_reason').hide();
         $('.pos_cashier_code').hide();
         $('.pos_credit_sale').hide();
         $('.pos_bank_sale').hide();
         $('.pos_date').hide();
         $('.supervisor_branches').hide();
         }
         if (employee_type == 'Supervisor')
         {
         $('.pos_supervisor_code').hide();
         }*/
        $("#pos_date").datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: new Date(1950, 10 - 1, 25),
            maxDate: '+0D',
            yearRange: '1950:c',
            changeMonth: true,
            changeYear: true
        });
        $(".sales").keyup(function () {
            $("#diff").css({"color":"black"});
            var cash_collection = parseInt($('#cash_collection').val());
            var total_sale = parseInt($('#total_sale').val());
            var credit_sale = parseInt($('#credit_sale').val());
            var bank_sale = parseInt($('#bank_sale').val());
            if (isNaN(total_sale)) total_sale= 0;
            if (isNaN(bank_sale)) bank_sale= 0;
            if (isNaN(credit_sale)) credit_sale= 0;
             if (isNaN(cash_collection)) cash_collection= 0;
               var diff = parseInt(cash_collection + credit_sale + bank_sale) - parseInt(total_sale);

                $('#diff').val(diff);
                if(diff<0)
                {
                $("#diff").css({"color":"red"});
                }
        });
        
        

        $(".pos_opening_amount_ajax").change(function () {

            var branch_id = $('#branch_id').val();
            var job_shift = $('#job_shift').val();
            var pos_date = $('#pos_date').val();
            if (branch_id && job_shift && pos_date != '')
            {
                $.ajax({
                    type: 'POST',
                    url: '../pos_sales/getopenamount',
                    data: 'branch_id=' + branch_id + '&job_shift=' + job_shift + '&pos_date=' + pos_date,
                    success: function (return_data) {

                        $('#open_amount').val(return_data.opening_amount); 
                        $('#cashier_amount').val(return_data.cash_collected);
                        if(return_data.opening_fund_editable== 1){
                            
                            $("#open_amount").prop("readonly", true);
                        }
                    }
                });
            }
            else
            {

            }


        });
        $("#branch_id").change(function () {

            var branch_id = $('#branch_id').val();
            if (branch_id)
            {
                $.ajax({
                    type: 'POST',
                    url: '../pos_sales/branch_shifts',
                    data: 'branch_id=' + branch_id,
                    success: function (return_data) {

                        $('.pos_branch_shifts').html(return_data);
                    }
                });
            }
            else
            {
                $('.pos_branch_shifts').html('');
            }


        });
        $("#job_shift").change(function () {
            var branch_id = $('#branch_id').val();
            var shift_id = $('#job_shift').val();
            if (shift_id)
            {
                $.ajax({
                    type: 'POST',
                    url: '../pos_sales/shift_cashier',
                    data: 'shift_id=' + shift_id + '&branch_id=' + branch_id,
                    success: function (return_data) {

                        $('.cashierdetails').html(return_data);
                        $('.cashierdetails').show();
                    }
                });
            }
            else
            {
                $('.cashierdetails').hide();
            }


        });
        $("#posalesinsertion").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                branch_id:
                        {
                            required: true,
                        },
                job_shift:
                        {
                            required: true,
                        },
                reason_details:
                        {
                            required: true,
                        },
                total_sale:
                        {
                            required: true,
                            number: true
                        },
                cash_collection:
                        {
                            required: true,
                            number: true
                        },
                pos_reason:
                        { 
                            required: function(element) {
                                return $('#diff').val() != '0'; } 
                        },
                employee_id:
                        {
                            required: true,
                        },
                open_amount:
                        {
                            required: true,
                            number: true
                        },
                pos_date:
                        {
                            required: true,
                            //date: true
                        },
            },
//            submitHandler: function () {
//                form.submit();
//            },
            messages: {
                branch_id: "Select Branch",
                job_shift: "Select Job Shift",
                reason_details: "Provide Reason Details",
                total_sale: {
                    required: "Enter Total Branch Sale",
                    number: "Enter number only"
                },
                cash_collection: {
                    required: "Enter Cashier Collection",
                    number: "Enter number only"
                },                
                pos_reason: "Select Reason",
                employee_id: "Select Supervisor",
                open_amount: {
                    required: "Enter Opening Amount",
                    number: "Enter number only"
                },
                pos_date: {
                    required: "Enter Date",
                    //date: "Accept Only Date"
                },
            },
        });

    });

function funBeforeSubmission(){
    if($("#chkAgree").prop('checked') == false){
        alert("Please select the checkbox");
        return false;
    }
    
    return confirm('Are you sure submit?');
}

</script>
@endsection
