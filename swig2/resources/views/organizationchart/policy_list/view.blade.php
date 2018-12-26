@extends('layouts.main')
@section('content')
<div class="innerContent">
    <header class="pageTitle">
        <h1>View <span>Policy</span></h1>
    </header>	

    <form action="" method="post"  id="frmaddpolicy">
        <div class="fieldGroup" id="fieldSet1">
            

            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Policy Master</label>
                        <input type="hidden" name="editid" id="editid" value="{{$policydata->id}}">
                        <select id="policy_id" name="policy_id" disabled>
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
          

        </div>
    </form>	
</div>

<script>
    $('#content').ckeditor();

</script>
@endsection
