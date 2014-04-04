ALTER TABLE `amazoni`.`amazoni4_users` 
ADD COLUMN `receive_notifcations` TINYINT(1) NULL DEFAULT 0 AFTER `phone`;

ALTER TABLE `amazoni`.`amazoni4_users` 
CHANGE COLUMN `receive_notifcations` `receive_notifications` TINYINT(1) NULL DEFAULT '0' ;

