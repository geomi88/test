@extends('layouts.main')
@section('content')
<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('purchase/view_postatus')}}">Back</a>
    <header class="pageTitleV3">
        <h1>PO Details</h1>
    </header>

    <div class="inputAreaWrapper">
        <div class="custRow reqCodeDateHolder">
            <div class="custCol-6 ">
                <label class="f-size-18">PO Code : <span>{{ $po_details->order_code }}</span></label>
                <label class="f-size-18">Supplier : <span>{{ ucfirst($po_details->supplier_name) }}</span></label>
            </div>
            <div class="custCol-6 alignRight">
                <label class="f-size-18">PO Date : <span>{{ $po_details->created_at }}</span></label>
            </div>
        </div>

        <div class="custRow reqCodeDateHolder ">

            <div class="custCol-6 m-t-10">
                <label class="f-size-16" style="padding-bottom:0px;">PO Status </label>
            </div>

        </div>
        <div class="approverDetailsWrapper">
            <div class="tbleListWrapper">
                <table cellpadding="0" cellspacing="0" style="width: 100%;">
                    <thead class="headingHolder ">
                        <tr>
                            <th style="min-width: 135px;">Updated On</th>
                            <th>PO Status</th>
                            <th style="min-width: 135px;">Updated By</th>
                        </tr>
                    </thead>
                    <tbody id="load_po_status">
                        @forelse($prev_details as $Row)
                        <tr>
                            <td>{{ $Row->updated_on }}</td>
                            <td>{{ $Row->po_status }}</td>
                            <td>{{ $Row->done_by }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td></td>
                            <td>No Data</td>
                            <td></td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="customClear"></div>
        @if($po_details->rfq_exists == 'has_rfq')
        <div class="custRow reqCodeDateHolder ">

            <div class="custCol-6 m-t-10">
                <label class="f-size-16" >RFQ List</label>
            </div>
            <div class="accordionWrapper">
                @forelse($rfq_details as $row)
                <div class="accordion">
                    <div class="accdnTitle">
                        
                        <a href="{{ url::to('requisitions/rfq/view',['id'=> Crypt::encrypt($row->id)]) }}" target="_blank"><i></i>RFQ Number : {{ $row->rfq_code }}</a>
                        
                    </div>
                </div>
                @empty
                
                @endforelse
            </div>

        </div>
        <div class="customClear"></div>
        @endif
        @if($po_details->requisition_id)
        <div class="custRow reqCodeDateHolder">
            <div class="accordionWrapper">
                <div class="custCol-6 m-t-10">
                    <label class="f-size-16">Requisition List</label>
                </div>
                @forelse($req_details as $row)
                <div class="accordion">
                    <div class="accdnTitle">

                        <a href="{{ url::to('requisitions/payment_advice/purchase_requisition/view',['id'=> Crypt::encrypt($row->id)]) }}" target="_blank"><i></i>Requisition Code : {{ $row->requisition_code }}</a> <em>Payment Amount : <strong>{{ round($row->total_price,2) }}</strong></em>

                        <div class="iconInfoWrapper">
                            <a href="javascript:get_transaction_details('{{ $row->requisition_code }}')" class="btnTransaction  btnTooltip"><img src="http://localhost/mtg/public/images/iconTrancation.png" alt="Transaction"></a>
                            <div class="tooltipInfo">
                                <a href="javascript:void(0);" class="infoClose"></a>
                                <a href="javascript:void(0);" class="btnAction print bgGreen printtransaction">Print</a>
                                <div class="tbleListWrapper ">
                                    <div class="inputView totAmntHandle">
                                        <span>Requisition : <strong id="reqcode_{{ $row->id }}">{{ $row->requisition_code }}</strong></span>
                                        <span class="right">Total Amount : <strong id="reqamount_{{ $row->id }}"></strong></span>
                                    </div>
                                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                                        <thead class="headingHolder">
                                            <tr>
                                                <th>Date</th>
                                                <th>Payment Code</th>
                                                <th>Mode Of Payment</th>
                                                <th>Paid By</th>
                                                <th class="amountAlign">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="history_{{ $row->id }}">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                
                @endforelse
            </div>
        </div>
        @endif
        <div class="customClear"></div>
        <div class="transactionprint" style="display: none;">
        <table border="0" cellpadding="5" width="100%" height="100%" border="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif; font-size:12px;color:#000;border:3px solid #760000;">
            <tr>
                <td>
                    <span style="color:#760000; font-size:25px;font-weight:bold; display:block;padding-bottom:15px;">Transaction History</span>
                    <p style="padding-bottom:5px;color:#000;"><strong style=padding-right:5px;>Requisition Code :</strong><span id="printcode">{{ $row->requisition_code }}</span></p>
                    <p style="margin: 0;"><strong style="padding-right:5px;color:#000;"> Total Amount :</strong><span id="printamount"></span></p>
                </td>
                <td style="text-align:right;vertical-align:top;">
                    <img src="{{ URL::asset('images/imgImtiyazatLogo.png')}}" src="Imtiyazat Al Riyada Est." style="width:150px;">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table cellpadding="3" width="100%" height="100%" border="3" bordercolor="#760000" cellspacing="0" style=" -webkit-appearance: none;-moz-appearance:none;appearance:none;">
                        <thead style="padding:5px;background:#760000;font-size:14px;font-weight:bold;color:#fff;text-align:left;">
                            <tr>
                                <th width="50px;">Date </th>
                                <th width="70px;">Payment Code</th>
                                <th width="60px;">Mode of Payment</th>
                                <th width="250px;">Paid By </th>
                                <th width="100px;" style="text-align:right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="printbody">

                        </tbody>

                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom:2px solid #760000; height:10px;"></td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:10px;text-align:center;">
                    <img src="{{Url('images/imgPartners.png')}}" style="max-width:65%;" alt="Partners">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding:5px 0 10px; color:#760000;text-align:center;">
                    P.O.Box: 108500 Jeddah 21351, Saudi Arabia, Phone: +966 12 698 5556, Fax: +966 12 639 7878, http://www.moroccantaste.com.sa/new
                </td>
            </tr>
        </table>
    </div>
    </div>
</div>

<script>

   function get_transaction_details(data){
       $.post("{{ url('requisitions/gettransactionhistory') }}",{requisition_code:data},function(s_data){
           $("#history_"+s_data.requisition.req_id).html(s_data.history);
           $("#reqamount_"+s_data.requisition.req_id).html(s_data.requisition.totalformated);
           $("#printamount").html(s_data.requisition.totalformated);
           $("#printbody").html(s_data.printhtml)   ;
       })
    }
    $('.printtransaction').click(function () {
            win = window.open('', 'Print', 'width=720, height=1018');
            win.document.write($('.transactionprint').html());
            win.document.close();
            win.print();
            win.close();
            return false;
        });

</script>
@endsection

