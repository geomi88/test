<?php $__env->startSection('content'); ?>
<head>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>


<div class="contentHolderV1">
    <h2>Products Management</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">

    <div class="searchWrapper">
        <form class="searchArea" action="<?php echo e(action('Products\ProductsController@search')); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <input type="text" id="search_key" placeholder="Search by Name,Price or Seller Name" name="search_key" value="<?php echo e($query); ?>">
            <input type="submit" value="Search" class="commonButton search">
            <div class="customClear"></div>
        </form>
        <a  href="<?php echo e(URL::to('products')); ?>" title="SetUp" class="btnReset">RESET</a>
        <div class="customClear"></div>
        <form class="searchArea" id="deals" action="<?php echo e(action('Products\ProductsController@deals')); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <label class="dealschkbox"><input type="checkbox" name="deal_chk" id="deal_chk"> Show Deals</label>
        </form>
    </div>
    <div class="tableHolder">
    <table style="width: 100%;" class="tableV1" cellpadding="0" cellspacing="0">
        <thead class="tableHeader">
            <tr>
                <td>#</td>
                <td>Product Name</td>
                <td>Price</td>
                <td>Offer Price</td>
                <td>Seller Name</td>
                <td>Date</td>
                <td>Product Status</td>
                <td class="actionHolder">Actions</td>
            </tr>
        </thead>
        <tbody>

            <?php if(isset($details)): ?>
            <?php $n = $details->perPage() * ($details->currentPage() - 1); ?>
            <?php
            foreach ($details as $product) {
                $n++;
                ?>
                <tr>
                    <td><?php echo e($n); ?></td>
                    <td><?php echo e($product->name); ?></td>
                    <td><?php if ($product->price > 1) { ?><?php echo e($product->price); ?> QAR<?php } ?></td>
                    <td><?php if ($product->offerPrice > 1) { ?><?php echo e($product->offerPrice); ?> QAR<?php } ?></td>
                    <td><?php echo e($product->sellerFirstName); ?> <?php echo e($product->sellerLastName); ?></td>
                    <td><?php echo e($product->created_at); ?></td>
                    <td><select id = "<?php echo e($product->id); ?>" class="productVerified tableSelect"><option value="1" <?php if($product->isVerified ==1): ?> selected = true <?php endif; ?> >Approved</option>
                            <option value="0" <?php if($product->isVerified ==0): ?> selected = true <?php endif; ?> >Pending Approval</option>
                            <option value="2" <?php if($product->isVerified ==2): ?> selected = true <?php endif; ?> >Verified</option>
                            <option value="3" <?php if($product->isVerified ==3): ?> selected = true <?php endif; ?> >Rejected</option>
                        </select>
                        <form class="resonToolTip" action="<?php echo e(action('Products\ProductsController@addreason')); ?>" method="post">
                                <?php echo e(csrf_field()); ?>

                                <h4>Reason</h4>
                                <a class="tooltipClose" href="javascript:void(0)">X</a>
                                <textarea name="rejectReason" required></textarea>
                                <div class="urlError error"></div>
                                <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                                <input type="submit" value="submit">
                        </form>
                    </td>

                    <td class="actionHolder">
                        <a class="actionBtn view" href="<?php echo e(URL::to('products/view', ['id' => ($product->id)])); ?>" title="Edit"></a>
                    </td>

                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    </div>
    <ul class="pagination">
        <?php if($details): ?><?php echo $details->render(); ?><?php endif; ?>
        <?php elseif(isset($message)): ?>
        <p><?php echo e($message); ?></p>
        <?php endif; ?>

    </ul>
</div>
<script>
    $(document).ready(function () {
        $('.productVerified').change(function () {
            var verified_status = $(this).val();
            var prod_id = $(this).attr("id");
            var base_path = $('#base_path').val();

            $.ajax({
                url: base_path + "/products/verify",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"product_id": prod_id, "is_verified": verified_status},
                success: function (result) {

                }
            });
        });
        $('input[name="deal_chk"]').change(function () {
            var base_path = $('#base_path').val();
            if (this.checked) {
                $('#deal_key').val(1);
                $("#deals").submit();
            } else
            {
                window.location = base_path + "/products";
            }
        });
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>