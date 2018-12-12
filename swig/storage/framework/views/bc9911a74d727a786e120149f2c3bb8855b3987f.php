<?php $__env->startSection('content'); ?>
<div class="contentHolderV1">
    <h1>Edit Category</h1>

        
        <form method="post" action="<?php echo e(action('Category\CategoryController@update')); ?>" id="upatecategoryform" enctype="multipart/form-data" class="formTypeV1">
            <?php echo e(csrf_field()); ?>

            <input type="hidden" id="base_path" value="<?php echo url('/');?>">
            <input type="hidden" name="category_id" id="category_id" value="<?php echo e($category_details->id); ?>">
            <div class="formSection">
                <label>Category Name</label>
                <input type="text" name="categoryName" id="categoryName" value="<?php echo e($category_details->name); ?>">
                <div class="customClear"></div>
                <div class="categoryNameError error"></div>
                <label>Description</label>
                <textarea name="description" id="description"><?php echo e($category_details->description); ?></textarea>
                <div class="customClear"></div>
                <div class="descriptionError error"></div>
                
                <label>Image</label>
                <img src="<?php echo url('/');?><?php echo e($category_details->image); ?>" style="width:150px;height:150px;">
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
            var category_id = $('#category_id').val();
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
                        data: 'categoryName=' + categoryName +'&category_id='+ category_id,
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

            
            
            if (errors == 1)
            {
                return false;
            }
            
            else
            {
                $( "#upatecategoryform" ).submit();
            }

        });

       
    });


</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>