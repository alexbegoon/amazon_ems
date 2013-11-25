
#112

ALTER TABLE amazoniTest.amazoni4_providers CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `amazoniTest`.`amazoni4_web_field` 
ENGINE = InnoDB ;

ALTER TABLE amazoniTest.amazoni4_web_field CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `amazoniTest`.`amazoni4_web_provider` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `web` VARCHAR(30) NULL,
  `provider_id` INT(11) NULL,
  `sku_regexp` VARCHAR(255) NULL COMMENT 'Helps to choose correct provider (if web have more than one provider) using UNIQUE sku format of product',
  PRIMARY KEY (`id`),
  INDEX `provider_id_fk_idx` (`provider_id` ASC),
  INDEX `web_fk_idx` (`web` ASC),
  CONSTRAINT `provider_id_fk`
    FOREIGN KEY (`provider_id`)
    REFERENCES `amazoniTest`.`amazoni4_providers` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `web_fk`
    FOREIGN KEY (`web`)
    REFERENCES `amazoniTest`.`amazoni4_web_field` (`web`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'List of relations - WEB to PROVIDER';

ALTER TABLE `amazoniTest`.`amazoni4_web_provider` 
DROP FOREIGN KEY `provider_id_fk`,
DROP FOREIGN KEY `web_fk`;
ALTER TABLE `amazoniTest`.`amazoni4_web_provider` 
CHANGE COLUMN `web` `web` VARCHAR(30) NOT NULL ,
CHANGE COLUMN `provider_id` `provider_id` INT(11) NOT NULL ;
ALTER TABLE `amazoniTest`.`amazoni4_web_provider` 
ADD CONSTRAINT `provider_id_fk`
  FOREIGN KEY (`provider_id`)
  REFERENCES `amazoniTest`.`amazoni4_providers` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `web_fk`
  FOREIGN KEY (`web`)
  REFERENCES `amazoniTest`.`amazoni4_web_field` (`web`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;




alter table amazoniTest.amazoni4_web_provider add unique index(web, provider_id);


ALTER TABLE `amazoniTest`.`amazoni4_web_provider` 
ADD COLUMN `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `sku_regexp`;


ALTER TABLE `amazoniTest`.`amazoni4_web_field` 
CHANGE COLUMN `title` `title` VARCHAR(255) NOT NULL ,
CHANGE COLUMN `url` `url` VARCHAR(255) NOT NULL ,
CHANGE COLUMN `email` `email` VARCHAR(255) NOT NULL ,
CHANGE COLUMN `template_language` `template_language` VARCHAR(2) NOT NULL ;

ALTER TABLE `amazoniTest`.`amazoni4_languages` 
ADD INDEX `code` (`code` ASC);


ALTER TABLE `amazoniTest`.`amazoni4_shipping_companies` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `amazoniTest`.`amazoni4_stock_temp` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `amazoniTest`.`amazoni4_taxes` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `amazoniTest`.`amazoni4_other_costs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `amazoniTest`.`amazoni4_exchange_rates` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `amazoniTest`.`amazoni4_sessions` CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `amazoniTest`.`amazoni4_web_provider` 
ADD COLUMN `sku_regexp_2` VARCHAR(255) NULL DEFAULT NULL AFTER `sku_regexp`;

ALTER TABLE `amazoniTest`.`amazoni4_web_provider` 
CHANGE COLUMN `sku_regexp_2` `sku_regexp_2` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Regular expression, that helps to restore the format of SKU for this Provider. \nIt may be useful in case when you try to extract name from provider\'s product list. ' ;



