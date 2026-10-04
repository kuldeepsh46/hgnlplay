-- Same as migration 2026_10_05_000000_create_rank_reward_payouts_table,
-- for running by hand (e.g. phpMyAdmin) if migrations are not used on the server.
CREATE TABLE IF NOT EXISTS `rank_reward_payouts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `level` tinyint unsigned NOT NULL,
  `rank_name` varchar(100) NOT NULL,
  `pairs_step` int unsigned NOT NULL,
  `pairs_required` int unsigned NOT NULL,
  `left_count` int unsigned NOT NULL,
  `right_count` int unsigned NOT NULL,
  `pairs_at_payout` int unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rank_reward_user_level_unique` (`user_id`,`level`),
  KEY `rank_reward_created_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rank_reward_baselines` (
  `user_id` bigint unsigned NOT NULL,
  `left_count` int unsigned NOT NULL,
  `right_count` int unsigned NOT NULL,
  `pairs` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Record it as migrated so `php artisan migrate` won't try to create it again.
-- Safe to run more than once.
INSERT INTO `migrations` (`migration`, `batch`)
SELECT `m`.`migration`, `m`.`batch` FROM (
    SELECT '2026_10_05_000000_create_rank_reward_payouts_table' AS `migration`, COALESCE(MAX(`batch`), 0) + 1 AS `batch` FROM `migrations`
) AS `m`
WHERE NOT EXISTS (SELECT 1 FROM `migrations` WHERE `migration` = '2026_10_05_000000_create_rank_reward_payouts_table');
