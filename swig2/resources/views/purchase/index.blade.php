@extends('layouts.main')
@section('content')
<div class="dashboardList">
<?php if ($employee_details->admin_status == 1) { ?>
    
    <a class="dashboardItems" href="{{ url('purchase/grn') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconGoodsReceipt.jpg" alt="Goods Receipt Notes(GRN)"></figure>
                <figcaption>Goods Receipt Notes (GRN)</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('purchase/pending_po') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconGoodsReceipt.jpg" alt="Pending PO"></figure>
                <figcaption>Pending PO</figcaption>
            </div>
        </div>
    </a>
    
    
    <a class="dashboardItems" href="{{ url('purchase/update_po_status') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/IconPurchaseOrder.jpg" alt="Pending PO"></figure>
                <figcaption>PO status update List</figcaption>
            </div>
        </div>
    </a>

    <a class="dashboardItems" href="{{ url('purchase/opening_stock/add') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconGoodsReceipt.jpg" alt="Opening Stock"></figure>
                <figcaption>Opening Stock</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems" href="{{ url('purchase/physical_stock/add') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconGoodsReceipt.jpg" alt="Physical Stock"></figure>
                <figcaption>Physical Stock</figcaption>
            </div>
        </div>
    </a>
    
       <a class="dashboardItems" href="{{ url('purchase/view_postatus') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconPurchase1.jpg" alt="Purchase Creation"></figure>
                <figcaption>View PO Status</figcaption>
            </div>
        </div>
    </a>
    
 <a class="dashboardItems" href="{{ url('purchase/received_grn') }}">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/IconPurchaseOrder.jpg" alt="Purchase Order"></figure>
                <figcaption>Received GRN</figcaption>
            </div>
        </div>
</a>
  <!--  <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconPurchase1.jpg" alt="Purchase"></figure>
                <figcaption>Purchase</figcaption>
            </div>
        </div>
    </a>
    
    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconPurchaseReturn.jpg" alt="Purchase Return"></figure>
                <figcaption>Purchase Return</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconSupplierCard.jpg" alt="Supplier Master Create"></figure>
                <figcaption>Supplier Master Create</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconSupplierCardApprove.jpg" alt="Supplier Master Approve"></figure>
                <figcaption>Supplier Master Approve</figcaption>
            </div>
        </div>
    </a>
    <a class="dashboardItems">
        <div class="dashboardTable">
            <div class="dashboardTd">
                <figure><img src="images/iconSupplierCardEdit.jpg" alt="Edit Supplier Master"></figure>
                <figcaption>Edit Supplier Master</figcaption>
            </div>
        </div>
    </a>-->

<?php } else {
        foreach ($user_sub_modules as $user_sub_module) {
            ?>
            <a class="dashboardItems" href="{{ url($user_sub_module->url) }}">
                <div class="dashboardTable">
                    <div class="dashboardTd">
                        <figure><img src="{{ URL::asset('images/'.$user_sub_module->logo) }}" alt="rfq"></figure>
                        <figcaption>{{$user_sub_module->name}}</figcaption>
                    </div>
                </div>
            </a>
<?php }} ?>

</div>
@endsection
