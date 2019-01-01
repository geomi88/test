@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('ledgers/general_ledgers')}}">Back</a>
    <header class="pageTitle">
        <h1>Add <span>General Ledgers</span></h1>
    </header>	

    <form action="{{ action('Ledgers\GeneralledgersController@store') }}" method="post" id="frmgenledger">
        <div class="fieldGroup clsparentdiv" id="fieldSet1">
            
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Account Group</label>
                        <select class="chosen-select" name="ledger_group_id" id="ledger_group_id">
                            <option value=''>Select Group</option>
                            @foreach ($parentgroups as $parentgroup)
                            <option value="{{ $parentgroup->id }}" >{{ $parentgroup->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter Name" autocomplete="off" maxlength="200">
                        <span class="commonError"></span>
                    </div>
                </div>
                 <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name"  placeholder="Enter Alias Name" autocomplete="off" id="alias_name" maxlength="200">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
<!--                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Type</label>
                        <select class="commoSelect" name="type" id="type">
                            <option value="">---- Select ----</option>
                            <option value="Income">Income</option>
                            <option value="Expense">Expense</option>
                        </select>    
                    </div>
                </div>-->
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Nature</label>
                        <select class="commoSelect" name="nature" id="nature">
                            <option value="">---- Select ----</option>
                            <option value="DR">Debit</option>
                            <option value="CR">Credit</option>
                        </select>    
                    </div>
                </div>
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Opening Balance</label>
                        <input type="text" name="openingbalance" class="numberwithdot" id="openingbalance" autocomplete="off" placeholder="Enter Opening Balance" maxlength="30">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            

            </div>

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn addledger">
                </div>
            </div>

        </div>
    </form>	
</div>

<script>
    $(document).ready(function ()
    {
        $("#frmgenledger").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                var id=$(element).attr("id")+"_chosen";
                if($(element).hasClass('valErrorV1')){ 
                  $("#"+id).find('.chosen-single').addClass('chosen_error');
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                ledger_group_id: {
                    required: true,
                            
                },
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

                    },
                    type:{required:true},
                    alias_name:{required:true},
//                    nature:{required:true},
                
            },
             
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                ledger_group_id: "Select Account Group",
                name: "Enter Group Name",
                type: "Select Type",
                alias_name: "Enter Alias Name",
                nature: "Select Nature",
            }
        });
        
    });
     
</script>
@endsection
