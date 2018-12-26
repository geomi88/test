<ul>
    <?php foreach ($users as $user) { ?>
        <input type="hidden" id="name_<?php echo e($user->id); ?>" value="<?php echo e($user->firstName); ?> <?php echo e($user->lastName); ?>">
        <li><input type="checkbox" id="<?php echo e($user->id); ?>" class="chkuser" <?php if(in_array($user->id, $arrid)){echo "checked";}?>> <?php echo e($user->firstName); ?> <?php echo e($user->lastName); ?></li>
    <?php } ?>
</ul>
<ul class="pagination">
    <?php echo $users->render(); ?>

</ul>