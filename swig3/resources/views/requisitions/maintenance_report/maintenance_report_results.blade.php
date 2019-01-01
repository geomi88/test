<?php  $n = $requsitions->perPage() * ($requsitions->currentPage() - 1);?>
                   
@foreach ($requsitions as $requsition)

<?php
if($requsition->status == 1)
    $status = 'Pending';
elseif($requsition->status == 4)
    $status = 'Approved';
elseif($requsition->status == 5)
    $status = 'Rejected';
?>
 <tr>
     <td class="sale_code">{{$requsition->requisition_code}}</td>
     <td class="sale_branch">{{$requsition->title}}</td>
     <td class="sale_q1target"><?php echo date("d-m-Y", strtotime($requsition->created_at)); ?></td>
     <td class="sale_q2target">{{$requsition->description}}</td>
     <td class="sale_q3target">{{$status}}</td>
     <td class="btnHolder">
        <div class="actionBtnSet">
            <a class="btnAction action bgGreen" href="{{ URL::to('requisitions/maintenance_report_results/view', ['id' => Crypt::encrypt($requsition->id)]) }}" target="_blank">View</a>
            
        </div>
    </td>
     

 </tr>
 @endforeach
 
 
<?php if($requsitions->lastPage() > 1){ ?>
<tr class="paginationHolder"><th><div>   {!! $requsitions->render() !!}</div> </th></tr>
<?php } ?> 