-- Migration 033: Add renewal pricing fields to membership plans
-- Allows plans to have a separate renewal price and policy for grandfathering existing members

-- 1. Add renewal pricing to membership_plans
ALTER TABLE `membership_plans`
    ADD COLUMN `renewal_price` DECIMAL(10,2) DEFAULT NULL COMMENT 'Price on renewal - NULL means same as plan price' AFTER `price`,
    ADD COLUMN `renewal_price_policy` ENUM('current_price','locked_price') NOT NULL DEFAULT 'current_price'
        COMMENT 'current_price = always charge latest plan price on renewal, locked_price = keep original signup price' AFTER `renewal_type`;

-- 2. Add locked price and renewal tracking to player_memberships
ALTER TABLE `player_memberships`
    ADD COLUMN `locked_price` DECIMAL(10,2) DEFAULT NULL COMMENT 'The price locked at signup for grandfathered members' AFTER `amount_paid`,
    ADD COLUMN `renewal_count` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Number of times this membership has been renewed' AFTER `locked_price`,
    ADD COLUMN `auto_renew` TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Whether this membership auto-renews' AFTER `renewal_count`;
