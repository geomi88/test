<?php  $n = $cash_collections->perPage() * ($cash_collections->currentPage() - 1);
//print_r($cash_collections);
//if($cash_collections->perPage()== 0){
//   $lastPage=0;
//}else{
//$lastPage = 2;
//print_r(count($cash_collections));
//}

$amount=0;
?> 
                   
                   @foreach ($cash_collections as $cash_collection)
                   <?php $amount+= $cash_collection->amount?>
                            <tr>
                                <td class="cash_collected_date"> <?php echo date("d-m-Y", strtotime($cash_collection->created_at));?></td>
                                <td class="cash_username">{{$cash_collection->username}}</td>
                                <td class="cash_job">{{$cash_collection->job_description}} </td>
                                <td class="cash_emp_name">{{$cash_collection->employee_fname}} {{$cash_collection->employee_aname}}</td>
                                <td class="cash_bank">{{$cash_collection->bank_name}}</td>
                                <td class="cash_ref_no">{{$cash_collection->ref_no}}</td>
                                <td class="cash_amount">{{$cash_collection->amount}}</td>
                                <td style="white-space:inherit;word-break: inherit;"><a class="fund_id" id="fund_id" href="javascript:void(0);" value="{{$cash_collection->id}}">Verify</a></td>
                                <td class="btnHolder">
                                <div class="actionBtnSet">
                                    <a class="btnAction action bgGreen" href="{{ URL::to('branchsales/cash_collection/showrecodetail', ['id' => Crypt::encrypt($cash_collection->pos_ids)]) }}">View</a>
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
        <td></td>
    </tr>
    <?php }?>
                            
                       

                <?php if($cash_collections->lastPage() > 1){ ?>
                       
        <tr class="paginationHolder"><th><div>   {!! $cash_collections->render() !!}</div> </th></tr>
      
            
      <?php } ?> 