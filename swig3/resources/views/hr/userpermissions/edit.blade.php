@extends('layouts.main')

@section('content')

<div class="innerContent">
    <a class="btnBack" href="{{ URL::to('hr/userpermissions')}}">Back</a>
    <header class="pageTitle">
        <h1>Modules List : <span>{{$employees->first_name}} {{$employees->alias_name}}</span></h1>
    </header>

    <div class="formListV1">
        <div class="formListV1Dtl" id="" style="">
            <form action="" method="post" id="previlegeForm">
                <input type="hidden" id="empid" name="empid" value="{{$empid}}">
                @foreach ($modules as $module)
                <div class="privilegeHolder">
                    <div class="privilegeTop">
                        <div class="commonCheckHolder">
                            <label>
                                <input type="checkbox" name="modules[]" value="{{$module['id']}}" <?php if($module['name']=="Calendar"){ echo "disabled='disabled'"." "."checked='checked'"; } else {echo (key_exists($module['id'], $user_modules)) ? "checked" : "" ;}?>>
                                <span></span>
                                <em>{{$module['name']}}</em>
                            </label>
                        </div>
                        <a href="javascript:void(0)" class="btnOpen" title="open"></a>
                    </div>
                    <div class="privilegeCont">
                        @foreach ($module['sub_modules'] as $sub_module)
                        <div class="custCol-4">
                            <div class="commonCheckHolder">
                                <label>
                                    <input type="checkbox" name="sub_modules[]" value="{{$sub_module['id']}}" <?php if($sub_module['name']=="To Do" || $sub_module['name']=="Create Plan" || $sub_module['name']=="View Plan" || $sub_module['name']=="History" || $sub_module['name']=="Assign Task List" || $sub_module['name']=="Track Task" || $sub_module['name']=="Add Suggestion"){ echo "disabled='disabled'"." "."checked='checked'"; } else{ echo (key_exists($sub_module['id'], $user_modules)) ? "checked" : "";} ?>>
                                    <span></span>
                                    <em>{{$sub_module['name']}}</em>
                                </label>
                                <?php if($sub_module['class_name']=="clsjobposition"){?>
                                
                                    <?php $arrSavedPositions = array();
                                    if (key_exists($sub_module['id'], $user_modules)) {
                                        $arrSavedPositions = explode(",", $user_modules[$sub_module['id']]['filter_by_job_position']);
                                    } ?>
                                
                                    <em class="PrevSettings" title="Job Positions">
                                        <span></span>
                                        <div class="prevToolTip">
                                            <input type="hidden" class="clshiden" name="jobpositions[{{$sub_module['id']}}]" value="<?php echo (key_exists($sub_module['id'], $user_modules)) ? $user_modules[$sub_module['id']]['filter_by_job_position'] : "" ?>">
                                            <a class="btnCloseV2"></a>
                                            <h5>Job Positions</h5>
                                            <div class="scrollManage">
                                                <?php foreach ($job_positions as $job){?>
                                                    <div class="commonCheckHolder">
                                                        <label>
                                                            <input type="checkbox" class="clsdesignations" value="{{$job->id}}" <?php echo (in_array($job->id, $arrSavedPositions)) ? "checked" : "" ?>>
                                                            <span></span>
                                                            <em><?php echo str_replace("_", " ", $job->name)?></em>
                                                        </label>
                                                    </div>
                                                <?php }?>
                                            </div>
                                            <a class="btnAction action bgGreen btnSavePositions" style="margin-top: 15px;">Save</a>
                                        </div>
                                    </em>
                                <?php }?>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="custRow">
                    <div class="custCol-4">
                        <a class="commonBtn bgBlue addPrev back4 " href="{{ URL::to('hr/userpermissions')}}">Back</a> 
                    </div>
                    <div class="custCol-8">
                        <button class="commonBtn bgGreen right addNext " id="btnModulesSave" type="button">Save Permissions</button> 
                    </div>
                </div>
                
            </form>
        </div>
    </div>
    <div class="commonLoaderV1"></div>
     
</div>

<script>


    $('#btnModulesSave').on('click', function() {
        
        $(".commonLoaderV1").show();
        //Creating an ajax method
        $.ajax({
            url: '../../userpermissions/updatemodules',
            //For file upload we use post request
            type: "POST",
            //Creating data from form 
            data: $("#previlegeForm").serialize(),
            success: function (data) {
                $(".commonLoaderV1").hide();
                window.location.href = '{{url("hr/userpermissions")}}';
            },
            error: function () {
                $(".commonLoaderV1").hide();
                window.location.href = '{{url("hr/userpermissions")}}';
            }
        });
        
//        $(".commonLoaderV1").hide();
    });
    
    $('body').on('click', '.btnSavePositions', function () {
        var strpositions='';
        $(this).parent().find('.clsdesignations').each(function() {
            if(this.checked){
                strpositions=strpositions+this.value+",";
            }
            
        });
        
        $(this).parent().find('.clshiden').val(strpositions);
    });
    
</script>
@endsection