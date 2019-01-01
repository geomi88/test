
UPDATE `modules` SET `name` = 'Local Purchase Orders' WHERE `modules`.`name` = 'Purchase Orders';

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Import Purchase Orders', 'requisitions/import_purchase_orders', 'IconPurchaseOrder.jpg', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

-------------------- Po Action (local,Import) -- (27-11-2018)    ---- Branch files -----------------------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Import PO Action', 'requisitions/po_action_import', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Import PO Action' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35');

update modules set name = 'Local PO Action' where url = 'requisitions/purchase_order_list';

------------------------- PO Action List (28-11-2018)------------------------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'PO Action List', 'requisitions/po_action_list', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'PO Action List' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Requisitions' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35');


------------- Pending PO -----------------------

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Pending PO', 'purchase/pending_po', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Pending PO' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35');

----------------------inventory new fields--------------------------
ALTER TABLE `inventory` ADD `track_manufacturing` TINYINT NOT NULL DEFAULT '1' COMMENT '1-track/2-donot track' AFTER `supplier_icode`, ADD `track_expiry` TINYINT NOT NULL DEFAULT '1' COMMENT '1-track/2-donot track' AFTER `track_manufacturing`; 

--------------------- ac_purchase_order new fields for stock-----------------
ALTER TABLE `ac_purchase_order` ADD `send_warehouse_status` TINYINT NOT NULL DEFAULT '2' COMMENT '1-send to warehouse/2-Not send' AFTER `mailed_status`; 
ALTER TABLE `ac_purchase_order` ADD `stock_entered` TINYINT NOT NULL DEFAULT '0' COMMENT '1-stock entered/0-stock not entered' AFTER `order_status`; 
ALTER TABLE `ac_purchase_order` ADD `po_close_status` TINYINT NOT NULL DEFAULT '0' COMMENT '1-closed/0-not closed' AFTER `status`;

----------------------------GRN module--------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Goods Receipt Notes(GRN)', 'purchase/grn', 'iconGoodsReceipt.jpg', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

-------------------------- Purchase - Update PO status List -------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'PO status update List', 'purchase/update_po_status', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'PO status update List' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35');

--------------------  VIew PO status ------------------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'View PO Status', 'purchase/view_postatus', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'View PO Status' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35');

----------------------------Opening Stock module--------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Opening Stock', 'purchase/opening_stock/add', 'iconGoodsReceipt.jpg', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

----------------------- Received GRN ---------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Received GRN', 'purchase/received_grn', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

----------------------------Physical Stock module--------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Physical Stock', 'purchase/physical_stock/add', 'iconGoodsReceipt.jpg', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Purchase' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);

----------------------------Table st_batch_master--------------------------
CREATE TABLE `st_batch_master` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `batch_code` varchar(200) NOT NULL,
 `batch_type` tinyint(4) NOT NULL COMMENT '1-GRN/2-OPS/3-PHS',
 `created_by` int(11) NOT NULL,
 `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-active/2-not active',
 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

----------------------------Table st_po_actions--------------------------
CREATE TABLE `st_po_actions` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `purchase_order_id` int(11) NOT NULL,
 `actions` smallint(6) NOT NULL COMMENT '1-send to supplier/2-send to warehouse/3-cancelled',
 `updated_by` int(11) NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

----------------------------Table st_po_order_status--------------------------
CREATE TABLE `st_po_order_status` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `purchase_order_id` int(11) NOT NULL,
 `po_status` text NOT NULL,
 `updated_by` int(11) NOT NULL,
 `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-active/2-deleted',
 `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

----------------------------Table st_stock_history--------------------------
CREATE TABLE `st_stock_history` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `purchase_order_id` int(11) DEFAULT NULL,
 `batch_id` int(11) NOT NULL,
 `batch_code` varchar(200) NOT NULL,
 `stock_area` tinyint(4) NOT NULL COMMENT '1-warehouse/2-branch',
 `stock_area_id` int(11) NOT NULL,
 `company_id` int(11) DEFAULT NULL,
 `stock_source` tinyint(4) DEFAULT NULL COMMENT '1-GRN/2-OPS/3-PHS',
 `item_id` int(11) NOT NULL,
 `purchase_quantity` float NOT NULL DEFAULT '0',
 `purchase_unit` int(11) NOT NULL,
 `is_primary_unit` tinyint(4) NOT NULL COMMENT '1-primary/0-alternate',
 `quantity_in_primary_unit` double NOT NULL DEFAULT '0',
 `stock_remaining` double DEFAULT NULL,
 `mfg_date` date DEFAULT NULL,
 `exp_date` date DEFAULT NULL,
 `unit_price` double NOT NULL DEFAULT '0',
 `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-active/2-old stock',
 `updated_by` int(11) NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

----------------------------Table st_stock_info--------------------------
CREATE TABLE `st_stock_info` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `purchase_order_id` int(11) DEFAULT NULL,
 `batch_id` int(11) NOT NULL,
 `batch_code` varchar(200) NOT NULL,
 `stock_area` tinyint(4) NOT NULL COMMENT '1-warehouse/2-branch',
 `stock_area_id` int(11) NOT NULL,
 `company_id` int(11) DEFAULT NULL,
 `stock_source` tinyint(4) DEFAULT NULL COMMENT '1-GRN/2-OPS/3-PHS',
 `item_id` int(11) NOT NULL,
 `purchase_quantity` float NOT NULL DEFAULT '0',
 `purchase_unit` int(11) NOT NULL,
 `is_primary_unit` tinyint(4) NOT NULL COMMENT '1-primary/0-alternate',
 `quantity_in_primary_unit` double NOT NULL DEFAULT '0',
 `stock_remaining` double DEFAULT NULL,
 `mfg_date` date DEFAULT NULL,
 `exp_date` date DEFAULT NULL,
 `unit_price` double NOT NULL DEFAULT '0',
 `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1-active/2-old stock',
 `updated_by` int(11) NOT NULL,
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

----------------------Location wise stock report------------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Location Wise Stock Report', 'inventory/locationwisestockreport', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Inventory' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Location Wise Stock Report' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Inventory' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35');

------------------ Item wise Stock report ----------------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Item Wise Stock Report', 'inventory/itemwisestockreport', 'iconCutofDate.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Inventory' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Item Wise Stock Report' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'Inventory' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-08 11:10:35', '2018-11-08 11:10:35');

------------------------inventory clearance----------------------------
------------------------check if any duplicate row existe in live-----------------------
SELECT inventory_item_id,unit_id,count(*) as unitcount FROM inventory_alternate_units GROUP BY inventory_item_id,unit_id HAVING count(*) > 1
SELECT * FROM `inventory_alternate_units` WHERE `conversion_value` = 0;-----check this also-----
SELECT * FROM `inventory_alternate_units` WHERE `conversion_value` IS null;-----check this also-----

-----------------update null,0 conversion value to 1------------------------------------
UPDATE `inventory_alternate_units` SET `conversion_value`=1 WHERE conversion_value IS null;
UPDATE `inventory_alternate_units` SET `conversion_value`=1 WHERE conversion_value=0;

----------------set conversion value to 1 by default and remove null----------------------
ALTER TABLE `inventory_alternate_units` CHANGE `conversion_value` `conversion_value` FLOAT NOT NULL DEFAULT '1';