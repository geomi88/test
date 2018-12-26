<?php  $n = $cash_collections->perPage() * ($cash_collections->currentPage() - 1);
//print_r($cash_collections);
//if($cash_collections->perPage()== 0){
//   $lastPage=0;
//}else{
//$lastPage = 2;
//print_r(count($cash_collections));
//}
?> 
                   
                   @foreach ($cash_collections as $cash_collection)
                            <tr>
                                <td> <?php echo date("d-m-Y", strtotime($cash_collection->created_at));?></td>
                                <td>{{$cash_collection->username}}</td>
                                <td>{{$cash_collection->job_description}} </td>
                                <td>{{$cash_collection->employee_fname}} {{$cash_collection->employee_aname}}</td>
                                <td>{{$cash_collection->bank_name}}</td>
                                <td>{{$cash_collection->ref_no}}</td>
                                <td>{{$cash_collection->amount}}</td>
                                <td style="white-space:inherit;word-break: inherit;"><a class="fund_id" id="fund_id" href="javascript:void(0);" value="{{$cash_collection->id}}">Verify</a></td>
                                <td class="btnHolder">
                                <div class="actionBtnSet">
                                    <a class="btnAction action bgGreen" href="{{ URL::to('branchsales/cash_collection/showrecodetail', ['id' => Crypt::encrypt($cash_collection->pos_ids)]) }}">View</a>
                                </div>
                                </td>
                            </tr>
                            @endforeach
                <?php if($cash_collections->lastPage() > 1){ ?>
                       
        <tr class="paginationHolder"><th><div>   {!! $cash_collections->render() !!}</div> </th></tr>
      <?php } ?> 