
ALTER TABLE `amazoni`.`amazoni4_amazon_price_rules` 
ADD COLUMN `ean` VARCHAR(100) NULL AFTER `timestamp`;

ALTER TABLE `amazoni`.`amazoni4_amazon_price_rules` 
DROP INDEX `unique_index` ,
ADD UNIQUE INDEX `unique_index` (`provider_id` ASC, `web` ASC, `ean` ASC);
