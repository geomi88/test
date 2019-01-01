@extends('layouts.main')
@section('content')
<script>
    $(window).on('hashchange', function() {
        if (window.location.hash) {
            var page = window.location.hash.replace('#', ''); 
            if (page == Number.NaN || page <= 0) {
                return false;
            }else{
                getData(page);
            }
        }
    });
    function getData(page){
    
        var type = $('#ledgerType').val();
        var pagelimit = $('#page-limit').val();
        var search = $('#search_key').val();
        var year = $('#year').val();  
//        var quarter = $('#quarter').val();
        $.ajax(
        {
            url: '?page=' + page,
            type: "get",
            datatype: "html",
            data: {type: type,pagelimit:pagelimit,search:search,year:year},
            
            // {
            //     you can show your loader 
            // }
        })
        .done(function(data)
        {
          //  console.log(data);
            
            $(".list_results").empty().html(data);
            location.hash = page;
        })
        .fail(function(jqXHR, ajaxOptions, thrownError)
        {
              alert('No response from server');
        });
}

</script>
<div class="contentArea">

    <div class="innerContent ">
        <form id="frmdisplaybudget" method="POST">
        <header class="pageTitleV3">
            <h1>Budget Creation</h1>
        </header>
        <div class="custRow inputAreaWrapper ">
            <div class="custCol-4">
                <div class="inputHolder  ">
                    <label>Select Year</label>
                    <select id="year">
                         <?php 
                            $Year=date('Y');
                            for($startYear=$Year-1;$startYear<=$Year+5;$startYear++){
                        ?>
                            <option  <?php if($Year==$startYear){ echo "selected";}?> value='{{$startYear}}'>{{$startYear}}</option>
                        <?php }?>
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>
<!--            <div class="custCol-4">
                <div class="inputHolder  ">
                    <label>Select Quarter</label>
                    <?php $month = date("m"); ?>
                    <select id="quarter">
                        <option>Select Quarter</option>
                        <option <?php  
                        if ($month < 4) {
                            ?>
                            selected=""<?php $quarter=1;} ?> value=1>Q1</option>
                        <option <?php
                        if ($month > 3 && $month < 7) {
                            ?>
                            selected=""<?php $quarter=2; } ?> value=2>Q2</option>
                        <option<?php
                        if ($month > 6 && $month < 10) {
                            ?>
                            selected=""<?php $quarter=3; } ?> value=3>Q3</option>
                        <option<?php
                        if ($month > 9) {
                            ?>
                            selected=""<?php $quarter=4; } ?> value=4>Q4</option>
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>-->
            <div class="custCol-4">
                <div class="inputHolder  ">
                    <label>Select Ledger Type</label>
                    <select id="ledgerType" name="ledgerType">
                        <option value="">Select Ledger Type</option>
                        @foreach($cost_centres as $cost_centre)
                        <option value="{{$cost_centre}}">{{$cost_centre}}</option>
                        @endforeach
                        <option value="Asset">Asset</option>
                        <option value="Customer">Customer</option>
                        <option value="Employee">Employee</option>
                        <option value="Supplier">Supplier</option>
                        <option value="Inventory">Inventory</option>
<!--                        <option value="Income">Income</option>
                        <option value="Expense">Expense</option>-->
                        <option value="General Ledger">General Ledger</option>
                    </select>
                    <span class="commonError"></span>
                </div>
            </div>
        </div>
        <div class="listHolderType2">
            <div class="listTableTitle">
                <p id="show"></p> 
            </div>
            <div class="listTableContent">
                <div class="tbleSearchHolder">
                    <input type="text" placeholder="Search" id="search_key">
                </div>
                <div class="tbleListWrapper">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <thead class="headingHolder">
                            <tr>
                                <th id="ledger_name"></th>
                                <th id="primeunit" style="display:none">Primary Unit</th>
                                <th id="supplier_quantity" style="display:none">Quantity</th>
                                <th class="supplier_amount">Amount</th>
                            </tr>
                        </thead>
                        <div >
                            <tbody class="list_results">
                                @include('finance/budget_creation/budgetcreationresults')
                            </tbody>
                    </table>
                </div>
                        <div class="pagesShow">
                            <span>Showing 10 of 20</span>
                            <select id="page-limit">

                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="bottomBtnsHolder">
                            <input type="button" class="btnIcon btnSaveV3 lightGreenV3" id="btnSave" value="Save Go">
                            <div class="customClear "></div>
                        </div>
            </div>
        </div>
        </form>
    </div>
