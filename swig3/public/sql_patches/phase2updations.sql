ALTER TABLE `employees` CHANGE `privilege_status` `privilege_status` INT(11) NOT NULL DEFAULT '0' COMMENT 'firld value can either one or 0, when 1 means the employee have the privilage to view all the information regrdless of the role we assigned or branches we assigned. ';
ALTER TABLE `master_resources` CHANGE `amount` `amount` DOUBLE NULL DEFAULT NULL COMMENT 'used in resource type = ledger';

20/09/17
ALTER TABLE `analyst_discussion` CHANGE `subject` `subject` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Discussion subject', CHANGE `message` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Discussion message';

22/09/2017

ALTER TABLE `pos_sales` ADD `collection_status` SMALLINT NOT NULL DEFAULT '0' COMMENT 'Set to 1 When Top cahier collects amount' AFTER `cash_collection_final_status`;
ALTER TABLE `cash_collection` ADD `all_collected_status` SMALLINT NOT NULL DEFAULT '0' COMMENT 'set to 1 when all pos sale is collected' AFTER `verified_status` 