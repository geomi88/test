

ALTER TABLE `inventory` ADD `primary_unit` INT( 11 ) UNSIGNED NOT NULL AFTER `inventory_category_id` ,
ADD `warehouse_id` INT( 11 ) UNSIGNED NOT NULL COMMENT 'Foreign key to unit table' AFTER `primary_unit` ,
ADD `description` TEXT NULL COMMENT 'Foreign key to master resources' AFTER `warehouse_id` ,
ADD `pic_url` VARCHAR( 2000 ) NULL AFTER `description` ;

ALTER TABLE `inventory` ADD `min_branch_stock` INT( 11 ) NULL COMMENT 'minimum branch stock' AFTER `warehouse_id` ,
ADD `max_branch_stock` INT( 11 ) NULL COMMENT 'maximum branch stock' AFTER `min_branch_stock` ;

ALTER TABLE `inventory` ADD `price` INT( 11 ) NULL AFTER `max_branch_stock` ;
ALTER TABLE `inventory` ADD `status` INT( 11 ) NOT NULL AFTER `pic_url` ;
ALTER TABLE `inventory` DROP `inventory_sub_category_id` ;
ALTER TABLE `inventory` CHANGE `created_at` `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE `inventory` CHANGE `updated_at` `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL ;

ALTER TABLE `inventory` ADD INDEX ( `primary_unit` ) ;
ALTER TABLE `inventory` ADD INDEX ( `warehouse_id` ) ;
ALTER TABLE `inventory` ADD FOREIGN KEY ( `primary_unit` ) REFERENCES `mtg`.`units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `inventory` ADD FOREIGN KEY ( `warehouse_id` ) REFERENCES `mtg`.`master_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

CREATE TABLE IF NOT EXISTS `inventory_alternate_units` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `inventory_item_id` int(11) unsigned NOT NULL COMMENT 'foreign key to inventory_items table',
  `unit_id` int(11) unsigned NOT NULL COMMENT 'forign key to units table',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `inventory_alternate_units` ADD `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT NULL AFTER `created_at` ;

ALTER TABLE `inventory_alternate_units` ADD INDEX ( `inventory_item_id` ) ;
ALTER TABLE `inventory_alternate_units` ADD INDEX ( `unit_id` ) ;

ALTER TABLE `inventory_alternate_units` ADD FOREIGN KEY ( `inventory_item_id` ) REFERENCES `mtg`.`inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

CREATE TABLE IF NOT EXISTS `item_request` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `branch_id` int(11) unsigned DEFAULT NULL COMMENT 'related to master resource table',
  `ordered_by` int(11) unsigned DEFAULT NULL COMMENT 'related to employee table',
  `request_status` enum('In_Progress','In_Transit','Completed','Holded','Rejected') NOT NULL COMMENT 'request status',
  `warehouse_id` int(11) unsigned DEFAULT NULL COMMENT 'related to master resource table',
  `created_at` timestamp ,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `item_request_details` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_request_id` int(11) unsigned NOT NULL COMMENT 'reference to item_request table',
  `inventory_id` int(11) unsigned NOT NULL COMMENT 'reference to master resource',
  `request_quantity` int(11) NOT NULL COMMENT 'requested quantity',
  `approved_quantity` int(11) DEFAULT NULL COMMENT 'approved quantity',
  `in_final_order` tinyint(5) DEFAULT NULL,
  `unit` int(11) unsigned NOT NULL COMMENT 'requested unit',
  `created_at` timestamp ,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


ALTER TABLE `item_request` ADD INDEX ( `branch_id` ) ;
ALTER TABLE `item_request` ADD INDEX ( `ordered_by` ) ;
ALTER TABLE `item_request` ADD INDEX ( `warehouse_id` ) ;

ALTER TABLE `item_request_details` ADD INDEX ( `item_request_id` ) ;
ALTER TABLE `item_request_details` ADD INDEX ( `inventory_id` ) ;
ALTER TABLE `item_request_details` ADD INDEX ( `unit` ) ;

ALTER TABLE `item_request` ADD FOREIGN KEY ( `branch_id` ) REFERENCES `mtg`.`master_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `item_request` ADD FOREIGN KEY ( `ordered_by` ) REFERENCES `mtg`.`employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `item_request` ADD FOREIGN KEY ( `warehouse_id` ) REFERENCES `mtg`.`master_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `item_request_details` ADD FOREIGN KEY ( `item_request_id` ) REFERENCES `mtg`.`item_request` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `item_request_details` ADD FOREIGN KEY ( `inventory_id` ) REFERENCES `mtg`.`inventory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `item_request_details` ADD FOREIGN KEY ( `unit` ) REFERENCES `mtg`.`units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;



ALTER TABLE `master_resources` ADD `warehouse_manager` INT( 11 ) UNSIGNED NULL DEFAULT NULL COMMENT 'Ware house manager' AFTER `warehouse_id` ;

