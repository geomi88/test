@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1><span><?php if ($loggedin_employee_details->name == "Cashier") {
                        echo "POS Cash Sales";
                    } else {
                        echo "Supervisor Collection";
                    } ?></span></h1>
    </header>	
        <?php if ($loggedin_employee_details->admin_status != 1) { ?>
            <input type="hidden" id="employee_type" value="{{$loggedin_employee_details->name}}">
        <?php } else { ?>
            <input type="hidden" id="employee_type" value="Supervisor">
        <?php } ?>

    <form action="{{ url('branchsales/pos_sales/store') }}" method="post" id="frmPosSales">

        <?php if ($loggedin_employee_details->admin_status != 1) { ?>
            <input type="hidden" name="added_by_user_type" id="added_by_user_type" value="{{$loggedin_employee_details->name}}">
        <?php } else { ?>
            <input type="hidden" id="added_by_user_type" name="added_by_user_type" value="Supervisor">
        <?php } ?>
            
        <?php if ($loggedin_employee_details->name == 'Supervisor') { ?>
            <div class="fieldGroup" id="fieldSet1">
                <div class="listContainerV1">
                    <div class="empList">
                        <figure class="imgHolder">
                            <img src="<?php echo $loggedin_employee_details->profilepic; ?>" alt="">
                        </figure>
                        <div class="details">
                            <b>{{$loggedin_employee_details->first_name}} {{$loggedin_employee_details->alias_name}}</b>
                            <p>Designation : <span>Supervisor</span></p>
                            <figure class="flagHolder">
                                <img src="{{ URL::asset('images/flags/'.$loggedin_employee_details->flag_name) }}" alt="Flag">
                                <figcaption>{{$loggedin_employee_details->country_name}}</figcaption>
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
                                <label>Branch Code (رمز الفرع)</label>
                                <select  name="branch_id" id="branch_id" class="pos_opening_amount_ajax">
                                    <option selected value=''>Select Branch Code</option>
                                    @foreach ($supervisor_branches as $branch)
                                    <option value='{{ $branch->branch_id }}'>{{ $branch->branch_code}}-{{ $branch->branch_name}}</option>
                                    @endforeach
                                </select>
                                <em class="selectedCompany"></em>
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder bgSelect">
                                <label>Shift (تحول)</label>
                                <select  name="job_shift" id="job_shift" class="pos_branch_shifts pos_opening_amount_ajax" title="تحول">

                                </select>
                                <em class="shifttimings"></em>
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4 pos_date">
                            <div class="inputHolder fieldMargin">
                                <label>Date (تاريخ)</label>
                                <input type="text" name="pos_date" id="pos_date" title="تاريخ" readonly="readonly" class="pos_opening_amount_ajax" placeholder="Select Date">
                            </div>
                        </div>
                       
                    </div>      
                </div>

                <div class="boxWrapper">
                    <div class="custRow"> 
                        <div class="custCol-4">
                            <div class="inputHolder bgSelect">
                                <label>Opening Amount (مبلغ الافتتاح)</label>
                                <input type="text" name="open_amount" id="open_amount" onpaste="return false;" title="مبلغ الافتتاح" autocomplete="off" class="sales numberwithdot" placeholder="Enter Opening Amount">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder bgSelect">
                                <label>Cashier Collection(أمين الصندوق)</label>
                                <input type="text" readonly="true" onpaste="return false;" id="cashier_amount" class="numberwithdot" name="cashier_amount">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder bgSelect">
                                <label>Tip Collection</label>
                                <input type="text" readonly="true" onpaste="return false;" id="tip_amount" class="numberwithdot" name="tip_amount">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                    </div>
                </div>
                    
                <div class="boxWrapper">
                    <div class="custRow ">

                        <div class="custCol-4 pos_total_sale">
                            <div class="inputHolder">
                                <label>Total Branch Sale</label>
                                <input type="text" name="total_sale" id="total_sale" onpaste="return false;" title="إجمالي بيع الفرع" autocomplete="off" class="sales numberwithdot" placeholder="Enter Total Branch Sale">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Sales Amount</label>
                                <input type="text" name="sales_amount" onpaste="return false;" id="sales_amount" autocomplete="off" readonly="true">
                                <span class="commonError"></span>
                            </div></div>

                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Tax Amount</label>
                                <input type="text" name="tax_amount" onpaste="return false;" id="tax_amount" autocomplete="off" readonly="true">
                                <span class="commonError"></span>
                            </div>
                        </div>  

                    </div>

                    <div class="custRow ">

                        <div class="custCol-4">
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
                                <label>Tax in POS (If any change)</label>
                                <input type="text" name="tax_in_pos" id="tax_in_pos" onpaste="return false;" autocomplete="off" class="numberwithdot" placeholder="Enter Tax in POS">
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
                                <label>Total Cash Sales</label>
                                <input type="text" name="cash_sale" id="cash_sale" title="مجموعة كاشير" autocomplete="off" onpaste="return false;" class="sales cash_difference totdiff numberwithdot" placeholder="Enter Total Cash Sale">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Cash Collection</label>
                                <input type="text" name="cash_collection" id="cash_collection" title="مجموعة كاشير" autocomplete="off" onpaste="return false;" class="sales cash_difference numberwithdot" placeholder="Enter Cashier Collection">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Excess/Shortage</label>
                                <input type="text" name="cash_diff" id="cash_diff" onpaste="return false;" readonly placeholder="Difference Amount">

                            </div>
                        </div>
                    </div>

                    <div class="custRow cashEntry">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Total Bank Card Sale (بيع بطاقة مصرفية)</label>
                                <input type="text" name="bank_sale" id="bank_sale" title="بيع بطاقة مصرفية" autocomplete="off" onpaste="return false;" class="sales bank_difference totdiff" placeholder="Enter Total Bank Card Sale">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Bank Collection</label>
                                <input type="text" name="bank_collection" id="bank_collection"  autocomplete="off" onpaste="return false;" class="sales bank_difference" placeholder="Enter Bank Collection">

                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Excess/Shortage</label>
                                <input type="text" name="bank_diff" id="bank_diff" onpaste="return false;" readonly placeholder="Difference Amount">
                            </div>
                        </div>
                    </div>

                    <div class="custRow cashEntry">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Credit/Free Sale (الائتمان / بيع مجانا)</label>
                                <input type="text" name="credit_sale" id="credit_sale" title="الائتمان / بيع مجانا" autocomplete="off" onpaste="return false;" class="sales totdiff" placeholder="Enter Credit/Free Sale">
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder">

                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Total Branch Sale</label>
                                <input type="text" name="tot_branch_sale" id="tot_branch_sale" onpaste="return false;" readonly placeholder="Total Branch Sale">
                            </div>
                        </div>

                    </div>

                </div>

                <div class="boxWrapper">
                    <div class="custRow">
                        <div class="custCol-4 pos_difference">
                            <div class="inputHolder">
                                <label>Net Difference (صافي الفرق)</label>
                                <input type="text" name="diff" id="diff" title="الفرق المبلغ" onpaste="return false;" readonly placeholder="Difference Amount">
                            </div>
                        </div>
                        <div class="custCol-4 pos_difference">
                            <div class="inputHolder">
                                <label></label>
                                <p class="total_diff" style="font-size: 14px;padding-top: 28px;color: #e74545;display:none;">Total Difference Should be Zero !!!!</p>
                                <p class="tally_total_diff" style="font-size: 14px;padding-top: 28px;color: #e74545;display:none;">Amount mismatch!! Total Difference Should be Zero </p>

                            </div>
                        </div>
                    </div>

                    <div class="custRow">
                        <div class="custCol-4 pos_reason">
                            <div class="inputHolder">
                                <label>Reason (السبب)</label>
                                <select name="pos_reason" title="السبب" id="pos_reason">
                                    <option selected value=''>Select Reason</option>
                                    @foreach ($pos_reasons as $pos_reason)
                                    <option value='{{ $pos_reason->id }}'>{{ $pos_reason->name}}</option>
                                    @endforeach
                                    <option  value='other'>Other</option>
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder reason_details">
                                <label>Reason Details</label>
                                <textarea name="reason_details" title="تفاصيل السبب"></textarea>
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
                                <input type="text" name="meals" id="meals" autocomplete="off" onpaste="return false;" placeholder="Staff Meal Consumption">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <label class="salenote">(If no meal consumption, enter 0)</label>
                        </div>

                    </div>

                </div>  
            </div>
            
            <div class="custRow">
                <div class="custCol-12">
                    <div class="commonCheckHolder clsChkPadding">
                        <label >
                            <input id="chkAgree" class="selectUnits" type="checkbox">
                            <span></span><em>I agree this cash  submission is 100% honest as per Saudi Arabian rules and regulations of GAZT and VAT.</em>
                        </label>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <input type="hidden" value="<?php echo date("Y-m-d"); ?>" name="currentdate" id="currentdate">

                <div class="custCol-4">
                    <input type="button" value="Create" id="btnSavePosSales" class="commonBtn bgGreen addBtn">
                </div>
            </div>
            
        <?php } ?>
            
            
        <?php if ($loggedin_employee_details->name == 'Cashier') { ?>
            <div class="fieldGroup" id="fieldSet1">

                <div class="listContainerV1">
                    <div class="empList">
                        <figure class="imgHolder">
                            <img src="<?php echo $loggedin_employee_details->profilepic; ?>" alt="">
                        </figure>
                        <div class="details">
                            <b>{{$loggedin_employee_details->first_name}} {{$loggedin_employee_details->alias_name}}</b>
                            <p>Designation : <span>Cashier</span></p>
                            <figure class="flagHolder">
                                <img src="{{ URL::asset('images/flags/'.$loggedin_employee_details->flag_name) }}" alt="Flag">
                                <figcaption>{{$loggedin_employee_details->country_name}}</figcaption>
                            </figure>
                        </div>
                        <div class="customClear"></div>
                    </div>
                    
                    <?php if (count($supervisor_details) > 0) { ?>
                        <div class="empList">
                            <input type="hidden" name="employee_id" value="{{$supervisor_details->supervisor_id}}">
                            <figure class="imgHolder">
                                <img src="<?php echo $supervisor_details->profilepic; ?>" alt="">
                            </figure>
                            <div class="details">
                                <b>{{$supervisor_details->supervisor_first_name}} {{$supervisor_details->supervisor_alias_name}}</b>
                                <p>Designation : <span>Supervisor</span></p>
                                <figure class="flagHolder">
                                    <img src="{{ URL::asset('images/flags/'.$supervisor_details->flag_name) }}" alt="Flag">
                                    <figcaption>{{$supervisor_details->country_name}}</figcaption>
                                </figure>
                            </div>
                            <div class="customClear"></div>
                        </div>
                    <?php } ?>
                </div>

                <div class="boxWrapper">
                    <div class="custRow">
                        <div class="custCol-4">
                            <input type="hidden" name="branch_id" value="{{$branch_details->branch_id}}">
                            <div class="inputHolder bgSelect">
                                <label>Branch Name</label>
                                <b>{{ $branch_details->branch_code}}-{{$branch_details->branch_name}}</b>
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
                                <label>Today's Date</label>
                                <b><?php echo date('d-m-Y'); ?></b>
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
                                <input type="text" name="open_amount" id="open_amount" <?php if ($branch_details->opening_fund_editable == 1) { ?> readonly <?php } ?> value="{{$branch_details->opening_fund}}" autocomplete="off" class="sales" placeholder="Enter Opening Amount">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="boxWrapper">
                    <div class="custRow">
                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Cash Collection</label>
                                <input type="text" name="cash_collection" id="cash_collection" autocomplete="off" class="sales" placeholder="Enter Cash Collection">
                                <span class="commonError"></span>
                            </div>
                        </div>

                    </div>

                    <div class="custRow">
                        <div class="custCol-4 clsfont">
                            <div class="inputHolder">
                                <label>Bank Card Collection</label>
                                <input type="text" name="bank_card_collection" id="bank_card_collection" autocomplete="off"  placeholder="Enter Bank Card Collection">
                                <span class="commonError"></span>
                            </div>
                        </div>
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
                                <input type="text" name="tips_collected" id="tips_collected" autocomplete="off" class="sales" placeholder="Enter Tips Submission">
                                <span class="commonError"></span>
                            </div></div>

                    </div>
                    
                    <div class="custRow">
                        <div class="custCol-6 ">
                            <div class="commonCheckHolder clsChkPadding">
                                <label >
                                    <input id="no_tips" class="no_tips" type="checkbox">
                                    <span></span><em>No Tips Collected Today</em>
                                </label>
                            </div>
                        </div>

                    </div>
                </div>
                
            <div class="custRow">
                <div class="custCol-12">
                    <div class="commonCheckHolder clsChkPadding">
                        <label >
                            <input id="chkAgree" class="selectUnits" type="checkbox">
                            <span></span><em id="i_agree">I agree this cash  submission is 100% honest as per Saudi Arabian rules and regulations of GAZT and VAT.</em>
                        </label>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <input type="hidden" value="<?php echo date("Y-m-d"); ?>" name="currentdate" id="currentdate">

                <div class="custCol-4">
                    <input type="button" value="Create" id="btnSavePosSales" class="commonBtn bgGreen addBtn">
                </div>
            </div>
        <?php } ?>
                


        </div>
    </form>
