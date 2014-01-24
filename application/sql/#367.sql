ALTER TABLE `amazoni`.`amazoni4_web_field` 
ADD COLUMN `print_order_title` TEXT NULL DEFAULT NULL AFTER `installed_languages`,
ADD COLUMN `print_order_footer` TEXT NULL DEFAULT NULL AFTER `print_order_title`;

