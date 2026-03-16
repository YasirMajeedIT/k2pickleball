-- Remove user columns that are no longer used in add/edit forms
ALTER TABLE `users`
    DROP COLUMN IF EXISTS `dupr_id`,
    DROP COLUMN IF EXISTS `timezone`,
    DROP COLUMN IF EXISTS `locale`;
