
-- branch start date in master resource
ALTER TABLE `master_resources` ADD `branch_start_date` DATETIME NULL DEFAULT NULL AFTER `branch_code`; 

UPDATE `master_resources` SET `branch_start_date`='2017-03-01' where `resource_type`='BRANCH';

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Cost Center', 'costcenter', 'iconPayments.png', NULL, '0', '1', NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Branch Cost Allocation', 'costcenter/cost_allocation/add', 'addSales.jpg', 'addSales.jpg', (SELECT `id` FROM `modules` as m WHERE m.name = 'Cost Center' limit 1), '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Branch Cost Analysis', 'costcenter/cost_analysis', 'addSalesList.jpg', 'addSalesList.jpg', (SELECT `id` FROM `modules` as m WHERE m.name = 'Cost Center' limit 1), '1', NULL, NULL);

CREATE TABLE `branch_fixed_cost` (
  `id` int(11) NOT NULL,
  `cost_name` varchar(250) NOT NULL,
  `cost_amount` double NOT NULL DEFAULT '0',
  `branch_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `branch_fixed_cost`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `branch_fixed_cost`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;COMMIT;

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Meeting', 'meeting', 'iconMeeting.png', 'iconMeeting.png', '0', '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Purchase', 'purchase', 'iconPurchase.png', 'iconPurchase.png', '0', '1', NULL, NULL);

ALTER TABLE `modules` ADD `menu_order` INT(11) NULL AFTER `parent_id`; 

UPDATE `modules` SET `menu_order` = '1' WHERE `modules`.`name` = 'Calendar';
UPDATE `modules` SET `menu_order` = '50' WHERE `modules`.`name` = 'Meeting';
UPDATE `modules` SET `menu_order` = '100' WHERE `modules`.`name` = 'MIS';
UPDATE `modules` SET `menu_order` = '150' WHERE `modules`.`name` = 'Cost Center';
UPDATE `modules` SET `menu_order` = '200' WHERE `modules`.`name` = 'HR';
UPDATE `modules` SET `menu_order` = '250' WHERE `modules`.`name` = 'Master Resources';
UPDATE `modules` SET `menu_order` = '300' WHERE `modules`.`name` = 'Purchase';
UPDATE `modules` SET `menu_order` = '350' WHERE `modules`.`name` = 'Inventory';
UPDATE `modules` SET `menu_order` = '400' WHERE `modules`.`name` = 'Branch Sales';
UPDATE `modules` SET `menu_order` = '450' WHERE `modules`.`name` = 'Operation';
UPDATE `modules` SET `menu_order` = '500' WHERE `modules`.`name` = 'Requisitions';
UPDATE `modules` SET `menu_order` = '550' WHERE `modules`.`name` = 'KPI Analysis';
UPDATE `modules` SET `menu_order` = '600' WHERE `modules`.`name` = 'Branches';
UPDATE `modules` SET `menu_order` = '650' WHERE `modules`.`name` = 'Supervisors';
UPDATE `modules` SET `menu_order` = '700' WHERE `modules`.`name` = 'Warehouses';

ALTER TABLE `branch_fixed_cost` ADD `status` SMALLINT(6) NOT NULL DEFAULT '1' AFTER `branch_id`;
 
-- Cost center report
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Cost Center Report', 'costcenter/costcenter_report', 'costCentreReport.png', 'costCentreReport.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'Cost Center' limit 1), '1', NULL, NULL);
/*Finance*/
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Finance', 'finance', 'iconFinance.png', 'iconFinanceActive.png', '0', '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Bank Reconciliation', 'branchsales/cash_collection/accounts', 'iconAccountsteam.png', 'iconAccountsteam.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'finance' limit 1), '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Bank Reconciliation' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

/*----*/

/* --reports new section dec15-2017----*/



INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Sales By Cashier','branchsales/cashier_wise_sales', 'salesByCashier.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1), NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Sales By Cashier' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Cashier Tips collection','branchsales/cashier_tips_collection', 'cashierTipsCollection.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1), NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Cashier Tips collection' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Collection Difference','branchsales/collection_difference', 'collectionDifference.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1), NULL, '1', NULL, NULL);


INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Collection Difference' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Credit and Free Sale','branchsales/credit_free_sale', 'creditAndFreeSale.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1), NULL, '1', NULL, NULL);


INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Credit and Free Sale' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Cash Sale','branchsales/cash_sale', 'cashSale.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1), NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Cash Sale' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Card Sale','branchsales/card_sale', 'cardSale.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1), NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Card Sale' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Opening Amount By Branch','branchsales/opening_amount_branch', 'openingAmountByBranch.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1), NULL,'1', NULL, NULL);


INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Opening Amount By Branch' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);


INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Cashier in duty evening shift','branchsales/cashier_evening_shift', 'cashierInDutyEveningShift.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1), NULL,'1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Cashier in duty evening shift' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Cashier in duty morning Shift','branchsales/cashier_morning_shift', 'cashierInDutyMorningShift.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Cashier in duty morning Shift' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);


INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Barista in duty morning shift','branchsales/barista_morning_shift', 'baristaInDutyMorningShift.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Barista in duty morning shift' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);


INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Barista in duty evening shift','branchsales/barista_evening_shift', 'baristaInDutyEveningShift.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Barista in duty evening shift' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Supervisors Branch list','branchsales/supervisor_branch_list', 'supervisorsBranchList.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Supervisors Branch list'limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Meal consumption Branch wise','branchsales/meal_consumption_branchwise', 'mealConsumptionBranchWise.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);


INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Meal consumption Branch wise' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Branch fixed cost ','branchsales/branch_fixed_cost ', 'branchFixedCost.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch fixed cost' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Branch based on Grade','branchsales/branch_on_grade ', 'branchClassification.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' and m.parent_id = 0 limit 1),NULL,'1',NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch based on Grade' limit 1),(SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);


INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Collection Difference' limit 1), (SELECT `id` FROM`employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);