</div>

<script>
    $(document).ready(function ()
    {

        $("#pos_date").datepicker({
            dateFormat: 'dd-mm-yy',
            minDate: new Date(1950, 10 - 1, 25),
            maxDate: '+0D',
            yearRange: '1950:c',
            changeMonth: true,
            changeYear: true
        });
        
        $(".sales").keyup(function () {
            $("#diff").css({"color": "black"});
            var cash_collection = parseFloat($('#cash_collection').val());
            var total_sale = parseFloat($('#total_sale').val());
            var credit_sale = parseFloat($('#credit_sale').val());
            var bank_sale = parseFloat($('#bank_sale').val());
            if (isNaN(total_sale))
                total_sale = 0;
            if (isNaN(bank_sale))
                bank_sale = 0;
            if (isNaN(credit_sale))
                credit_sale = 0;
            if (isNaN(cash_collection))
                cash_collection = 0;

            var difference_in_sales = (parseFloat($('#cash_sale').val()) + parseFloat(credit_sale) + parseFloat(bank_sale)) - parseFloat(total_sale);
            if (difference_in_sales < 0)
            {
                $(".total_diff").show();
            }
            else
            {
                $(".total_diff").hide();
            }

        });


        $(".cash_difference").keyup(function () {
            $("#cash_diff").css({"color": "black"});
            var cash_collection = parseFloat($('#cash_collection').val());
            var cash_sale = parseFloat($('#cash_sale').val());

            if (isNaN(cash_sale))
                cash_sale = 0;
            if (isNaN(cash_collection))
                cash_collection = 0;
            
            var cash_diff = parseFloat(cash_collection) - parseFloat(cash_sale);

           
            if (cash_diff < 0)
            {
                $('#cash_diff').val(amountformat(cash_diff));
                $("#cash_diff").css({"color": "red"});
            }else if(cash_diff==0){
                    $('#cash_diff').val(amountformat(cash_diff));
                }
            else{
                 $('#cash_diff').val("+"+amountformat(cash_diff));
            }

            var diff = parseFloat($('#cash_diff').val()) + parseFloat($('#bank_diff').val());
            if (isNaN(diff))
            {
                diff = '';
            }
            $('#diff').val(diff);
            if (diff < 0)
            {
                $("#diff").css({"color": "red"});

            }
        });

        $(".bank_difference").keyup(function () {
            $("#bank_diff").css({"color": "black"});
            var bank_collection = parseFloat($('#bank_collection').val());
            var bank_sale = parseFloat($('#bank_sale').val());

            if (isNaN(bank_sale))
                bank_sale = 0;
            if (isNaN(bank_collection))
                bank_collection = 0;
            
            var bank_diff = parseFloat(bank_collection) - parseFloat(bank_sale);
            
            if (bank_diff < 0){
                $('#bank_diff').val(bank_diff);
                $("#bank_diff").css({"color": "red"});
            }else if(bank_diff==0){
                $('#bank_diff').val(bank_diff);
            }else{
                 $('#bank_diff').val("+"+bank_diff);
            }

            var diff = parseFloat($('#cash_diff').val()) + parseFloat($('#bank_diff').val());
            if (isNaN(diff))
            {
                diff = '';
            }
            $('#diff').val(diff);
            if (diff < 0)
            {
                $("#diff").css({"color": "red"});

            }
        });

        $("#branch_id").change(function () {
            $('#job_shift').val('');
            $('#pos_date').val('');
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
                        $('#tip_amount').val(return_data.tip_collected);
                        if (return_data.opening_fund_editable == 1) {

                            $("#open_amount").prop("readonly", true);
                        }
                        if (return_data.cashier_entry == false) {

                            alert("Cashier not entered in MIS");
                        }
                    }
                });
            }
        });

        $("#pos_date").change(function () {

            var branch_id = $('#branch_id').val();
            var shift_id = $('#job_shift').val();
            var pos_date = $('#pos_date').val();
            if (branch_id && shift_id && pos_date) {
                if (shift_id)
                {
                    $.ajax({
                        type: 'POST',
                        url: '../pos_sales/shift_cashier',
                        data: 'shift_id=' + shift_id + '&branch_id=' + branch_id + '&pos_date=' + pos_date,
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
            }

            return dateCompare();

        });


        $("#job_shift").change(function () {
            var branch_id = $('#branch_id').val();
            var shift_id = $('#job_shift').val();
            var pos_date = $('#pos_date').val();
            if (branch_id && shift_id && pos_date) {
                if (shift_id)
                {
                    $.ajax({
                        type: 'POST',
                        url: '../pos_sales/shift_cashier',
                        data: 'shift_id=' + shift_id + '&branch_id=' + branch_id + '&pos_date=' + pos_date,
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
            }

        });
        
        $("#frmPosSales").validate({
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
                tax_in_pos:
                        {
                            number: true
                        },
               
                meals:
                        {
                            required: true,
                            number: true
                        },
                pos_reason:
                        {
                            required: function (element) {
                                return $('#diff').val() != '0';
                            }
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
                meals: {
                    required: "Enter Meal Consumption",
                    number: "Enter number only"
                },
                tax_in_pos: {
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
            if (!$("#frmPosSales").valid()) {
                return;
            }
        
            if ($("#chkAgree").prop('checked') == false) {
                $("#i_agree").css({"color": "red"});
                return false;
            }
            
            if ($('#added_by_user_type').val() == 'Supervisor') {
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
            }
            
            var blnConfirm = confirm("Are you sure to submit?");
            if(!blnConfirm){
               return; 
            }
            
            $("#frmPosSales").submit();
            
        });
        
    });

    $('#total_sale').bind('keyup', function () {
        getSupervisorTaxValue();
    });

    function getTaxValue() {
        var cash_collection = $('#cash_collection').val();
        var pos_date = $('#currentdate').val();

        if (cash_collection != '' && cash_collection != 0)
        {
            $.ajax({
                type: 'POST',
                url: '../../helper/helper/gettaxamount',
                data: 'cash_collection=' + cash_collection + '&pos_date=' + pos_date,
                success: function (return_data) {

                    $('#tax_amount').val(return_data.tax_amount);
                    $('#sales_amount').val(return_data.sales_amount);

                }
            });
        } else {
            $('#tax_amount').val("");
            $('#sales_amount').val("");
        }

    }

    function getSupervisorTaxValue() {
        var cash_collection = $('#total_sale').val();

        var employee_type = $('#employee_type').val();
        var pos_date = $('#pos_date').val();
        
        if (cash_collection != '' && cash_collection != 0 && pos_date != "")
        {
            $.ajax({
                type: 'POST',
                // url: '../pos_sales/gettaxamount',
                url: '../../helper/helper/gettaxamount',
                data: 'cash_collection=' + cash_collection + '&pos_date=' + pos_date,
                success: function (return_data) {

                    $('#tax_amount').val(return_data.tax_amount);
                    $('#sales_amount').val(return_data.sales_amount);

                }
            });
        } else if (pos_date == "") {
            alert("Please choose the date before entering the sales amount")
        } else {

            $('#tax_amount').val("");
            $('#sales_amount').val("");
        }

    }

    function dateCompare() {

        var pos_date = $('#pos_date').val();

        $.ajax({
            type: 'POST',
            url: '../pos_sales/dateCompare',
            data: 'pos_date=' + pos_date,
            success: function (return_data) {
                if (return_data.taxapplicable == true) {
                    $('.clsfont').show();
                } else {
                    $('.clsfont').hide();
                }
            }
        });

    }

    $(".totdiff").keyup(function () {
        var cash_sale = parseFloat($('#cash_sale').val());
        var bank_sale = parseFloat($('#bank_sale').val());
        var credit_sale = parseFloat($('#credit_sale').val());

        if (isNaN(bank_sale))
            bank_sale = 0;
        if (isNaN(cash_sale))
            cash_sale = 0;
        if (isNaN(credit_sale))
            credit_sale = 0;
        var tot_branch_sale = parseFloat(cash_sale) + parseFloat(bank_sale) + parseFloat(credit_sale);

        $('#tot_branch_sale').val(tot_branch_sale);
        if (tot_branch_sale < 0)
        {
            $("#tot_branch_sale").css({"color": "red"});
        }

    });

    $('#bank_slips').click(function () {
        if ($(this).prop('checked')) {
            $('#bank_card_collection').val("0");
            $('#bank_card_collection').prop('disabled', true);
        }else {
            $('#bank_card_collection').prop('disabled', false);
            $('#bank_card_collection').val("");
        }
    });


    $('#no_tips').click(function () {
        if ($(this).prop('checked')) {
            $('#tips_collected').val("0");
            $('#tips_collected').prop('disabled', true);
        }else {
             $('#tips_collected').val("");
           $('#tips_collected').prop('disabled', false);

        }
    });

</script>
@endsection
