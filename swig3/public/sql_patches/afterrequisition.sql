------------------User Wise Rquisitions----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'User Wise Rquisitions','requisitions/userwise_requisitions', 'iconCompletedRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

------------------Payment Approval List----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Payment Approval List','requisitions/userwisepayments', 'iconPaymentApprovals.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

------------------Leave Requisition Report----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Leave Requisition Report','requisitions/leave_report', 'iconLeaveRequest.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

-------------------menu name change---------------------------
UPDATE modules set name='Received Payments (Inbox)' where url='finance/received_payments';

------------------Approved Requisitions For Payment----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Approved Requisitions For Payment','requisitions/requisitionfor_payment/requisitionforpayment', 'iconPaymentAdviceList.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

---------------remitted date and beneficiary name in payment advice-------
ALTER TABLE `ac_payment_advice` ADD `remitted_date` DATETIME NULL DEFAULT NULL AFTER `remittance_desc`, ADD `beneficiary_name` VARCHAR(250) NULL DEFAULT NULL AFTER `remitted_date`;