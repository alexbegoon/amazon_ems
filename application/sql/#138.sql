
CREATE TABLE `amazoniTest`.`amazoni4_grutinet` (
  `ean` CHAR(13) NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_description` VARCHAR(255) NULL,
  `price` DECIMAL(15,5) NOT NULL,
  `stock` INT(6) NOT NULL,
  `brand_name` VARCHAR(255) NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ean`),
  INDEX `brand` (`brand_name` ASC))
ENGINE = InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `amazoniTest`.`amazoni4_grutinet_temp` (
  `ean` CHAR(13) NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_description` VARCHAR(255) NULL,
  `price` DECIMAL(15,5) NOT NULL,
  `stock` INT(6) NOT NULL,
  `brand_name` VARCHAR(255) NOT NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ean`),
  INDEX `brand` (`brand_name` ASC))
ENGINE = InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `amazoniTest`.`amazoni4_grutinet` 
ENGINE = MyISAM ;

ALTER TABLE `amazoniTest`.`amazoni4_grutinet_temp` 
ENGINE = MyISAM ;