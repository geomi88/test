@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Policy</span></h1>
    </header>	

    <form action="{{ action('Organizationchart\PolicyController@update') }}" method="post"  id="frmaddpolicy">
        <div class="fieldGroup" id="fieldSet1">
            

            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Policy Master</label>
                        <input type="hidden" name="editid" id="editid" value="{{$policydata->id}}">
                        <select id="policy_id" name="policy_id" >
                            <option value=''>Select Policy</option>
                            @foreach ($policymaster as $policy)
                                <option value="{{ $policy->id }}" <?php if($policy->id==$policydata->policy_master_id){echo "selected";}?>>{{$policy->name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>

            </div>

            <div class="custRow">
                <div class="custCol-12">
                    <div class="inputHolder">
                        <label>Content</label>
                        <textarea name="content" id="content" placeholder="Enter Content" >{{$policydata->content}}</textarea>
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
    $('#content').ckeditor();
    $(document).ready(function ()
    {
        $("#frmaddpolicy").validate({
            errorElement: "span",
            errorClass: "commonError",
            ignore: [],
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                
                policy_id:
                        {
                            required: true,
                        },
                
                content:
                        {
                            required: true,
                        }
            },
           
            messages: {
                policy_id: "Select policy master",
                content: "Enter content"
            }
        });
     
    });

</script>
@endsection
