
ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `inner_sku` VARCHAR(64) NULL DEFAULT NULL AFTER `inner_id`;
