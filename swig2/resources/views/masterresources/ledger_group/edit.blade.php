@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Account Group</span></h1>
    </header>	
    
    <form action="{{ action('Masterresources\LedgergroupController@update') }}" method="post" id="frmledgeredit">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder ledgername">
                        <label>Group Name</label>
                        <input type="hidden" name="ledgerid" id="ledgerid" value="{{$ledger_groups->id}}">
                        <input type="text" name="name" id="name" placeholder="Enter Group Name" value="{{$ledger_groups->name}}" maxlength="150">
                        <span class="commonError"></span>
                    </div>
                </div>

            </div>
            
             <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name" placeholder="Enter Alias Name" id="alias_name" value="{{$ledger_groups->alias_name}}">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Group Type</label>
                        <select class="commoSelect" name="grouptype" id="grouptype" disabled="disabled">
                            <option value="">---- Select ----</option>
                            <option value='1' <?php if($ledger_groups->group_type=='1'){ echo "selected";}?>>Main Group</option>
                            <option value='2' <?php if($ledger_groups->group_type=='2'){ echo "selected";}?>>Sub Group</option>
                        </select>    
                    </div>
                </div>
                
            </div>
            
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Parent Group</label>
                        <select class="commoSelect" name="parentgroup" id="parentgroup" disabled="disabled">
                            <option value=''>Select Parent Group</option>
                            @foreach ($parentgroups as $parentgroup)
                            <option value='{{ $parentgroup->id }}' <?php if($ledger_groups->parent_id==$parentgroup->id){ echo "selected";}?>>{{ $parentgroup->name}}</option>
                            @endforeach
                        </select>    
                    </div>
                </div>
            </div>
            

            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn">
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
                                remote:{
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
                                                    $('.ledgername').addClass('ajaxLoaderV1');
                                                    $('.ledgername').removeClass('validV1');
                                                    $('.ledgername').addClass('errorV1');
                                                    return false;
                                                   
                                                }
                                                else
                                                {
                                                    $('.ledgername').addClass('ajaxLoaderV1');
                                                    $('.ledgername').removeClass('errorV1');
                                                    $('.ledgername').addClass('validV1');
                                                  
                                                    return true;
                                                }
                                            }
                                        }

                                },
                        
                        },
                        messages: {
                         name: {required:"Enter Group Name",remote: "Group Name Already Exists"},
                        
                         }
                    });
     });
     
   
</script>
@endsection
