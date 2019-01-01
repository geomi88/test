@extends('layouts.main')
@section('content')
<script type="text/javascript" src="{{ URL::asset('js/jquery.timepicker.js') }}"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('css/jquery.timepicker.css') }}" />

<div class="innerContent">
    <header class="pageTitle">
        <h1> <span></span></h1>
    </header>	

    <form action="{{ url('operation/pos_supervisor_edit/store') }}" method="post" id="pos_edit_form">
        <div class="fieldGroup" id="fieldSet1">
           <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder bgSelect">
                         <label>Branch Code  </label>
                            <select  name="branch_editid" id="branch_editid" class="">
                                <option selected value=''>Select Branch</option>
                                @foreach ($branches as $branch)
                                <option value='{{ $branch->id }}'>{{ $branch->branch_code.'-'.$branch->name}}</option>
                                @endforeach
                            </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder bgSelect">
                         <label>Shift </label>
                            <select  name="job_shiftid" id="job_shiftid" class="edit_branch_shifts" >

                            </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
           
            
            
           
            <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder">
                        <label>Choose Date</label>
                        <input type="text" name="edit_date" placeholder="Select Date" id="edit_date" >
                        <span class="commonError"></span>
                    </div>
                </div>
                
              
                
            </div>
            
             <div class="custRow">
                <div class="custCol-2">
                    <div class="inputHolder cashierSelect">
                         <label>Choose Supervisor </label>
                            <select  name="cashier_id" id="cashier_id"  >

                            </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
           
            
           <input type="hidden" name="cashier" id="cashier">
           
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create"  class="commonBtn bgGreen addBtn shift" id="pos_edit_submit">
                </div>
            </div>

        </div>
    </form>	
</div>
<script>
    $(document).ready(function (){
       $('.cashierSelect').hide();
         
        var timepicker = $('#start_time').timepicker({'timeFormat': 'H:i', 'disableTextInput': true});
        $('#end_time').timepicker({'timeFormat': 'H:i', 'disableTextInput': true});

        $("#pos_edit_form").validate({
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
                branch_editid:{required: true},
                
                edit_date:{required: true},
                job_shiftid:{required: true},
              
            },
            
            messages: {
               
                branch_editid: "Choose a Branch",
                job_shiftid: "Choose a Shift",
                edit_date: "Choose a Date",
               
            }
        });
        
        
        
        $("#edit_date").datepicker({
            changeMonth: true,
            changeYear: true,
           // minDate: '+1D',
            dateFormat: 'dd-mm-yy',
            endDate: "today",
            maxDate: "today",
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

   $("#branch_editid").change(function () {

            var branch_id = $('#branch_editid').val();
         //   alert(branch_id);
            if (branch_id)
            {
                $.ajax({
                    type: 'POST',
                    url: 'pos_supervisor_edit/show_shifts',
                    data: 'branch_id=' + branch_id,
                    success: function (return_data) {

                        $('.edit_branch_shifts').html(return_data);
                    }
                });
            }
            else
            {
                $('.edit_branch_shifts').html('');
            }


        });
        
        
         
      $("#edit_date").datepicker({ dateFormat: 'dd-mm-yy',endDate: "today",
            maxDate: "today",
          onSelect: supervisorSelect});
      
         function supervisorSelect(){
            
             var branch_id = $('#branch_editid').val();
             var shift_id = $('#job_shiftid').val();   
             var edit_date = $('#edit_date').val();
       
              
             if(branch_id!="" && shift_id!="" && edit_date!=""){
              
              $.ajax({
                    type: 'POST',
                    url: 'pos_supervisor_edit/show_supervisors',
                    data: {branch_id: branch_id,shift_id:shift_id,edit_date:edit_date},
                    success: function (return_data) {
                       console.log(return_data);
                        
                       if(return_data.cashier_id== 0 && return_data.select_status== 0 && return_data.options== 0) {
                           $('.cashierSelect').hide(); 
                            $('#cashier').val('');
                            $('#cashier_id').html('');
                       }else if(return_data.cashier_id!= 0 && return_data.select_status== 0 && return_data.options== 0 ){
                           $('.cashierSelect').hide(); 
                         $('#cashier_id').html('');
                           $('#cashier').val(return_data.cashier_id);
                       }else{
                            $('.cashierSelect').hide(); 
                             $('#cashier_id').html('');
                               $('#cashier').val('');
                          // $('.cashierSelect').show();
                            // $('#cashier_id').html(return_data.options);
                             // $('#cashier').val('');
                       }
                        
                        
                        
                        
                        
                     /*   if(return_data== 0){
                            $('.cashierSelect').hide(); 
                        }else{
                             $('.cashierSelect').show();
                             $('#cashier_id').html(return_data);
                        }*/
                        
                    }
                       
                });
              
                  
        }else{
            
              $('.cashierSelect').hide();
            $('#cashier_id').html('');
        }
         }
         
        
</script>
@endsection