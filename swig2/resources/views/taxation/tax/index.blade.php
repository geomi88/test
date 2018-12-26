@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />

<div class="innerContent">
    <header class="pageTitle">
        <h1><span>Tax </span></h1>
    </header>	

    <form action="{{ url('taxation/store') }}" method="post" id="taxation">
        <div class="fieldGroup" id="fieldSet1">
           <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder bgSelect">
                         <label>Tax  </label>
                         <input type="text" id="tax_name" name="tax_name">
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
              <div class="custRow">
            <div class="custCol-2">
                 <div class="inputHolder bgSelect">
                                <select class="tax_type" name="tax_type" id="tax_type">
                                    <option value="">All</option>
                                        <?php for($i=0;$i<count($taxfunctions);$i++){?>
                                    <option value="{{$taxfunctions[$i]}}" ><?php echo ucwords(str_replace("_", " ", $taxfunctions[$i]))?></option>
                                        <?php }?>
                                   
                                </select>
                            </div>
              </div>
              </div>
            
            <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder bgSelect">
                         <label>Tax Percentage </label>
                        <input type="text" id="tax_percent" name="tax_percent">    
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
           
            
            
           <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder">
                        <label>Tax Applicable From</label>
                        <input type="text" name="tax_date" placeholder="Select Date" id="tax_date" >
                        <span class="commonError"></span>
                    </div>
                </div>
                     
            </div>
            
            
           
           
            
           
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Submit"  class="commonBtn bgGreen addBtn shift" id="pos_edit_submit">
                </div>
            </div>

        </div>
    </form>	
</div>

<div class="innerContent">
    <header class="pageTitle">
        <h1>Tax <span>List</span></h1>
    </header>
<!--    <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-12">
                     <a href="{{ action('Employee\EmployeeController@add') }}" class="right commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
            <div class="customClear"></div>
    </div>-->
    <a class="btnAction refresh bgRed" id="reset" href="#">Refresh</a>

    <div class="fieldGroup" id="fieldSet1">

        <div class="customClear"></div>
    </div>
    <div class="listHolderType1">
       
        <div class="listerType1 reportLister"> 

            <div id="postable">
            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                 <thead class="listHeaderTop">
                    <tr class="headingHolder">
                        <td>
                            Tax Type
                            
                        </td> 
                        <td>
                            Tax Percentage
                            
                        </td>
                         <td>
                            Affected From
                            
                        </td>
                        
<!--                       <td>
                            Status
                        </td>-->
                    </tr>
                </thead>

                <thead class="listHeaderBottom">
                
                    <tr class="headingHolder">
                       
                       
                        <td class="filterFields">
                            <div class="">
                            <div class="custCol-12">
                                <select class="tax_type" name="tax_type" id="tax_type">
                                    <option value="">All</option>
                                        @foreach ($tax_type as $tax_types)
                                        <option value="{{$tax_types->tax_id}}" <?php if($tax_id==$tax_types->tax_id){echo "selected";}?>>{{$tax_types->tax_type}}</option>
                                    @endforeach
                                   
                                </select>
                            </div>
                            </div>
                                

                        </td>
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <select class="tax_percent" >
                                        <option value="">Select</option>
                                        <option value=">">></option>
                                        <option value="<"><</option>
                                        <option value="=">=</option>
                                    </select>

                                </div>
                                <div class="custCol-6">
                                    <input type="text" id="tax_value" name="tax_value" >
                                </div>

                            </div>
                        </td>
                        
                        
                        
                       
                        
                        <td class="filterFields">
                            <div class="custRow">
                                <div class="custCol-6">
                                    <input type="text" id="start_date" name="start_date" value="">
                                </div>
                                

                            </div>
                        </td>
<!--                        <td>
                            
                            
                        </td>-->
                    </tr>
                      
                </thead>
              
                <tbody class="employee_list" id='employee_list'>
                  @include('taxation/tax/searchresults')
                </tbody>

            </table>
            </div>
            <div class="commonLoaderV1"></div>
        </div>

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
</div>

<script>
    $(document).ready(function (){
      
        
         $("#taxation").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
//                title: {required: {
//                            depends: function () {
//                                if ($.trim($(this).val()) == '') {
//                                    $(this).val($.trim($(this).val()));
//                                    return true;
//                                }
//                            }
//                        },},
                tax_name:{required: true},
                
                tax_percent:{required: true,number:true},
                tax_date:{required: true},
                tax_type:{required: true},
                
              
            },
            
            messages: {
               
                tax_name: "Please add the tax name",
                tax_percent: "Please enter the percentage",
                tax_date: "Add the tax applicable date",
                
               
            }
        });
        
        
        
          $("#tax_date").datepicker({
            changeMonth: true,
            changeYear: true,
           // minDate: '+1D',
            dateFormat: 'dd-mm-yy',
            //endDate: "today",
           // maxDate: "today",
            onSelect: function (selected) {
                var dt = new Date(selected);
               // dt.setDate(dt.getDate() + 1);
              //  $("#end_date").datepicker("option", "minDate", selected);
            }
        });

     
   
     
    });

    $(document).on('click', '.txt', function () {
    //    $("#container").css("overflow-x", "visible");
        //alert('Working with div id');
    });

  $('#page-limit').on("change", function () {
        // alert('dasd');
        search();

    });
     $('#start_date').on("change", function () {
        if ($('#start_date').val() !== '')
        {
          search();
        }
    });   
   
   $("#start_date").datepicker({
            changeMonth: true,
            changeYear: true, dateFormat: 'dd-mm-yy'
        });
        
     
     
     $('#tax_type').on('change', function () {
        search();
    });
    
     $('#tax_value').bind('keyup', function () {
         var tax_percent = $('.tax_percent').val();
         var tax_value = $('#tax_value').val();
         if(tax_percent!=""  && $.isNumeric(tax_value)){
            search();
            }
    });
    
    
      function search()
    {
        var tax_type = $('#tax_type').val();
        var tax_percent = $('.tax_percent').val();
        var tax_value = $('#tax_value').val();
        var start_date = $('#start_date').val();
     
        var pagelimit = $('#page-limit').val();
       
         $.ajax({
            type: 'POST',
            url: 'tax',
            data: {tax_type: tax_type,tax_percent:tax_percent,tax_value:tax_value, start_date:start_date,pagelimit: pagelimit},
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (return_data) {
                if (return_data != '')
                {
                    $('.employee_list').html('');
                    $('.employee_list').html(return_data);
                    $(".commonLoaderV1").hide();
                }
                else
                {
                    $(".commonLoaderV1").hide();
                    $('.employee_list').html('<p class="noData">No Records Found</p>');
                }
            }
        });



    }
</script>
@endsection