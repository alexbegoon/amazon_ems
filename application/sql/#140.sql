ALTER TABLE `amazoniTest`.`amazoni4_top_sales` 
CHANGE COLUMN `sku` `sku` VARCHAR(64) NOT NULL COMMENT 'SKU from product list of provider (providers_product)' ,
ADD COLUMN `sku_in_order` VARCHAR(64) NOT NULL COMMENT 'SKU that in order' AFTER `sku`;

