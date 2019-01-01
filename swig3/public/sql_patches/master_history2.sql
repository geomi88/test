
-------------reqistion table, adding comment-------------
ALTER TABLE `requisition` CHANGE `branch_id` `branch_id` INT(11) NULL DEFAULT NULL COMMENT 'Office/Branch/Warehouse';

----------remove unwanted modules---------------------
DELETE FROM `user_modules` WHERE `module_id`=(SELECT id from modules where name='Requisition Report');
DELETE FROM `modules` WHERE name='Requisition Report';

DELETE FROM `user_modules` WHERE `module_id`=(SELECT id from modules where name='Branch fixed cost');
DELETE FROM `modules` WHERE name='Branch fixed cost';

DELETE FROM `user_modules` WHERE `module_id`=(SELECT id from modules where name='Branch based on Grade');
DELETE FROM `modules` WHERE name='Branch based on Grade';

-----------------New update starts here 22-06-2018---------------------------------

------------------Maintenance In Pending----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Maintenance In Pending','requisition/maintenance_in_pending', 'iconMaintenanceReport.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

-----------------Requisition table new fileds-----------------
ALTER TABLE `requisition` ADD `workduration` VARCHAR(100) NULL AFTER `general_ledger`, 
ADD `no_of_emp_engaged` SMALLINT(5) NULL AFTER `workduration`, 
ADD `expenditure` DOUBLE NULL AFTER `no_of_emp_engaged`, 
ADD `document_url` TEXT NULL AFTER `expenditure`, 
ADD `maintenance_desc` TEXT NULL AFTER `document_url`, 
ADD `maintenance_status` SMALLINT(5) NULL DEFAULT '1' COMMENT '1-pending/2-completed' AFTER `maintenance_desc`;

ALTER TABLE `requisition` ADD `completed_date` DATE NULL AFTER `maintenance_desc`;

------------------Maintenance Requisition Status----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Maintenance Requisition Status','requisition/maintenance_status', 'iconMaintenanceRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

-----------------------Update in live up to here 25-06-18----------------------

-------------------Maodule name change -----------------------------------------
UPDATE `modules` SET `name`='Maintenance Work Status' WHERE name='Maintenance Requisition Status'

---------------------menu logo change--------------------------------------------
UPDATE `modules` SET `logo`='iconApprovedRequisitionPayment.png' WHERE name='Approved Requisitions For Payment';
UPDATE `modules` SET `logo`='iconMaintenancePending.png' WHERE name='Maintenance In Pending';
UPDATE `modules` SET `logo`='iconWorkingStatus.png' WHERE name='Maintenance Work Status';

----------------------------Add new resource type--------------------------------
ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','CHECK_LIST_CATEGORY','WARNING_TYPE','TAX','MEETING_ROOM','CHART_CATEGORY','OFFICE','ID_PROFESSION','STAFF_HOUSE') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';
ALTER TABLE `master_resources` ADD `staffhouseregion` INT(11) NULL AFTER `tax_function`;


---------------------New changes on organization chart 13-9-2018-------------------------------------------
DELETE FROM `oraganization_chart_nodes`;
DELETE FROM `organization_chart`;
ALTER TABLE `oraganization_chart_nodes` ADD `node_name` VARCHAR(250) NULL AFTER `chart_id`; 
ALTER TABLE `organization_chart` CHANGE `based_on` `based_on` ENUM('Job_Position','employees','Custom') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

update modules set url='organizationchart/organizationchartnew/add' WHERE name='Create Organization Chart';
update modules set url='organizationchart/organizationchartnew/getchartlist' WHERE name='List Organization Chart';
update modules set url='organizationchart/organizationchartnew/editlist' WHERE name='Edit Organization Chart';

-------- live issue organization chart-----

ALTER TABLE `oraganization_chart_nodes` CHANGE `employee_ids` `employee_ids` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL;

-------------Cost name related changes ------------

-----------cost id in branch_fixed_cost table------------
ALTER TABLE `branch_fixed_cost` ADD `cost_id` INT(11) NULL AFTER `branch_id`; 

