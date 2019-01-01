<?php $n = $assets->perPage() * ($assets->currentPage()-1); ?>   
    @foreach ($assets as $asset)

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

        <?php if($asset->status== 1){
            $status="Enabled";
        }else{
            $status="Disabled";
        }?>
         <td>{{$status}}</td>

    </tr>
    @endforeach

    <?php if(count($assets) > 0){ ?>
    <tr class="paginationHolder"><th><div>  {!! $assets->render() !!} </div> </th></tr>
    <?php } ?>