@extends('layouts.main')

@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Corporate Customer</span></h1>
    </header>

    <div class="formListV1 clsparentdiv">
        <form action="" method="post" id="customer_registration" enctype="multipart/form-data">
            <div class="formListV1Dtl" id="step1">
                <div class="formRange">
                    <ul>
                        <li class="selected"><span>1</span></li>
                        <li><span>2</span></li>
                        <li><span>3</span></li>
                    </ul>
                </div>
                <h3>Step 1</h3>
                <div class="fieldGroup" id="fieldSet1">
                    <input type="hidden" name="customer_id" id="customer_id" value="{{$elegantcustomer->id}}">
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Sales By</label>
                                <select class="chosen-select" name="created_by_id" id="created_by_id">
                                    <option value=''>Choose Employee</option>
                                    @foreach ($employees as $eachEmployee)
                                    <option value='{{ $eachEmployee->id }}' <?php if($elegantcustomer->created_by == $eachEmployee->id) {?>selected="selected"<?php } ?>>{{ $eachEmployee->username}}: {{$eachEmployee->first_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
<!--                        <div class="inputHolder">
                            <label>Created By</label>
                        </div>
                        <input type="hidden" name="created_by_id" id="created_by_id" value="{{$elegantcustomer->created_by}}">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Employee Code</label>
                                <input type="text" name="employee_code" id="employee_code" readonly="readonly" value="{{$elegantcustomer->empCode}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Employee Name</label>
                                <input type="text" name="employee_name" id="employee_name" readonly="readonly" value="{{$elegantcustomer->empName}}">
                                <span class="commonError"></span>
                            </div>
                        </div>-->

                    </div>
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Account Group</label>
                                <select class="chosen-select account-group" name="account_group_hidden" id="account_group_hidden">
                                    <option value=''>Select Group</option>
                                    @foreach ($parentgroups as $parentgroup)
                                    <option value='{{ $parentgroup->id }}' <?php if($parentgroup->name == 'Customer') {?>selected="selected"<?php } ?>>{{ $parentgroup->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <input type="hidden" name="account_group" id="account_group">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Customer Code</label>
                                <input type="text" name="customer_code" id="customer_code" readonly="readonly" value="{{$elegantcustomer->customer_code}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>

                    <div class="custRow">

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Customer Name (English)</label>
                                <input type="text" name="name_english" id="name_english" autocomplete="off" placeholder="Enter Customer Name" value="{{$elegantcustomer->name_english}}" maxlength="200">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Customer Name (Arabic)</label>
                                <input type="text" name="name_arabic" id="name_arabic"  autocomplete="off" placeholder="أدخل اسم العميل" value="{{$elegantcustomer->name_arabic}}" maxlength="200">
                                <span class="commonError"></span>
                            </div>
                        </div>


                    </div>

                    <div class="custRow">
                        <div class="custCol-5">
                            <div class="inputHolder bgSelect fieldMargin">
                                <label>Customer Type</label>                                        
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="customer_type" name="customer_type" id="customer_type" value="0" <?php if ($elegantcustomer->customer_type==0) { echo "checked";} ?> >
                                        <span></span>
                                        <em>Saudi</em>
                                    </label>
                                </div>
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="customer_type" name="customer_type" id="customer_type" value="1" <?php if ($elegantcustomer->customer_type==1) { echo "checked";} ?> >
                                        <span></span>
                                        <em>International</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder nat" <?php if ($elegantcustomer->customer_type!=1) { ?> style="display: none;" <?php } ?>>
                                <label>Choose Nationality</label>
                                <select class="chosen-select" name="nationality" id="nationality">
                                    <option value=''>Choose Nationality</option>
                                    @foreach ($countries as $country)
                                    <option value='{{ $country->id }}' <?php echo ($country->id == $elegantcustomer->nationality) ? "selected" : "" ?>>{{ $country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="custRow">


                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>CR Number</label>
                                <input type="text" name="cr_number" id="cr_number" placeholder="Enter CR Number" autocomplete="off" value="{{$elegantcustomer->cr_number}}" maxlength="20">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>VAT Number</label>
                                <input type="text" name="vat_number" id="vat_number" placeholder="Enter VAT Number" autocomplete="off" value="{{$elegantcustomer->vat_number}}" maxlength="20">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>P.O Box</label>
                                <input type="text" name="po_box" id="po_box" placeholder="Enter P.O Box" autocomplete="off" value="{{$elegantcustomer->po_box}}" maxlength="20">
                            </div>
                        </div>
                    </div>

                    <div class="custRow">


                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Building Name</label>
                                <input type="text" name="building_name" id="building_name" placeholder="Enter Building Name" autocomplete="off" value="{{$elegantcustomer->building_name}}" maxlength="200">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Street Name</label>
                                <input type="text" name="street_name" id="street_name" placeholder="Enter Street Name" autocomplete="off" value="{{$elegantcustomer->street_name}}" maxlength="200">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>City</label>
                                <input type="text" name="city" id="city" placeholder="Enter City" autocomplete="off" value="{{$elegantcustomer->city}}" maxlength="200">
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Detail Address </label>
                                <textarea name="detail_address" id="detail_address" placeholder="Enter Detail Address">{{$elegantcustomer->detail_address}}</textarea>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Google Map - Latitude</label>
                                <input type="text" name="latitude" id="latitude" class="numberwithdot" placeholder="Enter Latitude" autocomplete="off" value="{{$elegantcustomer->latitude}}" maxlength="30">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Google Map - Longitude</label>
                                <input type="text" name="longitude" id="longitude" class="numberwithdot" placeholder="Enter Longitude" autocomplete="off" value="{{$elegantcustomer->longitude}}" maxlength="30">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Company Website</label>
                                <input type="text" name="website" id="website" placeholder="Enter Company Website" autocomplete="off" value="{{$elegantcustomer->website}}" maxlength="100">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Business Type</label>
                                <select class="chosen-select" name="business_type" id="business_type">
                                    <option value=''>Choose Business Type</option>
                                    <option value='Large'<?php if($elegantcustomer->business_type == 'Large'){ ?>selected="selected" <?php } ?>>Large</option>
                                    <option value='Medium'<?php if($elegantcustomer->business_type == 'Medium'){ ?>selected="selected" <?php } ?>>Medium</option>
                                    <option value='Small'<?php if($elegantcustomer->business_type == 'Small'){ ?>selected="selected" <?php } ?>>Small</option>
                                    <option value='Startup Business'<?php if($elegantcustomer->business_type == 'Startup Business'){ ?>selected="selected" <?php } ?>>Startup Business</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Nature of Current Business</label>
                                <textarea name="nature_of_current_business" id="nature_of_current_business" placeholder="Enter Nature of Current Business">{{$elegantcustomer->nature_of_current_business}}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="custRow">
                    <div class="custCol-4">
                    </div>
                    <input type="hidden" id="employee_code_status">
                    <div class="custCol-8">
                        <button class="commonBtn bgGreen open1 right addNext" type="button">Save and Go <span class="fa fa-arrow-right"></span></button> 
                        <div class="customClear"></div>
                    </div>
                </div>
            </div>


            <div class="formListV1Dtl" id="step2" style="display: none;">
                <div class="formRange">
                    <ul>
                        <li class="selected"><span>1</span></li>
                        <li class="selected"><span>2</span></li>
                        <li><span>3</span></li>
                    </ul>
                </div>
                <h3>Step 2</h3>
                <div class="stepFields">
                    <div class="custRow">

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Contact Person</label>
                                <input type="text" name="contact_person" id="contact_person" placeholder="Enter Contact Person" autocomplete="off" value="{{$elegantcustomer->contact_person}}" maxlength="150">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Mobile Number 1</label>
                                <input type="text" name="mobile_1" id="mobile_1" onkeypress="return isNumberKey(event)" autocomplete="off"  placeholder="Enter Mobile Number 1" value="{{$elegantcustomer->mobile_1}}" maxlength="100">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Mobile Number 2</label>
                                <input type="text" name="mobile_2" id="mobile_2" onkeypress="return isNumberKey(event)" autocomplete="off" placeholder="Enter Mobile Number 2" value="{{$elegantcustomer->mobile_2}}" maxlength="100">
                                <span class="commonError"></span>
                            </div>
                        </div>

                    </div>
                    <div class="custRow">

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Land Phone Number</label>
                                <input type="text" name="land_phone" id="land_phone" onkeypress="return isNumberKey(event)" placeholder="Enter Land Phone Number" autocomplete="off" value="{{$elegantcustomer->land_phone}}" maxlength="100">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Nationality</label>
                                <select name="nationality_contact_person" id="nationality_contact_person">
                                    <option value=''>Choose Nationality</option>
                                    @foreach ($countries as $country)
                                    <option value='{{ $country->id }}' <?php if($elegantcustomer->nationality_contact_person == $country->id){ ?>selected="selected" <?php } ?>>{{ $country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Email 1</label>
                                <input type="text" name="email_1" id="email_1"  placeholder="Enter Email 1" autocomplete="off" value="{{$elegantcustomer->email_1}}" maxlength="50">
                                <span class="commonError"></span>
                            </div>
                        </div>

                    </div>
                    <div class="custRow">

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Email 2</label>
                                <input type="text" name="email_2" id="email_2" placeholder="Enter Email_2" autocomplete="off" value="{{$elegantcustomer->email_2}}" maxlength="50">
                                <span class="commonError"></span>
                            </div>
                        </div>

                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Job Position</label>
                                <input type="text" name="job_position"  id="job_position"  placeholder="Enter Job Position" autocomplete="off" value="{{$elegantcustomer->job_position}}" maxlength="100">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>ID Number</label>
                                <input type="text" name="id_number"  id="id_number"  placeholder="Enter ID Number" autocomplete="off" value="{{$elegantcustomer->id_number}}" maxlength="100">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                    </div>
                    <div class="custRow'">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>What is our expected business target from this customer per month</label>
                                <input type="text" class="numberwithdot" name="busines_target_per_month"  id="busines_target_per_month"  placeholder="Enter Business Target per Month" autocomplete="off" value="{{$elegantcustomer->busines_target_per_month}}" maxlength="30">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-8">
                            <div class="inputHolder">
                                <label>What are the interested products</label>
                                <textarea name="interested_products" id="interested_products" placeholder="Enter Interested Products">{{$elegantcustomer->interested_products}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-8">
                            <div class="inputHolder">
                                <label>Comments from Customer</label>
                                <textarea name="comments_from_customer" id="comments_from_customer" placeholder="Enter Comments from Customer">{{$elegantcustomer->comments_from_customer}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-8">
                            <div class="inputHolder">
                                <label>Make your comments about this Customer</label>
                                <textarea name="comments_about_customer" id="comments_about_customer" placeholder="Enter Comments about this customer">{{$elegantcustomer->comments_about_customer}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="custRow">
                    <div class="custCol-4">
                        <button class="commonBtn bgGreen addPrev  back2 " type="button">Previous <span class="fa fa-arrow-right"></span></button> 
                    </div>
                    <div class="custCol-8">
                        <button class="commonBtn bgGreen right addNext open2  " type="button">Save and Go <span class="fa fa-arrow-right"></span></button> 
                        <div class="customClear"></div>
                    </div>
                </div>
            </div>
        </form>


        <div class="formListV1Dtl" id="step4" style="display: none;">

            <form action="{{url('elegantclub/customer/upload_documents')}}" method="post" id="docuform" enctype='multipart/form-data'>
                <div class="formRange">
                    <ul>
                        <li class="selected"><span>1</span></li>
                        <li class="selected"><span>2</span></li>
                        <li class="selected"><span>3</span></li>
                    </ul>
                </div>
                <h3>Step 3</h3>
                <div class="custRow">
                    <div class="custCol-8">                            
                        <input type="hidden" name="upload_customer_id" id="upload_customer_id" value="{{$elegantcustomer->id}}">
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>VAT Certificate</label>
                                    <input type="file" onchange="vatcertificateurl(this);" id="vat_certificate" class="profilepic" name="vat_certificate" accept="image/*,.doc,.docx,.txt,.pdf,application/msword">      
                                    <span class="commonError"></span>
                                    <!--<img id="indentpic_previewpic">-->
                                </div>
                            </div>
                            <?php if($elegantcustomer->vat_certificate != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='vatcertificateurl' value href="{{$elegantcustomer->vat_certificate}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Company profile</label>
                                    <input type="file" onchange="companyprofileurl(this);" id="company_profile" class="profilepic" name="company_profile" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="jobprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if($elegantcustomer->company_profile != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='companyprofileurl' value href="{{$elegantcustomer->company_profile}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Vendor contract copy</label>
                                    <input type="file" onchange="contracturl(this);" id="vendor_contract_copy" class="profilepic" name="vendor_contract_copy" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="conprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if($elegantcustomer->vendor_contract_copy != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='vendorcontracturl' value href="{{$elegantcustomer->vendor_contract_copy}}">View</a>
                            <?php } ?>
                        </div>


                    </div>
                </div>
                <br>
                <div class="custRow">
                    <div class="custCol-4">
                        <button class="commonBtn bgGreen addPrev back4 " type="button">Previous <span class="fa fa-arrow-right"></span></button> 

                    </div>
                    <div class="custCol-8">
                        <input type="submit" value="Create" class="commonBtn bgBlue addNext right open4">
                        <div class="customClear"></div>
                    </div>
                </div>
            </form>
        </div>


    </div>
    <div class="commonLoaderV1"></div>
</div>
<script>
    
    $(function () {
        $val = $('#account_group_hidden').val();
        $('#account_group').val($val);
        $('input[name=account_group_hidden]').val($(".chosen-select").val());   
        $('.account-group').attr("disabled", true).trigger("chosen:updated");

        //$('.nat').hide();
        $('.vat').hide();
        $('#nationality option:contains("Saudi Arabia")').prop('selected', true);
        $(".customer_type").click(function () {
            var val = $(this).val();
            if (val == 1)
            {
                $('#nationality option:contains("Choose Nationality")').prop('selected', true);
                $('.nat').show();

            }
            else
            {
                $('#nationality option:contains("Saudi Arabia")').prop('selected', true);
                $('.nat').hide();

            }
        });
        $.validator.addMethod('complete_url', function(value, element) {
            var url = $.validator.methods.url.bind(this);
            return url(value, element) || url('http://' + value, element);
        }, 'Please enter a valid URL');
        var v = jQuery("#customer_registration").validate({
            errorElement: "span",
            errorClass: "commonError",
            //ignore:[],
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
                created_by_id: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                name_english: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                    minlength: 2,
                    maxlength: 150,
               },
                detail_address: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                business_type: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                nature_of_current_business: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                contact_person: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                mobile_1: {
                    required: {
                                depends: function () {
                                    $(this).val($.trim($(this).val()));
                                    return true;
                                }
                            },
                    number: true
                },
                job_position: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                busines_target_per_month: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                
                
                nationality: {
                    required: true
                },
                website:{
                  "complete_url":true 
                },
                email_1:{
                    email:true
                },
                email_2:{
                    email:true
                },
                nationality_contact_person: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
            },
            messages: {
                created_by_id: "Choose Employee",
                name_english: "Enter Customer Name in English",
                last_name: "Enter Last Name",
                detail_address: "Enter Detail Address",
                business_type: "Enter Business Type",
                nature_of_current_business: "Enter Nature of Current Business",
                contact_person:"Enter Contact person",
                mobile_1: {
                    required: "Enter Mobile Number",
                    number: "Enter Number Only",
                },
                job_position:"Enter job position",
                busines_target_per_month:"Enter business target",
                
                nationality: "Select Nationality",
                email_1: {
                    email : "Enter a valid email",
                },
                email_2: {
                    email : "Enter a valid email",
                },
                nationality_contact_person:"Choose Nationality",
            },
            // submitHandler: function() {  form.submit(); },  
            
        });
        $(".open1").click(function () {
            $('#container').animate({scrollTop: 28}, {queue: false, duration: 1000});
            if (v.form()) {
                
                $(".formListV1Dtl").hide("fast");
                
                //$("#customer_registration").submit();
                $("#step2").show();
                $("#nationality_contact_person").addClass("chosen-select");
                $("#nationality_contact_person").chosen();
                $('html,body').stop().animate({scrollTop: 0}, {queue: false, duration: 500});
            }
        });
        $(".open2").click(function () {
            if (v.form()) {
                $(".formListV1Dtl").hide("fast");
                $("#customer_registration").submit();
                $("#step4").show();
            }
        });

        $(".back2").click(function () {
            $(".formListV1Dtl").hide();
            $("#nationality_contact_person").removeClass("chosen-select");
            $("#step1").show();
        });

        $(".back4").click(function () {
            $(".formListV1Dtl").hide();
            $("#step2").show();
        });

    });

    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

        return true;
    }

    function vatcertificateurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#indentpic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function companyprofileurl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#jobprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function contracturl(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#conprofilepic_previewpic')
                        .attr('src', e.target.result)
                        .width(150)
                        .height(150);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    ///////////////////////////////////
    $('#customer_registration').submit(function (e) {

        //Preventing the default behavior of the form 
//        Because of this line the form will do nothing i.e will not refresh or redirect the page 
        e.preventDefault();
        //Creating an ajax method
        $.ajax({
            //Getting the url of the uploadphp from action attr of form 
            //this means currently selected element which is our form 
            url: '../customer/store',
            //For file upload we use post request
            type: "POST",
            //Creating data from form 
            data: new FormData(this),
            //Setting these to false because we are sending a multipart request
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data == -1) {
//                    window.location.href = '{{url("ledgers")}}';
//                    window.scrollTo(0, 0);
                }
            },
            error: function () {
            }
        });
    });

    $('#docuform').submit(function (e) {
        $.ajax({
            url: 'customer/upload_documents',
            //For file upload we use post request
            type: "POST",
            //Creating data from form 
            data: new FormData(this),
            //Setting these to false because we are sending a multipart request
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function () {
                $(".commonLoaderV1").show();
            },
            success: function (data) {
                $(".commonLoaderV1").hide();
                toastr.success('Customer Successfully Created!');
                window.location.href = '{{url("elegantclub")}}';
            },
            error: function () {
            }
        });
    });
</script>

@endsection