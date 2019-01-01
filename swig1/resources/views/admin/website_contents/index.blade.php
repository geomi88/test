@extends('layout.admin.menu')
@section('content')
@section('title', 'Website Contents')
			<div class="adminPageHolder adminAddPropertyHolder">
			<form method="post" action="<?php echo url('/'); ?>/website/save-content" id="formwebcontent">
				{{ csrf_field() }}
				<input type="hidden" id="base_path" value="<?php echo url('/');?>">
				<div class="mainBoxHolder">
					<div class="row">
						<div class="col-12 text-capitalize">
							<h2 class="mt-2">manage website contents</h2>
						</div>

						<div class="col-6 text-capitalize mt-5">
							<div class="halfWidth mb-3">
								<label class="labelStyle">content title</label>
							</div>
							<div class="halfWidth mb-3">
								<select class="inputStyle" name="content_id" id="content_id">
									<option value="">Select</option>
									<?php foreach($website_contents as $website_content) {?>
										<option value="{{$website_content->id}}">{{$website_content->title}}</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</div>
					<!-- <hr> -->
					<div class="web_contents">
						<label class="labelStyle text-capitalize">content</label>
						<div>
							<textarea rows="10" name="content" id="content" class="contentTextareaStyle" placeholder="Enter content"></textarea>
						</div>
						<div class="mt-2">
							<button type="button" class="btnStyle mr-1" id="saveContent">Save</button>
						</div>
					</div>
				</div>
			</form>
			</div>
<script>
	$('#content').ckeditor();
    $(document).ready(function () {
		$('.web_contents').hide();
		var base_path = $('#base_path').val();
        $('#content_id').change(function(){
		   var content_id = $('#content_id').val(); 
		   if(content_id > 0)
		   {
			$('.web_contents').show();
            $.ajax({
                type: 'post',
                url: base_path+'/admin/get-content',
                data: {content_id: content_id},
                async: false,
                //cache: false,
                //timeout: 30000,
                success: function (data) {
                    CKEDITOR.instances["content"].setData(data['content'])
                }
			});
		   }
		   else{
			$('.web_contents').hide();
		   }
	   });
	   
	   $('#saveContent').click(function(event){
		var content_id = $('#content_id').val(); 
		var content = CKEDITOR.instances['content'].getData();
           event.preventDefault();
           $.ajax({
                type: 'post',
                url: base_path+'/website/save-content',
                data: {content_id: content_id,content:content},
                async: false,
                //cache: false,
                //timeout: 30000,
                success: function (data) {
					toastr.success("Successfully saved the content", "Success");
                }
			});
       });

	});
</script>			
@endsection