--excepted employees--------

CREATE TABLE `excepted_employees_list` ( `id` INT(10) NOT NULLAUTO_INCREMENT , `employee_id` INT(10) UNSIGNED NOT NULL , `report_type`ENUM('kpi_report','') NULL , `status` INT(10) NOT NULL DEFAULT '1'COMMENT '0=included,1=excluded' , `created_at` DATETIME NOT NULL ,`updated_at` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Excepted Employee List', 'masterresources/excepted', 'iconJobposition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Master Resources' and m.parent_id = 0 limit 1), '1', NULL, NULL);


ALTER TABLE `excepted_employees_list` ADD INDEX(`employee_id`);



ALTER TABLE `excepted_employees_list` ADD FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;


-- New menu order

UPDATE `modules` SET `menu_order` = '150' WHERE `modules`.`name` = 'HR';
UPDATE `modules` SET `menu_order` = '200' WHERE `modules`.`name` = 'Finance';
UPDATE `modules` SET `menu_order` = '250' WHERE `modules`.`name` = 'Cost Center';
UPDATE `modules` SET `menu_order` = '300' WHERE `modules`.`name` = 'Branch Sales';
UPDATE `modules` SET `menu_order` = '350' WHERE `modules`.`name` = 'Check List' AND `modules`.`parent_id`=0;
UPDATE `modules` SET `menu_order` = '400' WHERE `modules`.`name` = 'KPI Analysis';
UPDATE `modules` SET `menu_order` = '450' WHERE `modules`.`name` = 'Operation';
UPDATE `modules` SET `menu_order` = '500' WHERE `modules`.`name` = 'Requisitions';
UPDATE `modules` SET `menu_order` = '550' WHERE `modules`.`name` = 'Inventory';
UPDATE `modules` SET `menu_order` = '600' WHERE `modules`.`name` = 'Purchase';
UPDATE `modules` SET `menu_order` = '650' WHERE `modules`.`name` = 'Branches';
UPDATE `modules` SET `menu_order` = '700' WHERE `modules`.`name` = 'Supervisors';
UPDATE `modules` SET `menu_order` = '750' WHERE `modules`.`name` = 'Warehouses';
UPDATE `modules` SET `menu_order` = '800' WHERE `modules`.`name` = 'Master Resources';

ALTER TABLE `modules` ADD `class_name` VARCHAR(100) NULL AFTER `menu_order`;
UPDATE `modules` SET `class_name` = 'clsjobposition' WHERE `modules`.`name` = 'Assign Task';
UPDATE `modules` SET `class_name` = 'clsjobposition' WHERE `modules`.`name` = 'View To Do';

ALTER TABLE `user_modules` ADD `filter_by_job_position` VARCHAR(50) NULL AFTER `employee_id`;

ALTER TABLE `check_list` CHANGE `checkpoint` `checkpoint` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `check_list` CHANGE `alias` `alias` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `checklist_entry` CHANGE `comments` `comments` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

/**28-12-17**/
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Bank deposit sale','finance/bankdepositsale', 'iconCashierCollection.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' and m.parent_id = 0 limit 1),NULL,'1',NULL, NULL)
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Bank deposit sale' limit 1), (SELECT `id` FROM`employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

-- Warnings module

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Warnings', 'checklist/warnings', 'warning.png', 'warning.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'Check List' AND m.parent_id=0 limit 1), '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Warnings Report', 'checklist/warnings_report', 'warningReport.png', 'warningReport.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'Check List' AND m.parent_id=0 limit 1), '1', NULL, NULL);

