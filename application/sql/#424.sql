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
