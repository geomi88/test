<?php  $n = $policymaster->perPage() * ($policymaster->currentPage() - 1); ?> 
    @foreach ($policymaster as $policy)
    
    <?php  $n++; ?>
    <tr>
        <td class="category_slno">{{ $n }}</td>
        <td class="category_name">{{$policy->name}}</td>
        <td class="category_alias">{{$policy->alias_name}}</td>
        
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/policy_master/edit', ['id' => Crypt::encrypt($policy->id)]) }}">Edit</a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/policy_master/delete', ['id' => Crypt::encrypt($policy->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if(count($policymaster) > 0){ ?>
        <tr class="paginationHolder"><th><div>   {!! $policymaster->render() !!}</div> </th></tr>
    <?php } ?>
    
    