CREATE TABLE `warnings` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `submitted_by` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text,
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '1-active/0-deleted',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `warnings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `warnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- menu order module

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Menu Order', 'hr/menu_order', 'menuOrder.png', 'menuOrder.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'HR' limit 1), '1', NULL, NULL);


---branch phone number----

ALTER TABLE `master_resources` ADD `branch_phone` VARCHAR(255) NULL AFTER `branch_code`;

-- Warning type

ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','CHECK_LIST_CATEGORY','WARNING_TYPE') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';

ALTER TABLE `warnings` ADD `warning_type` INT(11) NULL AFTER `submitted_by`; 

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Warning Types', 'masterresources/warning_type', 'warning.png', 'warning.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'Master Resources' limit 1), '1', NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Edit Warnings', 'checklist/editwarnings', 'warning.png', 'warning.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'Check List' AND m.parent_id=0 limit 1), '1', NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Warnings By Category', 'checklist/graphindex', 'warning.png', 'warning.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'Check List' AND m.parent_id=0 limit 1), '1', NULL, NULL);
----- Taxation module---

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Taxation', 'taxation', 'iconMis.png', 'iconMisActive.png', '0', NULL, '1', NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Tax', 'taxation/tax', 'iconPlanning.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Taxation' AND m.parent_id=0 limit 1), NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Taxation' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Tax' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','TAX') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';

ALTER TABLE `master_resources` ADD `tax_percent` FLOAT(50) NOT NULL DEFAULT '0' AFTER `bottom_sale_line`;


--pos_sales tax_in_mis/tax_in_pos----------

ALTER TABLE `pos_sales` ADD `tax_in_mis` FLOAT(25) NOT NULL DEFAULT '0' AFTER `reason_details`;

ALTER TABLE `pos_sales` ADD `tax_in_pos` FLOAT(25) NOT NULL DEFAULT '0' AFTER `tax_in_mis`;

ALTER TABLE `pos_sales` CHANGE `cash_collection` `cash_collection` FLOAT(25) NULL DEFAULT NULL;

ALTER TABLE `pos_sales` CHANGE `total_sale` `total_sale` FLOAT(25) NULL DEFAULT NULL;

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Check Points Rating Graph', 'checklist/ratingindex', 'warningReport.png', 'warningReport.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'Check List' AND m.parent_id=0 limit 1), '1', NULL, NULL);


---tax applicable from-----

ALTER TABLE `master_resources` ADD `tax_applicable_from` TIMESTAMP NULL DEFAULT NULL AFTER `tax_percent`;

----created_by in employee table---


