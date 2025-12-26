-- Manual SQL to add stripe_account_id to athletes table
-- Run this in production if the migration keeps failing

-- Check if column exists first, then add it
ALTER TABLE `athletes` 
ADD COLUMN `stripe_account_id` VARCHAR(255) NULL 
AFTER `email`;

