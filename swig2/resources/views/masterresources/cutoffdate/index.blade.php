@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />

<div class="innerContent">
    <header class="pageTitle">
        <h1> <span>Cut Off Date</span></h1>
    </header>	

    <form action="{{ url('masterresources/cutoffdate/store') }}" method="post" id="cut_off_date">
        <div class="fieldGroup" id="fieldSet1">
           
           
           
            
            
           <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder">
                        <label>Cut Off Date</label>
                        <input type="text" name="cutoffdate" placeholder="Select Date" id="cutoffdate" >
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



<script>
    $(document).ready(function (){
      
        
         $("#cut_off_date").validate({
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
              
                cutoffdate:{required: true},
                
              
            },
            
            messages: {
               
              
                cutoffdate: "Please choose a cut off date",
                
               
            }
        });
        
        
        
          $("#cutoffdate").datepicker({
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

    
</script>
@endsection