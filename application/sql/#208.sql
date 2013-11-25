ALTER TABLE `amazoniTest2`.`amazoni4_web_field` 
ADD COLUMN `installed_languages` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Installed languages on the web-site. en_gb, es_es' AFTER `virtuemart_version`;

