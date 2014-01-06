ALTER TABLE `amazoni`.`amazoni4_products_sales_history` 
ADD COLUMN `shipping_price` DECIMAL(15,5) NULL DEFAULT NULL COMMENT 'Shipping price of this order' AFTER `warehouse_price`;

