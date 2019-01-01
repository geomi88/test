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
        <h1>Edit <span>Performance</span></h1>
    </header>	

    <form action="{{ action('Organizationchart\PunchperformanceController@update') }}" method="post" id="frmeditperformance">
        <div class="fieldGroup" id="fieldSet1">
            <input type="hidden" name="editid" id="editid" value="{{$punchdata->id}}">
            <div class="custRow">
                <div class="custCol-6">
                    <div class="inputHolder">
                        <label>Employee</label>
                        <select id="employe_id" name="employe_id" class="chosen-select">
                            <option value=''>Select Employee</option>
                            @foreach ($employees as $employe)
                            <option value="{{ $employe->id }}" <?php if($punchdata->employe_id==$employe->id){ echo "selected";}?>>{{$employe->username}} : {{$employe->first_name}}</option>
                            @endforeach
                        </select>
                        <span class="commonError"></span>
                    </div>
                </div>
            </div>
            
            <div class="custRow">
                <div class="custCol-6">
                    <div class="empList">
                        <figure class="imgHolder">
                          <img src="{{$emp_det->profilepic}}" alt="image">
                        </figure>
                        <div class="details">
                            <b>{{$emp_det->username}} : {{$emp_det->first_name}}</b>
                            <p>Designation : <span><?php echo str_replace(" ", "_", $emp_det->name); ?></span></p>
                            <figure class="flagHolder">
                                <img src="{{ URL::asset('images/flags') }}/{{$emp_det->flag_name}}" alt="Flag">
                                <figcaption>{{$emp_det->country_name}}</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
            
            
                <div class="inputHolder clsRatingHolder">
                    <div class="custRow">
                        <label>Rating</label>
                        <div class="custCol-4">
                            <div class="commonCheckHolder chkexceptional"><label><i>A. </i><input name="rating" class="chkrating" value="1"  type="checkbox" <?php if($punchdata->rating==1){echo "checked";}?>><span></span><em>Exceptional (90 - 100%)</em></label></div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="commonCheckHolder chkeffective"><label><i>B. </i><input name="rating" class="chkrating" value="2"  type="checkbox" <?php if($punchdata->rating==2){echo "checked";}?>><span></span><em>Effective (70 - 90%)</em></label></div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="commonCheckHolder chkinconsistent"><label><i>C. </i><input name="rating" class="chkrating" value="3"  type="checkbox" <?php if($punchdata->rating==3){echo "checked";}?>><span></span><em>Inconsistent (50 - 70%)</em></label></div>
                        </div>
                    </div>
                    <div class="custRow">                    
                        <div class="custCol-4">
                            <div class="commonCheckHolder chkunsatisfactory"><label><i>D. </i><input name="rating" class="chkrating" value="4"  type="checkbox" <?php if($punchdata->rating==4){echo "checked";}?>><span></span><em>Unsatisfactory (40 - 50%)</em></label></div>
                        </div>
                    </div>
                    <div class="custRow">      
                        <div class="custCol-4">
                            <div class="commonCheckHolder chknotacceptable"><label><i>E. </i><input name="rating" class="chkrating" value="5"  type="checkbox" <?php if($punchdata->rating==5){echo "checked";}?>><span></span><em>Not Acceptable (Below 40%)</em></label></div>
                        </div>
                    </div>
                </div>
               
            </div>
            
            <div class="custRow">
                <div class="custCol-8">
                    <div class="inputHolder">
                        <label>Reason</label>
                        <textarea name="reason" id="reason" placeholder="Enter Reason">{{$punchdata->reason}}</textarea>
                    </div>
                </div>
            </div>
            <div class="custRow">
                <div class="custCol-4">
                    <input type="submit" value="Update" class="commonBtn bgGreen addBtn">
                </div>
            </div>

        </div>
    </form>	
</div>

<script>
    $(document).ready(function ()
    {
        $("#frmeditperformance").validate({
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
                            required: true,
                        },
               
                reason:
                        {
                            required: true,
                        }
            },
           
            messages: {
                employe_id: "Select employee",
                reason: "Enter reason"
            }
        });
        
        $('#employe_id').on("change", function () {
            var employe_id = $(this).val();
            if(employe_id!=''){
                $.ajax({
                    type: 'POST',
                    url: '../getempdata',
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
        
        $('body').on('click', '.chkrating', function() {
            $('.chkrating').prop("checked", false);
            $(this).prop("checked", true);
        });

    });


</script>
@endsection
