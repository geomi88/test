@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Account Group</span></h1>
    </header>	

    <form action="{{ action('Masterresources\LedgergroupController@store') }}" method="post" id="ledgerinsertion">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder ledgername">
                        <label>Group Name</label>
                        <input type="text" name="name" id="name" placeholder="Enter Group Name" maxlength="150">
                        <span class="commonError"></span>
                    </div>
                </div>

            </div>
            
             <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Alias</label>
                        <input type="text" name="alias_name"  placeholder="Enter Alias Name" id="alias_name" >
                        <span class="commonError"></span>
                    </div>
                </div>
               

            </div>
            
            <div class="custRow">
                <div class="custCol-3">
                    <div class="inputHolder">
                        <label>Group Type</label>
                        <select class="commoSelect" name="grouptype" id="grouptype">
                            <option value="">---- Select ----</option>
                            <option value='1'>Main Group</option>
                            <option value='2'>Sub Group</option>
                        </select>    
                    </div>
                </div>
                
            </div>
            
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Parent Group</label>
                        <select class="commoSelect" name="parentgroup" id="parentgroup">
                            <option value=''>Select Parent Group</option>
                          
                        </select>    
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
        $("#ledgerinsertion").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
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
                                    url: "../ledger_group/checkledgername",
                                    type: "post",
                                    data:
                                            {
                                                name: function () {
                                                    return $.trim($("#name").val());
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
                                            //valid="true";
                                            return true;
                                        }
                                    }
                                }

                        },
                        grouptype:{required:true},
                        parentgroup:{required:true},
                
            },
             
            submitHandler: function (form) {
                form.submit();
            },
            messages: {
                name: {required:"Enter Group Name",remote: "Group Name Already Exists"},
                grouptype: "Select Group type",
                parentgroup: "Select Parent",
            }
        });
        
        $('#grouptype').on("change", function () {
          var grouptype = $(this).val();
          if(grouptype!=''){
              $.ajax({
                type: 'POST',
                url: 'gerparentgroups',
                data: 'grouptype=' + grouptype,
                success: function (return_data) {
                    $('#parentgroup').html(return_data);
                }
            });
          }else{
              $('#parentgroup').html('<option value="">Select Parent Group</option>');
          }
            
        });
        
    });
     
</script>
@endsection
