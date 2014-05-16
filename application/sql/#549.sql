
CREATE TABLE `amazoni`.`amazoni4_provider_orders` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `provider_id` INT NULL,
  `provider_name` VARCHAR(100) NULL,
  `created_on` DATETIME NULL,
  `created_by` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `provider_name` (`provider_name` ASC));


CREATE TABLE `amazoni`.`amazoni4_provider_order_items` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `provider_order_id` INT(11) NULL,
  `order_item_id` INT(11) NULL,
  `provider_price` DECIMAL(15,5) NULL,
  `created_on` DATETIME NULL,
  `created_by` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `provider_order_id` (`provider_order_id` ASC));


ALTER TABLE `amazoni`.`amazoni4_provider_order_items` 
ADD COLUMN `quantity` MEDIUMINT NULL DEFAULT NULL AFTER `provider_price`;


ALTER TABLE `amazoni`.`amazoni4_provider_orders` 
ADD COLUMN `sent_to_provider` TINYINT NULL DEFAULT 0 AFTER `provider_name`,
ADD COLUMN `sending_date` DATETIME NULL AFTER `sent_to_provider`;
