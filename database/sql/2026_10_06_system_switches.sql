-- Same as migration 2026_10_06_000000_create_system_switches_table,
-- for running by hand (e.g. phpMyAdmin) if migrations are not used on the server.
CREATE TABLE IF NOT EXISTS `system_switches` (
  `key` varchar(50) NOT NULL,
  `is_on` tinyint(1) NOT NULL DEFAULT 0,
  `message` varchar(500) DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `system_switch_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `is_on` tinyint(1) NOT NULL,
  `message` varchar(500) DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_switch_logs_created_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record it as migrated so `php artisan migrate` won't try to create it again.
-- Safe to run more than once.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT `m`.`migration`, `m`.`batch` FROM (
    SELECT '2026_10_06_000000_create_system_switches_table' AS `migration`, COALESCE(MAX(`batch`), 0) + 1 AS `batch` FROM `migrations`
) AS `m`
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_10_06_000000_create_system_switches_table');
