<?php $__env->startSection('content'); ?>
<style>
    .contentTable li {
        min-height: 44px;

    }
</style>
<div class="contentSection">
    <div class="contentArea">

        <h2>User Management</h2>

        <div class="searchWrapper">
            <form class="searchArea" action="<?php echo e(view('users\search')); ?>" method="post">
                <?php echo e(csrf_field()); ?>

                <input type="text" id="SearchName" placeholder="Search Name Here" name="search_name" >
                <div class="customClear"></div>
                <input type="submit" value="Search" class="searchbtn">             
            </form>
            <div class="customClear"></div>
        </div>

        <div class="setupTeamTable">
            <div class="titleTable">
                <ul>
                    <li>#</li>
                    <li>First Name</li>
                    <li>Last Name</li>
                    <li>Phone Number</li>
                    <li>Email Address</li>
                    <li>User Code</li>
                    <li>Joined On</li>
                    <li>Actions</li>
                </ul>
            </div>
            <div class="contentTable">
                <?php $n = $users->perPage() * ($users->currentPage()-1); ?>
                <?php
                foreach ($users as $user) {
                    $n++;
                    ?>
                    <ul>
                        <li><?php echo e($n); ?></li>
                        <li><?php echo e($user->firstName); ?></li>
                        <li><?php echo e($user->lastName); ?></li>
                        <li><?php echo e($user->phoneNumber); ?></li>
                        <li><?php echo e($user->email); ?></li>
                        <li><?php echo e($user->userCode); ?></li>
                        <li><?php echo e($user->created_at); ?></li>
                        <li>
                            <a  href="<?php echo e(URL::to('users/edit', ['id' => Crypt::encrypt($user->id)])); ?>" title="SetUp"><img src="<?php echo e(URL::asset('images/iconEdit.png')); ?>"></a>
                            <a  href="<?php echo e(URL::to('users/delete', ['id' => Crypt::encrypt($user->id)])); ?>" title="Delete"><img src="<?php echo e(URL::asset('images/iconClose.png')); ?>"></a>

                        </li>

                    </ul>
                    <?php
                    
                }
                ?>
                <ul class="userPagination">

                    <?php echo $users->render(); ?>

                </ul>
            </div>
        </div>


    </div>
    <div class="customClear"></div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>