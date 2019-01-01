
-------- delete old requisition modules--------
DELETE FROM `modules` WHERE `modules`.`name` = 'General Requisition';
DELETE FROM `modules` WHERE `modules`.`name` = 'Add General Requisition';
DELETE FROM `modules` WHERE `modules`.`name` = 'Maintenance Requisition';
DELETE FROM `modules` WHERE `modules`.`name` = 'Add Maintenance Requisition';
DELETE FROM `modules` WHERE `modules`.`name` = 'Leave Requisition';
DELETE FROM `modules` WHERE `modules`.`name` = 'Add Leave Requisition';

-------- delete old requisition tables--------

DROP TABLE requisition;
DROP TABLE requisition_activity;
DROP TABLE requisition_hierarchy;
DROP TABLE requisition_items;
DROP TABLE requisition_type;

-------- create new requisition tables--------

-- Table structure for table `requisition_types`
CREATE TABLE `requisition_types` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `alias_name` varchar(200) NOT NULL,
  `do_payment` smallint(6) NOT NULL DEFAULT '2' COMMENT '1-payment advice needed/2-not needed',
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `requisition_types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `requisition_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;



-- Table structure for table `requisition_hierarchy`
CREATE TABLE `requisition_hierarchy` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `requisition_type_id` int(11) NOT NULL,
  `level` int(1) NOT NULL,
  `approver_type` enum('TOP_MANAGER','EMPLOYEE') NOT NULL,
  `approver_id` int(11) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1=means active, 2=means not active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `requisition_hierarchy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_type` (`requisition_type_id`),
  ADD KEY `requisition_type_id` (`requisition_type_id`);

ALTER TABLE `requisition_hierarchy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

-- Table structure for table `requisition`
CREATE TABLE `requisition` (
  `id` int(11) NOT NULL,
  `requisition_type` int(11) NOT NULL,
  `company_id` int(11) NOT NULL COMMENT 'identify which is the company',
  `requisition_code` varchar(200) NOT NULL,
  `party_id` int(11) NOT NULL COMMENT 'referring the supplier id. This will refer to ac_accounts table',
  `isallproducts` tinyint(2) NOT NULL COMMENT '1=allproducts/2-preferred products',
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `total_price` double NOT NULL DEFAULT '0' COMMENT 'This is the calculated price',
  `outstanding_amount` double NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL COMMENT 'refernce to employee table',
  `edited_by` int(11) DEFAULT NULL COMMENT 'refernce to employee table',
  `next_level` int(11) DEFAULT NULL COMMENT 'setting who is the next level approver',
  `next_approver_id` int(11) DEFAULT NULL COMMENT 'Basically a refernce to employee table. But basically this will define wich employeee have to approve next',
  `is_settled` smallint(6) NOT NULL COMMENT '1-settled/2-notsettled',
  `convert_to_payment` smallint(6) NOT NULL DEFAULT '2' COMMENT 'convert to payment advice =1 when requisition completed/2 othervise',
  `payment_generated` SMALLINT(4) NOT NULL DEFAULT '2' COMMENT '1-payment advice generated/2-payment advice not generated',
  `status` int(2) NOT NULL COMMENT '1=new, 2=pending,3=hold,4=approved,5=rejected,6=deleted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `requisition`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `requisition_code` (`requisition_code`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `edited_by` (`edited_by`),
  ADD KEY `party_id` (`party_id`),
  ADD KEY `requisition_type` (`requisition_type`);

ALTER TABLE `requisition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

