CREATE TABLE IF NOT EXISTS `unl_scan_attributes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `scans_id` INT NOT NULL,
  `html_version` VARCHAR(10),
  `dep_version` VARCHAR(10),
  `root_site_url` VARCHAR(255),
  CONSTRAINT `fk_unl_scan_attributes_scans1`
  FOREIGN KEY (`scans_id`)
  REFERENCES `scans` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  PRIMARY KEY (`id`),
  INDEX `scan_html_version_index` (`html_version` ASC),
  INDEX `scan_dep_version_index` (`dep_version` ASC))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `unl_page_attributes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `scanned_page_id` INT NOT NULL,
  `html_version` VARCHAR(10),
  `dep_version` VARCHAR(10),
  CONSTRAINT `fk_unl_page_attributes_page1`
  FOREIGN KEY (`scanned_page_id`)
  REFERENCES `scanned_page` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  PRIMARY KEY (`id`),
  INDEX `scan_html_version_index` (`html_version` ASC),
  INDEX `scan_dep_version_index` (`dep_version` ASC))
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `unl_site_progress` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `sites_id` INT NOT NULL,
  `estimated_completion` DATE DEFAULT '2010-01-01',
  `self_progress` INT(3) NOT NULL DEFAULT 0,
  `self_comments` TEXT,
  `created` DATETIME NOT NULL,
  `updated` DATETIME NOT NULL,
  `replaced_by` INT NULL,
  CONSTRAINT `fk_unl_site_status_site1`
  FOREIGN KEY (`sites_id`)
  REFERENCES `sites` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT fk_replaced_by
  FOREIGN KEY (`replaced_by`)
  references `sites`(`id`),
  PRIMARY KEY  (`id`)
) ENGINE = InnoDB;
