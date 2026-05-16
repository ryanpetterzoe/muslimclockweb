-- Muslim Clock Web schema
-- Tables use prefix placeholder {{P}} replaced by installer.

CREATE TABLE IF NOT EXISTS `{{P}}users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(60) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(120) NOT NULL DEFAULT '',
  `role` VARCHAR(20) NOT NULL DEFAULT 'admin',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{{P}}settings` (
  `key` VARCHAR(80) NOT NULL,
  `value` LONGTEXT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{{P}}slides` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('image','video') NOT NULL DEFAULT 'image',
  `path` VARCHAR(255) NOT NULL,
  `caption` VARCHAR(255) NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{{P}}imam_schedule` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `day_of_week` TINYINT NOT NULL COMMENT '0=Min,1=Sen,...6=Sab',
  `prayer` ENUM('subuh','dzuhur','ashar','maghrib','isya','jumat') NOT NULL,
  `imam_name` VARCHAR(120) NOT NULL DEFAULT '',
  `khatib_name` VARCHAR(120) NULL,
  `bilal_name` VARCHAR(120) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_day_prayer` (`day_of_week`,`prayer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{{P}}quran_quotes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `arabic` TEXT NOT NULL,
  `translation` TEXT NULL,
  `reference` VARCHAR(120) NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{{P}}running_text` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `text` TEXT NOT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
