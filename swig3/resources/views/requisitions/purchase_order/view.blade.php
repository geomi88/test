@extends('layouts.main')
@section('content')
<div class="innerContent">
    @if($orderdata->order_type==2)
    <a class="btnBack" href="{{ URL::to('requisitions/purchase_orders')}}">Back</a>
    @else
    <a class="btnBack" href="{{ URL::to('requisitions/import_purchase_orders')}}">Back</a>
    @endif
    <div class="formViewWrapper" style="margin-top: 35px;">
        <div class="formContentHolder">
            <form action="{{ action('Requisitions\PurchaseorderController@update') }}" id="frmpo" method="post" enctype="multipart/form-data">
                <input type="hidden" id="orderid" name="orderid" value="{{$orderdata->id}}">
                <input type="hidden" id="ordertype" name="ordertype" value="{{$orderdata->order_type}}">
                <div class="formHeader">
                    <div class="formTitle">
                        <p><strong>Order Type :</strong><span><?php if($orderdata->order_type==3){ $type="Import";echo "IPO";}else{ $type="Local"; echo "LPO";} ?></span></p>
                        <p><strong>Date :</strong><span><?php echo date('d-m-Y', strtotime($orderdata->created_at)); ?></span></p>
                        <p><strong>PO. No :</strong><span>{{$orderdata->order_code}}</span></p>
                    </div>
                    <div class="textHeading"><h2>{{$type}} Purchase Order</h2></div>
                    <div class="companyLogo">
                        <img src="{{ URL::asset('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est.">
                    </div>
                </div>
                <div class="formDetailsWrapper">
                    <span>Supplier</span>
                    <div class="formDetailsTable">
                        <div class="formDetails">
                            <p>
                                <span>Code :</span>
                                <strong>{{$orderdata->suppliercode}}</strong>
                            </p>
                            <p>
                                <span>Name :</span>
                                <strong>{{$orderdata->supplierfname}} {{$orderdata->supplieraname}}</strong>
                            </p>
                            
                            <p>
                                <span>Country :</span>
                                <strong>{{$orderdata->supnation}}</strong>
                            </p>
                        </div>
                        <div class="formDetails">
                            <p>
                                <span>Contact No :</span>
                                <strong>{{$orderdata->supmob}}</strong>
                            </p>
                            <p>
                                <span>CR No :</span>
                                <strong>{{$orderdata->cr_number}}</strong>
                            </p>
                           
                            <p>
                                <span>VAT No:</span>
                                <strong>{{$orderdata->supplier_pin}}</strong>
                            </p>
                        </div>
                        <div class="formDetails">
                             <p>
                                <span>Address :</span>
                                <strong>{{$orderdata->supplier_address}}</strong>
                            </p>
                            <p>
                                <span>Email :</span>
                                <strong>{{$orderdata->supemail}}</strong>
                            </p>
                            <p>
                                <span>Contact Person :</span>
                                <strong>{{$orderdata->contact_person}}</strong>
                            </p>
                        </div>
                    </div>
                </div>


                <div class="formDetailsWrapper">
                    <span>Supplier Bank</span>
                    <div class="formDetailsTable">
                        <div class="formDetails">
                            <p>
                                <span>Beneficiary Name :</span>
                                <strong>{{$orderdata->bank_beneficiary_name}}</strong>
                            </p>
                            
                            <p>
                                <span>Branch Name :</span>
                                <strong>{{$orderdata->bank_branch_name}}</strong>
                            </p>
                             <p>
                                <span>Bank Name :</span>
                                <strong>{{$orderdata->bank_name}}</strong>
                            </p>
                            <p class="inputHolder">
                                <label>Other Reference :</label>
                                <input placeholder="Enter Other Reference" autocomplete="off" name="bank_other_reference" type="text">
                                <span class="commonError"></span>
                            </p>
                        </div>
                        <div class="formDetails">
                             <p>
                                <span>Swift Code :</span>
                                <strong>{{$orderdata->bank_swift_code}}</strong>
                            </p>
                            <p>
                                <span>Account Number :</span>
                                <strong>{{$orderdata->bank_account_number}}</strong>
                            </p>
                           <p>
                                <span>IBAN Number :</span>
                                <strong>{{$orderdata->bank_iban_no}}</strong>
                            </p>
                            
                        </div>
                        <div class="formDetails">
                           
                            <p>
                                <span>City :</span>
                                <strong>{{$orderdata->bank_city}}</strong>
                            </p>
                            <p>
                                <span>Country :</span>
                                <strong>{{$orderdata->banknation}}</strong>
                            </p>
                            <p>
                                <span>Currency :</span>
                                <strong></strong>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="formTableWrapper">
                    <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="100px;">Item Code</th>
                                <th width="250px;">Item Name</th>
                                <th width="50px;">Qty </th>
                                <th width="50px;">Unit</th>
                                <th width="80px;">Price/Unit</th>
                                <th width="100px;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totalprice = 0; ?>
                            @foreach ($requisitionitems as $item)
                            <?php $totalprice+=$item->total_price; ?>
                            <tr>
                                <td>{{$item->product_code}}</td>
                                <td>{{$item->productname}}</td>
                                <td style="text-align:right;">{{$item->quantity}}</td>
                                <td>{{$item->unit}}</td>
                                <td style="text-align:right;"><?php echo Customhelper::numberformatter($item->unit_price); ?></td>
                                <td style="text-align:right;"><?php echo Customhelper::numberformatter($item->total_price); ?></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
                <div class="custRow">
                    <div class="custCol-8"></div>
                    <div class="custCol-4 totalValueHolder">
                        <div class="custRow ">
                            <div class="custCol-6 alignRight">Sub Total :</div>
                            <div class="custCol-6 alignRight"><?php echo Customhelper::numberformatter($totalprice); ?></div>
                        </div>
                        <div class="custRow ">
                            <div class="custCol-6 alignRight">VAT Amount :</div>
                            <div class="custCol-6 alignRight"><?php echo Customhelper::numberformatter($orderdata->total_vat); ?></div>
                        </div>
                        <div class="custRow ">
                            <div class="custCol-6 total alignRight">Total Amount :</div>
                            <div class="custCol-6 total alignRight"><?php echo Customhelper::numberformatter($totalprice+$orderdata->total_vat); ?></div>
                        </div>
                    </div>
                </div>
                <div class="formDetailsWrapper">

                    <span>Order Details </span>
                    <div class="formDetailsTable">
                        <div class="formDetails" style="padding-right:10px;">
                            <p class="inputHolder">
                                <label>Suppliers Quotation Number :</label>
                                <input placeholder="Enter Suppliers Quotation" maxlength="150" name="quotation" type="text" autocomplete="off">
                                <span class="commonError"></span>

                            </p>
                             <p class="inputHolder">
                                <label>Delivery Place :</label>
                                <input placeholder="Enter Delivery Place" name="delivery_destination" value="{{$orderdata->delivery_destination}}" autocomplete="off" type="text">
                                <span class="commonError"></span>
                            </p>
                              <p class="inputHolder">
                                <label>Terms of Delivery 1:</label>
                                <textarea placeholder="Enter Terms of Delivery" name="delivery_terms1" ></textarea>
                                <span class="commonError"></span>
                            </p>

                        </div>
                        <div class="formDetails" >
                            
                            <p class="inputHolder">
                                <label>Quotation Date :</label>
                                <input placeholder="Enter Quotation Date" name="quotation_date" id="quotation_date" autocomplete="off" type="text">
                                <span class="commonError"></span>
                            </p>
                              
                            <p class="inputHolder">
                                <label>Delivery Date :</label>
                                <input placeholder="Enter Delivery Date" name="delivery_date" id="delivery_date" value="<?php if($orderdata->delivery_date){ echo date("d-m-Y",  strtotime($orderdata->delivery_date));}?>" autocomplete="off" type="text">
                                <span class="commonError"></span>
                            </p>
                            <p class="inputHolder">
                                <label>Terms of Delivery 2:</label>
                                <textarea placeholder="Enter Terms of Delivery" name="delivery_terms2"></textarea>
                                <span class="commonError"></span>
                            </p>

                        </div>
                        
                        <div class="formDetails">
                            <div class="inputHolder checkHolder">
                                <label>Mode of Payment :</label>

                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input id="topManager" name="payment_term" <?php if($orderdata->payment_mode=="Cash" || $orderdata->payment_mode==''){ echo "checked";}?> value="Cash" type="radio" class="clsmodeofpay"  >
                                        <span></span>
                                        <em>Cash</em>
                                    </label>
                                </div>
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input id="otherEmployees" name="payment_term" <?php if($orderdata->payment_mode=="Credit"){ echo "checked";}?> value="Credit" type="radio" class="clsmodeofpay" >
                                        <span></span>
                                        <em>Credit</em>
                                    </label>
                                </div>

                            </div>
                         
                            <p class="inputHolder clscreditdays" style="<?php if($orderdata->payment_mode=="Cash" || $orderdata->payment_mode==''){ echo "display: none";}?>">
                                <label>Credit Days :</label>
                                <input placeholder="Enter Credit Days" name="credit_days" class="numberwithdot" value="{{$orderdata->credit_days}}" maxlength="10" autocomplete="off" type="text">
                                <span class="commonError"></span>
                            </p>
                            <p class="inputHolder">
                                <label>Terms of Payment :</label>
                                <input placeholder="Enter Terms of Payment" name="payment_terms" value="{{$orderdata->payment_term}}" maxlength="250" autocomplete="off" type="text">
                                <span class="commonError"></span>
                            </p>
                           
                        </div>
                    </div>

                </div>
                <div class="formDetailsWrapper">
                    <span>Requested By</span>
                    <div class="formDetailsTable">
                        <div class="formDetails">
                            <p>
                                <span>Employee Code :</span>
                                <strong>{{$orderdata->empcode}}</strong>
                            </p>
                        </div>
                        <div class="formDetails">
                            <p>
                                <span>Employee Name  :</span>
                                <strong>{{$orderdata->empname}} {{$orderdata->empaname}}</strong>
                            </p>
                        </div>
                        <div class="formDetails">
                            <p>
                                <span>Job Position :</span>
                                <strong><?php echo str_replace("_", " ", $orderdata->jobpos) ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="formTableWrapper">
                    <span>Approved By</span>
                    <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none;">
                        <thead>
                            <tr>
                                <th width="150px">Employee Code</th>
                                <th>Employee Name</th>
                                <th>Job Position</th>
                                <th>Sign</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($action_takers as $actor)
                            <tr>
                                <td>{{$actor->code}}</td>
                                <td>{{$actor->action_taker}}</td>
                                <td><?php echo str_replace("_", " ", $actor->jobpos); ?></td>
                                <td></td>

                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
                <div style="text-align: center; padding:10px 0;">
                    <input type="submit" id="btnsavepo" value="Submit" class="btnIcon lightGreenV3">
                </div>
            </form>
            <div class="commonLoaderV1"></div>
        </div>
        <div class="formFooter">
            <figure>
                <img src="{{ URL::asset('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
            </figure>
            <article>

                P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new

            </article>
        </div>
    </div>
</div>

<script>
    $(function () {
        
        $('#frmpo').submit(function(e) {
            $("#btnsavepo").attr("disabled","disabled");
            $(".commonLoaderV1").show();
            var blnConfirm = confirm("Are you sure submit ?");
            if(!blnConfirm){
                $("#btnsavepo").prop("disabled",false);
                $(".commonLoaderV1").hide();
                return false; 
            }
        });
        
        $('body').on('change', '.clsdespatch', function () {
            if($("#despatch_way").val() == 'Others'){
                $(".clsotherway").show();
            }else{
                $(".clsotherway").hide();
            }
        });
        
        $("#quotation_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy'
        });
        
        $('body').on('click', '.clsmodeofpay', function () {
            if($(this).val()=="Credit"){
                $(".clscreditdays").show();
            }else{
                $(".clscreditdays").hide();
            }
            
        });
        
        $("#delivery_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd-mm-yy'
        });
        
    });

</script>
@endsection