CREATE TABLE `amazoniTest2`.`amazoni4_customer_reviews` (
  `id` INT(11) NOT NULL,
  `web` VARCHAR(30) NOT NULL,
  `comment` TEXT NULL DEFAULT NULL,
  `rating` DECIMAL(10,2) NULL DEFAULT '0.00',
  `created` DATETIME NULL DEFAULT '0000-00-00 00:00:00',
  `virtuemart_product_id` INT(11) NOT NULL,
  `product_name` VARCHAR(255) NULL,
  `product_sku` VARCHAR(64) NOT NULL,
  `provider_product_sku` VARCHAR(64) NOT NULL,
  `virtuemart_rating_review_id` INT(11) NOT NULL DEFAULT 0,
  `amazoni_product_id` INT(11) NOT NULL COMMENT 'Reference to table providers products',
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = MyISAM
COMMENT = 'Customer reviews from all shops';


ALTER TABLE `amazoniTest2`.`amazoni4_customer_reviews` 
CHANGE COLUMN `product_sku` `product_sku` VARCHAR(64) NULL ,
CHANGE COLUMN `provider_product_sku` `provider_product_sku` VARCHAR(64) NULL ,
CHANGE COLUMN `amazoni_product_id` `amazoni_product_id` INT(11) NULL COMMENT 'Reference to table providers products' ;


ALTER TABLE `amazoniTest2`.`amazoni4_customer_reviews` 
CHANGE COLUMN `rating` `rating` DECIMAL(10,2) NOT NULL DEFAULT '0.00' ,
CHANGE COLUMN `created` `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
CHANGE COLUMN `product_name` `product_name` VARCHAR(255) NOT NULL ,
CHANGE COLUMN `product_sku` `product_sku` VARCHAR(64) NOT NULL ,
CHANGE COLUMN `provider_product_sku` `provider_product_sku` VARCHAR(64) NOT NULL ,
CHANGE COLUMN `amazoni_product_id` `amazoni_product_id` INT(11) NOT NULL COMMENT 'Reference to table providers products' ;


ALTER TABLE `amazoniTest2`.`amazoni4_customer_reviews` 
ADD UNIQUE INDEX `unique` (`web` ASC, `virtuemart_rating_review_id` ASC);


ALTER TABLE `amazoniTest2`.`amazoni4_customer_reviews` 
CHANGE COLUMN `id` `id` INT(11) NOT NULL AUTO_INCREMENT ;

ALTER TABLE `amazoniTest2`.`amazoni4_customer_reviews` 
CHANGE COLUMN `product_name` `product_name` VARCHAR(255) NULL ,
CHANGE COLUMN `provider_product_sku` `provider_product_sku` VARCHAR(64) NULL ,
CHANGE COLUMN `amazoni_product_id` `amazoni_product_id` INT(11) NULL COMMENT 'Reference to table providers products' ;
