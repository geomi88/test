@extends('layouts.main')
@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Create <span>Organization Chart</span></h1>
    </header>	
        <div class="fieldGroup" id="fieldSet1">
            <div class="custRow">
                <div class="custCol-4">
                    <div class="inputHolder">
                        <label>Chart Type</label>
                        <select  name="cmbtype" id="cmbtype" class="">
                            <option selected value=''>Select Type</option>
                            <option value="Employees">Based On Employees</option>
                            <option value="Job_Position">Based On Job Position</option>
                        </select>

                        <span class="commonError"></span>
                    </div>
                </div>
                
            </div>
             <div class="custRow" id="jobbased" style="display: none;">
                <div class="custCol-12">
                    <a href="{{ action('Organizationchart\OrganizationchartController@add') }}" id="btnCreateChart" class="commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
            <div class="custRow" id="empbased" style="display: none;">
                <div class="custCol-12">
                    <a href="{{ action('Organizationchart\OrganizationchartController@addjobwise') }}" id="btnCreateChart" class="commonBtn commonBtn bgGreen addBtn">Create</a>
                </div>
            </div>
            <div class="customClear"></div>
        </div>
</div>
<script>
    
    $("#frmmain").validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
            rules: {
                cmbcategory: {required: true},
            },
            messages: {
                cmbcategory: "Select Category",
            }
        });
        
         $('body').on('change', '#cmbtype', function () {
            
            if($("#cmbtype").val()=='Job_Position'){
                $("#jobbased").hide();
                $("#empbased").show();
            }else if($("#cmbtype").val()=='Employees'){

                $("#jobbased").show();
                $("#empbased").hide();
            }else{
                $("#jobbased").hide();
                $("#empbased").hide();
            }
            
        });
</script>
@endsection