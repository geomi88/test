<?php $__env->startSection('content'); ?>


<div class="contentHolderV1">
    <h2>Company Management</h2>
    <input type="hidden" id="base_path" value="<?php echo url('/'); ?>">
    <div class="searchWrapper">
        <form class="searchArea" action="<?php echo e(action('Company\CompanyController@search')); ?>" method="post">
            <?php echo e(csrf_field()); ?>

            <input type="text" id="search_key" placeholder="Search by Name,Email,Company Name or User Code" name="search_key" >
            <input type="submit" value="Search" class="commonButton search">
            <div class="customClear"></div>
        </form>
        <a  href="<?php echo e(URL::to('company')); ?>" title="SetUp" class="btnReset">RESET</a>
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
                <td>Company Name</td>
                <td>Joined On</td>
                <td class="actionHolder">Actions</td>
            </tr>
        </thead>
        <tbody>

            <?php if(isset($details)): ?>
            <?php $n = $details->perPage() * ($details->currentPage() - 1); ?>
            <?php
            foreach ($details as $company) {
                $n++;
                ?>
                <tr>
                    <td><?php echo e($n); ?></td>
                    <td><?php echo e($company->firstName); ?></td>
                    <td><?php echo e($company->lastName); ?></td>
                    <td><?php echo e($company->phoneNumber); ?></td>
                    <td><?php echo e($company->email); ?></td>
                    <td><?php echo e($company->companyName); ?></td>
                    <td><?php echo e($company->created_at); ?></td>
                    <td class="actionHolder">
                        <a class="actionBtn edit" href="<?php echo e(URL::to('company/edit', ['id' => ($company->id)])); ?>" title="Edit"></a>
                        <a class="actionBtn delete" href="<?php echo e(URL::to('company/delete', ['id' => ($company->id)])); ?>" title="Delete" onclick="return confirm('Are you sure?')"></a>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>