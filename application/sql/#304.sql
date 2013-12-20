
# remove duplicates before
ALTER TABLE `amazoni`.`stokoni` 
ADD UNIQUE INDEX `unique` (`ean` ASC, `proveedor` ASC);

CREATE TABLE `amazoni4_products_sales_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `sku` varchar(64) NOT NULL,
  `sku_in_order` varchar(64) NOT NULL,
  `provider_name` varchar(100) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `provider_price` decimal(15,5) DEFAULT NULL COMMENT 'Price in providers_products table',
  `order_price` decimal(15,5) NOT NULL COMMENT 'Price in the order',
  `warehouse_price` decimal(15,5) DEFAULT NULL,
  `warehouse_product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `provider_reserve_quantity` int(11) DEFAULT '0' COMMENT 'This field means that order have a quantity of product, that a little more than provider have. This order try to get product as CREDIT',
  `sold_from_warehouse` tinyint(1) NOT NULL DEFAULT '0',
  `web` varchar(30) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_status` varchar(100) DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `canceled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If order canceled or removed , will store this case.',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Store history of all products that was sold.';

ALTER TABLE `amazoni`.`amazoni4_products_sales_history` 
ADD COLUMN `provider_product_id` INT(11) NOT NULL AFTER `timestamp`;

ALTER TABLE `amazoni`.`amazoni4_products_sales_history` 
CHANGE COLUMN `provider_product_id` `provider_product_id` INT(11) NULL DEFAULT NULL AFTER `warehouse_product_id`;

ALTER TABLE `amazoni`.`amazoni4_products_sales_history` 
ADD COLUMN `order_name` VARCHAR(45) NULL DEFAULT NULL COMMENT 'pedido field of pedidos table' AFTER `order_id`;

ALTER TABLE `amazoni`.`amazoni4_products_sales_history` 
ADD COLUMN `out_of_stock` TINYINT(1) NULL DEFAULT 0 AFTER `canceled`;

ALTER TABLE `amazoni`.`amazoni4_products_sales_history` 
ADD COLUMN `csv_exported` TINYINT(1) NULL DEFAULT 0 AFTER `out_of_stock`;

ALTER TABLE `amazoni`.`amazoni4_products_sales_history` 
ADD COLUMN `csv_export_date` DATETIME NULL AFTER `csv_exported`;
