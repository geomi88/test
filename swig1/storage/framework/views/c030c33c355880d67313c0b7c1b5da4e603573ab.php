<?php $__env->startSection('content'); ?>
<head>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
</head>

<div class="contentHolderV1">
    <h2>Orders List</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <div class="searchWrapper">
            <form class="searchArea" id="productSearch" action="<?php echo e(action('Orders\OrdersController@search')); ?>" method="post">
                <?php echo e(csrf_field()); ?>

                <input type="text" id="search_key" placeholder="Search by Order Number,Buyer Name or Seller Name" name="search_key" value="<?php echo e($query); ?>">
                <input type="submit" value="Search" class="commonButton search">
                <div class="customClear"></div>
            </form>
            <a  href="<?php echo e(URL::to('orders')); ?>" title="SetUp" class="btnReset">RESET</a>

            <div class="customClear"></div>

        </div>
    <div class="tableHolder">
    <table style="width: 100%;" class="tableV1" cellpadding="0" cellspacing="0">
        <thead class="tableHeader">
            <tr>
                <td>#</td>
                <td>Order Number</td>
                <td>Product Name</td>
                <td>Seller Name</td>
                <td>Buyer Name</td>
                <td>Date</td>
                <td>Order Status</td>
                <td class="actionHolder">Actions</td>
            </tr>
        </thead>
        <tbody>

            <?php if(isset($details)): ?>
            <?php $n = $details->perPage() * ($details->currentPage() - 1); ?>
            <?php
            foreach ($details as $order) {
                $n++;
                ?>
                <tr>
                    <td><?php echo e($n); ?></td>
                    <td><?php echo e($order->orderNumber); ?></td>
                    <td><?php echo e($order->productName); ?></td>
                    <td><?php echo e($order->sellerFirstName); ?> <?php echo e($order->sellerLastName); ?></td>
                    <td><?php echo e($order->buyerFirstName); ?> <?php echo e($order->buyerLastName); ?></td>
                    <td><?php echo e($order->created_at); ?></td>

                    <td><select id = "<?php echo e($order->id); ?>" class="orderShipped tableSelect"><option value="0" <?php if($order->shippingStatus ==0): ?> selected = true <?php endif; ?> >Payment Done</option>
                            <option value="1" <?php if($order->shippingStatus ==1): ?> selected = true <?php endif; ?> >Shipped</option>
                            <option value="2" <?php if($order->shippingStatus ==2): ?> selected = true <?php endif; ?> >Delivered</option>
                        </select>
                    </td>

                    <td class="actionHolder">
                        <a class="actionBtn view" href="<?php echo e(URL::to('orders/view', ['id' => ($order->id)])); ?>" title="Edit"></a>
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
        $('.orderShipped').change(function () {
            var shipping_status = $(this).val();
            var order_id = $(this).attr("id");
            var base_path = $('#base_path').val();

            $.ajax({
                url: base_path + "/orders/changeshippingstatus",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {"order_id": order_id, "shipping_status": shipping_status},
                success: function (result) {

                }
            });
        });


    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>