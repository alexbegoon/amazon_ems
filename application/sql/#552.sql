
CREATE TABLE `amazoni`.`amazoni4_order_status_history` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NULL DEFAULT NULL,
  `status` VARCHAR(128) NULL DEFAULT NULL,
  `user_id` INT NULL DEFAULT NULL,
  `created_on` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `order_id` (`order_id` ASC));