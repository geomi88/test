@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1><span>POS Cash Sales</span></h1>
    </header>	
    

    <form  action ="{{ url('operation/pos_cashier_edit/save')}}"method="post" id="posalesedit">
        
       
            
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
                        <input type="hidden" name="pos_date" id="pos_date" value="<?php echo $pos_date;?>">
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
                                <input type="text" name="open_amount" id="open_amount" <?php if($branch_details->opening_fund_editable==1){ ?> readonly <?php } ?> value="{{$branch_details->opening_fund}}" autocomplete="off" class="sales" placeholder="Enter Opening Amount">
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
                                
                                <input type="text" name="cash_collection" id="cash_collection" value="<?php echo $cash_collection;?>" autocomplete="off" class="sales" placeholder="Enter Cash Submission" onkeyup="getTaxValue()">
                                <span class="commonError"></span>
                            </div></div>
                        
<!--                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Sales Amount</label>
                                <input type="text" name="sales_amount" id="sales_amount" autocomplete="off" class="sales" value="<?php echo ($cash_collection-$tax_amount);?>" readonly="true">
                                <span class="commonError"></span>
                            </div>
                        </div>

                  
                    
                    
                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Tax Amount</label>
                                <input type="text" name="tax_amount" id="tax_amount" autocomplete="off" value="<?php echo $tax_amount;?>" class="sales" readonly="true">
                                <span class="commonError"></span>
                            </div>
                        </div>-->

                    </div>
                    
                     <div class="custRow">
<!--                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Bank Card Submission</label>
                                <input type="text" name="bank_card_submission" id="bank_card_submission" autocomplete="off" value="{{$bankCard}}"  placeholder="Enter Bank Card Submission" >
                                <span class="commonError"></span>
                            </div>
                        </div>
-->



                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Bank Card Collection</label>
                                <input type="text" name="bank_card_collection" id="bank_card_collection" autocomplete="off"  value="{{$bankCollection}}"  placeholder="Enter Bank Card Collection">
                                <span class="commonError"></span>
                            </div>
                        </div>




                        <!--                        <div class="custCol-4 clsfont">
                                                    <div class="inputHolder">
                                                        <label>Tax Amount</label>
                                                        <input type="text" name="tax_amount" id="tax_amount" autocomplete="off" class="sales" readonly="true">
                                                        <span class="commonError"></span>
                                                    </div>
                                                </div>-->

                    </div>
                    
                    <div class="custRow">
                        <div class="custCol-6 ">
                            <div class="commonCheckHolder clsChkPadding">
                                <label >
                                    <input id="bank_slips" class="bank_slips" type="checkbox">
                                    <span></span><em>Not collected Bank slips</em>
                                </label>
                            </div>
                        </div>

                    </div>
                    
                </div>
                
                
                <div class="boxWrapper">
                    <div class="custRow">
                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Tips Collected</label>
                                
                                <input type="text" name="tips_collection" id="tips_collection" value="<?php echo $tip_collection;?>" autocomplete="off" class="sales" placeholder="Enter Tip Submission">
                                <span class="commonError"></span>
                            </div></div>

                    </div>
                    <div class="custRow">
                        <div class="custCol-6 ">
                            <div class="commonCheckHolder clsChkPadding">
                                <label >
                                    <input id="no_tips" class="no_tips" type="checkbox">
                                    <span></span><em >No Tips Collected Today</em>
                                </label>
                            </div>
                        </div>

                    </div>
                    
                </div>
                <div class="custRow">
                    <div class="custCol-6">
                        <div class="commonCheckHolder clsChkPadding">
                            <label >
                                <input id="chkAgree" class="selectUnits" type="checkbox">
                                <span></span><em id="i_agree">I agree the cash submission 100% correct & honest</em>
                            </label>
                        </div>
                    </div>
                </div>
                
       <input type="hidden" id="parent_id" name="parent_id" value="<?php echo $parent_id;?>">
         <input type="hidden" id="cashier_id" name="cashier_id" value="{{$branch_details->emp_id}}">
        
       <input type="hidden" id="employee_id" name="employee_id" value="{{$supervisor}}">
      
         <input type="hidden" value="<?php echo $pos_date;?>" name="currentdate" id="currentdate">
           
         
         
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
        $("#posalesedit").validate({
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
//                tips_collection:
//                        {
//                           
//                            number: true
//                        },
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
                        
//                bank_card_submission: {
//                     number: true,
//                    required: {
//                                depends: function () {
//                                     if($('.bank_slips').is(':unchecked')) {
//                                     return true;
//                                    }
//                                }
//                            },        
//                        },
//                        
                 bank_card_collection: {
                     number: true,
                    required: {
                                depends: function () {
                                   if($('.bank_slips').is(':unchecked')) {
                                     return true;
                                    }
                                }
                            },        
                        }, 
                 tips_collected: {
                     number: true,
                    required: {
                                depends: function () {
                                   if($('.no_tips').is(':unchecked')) {
                                     return true;
                                    }
                                }
                            },        
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
        
        $("#i_agree").css({"color": "red"});
      //  alert("Please select the checkbox");
        return false;
    }
    
    return confirm('Are you sure submit?');
}

function getTaxValue(){
        
        
          

            var cash_collection = $('#cash_collection').val();  
            var employee_type = $('#employee_type').val();
            
            var pos_date=$('#currentdate').val();
            
            
            if (cash_collection != '' && cash_collection != 0)
            {
                $.ajax({
                    type: 'POST',
                  //  url: '../pos_cashier_edit/gettaxamount',
                   url: '../../helper/helper/gettaxamount',
                   data: 'cash_collection=' + cash_collection +'&pos_date='+ pos_date ,
                    success: function (return_data) {

                        $('#tax_amount').val(return_data.tax_amount); 
                        $('#sales_amount').val(return_data.sales_amount);
                      
                    }
                });
            }else{
                       $('#tax_amount').val(""); 
                       $('#sales_amount').val("");
            }
      
    }
    
    
    $('#bank_slips').click(function () {
        if ($(this).prop('checked')) {
            
            $('#bank_card_collection').val("0");
           
            $('#bank_card_collection').prop('disabled', true);


        }
        else {
          
            $('#bank_card_collection').prop('disabled', false);

        }
    });

$('#no_tips').click(function () {
    
   
        if ($(this).prop('checked')) {
            $('#tips_collection').val("0");
            $('#tips_collection').prop('disabled', true);


        }
        else {
             $('#tips_collection').val("");
           $('#tips_collection').prop('disabled', false);

        }
    });


</script>
@endsection
