CREATE TABLE `amazoni`.`amazoni4_provider_order_errors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `provider_order_id` INT NULL DEFAULT NULL,
  `product_id` INT NULL DEFAULT NULL,
  `product_sku` VARCHAR(64) NULL DEFAULT NULL,
  `product_name` VARCHAR(255) NULL DEFAULT NULL,
  `provider_name` VARCHAR(100) NULL DEFAULT NULL,
  `quantity_needed` MEDIUMINT UNSIGNED NULL DEFAULT NULL,
  `quantity_available` MEDIUMINT UNSIGNED NULL DEFAULT NULL,
  `reason` VARCHAR(255) NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  `created_on` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `amazoni`.`amazoni4_provider_order_errors` 
ADD COLUMN `system_solution` TEXT NULL DEFAULT NULL AFTER `quantity_available`;

CREATE TABLE `amazoni`.`amazoni4_provider_order_extra_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `provider_order_id` INT NOT NULL,
  `provider_id` INT NOT NULL,
  `provider_name` VARCHAR(255) NOT NULL,
  `product_sku` VARCHAR(64) NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `quantity` MEDIUMINT NULL DEFAULT 0,
  `date_needed` DATE NOT NULL,
  `ordered` TINYINT NULL DEFAULT 0,
  `created_by` INT NULL,
  `created_on` DATETIME NULL,
  `modified_by` INT NULL,
  `modified_on` DATETIME NULL,
  PRIMARY KEY (`id`));


ALTER TABLE `amazoni`.`amazoni4_provider_order_extra_items` 
ADD COLUMN `reason` VARCHAR(255) NULL DEFAULT NULL AFTER `ordered`;


ALTER TABLE `amazoni`.`amazoni4_provider_order_extra_items` 
CHANGE COLUMN `provider_order_id` `provider_order_id` INT(11) NULL ;
