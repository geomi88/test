@extends('layouts.main')
@section('content')
<script>
  $(document).ready(function()
{
     $(document).on('click', '.pagination a',function(event)
    {
        $('li').removeClass('active');
        $(this).parent('li').addClass('active');
        event.preventDefault();
        //var myurl = $(this).attr('href');
       var page=$(this).attr('href').split('page=')[1];
       getData(page);
    });
    $('.print').click(function () {
        
       
        var pageTitle = 'Page Title',
        
        stylesheet = '//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css',
        win = window.open('', 'Print', 'width=500,height=300');
//        win.document.write('<style>.headingHolder{color:blue;}</style><html><head><title>' + pageTitle + '</title>' +
//            '<link rel="stylesheet" href="' + stylesheet + '">' +
//            '</head><body>' + $('.pos')[0].outerHTML + '</body></html>');
        win.document.write('<style>.paginationHolder {display:none;} .actionBtnSet{display:none;} .listerType1 tr:nth-of-type(2n) {background: rgba(0, 75, 111, 0.12) none repeat 0 0;}</style>'+
	
			'<div style="text-align:center;"><h1>Top Cashier Cash Collections</h1></div><table style="width: 100%; border: 3px solid #3088da" cellspacing="0" cellpadding="0" class="listerType1">'+
				'<thead style="background: rgba(0, 0, 0, 0) linear-gradient(to bottom, rgba(109, 179, 242, 1) 0%, rgba(84, 163, 238, 1) 50%, rgba(54, 144, 240, 1) 51%, rgba(30, 105, 222, 1) 100%) repeat  0 0;">'+
					'<tr class="headingHolder">'+
						'<td style="padding:10px 0;color:#fff;"> Date</td>'+
						'<td style="padding:10px 0;color:#fff;"> Employee Code</td>'+
						'<td style="padding:10px 0;color:#fff;"> Supervisor Name </td>'+
						'<td style="padding:10px 0;color:#fff;"> Amount </td>'+
						
					'</tr>'+
				'</thead>'+ $('.pos')[0].outerHTML +'</table>');    
        win.document.close();
        win.print();
        win.close();
        return false;
    });
    $('.saveDoc').click(function () {
      
//       svepdf();
       
        
    });
});  
     
    </script>

