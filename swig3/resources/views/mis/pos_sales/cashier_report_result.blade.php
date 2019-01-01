
                    <?php $n = $pos_sales->perPage() * ($pos_sales->currentPage() - 1); ?>   
                    @foreach ($pos_sales as $pos_sale)

                    <tr>
                        <td class="pos_date"><?php echo date("d-m-Y", strtotime($pos_sale->pos_date));?></td>
                        <td class="pos_cashier">{{$pos_sale->employee_fname}} {{$pos_sale->employee_aname}}</td>
                        <td class="pos_branch">{{$pos_sale->branch_code}} - {{$pos_sale->branch_name}}</td>
                        <td class="pos_shift">{{$pos_sale->jobshift_name}}</td> 
                        <td class="pos_cash">{{$pos_sale->cash_collection}}</td> 
                         <td class="pos_tip">{{$pos_sale->tips_collected}}</td> 
                        <td class="pos_amt">{{$pos_sale->opening_amount}}</td>
                        <td class="pos_supervisor">{{$pos_sale->supervisor_fname}} {{$pos_sale->supervisor_aname}}</td>
                    <td class="pos_editor">{{$pos_sale->editedby_name}} {{$pos_sale->editedby_aname}}</td>
                       
                  
                        <td class="btnHolder">
                            <div class="actionBtnSet">
                                <a class="btnAction action bgGreen" href="{{ URL::to('mis/pos_sales/cashier_show', ['id' => Crypt::encrypt($pos_sale->id)]) }}">View</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                     <?php if($pos_sales->lastPage() > 1){ ?>
                    <tr class="paginationHolder"><th><div>   {!! $pos_sales->render() !!}</div> </th></tr>
                  <?php } ?>