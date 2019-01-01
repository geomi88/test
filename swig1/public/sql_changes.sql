CREATE TABLE `architects`
( `id` INT
(11) NOT NULL AUTO_INCREMENT , `name_en` VARCHAR
(255) NULL , `name_ka` VARCHAR
(255) NULL , `name_ru` VARCHAR
(255) NULL , `address_en` TEXT NULL , `address_ka` TEXT NULL , `address_ru` TEXT NULL , `phone` VARCHAR
(20) NULL , `email` VARCHAR
(100) NULL , `description_en` TEXT NULL , `description_ka` TEXT NULL , `description_ru` TEXT NULL , `additional_description_en` TEXT NULL , `additional_description_ka` TEXT NULL , `additional_description_ru` TEXT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on
update CURRENT_TIMESTAMP
NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY
(`id`)) ENGINE = InnoDB;

CREATE TABLE `architect_images`
( `id` INT
(11) NOT NULL AUTO_INCREMENT , `architect_id` INT
(11) NOT NULL , `image` VARCHAR
(50) NOT NULL , `main_image` TINYINT
(1) NULL , `status` INT
(2) NOT NULL DEFAULT '1' , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on
update CURRENT_TIMESTAMP
NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY
(`id`)) ENGINE = InnoDB;

ALTER TABLE `architect_images`
ADD INDEX
(`architect_id`);

ALTER TABLE `architect_images`
ADD FOREIGN KEY
(`architect_id`) REFERENCES `architects`
(`id`) ON
DELETE CASCADE ON
UPDATE CASCADE; 



05-12-18

CREATE TABLE `contact_form`
( `id` INT
(11) NOT NULL AUTO_INCREMENT , `first_name` VARCHAR
(255) NOT NULL , `last_name` VARCHAR
(255) NOT NULL , `email` VARCHAR
(100) NOT NULL , `phone` VARCHAR
(20) NOT NULL , `message` TEXT NOT NULL , `stay_informed` INT
(11) NOT NULL COMMENT '0 - not subscribed,1 - subscribed' , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on
update CURRENT_TIMESTAMP
NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY
(`id`)) ENGINE = InnoDB;

06-12-18
CREATE TABLE `architect_projects`
( `id` INT
(11) NOT NULL AUTO_INCREMENT , `architect_id` INT
(11) NOT NULL , `project_id` INT
(11) NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on
update CURRENT_TIMESTAMP
NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY
(`id`)) ENGINE = InnoDB;

ALTER TABLE `architect_projects`
ADD INDEX
(`architect_id`);

ALTER TABLE `architect_projects`
ADD INDEX
(`project_id`);

ALTER TABLE `architect_projects`
ADD FOREIGN KEY
(`architect_id`) REFERENCES `architects`
(`id`) ON
DELETE CASCADE ON
UPDATE CASCADE;
ALTER TABLE `architect_projects`
ADD FOREIGN KEY
(`project_id`) REFERENCES `projects`
(`id`) ON
DELETE CASCADE ON
UPDATE CASCADE;

CREATE TABLE `stay_informed_form`
( `id` INT
(11) NOT NULL AUTO_INCREMENT , `first_name` VARCHAR
(255) NOT NULL , `last_name` VARCHAR
(255) NOT NULL , `street_number` VARCHAR
(900) NOT NULL , `city` VARCHAR
(50) NOT NULL , `zip` VARCHAR
(20) NOT NULL , `district` VARCHAR
(50) NOT NULL , `email` VARCHAR
(100) NOT NULL , `phone` VARCHAR
(20) NOT NULL , `tenure_type` INT
(11) NOT NULL COMMENT '1 - for sale,2- for rent ' , `min_price` VARCHAR
(20) NOT NULL , `max_price` VARCHAR
(20) NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on
update CURRENT_TIMESTAMP
NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY
(`id`)) ENGINE = InnoDB;

CREATE TABLE `stay_informed_building_type`
( `id` INT
(11) NOT NULL AUTO_INCREMENT , `stay_informed_id` INT
(11) NOT NULL , `building_type_id` INT
(11) NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on
update CURRENT_TIMESTAMP
NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY
(`id`)) ENGINE = InnoDB;

ALTER TABLE `stay_informed_building_type`
ADD INDEX
(`stay_informed_id`);

ALTER TABLE `stay_informed_building_type`
ADD INDEX
(`building_type_id`);

ALTER TABLE `stay_informed_building_type`
ADD FOREIGN KEY
(`building_type_id`) REFERENCES `building_type`
(`id`) ON
DELETE CASCADE ON
UPDATE CASCADE;
ALTER TABLE `stay_informed_building_type`
ADD FOREIGN KEY
(`stay_informed_id`) REFERENCES `stay_informed_form`
(`id`) ON
DELETE CASCADE ON
UPDATE CASCADE;

CREATE TABLE `stay_informed_muncipality`
( `id` INT
(11) NOT NULL AUTO_INCREMENT , `stay_informed_id` INT
(11) NOT NULL , `muncipality_id` INT
(11) NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on
update CURRENT_TIMESTAMP
NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY
(`id`)) ENGINE = InnoDB;

ALTER TABLE `stay_informed_muncipality`
ADD INDEX
(`stay_informed_id`);

ALTER TABLE `stay_informed_muncipality`
ADD INDEX
(`muncipality_id`);

ALTER TABLE `stay_informed_muncipality`
ADD FOREIGN KEY
(`muncipality_id`) REFERENCES `municipalities`
(`id`) ON
DELETE CASCADE ON
UPDATE CASCADE;
ALTER TABLE `stay_informed_muncipality`
ADD FOREIGN KEY
(`stay_informed_id`) REFERENCES `stay_informed_form`
(`id`) ON
DELETE CASCADE ON
UPDATE CASCADE;

ALTER TABLE `stay_informed_form`
ADD `message` TEXT NOT NULL AFTER `phone`;

ALTER TABLE `stay_informed_form`
ADD `stay_informed` INT
(11) NOT NULL COMMENT '0 - not subscribed,1 - subscribed ' AFTER `message`;

CREATE TABLE `estimate_form`
( `id` INT
(11) NOT NULL AUTO_INCREMENT , `first_name` VARCHAR
(255) NULL , `last_name` VARCHAR
(255) NULL , `street_number` VARCHAR
(900) NULL , `city` VARCHAR
(50) NULL , `zip` VARCHAR
(20) NULL , `district` VARCHAR
(50) NULL , `email` VARCHAR
(100) NULL , `tele_phone` VARCHAR
(20) NULL , `mobile_phone` VARCHAR
(20) NULL , `message` TEXT NULL , `stay_informed` INT
(11) NOT NULL COMMENT '0 - not subscribed,1 - subscribed ' , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on
update CURRENT_TIMESTAMP
NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY
(`id`)) ENGINE = InnoDB; 



11-12-18

ALTER TABLE `estimate_form` ADD `status` INT(11) NOT NULL COMMENT '0 -inactive, 1- active' AFTER `stay_informed`; 

ALTER TABLE `estimate_form` CHANGE `status` `status` INT(11) NOT NULL DEFAULT '1' COMMENT '0 -inactive, 1- active'; 

CREATE TABLE `website_contents` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `slug` VARCHAR(50) NOT NULL , `content` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB; 

ALTER TABLE `website_contents` ADD `title` VARCHAR(255) NOT NULL AFTER `slug`; 