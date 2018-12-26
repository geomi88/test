
                   
                    @foreach ($cashier_collections as $cashier_collection)

                     <tr>
                                <td><?php if($cashier_collection->all_collected_status==0){ }else{?><input type="checkbox" id="{{$cashier_collection->id}}" class="chkcashcollection"><?php }?></td>
                                <td><?php echo date("d-m-Y", strtotime($cashier_collection->created_at));?></td>
                                <td>{{$cashier_collection->username}}</td>
                                <td>{{$cashier_collection->employee_fname}} {{$cashier_collection->employee_aname}}</td>
                                <td>{{$cashier_collection->amount}}</td>
                                <td class="btnHolder">
                                <div class="actionBtnSet">
                                    <a class="btnAction action bgGreen" href="{{ URL::to('branchsales/cash_collection/show', ['id' => Crypt::encrypt($cashier_collection->pos_ids)]) }}">View</a>
                                </div>
                                </td>
                        <!--<input type="hidden" name="cash_collection_ids[]" value="{{$cashier_collection->id}}">-->
                            </tr>
                    @endforeach
                   