ALTER TABLE unl_page_attributes ADD `template_type` VARCHAR(20);
ALTER TABLE unl_scan_attributes ADD `template_type` VARCHAR(20);
ALTER TABLE unl_version_history CHANGE `version_type` `version_type` ENUM('HTML','DEP', 'TYPE');

ALTER TABLE unl_page_attributes ADD KEY `page_template_type_index` (`template_type`);
ALTER TABLE unl_scan_attributes ADD KEY `scan_template_type_index` (`template_type`);