@extends('layouts.main')

@section('content')

<div class="innerContent">
    <header class="pageTitle">
        <h1>Edit <span>Asset</span></h1>
    </header>

    <div class="formListV1 clsparentdiv">
        <form action="" method="post" id="asset_updation" enctype="multipart/form-data">
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
                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Account Group</label>
                                <select class="chosen-select" name="ledger_group_id" id="ledger_group_id">
                                    <option value=''>Select Group</option>
                                    @foreach ($parentgroups as $parentgroup)
                                    <option value='{{ $parentgroup->id }}' <?php if($asset_data->ledger_group_id==$parentgroup->id){echo "selected";}?>>{{ $parentgroup->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="custCol-4">
                         <div class="inputHolder">
                             <label>Asset Code</label>
                             <input type="text" disabled="disabled" value="{{$asset_data->code}}">
                             <span class="commonError"></span>
                         </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                        
                        <div class="custCol-4">
                         <div class="inputHolder">
                             <label>Asset Name</label>
                             <input type="text" name="name" id="name" value="{{$asset_data->name}}" placeholder="Enter Name" maxlength="200">
                             <span class="commonError"></span>
                         </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Alias Name</label>
                                <input type="text" name="alias_name" id="alias_name" value="{{$asset_data->alias_name}}" placeholder="Enter Alias Name" maxlength="200">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder bgSelect">
                                <label>Barcode</label>
                                <input type="hidden" name="barcodeold" id="barcodeold" value="{{$asset_data->barcode_id}}">
                                <select class="barcodeSelect" name="barcode_id" id="barcode_id">
                                    <option value=''>Select Barcode</option>
                                    @foreach ($barcodes as $barcode)
                                    <option value='{{ $barcode->id }}' <?php if($asset_data->barcode_id==$barcode->id){echo "selected";}?>>{{ $barcode->barcode_string}}</option>
                                    @endforeach
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                        
                        
                        <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Supplier</label>
                                <select class="barcodeSelect chosen-select" name="supplier_id" id="supplier_id">
                                    <option value=''>Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                    <option value='{{ $supplier->id }}' <?php if($asset_data->supplier_id==$supplier->id){echo "selected";}?>>{{ $supplier->code}} : {{ $supplier->first_name}} {{ $supplier->alias_name}}</option>
                                    @endforeach
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>
                         <div class="custCol-6">
                            <div class="inputHolder">
                                <label>Purchased By</label>
                                <select class="barcodeSelect chosen-select" name="purchased_emp_id" id="purchased_emp_id">
                                    <option value=''>Select</option>
                                    @foreach ($employees as $employee)
                                    <option value='{{ $employee->id }}' <?php if($asset_data->purchased_emp_id==$employee->id){echo "selected";}?>>{{ $employee->username}} : {{ $employee->first_name}} {{ $employee->alias_name}}</option>
                                    @endforeach
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                            <div class="custCol-4 dates_div">
                                <div class="inputHolder">
                                    <label>Purchase Date</label>
                                    <?php if($asset_data->purchase_date == NULL){
                                        $date = '';
                                    }else{
                                        $date = date("d-m-Y", strtotime($asset_data->purchase_date));
                                    } ?>
                                    <input type="text" name="purchase_date" placeholder="Select Purchase Date" value="<?php echo $date;?>" id="purchase_date" readonly="readonly">
                                    <span class="commonError"></span>
                                </div>
                            </div>
                            
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Purchase Value</label>
                                <input type="text" name="purchase_value" id="purchase_value" class="number" value="{{$asset_data->purchase_value}}" placeholder="Enter Purchase Value" maxlength="20">
                            </div>
                        </div>
                        
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Asset Value</label>
                                <input type="text" name="asset_value" id="asset_value" class="number" value="{{$asset_data->asset_value}}" placeholder="Enter Asset Value" maxlength="20">
                            </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                       
                        
                         <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>EST life (Years) </label>
                                <input type="text" name="expiry_year_count" onkeypress="return isNumberKey(event)" id="expiry_year_count" value="{{$asset_data->expiry_year_count}}" placeholder="Enter Estmated Life In Years" maxlength="200">
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder mobile">
                                <label>EST life (Months) </label>
                                <input type="text" name="expiry_month_count" onkeypress="return isNumberKey(event)" id="expiry_month_count" value="{{$asset_data->expiry_month_count}}" placeholder="Enter Estmated Life In Months" maxlength="20">
                            </div>
                        </div>
                        
                    </div>
                    
                </div>

                <div class="custRow">
                    <div class="custCol-4"></div>
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
                        <div class="custCol-5">
                            <div class="inputHolder bgSelect fieldMargin">
                                <label>Used By</label>                                        
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="used_by" name="used_by" id="used_by" value="1" <?php if($asset_data->used_by==1){ echo "checked";}?>>
                                        <span></span>
                                        <em>Employee</em>
                                    </label>
                                </div>
                                <div class="commonCheckHolder radioRender">
                                    <label>
                                        <input type="radio" class="used_by" name="used_by" id="used_by" value="2" <?php if($asset_data->used_by==2){ echo "checked";}?> >
                                        <span></span>
                                        <em>Location</em>
                                    </label>
                                </div>
                            </div>
                        </div>
                        </div>
                    <div class="custRow">
                        
                        <div class="custCol-6 emp" <?php if ($asset_data->used_by==1) { ?> style="display: none;" <?php } ?>>
                            <div class="inputHolder ">
                                <label>Employee</label>
                                <select class="chosen-select" name="used_employee" id="used_employee">
                                    <option value=''>Select</option>
                                    @foreach ($employees as $employee)
                                    <option value='{{ $employee->id }}' <?php if($asset_data->used_employee==$employee->id){ echo "selected";}?>>{{ $employee->username}} : {{ $employee->first_name}} {{ $employee->alias_name}}</option>
                                    @endforeach
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4 loc" <?php if ($asset_data->used_by==2) { ?> style="display: none;" <?php } ?>>
                            <div class="inputHolder">
                                <label>Branch</label>
                                <select class="chosen-select" name="used_branch" id="used_branch">
                                    <option value=''>Select</option>
                                    @foreach ($branches as $branch)
                                    <option value='{{ $branch->id }}' <?php if($asset_data->used_branch==$branch->id){ echo "selected";}?>>{{ $branch->branch_code}} : {{ $branch->name}}</option>
                                    @endforeach
                                </select>
                                <span class="commonError"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Warranty</label>
                                <input type="text" name="warrenty" onkeypress="return isNumberKey(event)" value="{{$asset_data->warrenty}}" id="warrenty" placeholder="Enter Warranty (Years)" maxlength="5">
                                <span class="commonError"></span>
                            </div>
                        </div>
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Depreciation</label>
                                <input type="text" name="depreciation" id="depreciation" class="number" value="{{$asset_data->depreciation}}" placeholder="Enter Depreciation Value" maxlength="20">
                            </div>
                        </div>
                    </div>
                    
                    <div class="custRow">
                        <div class="custCol-4">
                            <div class="inputHolder">
                                <label>Description</label>
                                <textarea name="description" id="description" placeholder="Enter Description">{{$asset_data->description}}</textarea>
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

            <form action="{{url('assets/update_upload_documents')}}" method="post" id="docuform" enctype='multipart/form-data'>
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
                                    <label>Asset Image 1</label>
                                    <input type="file" onchange="asset_image_1(this);" id="asset_image_1" class="profilepic" name="asset_image_1" accept="image/*">      
                                    <span class="commonError"></span>
                                    <!--<img id="indentpic_previewpic">-->
                                </div>
                            </div>
                            
                            <?php if($assetimage1 != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='assetimage1' value href="{{$assetimage1}}">View</a>
                            <?php } ?>
                             
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Asset Image 2</label>
                                    <input type="file" onchange="asset_image_2(this);" id="asset_image_2" class="profilepic" name="asset_image_2" accept="image/*" />      
                                    <span class="commonError"></span>
                                    <!--<img id="jobprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if($assetimage2 != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='assetimage2' value href="{{$assetimage2}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Asset Image 3</label>
                                    <input type="file" onchange="asset_image_3(this);" id="asset_image_3" class="profilepic" name="asset_image_3" accept="image/*" />      
                                    <span class="commonError"></span>
                                    <!--<img id="conprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if($assetimage3 != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='assetimage3' value href="{{$assetimage3}}">View</a>
                            <?php } ?>
                        </div>
                        
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Asset Document 1</label>
                                    <input type="file" onchange="asset_doc_1(this);" id="asset_doc_1" class="profilepic" name="asset_doc_1" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="conprofilepic_previewpic">-->
                                </div>
                            </div>
                            <?php if($assetdoc1 != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='assetdoc1' value href="{{$assetdoc1}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Asset Document 2</label>
                                    <input type="file" onchange="asset_doc_2(this);" id="asset_doc_2" class="profilepic" name="asset_doc_2" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="conprofilepic_previewpic">-->
                                </div>
                            </div>
                             <?php if($assetdoc2 != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='assetdoc2' value href="{{$assetdoc2}}">View</a>
                            <?php } ?>
                        </div>
                        <div class="custRow">                                
                            <div class="custCol-6">
                                <div class="inputHolder selectV1">
                                    <label>Asset Document 3</label>
                                    <input type="file" onchange="asset_doc_3(this);" id="asset_doc_3" class="profilepic" name="asset_doc_3" accept="image/*,.doc,.docx,.txt,.pdf,application/msword" />      
                                    <span class="commonError"></span>
                                    <!--<img id="conprofilepic_previewpic">-->
                                </div>
                            </div>
                             <?php if($assetdoc3 != '') {?>
                             <a class="btnAction edit bgBlue documentfile" name='assetdoc3' value href="{{$assetdoc3}}">View</a>
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
      
        $('.loc').hide();
        $(".used_by").click(function () {
            var val=$(this).val();
            if(val==1){
                $('.loc').hide();
                $('.emp').show();
            }
            else{
                $('.loc').show();
                $('.emp').hide();
            }
        });
        
        var v = jQuery("#asset_updation").validate({
            rules: {
                ledger_group_id: {
                    required: true,
                            
                },
                name: {
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
                
                alias_name:{
                    required: true,
                },
//                barcode_id: {
//                    required: true,
//                },
//                supplier_id: {
//                    required: true,
//                },
//                purchased_emp_id: {
//                    required: true,
//                },
//                purchase_date: {
//                    required: true,
//                },
//                purchase_value: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                asset_value: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                expiry_year_count: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                expiry_month_count: {
//                    required: {
//                                depends: function () {
//                                    if ($.trim($(this).val()) == '') {
//                                        $(this).val($.trim($(this).val()));
//                                        return true;
//                                    }
//                                }
//                            },
//                }, 
//                 used_employee: {
//                    required: true,
//                },
//                 used_branch: {
//                    required: true,
//                },
//                 warrenty: {
//                    required: true,
//                },
            },
            messages: {
                ledger_group_id: "Select Account Group",
                name: "Enter First Name",
                barcode_id: "Select Bar Code",
                supplier_id: "Select Supplier",
                purchased_emp_id: "Select Purchased Employee",
                purchase_date: "Select Purchase Date",
                purchase_value: "Enter Purchase Value",
                asset_value: "Enter Asset Value",
                expiry_year_count: "Enter Estimated Life In Year",
                expiry_month_count: "Enter Estimated Life In Months",
                 used_employee: "Select Employee",
                used_branch: "Select Branch",
                warrenty: "Enter Warrenty",
                depreciation: "Enter Depreciation",
                
                
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
                $("#asset_updation").submit();
                $("#step2").show();
                $('html,body').stop().animate({scrollTop: 0}, {queue: false, duration: 500});
            }
        });
        $(".open2").click(function () {
            if (v.form()) {
                $(".formListV1Dtl").hide("fast");
                $("#asset_updation").submit();
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
    
    });
    
    function isNumberKey(evt)
    {
       var charCode = (evt.which) ? evt.which : event.keyCode
       if (charCode > 31 && (charCode < 48 || charCode > 57))
          return false;

       return true;
    }

    function asset_image_1(input) {
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
    
    function asset_image_2(input) {
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
    function asset_image_3(input) {
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
    
    function asset_doc_1(input) {
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
    
    function asset_doc_2(input) {
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
    function asset_doc_3(input) {
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
    $('#asset_updation').submit(function (e) {

        //Preventing the default behavior of the form 
        e.preventDefault();
        //Creating an ajax method
        $.ajax({
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
                    window.location.href = '{{url("assets/editlist")}}';
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
    
    $("#purchase_date").datepicker({
         dateFormat: 'dd-mm-yy',
         yearRange: '1950:c',
         changeMonth: true,
         changeYear: true,
     });
     
    $('.number').keypress(function(event) {

        if(event.which == 8 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.which == 0) {
             return true;
        } else if((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)){
             event.preventDefault();
        }

        if($(this).val() == parseFloat($(this).val()).toFixed(2))
        {
            event.preventDefault();
        }

        return true;
   });
     
</script>

@endsection