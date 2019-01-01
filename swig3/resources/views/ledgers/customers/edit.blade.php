@extends('layouts.main')

@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Customer</span></h1>
    </header>

    <div class="formListV1 clsparentdiv">
        <form action="" method="post" id="customer_updation" enctype="multipart/form-data">
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
                    
                    <div class="custRow">
                        <div class="custCol-8">
                            <div class="inputHolder">
                                <label>Account Group</label>
                                <select class="chosen-select" name="ledger_group_id" id="ledger_group_id">
                                    <option value=''>Select Group</option>
                                    @foreach ($parentgroups as $parentgroup)
                                    <option value='{{ $parentgroup->id }}' <?php echo ($parentgroup->id == $supplier_data->ledger_group_id) ? "selected" : "" ?>>{{ $parentgroup->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="custCol-4">
                         <div class="inputHolder">
                             <label>Customer Code</label>
                             <input type="text" disabled="disabled" value="{{$supplier_data->code}}">
                             <span class="commonError"></span>
                         </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                        
                        <div class="custCol-4">
                         <div class="inputHolder">
                             <label>First Name</label>
                             <input type="text" name="first_name" id="first_name" placeholder="Enter First Name" maxlength="200" value="{{$supplier_data->first_name}}">
                             <span class="commonError"></span>
                         </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Last Name</label>
                                <input type="text" name="last_name" id="last_name"  autocomplete="off" placeholder="Enter Last Name" maxlength="200" value="{{$supplier_data->last_name}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Alias Name</label>
                                <input type="text" name="alias_name" id="alias_name" placeholder="Enter Alias Name" maxlength="200" value="{{$supplier_data->alias_name}}">
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                       
                        
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Mobile Number</label>
                                <input type="text" name="mobile_number" id="mobile_number" placeholder="Enter Mobile Number" maxlength="20" value="{{$supplier_data->mobile_number}}">
                            </div>
                        </div>
                         <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Contact Person</label>
                                <input type="text" name="contact_person" id="contact_person" placeholder="Enter Contact Person" maxlength="200" value="{{$supplier_data->contact_person}}">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number" placeholder="Enter Contact Number" maxlength="20" value="{{$supplier_data->contact_number}}">
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-5">
                            <div class="inputHolder bgSelect fieldMargin">
                                <label>Customer Type</label>                                        
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="supplier_type" name="supplier_type" id="supplier_type" value="0" <?php if ($supplier_data->supplier_type==0) { echo "checked";} ?>>
                                        <span></span>
                                        <em>Saudi</em>
                                    </label>
                                </div>
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="supplier_type" name="supplier_type" id="supplier_type" value="1" <?php if ($supplier_data->supplier_type==1) { echo "checked";} ?>>
                                        <span></span>
                                        <em>International</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder nat" <?php if ($supplier_data->supplier_type!=1) { ?> style="display: none;" <?php } ?>>
                                <label>Choose Nationality</label>
                                <select class="chosen-select" name="nationality" id="nationality">
                                    <option value=''>Choose Nationality</option>
                                    @foreach ($countries as $country)
                                    <option value='{{ $country->id }}' <?php echo ($country->id == $supplier_data->nationality) ? "selected" : "" ?>>{{ $country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                   
                    <div class="custRow">
                        <div class="custCol-5">
                            <div class="inputHolder bgSelect fieldMargin">
                                <label>Customer Registration</label>                                        
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="registration_type" name="registration_type" id="registration_type" value="1" <?php if ($supplier_data->registration_type==1) { echo "checked";} ?>>
                                        <span></span>
                                        <em>Registered</em>
                                    </label>
                                </div>
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="registration_type" name="registration_type" id="registration_type" value="0" <?php if ($supplier_data->registration_type==0) { echo "checked";} ?>>
                                        <span></span>
                                        <em>Not Registered</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder vat" <?php if ($supplier_data->registration_type!=1) { ?> style="display: none;" <?php } ?>>
                                <label>VAT Number</label>
                                <input type="text" name="supplier_pin" id="supplier_pin" placeholder="Enter VAT Number" maxlength="50" value="{{$supplier_data->supplier_pin}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    
<!--                    <div class="custRow">
                        <div class="custCol-6">
                            <div class="inputHolder bgSelect fieldMargin">
                                <label>Please enter Email id or Contact email id</label>

                                <div class="commonCheckHolder radioRender">
                                    <label>

                                        <input name="email_radio" id="email_radio" value="email" checked type="radio">
                                        <span></span>
                                        <em>Email id</em>
                                    </label>
                                </div>

                                <div class="commonCheckHolder radioRender">
                                    <label>

                                        <input name="email_radio" id="contact_email_radio" value="contact_email" type="radio">
                                        <span></span>
                                        <em>Contact Email id</em>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>-->
                    <div class="custRow">
                       <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Email Id</label>
                                <input type="text"  name="email" id="email" onpaste="return false;" autocomplete="off" placeholder="Enter Email Id" maxlength="100" value="{{$supplier_data->email}}">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Contact Email Id</label>
                                <input type="text" name="contact_email" id="contact_email" onpaste="return false;" autocomplete="off" placeholder="Enter Contact Email Id" maxlength="100" value="{{$supplier_data->contact_email}}">
                            </div>
                        </div>
                    </div>

                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Address </label>
                                <textarea name="address" id="address" placeholder="Enter Address">{{$supplier_data->address}}</textarea>
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
                                <label>Branch Name</label>
                                <input type="text" name="bank_branch_name" id="bank_branch_name" placeholder="Enter Bank Branch" maxlength="150" value="{{$supplier_data->bank_branch_name}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Swift Code</label>
                                <input type="text" name="bank_swift_code" id="bank_swift_code"  placeholder="Enter Swift Code" maxlength="100" value="{{$supplier_data->bank_swift_code}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Country</label>
                                <select class="chosen-select" name="bank_country" id="bank_country">
                                    <option value=''>Choose Country</option>
                                    @foreach ($countries as $country)
                                    <option value='{{ $country->id }}' <?php echo ($country->id == $supplier_data->bank_country) ? "selected" : "" ?>>{{ $country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="custRow">
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Beneficiary Name</label>
                                <input type="text" name="bank_beneficiary_name" id="bank_beneficiary_name" placeholder="Enter Beneficiary Name" maxlength="200" value="{{$supplier_data->bank_beneficiary_name}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Account Number</label>
                                <input type="text" name="bank_account_number" id="bank_account_number"  placeholder="Enter Account Number" maxlength="50" value="{{$supplier_data->bank_account_number}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>IBAN Number</label>
                                <input type="text" name="bank_iban_no" id="bank_iban_no"  placeholder="Enter IBAN Number" maxlength="50" value="{{$supplier_data->bank_iban_no}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                    </div>
                    <div class="custRow">
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Credit Days</label>
                                <input type="text" name="credit_days" onkeypress="return isNumberKey(event)" id="credit_days" placeholder="Enter Credit Days" maxlength="5" value="{{$supplier_data->credit_days}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Credit Limit</label>
                                <input type="text" name="credit_limit" onkeypress="return isNumberKey(event)" id="credit_limit"  placeholder="Enter Credit Limit" maxlength="10" value="{{$supplier_data->credit_limit}}">
                                <span class="commonError"></span>
                            </div>
                        </div>
                         <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Preferred Product</label>
                                <select class="chosen-select" name="preferred_product" id="preferred_product">
                                    <option value=''>Choose Product</option>
                                    @foreach ($inventories as $inventory)
                                    <option value='{{ $inventory->id }}' <?php echo ($inventory->id == $supplier_data->preferred_product) ? "selected" : "" ?>>{{ $inventory->product_code}} : {{ $inventory->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Business Nature </label>
                                <textarea name="business_nature" id="business_nature" placeholder="Enter Business Nature">{{$supplier_data->business_nature}}</textarea>
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

            <form action="{{url('customers/update_upload_documents')}}" method="post" id="docuform" enctype='multipart/form-data'>
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
                       
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>VAT Certificate</label>
                                    <input type="file" onchange="vatcertificateurl(this);" id="vat_certificate" class="profilepic" name="vat_certificate" accept="image/*,.doc,.docx,.txt,.pdf,application/msword">      
                                    <span class="commonError"></span>
                                    <!--<img id="indentpic_previewpic">-->
                                </div>
                            </div>
                            <?php if($vatcertificateurl != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='vatcertificateurl' value href="{{$vatcertificateurl}}">View</a>
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
                            <?php if($companyprofileurl != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='companyprofileurl' value href="{{$companyprofileurl}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Vendor contract copy</label>
                                    <input type="file" onchange="contracturl(this);" id="vendor_contract" class="profilepic" name="vendor_contract" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="conprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if($vendorcontracturl != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='vendorcontracturl' value href="{{$vendorcontracturl}}">View</a>
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
                        <input type="submit" value="Update" class="commonBtn bgBlue addNext right open4">
                        <div class="customClear"></div>
                    </div>
                </div>
            </form>
        </div>


    </div>
    <div class="commonLoaderV1"></div>
    <div class="commonModalHolder">
        <div class="modalContent">
            <a href="javascript:void(0)" class="btnModalClose">Close(X)</a>
            <iframe id="frame" style="width:100%;height:100%;"></iframe>
        </div>
    </div>
</div>
<script>
    $(function () {

        $(".supplier_type").click(function () {
            var val=$(this).val();
            if(val==1)
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
        
        $(".registration_type").click(function () {
            var val=$(this).val();
            if(val==1){
                $('.vat').show();
            }
            else{
                $('.vat').hide();
            }
        });
        
        var v = jQuery("#customer_updation").validate({
            rules: {
                ledger_group_id: {
                    required: true,
                            
                },
                first_name: {
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
                
                alias_name: {
                    required: {
                            depends: function () {
                                    if ($.trim($(this).val()) == '') {
                                        $(this).val($.trim($(this).val()));
                                        return true;
                                    }
                                }
                            },
                            
                },
                
                
                email: {
//                    required:true,
                    customemail: {
                                depends: function () {
                                    $(this).val($.trim($(this).val()));
                                    return true;
                                }
                            },
                    
                },
                contact_email: {
//                    required:true,
                    customemail: {
                            depends: function () {
                                    $(this).val($.trim($(this).val()));
                                    return true;
                                }
                        },
                },
             
                mobile_number: {
//                    required: {
//                                depends: function () {
//                                    $(this).val($.trim($(this).val()));
//                                    return true;
//                                }
//                            },
                    number: true
                },
                nationality: {
                    required: true,
                },
//                bank_branch_name: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                bank_swift_code: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                bank_country: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                bank_beneficiary_name: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                bank_account_number: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                bank_iban_no: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                credit_days: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                credit_limit: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
                
            },
            messages: {
                ledger_group_id: "Select Account Group",
                first_name: "Enter First Name",
                last_name: "Enter Last Name",
                alias_name: "Enter Alias Name",
                email: {required:"Enter Email ID",customemail:"Please enter a valid email"},
                contact_email: {customemail:"Please enter a valid email"},
                nationality: "Select Nationality",
                mobile_number: {
                    required: "Enter Mobile Number",
                    number: "Enter Number Only",
                },
                bank_branch_name: "Enter Bank Name",
                bank_swift_code: "Enter Swift Code",
                bank_country: "Select Country",
                bank_beneficiary_name: "Enter Beneficiary Name",
                bank_account_number: "Enter Account Number",
                bank_iban_no: "Enter IBAN Number",
                credit_days: "Enter Credit Days",
                credit_limit: "Enter Credit Limit",
                
            },
            // submitHandler: function() {  form.submit(); },  
            errorElement: "span",
            errorClass: "commonError",
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                var id=$(element).attr("id")+"_chosen";
                        if($(element).hasClass('valErrorV1')){ 
                            $("#"+id).find('.chosen-single').addClass('chosen_error');
                        }
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("valErrorV1");
            },
        });
        $(".open1").click(function () {
            $('#container').animate({scrollTop: 28}, {queue: false, duration: 1000});
            if (v.form()) {
                $(".formListV1Dtl").hide("fast");
                $("#customer_updation").submit();
                $("#step2").show();
                $('html,body').stop().animate({scrollTop: 0}, {queue: false, duration: 500});
            }
        });
        $(".open2").click(function () {
            if (v.form()) {
                $(".formListV1Dtl").hide("fast");
                $("#customer_updation").submit();
                $("#step4").show();
            }
        });

        $(".back2").click(function () {
            $(".formListV1Dtl").hide();
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
    $('#customer_updation').submit(function (e) {

        //Preventing the default behavior of the form 
        //Because of this line the form will do nothing i.e will not refresh or redirect the page 
        e.preventDefault();
        //Creating an ajax method
        $.ajax({
            //Getting the url of the uploadphp from action attr of form 
            //this means currently selected element which is our form 
            url: '../store',
            //For file upload we use post request
            type: "POST",
            //Creating data from form 
            data: new FormData(this),
            //Setting these to false because we are sending a multipart request
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data==-1){
                    window.location.href = '{{url("customers/editlist")}}';
                    window.scrollTo(0, 0);
                }
            },
            error: function () {
            }
        });
    });

     $('#docuform').submit(function (e) {
       $.ajax({
            url: '../update_upload_documents',
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
            },
            error: function () {
            }
        });
    });
</script>
<script>
    $('.documentfile').on('click', function () {
        var documentfile = $(this).attr('href');
        var arr = documentfile.split(".");      // Split the string using dot as separator
        var extension = arr.pop();       // Get last element
        var myArray=["doc","docx"];
       
        if($.inArray(extension, myArray)==-1){
            $('.commonModalHolder').show();
        }
        
        $('#frame').attr('src', documentfile)
        return false;
        
    })

    

    $('.btnModalClose').on('click', function () {
        $('#frame').attr("src","");
        $('.commonModalHolder').hide()

    })
</script>

@endsection