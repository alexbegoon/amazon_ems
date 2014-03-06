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

CREATE TABLE `amazoni`.`amazoni4_amazon_reports` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `report_type` VARCHAR(255) NULL,
  `report_id` VARCHAR(45) NULL,
  `report_request_id` VARCHAR(45) NULL,
  `report_status` VARCHAR(45) NULL,
  `report_requested_at` DATETIME NULL,
  `report_created_at` DATETIME NULL,
  `report_body` LONGTEXT NULL,
  `web` VARCHAR(30) NULL,
  `country_code` CHAR(2) NULL,
  `merchant_id` VARCHAR(30) NULL,
  PRIMARY KEY (`id`))
COMMENT = 'Store reports from Amazon';


ALTER TABLE `amazoni`.`amazoni4_amazon_sales_rank` 
ADD COLUMN `sales_rank_category_name` VARCHAR(128) NULL AFTER `sales_rank`,
ADD COLUMN `merchant_sku` VARCHAR(64) NULL AFTER `sales_rank_category_name`,
ADD COLUMN `asin_isbn` VARCHAR(45) NULL AFTER `merchant_sku`,
ADD COLUMN `status` VARCHAR(100) NULL AFTER `asin_isbn`,
ADD COLUMN `fee_preview` DECIMAL(15,5) NULL AFTER `status`,
ADD COLUMN `fee_preview_currency_code` CHAR(3) NULL AFTER `fee_preview`,
ADD COLUMN `low_price` DECIMAL(15,5) NULL AFTER `fee_preview_currency_code`,
ADD COLUMN `low_price_currency_code` CHAR(3) NULL AFTER `low_price`,
ADD COLUMN `low_price_delivery` DECIMAL(15,5) NULL AFTER `low_price_currency_code`,
ADD COLUMN `low_price_delivery_currency_code` CHAR(3) NULL AFTER `low_price_delivery`,
ADD COLUMN `your_price` DECIMAL(15,5) NULL AFTER `low_price_delivery_currency_code`,
ADD COLUMN `your_price_currency_code` CHAR(3) NULL AFTER `your_price`,
ADD COLUMN `your_price_delivery` DECIMAL(15,5) NULL AFTER `your_price_currency_code`,
ADD COLUMN `your_price_delivery_currency_code` CHAR(3) NULL AFTER `your_price_delivery`;


ALTER TABLE `amazoni`.`amazoni4_amazon_sales_rank` 
ADD COLUMN `condition` VARCHAR(45) NULL AFTER `your_price_delivery_currency_code`;

ALTER TABLE `amazoni`.`amazoni4_amazon_sales_rank` 
ADD COLUMN `products_in_stock` INT NULL AFTER `condition`;


CREATE TABLE `amazoni4_amazon_sales_rank_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ean` varchar(64) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `brand_name` varchar(255) DEFAULT '',
  `web` varchar(30) NOT NULL,
  `sales_rank` int(11) NOT NULL DEFAULT '0',
  `sales_rank_category_name` varchar(128) DEFAULT NULL,
  `merchant_sku` varchar(64) DEFAULT NULL,
  `asin_isbn` varchar(45) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `fee_preview` decimal(15,5) DEFAULT NULL,
  `fee_preview_currency_code` char(3) DEFAULT NULL,
  `low_price` decimal(15,5) DEFAULT NULL,
  `low_price_currency_code` char(3) DEFAULT NULL,
  `low_price_delivery` decimal(15,5) DEFAULT NULL,
  `low_price_delivery_currency_code` char(3) DEFAULT NULL,
  `your_price` decimal(15,5) DEFAULT NULL,
  `your_price_currency_code` char(3) DEFAULT NULL,
  `your_price_delivery` decimal(15,5) DEFAULT NULL,
  `your_price_delivery_currency_code` char(3) DEFAULT NULL,
  `condition` varchar(45) DEFAULT NULL,
  `products_in_stock` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_on` datetime NOT NULL,
  `updated_on` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index` (`ean`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `amazoni`.`amazoni4_amazon_sales_rank_temp` 
CHANGE COLUMN `product_name` `product_name` VARCHAR(255) NULL ,
CHANGE COLUMN `web` `web` VARCHAR(30) NULL ,
CHANGE COLUMN `sales_rank` `sales_rank` INT(11) NULL DEFAULT '0' ;

ALTER TABLE `amazoni`.`amazoni4_amazon_sales_rank` 
CHANGE COLUMN `product_name` `product_name` VARCHAR(255) NULL ,
CHANGE COLUMN `web` `web` VARCHAR(30) NULL ,
CHANGE COLUMN `sales_rank` `sales_rank` INT(11) NULL DEFAULT '0' ;

ALTER TABLE `amazoni`.`amazoni4_amazon_sales_rank_temp` 
CHANGE COLUMN `ean` `ean` VARCHAR(64) NULL ;

ALTER TABLE `amazoni`.`amazoni4_amazon_sales_rank_temp` 
CHANGE COLUMN `condition` `product_condition` VARCHAR(45) NULL DEFAULT NULL ;

ALTER TABLE `amazoni`.`amazoni4_amazon_sales_rank` 
CHANGE COLUMN `condition` `product_condition` VARCHAR(45) NULL DEFAULT NULL ;

ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `sales_rank_uk` INT NULL DEFAULT 0 AFTER `sex`,
ADD COLUMN `sales_rank_de` INT NULL DEFAULT 0 AFTER `sales_rank_uk`;
