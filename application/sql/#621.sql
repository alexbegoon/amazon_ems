
ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `inner_id` VARCHAR(64) NULL DEFAULT NULL AFTER `sex`;


ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `provider_image_url` VARCHAR(255) NULL DEFAULT NULL AFTER `inner_id`,
ADD COLUMN `provider_thumb_image_url` VARCHAR(255) NULL DEFAULT NULL AFTER `provider_image_url`;
