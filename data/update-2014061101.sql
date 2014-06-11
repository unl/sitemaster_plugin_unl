ALTER TABLE unl_site_progress ADD replaced_by INT NULL;
ALTER TABLE unl_site_progress ADD CONSTRAINT fk_replaced_by FOREIGN KEY (`replaced_by`) references `sites`(`id`);
