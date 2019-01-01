
------- Table structure for table `requisition_docs`-----
CREATE TABLE `requisition_docs` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `level` smallint(4) NOT NULL,
  `created_by` int(11) NOT NULL,
  `doc_url` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `requisition_docs`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `requisition_docs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

--------------finance menu icons--------------------
UPDATE `modules` SET `logo` = 'iconBudgetVariance.jpg' WHERE `modules`.`name` = 'Budget Variance';
UPDATE `modules` SET `logo` = 'iconRemittanceReport.png' WHERE `modules`.`name` = 'Remittance Report';

--------------requsition menu icons--------------------
UPDATE `modules` SET `logo` = 'iconMaintenanceReport.png' WHERE `modules`.`name` = 'Maintenance Requisition Report';

---------------updatind menu under requisition(Purchase orders to purchase order list)------------
UPDATE `modules` SET `name` = 'Purchase Order List' WHERE `modules`.`url` = 'requisitions/purchase_order_list';

ALTER TABLE `ac_payment_advice` DROP `reject_reason`;

ALTER TABLE `ac_payment_advice` ADD `remittance_desc` TEXT NULL AFTER `remittance_image`;

---------------menu name change -------------------------
UPDATE `modules` SET `name`='All Requisitions' WHERE name='Completed Requisition';
UPDATE `modules` SET `name`='All Payment Advices' WHERE name='Completed Payment Advice';