<?php  $n = $requsitions->perPage() * ($requsitions->currentPage() - 1);?>
                   
@foreach ($requsitions as $requsition)
<?php
    if ($requsition->resource_type == "BRANCH") {
        $text = "Branch";
    }
    if ($requsition->resource_type == "OFFICE") {
        $text = "Office";
    }
    if ($requsition->resource_type == "WAREHOUSE") {
        $text = "Warehouse";
    }
     if ($requsition->resource_type == "STAFF_HOUSE") {
        $text = "Staff House";
    }
?>
 <tr>
     <td class="codeC">{{$requsition->requisition_code}}</td>
     <td class="titleC">{{$requsition->title}}</td>
     <td class="dateC"><?php echo date("d-m-Y", strtotime($requsition->created_at)); ?></td>
     <td class="resourcetypeC">{{$text}}</td>
     <td class="centerC">{{$requsition->centername}}</td>
     <td class="statusC">{{$requsition->maintenancestatus}}</td>
     <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/maintenance_status/view', ['id' => Crypt::encrypt($requsition->id)]) }}" target="_blank">View</a>
            
        </div>
    </td>

 </tr>
 @endforeach
 
 
<?php if($requsitions->lastPage() > 1){ ?>
<tr class="paginationHolder"><th><div>   {!! $requsitions->render() !!}</div> </th></tr>
<?php } ?> 