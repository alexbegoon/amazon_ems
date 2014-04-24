
INSERT INTO `amazoni`.`amazoni4_other_costs` (`name`, `code`, `price`, `description`) VALUES ('Sagepay cost', 'sagepay', '0', 'Sagepay cost');
INSERT INTO `amazoni`.`amazoni4_other_costs` (`name`, `code`, `price`, `description`) VALUES ('TPV cost', 'tpv', '0', 'TPV cost');
INSERT INTO `amazoni`.`amazoni4_other_costs` (`name`, `code`, `price`, `description`) VALUES ('Paypal cost', 'paypal', '0', 'PayPal cost');
INSERT INTO `amazoni`.`amazoni4_other_costs` (`name`, `code`, `price`, `description`) VALUES ('Operating cost', 'operating_cost', '0', 'Operating cost');
INSERT INTO `amazoni`.`amazoni4_other_costs` (`name`, `code`, `price`, `description`) VALUES ('Operating Profit', 'oper_profit', '0', 'Operating Profit');


ALTER TABLE `amazoni`.`amazoni4_other_costs` 
ADD COLUMN `sign` SMALLINT NULL DEFAULT 0 AFTER `price`;

ALTER TABLE `amazoni`.`amazoni4_other_costs` 
CHANGE COLUMN `sign` `sign` SMALLINT(6) NULL DEFAULT -1 ;


UPDATE `amazoni`.`amazoni4_other_costs` SET `sign`='-1' WHERE `id`='3';
UPDATE `amazoni`.`amazoni4_other_costs` SET `sign`='1' WHERE `id`='4';
UPDATE `amazoni`.`amazoni4_other_costs` SET `sign`='-1' WHERE `id`='5';
UPDATE `amazoni`.`amazoni4_other_costs` SET `sign`='-1' WHERE `id`='6';
UPDATE `amazoni`.`amazoni4_other_costs` SET `sign`='-1' WHERE `id`='7';
UPDATE `amazoni`.`amazoni4_other_costs` SET `sign`='-1' WHERE `id`='8';
UPDATE `amazoni`.`amazoni4_other_costs` SET `sign`='1' WHERE `id`='9';

ALTER TABLE `amazoni`.`amazoni4_other_costs` 
ADD COLUMN `read_only` TINYINT NULL DEFAULT 0 AFTER `sign`;


UPDATE `amazoni`.`amazoni4_other_costs` SET `read_only`='1' WHERE `id`='5';
UPDATE `amazoni`.`amazoni4_other_costs` SET `read_only`='1' WHERE `id`='6';
UPDATE `amazoni`.`amazoni4_other_costs` SET `read_only`='1' WHERE `id`='7';
UPDATE `amazoni`.`amazoni4_other_costs` SET `read_only`='1' WHERE `id`='9';