</div>
<div class="commonLoaderV1"></div>
</div>
<script>
    $(document).ready(function () {  
        var v = jQuery("#frmdisplaybudget").validate({
            rules: {
                ledgerType: {
                    required: true,
                },
            },
            messages: {
                ledgerType: "Select Ledger Type",
            },
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
        });
        
        $('#show').html('Ledger Listing');
        $('#ledger_name').html('Ledger Name');
        $('#ledgerType').change(function () { 
            $("#supplier_quantity").hide();
            $("#primeunit").hide();
            var type=$('#ledgerType').val();
            if(type==''){
                $('#show').html('Ledger Listing');
                $('#ledger_name').html('Ledger Name');
            }else{
                $('#show').html(type+' Listing');
                $('#ledger_name').html(type+' Name');
            }
            if(type=="Inventory"){
                $("#supplier_quantity").show();
                $("#primeunit").show();
            }
            search();
        });
//        $('#ledgerType').trigger('change');

        $('#year').change(function () {  
            search();
        });
//        $('#quarter').change(function () {
//            search();
//        });
        $('#page-limit').on("change", function () {
            search();
        });
        $('#search_key').bind('keyup', function () {
            search();
        });
         $(document).on('click', '.pagination a',function(event)
        {
            $('li').removeClass('active');
            $(this).parent('li').addClass('active');
            event.preventDefault();
            //var myurl = $(this).attr('href');
           var page=$(this).attr('href').split('page=')[1];
           getData(page);
        });
        $('#btnSave').click(function(){
            if (!$("#frmdisplaybudget").valid()) {
                return;
            }
            saveBudget();
        });
    });
    
    function search()
    {
        var type = $('#ledgerType').val();
        var year = $('#year').val();  
//        var quarter = $('#quarter').val();
        var pagelimit = $('#page-limit').val();
        var search = $('#search_key').val();
            $.ajax({
                type: 'POST',
                url: 'budgetcreation',
                data: {type: type,year:year,pagelimit:pagelimit,search:search},
                beforeSend: function () {
                    $(".commonLoaderV1").show();
                },
                success: function (return_data) {
                    if (return_data != '')
                    {
                        $('.list_results').html('');
                        $('.list_results').html(return_data);
                        $(".commonLoaderV1").hide();
                    }
                    else
                    {
                        $(".commonLoaderV1").hide();
                        $('.list_results').html('<p class="noData">No Records Found</p>');
                    }
                }
            });



    }
    function saveBudget(){ 
        var arrayList=[]; 
        var type = $('#ledgerType').val(); 
        var year = $('#year').val();
//        var quarter = $('#quarter').val();
        $('.budget_amount').each(function () {  
            var quantity='';
            var type_id=$(this).attr('id'); 
            if(type=='Inventory'){
                quantity = $('#quan_'+type_id).val(); 
            }
            var value=$(this).val(); 
            var budget_id=$(this).attr('rel'); 
            arrayList.push({type_id:type_id,value:value,budget_id:budget_id,quantity:quantity}); 
        });     
        $('#btnSave').attr('disabled','disabled');
            $.ajax({
                type: 'POST',
                url: 'addbudget',
                data: {arrayList: arrayList,type:type,year:year},
                beforeSend: function () {
                    $(".commonLoaderV1").show();
                },
                success: function (return_data) {
                    if (return_data.message != -1)
                    {                                               
                       toastr.success('Budget Succeccfully Created'); //getData(1);
                       if(typeof(return_data.rel)!='undefined'){
                           var retdata=return_data.rel;
                           for(var countC=0;countC<retdata.length;countC++ )
                           {
                              $( ".budget_amount" ).eq(countC ).attr('rel',retdata[countC]);                          
                           }
                       }
                       $(".pagination li:last-child a").trigger('click');
                       $(".commonLoaderV1").hide();
                    }
                    else
                    {
                        window.location.href="budgetcreation";
                    }
                }
            });
            $('#btnSave').removeAttr('disabled');

        
    }
</script>
@endsection