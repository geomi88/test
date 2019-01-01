
<?php 
$agentList=$pageData['agentList'];
?>
<?php $n = $agentList->perPage() * ($agentList->currentPage() - 1); ?>  
@foreach ($agentList as $agents)
<tr>
    <td>{{++$n}}</td>
    <td>{{$agents->name}}</td>
    <td>{{$agents->muncname}}</td>
    <td>{{$agents->office_phone}}</td>
    <td>{{$agents->email}}</td>
   
    <td>
    
        <a class="actnIcons" href="{{ URL::to('admin/agent-view', ['id' => Crypt::encrypt($agents->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconView.png"></figure>
        </a>
        <a class="actnIcons" onclick="return confirmation()" href="{{ URL::to('admin/agent-remove', ['id' => Crypt::encrypt($agents->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconDel.png"></figure>
        </a>
        <a class="actnIcons" href="{{ URL::to('admin/agent-edit', ['id' => Crypt::encrypt($agents->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconEdit.png"></figure>
        </a>
    </td>
</tr>
@endforeach
<?php if ($agentList->lastPage() > 1) { ?>
<tr>
    <td colspan='8'>

    <div class="mt-1 clearfix">
        {!! $agentList->render() !!}
    </div> 
    </td>
</tr>

<?php } ?>
			