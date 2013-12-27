ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `brand` VARCHAR(255) NULL DEFAULT NULL AFTER `provider_id`,
ADD COLUMN `sex` VARCHAR(45) NULL DEFAULT NULL AFTER `brand`;

