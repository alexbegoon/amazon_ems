ALTER TABLE `amazoni`.`pedidos` 
CHANGE COLUMN `direccion` `direccion` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ;

ALTER TABLE `amazoni`.`amazoni4_pedidos_temp` 
CHANGE COLUMN `direccion` `direccion` TEXT CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL ;
