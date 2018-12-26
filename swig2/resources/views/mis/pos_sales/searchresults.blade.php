
                    <?php $n = $pos_sales->perPage() * ($pos_sales->currentPage() - 1); ?>   
                    @foreach ($pos_sales as $pos_sale)

                    <tr>
                        <td class="pos_employee">{{$pos_sale->employee_fname}} {{$pos_sale->employee_aname}}</td>
                        <td class="pos_branch">{{$pos_sale->branch_name}}</td>
                        <td class="pos_jobshift">{{$pos_sale->jobshift_name}}</td> 
<!--                        <td>{{$pos_sale->total_sale}}</td>-->
                        
                        <td class="pos_total">{{Customhelper::numberformatter($pos_sale->total_sale)}}</td>
                        <td class="pos_cash">{{Customhelper::numberformatter($pos_sale->cash_collection)}}</td>   
                                
                        <?php $difference= (($pos_sale->total_sale) - ($pos_sale->cash_collection));?>
                        <td class="pos_diff"><?php echo Customhelper::numberformatter($difference); ?> </td>
                        <td class="pos_dat"><?php echo date("d-m-Y", strtotime($pos_sale->pos_date));?></td>
                        <td class="btnHolder">
                            <div class="actionBtnSet">
                                <a class="btnAction action bgGreen" href="{{ URL::to('mis/pos_sales/show', ['id' => Crypt::encrypt($pos_sale->id)]) }}">View</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    <tr class="paginationHolder"><th><div>   {!! $pos_sales->render() !!}</div> </th></tr>
                