----------new master resource cost name------------
ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','CHECK_LIST_CATEGORY','WARNING_TYPE','TAX','MEETING_ROOM','CHART_CATEGORY','OFFICE','ID_PROFESSION','STAFF_HOUSE','COST_NAME') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';

-------------------Master module menu------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Cost Name','masterresources/costname', 'iconChartCategory.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Master Resources' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

------------------- module Cash Flow menu 12-10-2018------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Cash Flow','finance/cashflow', 'iconBudgetCreation.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Finance' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Cash Flow' limit 1), 1, NULL, NULL);

-----------------------menu name change-----------------------------
UPDATE `modules` SET `name`='ISO Rules' WHERE name='Organization Chart';


------------------------Reception Main menu-----------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Reception', 'reception', 'iconSupervisor.png', 'iconSupervisor.png', '0', NULL, NULL, '1', NULL, NULL);

------------------------Reception sub menu-----------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Visitors Log','reception/visitors_log', 'iconOffice.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Reception' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);



-----------------visitors log table----------------------------
CREATE TABLE `visitors_log` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `company` varchar(250) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `date_time` datetime NOT NULL,
  `to_meet` int(11) NOT NULL,
  `purpose` text NOT NULL,
  `status` tinyint(5) NOT NULL DEFAULT '1' COMMENT '1-active/2-deleted',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `visitors_log`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `visitors_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;
  
----------------visitors list submenu----------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Visitors List','reception/visitors_list', 'iconListEmployees.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Reception' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);


------------POLICY MASTER 16-10-18----------------------------
ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','CHECK_LIST_CATEGORY','WARNING_TYPE','TAX','MEETING_ROOM','CHART_CATEGORY','OFFICE','ID_PROFESSION','STAFF_HOUSE','COST_NAME','POLICY_MASTER') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';

-------------------Master module menu------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Policy Master','masterresources/policy_master', 'iconPlanning.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Master Resources' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

-------------------module create policy------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Create Policy','organizationchart/policy', 'iconLeaveRequest.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'ISO Rules' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

-------------------module list policy------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Policy List','organizationchart/policy_list', 'iconPlanning.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'ISO Rules' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

---------------create policy table---------------------------
CREATE TABLE `policy` (
  `id` int(11) NOT NULL,
  `policy_master_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-active/2-deleted',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8

ALTER TABLE `policy`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `policy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;


-------------------punch perfomance modules------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Punch Performance','organizationchart/punch_performance', 'iconEmployJobPosition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'ISO Rules' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Punch View','organizationchart/getemployesrating', 'iconEmployeeStatus.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'ISO Rules' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Edit Punch Performance','organizationchart/puncheditindex', 'iconEmployJobPosition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'ISO Rules' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Performance Report','organizationchart/punchreport', 'exceptedEmployees.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'ISO Rules' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

-----------------------punch_performance---------------------------------
CREATE TABLE `punch_performance` (
  `id` int(11) NOT NULL,
  `employe_id` int(11) NOT NULL,
  `rated_by` int(11) NOT NULL,
  `rating` smallint(5) NOT NULL COMMENT '1-Exceptional/2-Effective/3-Inconsistent/4-Unsatisfactory/5-Not Acceptable',
  `reason` text NOT NULL,
  `status` tinyint(5) NOT NULL DEFAULT '1' COMMENT '1-active/2-deleted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `punch_performance`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `punch_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;


------------------Trainig module quereis-----------------------------------
ALTER TABLE `tasks` CHANGE `task_type` `task_type` SMALLINT(6) NOT NULL DEFAULT '0' COMMENT '0-todo,1-plan,2-assigned,3-meeting,4-Agenda,5-Training';


------------------------Training Main menu-----------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Training', 'training', 'iconHr.png', 'iconHr.png', '0', NULL, NULL, '1', NULL, NULL);

------------------------Training sub menu-----------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Create Training','training/createtraining', 'iconCreateMeeting.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Training' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Training List','training/training_list', 'iconMeetingList.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Training' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Training Performance','training/training_performance', 'iconEmployJobPosition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Training' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Performance View (MTG Employees)','training/getemployesrating', 'iconEmployeeStatus.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Training' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Performance View (New Employees)','training/getnewemployesrating', 'iconEmployeeStatus.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Training' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

