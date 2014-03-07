CREATE TABLE `amazoni`.`amazoni4_providers_products_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT(11) NOT NULL,
  `price` DECIMAL(15,5) NOT NULL,
  `stock` INT(6) NOT NULL,
  `created_on` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `index` (`product_id` ASC))
ENGINE = InnoDB;
