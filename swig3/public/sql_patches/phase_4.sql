ALTER TABLE `tasks` CHANGE `task_type` `task_type` SMALLINT(6) NOT NULL DEFAULT '0' COMMENT '0-todo,1-plan,2-assigned,3-meeting'; 

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Meetin Rooms', 'masterresources/meeting_room', 'iconCreateMeeting.png', 'iconCreateMeeting.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'Master Resources' limit 1), '1', NULL, NULL);

ALTER TABLE `master_resources` CHANGE `resource_type` `resource_type` ENUM('BRANCH','REGION','PLANNING','DEPARTMENT','RELIGION','GENDER','JOB_POSITION','INVENTORY_GROUP','INVENTORY_CATEGORY','INVENTORY_SUB_CATEGORY','WAREHOUSE','SPOT','JOB_SHIFT','POS_REASON','BLOOD_GROUP','AREA','BANK','DIVISION','LEDGER','CHECK_LIST_CATEGORY','WARNING_TYPE','MEETING_ROOM') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'mention what type of resource type is this';

CREATE TABLE `mtg`.`meeting_attendees` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `meeting_id` INT(11) NOT NULL , `user_id` INT NOT NULL , `availability_status` INT NOT NULL DEFAULT '1' COMMENT '1-available,2-not available' , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `tasks` ADD `meeting_room` INT(11) NOT NULL COMMENT 'relation to master_resources table' AFTER `priority`;

ALTER TABLE `tasks` ADD `parent_meeting_id` INT(11) NOT NULL AFTER `task_type`; 

ALTER TABLE `tasks` CHANGE `meeting_room` `meeting_room` INT(11) NULL COMMENT 'relation to master_resources table'; 
ALTER TABLE `tasks` CHANGE `parent_meeting_id` `parent_meeting_id` INT(11) NULL; 

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Create Meeting', 'meeting/createmeeting', 'iconCreateMeeting.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Meeting' limit 1), '1', '2017-12-09 18:31:48', '2017-12-09 18:31:48');

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Meetings List', 'meeting/meeting_list', 'iconCreateMeeting.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Meeting' limit 1), '1', '2017-12-09 18:31:48', '2017-12-09 18:31:48');

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Meetings List' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), '2017-12-09 18:31:48', '2017-12-09 18:31:48');

INSERT INTO `user_modules` (`id`, `module_id`, `employee_id`, `created_at`, `updated_at`) VALUES (NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Create Meeting' limit 1), (SELECT `id` FROM `employees` as e WHERE e.admin_status = 1 limit 1), '2017-12-09 18:31:48', '2017-12-09 18:31:48');

/*08-01-18*/
ALTER TABLE `meeting_attendees` ADD INDEX(`meeting_id`); 

ALTER TABLE `meeting_attendees` ADD INDEX(`user_id`); 

ALTER TABLE `meeting_attendees` ADD FOREIGN KEY (`meeting_id`) REFERENCES `tasks`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 

ALTER TABLE `meeting_attendees` CHANGE `user_id` `user_id` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `meeting_attendees` CHANGE `id` `id` INT(10) NOT NULL AUTO_INCREMENT;  

ALTER TABLE `meeting_attendees` ADD FOREIGN KEY (`user_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; 

ALTER TABLE `meeting_attendees` CHANGE `user_id` `user_id` INT(11) UNSIGNED NULL; 

ALTER TABLE `meeting_attendees` ADD `guest_name` VARCHAR(200) NULL AFTER `user_id`, ADD `guest_email` VARCHAR(200) NULL AFTER `guest_name`, ADD `guest_phone` VARCHAR(200) NULL AFTER `guest_email`;

ALTER TABLE `meeting_attendees` ADD `is_organizer` INT(11) NOT NULL DEFAULT '0' AFTER `availability_status`; 

/*** 12-01-18****/
ALTER TABLE `meeting_attendees` ADD `comment` TEXT NULL AFTER `availability_status`; 