--------------training_attendees----------------------
CREATE TABLE `training_attendees` (
  `id` int(11) NOT NULL,
  `training_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(250) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_phone` varchar(50) DEFAULT NULL,
  `availability_status` smallint(5) NOT NULL DEFAULT '1' COMMENT '1-available,2-not available',
  `comment` text,
  `is_organizer` smallint(5) NOT NULL DEFAULT '1' COMMENT '1-organizer/2-not organizer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `training_attendees`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `training_attendees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;


CREATE TABLE `training_performance` (
  `id` int(11) NOT NULL,
  `trainee_id` int(11) DEFAULT NULL,
  `traine_type` tinyint(4) NOT NULL COMMENT '1-employe/2-new employe',
  `phone` varchar(50) DEFAULT NULL COMMENT 'only for new employee',
  `rated_by` int(11) NOT NULL,
  `rating` smallint(5) NOT NULL COMMENT '1-Exceptional/2-Effective/3-Inconsistent/4-Unsatisfactory/5-Not Acceptable',
  `reason` text NOT NULL,
  `status` tinyint(5) NOT NULL DEFAULT '1' COMMENT '1-active/2-deleted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `training_performance`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `training_performance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

------------------training_performance changes-------------------------
ALTER TABLE `training_performance` CHANGE `phone` `guest_phone` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'only for new employee';
ALTER TABLE `training_performance` ADD `guest_email` VARCHAR(100) NULL AFTER `guest_phone`, ADD `guest_name` VARCHAR(250) NULL AFTER `guest_email`;

--------------------till here executed in live-----------------------------

--------------------new modules under training-----------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Training Report (MTG Employees)','training/punchreport', 'exceptedEmployees.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Training' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Training Report (New Employees)','training/punchreportnew', 'exceptedEmployees.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Training' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

--------------------till here executed in live-----------------------------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'KPI', 'dashboard/employeekpi', 'iconPlan.png', 'lightYellow/darkYellow', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL);

--------------------till here executed in live-----------------------------
-------------------------import requisition ---------------------------
INSERT INTO `requisition_types` (`id`, `name`, `alias_name`, `do_payment`, `make_purchase_order`, `status`, `created_at`, `updated_at`) VALUES (NULL, 'Import Purchase Requisition', '', '1', '1', '1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Import Purchase Requisition','requisitions/import_purchase_requisition/add', 'iconPurchaseRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

UPDATE `modules` SET `is_removable` = '0' WHERE `modules`.`url` = 'dashboard/employeekpi';

------------------elegant club(new module)----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Elegant Club', 'elegantclub', 'iconHr.png', 'iconHr.png', '0', NULL, NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Corporate Customers', 'elegantclub/add_corporate_customer', 'iconAddConsumer.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'List Corporate Customers', 'elegantclub/list_corporate_customer', 'iconListConsumer.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Edit Corporate Customers', 'elegantclub/edit_corporate_customer', 'iconEditConsumer.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Delete Corporate Customers', 'elegantclub/delete_corporate_customer', 'iconPlanning.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1), '1', NULL, '2017-12-01 23:43:20', '2017-12-01 23:43:20');
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Add Corporate Customers' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2017-12-01 23:43:20', '2017-12-01 23:43:20');
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'List Corporate Customers' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2017-12-01 23:43:20', '2017-12-01 23:43:20') ;
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Edit Corporate Customers' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2017-12-01 23:43:20', '2017-12-01 23:43:20') ;
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Delete Corporate Customers' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2017-12-01 23:43:20', '2017-12-01 23:43:20');

