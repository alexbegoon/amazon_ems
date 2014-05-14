ALTER TABLE `amazoni`.`amazoni4_providers` 
ADD COLUMN `emails_list` TEXT NULL DEFAULT NULL AFTER `description`,
ADD COLUMN `cc_emails_list` TEXT NULL DEFAULT NULL AFTER `emails_list`,
ADD COLUMN `email_subject` TEXT NULL DEFAULT NULL AFTER `cc_emails_list`,
ADD COLUMN `email_content` TEXT NULL DEFAULT NULL AFTER `email_subject`;
