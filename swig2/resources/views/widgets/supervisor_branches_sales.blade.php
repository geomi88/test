<div class="regionalSales allSalesReport">
    <h3 class="commonHeadingV2">Branches Sales</h3>
    <div class="customClear"></div>
    <?php foreach($branches_sales as $branch_sale) {?>
    <div class="statusList <?php echo $branch_sale['profit'];?>">
        <b><a href="{{ URL::to('supervisors/branch/view', ['id' => Crypt::encrypt($branch_sale['branch_id'])]) }}"><?php echo $branch_sale['branch_name'];?></a></b>
        <em><?php echo number_format($branch_sale['total']);?></em>
    </div>
    <?php } ?>
    
    
</div>