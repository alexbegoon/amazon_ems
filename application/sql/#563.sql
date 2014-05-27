CREATE TABLE `amazoni`.`amazoni4_roturastock_report` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `date_when_out_of_stock` DATETIME NULL DEFAULT NULL,
  `order_id` INT NULL DEFAULT NULL,
  `order_name` VARCHAR(20) NULL DEFAULT NULL,
  `order_status` VARCHAR(64) NULL DEFAULT NULL,
  `created_on` DATETIME NULL DEFAULT NULL,
  `created_by` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `amazoni`.`amazoni4_roturastock_report` 
ADD INDEX `order_id` (`order_id` ASC);
