<?php $n = $assets->perPage() * ($assets->currentPage()-1); ?>   
    @foreach ($assets as $asset)
    @if($asset->status==0)
    <?php $status='Enable'; ?>
    @else
    <?php $status='Disable'; ?>
    @endif
    <?php $n++; 
    $asset_name = $asset->name." ".$asset->alias_name;
    if($asset->purchase_date == NULL){
        $date='';
    }else{
        $date=date("d-m-Y", strtotime($asset->purchase_date));
    }
    if($asset->purchase_value == 0){
        $purchase_value = '';
    }else{
        $purchase_value = $asset->purchase_value;
    }
    if($asset->asset_value == 0){
        $asset_value = '';
    }else{
        $asset_value = $asset->asset_value;
    }
    ?>

    <tr>
        <td>{{$n}}</td>
        <td>{{$asset->code}}</td>
        <td><?php echo $asset_name;?></td>
        <td>{{$asset->supplier_name}}</td> 
        <td><?php echo $date;?></td>
        <td>{{$purchase_value}}</td>
        <td>{{$asset_value}}</td>

        <td class="btnHolder">
            <div class="actionBtnSet">
                <a class="btnAction action bgRed" href="javascript:void(0)">Action</a>
                <div class="actionBtnHolderV1">
                <a class="btnAction edit bgBlue" href="{{ URL::to('assets/edit', ['id' => Crypt::encrypt($asset->id)]) }}">Edit</a>
                <a class="btnAction disable bgOrange"  href="{{ URL::to('assets/'.$status, ['id' => Crypt::encrypt($asset->id)]) }}"><?php echo $status; ?></a>
                <a class="btnAction delete bgLightRed" href="{{ URL::to('assets/delete', ['id' => Crypt::encrypt($asset->id)]) }}">Delete</a>
                </div>
            </div>
        </td>

    </tr>
    @endforeach

    <?php if(count($assets) > 0){ ?>
    <tr class="paginationHolder"><th><div>  {!! $assets->render() !!} </div> </th></tr>
    <?php } ?>