@extends('layout.admin.menu')
@section('content')
@section('title', 'Add Floor')
			
			<div class="adminPageHolder adminAddPropertyHolder">
				<div class="text-capitalize adminTitle">
					<h1>Add Floor</h1>
				</div>
				<div class="mainBoxHolder">
                                 <form action="<?php echo url('/'); ?>/admin/save-floor" method="post" id="floorForm" enctype="multipart/form-data">
                                    {{ csrf_field() }} 
                                    <input autocomplete="off" type="hidden" name="projectId" id="projectId" value="<?php echo $projectId; ?>">
          
                                    <div class="tabWrapper tabOuterHolder">
                                        <ul class="tabLinkStyle tabEventHolder text-uppercase list-unstyled list-inline">
                                            <li data-tab="en" class="list-inline-item"><a href="javascript:void(0)">EN</a></li>
                                            <li data-tab="ka" class="list-inline-item"><a href="javascript:void(0)">ge</a></li>
                                            <li data-tab="ru" class="list-inline-item"><a href="javascript:void(0)">ru</a></li>
                                        </ul>
                                        <div id="en" class="tabContent">
                                            <div class="row">
                                                <div class="col-3 mb-3">
                                                    <label class="labelStyle text-capitalize">appartment name <span class="redColor">*</span>
                                                    </label>
                                                </div>
                                                <div class="col-9 mb-3">
                                                    <input autocomplete="off" class="inputStyle" type="text" placeholder="Appartment Name" name="name_en" id="name_en">
                                                </div>

                                            </div>

                                        </div>
                                        <div id="ka" class="tabContent">
                                            <div class="row">
                                                <div class="col-3 mb-3">
                                                    <label class="labelStyle text-capitalize">appartment name <span class="redColor">*</span>
                                                    </label>
                                                </div>
                                                <div class="col-9 mb-3">
                                                    <input autocomplete="off" class="inputStyle" type="text" placeholder="Appartment Name" name="name_ka" id="name_ka">
                                                </div>

                                            </div>

                                        </div>
                                        <div id="ru" class="tabContent">
                                            <div class="row">
                                                <div class="col-3 mb-3">
                                                    <label class="labelStyle text-capitalize">appartment name <span class="redColor">*</span>
                                                    </label>
                                                </div>
                                                <div class="col-9 mb-3">
                                                    <input autocomplete="off" class="inputStyle" type="text" placeholder="Appartment Name" name="name_ru" id="name_ru">
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                    <div class="row">
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">Select floor <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
                                                                    <input autocomplete="off" class="inputStyle" type="text" placeholder="Floor" name="floor" id="floor">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">Price <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
                                                                    <input autocomplete="off" class="inputStyle" type="text" placeholder="price" name="price" id="price">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">Approximate Area <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
                                                                    <input autocomplete="off" class="inputStyle" type="text" placeholder="sq.m" name="area" id="area">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">terrace area <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
                                                                    <input autocomplete="off" class="inputStyle" type="text" placeholder="sq.m" name="terrace" id="terrace">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">bedrooms <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
                                                                    <input autocomplete="off" class="inputStyle" type="number" placeholder="0" name="bedroom" id="bedroom">
								</div>
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">bathrooms <span class="redColor">*</span></label>
								</div>
								<div class="col-9 mb-3">
                                                                    <input autocomplete="off" class="inputStyle" type="number" placeholder="0" name="bathroom" id="bathroom">
								</div>
								
								<div class="col-3 mb-3">
									<label class="labelStyle text-capitalize">upload floor plan</label>
								</div>
								<div class="col-9 mb-3">
									<input class="custmFileInput" id="floor_plan" name="floor_plan" type="file">
									<label for="floor_plan" class="customFileUpload text-lowercase mt-1">.pdf file</label>
								</div>
                                                                <input  autocomplete="off" type="hidden" name="saveType" id="saveType" value="">
                                                                
							</div>
                                            <div class="mt-2">
								<button type="button" class="btnStyle mr-1" id="btnSave">Save</button>
								<button type="button" class="btnStyle mr-1" id="btnSaveAdd">Save & Add more</button>
								<button type="button" class="cancelBtnStyle">Cancel</button>
                                            </div>
                                      </form>
				</div>
                      
			</div>
<script>
    $('#saveType').val('');
    
                        $.validator.addMethod("PropertyNameLang", function (value, element) {

                            var name_ka = $('#name_ka').val();
                            var name_ru = $('#name_ru').val();
                            if (name_ka == "" || name_ru == "") {
                                return false;
                            }
                            else {
                                return true;
                            }


                        }, 'Please check name in Russian and Georgian');
                        
              $("#floorForm").validate({
                            errorElement: 'span',
                            errorClass: "errorMsg",
                            ignore: '',
                            highlight: function (element, errorClass) {
                                 //$(element).addClass(errorClass);
                               },
                            unhighlight: function (element, errorClass, validClass) {
                                $(element).removeClass(errorClass);
                                },
                            rules: {
                                name_en: {
                                    required: true,
                                    PropertyNameLang: true,
                                    },
                                name_ka: {
                                    required: true,
                                },
                                name_ru: {
                                    required: true,
                                },
                                floor: {
                                    required: true,
                                    /*remote: {
                                        url: "<?php echo url('/')?>/admin/construction/checkFloorExist",
                                        type: "post",
                                        data:
                                            {
                                                projectId: function () {
                                                        return $.trim($("#projectId").val());
                                                },
                                                floornum: function () {
                                                        return $.trim($("#floor").val());
                                                }
                                                },
                                                dataFilter: function (data)
                                                   {
                                                    
                                                       if (data == 1) {
                                                           return false;
                                                       } else {
                                                           return true;
                                                       }
                                                   }
                                    },*/
                                },
                                area: {
                                    required: true,
                                },
                                bedroom: {
                                    required: true,
                                },
                                bathroom: {
                                    required: true,
                                } ,
                                floor_plan: {
                                    // required:true,
                                    // extension: "docx|rtf|doc|pdf",
                                    extension: "pdf",
                                }

                            },
                            messages: {
                               
                                name_en: {
                                    // required: "Please check the property name in English",
                                    required: "Please check the property name in English,Georgian and Russian",
                                },
                                
                                name_ka: {
                                    required: "Please check the property name in English",
                                },
                                name_ru: {
                                    required: "Please check the property name in Russian",
                                },
                                
                                
                                floor: {
                                    required: "Please enter the floor number",
                                    remote:"Floor details already exists",
                                },
                                area: {
                                    required: "Please enter the approximate area",
                                },
                                bedroom: {
                                    required: "Please enter the number of bedrooms",
                                },
                                bathroom: {
                                    required: "Please enter the number of bathrooms",
                                },
                                floor_plan: {
                                    required: "Please choose the proper format(.pdf)",
                                }
                            },
                        });
    $('#btnSave').click(function(){
        if (!$("#floorForm").valid()) {
         return false;
        }
        $("#btnSave").attr("disabled", "disabled");
        $('#saveType').val('save');
        $('#floorForm').submit();
    });
    $('#btnSaveAdd').click(function(){
        if (!$("#floorForm").valid()) {
         return false;
        }
        $("#btnSave").attr("disabled", "disabled");
        $('#saveType').val('Add');
        $('#floorForm').submit();
     });
    </script>
@endsection
