@extends('layouts.main')
@section('content')
<div class="dashboardList">
    <?php if ($employee_details->admin_status == 1) { ?>
        <a class="dashboardItems" href="{{ url('requisitions/purchase_requisition/add') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconPurchaseRequisition.png')}}" alt="Add Purchase Requisition" name="purchase_requisition"></figure>
                    <figcaption>Add Purchase Requisition</figcaption>
                </div>
            </div>
        </a>
        <a class="dashboardItems" href="{{ url('requisitions/import_purchase_requisition/add') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconPurchaseRequisition.png')}}" alt="Import Purchase" name="purchase_requisition"></figure>
                    <figcaption>Add Import Purchase Requisition</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/general_requisition/add') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconGeneralRequisition.png')}}" alt="Add General Requisition" name="general_requisition"></figure>
                    <figcaption>Add General Requisition</figcaption>
                </div>
            </div>
        </a>
        <a class="dashboardItems" href="{{ url('requisitions/maintainance_requisition/add') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconMaintenanceRequisition.png')}}" alt="Add Maintainance Requisition" name="maintainance_requisition"></figure>
                    <figcaption>Add Maintenance Requisition</figcaption>
                </div>
            </div>
        </a>
        <a class="dashboardItems" href="{{ url('requisitions/service_requisition/add') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconServiceRequisition.png')}}" alt="Add Service Requisition" name="service_requisition"></figure>
                    <figcaption>Add Service Requisition</figcaption>
                </div>
            </div>
        </a>
        <a class="dashboardItems" href="{{ url('requisitions/inbox') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconRequisitionInbox.png')}}" alt="Purchase Requisition Inbox" name="purchase_requisition_inbox"></figure>
                    <figcaption>Requisition Inbox</figcaption>
                </div>
            </div>
        </a>
        <a class="dashboardItems" href="{{ url('requisitions/outbox') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconRequisitionOutbox.png')}}" alt="Purchase Requisition Outbox" name="purchase_requisition_outbox"></figure>
                    <figcaption>Requisition Outbox</figcaption>
                </div>
            </div>
        </a>
        <a class="dashboardItems" href="{{ url('requisitions/requisition_hierarchy/add') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconHierarchySetup.png')}}" alt="Requisition Hierarchy" name="requisition_hierarchy"></figure>
                    <figcaption>Requisition Hierarchy</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/payment_advice') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconMakePayment.png')}}" alt="Make Payment" name="payment_advice"></figure>
                    <figcaption>Make Payment</figcaption>
                </div>
            </div>
        </a>
        <a class="dashboardItems" href="{{ url('requisitions/payment_advice/outbox') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconPaymentAdviceList.png')}}" alt="Payment Advice List" name="payment_advice"></figure>
                    <figcaption>Payment Advice List</figcaption>
                </div>
            </div>
        </a>
        <a class="dashboardItems" href="{{ url('requisitions/payment_approval/inbox') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconPaymentApprovals.png')}}" alt="Payment Approvals" name="payment_advice"></figure>
                    <figcaption>Payment Approvals</figcaption>
                </div>
            </div>
        </a>


        <a class="dashboardItems" href="{{ url('requisitions/advancePayment_requisition/add') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconEmployeeAdvancePayment.png')}}" alt="Add Advance Payment Requisition" name="add_advance_payment_requisition"></figure>
                    <figcaption>Add Advance Payment Requisition</figcaption>
                </div>
            </div>
        </a>


        <a class="dashboardItems" href="{{ url('requisitions/completed_requisition/list') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconCompletedRequisition.png')}}" alt="Completed Requisition" name="completed_requisition"></figure>
                    <figcaption>All Requisitions</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/completed_paymentadvice/list') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconCompletedPayments.png')}}" alt="Completed Payment Advice" name="completed_payment_advice"></figure>
                    <figcaption>All Payment Advices</figcaption>
                </div>
            </div>
        </a>  

        <a class="dashboardItems" href="{{ url('requisitions/leave_requisition/add') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconLeaveRequest.png')}}" alt="Leave Requisition " name="leave_requsition"></figure>
                    <figcaption>Leave Requisition</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/outstanding_payments') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconOutstandingPaymentRequisition.png')}}" alt="Outstanding Requisitions" name="Outstanding Payments"></figure>
                    <figcaption>Outstanding Requisitions</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/purchase_orders') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/IconPurchaseOrder.jpg')}}" alt="Local Purchase Orders" name="Local_Purchase_Orders"></figure>
                    <figcaption>Purchase Orders Local</figcaption>
                </div>
            </div>
        </a>  

        <a class="dashboardItems" href="{{ url('requisitions/import_purchase_orders') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/IconPurchaseOrder.jpg')}}" alt="Import Purchase Orders" name="Import_Purchase_Orders"></figure>
                    <figcaption>Purchase Orders Import</figcaption>
                </div>
            </div>
        </a> 

        <a class="dashboardItems" href="{{ url('requisition/maintenance_requisition_report') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconMaintenanceReport.png')}}" alt="Maintenance Report" name="Maintenance Report"></figure>
                    <figcaption>Maintenance Requisition Report</figcaption>
                </div>
            </div>
        </a> 

        <a class="dashboardItems" href="{{ url('requisitions/purchase_order_list') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconPurchaseRequest.png')}}" alt="Purchase Order List" name="Purchase_Orders"></figure>
                    <figcaption>Local PO Action </figcaption>
                </div>
            </div>
        </a>  

        <a class="dashboardItems" href="{{ url('requisitions/po_action_import') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconPurchaseRequest.png')}}" alt="Purchase Order List" name="Purchase_Orders"></figure>
                    <figcaption>Import PO Action </figcaption>
                </div>
            </div>
        </a>  

        <a class="dashboardItems" href="{{ url('requisitions/userwise_requisitions') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconCompletedRequisition.png')}}" alt="User Wise Requisitions" name="userwise_requisitions"></figure>
                    <figcaption>User Wise Requisitions</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/userwisepayments') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconPaymentApprovals.png')}}" alt="Payment Approval List" name="userwise_requisitions"></figure>
                    <figcaption>Payment Approval List</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/leave_report') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconLeaveRequest.png')}}" alt="Leave Requisition Report" name="leave_report"></figure>
                    <figcaption>Leave Requisition Report</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/requisitionfor_payment/requisitionforpayment') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconApprovedRequisitionPayment.png')}}" alt="Approved Requisitions For Payment" name="payment_advice"></figure>
                    <figcaption>Approved Requisitions For Payment</figcaption>
                </div>
            </div>
        </a> 
    
        <a class="dashboardItems" href="{{ url('requisitions/drawing_requsition/add') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconSupervisorgraph.png')}}" alt="Add Drawing Requisition" name="Add_Drawing_Requisition"></figure>
                    <figcaption>Add Owner Drawings Requisition</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/drawing_requsition_payment_advice/list') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconPaymentAdviceList.png')}}" alt="Owner Drawings Payment Advice List" name="Owner_Drawings_Payment_Advice_List"></figure>
                    <figcaption>Owner Drawings Payment Advice List</figcaption>
                </div>
            </div>
        </a>

        <a class="dashboardItems" href="{{ url('requisitions/drawing_requsition/list') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconCompletedRequisition.png')}}" alt="List Owner Drawings Requisition" name="List_Owner_Drawings_Requisition"></figure>
                    <figcaption>List Owner Drawings Requisition</figcaption>
                </div>
            </div>
        </a>
    

        <a class="dashboardItems" href="{{ url('requisition/maintenance_in_pending') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconMaintenancePending.png')}}" alt="Maintenance Report" name="Maintenance Report"></figure>
                    <figcaption>Maintenance In Pending</figcaption>
                </div>
            </div>
        </a> 

        <a class="dashboardItems" href="{{ url('requisition/maintenance_status') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconWorkingStatus.png')}}" alt="Maintenance Work Status" name="Maintenance_Work_Status"></figure>
                    <figcaption>Maintenance Work Status</figcaption>
                </div>
            </div>
        </a> 

        <a class="dashboardItems" href="{{ url('requisitions/po_action_list') }}">
            <div class="dashboardTable">
                <div class="dashboardTd">
                    <figure><img src="{{ URL::asset('images/iconPurchaseRequest.png')}}" alt="PO Action List" name="PO_Action_List"></figure>
                    <figcaption>PO Action List</figcaption>
                </div>
            </div>
        </a> 



        <?php } else { foreach ($user_sub_modules as $user_sub_module) { ?>
            <a class="dashboardItems" href="{{ url($user_sub_module->url) }}">
                <div class="dashboardTable">
                    <div class="dashboardTd">
                        <figure><img src="{{ URL::asset('images/'.$user_sub_module->logo) }}" alt="Requisition"></figure>
                        <figcaption>{{$user_sub_module->name}}</figcaption>
                    </div>
                </div>
            </a>
        <?php } } ?>
</div>
@endsection
