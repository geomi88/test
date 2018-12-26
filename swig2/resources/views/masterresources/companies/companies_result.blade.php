<?php  $n = $companies->perPage() * ($companies->currentPage() - 1); ?> 
    @foreach ($companies as $company)
    @if($company->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php  $n++; ?>
    <tr>
        <td class="comany_slno">{{ $n }}</td>
        <td class="company_name">{{$company->name}}</td>
        <td class="company_alias">{{$company->alias_name}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
		<a class="btnAction action bgRed" href="javascript:void(0)">Action</a>		
                <div class="actionBtnHolderV1">
		<a class="btnAction edit bgBlue" href="{{ URL::to('masterresources/companies/edit', ['id' => Crypt::encrypt($company->id)]) }}">Edit</a>
		<a class="btnAction disable bgOrange" href="{{ URL::to('masterresources/companies/'.$status, ['id' => Crypt::encrypt($company->id)]) }}"><?php echo $status; ?></a>
		<a class="btnAction delete bgLightRed" href="{{ URL::to('masterresources/companies/delete', ['id' => Crypt::encrypt($company->id)]) }}">Delete</a>
		</div>
            </div>
        </td>
    </tr>
    @endforeach
    
    <?php if($companies->lastPage() > 1){ ?>
        <tr class="paginationHolder"><th><div>   {!! $companies->render() !!}</div> </th></tr>
    <?php } ?>

