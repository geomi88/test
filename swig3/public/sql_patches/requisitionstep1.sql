-- create sub menu accounts group
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Account Group', 'masterresources/ledger_group', 'iconLedger.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Master Resources' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Account Group' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), NULL, NULL);

CREATE TABLE `ac_ledger_group` (
  `id` int(11) NOT NULL,
  `group_type` tinyint(6) NOT NULL COMMENT '0-primary/1-main group/2-sub group',
  `name` varchar(250) CHARACTER SET utf8 NOT NULL,
  `alias_name` varchar(250) CHARACTER SET utf8 DEFAULT NULL,
  `parent_id` int(11) NOT NULL,
  `group_nature` tinyint(2) NOT NULL COMMENT '0-Credit/1-Debit',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '0-Deleted/1-Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `ac_ledger_group`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ac_ledger_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;COMMIT;

-- insert primary groups
INSERT INTO `ac_ledger_group` (`id`, `group_type`, `name`, `alias_name`, `parent_id`, `group_nature`, `status`, `created_at`, `updated_at`) VALUES
(1, 0, 'Income', 'Income', 0, 0, 1, NULL, NULL),
(2, 0, 'Expenses', 'Expenses', 0, 1, 1, NULL, NULL),
(3, 0, 'Asset', 'Asset', 0, 1, 1, NULL, NULL),
(4, 0, 'Liability', 'Liability', 0, 0, 1, NULL, NULL);

-- create main menu ledgers
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Ledgers', 'ledgers', 'iconRequisition.png', 'iconRequisition.png', '0', '110', NULL, '1', NULL, NULL);

-- create sub menu under ledger
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'List Suppliers', 'suppliers', 'iconJobposition.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Suppliers', 'suppliers/add', 'iconJobposition.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Edit Suppliers', 'suppliers/editlist', 'iconJobposition.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);

-- To keep supplier documents add new document types
ALTER TABLE `documents` CHANGE `document_type` `document_type` ENUM('PLANNING','KPI','RECIPE','CHART','ID_CARD','JOB_DESCRIPTION','CONTRACT','START_FROM','PERSONAL_PROFILE','QUALIFICATION','CV','JOB_OFFER','PASSPORT','VAT_CERTIFICATE','COMPANY_PROFILE','VENDOR_CONTRACT') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

-- create accounts table contains all ledgers with basic data
CREATE TABLE `ac_accounts` (
  `id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `ledger_group_id` int(11) DEFAULT NULL,
  `code` varchar(200) NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) DEFAULT NULL,
  `alias_name` varchar(200) DEFAULT NULL,
  `type` enum('Supplier','Customer','Employee') NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '0-disables/1-active/2-deleted',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_accounts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ac_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;COMMIT;

-- Insert customer ledgers to ac_accounts table
INSERT INTO ac_accounts (party_id,code,first_name,last_name,alias_name,type,status) SELECT id,username,first_name,last_name,alias_name,'Employee' as type,status from employees where status!=2;

-- create party table
CREATE TABLE `ac_party` (
  `id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(200) NOT NULL,
  `last_name` varchar(200) DEFAULT NULL,
  `alias_name` varchar(200) DEFAULT NULL,
  `ledger_group_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `supplier_type` tinyint(6) NOT NULL COMMENT '0-local /1-international',
  `party_type` enum('Supplier','Customer') NOT NULL,
  `registration_type` tinyint(6) NOT NULL COMMENT '0=not registered 1= registered',
  `address` text,
  `supplier_pin` varchar(50) DEFAULT NULL COMMENT 'Will contain value only when supplier registered',
  `contact_person` varchar(200) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `nationality` int(11) UNSIGNED NOT NULL COMMENT 'Foreign key to country table',
  `bank_beneficiary_name` varchar(200) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bank_iban_no` varchar(50) DEFAULT NULL,
  `bank_country` int(11) UNSIGNED DEFAULT NULL COMMENT 'Foreign key to country table',
  `bank_branch_name` varchar(150) DEFAULT NULL,
  `bank_swift_code` varchar(100) DEFAULT NULL,
  `additional_info` text,
  `business_nature` text,
  `credit_date` datetime DEFAULT NULL,
  `credit_limit` double DEFAULT NULL,
  `credit_days` int(11) DEFAULT NULL,
  `preferred_product` text,
  `location_details` varchar(200) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '0-disabled/1-active/2-deleted',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_party`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nationality` (`nationality`),
  ADD KEY `bank_country` (`bank_country`);

ALTER TABLE `ac_party`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `ac_party`
  ADD CONSTRAINT `ac_party_ibfk_1` FOREIGN KEY (`nationality`) REFERENCES `country` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ac_party_ibfk_2` FOREIGN KEY (`bank_country`) REFERENCES `country` (`id`);COMMIT;

-- create sub menu under ledger for customers
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'List Customers', 'customers', 'iconJobposition.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Customer', 'customers/add', 'iconJobposition.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Edit Customers', 'customers/editlist', 'iconJobposition.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);


------------- Assets 13-02-2017------------

ALTER TABLE `documents` CHANGE `document_type` `document_type` ENUM('PLANNING','KPI','RECIPE','CHART','ID_CARD','JOB_DESCRIPTION','CONTRACT','START_FROM','PERSONAL_PROFILE','QUALIFICATION','CV','JOB_OFFER','PASSPORT','VAT_CERTIFICATE','COMPANY_PROFILE','VENDOR_CONTRACT','ASSET_IMAGE_1','ASSET_IMAGE_2','ASSET_IMAGE_3','ASSET_DOC_1','ASSET_DOC_2','ASSET_DOC_3') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `ac_accounts` CHANGE `type` `type` ENUM('Supplier','Customer','Employee','Asset') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `ac_accounts` ADD `asset_id` INT(11) NULL COMMENT 'Reference to Asset table' AFTER `party_id`;

ALTER TABLE `ac_accounts` CHANGE `party_id` `party_id` INT(11) NULL COMMENT 'Reference to ac_party table Null if type is asset';

ALTER TABLE `ac_accounts` ADD INDEX(`ledger_group_id`);
ALTER TABLE `ac_accounts` ADD FOREIGN KEY (`ledger_group_id`) REFERENCES `ac_ledger_group`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ac_party` ADD INDEX(`ledger_group_id`);
ALTER TABLE `ac_party` DROP FOREIGN KEY `ac_party_ibfk_2`; ALTER TABLE `ac_party` ADD CONSTRAINT `ac_party_ibfk_2` FOREIGN KEY (`bank_country`) REFERENCES `country`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `ac_party` ADD FOREIGN KEY (`ledger_group_id`) REFERENCES `ac_ledger_group`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ac_party` CHANGE `preferred_product` `preferred_product` INT(11) NULL DEFAULT NULL COMMENT 'inventory id';

-- create sub menu under ledger for asset
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'List Assets', 'assets', 'iconJobposition.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Add Asset', 'assets/add', 'iconJobposition.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Edit Assets', 'assets/editlist', 'iconJobposition.png ', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Ledgers' limit 1), NULL, '1', NULL, NULL);

-- create table ac_assets
CREATE TABLE `ac_assets` (
  `id` int(11) NOT NULL,
  `code` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `alias_name` varchar(200) DEFAULT NULL,
  `company_id` int(11) NOT NULL,
  `barcode_id` int(11) NOT NULL,
  `ledger_group_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `purchased_emp_id` int(11) NOT NULL,
  `expiry_year_count` int(11) NOT NULL,
  `expiry_month_count` int(11) NOT NULL,
  `purchase_date` datetime NOT NULL,
  `purchase_value` double DEFAULT NULL,
  `asset_value` double DEFAULT NULL,
  `depreciation` double DEFAULT NULL,
  `used_by` smallint(6) NOT NULL COMMENT '1-employee/2-branch',
  `used_employee` int(11) DEFAULT NULL,
  `used_branch` int(11) DEFAULT NULL,
  `description` text,
  `warrenty` int(11) DEFAULT NULL COMMENT 'Number of years',
  `status` smallint(6) NOT NULL DEFAULT '1' COMMENT '0-disabled/1-active/2-deleted',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ac_assets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ledger_group_id` (`ledger_group_id`);

ALTER TABLE `ac_assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

ALTER TABLE `ac_assets`
  ADD CONSTRAINT `ac_assets_ibfk_1` FOREIGN KEY (`ledger_group_id`) REFERENCES `ac_ledger_group` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;