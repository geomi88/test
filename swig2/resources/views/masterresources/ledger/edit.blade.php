@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Ledger</span></h1>
    </header>	
    
    <form action="{{ action('Masterresources\LedgerController@update') }}" method="post" id="frmledgeredit">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder ledgername">
                        <label>Ledger Name</label>
                        <input type="hidden" id="ledgerid" name="ledgerid" value="{{ $ledger->id }}">
                        <input type="text" name="name" id="name" value="{{ $ledger->name }}" placeholder="Enter Ledeger Name" maxlength="50">
                         <span class="commonError"></span>
                    </div>
                </div>

                
            </div>
            
              <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder ledgername">
                        <label>Ledger Code</label>
                       <input type="text" name="code" id="code" value="{{ $ledger->ledger_code }}" placeholder="Enter Ledeger Code" maxlength="50">
                          <span class="commonError"></span>
                    </div>
                </div>

                
            </div>
            
            <div class="dates_div custCol-8">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Budget Start Date</label>
                        <input type="text" name="from_date" placeholder="Enter Budget Start Date" value="<?php echo date("d-m-Y", strtotime($ledger->start_time));?>" id="from_date" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Budget End Date</label>
                        <input  type="text" name="to_date" placeholder="Enter Budget End Date" value="<?php echo date("d-m-Y", strtotime($ledger->end_time));?>" id="to_date" readonly="readonly">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Budget Amount</label>
                        <input type="text" name="amount" id="amount" value="{{ $ledger->amount }}" placeholder="Enter Budget Amount" maxlength="20">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Details</label>
                        <textarea name="description" id="description"  placeholder="Enter Details">{{ $ledger->description }}</textarea>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn editledger">
                </div>
            </div>

        </div>
    </form>	
</div>

<script>
    $(document).ready(function()
{

        $( "#frmledgeredit" ).validate({
    
                    errorElement: "span",
                    errorClass: "commonError",
                    highlight: function(element, errorClass){
                                $(element).addClass('valErrorV1');
                            },
                            unhighlight: function(element, errorClass, validClass){
                                $(element).removeClass("valErrorV1");
                            },

                    rules: {
                        name:
                            {
                                required: {
                                    depends: function () {

                                        if ($.trim($(this).val()) == '') {
                                            $(this).val($.trim($(this).val()));
                                            return true;
                                        }
                                    }
                                },
                                remote:
                                        {
                                            url: "../checkledgername",
                                            type: "post",
                                            data:
                                                    {
                                                        name: function () {
                                                            return $.trim($("#name").val());
                                                        },
                                                        ledgerid: function () {
                                                            return $("#ledgerid").val();
                                                        }
                                                    },
                                            dataFilter: function (data)
                                            {
                                                var json = JSON.parse(data);
                                                if (json.msg == "true") {
                                                    //return "\"" + "That Company name is taken" + "\"";

                                                    $('.ledgername').addClass('ajaxLoaderV1');
                                                    $('.ledgername').removeClass('validV1');
                                                    $('.ledgername').addClass('errorV1');
                                                    // valid="false"
                                                }
                                                else
                                                {
                                                    $('.ledgername').addClass('ajaxLoaderV1');
                                                    $('.ledgername').removeClass('errorV1');
                                                    $('.ledgername').addClass('validV1');
                                                    //valid="true";
                                                    return true;
                                                }
                                            }
                                        }

                                },
                        from_date:
                            {
                                required: true,
                            },
                        to_date:
                            {
                                required: true,
                            },
                        amount:
                            {
                                required: true,number: true
                            }

                        },
                        messages: {
                         name: "Enter Ledger Name",
                        from_date: "Enter Budget Start Date",
                        to_date: "Enter Budget End Date",
                        amount:
                            {
                                required:"Enter Budget Amount",
                                number: "Enter Number Only"
                            }
                         }
                    });
     });
     
     $("#from_date").datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '1950:c',
         changeMonth: true,
         changeYear: true,
         onSelect: function(selected) {
         $("#to_date").datepicker("option","minDate", selected)
         }
     });

     $("#to_date").datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '1950:c',
         changeMonth: true,
         changeYear: true,
         
     });
</script>
@endsection
