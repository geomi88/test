
ALTER TABLE `ac_accounts` CHANGE `type` `type` ENUM('Supplier','Customer','Employee','Asset','General Ledger') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

----- create ac_general_ledgers table---

CREATE TABLE `ac_general_ledgers` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `company_id` int(11) NOT NULL,
  `alias_name` varchar(200) DEFAULT NULL,
  `ledger_group_id` int(11) NOT NULL,
  `type` enum('Income','Expense') NOT NULL,
  `opening_balance` double NOT NULL DEFAULT '0',
  `ac_nature` enum('DR','CR') NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '0-disabled/1-active/2-deleted',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_general_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ledger_group_id` (`ledger_group_id`);

ALTER TABLE `ac_general_ledgers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `ac_general_ledgers`
  ADD CONSTRAINT `ac_general_ledgers_ibfk_1` FOREIGN KEY (`ledger_group_id`) REFERENCES `ac_ledger_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

----- create sub module general ledger under ledgers----

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'General Ledgers', 'ledgers/general_ledgers', 'iconCompany.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);


------ Inventory table change add is_consummable field------

ALTER TABLE `inventory` ADD `is_consumable` TINYINT NOT NULL DEFAULT '0' COMMENT '1-Consummable/0-Not consummable' AFTER `warehouse_id`; 