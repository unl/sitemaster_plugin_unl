CREATE TABLE IF NOT EXISTS `unl_cms_id` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `site_id` INT NOT NULL,
  `unlcms_site_id` VARCHAR(20),
  `next_gen_cms_site_id` VARCHAR(20),
  PRIMARY KEY (`id`),
  FOREIGN KEY (`site_id`)
  references `sites`(`id`)
) ENGINE = InnoDB;