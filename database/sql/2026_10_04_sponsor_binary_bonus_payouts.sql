-- Same as migration 2026_10_04_000000_create_sponsor_binary_bonus_payouts_table,
-- for running by hand (e.g. phpMyAdmin) if migrations are not used on the server.
CREATE TABLE IF NOT EXISTS `sponsor_binary_bonus_payouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sponsor_id` bigint unsigned NOT NULL,
  `source_user_id` bigint unsigned NOT NULL,
  `income_date` date NOT NULL,
  `binary_income` decimal(15,2) NOT NULL,
  `capped_income` decimal(15,2) NOT NULL,
  `bonus_amount` decimal(15,2) NOT NULL,
  `transaction_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sbb_payouts_source_date_unique` (`source_user_id`,`income_date`),
  KEY `sbb_payouts_sponsor_date_index` (`sponsor_id`,`income_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record it as migrated so `php artisan migrate` won't try to create it again.
-- Safe to run more than once.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT `m`.`migration`, `m`.`batch` FROM (
    SELECT '2026_10_04_000000_create_sponsor_binary_bonus_payouts_table' AS `migration`, COALESCE(MAX(`batch`), 0) + 1 AS `batch` FROM `migrations`
) AS `m`
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_10_04_000000_create_sponsor_binary_bonus_payouts_table');