CREATE TABLE IF NOT EXISTS `item_request_tracking` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `inventory_request_id` int(11) unsigned NOT NULL,
  `status` enum('In_Progress','In_Transit','Holded','Rejected','Completed') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `item_request_tracking` ADD INDEX ( `inventory_request_id` ) ;
ALTER TABLE `item_request_tracking` ADD FOREIGN KEY ( `inventory_request_id` ) REFERENCES `mtg`.`item_request` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

CREATE TABLE IF NOT EXISTS `branch_physical_stock` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `physical_stock` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;




INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Warehouse', 'warehouse', 'iconWireHouse.png', NULL, '0', NOW(), NOW());

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Operation', 'operation', 'iconOperation.png', NULL, '0', NOW(), NOW());

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Resource Allocation', 'operation/resource_allocation', 'iconResAllocation.png', NULL, '27', NOW(), NOW());

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Resource Listing', 'operation/resource_listing', 'iconResList.png', NULL, '27', NOW(), NOW());

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Physical Stock', 'branchsales/physicalstock', 'iconPlanning.png', NULL, '6', NOW(), NOW());

INSERT INTO `mtg`.`user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, '65', '1', NOW(), NOW());

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'KPI', 'kpi', 'iconRequisition.png', 'iconRequisitionActive.png', '0', NOW(), NOW());

ALTER TABLE `item_request_details` ADD `received_quantity` INT( 11 ) NULL AFTER `approved_quantity` ;
ALTER TABLE `item_request` ADD `request_id` VARCHAR( 50 ) NOT NULL AFTER id;

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Warehouse', 'warehouse', 'iconWireHouse.png', 'iconWireHouse.png', '0', NOW(), NOW());
INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Supervisors', 'supervisors', 'iconSupervisor.png', 'iconSupervisor.png', '0', NOW(), NOW());
INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Inventory', 'inventory', 'iconBranchSale.png', 'iconBranchSaleActive.png', '0', NOW(), NOW());

ALTER TABLE `item_request_details` CHANGE `request_quantity` `request_quantity` DOUBLE NULL DEFAULT NULL COMMENT 'requested quantity';
ALTER TABLE `item_request_details` CHANGE `approved_quantity` `approved_quantity` DOUBLE NULL DEFAULT NULL COMMENT 'approved quantity';
ALTER TABLE `item_request_details` CHANGE `received_quantity` `received_quantity` DOUBLE NULL DEFAULT NULL COMMENT 'received quantity';

ALTER TABLE `inventory` CHANGE `min_branch_stock` `min_branch_stock` DOUBLE NULL DEFAULT NULL COMMENT 'minimum branch quantity';
ALTER TABLE `inventory` CHANGE `max_branch_stock` `max_branch_stock` DOUBLE NULL DEFAULT NULL COMMENT 'maximum branch quantity';
ALTER TABLE `inventory` CHANGE `price` `price` DOUBLE NULL DEFAULT NULL ;

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Inventory Consumption', 'branchsales/inventory_consumption', 'iconInventoryGroups.png', NULL, '6', NOW(), NOW());
INSERT INTO `mtg`.`user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, '71', '1', NOW(), NOW());


INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Branch', 'branch', 'iconBranch.png', NULL, '0', NOW(), NOW());

CREATE TABLE IF NOT EXISTS `branches_to_analyst` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `analyst_id` int(11) unsigned NOT NULL,
  `branch_id` int(11) unsigned NOT NULL,
  `status` int(4) NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `branches_to_analyst` ADD INDEX ( `analyst_id` ) ;
ALTER TABLE `branches_to_analyst` ADD INDEX ( `branch_id` ) ;

ALTER TABLE `branches_to_analyst` ADD FOREIGN KEY ( `analyst_id` ) REFERENCES `mtg`.`employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `branches_to_analyst` ADD FOREIGN KEY ( `branch_id` ) REFERENCES `mtg`.`master_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;



