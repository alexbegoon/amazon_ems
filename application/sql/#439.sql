ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `quantity_needed` INT(6) NULL AFTER `sales_rank_de`,
ADD COLUMN `target_price` DECIMAL(15,5) NULL AFTER `quantity_needed`;

ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `provider_ordered` TINYINT NULL AFTER `target_price`,
ADD COLUMN `provider_order_date` DATETIME NULL AFTER `provider_ordered`;



ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `is_checked` TINYINT(1) NULL DEFAULT 0 AFTER `provider_order_date`;
