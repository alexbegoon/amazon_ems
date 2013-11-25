CREATE TABLE `amazoniTest`.`amazoni4_shipping_types` (
  `shipping_type_id` INT NOT NULL AUTO_INCREMENT,
  `shipping_type_name` VARCHAR(32) NOT NULL,
  `shipping_type_description` VARCHAR(255) NULL,
  `shipping_type_keywords` VARCHAR(64) NULL,
  `shipping_type_regexp` VARCHAR(255) NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`shipping_type_id`),
  UNIQUE INDEX `shipping_type_name_UNIQUE` (`shipping_type_name` ASC))
ENGINE = InnoDB DEFAULT CHARSET=utf8
COMMENT = 'Store shipping types. For example: Standard, Express and etc /* comment truncated */ /*.*/';


ALTER TABLE `amazoniTest`.`amazoni4_shipping_costs` 
ADD COLUMN `web` VARCHAR(30) NOT NULL AFTER `price`,
ADD COLUMN `shipping_type_id` INT(11) NOT NULL AFTER `web`;

TRUNCATE `amazoniTest`.`amazoni4_shipping_costs`;

ALTER TABLE `amazoniTest`.`amazoni4_shipping_costs` 
DROP FOREIGN KEY `amazoni4_shipping_costs_ibfk_2`;
ALTER TABLE `amazoniTest`.`amazoni4_shipping_costs` 
DROP INDEX `country_code` ;


ALTER TABLE `amazoniTest`.`amazoni4_shipping_costs` 
ADD INDEX `amazoni4_shipping_costs_ibfk_2_idx` (`country_code` ASC);
ALTER TABLE `amazoniTest`.`amazoni4_shipping_costs` 
ADD CONSTRAINT `amazoni4_shipping_costs_ibfk_2`
  FOREIGN KEY (`country_code`)
  REFERENCES `amazoniTest`.`amazoni4_countries` (`code`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `amazoniTest`.`amazoni4_shipping_costs` 
ADD INDEX `amazoni4_shipping_costs_ibfk_3_idx` (`web` ASC),
ADD INDEX `amazoni4_shipping_costs_ibfk_4_idx` (`shipping_type_id` ASC);
ALTER TABLE `amazoniTest`.`amazoni4_shipping_costs` 
ADD CONSTRAINT `amazoni4_shipping_costs_ibfk_3`
  FOREIGN KEY (`web`)
  REFERENCES `amazoniTest`.`amazoni4_web_field` (`web`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `amazoni4_shipping_costs_ibfk_4`
  FOREIGN KEY (`shipping_type_id`)
  REFERENCES `amazoniTest`.`amazoni4_shipping_types` (`shipping_type_id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


ALTER TABLE `amazoniTest`.`amazoni4_shipping_costs` 
ADD UNIQUE INDEX `shipping_costs_unique` (`id_shipping_company` ASC, `country_code` ASC, `web` ASC, `shipping_type_id` ASC);


ALTER TABLE `amazoniTest`.`amazoni4_shipping_costs` 
ADD COLUMN `description` VARCHAR(255) NULL AFTER `shipping_type_id`,
ADD COLUMN `regexp` VARCHAR(255) NULL AFTER `description`;


ALTER TABLE `amazoniTest`.`amazoni4_shipping_companies` 
CHANGE COLUMN `company_description` `company_description` VARCHAR(255) NULL ,
ADD COLUMN `company_regexp` VARCHAR(255) NULL AFTER `company_description`;


