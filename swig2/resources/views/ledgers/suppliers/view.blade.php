@extends('layouts.main')

@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>View <span>Supplier</span></h1>
    </header>

    <div class="formListV1 clsparentdiv">
        <form action="" method="post" id="supplier_registration" enctype="multipart/form-data">
            <div class="formListV1Dtl" id="step1">
                <div class="formRange">
                    <ul>
                        <li class="selected"><span>1</span></li>
                        <li><span>2</span></li>
                        <li><span>3</span></li>
                        <li><span>4</span></li>
                    </ul>
                </div>
                <h3>Step 1</h3>
                <div class="fieldGroup" id="fieldSet1">
                    
                    <div class="custRow">
                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Account Group</label>
                                <select class="chosen-select" name="ledger_group_id" id="ledger_group_id" disabled>
                                    <option value=''>Select Group</option>
                                    @foreach ($parentgroups as $parentgroup)
                                    <option value='{{ $parentgroup->id }}' <?php if($parentgroup->id == $supplier->ledger_group_id) { ?>selected="selected" <?php } ?>>{{ $parentgroup->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="custCol-4">
                         <div class="inputHolder">
                             <label>Supplier Code</label>
                             <input type="text" disabled="disabled" value="{{$supplier->code}}">
                             <span class="commonError"></span>
                         </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                        
                        <div class="custCol-4">
                         <div class="inputHolder">
                             <label>Company Name</label>
                             <input type="text" value="{{$supplier->first_name}}" disabled="" name="first_name" id="first_name" placeholder="Enter Company Name" maxlength="200">
                             <input type="hidden" name="last_name" id="last_name" >
                             <span class="commonError"></span>
                         </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Alias Name</label>
                                <input type="text" name="alias_name" value="{{$supplier->alias_name}}" disabled="" id="alias_name" placeholder="Enter Alias Name" maxlength="200">
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                       
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Contact Person</label>
                                <input type="text" value="{{$supplier->contact_person}}" disabled="" name="contact_person" id="contact_person" placeholder="Enter Contact Person" maxlength="200">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Mobile Number</label>
                                <input type="text" name="mobile_number" value="{{$supplier->mobile_number}}" disabled="" id="mobile_number" placeholder="Enter Mobile Number" maxlength="20">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>Office Telephone</label>
                                <input type="text" name="contact_number" value="{{$supplier->contact_number}}" disabled="" id="contact_number" placeholder="Enter Office Telephone" maxlength="20">
                                <input type="hidden" name="company_name" id="company_name" placeholder="Enter Company Name" maxlength="200">
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        <div class="custCol-5">
                            <div class="inputHolder bgSelect fieldMargin">
                                <label>Supplier Type</label>                                        
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="supplier_type" name="supplier_type" id="supplier_type" value="0" checked <?php if ($supplier->supplier_type==0) { echo "checked";} ?> disabled="">
                                        <span></span>
                                        <em>Saudi</em>
                                    </label>
                                </div>
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="supplier_type" name="supplier_type" id="supplier_type" value="1" <?php if ($supplier->supplier_type==1) { echo "checked";} ?> disabled="">
                                        <span></span>
                                        <em>International</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder nat" <?php if ($supplier->supplier_type!=1) { ?> style="display: none;" <?php } ?>>
                                <label>Choose Nationality</label>
                                <select class="chosen-select" name="nationality" id="nationality" disabled="">
                                    <option value=''>Choose Nationality</option>
                                    @foreach ($countries as $country)
                                    <option value='{{ $country->id }}' <?php echo ($country->id == $supplier->nationality) ? "selected" : "" ?>>{{ $country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                   
                    <div class="custRow">
                        <div class="custCol-5">
                            <div class="inputHolder bgSelect fieldMargin">
                                <label>Supplier Registration</label>                                        
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="registration_type" name="registration_type" id="registration_type" value="1" <?php if ($supplier->registration_type==1) { echo "checked";} ?> disabled >
                                        <span></span>
                                        <em>Registered</em>
                                    </label>
                                </div>
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="registration_type" name="registration_type" id="registration_type" value="0" <?php if ($supplier->registration_type==0) { echo "checked";} ?> disabled>
                                        <span></span>
                                        <em>Not Registered</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder vat" <?php if ($supplier->registration_type!=1) { ?> style="display: none;" <?php } ?>>
                                <label>VAT Number</label>
                                <input type="text" name="supplier_pin" id="supplier_pin" placeholder="Enter VAT Number" maxlength="50" value="{{$supplier->supplier_pin}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>

                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Email Id</label>
                                <input type="text"  name="email" id="email" onpaste="return false;" autocomplete="off" placeholder="Enter Email Id" maxlength="100" value="{{$supplier->email}}" disabled="">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder ">
                                <label>Contact Email Id</label>
                                <input type="text" name="contact_email" id="contact_email" onpaste="return false;" autocomplete="off" placeholder="Enter Contact Email Id" maxlength="100" value="{{$supplier->contact_email}}" disabled="">
                            </div>
                        </div>
                    </div>

                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Address </label>
                                <textarea name="address" id="address" placeholder="Enter Address" disabled="">{{$supplier->address}}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>

                <div class="custRow">
                    <div class="custCol-4">
                    </div>
                    <input type="hidden" id="employee_code_status">
                    <div class="custCol-8">
                        <button class="commonBtn bgGreen open1 right addNext" type="button">Next &gt;&gt;<span class="fa fa-arrow-right"></span></button>
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
                        <li><span>4</span></li>
                    </ul>
                </div>
                <h3>Step 2</h3>
                <div class="stepFields">
                    <div class="custRow">
                        
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Beneficiary Name</label>
                                <input type="text" name="bank_beneficiary_name" id="bank_beneficiary_name" placeholder="Enter Beneficiary Name" maxlength="200" value="{{$supplier->bank_beneficiary_name}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" placeholder="Enter Bank Name" maxlength="200" value="{{$supplier->bank_name}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Branch Name</label>
                                <input type="text" name="bank_branch_name" id="bank_branch_name" placeholder="Enter Bank Branch" maxlength="150" value="{{$supplier->bank_branch_name}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        
                        
                        
                        
                    </div>
                    <div class="custRow">
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Swift Code</label>
                                <input type="text" name="bank_swift_code" id="bank_swift_code"  placeholder="Enter Swift Code" maxlength="100" value="{{$supplier->bank_swift_code}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>City</label>
                                <input type="text" name="bank_city" id="bank_city"  placeholder="Enter City" maxlength="100" value="{{$supplier->bank_city}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Country</label>
                                <select class="chosen-select" name="bank_country" id="bank_country" disabled="">
                                    <option value=''>Choose Country</option>
                                    @foreach ($countries as $country)
                                    <option value='{{ $country->id }}' <?php echo ($country->id == $supplier->bank_country) ? "selected" : "" ?>>{{ $country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        
                        
                        
                        
                    </div>
                    <div class="custRow">
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Account Number</label>
                                <input type="text" name="bank_account_number" id="bank_account_number"  placeholder="Enter Account Number" maxlength="50" value="{{$supplier->bank_account_number}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>IBAN Number</label>
                                <input type="text" name="bank_iban_no" id="bank_iban_no"  placeholder="Enter IBAN Number" maxlength="50" value="{{$supplier->bank_iban_no}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>CR Number</label>
                                <input type="text" name="cr_number" id="cr_number"  placeholder="Enter CR Number" maxlength="50" value="{{$supplier->cr_number}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    <div class="custRow">
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Credit Days</label>
                                <input type="text" name="credit_days" onkeypress="return isNumberKey(event)" id="credit_days" placeholder="Enter Credit Days" maxlength="5" value="{{$supplier->credit_days}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Credit Limit</label>
                                <input type="text" name="credit_limit" onkeypress="return isNumberKey(event)" id="credit_limit"  placeholder="Enter Credit Limit" maxlength="10" value="{{$supplier->credit_limit}}" disabled="">
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                        <div class="custCol-5">
                            <div class="inputHolder">
                                <label>Business Nature </label>
                                <textarea name="business_nature" id="business_nature" placeholder="Enter Business Nature" disabled="">{{$supplier->business_nature}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="custRow">
                    <div class="custCol-4">
                        <button class="commonBtn bgGreen addPrev  back2 " type="button">Previous <span class="fa fa-arrow-right"></span></button> 
                    </div>
                    <div class="custCol-8">
                        <button class="commonBtn bgGreen right addNext open2" type="button">Next &gt;&gt;<span class="fa fa-arrow-right"></span></button>
                        <div class="customClear"></div>
                    </div>
                </div>
            </div>
        </form>

        <div class="formListV1Dtl" id="step3" style="display: none;">
            <form action="" method="post" id="frmProduct" enctype='multipart/form-data'>
                <div class="formRange">
                        <ul>
                            <li class="selected"><span>1</span></li>
                            <li class="selected"><span>2</span></li>
                            <li class="selected"><span>3</span></li>
                            <li><span>4</span></li>
                        </ul>
                    </div>
                    <h3>Step 3</h3>
                    <div class="stepFields">
                        <div class="custRow">
                            <div class="custCol-5">
                                <div class="inputHolder">
                                    <label>Preferred Products</label>
<!--                                    <div class="bgSelect">                                                          
                                        <input type="text"  name="product" id="product" class="" autocomplete="off" placeholder="Enter Product Code or Name">
                                        <ul class="add_product_list classscroll">
                                        </ul>

                                        <input type="hidden"  name="product_id" id="product_id" >
                                        <input type="hidden"  name="product_code" id="product_code" >
                                        <input type="hidden"  name="product_name" id="product_name" >
                                    </div>-->
                                </div>
                            </div>
<!--                            <div class="custCol-3" >
                                <a class="btnAction action bgLightPurple" style="margin-top: 40px;" id="add_product" > Add</a>
                            </div>-->
                        </div>

                        <div class="listHolderType1" style="margin-top: 10px;margin-bottom: 30px;">
                            <div class="listerType1"> 
                                <table style="width: 100%;" cellspacing="0" cellpadding="0">
                                    <thead class="listHeaderTop">
                                        <tr class="headingHolder">
                                            <td>Product Code</td>
                                            <td>Product Name</td>
                                            <!--<td>Remove</td>-->
                                        </tr>
                                    </thead>
                                    <tbody id="tblproducts">
                                        <tr><td>No products added</td></tr>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                        
                    </div>
                    
                    
                    <div class="custRow">
                        <div class="custCol-4">
                            <button class="commonBtn bgGreen addPrev  back3" type="button">Previous <span class="fa fa-arrow-right"></span></button> 
                        </div>
                        <div class="custCol-8">
                            <button class="commonBtn bgGreen right addNext open3" type="button">Next &gt;&gt;<span class="fa fa-arrow-right"></span></button>
                            <div class="customClear"></div>
                        </div>
                    </div>
            </form>
        </div>
        
        <div class="formListV1Dtl" id="step4" style="display: none;">

            <form action="{{url('suppliers')}}" method="get" id="docuform" enctype='multipart/form-data'>
                <div class="formRange">
                    <ul>
                        <li class="selected"><span>1</span></li>
                        <li class="selected"><span>2</span></li>
                        <li class="selected"><span>3</span></li>
                        <li class="selected"><span>4</span></li>
                    </ul>
                </div>
                <h3>Step 4</h3>
                <div class="custRow">
                    <div class="custCol-8">                            
                       
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>VAT Certificate</label>
                                    <!--<input type="file" onchange="vatcertificateurl(this);" id="vat_certificate" class="profilepic" name="vat_certificate" accept="image/*,.doc,.docx,.txt,.pdf,application/msword">-->      
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
                                    <!--<input type="file" onchange="companyprofileurl(this);" id="company_profile" class="profilepic" name="company_profile" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />-->      
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
                                    <!--<input type="file" onchange="contracturl(this);" id="vendor_contract" class="profilepic" name="vendor_contract" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />-->      
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
                        <input type="submit" value="Ok" class="commonBtn bgBlue addNext right open4">
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
    var arrProductList = <?php echo $arrProducts;?>;
    showproductlist();
      
    $(function () {
        
        $('#nationality option:contains("Saudi Arabia")').prop('selected', true);
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
        
        var v = jQuery("#supplier_registration").validate({
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
                first_name: "Enter Company Name",
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
        
        $("#frmProduct" ).validate({
            errorElement: "span",
            errorClass: "commonError",
            highlight: function(element, errorClass){
                        $(element).addClass('valErrorV1');
                        var id=$(element).attr("id")+"_chosen";
                        if($(element).hasClass('valErrorV1')){ 
                            $("#"+id).find('.chosen-single').addClass('chosen_error');
                        }
                    },
                    unhighlight: function(element, errorClass, validClass){
                        $(element).removeClass("valErrorV1");
                    },

            rules: {

                product: {
                        required: {
                            depends: function () { if ($.trim($(this).val()) == '') {$(this).val($.trim($(this).val()));return true;}}
                        },
                    }  
                },
                messages: {product:{required: "Select Product"}}
        });
        
        $(".open1").click(function () {
            $('#container').animate({scrollTop: 28}, {queue: false, duration: 1000});
            if (v.form()) {
                $(".formListV1Dtl").hide("fast");
                //$("#supplier_registration").submit();
                $("#step2").show();
                $('html,body').stop().animate({scrollTop: 0}, {queue: false, duration: 500});
            }
        });
        $(".open2").click(function () {
            if (v.form()) {
                $(".formListV1Dtl").hide("fast");
                //$("#supplier_registration").submit();
                $("#step3").show();
            }
        });
        
        $(".open3").click(function () {
//            if (arrProductList.length>0) {
                $(".formListV1Dtl").hide("fast");
                //savepreferredproducts();
                $("#step4").show();
//            }else{
//                alert("Please add atleast one product");
//            }
        });

        $(".back2").click(function () {
            $(".formListV1Dtl").hide();
            $("#step1").show();
        });
        
        $(".back3").click(function () {
            $(".formListV1Dtl").hide();
            $("#step2").show();
        });

        $(".back4").click(function () {
            $(".formListV1Dtl").hide();
            $("#step3").show();
        });
        
        $('#product').keyup(function () {
            $(".add_product_list").html('');
            $('#product_id').val('');
            $('#product_code').val('');
            $('#product_name').val('');
            var searchkey = $(this).val();
            
            jQuery.ajax({
                url: "autocompleteinventory",
                type: 'POST',
                data: {searchkey: searchkey},
                success:function (result) {
                        var total = result.length;
                        if (total == 0) {
                            $('#product').val('');
                        }
                        var liname = '';
                        $.each(result, function (i, value) {
                            liname += '<li id=' + value['id'] + ' attrcode=' + value['product_code'] + ' attrname="' + value['name'] + '">' +value['name'] +' (' + value['product_code'] + ')' + '</li>';
                        });

                        $(".add_product_list").append(liname);

                        var $selectText = $('.bgSelect input');
                        var $selectLi = $('.bgSelect li');

                        var selectval;
                        var Drop = 0;

                        $('body').click(function () {
                            if (Drop == 1) {
                                $('.bgSelect ul').hide();
                                Drop = 0;
                            }
                        });

                        $selectText.click(function () {
                            $('.bgSelect ul').hide();
                            Drop = 0;
                            if (Drop == 0) {
                                $(this).parent().find('ul').slideDown();
                            }
                            setTimeout(function () {
                                Drop = 1;
                            }, 50);

                        });

                        $selectLi.click(function () {
                            Drop = 1;
                            selectval = $(this).text();

                                $('#product').val(selectval);

                                $(this).parent().parent().find('.commonError').hide();
                        });


                        $('.bgSelect li').click(function () {
                            $('#product_id').val($(this).attr('id'));
                            $('#product_code').val($(this).attr('attrcode'));
                            $('#product_name').val($(this).attr('attrname'));
                        });
                    },
            });
        });
        
        $('#add_product').on('click', function () {

            if (!$("#frmProduct").valid()) {
                return;
            }
            
            if($('#product_id').val()==''){
                alert("Please Select Product");
                return;
            }
            
            var intItemDuplicate = 0;
            if (arrProductList.length > 0) {
                for (var i = 0; i < arrProductList.length; i++) {
                    if ($('#product_id').val() == arrProductList[i].product_id) {
                        intItemDuplicate = 1;
                    }
                }
            }

            var arraData = {
                product_id: $('#product_id').val(),
                product_code: $('#product_code').val(),
                product_name: $("#product_name").val(),
            }
            
            if (intItemDuplicate != 1) {
                arrProductList.push(arraData);
                $('#product').val('');
                $('#product_id').val('');
                $('#product_code').val('');
                $('#product_name').val('');
                showproductlist();
            }else{
                alert("Product Already Selected");
            }
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
    
    function showproductlist()
    {
        $("#tblproducts").html('<tr><td>No products added<td></tr>');
        if (arrProductList.length > 0) {
            var strHtml = '';
            for (var i = 0; i < arrProductList.length; i++) {
                strHtml += '<tr><td>' + arrProductList[i].product_code + '</td><td>' + arrProductList[i].product_name + '</td>\n\
                            </tr>';
            }
            $("#tblproducts").html(strHtml);
        }
    }

    function removeproduct(index) {
        arrProductList.splice(index, 1);
        showproductlist();
    }
    
    function savepreferredproducts() {
        var arrProductsList = encodeURIComponent(JSON.stringify(arrProductList));
        $.ajax({
            type: 'POST',
            url: 'saveproducts',
            data: '&arrProductsList=' + arrProductsList,
            success: function (return_data) {
                if(return_data==-1){
                    window.location.href = '{{url("ledgers")}}';
                }
            }
        });
        
        return 1;
    }
    
     ///////////////////////////////////
    $('#supplier_registration').submit(function (e) {

        //Preventing the default behavior of the form 
        //Because of this line the form will do nothing i.e will not refresh or redirect the page 
        e.preventDefault();
        //Creating an ajax method
        $.ajax({
            //Getting the url of the uploadphp from action attr of form 
            //this means currently selected element which is our form 
            url: '../suppliers/store',
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
                    window.location.href = '{{url("ledgers")}}';
                    window.scrollTo(0, 0);
                }
            },
            error: function () {
            }
        });
    });

     $('#docuform').submit(function (e) {
       $.ajax({
            url: '../suppliers/upload_documents',
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
                toastr.success('Supplier Successfully Created!');
                    window.location.href = '{{url("ledgers")}}';
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