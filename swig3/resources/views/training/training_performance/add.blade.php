@extends('layouts.main')
@section('content')
<style>
    .empList{
        border: 1px;
        padding-bottom: 28px;
    }
</style>
<div class="innerContent">
    <header class="pageTitle">
        <h1>Add <span>Performance</span></h1>
    </header>	

    <form action="{{ action('Training\TrainingperformanceController@store') }}" method="post" id="frmaddperformance">
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <div class="commonCheckHolder">
                            <label>
                                <input type="radio" name="empType" id="chkmtgemp" value="1" checked class="mtgEmpchk">
                                <span></span>
                                <em>MTG Employee</em>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="custCol-4">
                    <div class="inputHolder">
                        <div class="commonCheckHolder">
                            <label>
                                <input type="radio" name="empType" id="chkguestemp" value="2" class="guestEmpchk">
                                <span></span>
                                <em>New Employee</em>
                            </label>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="custRow mtg_employees">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Employee</label>
                        <select id="employe_id" name="employe_id" class="chosen-select emporguest">
                            <option value=''>Select Employee</option>
                            @foreach ($employees as $employe)
                            <option value="{{ $employe->id }}" >{{$employe->username}} : {{$employe->first_name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>


            </div>
            
            <div class="custRow guestEmp" style="display: none;">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>New Employee</label>
                        <select id="guest_id" name="guest_id" class="chosen-select emporguest">
                            <option value=''>Select New Employee</option>
                            @foreach ($guests as $guest)
                            <option value="{{ $guest->guest_phone }}" >{{$guest->guest_phone}} : {{$guest->guest_name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>


            </div>
            
            <div class="custRow">
                <div class="custCol-6">
                    <div class="empList" style="display: none;">
                        
                    </div>
                </div>
            </div>
            
            <div class="inputHolder clsRatingHolder">
                <div class="custRow">
                    <label>Rating</label>
                    <div class="custCol-4">
                        <div class="commonCheckHolder chkexceptional"><label><i>A. </i><input name="rating" class="chkrating" value="1"  type="checkbox"><span></span><em>Exceptional (90 - 100%)</em></label></div>
                    </div>
                </div>
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="commonCheckHolder chkeffective"><label><i>B. </i><input name="rating" class="chkrating" value="2"  type="checkbox" checked><span></span><em>Effective (70 - 90%)</em></label></div>
                    </div>
                </div>
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="commonCheckHolder chkinconsistent"><label><i>C. </i><input name="rating" class="chkrating" value="3"  type="checkbox"><span></span><em>Inconsistent (50 - 70%)</em></label></div>
                    </div>
                </div>
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="commonCheckHolder chkunsatisfactory"><label><i>D. </i><input name="rating" class="chkrating" value="4"  type="checkbox"><span></span><em>Unsatisfactory (40 - 50%)</em></label></div>
                    </div>
                </div>
                <div class="custRow">
                    <div class="custCol-4">
                        <div class="commonCheckHolder chknotacceptable"><label><i>E. </i><input name="rating" class="chkrating" value="5"  type="checkbox"><span></span><em>Not Acceptable (Below 40%)</em></label></div>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-8">
                    <div class="inputHolder">
                        <label>Reason</label>
                        <textarea name="reason" id="reason" placeholder="Enter Reason"></textarea>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Create" class="commonBtn bgGreen addBtn">
                </div>
            </div>

        </div>
    </form>	
</div>

<script>
    $(document).ready(function ()
    {
        $("#frmaddperformance").validate({
            errorElement: "span",
            errorClass: "commonError",
            ignore: [],
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                var id = $(element).attr("id") + "_chosen";
                if ($(element).hasClass('valErrorV1')) {
                    $("#" + id).find('.chosen-single').addClass('chosen_error');
                }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                
                employe_id:
                        {
                            require_from_group: [1, ".emporguest"]
                        },
                guest_id:
                        {
                            require_from_group: [1, ".emporguest"]
                        },
               
                reason:
                        {
                            required: true,
                        }
            },
           
            messages: {
                employe_id: "Select employee",
                guest_id: "Select employee",
                reason: "Enter reason"
            }
        });
        
        $('#employe_id').on("change", function () {
            var employe_id = $(this).val();
            if(employe_id!=''){
                $.ajax({
                    type: 'POST',
                    url: 'getempdata',
                    data: 'employe_id=' + employe_id,
                    success: function (return_data) {
                        $('.empList').html(return_data);
                        $('.empList').show();
                    }
                });
            }else{
                $('.empList').html('');
                $('.empList').hide();
            }

        });
        
        $('#guest_id').on("change", function () {
            $('.empList').html('');
            $('.empList').hide();
        });
        
        $('body').on('click', '.chkrating', function() {
            $('.chkrating').prop("checked", false);
            $(this).prop("checked", true);
        });
        
        $(".mtgEmpchk").on('click', function () {
            $('.guestEmp').hide();
            $('.mtg_employees').show();
        });
        
        $(".guestEmpchk").on('click', function () {
            $('.mtg_employees').hide();
            $('.guestEmp').show();
        });

    });


</script>
@endsection
