
CREATE TABLE `amazoni`.`amazoni4_amazon_price_rules` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `provider_id` INT NOT NULL,
  `provider_name` VARCHAR(255) NOT NULL,
  `web` VARCHAR(30) NOT NULL,
  `currency_id` INT NOT NULL,
  `multiply` DECIMAL(15,5) NOT NULL DEFAULT 1,
  `sum` DECIMAL(15,5) NOT NULL DEFAULT 0,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`));


ALTER TABLE `amazoni`.`amazoni4_amazon_price_rules` 
ADD UNIQUE INDEX `unique_index` (`provider_id` ASC, `web` ASC, `currency_id` ASC);

ALTER TABLE `amazoni`.`amazoni4_amazon_price_rules` 
DROP INDEX `unique_index` ,
ADD UNIQUE INDEX `unique_index` (`provider_id` ASC, `web` ASC);

ALTER TABLE `amazoni`.`amazoni4_amazon_price_rules` 
ADD COLUMN `transport` DECIMAL(15,5) NOT NULL DEFAULT 0 AFTER `sum`,
ADD COLUMN `marketplace` DECIMAL(15,5) NOT NULL DEFAULT 1 AFTER `transport`,
ADD COLUMN `tax` DECIMAL(15,5) NOT NULL DEFAULT 1 AFTER `marketplace`;
