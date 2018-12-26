@extends('layouts.main')
@section('content')
<style>
    

    </style>
<script>
   
    $(document).ready(function ()
    {
 
        $('.print').click(function () {
            
            var pageTitle = 'Page Title',
                stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
                win = window.open('', 'Print', 'height='+screen.height,'width='+screen.width);
                win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>'+
                                '<div style="text-align:center;"><h1>Collection Details</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
                                    '<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
                                        '<tr class="headingHolder">'+
                                         '<td style="padding:10px 0;color:#fff;"></td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Date</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Branch Name</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Shift</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Total Sale</td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Collected Cash </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Credit sale </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Bank Sale </td>'+ 
                                            '<td style="padding:10px 0;color:#fff;"> Meal Consumption </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Difference </td>'+
                                            '<td style="padding:10px 0;color:#fff;"> Reason </td>'+
                                        '</tr>'+
                                    '</thead>'+ $('.resultbody')[0].outerHTML +'</table>');
            win.document.close();
            win.print();
            win.close();
            return false;
        });
    });
    
  


</script>
<div class="contentArea">
    <a class="btnAction action bgGreen" href="{{ URL::to('branchsales/cash_collection/topcashierfund')}}">Back</a>
<div class="innerContent">
 <form id="transfer_detail_collection" action="{{ url('branchsales/cash_collection/collectionexporttopdf') }}" method="post">
       
        <h4 class="blockHeadingV1">Collection Details</h4>
        <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
        <a class="btnAction print bgGreen" href="#">Print</a>
        <a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>
           
                <input type="hidden" id="cashcollectionid" name="cashcollectionid"  value="{{$cashcollectionid}}">
                <input type="hidden" id="posids" name="posids" value="{{$posids}}">
            <div class="selected_pos">
            <div class="listHolderType1">
                

                <div class="listerType1"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td>Collection Status</td>
                                <td>Date</td>
                                <td>Branch Name</td>
                                <td>Shift</td>
                                <td>Total Sale</td>
                                <td>Collected Cash</td>
                                <td>Credit sale</td>
                                <td>Bank Sale</td>
                                <td>Meal Consumption</td> 
                                <td>Difference</td>
                                <!--<td>Collection Status <br><input type="checkbox" id="selectAll" class="chkallsales"> Select all</td>-->
                                
                                <td>Reason</td>
                            </tr>
                        </thead>
                        
                        <thead class="listHeaderBottom">

                            <tr class="headingHolder">
                                <td>
                                    <label><input type="checkbox" id="selectAll" class="chkallsales"> Select all</label>

                                </td>
                                <td class="filterFields">
                                    <div class="custRow">
                                        <div class="custCol-6">
                                            <input type="text" id="start_date" name="start_date" value="" placeholder="Start Date">
                                        </div>
                                        <div class="custCol-6">
                                            <input type="text" id="end_date" name="end_date" value="End Date">
                                        </div>

                                    </div>
                                </td>
                                <td>
                                    <div class="custCol-12">
                                        <select class="branch" name="branch" id="branch">
                                            <option value="">All</option>
                                            @foreach ($branch_names as $branch_name)
                                            <option value="{{$branch_name->branch_id}}">{{$branch_name->branch_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </td>
                                <td class="">
                                    <div class="custRow">
                                        <div class="custCol-12">
                                            <select class="shift" name="shift" id="shift">
                                                <option value="">All</option>
                                                @foreach ($shift_names as $shift_name)
                                                <option value="{{$shift_name->jobshift_id}}">{{$shift_name->jobshift_name}}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                    </div>
                                </td>
                               
                                <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="torder" name="torder" id="torder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="tamount" name="tamount" >
                                </div>

                            </div>
                        </td>
                        
                                    <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="ccorder" name="ccorder" id="ccorder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="ccamount" name="ccamount" >
                                </div>

                            </div>
                        </td>
                                
                         <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="crorder" name="crorder" id="crorder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="cramount" name="cramount" >
                                </div>

                            </div>
                        </td>
                               <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="border" name="border" id="border">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="bamount" name="bamount" >
                                </div>

                            </div>
                        </td>
                           <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="morder" name="morder" id="morder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="mamount" name="mamount" >
                                </div>

                            </div>
                        </td>
                                 <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="dorder" name="dorder" id="dorder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="damount" name="damount" >
                                </div>

                            </div>
                        </td>
                                <td></td>
                            </tr>

                        </thead>
                        
                        <tbody class="resultbody">
                            @include('branchsales/cash_collection/filter_result')
                        </tbody>
                        
                    </table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>

                <h4 class="blockHeadingV1 spacingBtm2 alignRight" id="total">Total : <?php echo $total_cash ?></h4>	
            </div>
                <div class="custRow">
                    <input value="Submit" class="commonBtn bgRed addBtn" type="button" id="btnSaveCollection">
                </div>
            </form>
    
</div>
</div>
<script>

$(document).ready(function ()
{
    $('body').on('click', '.chkpossales', function() {
        
        if($('input:checkbox.chkpossales:not(:checked)').length==0){
            $('#selectAll').prop("checked", true);
        }else{
            $('#selectAll').prop("checked", false);
        }
      
    });
    
    $('#selectAll').click(function() {
        
        if ($('#selectAll').prop("checked")) {
            $('.chkpossales').prop("checked", true);
        } else {
            $('.chkpossales').prop("checked", false);
        }
      
    });
    
    $('#btnSaveCollection').click(function() {
        var arrSalesId = '';
       arrSalesId=$('input:checkbox.chkpossales:checked').map(function () {return this.id;}).get();
        
        if(arrSalesId==''){
            alert("Please select atleast one checkbox");
            return;
        }
        
        var blnConfirm = confirm("Are you sure to submit");
        if(blnConfirm){
            $.ajax({
                type: 'POST',
                url: '../collect_cash',
                data: '&arrSalesId=' + arrSalesId + '&posids=' + $("#posids").val()+
                    '&cashcollectionid='+$("#cashcollectionid").val(),
                success: function (return_data) {
                    window.location.reload();
                },
                error: function (return_data) {
                    window.location.href = '{{url("branchsales/cash_collection/topcashierfund")}}';
                }
            });
        }
       
    });
    
    $("#start_date").datepicker({
        changeMonth: true,
        changeYear: true, dateFormat: 'dd-mm-yy'
    });

    $("#end_date").datepicker({
        changeMonth: true,
        changeYear: true, dateFormat: 'dd-mm-yy'
    }).datepicker("setDate", new Date());
        
    $('.branch').on("change", function () {
        search();
    });
    $('.shift').on("change", function () {
        search();
    });
   
   $('#start_date').on("change", function () {
        if ($('#start_date').val() !== '')
        {

            search();
        }
    });
    $('#end_date').on("change", function () {
        if ($('#end_date').val() !== '')
        {

            search();
        }
    });
    
      $('.torder').on("change", function () {
         var tamount = $('#tamount').val();
        if ($('.torder').val() !== '' && $.isNumeric(tamount))
        {

            search();
        }
    });
    $('#tamount').bind('keyup', function () {
        var torder = $('.torder').val();
         var tamount = $('#tamount').val();
         if(torder!=""  && $.isNumeric(tamount)){
            search();
            }
      
    });
    
     $('.ccorder').on("change", function () {
         var ccamount = $('#ccamount').val();
        if ($('.ccorder').val() !== '' && $.isNumeric(ccamount))
        {

            search();
        }
    });
    $('#ccamount').bind('keyup', function () {
        var ccorder = $('.ccorder').val();
         var ccamount = $('#ccamount').val();
         if(ccorder!=""  && $.isNumeric(ccamount)){
            search();
            }
      
    });
    
    $('.crorder').on("change", function () {
         var cramount = $('#cramount').val();
        if ($('.crorder').val() !== '' && $.isNumeric(cramount))
        {

            search();
        }
    });
    $('#cramount').bind('keyup', function () {
        var crorder = $('.crorder').val();
         var cramount = $('#cramount').val();
         if(crorder!=""  && $.isNumeric(cramount)){
            search();
            }
      
    });
    
    $('.border').on("change", function () {
         var bamount = $('#bamount').val();
        if ($('.border').val() !== '' && $.isNumeric(bamount))
        {

            search();
        }
    });
    $('#bamount').bind('keyup', function () {
        var border = $('.border').val();
         var bamount = $('#bamount').val();
         if(border!=""  && $.isNumeric(bamount)){
            search();
            }
      
    });
    
     $('.dorder').on("change", function () {
         var damount = $('#damount').val();
        if ($('.dorder').val() !== '' && $.isNumeric(damount))
        {

            search();
        }
    });
    $('#damount').bind('keyup', function () {
        var dorder = $('.dorder').val();
         var damount = $('#damount').val();
         if(dorder!=""  && $.isNumeric(damount)){
            search();
            }
      
    });
    
    $('.morder').on("change", function () {
         var mamount = $('#mamount').val();
        if ($('.morder').val() !== '' && $.isNumeric(mamount))
        {

            search();
        }
    });
    $('#mamount').bind('keyup', function () {
        var morder = $('.morder').val();
         var mamount = $('#mamount').val();
         if(morder!=""  && $.isNumeric(mamount)){
            search();
            }
      
    });
    
    function search()
    {

        var branch = $('.branch').val();
        var shift = $('.shift').val();
 
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var posids = $('#posids').val();
        var torder = $('.torder').val(); 
        var tamount = $('#tamount').val();
        var ccorder = $('.ccorder').val(); 
        var ccamount = $('#ccamount').val();
        var crorder = $('.crorder').val(); 
        var cramount = $('#cramount').val();
        var border = $('.border').val(); 
        var bamount = $('#bamount').val();
        var dorder = $('.dorder').val(); 
        var damount = $('#damount').val();
        var morder = $('.morder').val(); 
        var mamount = $('#mamount').val();
        
//        var pagelimit = $('#page-limit').val();
        var searchable = 'YES';

        $.ajax({
            type: 'POST',
            url: '../filter_result',
            data: {branch: branch, shift: shift, startdate: startdate, enddate: enddate,posids:posids,torder:torder,tamount:tamount,ccorder:ccorder,ccamount:ccamount,crorder:crorder,cramount:cramount,border:border,bamount:bamount,dorder:dorder,damount:damount,morder:morder,mamount:mamount},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                   // alert(return_data);
                    $('.resultbody').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.resultbody').html('<p class="noData">No Records Found</p>');
                }
            }
        });
    }
    
    $('#reset').click(function () {
            $(':input')
                    .not(':button, :submit, :reset, :hidden')
                    .val('')
                    .removeAttr('checked')
                    .removeAttr('selected');

            search();
        });
});

 function savetopdf(){
      
        
         document.getElementById("transfer_detail_collection").submit();

 }

</script>
@endsection