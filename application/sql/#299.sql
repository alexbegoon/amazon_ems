CREATE TABLE `amazoni`.`amazoni4_amazon_requests_log` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `Feed_Submission_Id` VARCHAR(100) NULL,
  `Feed_Type` VARCHAR(100) NULL,
  `Submitted_Date` VARCHAR(100) NULL,
  `Feed_Processing_Status` VARCHAR(100) NULL,
  `Request_Id` VARCHAR(255) NULL,
  `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = MyISAM;


ALTER TABLE `amazoni`.`amazoni4_amazon_requests_log` 
ADD UNIQUE INDEX `unique` (`Feed_Submission_Id` ASC, `Feed_Processing_Status` ASC);


ALTER TABLE `amazoni`.`amazoni4_amazon_requests_log` 
ADD COLUMN `Request_Result` TEXT NULL DEFAULT NULL AFTER `Completed_Processing_Date`;

