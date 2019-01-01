
<?php 
$projectsList=$pageData['projectsList'];
?>
<?php $n = $projectsList->perPage() * ($projectsList->currentPage() - 1); ?>  
@foreach ($projectsList as $property)
<tr>
    <td>{{++$n}}</td>
    <td>{{$property->name_en}}</td>
    <td>{{$property->districtname}}</td>
    <td>{{$property->url}}</td>
   
    <?php 
    /*if($property->agent_id==NULL){
        $agentName="Admin";
    }else{
        $agentName=$property->agentname;
    }*/?>
   
    <td>
    
        <a class="actnIcons" href="{{ URL::to('admin/construction-view', ['id' => Crypt::encrypt($property->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconView.png"></figure>
        </a>
        <a class="actnIcons" onclick="return confirmation()" href="{{ URL::to('admin/construction-remove', ['id' => Crypt::encrypt($property->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconDel.png"></figure>
        </a>
        <a class="actnIcons" href="{{ URL::to('admin/construction-edit', ['id' => Crypt::encrypt($property->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconEdit.png"></figure>
        </a>
    </td>
</tr>
@endforeach
<?php if ($projectsList->lastPage() > 1) { ?>
<tr>
    <td colspan='8'>

    <div class="mt-1 clearfix">
        {!! $projectsList->render() !!}
    </div> 
    </td>
</tr>

<?php } ?>
			