CREATE TABLE `amazoni`.`amazoni4_amazon_sales_rank` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `ean` VARCHAR(64) NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `brand_name` VARCHAR(255) NULL DEFAULT '',
  `web` VARCHAR(30) NOT NULL,
  `sales_rank` INT NOT NULL DEFAULT 0,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_on` DATETIME NOT NULL,
  `updated_on` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `index` (`ean` ASC));
