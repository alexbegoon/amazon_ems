ALTER TABLE `amazoniTest`.`amazoni4_web_field` 
ADD COLUMN `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `template_language`,
ADD COLUMN `hostname` VARCHAR(128) NOT NULL COMMENT 'The hostname of your database server.' AFTER `timestamp`,
ADD COLUMN `username` VARCHAR(45) NOT NULL COMMENT 'The username used to connect to the database' AFTER `hostname`,
ADD COLUMN `password` BLOB NOT NULL COMMENT 'The password used to connect to the database' AFTER `username`,
ADD COLUMN `database` VARCHAR(45) NOT NULL COMMENT 'The name of the database you want to connect to' AFTER `password`,
ADD COLUMN `dbprefix` VARCHAR(45) NOT NULL COMMENT 'You can add an optional prefix, which will be added to the table name when using the  Active Record class' AFTER `database`;

ALTER TABLE `amazoniTest`.`amazoni4_web_field` 
CHANGE COLUMN `dbprefix` `dbprefix` VARCHAR(45) NULL COMMENT 'You can add an optional prefix, which will be added to the table name when using the  Active Record class' ;

ALTER TABLE `amazoniTest`.`amazoni4_web_field` 
ADD COLUMN `char_set` VARCHAR(30) NULL COMMENT 'The character set used in communicating with the database' AFTER `dbprefix`,
ADD COLUMN `dbcollat` VARCHAR(30) NULL COMMENT 'The character collation used in communicating with the database\n NOTE: For MySQL and MySQLi databases, this setting is only used\n as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7\n (and in table creation queries made with DB Forge).\n Ther /* comment truncated */ /*e is an incompatibility in PHP with mysql_real_escape_string() which
 can make your site vulnerable to SQL injection if you are using a
 multi-byte character set and are running versions lower than these.
 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.*/' AFTER `char_set`;


ALTER TABLE `amazoniTest`.`pedidos` 
ADD COLUMN `magnet_msg_received` TINYINT NULL DEFAULT 0 AFTER `in_stokoni`;
