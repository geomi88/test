@extends('layouts.main')
@section('content')
<div class="contentHolderV1">
    <h1>Add Category</h1>

        <form method="post" action="{{ action('Category\CategoryController@store') }}" id="addcategoryform" enctype="multipart/form-data" class="formTypeV1">
            {{ csrf_field() }}
            <input type="hidden" id="base_path" value="<?php echo url('/');?>">
            <div class="formSection">
                <label>Category Name</label>
                <input type="text" name="categoryName" id="categoryName">
                <div class="customClear"></div>
                <div class="categoryNameError error"></div>
                <label>Description</label>
                <textarea name="description" id="description"></textarea>
                <div class="customClear"></div>
                <div class="descriptionError error"></div>
                
                <label>Image</label>
                <input type="file" name="categoryImage" id="categoryImage">
                <div class="customClear"></div>
                <div class="categoryImageError error"></div>
            </div>
            <input class="commonButton" id="saveCategory" type="button" value="SAVE">  
        </form>
    

</div>  

<div class="customClear"></div>
</div>


<script>
    $(document).ready(function () {

        $("#saveCategory").click(function (event) {
            event.preventDefault();
            var base_path = $('#base_path').val();
            var categoryName = $('#categoryName').val();
            categoryName = categoryName.trim();
            var description = $('#description').val();
            description = description.trim();
            
            var errors = 0;
            if (categoryName == '') {
                $('.categoryNameError').html('Please enter Category Name');
                errors = 1;
            } else {
                $('.categoryNameError').html('');
                $.ajax({
                        type: 'get',
                        url: base_path+'/category/checkname',
                        data: 'categoryName=' + categoryName,
                        async: false,
                        cache: false,
                        timeout: 30000,
                        success: function (response) {
                            if (response == 1) {
                                $('.categoryNameError').html('Category Name already exist.');
                                errors = 1;
                            } else {
                                $('.categoryNameError').html('');
                            }
                        }
                    });
            }

            if (description == '') {
                $('.descriptionError').html('Please enter Description');
                errors = 1;
            } else {
                $('.descriptionError').html('');
            }

            
             if ($('#categoryImage').get(0).files.length === 0) {
                $('.categoryImageError').html('No files selected.');
                errors = 1;
            } else {
                $('.categoryImageError').html('');
            }
            if (errors == 1)
            {
                return false;
            }
            
            else
            {
                $( "#addcategoryform" ).submit();
            }

        });

       
    });


</script>
@endsection
