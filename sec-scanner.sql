
-- -----------------------------------------------------
-- Table `projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projects` ;

CREATE  TABLE IF NOT EXISTS `projects` (
  `project_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `project_name` VARCHAR(256) NOT NULL ,
  `project_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  `project_directory` VARCHAR(256) NOT NULL ,
  `project_scanned` TINYINT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`project_id`) ,
  UNIQUE INDEX `project_id_UNIQUE` (`project_id` ASC) )
  ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `reports`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `reports` ;

CREATE  TABLE IF NOT EXISTS `reports` (
  `report_id` VARCHAR(64) NOT NULL ,
  `project_id` INT UNSIGNED NOT NULL ,
  `report_file` VARCHAR(128) NOT NULL ,
  `report_language` VARCHAR(12) NOT NULL ,
  `report_type` VARCHAR(32) NOT NULL ,
  `report_line` INT NOT NULL ,
  `report_code` TEXT NOT NULL ,
  `report_false` TINYINT(1) NOT NULL DEFAULT 0 ,
  `report_file_signature` VARCHAR(32) NOT NULL ,
  `report_ticket` VARCHAR(512) NULL ,
  PRIMARY KEY (`report_id`) ,
  UNIQUE INDEX `report_id_UNIQUE` (`report_id` ASC) ,
  INDEX `report_false` (`report_false` ASC) ,
  INDEX `report_file` (`report_file` ASC) ,
  INDEX `project_id` (`project_id` ASC) ,
  INDEX `report_file_signature` (`report_file_signature` ASC) )
  ENGINE = InnoDB;

