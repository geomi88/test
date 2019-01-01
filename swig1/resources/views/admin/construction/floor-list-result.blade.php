
<?php $n = $floorList->perPage() * ($floorList->currentPage() - 1); ?>  
@foreach ($floorList as $floors)
<tr>
    <td>{{++$n}}</td>
    <td>{{$floors->name_en}}</td>
    <td>{{$floors->approximate_area}}</td>
    <td>{{$floors->no_of_beds}}</td>
    <td>{{$floors->no_of_baths}}</td>
   
    <td>
    
        <!--<a class="actnIcons" href="{{ URL::to('admin/floo-view', ['id' => Crypt::encrypt($floors->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconView.png"></figure>
        </a>-->
        <a class="actnIcons" onclick="return confirmation()" href="{{ URL::to('admin/floor-remove', ['id' => Crypt::encrypt($floors->id),'pid'=> Crypt::encrypt($floors->project_id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconDel.png"></figure>
        </a>
        <a class="actnIcons" href="{{ URL::to('admin/floor-edit', ['id' => Crypt::encrypt($floors->id)]) }}">
            <figure><img src="{{URL::asset('admin')}}/images/iconEdit.png"></figure>
        </a>
    </td>
</tr>
@endforeach
<?php if ($floorList->lastPage() > 1) { ?>
<tr>
    <td colspan='8'>

    <div class="mt-1 clearfix">
        {!! $floorList->render() !!}
    </div> 
    </td>
</tr>

<?php } ?>
			