@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1><span>Supervisor Collection</span></h1>
    </header>	
    

    <form  action="{{ url('operation/pos_supervisor_edit/save')}}" method="post" id="frmpossaleupdate">
        
       
        
            <div class="fieldGroup" id="fieldSet1">
               
                <div class="listContainerV1">
                    <div class="empList">
                        <figure class="imgHolder">
                            <img src="<?php echo $branch_details->profilepic; ?>" alt="">
                        </figure>
                        <div class="details">
                            <b>{{$branch_details->first_name}} {{$branch_details->alias_name}}</b>
                            <p>Designation : <span>Supervisor</span></p>
                            <figure class="flagHolder">
                                <img src="{{ URL::asset('images/flags/'.$branch_details->flag_name) }}" alt="Flag">
                                <figcaption>{{$branch_details->country_name}}</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>
                    </div>
                    <div class="empList cashierdetails">


                    </div>

                </div>

                <div class="boxWrapper">
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="inputHolder bgSelect supervisor_branches">
                            <input type="hidden" name="branch_id" value="{{$branch_details->branch_id}}">
                            <div class="inputHolder bgSelect">
                            <label>Branch Name</label>
                            <b>{{$branch_details->branch_name}}</b>
                            <span class="commonError"></span>
                            </div>
                            <em class="selectedCompany"></em>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4">
                        <div class="inputHolder bgSelect">
                        <input type="hidden" name="job_shift" id="job_shift" value="{{$shift_details->id}}">
                           
                            <label>Shift</label>
                         <b>{{$shift_details->name}}</b>   
                            <em class="shifttimings"></em>
                            <span class="commonError"></span>
                        </div>
                    </div>
                    <div class="custCol-4 pos_date">
                        <div class="inputHolder fieldMargin">
                            <label>Date </label>
                            <input type="text" name="pos_date" id="pos_date" title="تاريخ" readonly="readonly" value="<?php  echo $pos_date ;?>" class="pos_opening_amount_ajax" placeholder="Select Date">
                         </div>
                    </div>
                   <!-- <div class="custCol-3">
                        <div class="inputHolder fieldMargin">
                            <label>Time </label>
                            <input type="text"  value="{{date('h:i a')}}" readonly="readonly" >
                        </div>
                    </div>-->
                   
                </div>
                </div>
                   <div class="boxWrapper">
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="inputHolder bgSelect">
                            <label>Opening Amount</label>
                            <input type="text" name="open_amount"  readonly="true" onpaste="return false;"  id="open_amount" class="numberwithdot"  value="{{$branch_details->opening_fund}}">
                            <span class="commonError"></span>
                        </div>
                    </div>
                     <div class="custCol-4">
                        <div class="inputHolder bgSelect">
                            <label>Cashier Collection</label>
                            <input type="text" readonly="true" id="cashier_amount" onpaste="return false;" class="numberwithdot" name="cashier_amount" value="{{$cashier_collection}}">
                            <span class="commonError"></span>
                        </div>
                    </div>
                   
                   <div class="custCol-4">
                        <div class="inputHolder bgSelect">
                            <label>Tips Collection</label>
                            <input type="text" readonly="true" id="tips_amount" onpaste="return false;" class="numberwithdot" name="tips_amount" value="{{$tips_collected}}">
                            <span class="commonError"></span>
                        </div>
                    </div>
                </div>
                   </div>

              <div class="boxWrapper">
                <div class="custRow">

                    <div class="custCol-4 pos_total_sale">
                        <div class="inputHolder">
                            <label>Total Branch Sale </label>
                            <input type="text" name="total_sale" id="total_sale" onpaste="return false;" title="إجمالي بيع الفرع" value="<?php echo ($total_sale);?>" autocomplete="off" class="sales numberwithdot" placeholder="Enter Total Branch Sale">
                            <span class="commonError"></span>
                        </div>
                    </div>
                    
                    <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Sales Amount</label>
                                <?php $salesAmount=$total_sale-$tax_amount;?>
                                <input type="text" name="sales_amount" id="sales_amount" onpaste="return false;" autocomplete="off" value="{{Customhelper::numberformatter($salesAmount)}}" class="sales numberwithdot" readonly="true">
                                <span class="commonError"></span>
                            </div></div>

                  
                    
                    
                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Tax Amount</label>
                                <input type="text" name="tax_amount" id="tax_amount" autocomplete="off" onpaste="return false;" class="sales numberwithdot"  value="{{Customhelper::numberformatter($tax_amount)}}" readonly="true">
                                <span class="commonError"></span>
                            </div></div> 
                    
                    
               
                   
                       <div class="custCol-4 ">
                        <div class="inputHolder">
                            <label></label>
                           </div>
                    </div>
                        
                        
                        <div class="custCol-4">
                        <div class="inputHolder">
                            <label></label>
                            </div>
                    </div>
                        
                    <div class="custCol-4 clsfont">
                        <div class="inputHolder">
                            <label>Tax in POS</label>
                            <input type="text" name="tax_in_pos" id="tax_in_pos" onpaste="return false;" autocomplete="off" class="sales numberwithdot" placeholder="Enter Tax in POS" value="{{$tax_in_pos}}">
                            <span class="commonError"></span>
                        </div>
                    </div>
                </div>
                </div>

                
                 <div class="boxWrapper">
                                <h3 style="color:#f91137;font-weight:bold;">Cash Count</h3>
                                <div class="custRow cashCountHeading">
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                            <label>Sales</label>
                                        </div>
                                    </div>
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                            <label>Collection</label>
                                        </div>
                                    </div>
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                            <label>Difference</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="custRow cashEntry">
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                            <label>Cash Sales</label>
                                            <input type="text" name="cash_sale" id="cash_sale" onpaste="return false;"  value="{{$cash_sale}}" title="مجموعة كاشير" autocomplete="off" class="sales cash_difference totdiff numberwithdot" placeholder="Enter Cash Sale">
                                        </div>
                                    </div>
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                            <label>Cash Collection</label>
                                            <input type="text" name="cash_collection" id="cash_collection" onpaste="return false;" value="{{$cash_collection}}"  title="مجموعة كاشير" autocomplete="off" class="sales cash_difference numberwithdot" placeholder="Enter Cashier Collection">
                                        </div>
                                    </div>
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                   
                                            
                                            
                                            <label>Excess/Shortage</label>
                                            <?php  
                                            if($cash_sale==""){
                                                $cash_sale=0;
                                            }if($cash_collection=="") {
                                                $cash_collection=0;
                                            }
                                            
                                           // $cash_diff=$cash_sale-$cash_collection;
                                           $cash_diff=$cash_collection-$cash_sale;
                                             
                                            ?>
                                           <input type="text" name="cash_diff" id="cash_diff"  value="{{$cash_diff}}"  readonly placeholder="Difference Amount">
                                        
                                        </div>
                                    </div>
                                </div>

                                

                                <div class="custRow cashEntry">
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                            <label>Bank Card Sale (بيع بطاقة مصرفية)</label>
                                            <input type="text" name="bank_sale"  value="{{$bank_sale}}" onpaste="return false;"  id="bank_sale" title="بيع بطاقة مصرفية" autocomplete="off" class="sales bank_difference totdiff numberwithdot" placeholder="Enter Bank Card Sale">
                                            </div>
                                    </div>
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                            <label>Bank Collection</label>
                                            <input type="text" name="bank_collection" id="bank_collection" onpaste="return false;" value="{{$bank_collection}}"  autocomplete="off" class="sales bank_difference numberwithdot" placeholder="Enter Bank Collection">
                                           
                                        </div>
                                    </div>
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                            
                                            
                                           <?php   if($bank_sale==""){
                                                $bank_sale=0;
                                            }if($bank_collection=="") {
                                                $bank_collection=0;
                                            }
                                            
                                        //    $bank_diff=$bank_sale-$bank_collection; 
                                            $bank_diff=$bank_collection-$bank_sale; 
                                            
                                            ?>
                                            <label>Excess/Shortage</label>
                                             <input type="text" name="bank_diff" id="bank_diff" value="{{$bank_diff}}"  readonly placeholder="Difference Amount">
                                          </div>
                                    </div>
                                </div>
                               
                                <div class="custRow cashEntry">
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                           <label>Credit/Free Sale (الائتمان / بيع مجانا)</label>
                                            <input type="text" name="credit_sale" id="credit_sale"  value="{{$credit_sale}}" title="الائتمان / بيع مجانا" autocomplete="off" onpaste="return false;" class="sales totdiff numberwithdot" placeholder="Enter Credit/Free Sale">
                                         </div>
                                    </div>
                                    
                                   <div class="custCol-4">
                                        <div class="inputHolder">
                                            </div>
                                    </div>
                                    
                                    <?php  $tot_branch_sale= $cash_sale+$bank_sale+$credit_sale; ?>
                                    <div class="custCol-4">
                                        <div class="inputHolder">
                                            <label>Total Branch Sale</label>
                                            <input type="text" name="tot_branch_sale" id="tot_branch_sale" onpaste="return false;" readonly value="{{$tot_branch_sale}}"   autocomplete="off" class="sales bank_difference numberwithdot" placeholder="Enter Bank Collection">
                                           
                                        </div>
                                    </div> 
                                    
                                    
                                </div>
                                
                                
                            </div>
                
                
                
                <div class="boxWrapper">
                <div class="custRow">
                    <div class="custCol-4 pos_difference">
                        <div class="inputHolder">
                            <label>Difference Amount </label>
                            <input type="text" name="diff" id="diff" title="الفرق المبلغ"  value="{{$difference}}" readonly onpaste="return false;" placeholder="Difference Amount">
                        </div>
                    </div>

                </div>

                <div class="custRow">
                    <div class="custCol-4 pos_reason">
                        <div class="inputHolder">
                            <label>Reason </label>
 <select name="pos_reason" title="السبب" id="pos_reason">
                                <option selected value=''>Select Reason</option>
                                @foreach ($pos_reasons as $pos_reason)
                                <option value='{{ $pos_reason->id }}'>{{ $pos_reason->name}}</option>
                               <option <?php echo ($pos_reason->id == $reason_id) ? "selected" : "" ?> value='{{ $pos_reason->id }}'><?php echo str_replace('_', ' ', $pos_reason->name); ?></option>
                               <option <?php if($reason_id == "" && $branch_details->reason_details==""){  echo ($pos_reason->id == $reason_id) ? "selected" : "" ;} ?> value='{{ $pos_reason->id }}'><?php echo str_replace('_', ' ', $pos_reason->name); ?></option>
                                    
                                
                                @endforeach
                                <option <?php echo ( $branch_details->reason_details != "" && $branch_details->reason_id=="") ? "selected" : ""  ?> value='other'>Other</option>
                            </select>
                            <span class="commonError"></span>
                        </div>
                    </div>
 <input type="hidden" id="parent_id" name="parent_id" value="<?php echo $parent_id;?>">
         <input type="hidden" id="employee_id" name="employee_id" value="{{$branch_details->emp_id}}">
      <input type="hidden" id="cashier_id" name="cashier_id" value="{{$branch_details->cashier_id}}">
      
         <input type="hidden" id="details_reason" name="details_reason" value="{{$branch_details->reason_details}}">
                 <div class="custCol-4">
                        <div class="inputHolder reason_details">
                            <label>Reason Details</label>
                            <textarea name="reason_details" id="reason_details"  title="تفاصيل السبب"></textarea>
                            <span class="commonError"></span>
                        </div>
                    </div>
                </div>
                </div>    
                
                  <div class="boxWrapper mealsClass">
                <div class="custRow ">
                    <div class="custCol-4">
                        <div class="inputHolder ">
                            <label>Staff Meal Consumption</label>
                            <input type="text" name="meals" id="meals"  placeholder="Staff Meal Consumption" value="{{$branch_details->meal_consumption}}">
                        </div>
                    </div>

                </div>

               </div>
                
        <div class="custRow">
            <div class="custCol-12">
                <div class="commonCheckHolder clsChkPadding">
                    <label >
                        <input id="chkAgree" type="checkbox">
                        <span></span><em id="i_agree">I agree this cash  submission is 100% honest as per Saudi Arabian rules and regulations of GAZT and VAT.</em>
                    </label>
                </div>
            </div>
        </div>
        <div class="custRow">
            <div class="custCol-4">
                <input type="button" value="Update" id="btnSavePosSales" class="commonBtn bgGreen addBtn" >
            </div>
        </div>
     
            
            </div>
    </form>
