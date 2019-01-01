<?php  $n = $cash_collections->perPage() * ($cash_collections->currentPage() - 1);
$amount=0;?> 
    @foreach ($cash_collections as $cash_collection)
    <?php  $n++;
   $amount+= $cash_collection->amount?>
    <tr>
        <td class="verifiy_date"> <?php echo date("d-m-Y", strtotime($cash_collection->created_at)); ?></td>
         <td class="verify_depo_by">{{$cash_collection->job_description}} </td>
        <td class="verify_depo_name">{{$cash_collection->employee_fname}} {{$cash_collection->employee_aname}}</td>
        <td class="verify_by">{{$cash_collection->verified_fname}} {{$cash_collection->verified_aname}}</td>
        <td class="verify_bank">{{$cash_collection->bank_name}}</td>
        <td class="verify_reference">{{$cash_collection->ref_no}}</td>
        <td class="verify_amount">{{Customhelper::numberformatter($cash_collection->amount)}}</td>
        <td class="btnHolder">
            <div class="actionBtnSet">
                <a class="btnAction action bgGreen" href="{{ URL::to('mis/verified_accounts/showverifieddetails', ['id' => Crypt::encrypt($cash_collection->pos_ids)]) }}">View</a>
            </div>
        </td>
    </tr>
    @endforeach
    <?php if($amount>0){?>
    <tr>
        <td> </td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><b>Total : </b><?php echo Customhelper::numberformatter($amount);?> </td>
        <td class="btnHolder"></td>
    </tr>
    <?php }?>
    <?php if($cash_collections->lastPage() > 1){ ?>
    <tr class="paginationHolder"><th><div>   {!! $cash_collections->render() !!}</div> </th></tr>
    <?php } ?>
    
