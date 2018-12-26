<?php $__env->startSection('content'); ?>

<div class="contentHolderV1">
    <h2>User Management</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <div class="searchWrapper">
        <form class="searchArea" id="productSearch" action="<?php echo e(action('Users\UsersController@search')); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <input type="text" id="search_key" placeholder="Search by Name,Price or Seller Name" name="search_key" >
            <input type="submit" value="Search" class="commonButton search">
            <div class="customClear"></div>

        </form>
        <div class="customClear"></div>

    </div>
    <div class="tableHolder">
        <table style="width: 100%;" class="tableV1" cellpadding="0" cellspacing="0">
            <thead class="tableHeader">
                <tr>
                    <td>#</td>
                    <td>First Name</td>
                    <td>Last Name</td>
                    <td>Phone Number</td>
                    <td>Email Address</td>
                    <td>User Code</td>
                    <td>Joined On</td>
                    <td class="actionHolder">Actions</td>
                </tr>
            </thead>
            <tbody>

                <?php $n = $users->perPage() * ($users->currentPage() - 1); ?>
                <?php
                foreach ($users as $user) {
                    $n++;
                    ?>
                    <tr>
                        <td><?php echo e($n); ?></td>
                        <td><?php echo e($user->firstName); ?></td>
                        <td><?php echo e($user->lastName); ?></td>
                        <td><?php echo e($user->phoneNumber); ?></td>
                        <td><?php echo e($user->email); ?></td>
                        <td><?php echo e($user->userCode); ?></td>
                        <td><?php echo e($user->created_at); ?></td>
                        <td class="actionHolder">
                            <a class="actionBtn edit" href="<?php echo e(URL::to('users/edit', ['id' => ($user->id)])); ?>" title="Edit"></a>
                            <a class="actionBtn delete" href="<?php echo e(URL::to('users/delete', ['id' => ($user->id)])); ?>" title="Delete" onclick="return confirm('Are you sure?')"></a>
                        </td>

                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <ul class="pagination">
        <?php echo $users->render(); ?>

    </ul>
</div>



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>