ALTER TABLE `amazoni`.`amazoni4_providers` 
ADD COLUMN `csv_format` TEXT NULL DEFAULT NULL AFTER `email_content`,
ADD COLUMN `xls_format` TEXT NULL DEFAULT NULL AFTER `csv_format`;

ALTER TABLE `amazoni`.`amazoni4_providers` 
ADD COLUMN `send_csv` TINYINT NULL DEFAULT 0 AFTER `xls_format`,
ADD COLUMN `send_xls` TINYINT NULL DEFAULT 1 AFTER `send_csv`;

