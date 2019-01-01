
<?php 
$neighbourhoodList=$pageData['neighbourhoodList'];
?>
<?php $n = $neighbourhoodList->perPage() * ($neighbourhoodList->currentPage() - 1); ?>  
@foreach ($neighbourhoodList as $property)
<tr>
    <td>{{++$n}}</td>
    <td>{{$property->name_en}}</td>
    <td>{{$property->muncname}}</td>
    <td>{{$property->districtname}}</td>
    <td>
    
        <a class="actnIcons" href="{{ URL::to('admin/neighbourhood-view', ['id' => Crypt::encrypt($property->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconView.png"></figure>
        </a>
        <a class="actnIcons" onclick="return confirmation()" href="{{ URL::to('admin/neighbourhood-remove', ['id' => Crypt::encrypt($property->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconDel.png"></figure>
        </a>
        <a class="actnIcons" href="{{ URL::to('admin/neighbourhood-edit', ['id' => Crypt::encrypt($property->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconEdit.png"></figure>
        </a>
    </td>
</tr>
@endforeach
<?php if ($neighbourhoodList->lastPage() > 1) { ?>
<tr>
    <td colspan='8'>

    <div class="mt-1 clearfix">
        {!! $neighbourhoodList->render() !!}
    </div> 
    </td>
</tr>

<?php } ?>
			