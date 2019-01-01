@extends('layout.admin.menu')
@section('content')
@section('title', 'Agent Edit')
<?php 
//$agentList=$pageData['agentList'];
$municipalities=$pageData['municipalities'];
$districts=$pageData['districts'];
$agentDetails=$pageData['agentDetails'];
?>
			<div class="adminPageHolder adminAddPropertyHolder">
				<div class="text-capitalize adminTitle">
					<h1>Edit Agent</h1>
				</div>
				<div class="mainBoxHolder">
					<div class="tabWrapper tabOuterHolder">
<!--						<ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
							<li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">EN</a></li>
							<li data-tab="ge" class="list-inline-item"><a href="javascript:void(0)">ge</a></li>
							<li data-tab="ru" class="list-inline-item"><a href="javascript:void(0)">ru</a></li>
						</ul>-->
						<div id="en" class="tabContent">
							<h2 class="mt-2 text-capitalize">basic information <span class="subInfo">(* these are mandatory fields)</span></h2>
							<form method="POST" action="<?php echo url('/'); ?>/admin/update-agent" enctype="multipart/form-data" id="saveagent" >
                                                         {{ csrf_field() }}
                                                             <div class="row">
								<div class="col-6 text-capitalize mt-5">
									<div class="halfWidth mb-3">
										<label class="labelStyle">select city <span class="redColor">*</span></label>
									</div>
                                                                    <div class="halfWidth mb-3 relative">
                                                                        <select class="inputStyle" name="municipality" id="municipality">
                                                                            <option value="">Choose City</option>
                                                                            @foreach($municipalities as $city)
                                                                            <option value="{{$city->id}}" <?php if($city->id==$agentDetails->municipality_id){ echo "selected";} ?>>{{$city->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="halfWidth mb-3">
										<label class="labelStyle">select district <span class="redColor">*</span></label>
									</div>
                                                                    <div class="halfWidth mb-3 relative">
                                                                        <select class="inputStyle" name="district" id="district">
                                                                            <option value="">Choose City</option>
                                                                            @foreach($districts as $district)
                                                                            <option value="{{$district->id}}" <?php if($district->id==$agentDetails->district_id){ echo "selected";} ?>>{{$district->name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
									<div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">agent name <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 relative">
										<input class="inputStyle" type="text" name="name" id="name" placeholder="Agent Name" value="{{$agentDetails->name}}">
									</div>
									<div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">address <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 relative">
<!--										<input class="inputStyle" type="text"  placeholder="Enter Address">-->
                                                                            <textarea class="inputStyle" id="address" name="address">{{$agentDetails->address}}</textarea>
                                                                        
                                                                        </div>
									<div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Office number <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 relative">
										<input class="inputStyle" type="text" name="phone_offc" id="phone_offc" placeholder="Office Number" value="{{$agentDetails->office_phone}}">
									</div>
                                                                        <div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">Personal number <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 relative">
										<input class="inputStyle" type="text" name="phone_mob" id="phone_mob" placeholder="Personal number" value="{{$agentDetails->mobile_number}}">
									</div>
									<div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">email id <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 relative">
										<input class="inputStyle" name="email" id="email" type="text" placeholder="Email Id" value="{{$agentDetails->email}}">
									</div>
									<div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">joined on <span class="redColor">*</span></label>
									</div>
									<div class="halfWidth mb-3 relative">
                                                                            <input class="inputStyle datepicker" type="text" name="member_since" id="member_since" value="<?php echo date('d-M-Y',strtotime($agentDetails->member_since)); ?>">
									</div>
									<div class="clearfix"></div>
									<div class="halfWidth mb-3">
										<label class="labelStyle">add image </label>
									</div>
									<div class="halfWidth mb-3">
										<input class="custmFileInput"  id="preview_upload" name="preview_upload" type="file" onchange="previewFile('#preview_upload','#previewbox_previewimg','140X140')" />
										<label for="preview_upload" class="customFileUpload mt-1">choose image</label>
                                                                                <a href="javascript:void(0)" style="display: none;"  onclick="deletePreview('#previewbox_previewimg')">Delete Image</a>
                                                                                <div class="previewImageHolder">
                                                                                    <?php
                                                                                    $image = url('/') . '/uploads/imgPlaceholder.jpg';

                                                                                    if ($agentDetails->image == "" || $agentDetails->image == NULL) {
                                                                                        ?>
                                                                                        <img id="previewbox_previewimg"  alt="agent1" src="{{ URL::asset($image)}}">
                                                                                        <?php
                                                                                    } else {
                                                                                        $image = url('/') . '/uploads/agent/' . $agentDetails->image;

                                                                                        if (!@getimagesize($image)) {
                                                                                            ?>
                                                                                            <img id="previewbox_previewimg" alt="agent1" src="{{ URL::asset($image)}}">
                                                                                        <?php } else { ?>
                                                                                            <img id="previewbox_previewimg" alt="agent1" src="{{ URL::asset($image)}}">
                                                                                            <?php
                                                                                        }
                                                                                    }
                                                                                    ?>
                                                                                   
                                                                                </div>
									</div>
									<div class="clearfix"></div>
                                                                       
                                                                       
                                                                </div>
                                                        </div>
							<!-- <hr> -->
							<label class="labelStyle text-capitalize">Add description</label>
							<div>
								<textarea  class="contentTextareaStyle ckeditor" id="description" name="description" placeholder="Enter content">{{$agentDetails->description}}</textarea>
							</div>
							<div class="mt-2">
								<button type="button" id="btnsaveProperty" class="btnStyle mr-1">Save</button>
<!--								<button type="button" class="cancelBtnStyle">Cancel</button>-->
							</div>
                                                        <input type="hidden" id="agentId" name="agentId" value="{{\Crypt::encrypt($agentDetails->id)}}" />
                                                        </form>
						</div>
						
					</div>
				</div>
			</div>

<script>
    $('#description').ckeditor();  
   
</script>
<script type="text/javascript">
    $(document).ready(function() {
      $(".datepicker").datepicker({
           // maxDate: new Date(),
            dateFormat: 'dd-M-yy',
            changeMonth: true,
            changeYear: true,
           
        });
        
                        
jQuery.validator.addMethod("strictEmail", function (value, element)
{
    return this.optional(element) || /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9])+$/.test(value);
}, "Please enter a valid email ");

jQuery.validator.addMethod("phoneNumber", function (value, element)
{
    return this.optional(element) || /^[0-9-+s()\.][0-9\s-()\.]*$/.test(value);

}, "Alphabets or Empty blank spaces are not allowed");


    });
       $('#btnsaveProperty').click(function () {
          if (!$("#saveagent").valid()) {
               return false;
            }

            $("#btnsaveProperty").attr("disabled", "disabled");
            $("#saveagent").submit();
        }); 
        
        
        $("#saveagent").validate({
            errorElement: 'span',
            errorClass: "errorMsg",
            ignore: '',
            highlight: function (element, errorClass) {
                $(element).addClass('valErrorV1');
                $("#" + element.id + "_chosen").find('.chosen-single').addClass('valErrorV1');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('valErrorV1');
            },
              rules: {
                municipality: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },

                district: {
                    required:true,
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
                 
                }, 
                address: {
                     required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                },
                phone_offc: {
                    required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                    phoneNumber:true,
                }, 
              
                phone_mob: {
                     required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                    phoneNumber:true,
                   
                }, 
            
                email: {
                     required: {
                        depends: function () {
                            if ($.trim($(this).val()) == '') {
                                $(this).val($.trim($(this).val()));
                                return true;
                            }
                        }
                    },
                    strictEmail:true,
                     
                },
                
                member_since: {
                    required: true,
                },
               
              
            },
            messages: {
                estimated_price: {
                    required:"Estimated Price is required",
                 },

               municipality: {
                   required:"Please choose a Municipality",
                },

                district: {
                    required:"Please choose a District",
                  }, 
                name: {
                    required: "Enter the Agent name",
                 
                }, 
                address: {
                    required: "Enter the Address",
                },
                phone_offc: {
                    required: "Enter the Phone number",
                }, 
              
                phone_mob: {
                   required: "Enter the Mobile number",
                   
                }, 
            
                email: {
                    required: "Enter the Email",
                     
                },
                
                member_since: {
                    required: "Enter the Member joining date",
                },
               
            },

        });
               
</script>
@endsection