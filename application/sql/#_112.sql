CREATE TABLE `amazoniTest`.`amazoni4_top_sales` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`))
ENGINE = MyISAM;

ALTER TABLE `amazoniTest`.`amazoni4_top_sales` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `amazoniTest`.`amazoni4_top_sales` 
ADD COLUMN `sku` VARCHAR(64) NOT NULL AFTER `id`,
ADD COLUMN `quantity` INT(5) NOT NULL AFTER `sku`,
ADD COLUMN `sales_price` DECIMAL(15,5) NOT NULL AFTER `quantity`,
ADD COLUMN `provider_name` VARCHAR(255) NOT NULL AFTER `sales_price`,
ADD COLUMN `provider_id` INT(11) NOT NULL AFTER `provider_name`,
ADD COLUMN `web` VARCHAR(30) NOT NULL AFTER `provider_id`,
ADD COLUMN `order_id` INT(10) NOT NULL AFTER `web`,
ADD INDEX `provider` (`provider_name` ASC);

ALTER TABLE `amazoniTest`.`amazoni4_top_sales` 
ADD COLUMN `product_name` VARCHAR(255) NOT NULL AFTER `sku`,
ADD INDEX `sku` (`sku` ASC);


ALTER TABLE `amazoniTest`.`amazoni4_top_sales` 
ADD COLUMN `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `order_id`;


CREATE TABLE `amazoni4_providers_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(64) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `provider_name` varchar(100) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku_provider` (`sku`,`provider_name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


ALTER TABLE `amazoniTest`.`amazoni4_providers_products` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `amazoniTest`.`amazoni4_providers_products` 
ENGINE = InnoDB ;

ALTER TABLE `amazoniTest`.`amazoni4_providers_products` 
ADD COLUMN `price` DECIMAL(15,5) NOT NULL AFTER `product_name`,
ADD COLUMN `stock` INT(6) NOT NULL AFTER `price`;


-- ALTER TABLE `amazoniTest`.`amazoni4_providers_products` 
-- ADD COLUMN `sku` VARCHAR(128) NOT NULL AFTER `id`,
-- ADD COLUMN `product_name` VARCHAR(255) NOT NULL AFTER `sku`,
-- ADD COLUMN `provider_name` VARCHAR(255) NOT NULL AFTER `product_name`,
-- ADD COLUMN `provider_id` INT(11) NOT NULL AFTER `provider_name`,
-- ADD COLUMN `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `provider_id`;


ALTER TABLE `amazoniTest`.`amazoni4_providers_products` 
CHANGE COLUMN `sku` `sku` VARCHAR(64) NOT NULL ;

ALTER TABLE `amazoniTest`.`amazoni4_top_sales` 
ADD COLUMN `order_date` DATE NOT NULL COMMENT 'Date of order' AFTER `timestamp`;

ALTER TABLE `amazoniTest`.`amazoni4_top_sales` 
CHANGE COLUMN `sku` `sku` VARCHAR(64) NOT NULL ;

