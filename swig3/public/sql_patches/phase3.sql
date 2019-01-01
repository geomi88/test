--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_all_day_task` tinyint(1) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `owner_id` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `priority` int(11) NOT NULL,
  `status` smallint(6) NOT NULL COMMENT '0-deleted,1-New,2-Pending,3-Completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;COMMIT;


CREATE TABLE `task_history` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `status` smallint(3) NOT NULL COMMENT '0-deleted,1-New,2-Pending,3-Completed',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `task_history`
--
ALTER TABLE `task_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `task_history`
--
ALTER TABLE `task_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `task_history`
--
ALTER TABLE `task_history`
  ADD CONSTRAINT `task_history_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;
-- changes in POS entry --

ALTER TABLE `pos_sales` ADD `pos_date` DATETIME NOT NULL AFTER `job_shift_id`;
UPDATE `pos_sales` SET `pos_date` = `created_at`;

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `created_at`, `updated_at`) VALUES (NULL, 'Calendar', 'dashboard', 'iconDashboard.png', 'iconThingActive.png', '0', NULL, NULL); 
ALTER TABLE `modules` ADD `is_removable` SMALLINT NOT NULL DEFAULT '1' COMMENT '0-not removable/1-removable' AFTER `parent_id`; 

UPDATE `modules` SET `is_removable` = '0' WHERE `modules`.`name` = 'Calendar'; 

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Sales', 'dashboard/managementconsole', 'iconSales.png', 'lightGreen/darkGreen', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL); 
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'To do', 'dashboard/todo', 'iconTodo.png', 'lightPink/darkPink', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL); 
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Plan', 'dashboard/plan', 'iconCreatePlan.png', 'lightPurple/darkPurple', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL); 
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Create Plan', 'dashboard/plan/createplan', 'iconCreatePlan.png', 'lightYellow/darkYellow', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL); 
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'History', 'tasks/history', 'iconHistory.png', 'lightViolet/DarkViolet', (SELECT `id` FROM `modules` as m WHERE m.name = 'Calendar' limit 1), '1', NULL, NULL);


INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'POS Cashier Edit', 'operation/pos_cashier_edit', 'iconCashierCollection.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Operation' limit 1), '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'POS Supervisor Edit', 'operation/pos_supervisor_edit', 'iconCashierCollection.png', NULL, (SELECT `id` FROM `modules` as m WHERE m.name = 'Operation' limit 1), '1', '2017-11-01 00:00:00', '2017-11-01 00:00:00');
ALTER TABLE `pos_sales` ADD `status` INT(10) NOT NULL DEFAULT '1' COMMENT '1=non edited sales,0=edited sales' AFTER `pos_date`;
ALTER TABLE `pos_sales` ADD `parent_id` VARCHAR(10) NULL DEFAULT NULL COMMENT 'to store the row id if an edit occur' AFTER `pos_date`;
ALTER TABLE `pos_sales` ADD `edited_by` VARCHAR(10) NULL DEFAULT NULL AFTER `parent_id`;

-- added for tips_collection --

ALTER TABLE `pos_sales` ADD `tips_collected` VARCHAR(10) NULL DEFAULT NULL COMMENT 'for storing the tips collected' AFTER `cash_collection`;


--added opening_fund_editable field on master_resource table --

ALTER TABLE `master_resources` ADD `opening_fund_editable` SMALLINT(6) NOT NULL COMMENT '0-editable,1-not editable' AFTER `opening_fund`;


--added meal consumption field on pos_sales---

ALTER TABLE `pos_sales` ADD `meal_consumption` VARCHAR(10) NULL DEFAULT NULL COMMENT 'added for saving meal consumption on supervisor collection' AFTER `tips_collected`;

--altered table meal_consumption for dropdown issue---
ALTER TABLE `pos_sales` CHANGE `meal_consumption` `meal_consumption` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '0' COMMENT 'added for saving meal consumption on supervisor collection';
 
--change name olan to view plan 
UPDATE `modules` SET `name` = 'View Plan' WHERE `modules`.`name` = 'Plan';

---updated meal_consumption on pos_sales for null to 0

UPDATE pos_sales SET `meal_consumption`=0 where `meal_consumption` is NULL

INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Employee By Nationality', 'hr/countrywise', 'iconEmployNationality.png', 'iconEmployNationality.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'HR' limit 1), '1', NULL, NULL);
INSERT INTO `modules` (`id`, `name`, `url`, `logo`, `active_logo`, `parent_id`, `is_removable`, `created_at`, `updated_at`) VALUES (NULL, 'Employee By Job Position', 'hr/job_positionwise', 'iconEmployJobPosition.png', 'iconEmployJobPosition.png', (SELECT `id` FROM `modules` as m WHERE m.name = 'HR' limit 1), '1', NULL, NULL);


----changed icon kpi discussion ---------

UPDATE `modules` SET `logo` = 'iconKPA.png'  WHERE `modules`.`name` = 'KPI Discussions';
