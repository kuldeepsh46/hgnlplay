-- ---------------------------------------------------------------------------
-- Dynamic Package Bonuses + Package Pairing
-- ---------------------------------------------------------------------------
-- Plain-SQL equivalent of migration
-- 2026_09_30_000000_make_package_bonuses_dynamic.php, for servers where
-- `php artisan migrate` can't be run (e.g. phpMyAdmin). Run it ONCE.
-- Take a backup first.
--
-- Existing packages are set so payouts stay exactly as before:
--   Starter Package         -> ₹500 fixed direct, ₹300 fixed pair
--   Normal (< ₹50,000)      -> 10% direct, 10% pair, all paired together
--                              (+ Starter, whose ₹1000 EMI joins this pool)
--   Repurchase Booster      -> 0 / 0 (Matrix only)
--   ₹50,000 and above       -> 0 / 0 (level commission, no pair income)
-- ---------------------------------------------------------------------------

-- 1. Bonus value can be % or fixed ------------------------------------------
ALTER TABLE `packages`
    MODIFY `direct_bonus` DECIMAL(10,2) NOT NULL DEFAULT 0,
    MODIFY `pair_bonus` DECIMAL(10,2) NOT NULL DEFAULT 0,
    ADD `direct_bonus_type` VARCHAR(10) NOT NULL DEFAULT 'percent' AFTER `direct_bonus`,
    ADD `pair_bonus_type` VARCHAR(10) NOT NULL DEFAULT 'percent' AFTER `pair_bonus`;

-- 2. Which packages can pair with which (stored both ways) ------------------
CREATE TABLE `package_pairings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `package_id` BIGINT UNSIGNED NOT NULL,
    `paired_package_id` BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `package_pairings_package_id_paired_package_id_unique` (`package_id`, `paired_package_id`),
    KEY `package_pairings_paired_package_id_foreign` (`paired_package_id`),
    CONSTRAINT `package_pairings_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
    CONSTRAINT `package_pairings_paired_package_id_foreign` FOREIGN KEY (`paired_package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Processed pair volume per user, per pairing pool -----------------------
CREATE TABLE `user_pair_volumes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `pool_key` VARCHAR(191) NOT NULL,
    `processed_volume` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_pair_volumes_user_id_pool_key_unique` (`user_id`, `pool_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Keep current payouts for existing packages -----------------------------
UPDATE `packages` SET
    `direct_bonus` = 10, `direct_bonus_type` = 'percent',
    `pair_bonus` = 10, `pair_bonus_type` = 'percent';

UPDATE `packages` SET
    `direct_bonus` = 0, `direct_bonus_type` = 'percent',
    `pair_bonus` = 0, `pair_bonus_type` = 'percent'
WHERE UPPER(TRIM(`name`)) = 'REPURCHASE BOOSTER PACKAGE'
   OR COALESCE(`amount`, `actual_amount`, 0) >= 50000;

UPDATE `packages` SET
    `direct_bonus` = 500, `direct_bonus_type` = 'fixed',
    `pair_bonus` = 300, `pair_bonus_type` = 'fixed'
WHERE UPPER(TRIM(`name`)) = 'STARTER PACKAGE';

-- 5. Normal packages + Starter all pair with each other ---------------------
INSERT INTO `package_pairings` (`package_id`, `paired_package_id`)
SELECT a.`id`, b.`id`
FROM `packages` a
JOIN `packages` b ON a.`id` <> b.`id`
WHERE (UPPER(TRIM(a.`name`)) = 'STARTER PACKAGE'
       OR (UPPER(TRIM(a.`name`)) <> 'REPURCHASE BOOSTER PACKAGE' AND COALESCE(a.`amount`, a.`actual_amount`, 0) < 50000))
  AND (UPPER(TRIM(b.`name`)) = 'STARTER PACKAGE'
       OR (UPPER(TRIM(b.`name`)) <> 'REPURCHASE BOOSTER PACKAGE' AND COALESCE(b.`amount`, b.`actual_amount`, 0) < 50000));

-- 6. Carry over already-processed pair volume so nothing is re-paid ---------
SET @normal_key = (
    SELECT GROUP_CONCAT(`id` ORDER BY `id` SEPARATOR ',')
    FROM `packages`
    WHERE UPPER(TRIM(`name`)) = 'STARTER PACKAGE'
       OR (UPPER(TRIM(`name`)) <> 'REPURCHASE BOOSTER PACKAGE' AND COALESCE(`amount`, `actual_amount`, 0) < 50000)
);
SET @starter_key = (
    SELECT CONCAT('starter:', `id`) FROM `packages` WHERE UPPER(TRIM(`name`)) = 'STARTER PACKAGE' ORDER BY `id` LIMIT 1
);

INSERT INTO `user_pair_volumes` (`user_id`, `pool_key`, `processed_volume`, `created_at`, `updated_at`)
SELECT `id`, @normal_key, `normal_pair_processed_volume`, NOW(), NOW()
FROM `users`
WHERE `normal_pair_processed_volume` > 0 AND @normal_key IS NOT NULL;

INSERT INTO `user_pair_volumes` (`user_id`, `pool_key`, `processed_volume`, `created_at`, `updated_at`)
SELECT `id`, @starter_key, `starter_pair_processed_volume`, NOW(), NOW()
FROM `users`
WHERE `starter_pair_processed_volume` > 0 AND @starter_key IS NOT NULL;

-- 7. Mark the Laravel migration as run so `artisan migrate` skips it --------
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_09_30_000000_make_package_bonuses_dynamic', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
