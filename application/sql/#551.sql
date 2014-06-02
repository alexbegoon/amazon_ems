CREATE TABLE `amazoni`.`amazoni4_order_modifications` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `order_id` INT NULL,
  `user_id` INT NULL,
  `product_sku` VARCHAR(64) NULL,
  `action` TINYINT NULL,
  `created_on` DATETIME NULL,
  PRIMARY KEY (`id`),
  INDEX `order_id` (`order_id` ASC));
