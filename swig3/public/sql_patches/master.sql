------Nov 29---------New field (is_closed) in table crm_feedbacks----------------
ALTER TABLE `crm_feedbacks` ADD `is_closed` INT NOT NULL DEFAULT '1' COMMENT '1-pending/2-closed' AFTER `created_by`;

---------------New table crm_feedback_follwup----------------
CREATE TABLE `crm_feedback_follwup` ( `id` INT NOT NULL AUTO_INCREMENT ,
 `crm_feedback_id` INT NOT NULL COMMENT 'references crm_feedbacks' ,
 `followup` TEXT NOT NULL , `created_by` INT NOT NULL ,
 `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
 `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
 PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;

---------------New field (closed_by) in table crm_feedbacks----------------
ALTER TABLE `crm_feedbacks` ADD `closed_by` INT NOT NULL COMMENT 'Reference to Employee table' AFTER `is_closed`;
ALTER TABLE `crm_feedbacks` CHANGE `closed_by` `closed_by` INT(11) NOT NULL DEFAULT '0' COMMENT 'Reference to Employee table';

------Nov 30---------New module (CRM Followups) under CRM----------------
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `menu_order`, `class_name`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'CRM Followups', 'crm/crm_followups', 'iconListEmployees.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1), NULL, NULL, '1', NULL, NULL);
INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `filter_by_job_position`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM Followups' and m.parent_id = (SELECT `id` FROM `modules` as m WHERE m.name = 'CRM' and m.parent_id = 0 limit 1) limit 1), '1', NULL, '2018-11-30 11:10:35', '2018-11-30 11:10:35');


-------------------------pos rework----------------------
ALTER TABLE `pos_sales` ADD `total_sale_old` DOUBLE NULL DEFAULT '0' AFTER `total_sale`;
ALTER TABLE `pos_sales` ADD `tax_in_mis_old` DOUBLE NULL DEFAULT '0' AFTER `tax_in_mis`;
ALTER TABLE `pos_sales` ADD `difference_old` VARCHAR(250) NULL AFTER `difference`;

UPDATE pos_sales SET total_sale_old=total_sale;
UPDATE pos_sales SET tax_in_mis_old=tax_in_mis;
UPDATE pos_sales SET difference_old=difference;

ALTER TABLE `pos_sales` CHANGE `credit_sale` `credit_sale` DOUBLE NULL DEFAULT '0';
ALTER TABLE `pos_sales` CHANGE `bank_sale` `bank_sale` DOUBLE NULL DEFAULT '0';

SELECT id,cash_sale,bank_sale,credit_sale,(cash_sale+bank_sale+credit_sale) as total,total_sale FROM `pos_sales` WHERE added_by_user_type = 'Supervisor' AND cash_sale!='' AND cash_sale IS NOT NULL AND (cash_sale+bank_sale+credit_sale)!=total_sale;

--------------update live data----------------------------
UPDATE pos_sales set total_sale = (cash_sale+bank_sale+credit_sale) WHERE added_by_user_type = 'Supervisor' AND cash_sale!='' AND cash_sale IS NOT NULL AND (cash_sale+bank_sale+credit_sale) != total_sale;


UPDATE pos_sales SET tax_in_mis=(total_sale-(total_sale/1.05)) WHERE total_sale!=0 AND total_sale!='' AND date(created_at) > '2018-01-27' AND added_by_user_type='Supervisor';

------------------data correction bcause sale is already 0, so tax also must be 0-------
SELECT * FROM `pos_sales` WHERE `pos_date` BETWEEN '2018-05-01 00:00:00.000000' AND '2018-05-31 00:00:00.000000' and added_by_user_type='Supervisor' and total_sale='' AND tax_in_mis!=0 

UPDATE `pos_sales` SET `tax_in_mis` = '0' WHERE `pos_sales`.`id` = 71328;
UPDATE `pos_sales` SET `tax_in_mis` = '0' WHERE `pos_sales`.`id` = 71330;


UPDATE pos_sales SET difference=((COALESCE(cash_collection,0)-COALESCE(cash_sale,0))+(COALESCE(bank_collection,0)-COALESCE(bank_sale,0))) WHERE date(created_at) > '2018-01-27' AND added_by_user_type='Supervisor';

------Dec 12---------New field (CRM top_cashier_file) in cash_collection tbl----------------
ALTER TABLE `cash_collection` ADD `top_cashier_file` TEXT NULL AFTER `ref_no`;
ALTER TABLE `cash_collection` ADD `comment` TEXT NOT NULL AFTER `all_collected_status`;

------Dec 20---------Comment change in crm_feedbacks----------------
ALTER TABLE `crm_feedbacks` CHANGE `is_closed` `is_closed` INT(11) NOT NULL DEFAULT '1' COMMENT '1-pending/2-closed/3-following';