ALTER TABLE `employees` ADD `created_by` INT(10) NULL DEFAULT NULL AFTER `division`;

ALTER TABLE `employees` ADD INDEX(`created_by`);

ALTER TABLE `employees` CHANGE `created_by` `created_by` INT(10) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE `employees` ADD FOREIGN KEY (`created_by`) REFERENCES `employees`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
---------------------------------- Uploaded upto this to live on 10-01-2018 -----


ALTER TABLE `tasks` ADD `mail_sent` SMALLINT(6) NULL DEFAULT '0' COMMENT '1-send,0-not send' AFTER `status`;
--added cash sales and bank card sale in pos_sales---

ALTER TABLE `pos_sales` ADD `cash_sale` FLOAT(25) NULL DEFAULT NULL AFTER `cash_collection`;

ALTER TABLE `pos_sales` ADD `bank_collection` FLOAT(25) NULL DEFAULT NULL AFTER `bank_sale`;

ALTER TABLE `meeting_attendees` ADD `comment` TEXT NULL AFTER `availability_status`; 

------ Need o pull to the live server
-- suggestion table

CREATE TABLE `suggestions` (
  `id` int(11) NOT NULL,
  `title` varchar(250) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8,
  `submitted_to` enum('Owner','CEO') NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` tinyint(6) NOT NULL DEFAULT '1' COMMENT '0-deleted/1-active/2-noted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `suggestions`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;


INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Suggestion', 'dashboard/suggestion', 'addSuggestion.png', 'lightAddSuggestion/darkAddSuggestion', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'View Suggestion', 'dashboard/view_suggestions', 'viewSuggestion.png', 'lightViewSuggestion/darkViewSuggestion', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL);

--------------- cut off date ----

ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','TAX','CUT_OFF_DATE') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Cut Off Date', 'master_resources/cutoffdate', 'iconLedger.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Master Resources' limit 1), NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Cut Off Date' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL)

-- Organization chart--

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Organization Chart', 'organizationchart', 'iconNavInventory.png', 'iconNavInventory.png', '0', NULL, NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Create Organization Chart', 'organizationchart/organizationchart/index', 'checkListCategory.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Organization Chart' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'List Organization Chart', 'organizationchart/organizationchart/getchartlist', 'checkListCategory.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Organization Chart' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Edit Organization Chart', 'organizationchart/organizationchart/editlist', 'checkListCategory.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Organization Chart' limit 1), NULL, '1', NULL, NULL);

ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','CHECK_LIST_CATEGORY','WARNING_TYPE','MEETING_ROOM','CHART_CATEGORY') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';

CREATE TABLE `organization_chart` (
  `id` int(11) NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `alias_name` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `category` int(11) NOT NULL,
  `based_on` enum('Job_Position','employees') NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `organization_chart`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `organization_chart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

CREATE TABLE `oraganization_chart_nodes` (
  `id` int(11) NOT NULL,
  `child_node` int(11) DEFAULT NULL,
  `parent_node` int(11) DEFAULT NULL,
  `employee_ids` varchar(250) DEFAULT NULL,
  `chart_id` int(11) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `oraganization_chart_nodes`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `oraganization_chart_nodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

---tax functions--------

ALTER TABLE `master_resources` ADD `tax_function` VARCHAR(255) NULL DEFAULT NULL AFTER `tax_applicable_from`;

/** 23-01-18***/
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Tax Report', 'finance/tax_report', 'iconCashierCollection.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Tax Report' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

** 23-01-18***/
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Sales Variance Report', 'finance/sales_variance_report', 'iconCashierCollection.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Sales Variance Report' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Newly Opening Branches', 'mis/opening_branches', 'iconCashierCollection.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'MIS' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Newly Opening Branches' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Minimum Sales Plan', 'finance/minimum_sales_plan', 'iconCashierCollection.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Minimum Sales Plan' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

--------VAT under finance----------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'VAT', 'finance/vat_report', 'iconCashierCollection.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'VAT' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL)

---attachment for suggestion-------
ALTER TABLE `suggestions` ADD `attachment` VARCHAR(255) NULL DEFAULT NULL AFTER `created_by`;

----enum fied for ceo/owner------
ALTER TABLE `suggestions` CHANGE `submitted_to` `submitted_to` ENUM('Owner','CEO','Owner/CEO') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `tasks` ADD `note` TEXT NULL AFTER `mail_sent`; 

----for sales comparison-------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Sales Comparison', 'finance/salescomparison', 'iconCashierCollection.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Sales Comparison' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL)

----sales_comparison table-------------

CREATE TABLE `mtgmis`.`sales_comparison` ( `id` INT(25) NOT NULL AUTO_INCREMENT , `month_id` INT(25) NOT NULL , `year` INT(25) NOT NULL , `sales_amount` DOUBLE NOT NULL , `status` INT(25) NOT NULL COMMENT '0=removed,1=active' , PRIMARY KEY (`id`)) ENGINE = InnoDB;

----alter sales_comparison table with company id-------

ALTER TABLE `sales_comparison` ADD `company_id` INT(10) UNSIGNED NOT NULL AFTER `id`;

ALTER TABLE `sales_comparison` ADD INDEX(`company_id`);

ALTER TABLE `sales_comparison` ADD FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;


---delete employee---
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Delete Employees', 'employee/delete', 'iconJobposition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'HR' limit 1), NULL, NULL, '1', NULL, NULL); 

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Delete Employees' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL)




---Id Professional in modules/usermodules/master_resource/employeetable-----------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'ID Professionals', 'masterresources/id_professionals', 'iconJobposition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Master Resources' limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'ID Professionals' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','CHECK_LIST_CATEGORY','WARNING_TYPE','TAX','MEETING_ROOM','CUT_OFF_DATE','CHART_CATEGORY','ID_PROFESSIONAL') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';



----gossi_number in employee table-----

ALTER TABLE `employees` ADD `gossi_number` VARCHAR(255) NULL DEFAULT NULL AFTER `passport_number`;

---date of hiring/basicsalary in employee table----

ALTER TABLE `employees` ADD `date_of_hiring` VARCHAR(255) NULL DEFAULT NULL AFTER `created_by`;

ALTER TABLE `employees` ADD `id_professional` INT(255) NULL DEFAULT NULL AFTER `date_of_hiring`;

ALTER TABLE `employees` ADD `basic_salary` DOUBLE NOT NULL DEFAULT '0' AFTER `id_professional`;

ALTER TABLE `employees` ADD `housing_allowance` DOUBLE NOT NULL DEFAULT '0' AFTER `basic_salary`;

ALTER TABLE `employees` ADD `transportation_allowance` DOUBLE NOT NULL DEFAULT '0' AFTER `housing_allowance`;

ALTER TABLE `employees` ADD `food_allowance` DOUBLE NOT NULL DEFAULT '0' AFTER `transportation_allowance`;

ALTER TABLE `employees` ADD `other_expense` DOUBLE NOT NULL DEFAULT '0' AFTER `food_allowance`;


------------payroll in modules--------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Payroll', 'hr/payroll', 'iconEligibility.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'HR' limit 1), NULL, NULL, '1', NULL, NULL);


INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Payroll' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);



---- uploaded to live server --------

-- report settings-----

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Report Settings', 'masterresources/report_settings', 'iconEligibility.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Master Resources' limit 1), NULL, '1', NULL, NULL);

CREATE TABLE `report_settings` (
  `id` int(11) NOT NULL,
  `report_name` varchar(100) NOT NULL,
  `type` enum('Daily','Weekly','Monthly') NOT NULL,
  `day` int(11) DEFAULT NULL,
  `time` varchar(20) NOT NULL,
  `send_to_emps` varchar(250) DEFAULT NULL,
  `exempted_emps` varchar(250) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '1-active/2-deleted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `report_settings`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `report_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

-- store function name in settings table for dynamic report generation

ALTER TABLE `report_settings` ADD `function_name` VARCHAR(100) NOT NULL COMMENT 'Report Generating function' AFTER `report_name`;

ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','CHECK_LIST_CATEGORY','WARNING_TYPE','TAX','MEETING_ROOM','CHART_CATEGORY','OFFICE') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Office', 'masterresources/office', 'iconCompany.png', NULL, '1', NULL, NULL, '1', NULL, NULL);


----changes in document table--------

ALTER TABLE `documents` CHANGE `document_type` `document_type` ENUM('PLANNING','KPI','RECIPE','CHART','ID_CARD','JOB_DESCRIPTION','CONTRACT','START_FROM','PERSONAL_PROFILE','QUALIFICATION','CV','JOB_OFFER','PASSPORT','ARABIC_CV') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `documents` ADD `document_owner_type` ENUM('EMPLOYEE','ASSETS','PARTY_LEDGER','CHART','KPI','PLANNING') NULL DEFAULT NULL AFTER `document_type`;

ALTER TABLE `documents` ADD `document_owner_id` INT(11) NULL DEFAULT NULL AFTER `user_id`;

ALTER TABLE `documents` ADD `document_expiry` TIMESTAMP NULL DEFAULT NULL AFTER `document_owner_id`;

UPDATE `documents` SET`document_owner_id`=`user_id` where document_type IN ('ID_CARD','JOB_DESCRIPTION','CONTRACT','START_FROM','PERSONAL_PROFILE','QUALIFICATION','CV','JOB_OFFER','PASSPORT','ARABIC_CV');

UPDATE `documents` SET `document_owner_type`='EMPLOYEE' where document_type IN ('ID_CARD','JOB_DESCRIPTION','CONTRACT','START_FROM','PERSONAL_PROFILE','QUALIFICATION','CV','JOB_OFFER','PASSPORT','ARABIC_CV');


----id profession changes------

ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','CHECK_LIST_CATEGORY','WARNING_TYPE','TAX','MEETING_ROOM','CUT_OFF_DATE','CHART_CATEGORY','ID_PROFESSION') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';

--id profession change on modules-----------

UPDATE `modules` SET `name` = 'ID Profession', `created_at` = NULL, `updated_at` = NULL WHERE `modules`.`name` = 'ID Professionals';

--------document table changes-----------------

ALTER TABLE `documents` CHANGE `document_owner_type` `document_owner_type` ENUM('EMPLOYEE','ASSETS','PARTY_LEDGER','CHART','KPI','PLANNING','RECIPE') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

------- sql field size changes 27-02-2018-------

ALTER TABLE `user_modules` CHANGE `filter_by_job_position` `filter_by_job_position` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `report_settings` CHANGE `send_to_emps` `send_to_emps` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `report_settings` CHANGE `exempted_emps` `exempted_emps` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

------ Changes in ac_accounts table for diffrent ledger types 01-03-2018---

ALTER TABLE `ac_accounts` CHANGE `party_id` `type_id` INT(11) NULL DEFAULT NULL COMMENT 'Reference to ac_party,employees,ac_asset,ac_general_ledger based on type';
ALTER TABLE `ac_accounts` DROP COLUMN asset_id;

-------Preferrsd products in supplier module 06-03-2018-------

CREATE TABLE `ac_preferred_prducts` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0-deleted/1-active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_preferred_prducts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ac_preferred_prducts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

-------- Need to upload to live server from here ---->

-------ac assets ,default null for purchase date-------
ALTER TABLE `ac_assets` CHANGE `purchase_date` `purchase_date` DATETIME NULL DEFAULT NULL; 

-------ac_party ,new field = bank_city-------
ALTER TABLE `ac_party` ADD `bank_city` VARCHAR(100) NULL DEFAULT NULL AFTER `bank_iban_no`;

-------employees ,new field = region-------
ALTER TABLE `employees` ADD `region` INT NULL DEFAULT NULL AFTER `other_expense`;

-------menu icons under finance-------
UPDATE `modules` SET `logo` = 'iconTaxReport.png' WHERE `modules`.`name` = 'Tax Report';
UPDATE `modules` SET `logo` = 'iconMinimumSalesPlan.png' WHERE `modules`.`name` = 'Minimum Sales Plan';
UPDATE `modules` SET `logo` = 'iconSalesVarianceReport.png' WHERE `modules`.`name` = 'Sales Variance Report';

-------ac_party ,new field = company_name , bank_name-------
ALTER TABLE `ac_party` ADD `company_name` VARCHAR(200) NULL DEFAULT NULL AFTER `contact_number`;
ALTER TABLE `ac_party` ADD `bank_name` VARCHAR(200) NULL DEFAULT NULL AFTER `nationality`;

--------create purchaser order list module and add new fileds-------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Purchase Orders','requisitions/purchase_order_list', 'iconPurchaseRequest.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

ALTER TABLE `ac_purchase_order` ADD `quotation` DOUBLE NULL AFTER `amount`, ADD `payment_term` ENUM('Cash','Credit') NULL AFTER `quotation`, ADD `credit_days` VARCHAR(50) NULL AFTER `payment_term`, ADD `other_reference` TEXT NULL AFTER `credit_days`, ADD `delivery_port` TEXT NULL AFTER `other_reference`, ADD `delivery_destination` TEXT NULL AFTER `delivery_port`;
ALTER TABLE `ac_purchase_order` ADD `delivery_terms` TEXT NULL AFTER `delivery_destination`;
ALTER TABLE `ac_purchase_order` ADD `despatch_way` ENUM('By_Air','By_Road','By_Sea','Others') NULL AFTER `delivery_terms`, ADD `other_ways` TEXT NULL AFTER `despatch_way`;
ALTER TABLE `ac_purchase_order` ADD `bank_other_reference` TEXT NULL AFTER `other_reference`;
ALTER TABLE `ac_purchase_order` ADD `order_status` SMALLINT NOT NULL DEFAULT '2' COMMENT '1-submitted/2-not submitted' AFTER `mailed_status`;

--------changing menu(budget plan -> budget variance)-------------
UPDATE `modules` SET `name` = 'Budget Variance' WHERE `modules`.`name`='Budget Plan'; 

-------------description ac_payment advice table -------------------
ALTER TABLE `ac_payment_advice` ADD `description` TEXT NULL AFTER `total_amount`;

-----------------comment change only---------------------
ALTER TABLE `requisition` CHANGE `convert_to_payment` `convert_to_payment` SMALLINT(6) NOT NULL DEFAULT '2' COMMENT 'convert to payment advice =1 when requisition completed/2 othervise';

-------------quotation field change------------------------
ALTER TABLE `ac_purchase_order` CHANGE `quotation` `quotation` VARCHAR(200) NULL DEFAULT NULL;

------------new type to documents table-------------------
ALTER TABLE `documents` CHANGE `document_type` `document_type` ENUM('PLANNING','KPI','RECIPE','CHART','ID_CARD','JOB_DESCRIPTION','CONTRACT','START_FROM','PERSONAL_PROFILE','QUALIFICATION','CV','JOB_OFFER','PASSPORT','ARABIC_CV','VAT_CERTIFICATE','COMPANY_PROFILE','VENDOR_CONTRACT','ASSET_IMAGE_1','ASSET_IMAGE_2','ASSET_IMAGE_3','ASSET_DOC_1','ASSET_DOC_2','ASSET_DOC_3') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

-------------------menu changes----------------------------
UPDATE `modules` SET is_removable=0 WHERE name='To Do';
UPDATE `modules` SET is_removable=0 WHERE name='View Plan';
UPDATE `modules` SET is_removable=0 WHERE name='Create Plan';
UPDATE `modules` SET is_removable=0 WHERE name='History';
UPDATE `modules` SET is_removable=0 WHERE name='Assign Task List';
UPDATE `modules` SET is_removable=0 WHERE name='Track Task';
UPDATE `modules` SET is_removable=0 WHERE name='Add Suggestion';
UPDATE `modules` SET is_removable=0 WHERE name='View Suggestion';

------new update on menu
UPDATE `modules` SET is_removable=1 WHERE name='View Suggestion';