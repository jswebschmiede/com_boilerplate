CREATE TABLE IF NOT EXISTS `#__boilerplate_boilerplate` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`created_by` int(11) NOT NULL,
	`created` datetime NOT NULL,
	`ordering` int(11) NOT NULL,
	`state` INT(11) NOT NULL DEFAULT 1,
	`title` varchar(255),
	`description` text,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB COMMENT = '' DEFAULT COLLATE = utf8_general_ci;