ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `created_on` DATETIME NOT NULL AFTER `timestamp`,
ADD COLUMN `updated_on` DATETIME NOT NULL AFTER `created_on`;

ALTER TABLE `amazoni`.`amazoni4_providers_products` 
CHANGE COLUMN `created_on` `created_on` DATETIME NULL ,
CHANGE COLUMN `updated_on` `updated_on` DATETIME NULL ;

