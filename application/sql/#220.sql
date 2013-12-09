
ALTER TABLE `amazoniTest2`.`pedidos` 
CHANGE COLUMN `procesado` `procesado` ENUM('PREPARADO','NO','SI','INCIDENCIA','PREPARACION_ENGELSA_GLS','PREPARACION_ENGELSA_FEDEX','PEDIDO_ENGELSA_GLS','PEDIDO_ENGELSA_FEDEX','ENVIADO_GLS','ENVIADO_FEDEX','PEDIDO_ENGELSA_GLS_AQUI','PEDIDO_ENGELSA_FEDEX_AQUI','ROTURASTOCK','CANCELADO','ENVIADO_GRUTINET','ENALMACEN','PROCESADO_MEGASUR','ENVIADO_MEGASUR','PEDIDO_MARABE','ENVIADO_MARABE','MULTIPRODUCTO','PAGADO','PEDIDO_MARABE_AQUI','PAYPAL','PEDIDO_ENGELSA_PACK','PREPARACION_ENGELSA_PACK','PEDIDO_ENGELSA_PACK_AQUI','ENVIADO_PACK','PREPARACION_ENGELSA_TOURLINE','PREPARACION_ENGELSA_TOURLINE_AQUI','PEDIDO_ENGELSA_TOURLINE','ENVIADO_TOURLINE','PREPARACION_FARMA_TOURLINE','PREPARACION_FARMA_TOURLINE_AQUI','PEDIDO_FARMA_TOURLINE','PTE_PAGO','PEDIDO_ENGELSA_TOURLINE_AQUI') CHARACTER SET 'utf8' COLLATE 'utf8_bin' NOT NULL DEFAULT 'NO' ;