-- Table structure for table `requisition_history`
CREATE TABLE `requisition_history` (
  `id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `requisition_code` varchar(200) NOT NULL,
  `party_id` int(11) NOT NULL COMMENT 'referring the supplier id. This will refer to ac_accounts table',
  `isallproducts` tinyint(2) NOT NULL COMMENT '1=allproducts/2-preferred products ',
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `total_price` double NOT NULL DEFAULT '0' COMMENT 'This is the calculated price',
  `outstanding_amount` double NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL COMMENT 'refernce to employee table',
  `edited_by` int(11) DEFAULT NULL COMMENT 'refernce to employee table',
  `next_level` int(11) DEFAULT NULL COMMENT 'setting who is the next level approver',
  `next_approver_id` int(11) DEFAULT NULL COMMENT 'Basically a refernce to employee table. But basically this will define wich employeee have to approve next',
  `status` int(2) NOT NULL COMMENT '1=new, 2=pending,3=hold,4=approved,5=rejected,6=deleted 	',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `requisition_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `party_id` (`party_id`);

ALTER TABLE `requisition_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

-- Table structure for table `requisition_items`

CREATE TABLE `requisition_items` (
  `id` int(11) NOT NULL,
  `requisition_code` varchar(200) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `requisition_item_id` int(11) NOT NULL,
  `item_type` int(1) NOT NULL COMMENT '1=inventory only now',
  `item_type_id` int(11) NOT NULL COMMENT 'if item type 1 it will be refernce to inventory table',
  `purchase_in_primary_unit` int(1) NOT NULL DEFAULT '1' COMMENT '1=purchase in primary unit, 0=purchase in alternate unit',
  `alternate_unit_id` int(11) DEFAULT NULL COMMENT 'relation to the alternate unit table',
  `quantity` float NOT NULL,
  `unit_price` double NOT NULL DEFAULT '0',
  `total_price` double NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `requisition_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `requisition_item_id` (`requisition_item_id`);

ALTER TABLE `requisition_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

-- Table structure for table `requisition_items_history`
CREATE TABLE `requisition_items_history` (
  `id` int(11) NOT NULL,
  `requisition_code` varchar(200) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `requisition_item_id` int(11) NOT NULL,
  `level` int(1) NOT NULL COMMENT 'Basically mention which level of history is this',
  `item_type` int(1) NOT NULL COMMENT '1=inventory only now',
  `item_type_id` int(11) NOT NULL COMMENT 'if item type 1 it will be refernce to inventory table',
  `purchase_in_primary_unit` int(1) NOT NULL DEFAULT '1' COMMENT '1=purchase in primary unit, 0=purchase in alternate unit',
  `alternate_unit_id` int(11) DEFAULT NULL COMMENT 'relation to the alternate unit table',
  `quantity` float NOT NULL,
  `unit_price` double NOT NULL DEFAULT '0',
  `total_price` double NOT NULL DEFAULT '0',
  `status` smallint(6) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `requisition_items_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisition_id` (`requisition_id`),
  ADD KEY `requisition_items_id` (`requisition_item_id`);

ALTER TABLE `requisition_items_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

-- Table structure for table `requisition_activity`
CREATE TABLE `requisition_activity` (
  `id` int(11) NOT NULL,
  `requisition_code` varchar(200) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `action` int(1) NOT NULL COMMENT '1=approve,2=hold,3=reject',
  `actor_id` int(11) NOT NULL COMMENT 'refernce to employee table. Basically identify who takes the action. ',
  `comments` text,
  `status` SMALLINT(6) NOT NULL DEFAULT '1' COMMENT '1-active/2-deleted ',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `requisition_activity`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `requisition_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;



-- Table structure for table `ac_budget`
CREATE TABLE `ac_budget` (
  `id` int(11) NOT NULL,
  `year` varchar(6) NOT NULL,
  `quarter` int(2) NOT NULL,
  `budget_type` enum('Inventory','Asset','Supplier','Customer','Expense','Branch','Warehouse','Office','General Ledger','Employee','Income') NOT NULL,
  `budget_type_id` int(11) NOT NULL COMMENT 'refernce to different table based on the refernce type value',
  `quantity_budget` int(10) DEFAULT NULL,
  `price_budget` double DEFAULT '0',
  `status` int(1) NOT NULL COMMENT '1=enabled,2=disabled',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_budget`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ac_budget`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;COMMIT;



--changes in branch_physical_stock for stock_area------
ALTER TABLE `branch_physical_stock` ADD `stock_area` INT(5) NOT NULL DEFAULT '0' COMMENT ' 0= entry for branch, 1=entry for warehouse' AFTER `physical_stock`;


ALTER TABLE `branch_physical_stock` CHANGE `branch_id` `stock_area_id` INT(11) NOT NULL;
ALTER TABLE `branch_physical_stock` CHANGE `stock_area_id` `stock_area_id` INT(11) NOT NULL COMMENT 'either branch_id or warehouse id';



--level in requisition_items---------

ALTER TABLE `requisition_items` ADD `level` INT(10) NOT NULL AFTER `requisition_id`;



ALTER TABLE `requisition` CHANGE `isallproducts` `isallproducts` TINYINT(2) NULL COMMENT '1=allproducts/2-preferred products';
ALTER TABLE `requisition` CHANGE `convert_to_payment` `convert_to_payment` SMALLINT(6) NOT NULL DEFAULT '2' COMMENT 'convert to payment advice =1 when requisition completed/2 othervise,0=requisition not needed any conversion to payment';
ALTER TABLE `requisition` CHANGE `party_id` `party_id` INT(11) NULL DEFAULT NULL COMMENT 'referring the supplier id. This will refer to ac_accounts table';
ALTER TABLE `requisition_history` CHANGE `party_id` `party_id` INT(11) NULL DEFAULT NULL COMMENT 'referring the supplier id. This will refer to ac_accounts table', CHANGE `isallproducts` `isallproducts` TINYINT(2) NULL DEFAULT NULL COMMENT '1=allproducts/2-preferred products ';




----ledger type alteration in ac_general_ledgers---

ALTER TABLE `ac_general_ledgers` DROP `type`;



---null level in requisition item--
ALTER TABLE `requisition_items` CHANGE `level` `level` INT(10) NULL DEFAULT NULL;



----- party type field in requisition table---
ALTER TABLE `requisition` ADD `party_type` VARCHAR(50) NULL COMMENT 'Ledger Type' AFTER `party_id`;



--------payment_approved in requisition ---------------
ALTER TABLE `requisition` ADD `payment_approved` SMALLINT(4) NOT NULL DEFAULT '2' COMMENT '1-paymnet final approved/2-not apprved' AFTER `payment_generated`;





ALTER TABLE `requisition` ADD `leave_from` DATETIME NULL DEFAULT NULL AFTER `status`;

ALTER TABLE `requisition` ADD `leave_to` DATETIME NULL DEFAULT NULL AFTER `leave_from`;


ALTER TABLE `requisition_history` ADD `leave_from` DATETIME NULL DEFAULT NULL AFTER `status`;

ALTER TABLE `requisition_history` ADD `leave_to` DATETIME NULL DEFAULT NULL AFTER `leave_from`;

ALTER TABLE `requisition_history` ADD `leave_length` INT NULL DEFAULT NULL COMMENT '0-full day,1-half day' AFTER `leave_to`;

ALTER TABLE `requisition` ADD `leave_length` INT NULL DEFAULT NULL COMMENT '0-full day,1-half day' AFTER `leave_to`;

--------make_purchase_order in requisition table -------------
ALTER TABLE `requisition_types` ADD `make_purchase_order` SMALLINT(4) NOT NULL DEFAULT '2' COMMENT '1-make purchase order/2-do not make order' AFTER `do_payment`;



----- Table structure for table `ac_payment_advice`------
CREATE TABLE `ac_payment_advice` (
  `id` int(11) NOT NULL,
  `payment_code` varchar(200) NOT NULL,
  `payment_from_id` int(11) NOT NULL COMMENT 'Reference to ledger table. From which ledger the amount is taken',
  `from_ledger_type` varchar(50) NOT NULL,
  `payment_to_id` int(11) NOT NULL COMMENT 'Reference to ledger table. Storing to which account the amount is credited',
  `to_ledger_type` varchar(50) NOT NULL,
  `payment_type` smallint(6) NOT NULL COMMENT '1= pay by check 2 = Pay by Cash 3 = Pay by Online',
  `cheque_number` varchar(100) DEFAULT NULL COMMENT 'Storing the cheque number. The number can be null if the payment time is not cheque',
  `cheque_image` text,
  `level` smallint(2) DEFAULT NULL,
  `next_approver_id` int(11) DEFAULT NULL COMMENT 'Decide who is the next approver. Can be null if all are approve.',
  `responsible_emp_id` int(11) DEFAULT NULL,
  `generate_purchase_order` smallint(4) NOT NULL DEFAULT '2' COMMENT '1- generate purchase order/2- not generate',
  `total_amount` double NOT NULL DEFAULT '0',
  `reject_reason` text,
  `status` smallint(6) NOT NULL COMMENT '1 = new ,2 = approved,3=Reject',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_payment_advice`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ac_payment_advice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

----- Table structure for table `ac_payment_advice_details`----
CREATE TABLE `ac_payment_advice_details` (
  `id` int(11) NOT NULL,
  `payment_advice_id` int(11) NOT NULL,
  `requisition_id` int(11) NOT NULL,
  `requisition_code` varchar(200) NOT NULL,
  `pay_amount` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_payment_advice_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_advice_id` (`payment_advice_id`);

ALTER TABLE `ac_payment_advice_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `ac_payment_advice_details`
  ADD CONSTRAINT `ac_payment_advice_details_ibfk_1` FOREIGN KEY (`payment_advice_id`) REFERENCES `ac_payment_advice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

----- Table structure for table `ac_payment_advice_activity`-----
CREATE TABLE `ac_payment_advice_activity` (
  `id` int(11) NOT NULL,
  `payment_advice_id` int(11) NOT NULL,
  `action` smallint(6) NOT NULL COMMENT '1 = approve 2 = reject',
  `comments` text,
  `action_taker_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_payment_advice_activity`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ac_payment_advice_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

------ Table structure for table `ac_transaction` ----
CREATE TABLE `ac_transaction` (
  `id` int(11) NOT NULL,
  `payment_advice_id` int(11) NOT NULL,
  `payment_code` varchar(200) NOT NULL,
  `payment_from_account` int(11) NOT NULL COMMENT 'From which account the payment done',
  `from_ledger_type` varchar(50) NOT NULL,
  `payment_to_account` int(11) NOT NULL COMMENT 'Payment to which account',
  `to_ledger_type` varchar(50) NOT NULL,
  `debit_amount` double NOT NULL DEFAULT '0',
  `credit_amount` double NOT NULL DEFAULT '0',
  `payment_mode` smallint(6) NOT NULL COMMENT '1= pay by check 2 = Pay by Cash 3 = Pay by Online',
  `cheque_number` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_transaction`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ac_transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

------ Table structure for table `ac_purchase_order`----
CREATE TABLE `ac_purchase_order` (
  `id` int(11) NOT NULL,
  `order_code` varchar(100) NOT NULL,
  `payment_advice_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `requisition_id` int(11) NOT NULL,
  `from_ledger_id` int(11) NOT NULL,
  `from_ledger_type` varchar(50) NOT NULL,
  `to_ledger_id` int(11) NOT NULL,
  `to_ledger_type` varchar(50) NOT NULL,
  `amount` double NOT NULL DEFAULT '0',
  `mailed_status` smallint(4) NOT NULL DEFAULT '2' COMMENT '1-mail send/2-mail not send',
  `status` smallint(4) NOT NULL DEFAULT '1' COMMENT '1-active/2-not active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_purchase_order`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ac_purchase_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;



--------ledger menu icons------------------------
UPDATE `modules` SET `logo` = 'iconAddSupplier.png ' WHERE `modules`.`name` = 'Add Suppliers';
UPDATE `modules` SET `logo` = 'iconSupplierList.png ' WHERE `modules`.`name` = 'List Suppliers';
UPDATE `modules` SET `logo` = 'iconEditSupplier.png ' WHERE `modules`.`name` = 'Edit Suppliers';
UPDATE `modules` SET `logo` = 'iconAddConsumer.png ' WHERE `modules`.`name` = 'Add Customer';
UPDATE `modules` SET `logo` = 'iconListConsumer.png ' WHERE `modules`.`name` = 'List Customers';
UPDATE `modules` SET `logo` = 'iconEditConsumer.png ' WHERE `modules`.`name` = 'Edit Customers';
UPDATE `modules` SET `logo` = 'iconLedger.png ' WHERE `modules`.`name` = 'General Ledgers';

UPDATE `modules` SET `logo` = 'iconAddAsset.png ' WHERE `modules`.`name` = 'Add Asset';
UPDATE `modules` SET `logo` = 'iconEditAsset.png ' WHERE `modules`.`name` = 'Edit Assets';
UPDATE `modules` SET `logo` = 'iconListAsset.png ' WHERE `modules`.`name` = 'List Assets';
UPDATE `modules` SET `logo` = 'iconBudgetCreation.png ' WHERE `modules`.`name` = 'Budget Creation';

------------employee menu icons---------------
UPDATE `modules` SET `logo` = 'iconListEmployees.png ' WHERE `modules`.`name` = 'List Employees';
UPDATE `modules` SET `logo` = 'iconAddEmployee.png ' WHERE `modules`.`name` = 'Add Employees';
UPDATE `modules` SET `logo` = 'iconEditEmployee.png ' WHERE `modules`.`name` = 'Edit Employee';
UPDATE `modules` SET `logo` = 'iconDeleteEmployee.png ' WHERE `modules`.`name` = 'Delete Employees';
UPDATE `modules` SET `logo` = 'iconpayrol.png ' WHERE `modules`.`name` = 'Payroll';

------------menu icons under master resources-----------------
UPDATE `modules` SET `logo` = 'exceptedEmployees.png ' WHERE `modules`.`name` = 'Excepted Employee';
UPDATE `modules` SET `logo` = 'iconChartCategory.png ' WHERE `modules`.`name` = 'Chart Category';
UPDATE `modules` SET `logo` = 'iconCutofDate.png ' WHERE `modules`.`name` = 'Cut Off Date';
UPDATE `modules` SET `logo` = 'iconAccountGroup.png ' WHERE `modules`.`name` = 'Account Group';
UPDATE `modules` SET `logo` = 'iconIDProfession.png ' WHERE `modules`.`name` = 'ID Profession';
UPDATE `modules` SET `logo` = 'iconReportSettings.png ' WHERE `modules`.`name` = 'Report Settings';
UPDATE `modules` SET `logo` = 'iconOffice.png ' WHERE `modules`.`name` = 'Office';
UPDATE `modules` SET `logo` = 'iconWarehouse.png ' WHERE `modules`.`name` = 'Warehouse';
UPDATE `modules` SET `logo` = 'iconMeetingRoom.png ' WHERE `modules`.`name` = 'Meetin Rooms';
UPDATE `modules` SET `logo` = 'iconWarningType.png ' WHERE `modules`.`name` = 'Warning Types';

------------menu icons under meeting-----------------
UPDATE `modules` SET `logo` = 'iconMeetingList.png ' WHERE `modules`.`name` = 'Meetings List';

------------menu icons under Organisation chart-----------------
UPDATE `modules` SET `logo` = 'iconCreateOrganisationChart.png ' WHERE `modules`.`name` = 'Create Organization Chart';
UPDATE `modules` SET `logo` = 'iconListOrganisationChart.png ' WHERE `modules`.`name` = 'List Organization Chart';
UPDATE `modules` SET `logo` = 'iconEditOrganisationChart.png ' WHERE `modules`.`name` = 'Edit Organization Chart';

------------menu icons under MIS-----------------
UPDATE `modules` SET `logo` = 'iconPosCashierSalesReport.png ' WHERE `modules`.`name` = 'POS Cashier Sales Report';
UPDATE `modules` SET `logo` = 'iconSupervisorCashDeposit.png ' WHERE `modules`.`name` = 'Supervisor Cash Deposit Report';
UPDATE `modules` SET `logo` = 'iconTopCashierDeposit.png ' WHERE `modules`.`name` = 'Top cashier Cash Deposit Report';
UPDATE `modules` SET `logo` = 'iconVerifiedAccounts.png ' WHERE `modules`.`name` = 'Verified Accounts';
UPDATE `modules` SET `logo` = 'iconNewlyOpening.png ' WHERE `modules`.`name` = 'Newly Opening Branches';

------------menu icons under Taxation-----------------
UPDATE `modules` SET `logo` = 'iconTax.png ' WHERE `modules`.`name` = 'Tax';

------------menu icons under Check List-----------------
UPDATE `modules` SET `logo` = 'iconWraningEdit.png ' WHERE `modules`.`name` = 'Edit Warnings';
UPDATE `modules` SET `logo` = 'iconCategoryWarning.png ' WHERE `modules`.`name` = 'Warnings By Category';
UPDATE `modules` SET `logo` = 'iconRatingGraph.png ' WHERE `modules`.`name` = 'Check Points Rating Graph';

------------menu icons under Operation-----------------
UPDATE `modules` SET `logo` = 'iconPosCashierEdit.png ' WHERE `modules`.`name` = 'POS Cashier Edit';
UPDATE `modules` SET `logo` = 'iconPosSupervisorEdit.png ' WHERE `modules`.`name` = 'POS Supervisor Edit';
-----Insert values to requisition types --------
INSERT INTO `requisition_types` (`id`, `name`, `alias_name`, `do_payment`, `make_purchase_order`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Purchase Requisition', '', 1, 1, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(2, 'General Requisition', '', 2, 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(3, 'Payment Advice', '', 2, 2, 2, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(4, 'Advance Payment Requisition', '', 2, 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(5, 'Maintainance Requisition', '', 2, 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(6, 'Service Requisition', '', 2, 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
(7, 'Leave Requisition', '', 2, 2, 1, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-------- create new requisition menu--------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Purchase Requisition','requisitions/purchase_requisition/add', 'iconPurchaseRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Requisition Inbox','requisitions/inbox', 'iconRequisitionInbox.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Requisition Outbox','requisitions/outbox', 'iconRequisitionOutbox.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
---create new budget creation menu---
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Budget Creation', 'finance/budgetcreation', 'iconCashierCollection.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Budget Creation' limit 1), '1', NULL, NULL, NULL);

---create requisition hierarchy menu---
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Requisition Hierarchy','requisitions/requisition_hierarchy/add', 'iconHierarchySetup.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

---warehouse physical stock-------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Warehouse Physical Stock', 'warehouse/warehouse_physical_stock', 'iconInventoryGroups.png', NULL,  (SELECT `id` FROM `modules` as m WHERE m.name = 'Warehouses' limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Warehouse Physical Stock' limit 1), '1', NULL, NULL, NULL);

-------- create new payments menu--------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Make Payment','requisitions/payment_advice', 'iconMakePayment.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Payment Approvals','requisitions/payment_approval/inbox', 'iconPaymentApprovals.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

---------create general reqisition------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add General Requisition', 'requisitions/general_requisition/add', 'iconGeneralRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Add General Requisition' limit 1), '1', NULL, NULL, NULL);

---------create maintainance reqisition------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Maintenance Requisition', 'requisitions/maintainance_requisition/add', 'iconMaintenanceRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Add Maintenance Requisition' limit 1), '1', NULL, NULL, NULL);
-----advance payment requisition---

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Advance Payment Requisition', 'requisitions/advancePayment_requisition/add', 'iconEmployeeAdvancePayment.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Add Advance Payment Requisition' limit 1), '1', NULL, NULL, NULL);

--completed requisition-----------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Completed Requisition', 'requisitions/completed_requisition/list', 'iconCompletedRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Completed Requisition' limit 1), '1', NULL, NULL, NULL);

--completed payment advice-----

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Completed Payment Advice', 'requisitions/completed_paymentadvice/list', 'iconCompletedPayments.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Completed Payment Advice' limit 1), '1', NULL, NULL, NULL);

---------create service reqisition------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Service Requisition', 'requisitions/service_requisition/add', 'iconServiceRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' limit 1), NULL, NULL, '1', NULL, NULL);

--------create Outstanding Requisiion menu----------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Outstanding Requisitions','requisitions/outstanding_payments', 'iconOutstandingPaymentRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

---leave requisition-------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Leave Requisition', 'requisitions/leave_requisition/add', 'iconLeaveRequest.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Add Leave Requisition' limit 1), '1', NULL, NULL, NULL);

--------create purchaser order module------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Purchase Orders','requisitions/purchase_orders', 'IconPurchaseOrder.jpg', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

------create payment advice list module --------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Payment Advice List', 'requisitions/payment_advice/outbox', 'iconPaymentAdviceList.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

-------change  not null in----------------
ALTER TABLE `requisition_items_history` CHANGE `level` `level` SMALLINT(2) NULL COMMENT 'Basically mention which level of history is this';

------------------Received Payments-----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Received Payments', 'finance/received_payments', 'iconPaymentAdviceList.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Received Payments' limit 1), '1', NULL, NULL, NULL);

-------------remittance_number and remittance_image in payment advice--------
ALTER TABLE `ac_payment_advice` ADD `remittance_number` VARCHAR(150) NULL AFTER `cheque_image`, ADD `remittance_image` TEXT NULL AFTER `remittance_number`;

-------------remove quarter from budget table---------------
ALTER TABLE `ac_budget` DROP `quarter`;

--------create Budget Plan menu--------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Budget Plan', 'finance/budget_plan', 'iconCashierCollection.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Budget Plan' limit 1), '1', NULL, NULL, NULL); 

--------------adding new column branch_id to requsition table----------------------
ALTER TABLE `requisition` ADD `branch_id` INT NULL DEFAULT NULL AFTER `party_type`;

---leave requisition vacation leave_type----
ALTER TABLE `requisition` CHANGE `leave_length` `leave_length` INT(11) NULL DEFAULT NULL COMMENT '0-full day,1-half day,2-vacation';
ALTER TABLE `requisition_history` CHANGE `leave_length` `leave_length` INT(11) NULL DEFAULT NULL COMMENT '0-full day,1-half day,2-vacation';

------------menu for Maintenance Requisition Report under Requisition---------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Maintenance Requisition Report', 'requisition/maintenance_requisition_report', 'iconGeneral.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Maintenance Requisition Report' limit 1), '1', NULL, '2017-12-01 23:43:21', '2017-12-01 23:43:21');


--- todo,view paln,create plan removed from user modules---------
DELETE FROM `user_modules` WHERE `module_id` in (SELECT id FROM `modules` WHERE `name` IN ('To Do','Create Plan','View Plan'));

----------menu for Remittance Report under Finance---------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Remittance Report', 'finance/remittance_report', 'iconCashierCollection.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Remittance Report' limit 1), '1', NULL, '2017-03-28 17:14:12', '2017-03-28 17:14:12');

-----changes for primary unit quantity------------
ALTER TABLE `requisition_items`  ADD `qty_in_primary` FLOAT NULL COMMENT 'quantity in primary unit'  AFTER `quantity`;
ALTER TABLE `requisition_items_history` ADD `qty_in_primary` FLOAT NULL COMMENT 'quantity in primary unit' AFTER `quantity`;

-----changes for payment advice history table------
CREATE TABLE `ac_payment_advice_history` (
  `id` int(11) NOT NULL,
  `payment_code` varchar(200) NOT NULL,
  `payment_from_id` int(11) NOT NULL COMMENT 'Reference to ledger table. From which ledger the amount is taken',
  `from_ledger_type` varchar(50) NOT NULL,
  `payment_to_id` int(11) NOT NULL COMMENT 'Reference to ledger table. Storing to which account the amount is credited',
  `to_ledger_type` varchar(50) NOT NULL,
  `payment_type` smallint(6) NOT NULL COMMENT '1= pay by check 2 = Pay by Cash 3 = Pay by Online',
  `cheque_number` varchar(100) DEFAULT NULL COMMENT 'Storing the cheque number. The number can be null if the payment time is not cheque',
  `cheque_image` text,
  `remittance_number` varchar(150) DEFAULT NULL,
  `remittance_image` text,
  `level` smallint(2) DEFAULT NULL,
  `next_approver_id` int(11) DEFAULT NULL COMMENT 'Decide who is the next approver. Can be null if all are approve.',
  `responsible_emp_id` int(11) DEFAULT NULL,
  `generate_purchase_order` smallint(4) NOT NULL DEFAULT '2' COMMENT '1- generate purchase order/2- not generate',
  `total_amount` double NOT NULL DEFAULT '0',
  `reject_reason` text,
  `status` smallint(6) NOT NULL COMMENT '1 = new ,2 = approved,3=Reject',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_payment_advice_history` ADD PRIMARY KEY (`id`);
ALTER TABLE `ac_payment_advice_history` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;
ALTER TABLE `ac_payment_advice_history` ADD `payment_advice_id` INT(11) NULL DEFAULT NULL AFTER `id`;

---changes in ac_payment_advice_activity-------
ALTER TABLE `ac_payment_advice_activity` ADD `status` INT(2) NULL DEFAULT NULL COMMENT '1-active comments,2-removed comments' AFTER `action_taker_id`;

--changes in requisition table for advance payment requisition----

ALTER TABLE `requisition` ADD `general_ledger` INT(10) NULL DEFAULT NULL AFTER `leave_length`;

--changes in requisition hierarchy for paymentadvice in advance payment requisition-----

UPDATE `requisition_types` SET `do_payment` = '1' WHERE `requisition_types`.`name` = 'Advance Payment Requisition';