<div class="contentArea">

    <div class="innerContent">
        <form id="transfer_cash_collection" action="{{ url('branchsales/cash_collection/exporttopdf') }}" method="post">
        <header class="pageTitle">
            <h1>Top Cashier Cash Collections</h1>
        </header>
         <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>
    <a class="btnAction print bgGreen" href="#">Print</a>
    <a class="btnAction saveDoc bgBlue" href="#" onclick="savetopdf()">Save</a>
    <!--<a class="btnAction savedirectDoc bgBlue" href="{{ route('export',['download'=>'pdf','searchkey'=>'']) }}">Save</a>-->
    <!--<a class="btnAction savedirectDoc bgBlue"  id="btnExport" onclick="savetopdf()">Save</a>-->
    <div class="fieldGroup" id="fieldSet1">

        <div class="customClear"></div>
    </div>
            <div class="listHolderType1">
                
                <div class="listerType1 not_selected_pos"> 
                    <table style="width: 100%;" cellspacing="0" cellpadding="0">
                        <thead class="listHeaderTop">
                            <tr class="headingHolder">
                                <td></td>
                                <td>Date</td>
                                <td>Employee Code</td>
                                <td>Supervisor Name</td>
                                <td>Amount</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                         <thead class="listHeaderBottom">
                
                    <tr class="headingHolder">
                         <td></td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <input type="text" id="start_date" name="start_date" value="">
                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="end_date" name="end_date" value="">
                                </div>

                            </div>
                        </td>
                       <!-- <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search" name="searchkey">
                                </div>
                            </div>
                        </td>-->
                        <td class="filterFields">
                             </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-12">
                                    <input type="text" id="search" name="searchkey">
                                </div>
                            </div>
                        </td>
                       
                       
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="aorder" name="aorder">
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="aamount" name="aamount" >
                                </div>

                            </div>
                        </td>
                        
                        
                     

                        <td></td>
                    </tr>
                      
                </thead>
                        <tbody class="pos" id='pos'>
                             @include('branchsales/cash_collection/cash_collection_result')
                           
                            
                        </tbody>
                    </table>
                    <div class="commonLoaderV1"></div>
                </div>					
            </div>
            

            
               
                    <div class="bankPay">
                        <div class="custRow">
                            <div class="custCol-4">
                                <div class="inputHolder">
                                    <label>Select Bank</label>
                                    <select name="bank_id" id="bank_id">
                                        <option value=''>Select Bank</option>
                                        @foreach ($banks as $bank)
                                        <option value="{{$bank->id}}">{{$bank->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="custCol-4">
                                <div class="inputHolder">
                                    <label>Reference Number</label>
                                    <input type="text" name="ref_no" id="ref_no" placeholder="Enter Reference Number">
                                </div>
                            </div>
                        </div>
                        <input value="Submit" class="commonBtn bgRed addBtn" type="button" id="btnSaveTransfer">
                    </div>
                </form>
                </div>
            </div>
            

        </section>
    </div>
</div>
<script>
$(function () {        

    var s = jQuery("#transfer_cash_collection").validate({
        rules: {

            bank_id: {
                required: true,
            },
            ref_no: {
                required: {
                            depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                        },
            },

        },
        messages: {
            bank_id: "Select Bank",
            ref_no: "Enter Reference Number",
        },
        // submitHandler: function() {  form.submit(); },  
        errorElement: "span",
        errorClass: "commonError",
        highlight: function (element, errorClass) {
            $(element).addClass('valErrorV1');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass("valErrorV1");
        },
    });

    $('#btnSaveTransfer').click(function() {
        var strCollectionIds = '';
        strCollectionIds=$('input:checkbox.chkcashcollection:checked').map(function () {return this.id;}).get();

        if(strCollectionIds==''){
            alert("Please select atleast one checkbox");
            return;
        }
        
        if(!$("#transfer_cash_collection").valid()){
            return;
        }
        
        var blnConfirm = confirm("Are you sure to submit");
        if(blnConfirm){
            $.ajax({
                type: 'POST',
                url: '../cash_collection/transferfund',
                data: '&strCollectionIds=' + strCollectionIds +
                        '&bank_id=' + $("#bank_id").val() +
                        '&ref_no=' + $("#ref_no").val(),
                success: function (return_data) {
                    window.location.href = '{{url("branchsales/cash_collection/topcashierfund")}}';
                },
                error: function (return_data) {
                    window.location.href = '{{url("branchsales/cash_collection/topcashierfund")}}';
                }
            });
        }

    });

});
    
    $("#reset").click(function(){
        window.location.href = '{{url("branchsales/cash_collection/topcashierfund")}}';
               
       
    });
    
    function savetopdf(){
      
        
         document.getElementById("transfer_cash_collection").submit();

 }
    
   
    
     $(function () {
        $('.commonLoaderV1').hide();
        $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        $("#end_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        }).datepicker("setDate", new Date());
  
  
            
    });
    $('#search').bind('keyup', function () {
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
    
     $('.aorder').on("change", function () {
         var aamount = $('#aamount').val();
        if ($('.aorder').val() !== '' && $.isNumeric(aamount))
        {

            search();
        }
    });
    $('#aamount').bind('keyup', function () {
        var aorder = $('.aorder').val();
         var aamount = $('#aamount').val();
         if(aorder!=""  && $.isNumeric(aamount)){
            search();
            }
        
        
       // search();
    });
    
       function search()
    {
        // alert('asd');
       
        var searchkey = $('#search').val();
        var aorder = $('.aorder').val(); 
        var aamount = $('#aamount').val();
        var startdate = $('#start_date').val();
        var enddate = $('#end_date').val();
        var searchable = 'YES';


        $.ajax({
            type: 'POST',
            url: 'topcashierfund',
            data: { searchkey: searchkey, aorder: aorder, aamount: aamount, startdate: startdate, enddate: enddate},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                   // alert(return_data);
                    $('.pos').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.pos').html('<p class="noData">No Records Found</p>');
                }
            }
        });



    }
</script>
@endsection