----------------------new table customers-------------------------------
CREATE TABLE `mtg`.`customers` ( `id` INT NOT NULL AUTO_INCREMENT , `account_group` INT NULL , `customer_code` VARCHAR(100) NOT NULL , `name_english` VARCHAR(100) NOT NULL , `name_arabic` VARCHAR(100) NULL , `nationality` INT NULL , `cr_number` VARCHAR(100) NULL , `vat_number` VARCHAR(100) NULL , `po_box` VARCHAR(100) NULL , `building_name` VARCHAR(100) NULL , `street_name` VARCHAR(100) NULL , `city` VARCHAR(100) NULL , `detail_address` TEXT NOT NULL , `latitude` VARCHAR(100) NULL , `longitude` VARCHAR(100) NULL , `website` VARCHAR(100) NULL , `business_type` VARCHAR(100) NOT NULL , `nature_of_current_business` TEXT NOT NULL , `contact_person` VARCHAR(100) NOT NULL , `mobile_1` VARCHAR(100) NOT NULL , `mobile_2` VARCHAR(100) NULL , `land_phone` VARCHAR(100) NULL , `email_1` VARCHAR(100) NULL , `email_2` VARCHAR(100) NULL , `job_position` VARCHAR(100) NOT NULL , `id_number` VARCHAR(100) NULL , `busines_target_per_month` VARCHAR(100) NOT NULL , `interested_products` TEXT NULL , `comments_from_customer` TEXT NULL , `comments_about_customer` TEXT NULL , `vat_certificate` VARCHAR(255) NULL , `company_profile` VARCHAR(255) NULL , `vendor_contract_copy` VARCHAR(255) NULL , `created_at` TIMESTAMP NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

-------------customer table changes---------------------------------
ALTER TABLE `customers` ADD `status` TINYINT NOT NULL DEFAULT '1' COMMENT '1-active/0-disabled/2-deleted' AFTER `vendor_contract_copy`;
ALTER TABLE `customers` CHANGE `created_at` `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `customers` CHANGE `name_english` `name_english` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `customers` CHANGE `name_arabic` `name_arabic` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `customers` CHANGE `building_name` `building_name` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `customers` CHANGE `contact_person` `contact_person` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-------------------till executed in live 5-11-2018----------------------

-------------changes in customer table--------------------
ALTER TABLE `customers` ADD `nationality_contact_person` INT NOT NULL AFTER `land_phone`;
---7 Nov 2018--
ALTER TABLE `customers` ADD `customer_type` TINYINT(6) NOT NULL COMMENT '0-local / 1-international' AFTER `name_arabic`;
ALTER TABLE `customers` ADD `created_by` INT NOT NULL COMMENT 'Reference to Employees table' AFTER `vendor_contract_copy`;
---8 Nov 2018---
ALTER TABLE `customers` CHANGE `busines_target_per_month` `busines_target_per_month` DOUBLE NOT NULL;

----------ac_party, New field cr_number, supplier_icode-----------
ALTER TABLE `ac_party` ADD `cr_number` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `bank_iban_no`;
ALTER TABLE `inventory` ADD `supplier_icode` VARCHAR(100) NULL AFTER `pic_url`;

-- ######################### till executed in live 9-11-2018 ########################---

---------------------Purchase Order Changes-------------------------
ALTER TABLE `requisition` ADD `order_type` TINYINT NOT NULL DEFAULT '1' COMMENT '1-other/2-local/3-import' AFTER `requisition_type`;
ALTER TABLE `ac_purchase_order` CHANGE `payment_advice_id` `payment_advice_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `ac_purchase_order` CHANGE `from_ledger_id` `from_ledger_id` INT(11) NULL DEFAULT NULL;
ALTER TABLE `ac_purchase_order` CHANGE `from_ledger_type` `from_ledger_type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `ac_purchase_order` ADD `order_type` TINYINT NOT NULL DEFAULT '1' COMMENT '1-other/2-local/3-import ' AFTER `order_code`;

ALTER TABLE `ac_purchase_order` CHANGE `payment_term` `payment_term` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `ac_purchase_order` ADD `quotation_date` DATE NULL AFTER `quotation`;
ALTER TABLE `ac_purchase_order` ADD `delivery_date` DATE NULL AFTER `delivery_destination`;
ALTER TABLE `ac_purchase_order` CHANGE `delivery_terms` `delivery_terms1` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `ac_purchase_order` ADD `delivery_terms2` TEXT NULL AFTER `delivery_terms1`;

------------------------requisition changes ------------------------------
ALTER TABLE `requisition` ADD `total_vat` DOUBLE NOT NULL DEFAULT '0' AFTER `total_price`;
ALTER TABLE `requisition_history` ADD `total_vat` DOUBLE NOT NULL DEFAULT '0' AFTER `total_price`;

-------------------till executed in developement server 12-11-2018----------------------

---------new table, employee_warehouse_allocation-------------------

CREATE TABLE `employee_warehouse_allocation` ( `id` INT NOT NULL AUTO_INCREMENT , `warehouse_id` INT NOT NULL COMMENT 'reference to master resources' , `employee_id` INT NOT NULL COMMENT 'reference to employees' , `company_id` INT NOT NULL COMMENT 'reference to companies' , `created_at` TIMESTAMP NULL , `updated_at` TIMESTAMP NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `employee_warehouse_allocation` ADD `status` SMALLINT NOT NULL COMMENT '0-disable/1-active/2-deleted' AFTER `company_id`;
------------new table, declaration-------------------
CREATE TABLE `declaration` ( `id` INT NOT NULL AUTO_INCREMENT , `declaration_content` VARCHAR(255) NOT NULL , `declaration_content_alias` VARCHAR(255) NOT NULL , `company_id` INT NOT NULL , `status` INT NOT NULL , `created_at` TIMESTAMP NULL , `updated_at` TIMESTAMP NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
------------new table, customer_orders-----------------------
CREATE TABLE `customer_orders` ( `id` INT NOT NULL AUTO_INCREMENT , `order_from` INT NOT NULL , `order_to` INT NOT NULL , `company_id` INT NOT NULL , `status` INT NOT NULL COMMENT '1-new' , `total_amount` DOUBLE NOT NULL , `created_at` TIMESTAMP NULL , `updated_at` TIMESTAMP NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;
------------new table, customer_order_details---------------------
CREATE TABLE `customer_order_details` ( `id` INT NOT NULL AUTO_INCREMENT , `order_id` INT NOT NULL , `inventory_id` INT NOT NULL , `quantity` INT NOT NULL , `vat_amount` DOUBLE NOT NULL , `individual_price` DOUBLE NOT NULL , `created_at` TIMESTAMP NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
-----------new table, customer_order_status_history----------------------
CREATE TABLE `customer_order_status_history` ( `id` INT NOT NULL AUTO_INCREMENT , `customer_order_id` INT NOT NULL , `status` SMALLINT NOT NULL , `date_time` DATE NOT NULL , `created_at` TIMESTAMP NULL , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;

------------new module under Elegant Club, (Warehose Allocation)-----------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Warehose Allocation', 'elegantclub/warehose_allocation', 'iconEmployJobPosition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Warehose Allocation' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2017-12-01 23:43:20', '2017-12-01 23:43:20');

-------------------till executed in developement server 13-11-2018----------------------


------------13 Nov 2018, new module under Elegant Club , Elegant Declaration----------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Elegant Declaration', 'elegantclub/declaration', 'iconTopCashierDeposit.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL)
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Declaration' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Elegant Club' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-13 23:43:20', '2018-11-13 23:43:20')

-------new field, title in declaration table---------------
ALTER TABLE `declaration` ADD `title` VARCHAR(100) NOT NULL AFTER `id`;
ALTER TABLE `declaration` CHANGE `declaration_content_alias` `declaration_content_alias` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `declaration` CHANGE `status` `status` TINYINT NOT NULL COMMENT '0-disable/1-active/2-deleted';

---------------new module, crm-----------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'CRM', 'crm', 'iconOperation.png', 'iconOperation.png', '0', '60', NULL, '1', '2018-11-13 14:28:59', '2018-11-13 14:28:59')
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1), '1', NULL, '2017-12-01 23:43:20', '2017-12-01 23:43:20')

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Enter Data', 'crm/enter_data', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL)
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Enter Data' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35')

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'All Customers', 'crm/all_customers', 'iconEmployeeStatus.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL)
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'All Customers' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35')

------------new table, crm_customers-----------------
CREATE TABLE `crm_customers` ( `id` INT NOT NULL AUTO_INCREMENT ,
     `name` VARCHAR(200) NOT NULL , 
    `mobile_number` VARCHAR(20) NOT NULL , 
    `branch_id` INT NOT NULL , 
    `created_by` INT NOT NULL , 
    `status` TINYINT NOT NULL COMMENT '0-disable/1-active/2-deleted' , 
    `created_at` TIMESTAMP NULL , 
    `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL , 
    PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;

-----------Drop unwanted fields in inventory-------------------------
ALTER TABLE `inventory` DROP `is_consumable`, DROP `min_branch_stock`, DROP `max_branch_stock`, DROP `price`;


--------------------New fields in requisition and po-----------------------------
ALTER TABLE `requisition` ADD `payment_mode` ENUM('Cash','Credit') NOT NULL AFTER `maintenance_status`, ADD `creditdays` INT NULL AFTER `payment_mode`, ADD `payment_terms` VARCHAR(250) NULL AFTER `creditdays`, ADD `delivery_place` VARCHAR(250) NULL AFTER `payment_terms`, ADD `delivery_date` DATE NULL AFTER `delivery_place`;
ALTER TABLE `requisition` CHANGE `payment_mode` `payment_mode` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'Cash/Credit';
ALTER TABLE `ac_purchase_order` ADD `payment_mode` VARCHAR(20) NULL AFTER `payment_term`;
ALTER TABLE `requisition` CHANGE `creditdays` `creditdays` INT(11) NULL;
ALTER TABLE `requisition` CHANGE `creditdays` `creditdays` VARCHAR(20) NULL DEFAULT NULL;


-------------- crm_feedbacks 19-11-2018  ---------------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'CRM Feedbacks', 'crm/crm_feedback/add', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM Feedbacks' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35');


