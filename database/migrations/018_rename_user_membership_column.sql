-- Rename legacy sports-specific users column to generic membership naming
SET @has_old_col := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'usapa_member_id'
);

SET @has_new_col := (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'users'
      AND COLUMN_NAME = 'membership_id'
);

SET @sql := IF(
    @has_old_col > 0 AND @has_new_col = 0,
    'ALTER TABLE `users` CHANGE COLUMN `usapa_member_id` `membership_id` VARCHAR(50) DEFAULT NULL',
    'SELECT 1'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
