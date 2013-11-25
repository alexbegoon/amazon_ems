ALTER TABLE `amazoniTest2`.`amazoni4_web_field` 
ADD COLUMN `sync_enabled` TINYINT NULL DEFAULT 0 COMMENT 'Sync process using Sinc_general library. Help to extract orders from Virtuemart\'s DB.' AFTER `dbcollat`,
ADD COLUMN `start_time` TIMESTAMP NULL DEFAULT NULL COMMENT 'Start datetime of sync process' AFTER `sync_enabled`,
ADD COLUMN `test_mode` TINYINT NULL DEFAULT 0 COMMENT 'If test mode is 1, then orders not store to DB and appears as standart output' AFTER `start_time`,
ADD COLUMN `virtuemart_version` VARCHAR(10) NOT NULL AFTER `test_mode`;

ALTER TABLE `amazoniTest2`.`amazoni4_web_field` 
CHANGE COLUMN `sync_enabled` `sync_enabled` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Sync process using Sinc_general library. Help to extract orders from Virtuemart\'s DB.' ;


