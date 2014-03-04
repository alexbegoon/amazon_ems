ALTER TABLE `amazoni`.`pedidos` 
ADD COLUMN `shipping_phrase` VARCHAR(255) NULL AFTER `magnet_msg_received`;

ALTER TABLE `amazoni`.`pedidos` 
CHANGE COLUMN `formadepago` `formadepago` VARCHAR(255) CHARACTER SET 'utf8' COLLATE 'utf8_bin' NULL DEFAULT NULL ;

