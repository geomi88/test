
<?php 
$propertyList=$pageData['propertyList'];
?>
<?php $n = $propertyList->perPage() * ($propertyList->currentPage() - 1); ?>  
@foreach ($propertyList as $property)
<tr>
    <td>{{++$n}}</td>
    <td>{{$property->name_en}}</td>
    <td>{{$property->muncname}}</td>
    <td>{{$property->districtname}}</td>
    <td>{{$property->no_of_beds}}</td>
    <td>{{$property->no_of_baths}}</td>
    <?php 
    if($property->agent_id==NULL){
        $agentName="Admin";
    }else{
        $agentName=$property->agentname;
    }?>
    <td>{{$agentName}}</td>
    <td>
    
        <a class="actnIcons" href="{{ URL::to('admin/property-view', ['id' => Crypt::encrypt($property->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconView.png"></figure>
        </a>
        <a class="actnIcons" onclick="return confirmation()" href="{{ URL::to('admin/property-remove', ['id' => Crypt::encrypt($property->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconDel.png"></figure>
        </a>
        <a class="actnIcons" href="{{ URL::to('admin/property-edit', ['id' => Crypt::encrypt($property->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconEdit.png"></figure>
        </a>
    </td>
</tr>
@endforeach
<?php if ($propertyList->lastPage() > 1) { ?>
<tr>
    <td colspan='8'>

    <div class="mt-1 clearfix">
        {!! $propertyList->render() !!}
    </div> 
    </td>
</tr>

<?php } ?>
			