CREATE TABLE `amazoni`.`amazoni4_products_translation` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` INT UNSIGNED NOT NULL,
  `language_code` CHAR(5) NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_desc` TEXT NULL,
  `product_s_desc` TEXT NULL,
  `meta_desc` VARCHAR(255) NULL,
  `meta_keywords` VARCHAR(255) NULL,
  `custom_title` VARCHAR(255) NULL,
  `slug` VARCHAR(255) NULL,
  `created_on` DATETIME NOT NULL,
  `updated_on` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `translation_id` (`product_id` ASC),
  INDEX `language` (`language_code` ASC));


CREATE TABLE `amazoni`.`amazoni4_translation_languages` (
  `language_code` CHAR(5) NOT NULL,
  `language_name` VARCHAR(100) NULL,
  PRIMARY KEY (`language_code`));

INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('nl-NL', 'Dutch (NL)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('en-AU', 'English (Australia)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('en-US', 'English (USA)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('en-GB', 'English (United Kingdom)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('es-ES', 'Español (España)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('fr-FR', 'French (fr-FR)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('de-DE', 'German (Germany-Switzerland-Austria)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('it-IT', 'Italian (IT)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('nn-NO', 'Norsk nynorsk (Norway)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('pt-PT', 'Português (pt-PT)');
INSERT INTO `amazoni`.`amazoni4_translation_languages` (`language_code`, `language_name`) VALUES ('sv-SE', 'Svenska (SE)');


-- Part II

TRUNCATE `amazoni`.`amazoni4_products_translation`;

ALTER TABLE `amazoni`.`amazoni4_products_translation` 
CHANGE COLUMN `product_id` `sku` VARCHAR(64) NOT NULL ;

ALTER TABLE `amazoni`.`amazoni4_products_translation` 
DROP INDEX `translation_id` ;

ALTER TABLE `amazoni`.`amazoni4_products_translation` 
ADD INDEX `translation_id` (`sku` ASC);

ALTER TABLE `amazoni`.`amazoni4_providers_products` 
ADD COLUMN `locked_on` DATETIME NULL DEFAULT NULL AFTER `updated_on`,
ADD COLUMN `locked_by` INT NULL DEFAULT 0 AFTER `locked_on`;

ALTER TABLE `amazoni`.`amazoni4_products_translation` 
ADD COLUMN `locked_on` DATETIME NULL DEFAULT NULL AFTER `updated_on`,
ADD COLUMN `locked_by` INT NULL DEFAULT 0 AFTER `locked_on`;