CREATE TABLE `crm_feedbacks` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `customer_name` varchar(100) NOT NULL,
 `mobile_number` varchar(25) NOT NULL,
 `customer_comment` text NOT NULL,
 `branch_id` int(11) NOT NULL,
 `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '1-active/0-delete',
 `created_by` int(11) NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-------------  all_crm feedback  -----------------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'All CRM', 'crm/all_crm_feedback', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'All CRM' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35');


---###################till executed in Live server 19-11-2018###################----


------------------ Task 6004 (Change Enter Data to Customer Data) -------------------

UPDATE modules set `name` = 'Customer Data' WHERE name = 'Enter Data';


-----------------====================== Elegant Declaration ============---------------

ALTER TABLE `declaration`
  DROP `declaration_content_alias`;

ALTER TABLE `declaration` ADD `created_by` INT(11) NOT NULL AFTER `company_id`;


------------------------RFQ Modules menu------------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Create RFQ','requisitions/rfq/add', 'iconInsurance.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'RFQ List','requisitions/rfq', 'iconInsurance.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Edit RFQ','requisitions/editrfq', 'iconInsurance.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1),NULL,'1', NULL, NULL);

-------------------table rfq 21-11-2018----------------------------------

CREATE TABLE `rfq` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `rfq_code` varchar(100) NOT NULL,
 `company_id` int(11) NOT NULL,
 `supplier_id` int(11) NOT NULL,
 `title` varchar(250) NOT NULL,
 `description` text,
 `doc_url` text,
 `isallproducts` tinyint(2) NOT NULL COMMENT '1=allproducts/2-preferred products ',
 `total_price` double DEFAULT '0',
 `total_vat` double NOT NULL DEFAULT '0',
 `payment_mode` varchar(20) DEFAULT NULL COMMENT 'Cash/Credit',
 `creditdays` varchar(20) DEFAULT NULL,
 `payment_terms` varchar(250) DEFAULT NULL,
 `delivery_place` varchar(250) DEFAULT NULL,
 `delivery_date` date DEFAULT NULL,
 `stock_in_hand` double DEFAULT '0',
 `stock_period_from` date DEFAULT NULL,
 `stock_period_to` date DEFAULT NULL,
 `stock_total` double DEFAULT '0',
 `forecast_from` date DEFAULT NULL,
 `forecast_to` date DEFAULT NULL,
 `product_spec` text,
 `isapprovedsupplier` varchar(10) NOT NULL COMMENT 'Yes/No',
 `last_purchase_date` date DEFAULT NULL,
 `last_qty` double DEFAULT NULL,
 `last_value` double DEFAULT NULL,
 `confirm_status` tinyint(4) NOT NULL DEFAULT '2' COMMENT '1-confirmed/2-not confirmed',
 `created_by` int(11) NOT NULL,
 `status` tinyint(4) NOT NULL,
 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-------------------table rfq_items--------------------

CREATE TABLE `rfq_items` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `rfq_code` varchar(100) NOT NULL,
 `rfq_id` int(11) NOT NULL,
 `item_id` int(11) NOT NULL,
 `purchase_in_primary_unit` tinyint(4) NOT NULL COMMENT '1=purchase in primary unit, 0=purchase in alternate unit ',
 `alternate_unit_id` int(11) DEFAULT NULL,
 `quantity` float NOT NULL,
 `qty_in_primary` float DEFAULT NULL,
 `unit_price` double DEFAULT '0',
 `total_price` double DEFAULT '0',
 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

--------------------------------requisition_items changes-------------------------------------
ALTER TABLE `requisition_items` ADD `rfq_id` INT(11) NULL AFTER `total_price`, ADD `rfq_code` VARCHAR(100) NULL AFTER `rfq_id`;

-----------  Leave Requistition  ----------------

ALTER TABLE `requisition` CHANGE `leave_length` `leave_length` INT(11) NULL DEFAULT NULL COMMENT '0-full day,1-half day,2-annual vacation,3- sick leave,4-Maternity leave,5-emergency leave,6-business leave';

ALTER TABLE `rfq` ADD `planning_date` DATE NULL AFTER `delivery_place`;
ALTER TABLE `rfq` ADD `confirmed_by` INT(11) NULL AFTER `confirm_status`; 

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'RFQ', 'rfq', 'iconChecklist.png', 'iconChecklist.png', '0', NULL, NULL, '1', NULL, NULL);

UPDATE modules SET parent_id=245 WHERE url='requisitions/rfq/add';
UPDATE modules SET parent_id=245 WHERE url='requisitions/rfq';
UPDATE modules SET parent_id=245 WHERE url='requisitions/editrfq';

ALTER TABLE `rfq` ADD `mailed_status` TINYINT NOT NULL DEFAULT '0' AFTER `confirm_status`;

--------------------- Approved Rfq List ----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Approved RFQ List', 'requisitions/approvedlist', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'RFQ' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

------Nov 28---------Drawing Requisition----------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Owner Drawings Requisition', 'requisitions/drawing_requsition/add', 'iconSupervisorgraph.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL)
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Add Owner Drawings Requisition' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1) limit 1), '1', NULL, NULL, NULL)
INSERT INTO `requisition_types` (`id`, `name`, `alias_name`, `do_payment`, `make_purchase_order`, `status`, `created_at`, `updated_at`) VALUES (NULL, 'Owner Drawings Requisition', '', '1', '2', '1', '2018-10-29 13:28:41', '2018-10-29 17:32:55')

ALTER TABLE `ac_payment_advice` ADD `is_owner_drawing_requsition` INT NOT NULL DEFAULT '2' COMMENT '1=owner_drawing_requsition/2=not_owner_drawing_requsition' AFTER `created_by`;

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Owner Drawings Payment Advice List', 'requisitions/drawing_requsition_payment_advice/list', 'iconPaymentAdviceList.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL)

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'List Owner Drawings Requisition', 'requisitions/drawing_requsition/list', 'iconCompletedRequisition.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL)


-------------------till executed in developement server 29-11-2018----------------------
---###################till executed in Live server 29-11-2018###################----
