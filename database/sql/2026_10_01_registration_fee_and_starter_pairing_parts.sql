-- ---------------------------------------------------------------------------
-- Registration Fee Setting + Starter Pairing Parts + Per-Leg Pair Volume
-- ---------------------------------------------------------------------------
-- Plain-SQL equivalent of migration
-- 2026_10_01_000000_add_registration_fee_and_starter_pairing_parts.php,
-- for servers where `php artisan migrate` can't be run (e.g. phpMyAdmin).
-- Run it ONCE, AFTER 2026_09_30_dynamic_package_bonuses (already live).
-- Take a backup first. Use EITHER this file OR `php artisan migrate`.
-- ---------------------------------------------------------------------------

-- 1. Registration fee per package (Starter + Repurchase Booster: No) ------
ALTER TABLE `packages`
    ADD `charges_registration_fee` TINYINT(1) NOT NULL DEFAULT 1 AFTER `pair_bonus_type`;

UPDATE `packages` SET `charges_registration_fee` = 0
WHERE UPPER(TRIM(`name`)) IN ('STARTER PACKAGE', 'REPURCHASE BOOSTER PACKAGE');

-- 2. Starter pairs in two parts: 'first' (₹1600) / 'repeat' (₹1000) --------
ALTER TABLE `package_pairings`
    ADD `package_part` VARCHAR(10) NULL DEFAULT NULL AFTER `package_id`,
    ADD `paired_part` VARCHAR(10) NULL DEFAULT NULL AFTER `paired_package_id`,
    ADD UNIQUE KEY `package_pairings_unique` (`package_id`, `package_part`, `paired_package_id`, `paired_part`);

ALTER TABLE `package_pairings`
    DROP INDEX `package_pairings_package_id_paired_package_id_unique`;

-- Packages paired when dynamic pairing was introduced (e.g. "1,2,3,8"):
-- with them Starter only ever matched its ₹1000 repurchase -> 'repeat'.
-- Packages created later -> 'first' (₹1600 Starter first purchase).
SET @starter_id = (SELECT `id` FROM `packages` WHERE UPPER(TRIM(`name`)) = 'STARTER PACKAGE' ORDER BY `id` LIMIT 1);
SET @seeded_key = (SELECT `pool_key` FROM `user_pair_volumes` WHERE `pool_key` NOT LIKE 'starter:%' ORDER BY `id` LIMIT 1);
SET @introduced_at = COALESCE((SELECT MIN(`created_at`) FROM `user_pair_volumes`), NOW());

UPDATE `package_pairings` pp
JOIN `packages` other ON other.`id` = pp.`paired_package_id`
SET pp.`package_part` = IF(
    IF(@seeded_key IS NOT NULL, FIND_IN_SET(other.`id`, @seeded_key) > 0, other.`created_at` IS NULL OR other.`created_at` < @introduced_at),
    'repeat', 'first')
WHERE pp.`package_id` = @starter_id;

UPDATE `package_pairings` pp
JOIN `packages` other ON other.`id` = pp.`package_id`
SET pp.`paired_part` = IF(
    IF(@seeded_key IS NOT NULL, FIND_IN_SET(other.`id`, @seeded_key) > 0, other.`created_at` IS NULL OR other.`created_at` < @introduced_at),
    'repeat', 'first')
WHERE pp.`paired_package_id` = @starter_id;

-- 3. Volume already used for pair income, per user / leg / package ---------
--    (filled per user by the app from user_pair_volumes when first needed)
CREATE TABLE `user_pair_consumptions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `leg` VARCHAR(5) NOT NULL,
    `class_key` VARCHAR(30) NOT NULL,
    `consumed_volume` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_pair_consumptions_user_id_leg_class_key_unique` (`user_id`, `leg`, `class_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Mark the Laravel migration as run so `artisan migrate` skips it --------
INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_10_01_000000_add_registration_fee_and_starter_pairing_parts', COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`;