</div>

<script>
    $(document).ready(function ()
    {

     
     var reason=$("#details_reason").val();
    
     if(reason==""){
         
     }else{
       $('.reason_details').show();
       $('#reason_details').text(reason);
     }
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
        $("#frmpossaleupdate").validate({
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
                cash_sale:
                        {
                            required: true,
                        }, 
                bank_sale:
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
               meals:
                        {
                           
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
                           
                        },
                pos_date:
                        {
                            required: true,
                            //date: true
                        },
            },
            
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
        
        $("#btnSavePosSales").click(function () {
            if (!$("#frmpossaleupdate").valid()) {
                return false;
            }
            
            if ($("#chkAgree").prop('checked') == false) {
                $("#i_agree").css({"color": "red"});
                return false;
            }
            
            if (parseFloat($('#total_sale').val()) != parseFloat($('#tot_branch_sale').val())) {
                alert("Total branch sales doesn't matches the amount you have entered");
                return false;
            }

            var cash_sale=0;
            if($('#cash_sale').val()!=''){
                cash_sale=parseFloat($('#cash_sale').val());
            }

            var bank_sale=0;
            if($('#bank_sale').val()!=''){
                bank_sale=parseFloat($('#bank_sale').val());
            }

            var credit_sale=0;
            if($('#credit_sale').val()!=''){
                credit_sale=parseFloat($('#credit_sale').val());
            }

            var totalsale=(parseFloat(cash_sale+bank_sale+credit_sale));

            if (totalsale!= parseFloat($('#tot_branch_sale').val())) {
                alert("Total branch sales doesn't matches the amount you have entered");
                return false;
            }
            
            var blnConfirm = confirm("Are you sure to submit?");
            if(!blnConfirm){
               return false; 
            }
            
           $("#frmpossaleupdate").submit();
           
        });
        
    });

    $('#total_sale').bind('keyup', function () {
        getSupervisorTaxValue();
    });
    
    
     function getSupervisorTaxValue(){
        
        
          

            var cash_collection = $('#total_sale').val();
            
           
            var pos_date=$('#pos_date').val();
            
          
            if (cash_collection != '' && cash_collection != 0 && pos_date!="")
            {
                $.ajax({
                    type: 'POST',
                   // url: '../pos_cashier_edit/gettaxamount',
                    url: '../../helper/helper/gettaxamount',
                   
                    data: 'cash_collection=' + cash_collection +'&pos_date='+ pos_date,
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
     $(".cash_difference").keyup(function () {
            $("#cash_diff").css({"color":"black"});
            var cash_collection = parseInt($('#cash_collection').val());
            var cash_sale = parseInt($('#cash_sale').val());
           
            if (isNaN(cash_sale)) cash_sale= 0;
             if (isNaN(cash_collection)) cash_collection= 0;
              // var cash_diff = parseInt(cash_sale) - parseInt(cash_collection);
                 var cash_diff = parseInt(cash_collection) - parseInt(cash_sale);

                
                if(cash_diff<0)
                {
                   $('#cash_diff').val(cash_diff);
                   $("#cash_diff").css({"color":"red"});
                }else if(cash_diff==0){
                    $('#cash_diff').val(cash_diff);
                }else{
                   $('#cash_diff').val("+"+cash_diff);
                }
                
                var diff = parseInt($('#cash_diff').val()) + parseInt($('#bank_diff').val());
                if (isNaN(diff))
                {diff= '';}
                $('#diff').val(diff);
                if(diff<0)
                {
                $("#diff").css({"color":"red"});
                
                }
        });
        
         $(".bank_difference").keyup(function () {
            $("#bank_diff").css({"color":"black"});
            var bank_collection = parseInt($('#bank_collection').val());
            var bank_sale = parseInt($('#bank_sale').val());
           
            if (isNaN(bank_sale)) bank_sale= 0;
             if (isNaN(bank_collection)) bank_collection= 0;
               //var bank_diff = parseInt(bank_sale) - parseInt(bank_collection);
            var bank_diff =  parseInt(bank_collection)-parseInt(bank_sale);

                
                if(bank_diff<0)
                {
                    $('#bank_diff').val(bank_diff);
                    $("#bank_diff").css({"color":"red"});
                }else if(bank_diff==0){
                    $('#bank_diff').val(bank_diff);
                }else{
                    $('#bank_diff').val("+"+bank_diff);
                }
                
                var diff = parseInt($('#cash_diff').val()) + parseInt($('#bank_diff').val());
                if (isNaN(diff))
                {diff= '';}
                $('#diff').val(diff);
                if(diff<0)
                {
                $("#diff").css({"color":"red"});
                
                }
        });
        
           $(".totdiff").keyup(function () {
           // $("#bank_diff").css({"color":"black"});
            var cash_sale = parseFloat($('#cash_sale').val());
            var bank_sale = parseFloat($('#bank_sale').val());
            var credit_sale = parseFloat($('#credit_sale').val());
           
            if (isNaN(bank_sale)) bank_sale= 0;
             if (isNaN(cash_sale)) cash_sale= 0;
             if (isNaN(credit_sale)) credit_sale= 0;
               var tot_branch_sale = parseInt(cash_sale) + parseInt(bank_sale)+parseInt(credit_sale);

                $('#tot_branch_sale').val(tot_branch_sale);
                if(tot_branch_sale<0)
                {
                $("#tot_branch_sale").css({"color":"red"});
                }
                
//                var diff = parseInt($('#cash_diff').val()) + parseInt($('#bank_diff').val());
//                if (isNaN(diff))
//                {diff= '';}
//                $('#diff').val(diff);
//                if(diff<0)
//                {
//                $("#diff").css({"color":"red"});
//                
//                }
        });
     
        
        
</script>
@endsection
