CREATE TABLE `amazoni`.`amazoni4_providers_products_statistic_history` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `provider_name` VARCHAR(100) NOT NULL,
  `provider_id` INT(11) NOT NULL,
  `total_products` INT(11) NOT NULL,
  `total_products_with_stock` INT(11) NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_on` DATETIME NOT NULL,
  `updated_on` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `index` (`provider_name` ASC));

