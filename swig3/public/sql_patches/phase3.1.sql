UPDATE `modules` SET `name` = 'To Do' WHERE `modules`.`name` = 'To do';
UPDATE `modules` SET `logo` = 'iconPlan.png' WHERE `modules`.`name` = 'View Plan';

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`)
VALUES (NULL, 'View To Do', 'dashboard/view_todo', 'iconViewTodo.png', 'lightSkyBlue/darkSkyBlue', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL);

ALTER TABLE `tasks` ADD `task_type` SMALLINT NOT NULL DEFAULT '0' COMMENT '0-todo,1-plan,2-assigned' AFTER `priority`; 
ALTER TABLE `tasks` CHANGE `assigned_to` `assigned_by` INT(11) NULL DEFAULT NULL;

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`)
VALUES (NULL, 'Assign Task', 'dashboard/assign_task', 'imgViewAssignList.png', 'lightGrey/DarkGrey', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`)
VALUES (NULL, 'Assign Task List', 'dashboard/task_list', 'iconTaskList.png', 'lightPeach/DarkPeach', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`)
VALUES (NULL, 'Track Task', 'dashboard/track_task', 'iconTrack.png', 'lightGreen/DarkGreen', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`)
VALUES (NULL, 'Sales By Supervisor', 'branchsales/supervisor_wise_sales', 'iconSupervisorgraph.png', '', (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' limit 1), '1', NULL, NULL);

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`)
VALUES (NULL, 'Sales By Branch', 'branchsales/branch_wise_sales', 'iconBranchSalegraph.png', '', (SELECT `id` FROM `modules` as m WHERE m.name = 'Branch Sales' limit 1), '1', NULL, NULL);

UPDATE `modules` SET `active_logo`='lightGreen2/DarkGreen' where `name`='Track Task';
