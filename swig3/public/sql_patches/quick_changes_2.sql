INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Sales Dashboard', 'supervisors/sales_dashboard', 'iconInventoryGroups.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Supervisors' limit 1), '1', '2017-06-09 18:31:48', '2017-06-09 18:31:48');
ALTER TABLE `master_resources` ADD `bottom_sale_line` VARCHAR(200) NULL COMMENT 'Related To branch sales' AFTER `amount`; 

-----------ledgercode--------------
ALTER TABLE `master_resources` ADD `ledger_code` VARCHAR(255) NULL DEFAULT NULL COMMENT 'added for saving the ledger code' AFTER `branch_code`;
