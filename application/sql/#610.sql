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