CREATE TABLE IF NOT EXISTS `analyst_discussion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator_id` int(11) unsigned NOT NULL COMMENT 'foreign key to employee table',
  `participants` text COMMENT 'all employee id in json format which the sales anayst taken for discussion',
  `branch_id` int(11) unsigned NOT NULL COMMENT 'reference to master resources table',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT 'relation to this table itself',
  `subject` text COMMENT 'Discussion subject',
  `message` text COMMENT 'Discussion message',
  `type` enum('MAIL','CALL','CHAT') NOT NULL COMMENT 'type of conversion',
  `status` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `branch_id` (`branch_id`),
  KEY `creator_id` (`creator_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;


ALTER TABLE `analyst_discussion`
  ADD CONSTRAINT `analyst_discussion_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `analyst_discussion_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `master_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


ALTER TABLE `analyst_discussion` ADD `branch_id` INT( 11 ) UNSIGNED NOT NULL COMMENT 'reference to master resources table' AFTER `participants` ,
ADD INDEX ( `branch_id` ) ;
ALTER TABLE `analyst_discussion` ADD INDEX ( `creator_id` ) ;
ALTER TABLE `analyst_discussion` ADD INDEX ( `parent_id` ) ;


ALTER TABLE `analyst_discussion` ADD FOREIGN KEY ( `creator_id` ) REFERENCES `mtg`.`employees` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;
ALTER TABLE `analyst_discussion` ADD FOREIGN KEY ( `branch_id` ) REFERENCES `mtg`.`master_resources` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `analyst_discussion` CHANGE `participants` `participants` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL COMMENT 'all employee id in json format which the sales anayst taken for discussion',
CHANGE `parent_id` `parent_id` INT( 11 ) NOT NULL DEFAULT '0' COMMENT 'relation to this table itself';

UPDATE `mtg`.`modules` SET `name` = 'KPI Discussions',
`url` = 'branchsales/analyst_discussion',
`created_at` = NOW( ) ,
`updated_at` = NOW( ) WHERE `modules`.`id` =22;
UPDATE `mtg`.`modules` SET `name` = 'KPI Analysis' WHERE `modules`.`id` =67;

INSERT INTO `mtg`.`modules` (
`id` ,
`name` ,
`url` ,
`logo` ,
`active_logo` ,
`parent_id` ,
`created_at` ,
`updated_at`
)
VALUES (
NULL , 'KPI Dashboard', 'kpi/dashboard', 'iconInventoryGroups.png', NULL , '67', NOW( ) , NOW( )
);
INSERT INTO `mtg`.`modules` (
`id` ,
`name` ,
`url` ,
`logo` ,
`active_logo` ,
`parent_id` ,
`created_at` ,
`updated_at`
)
VALUES (
NULL , 'KPI Discussions', 'branchsales/analyst_discussion', 'iconAttendees.png', NULL , '6', NOW( ) , NOW( )
);

DELETE FROM `mtg`.`modules` WHERE `modules`.`id` = 71 LIMIT 1;
INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Inventory Consumption', 'kpi/inventory_consumption', 'imgInventory.png', NULL, '67', NOW(), NOW());

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Inventory Items', 'inventory/inventory_items', 'iconInventoryGroups.png', NULL, '70', NOW(), NOW());

UPDATE `mtg`.`modules` SET `name` = 'Warehouses' WHERE `modules`.`id` =68;
INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Received Inventory Requests', 'warehouse/received_inventory_request', 'iconLeave.png', NULL, '68', NOW(), NOW());

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Inventory Request', 'supervisors/inventory_request', 'iconLeave.png', NULL, '69', NOW(), NOW());

UPDATE `mtg`.`modules` SET `name` = 'Branches' WHERE `modules`.`id` =72;
INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Sales Analyst Branches', 'branch/sales_analyst_branches', 'iconLeave.png', NULL, '72', NOW(), NOW());
INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Branch Physical Stock', 'branch/branch_physical_stock', 'iconInventoryGroups.png', NULL, '72', NOW(), NOW());

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'User Permissions', 'hr/userpermissions', 'iconEligibility.png', NULL, '5', NOW(), NOW());

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Sales Target', 'kpi/sales_target', 'iconPos.png', NULL, '67', NOW(), NOW());

CREATE TABLE IF NOT EXISTS `sales_target` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `branch_id` int(11) unsigned NOT NULL,
  `start_date` timestamp NULL DEFAULT NULL,
  `end_date` timestamp NULL DEFAULT NULL,
  `duration_type` varchar(20) DEFAULT NULL COMMENT 'values can be quarterly,yearly,monthly',
  `target_quarter` int(11) DEFAULT NULL COMMENT 'Four quarters in an year values can be  1,2,3,4 ',
  `target_month` int(11) DEFAULT NULL,
  `target_year` int(11) DEFAULT NULL COMMENT 'Selected Year',
  `target_amount` double DEFAULT NULL COMMENT 'Target Amount',
  `status` int(5) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `sales_target` ADD INDEX ( `branch_id` ) ;
ALTER TABLE `sales_target` ADD FOREIGN KEY ( `branch_id` ) REFERENCES `mtg`.`master_resources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE ;

=============================================================================================================================================

INSERT INTO `mtg`.`modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Resourse Listing', 'operation/resource_listing', 'iconResList.png', NULL, (SELECT id  FROM `modules` as t WHERE name = 'Operation'), NOW(), NOW())
UPDATE `mtg`.`modules` SET `active_logo` = 'iconSupervisor.png',`created_at` = NOW( ) ,`updated_at` = NOW( ) WHERE `modules`.`id` =69;
UPDATE `mtg`.`modules` SET `active_logo` = 'iconWireHouse.png',`created_at` = NOW( ) ,`updated_at` = NOW( ) WHERE `modules`.`id` =68;