-- ================================================================
--  Mini Cafe — Users / Auth Table
--  Add to koppee_db (import in phpMyAdmin)
-- ================================================================
USE `koppee_db`;

CREATE TABLE IF NOT EXISTS `users` (
    `user_id`      INT           NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(150)  NOT NULL,
    `email`        VARCHAR(200)  NOT NULL UNIQUE,
    `password`     VARCHAR(255)  NOT NULL COMMENT 'bcrypt hash',
    `phone`        VARCHAR(20)   DEFAULT NULL,
    `avatar`       VARCHAR(10)   NOT NULL DEFAULT '☕' COMMENT 'emoji avatar',
    `created_at`   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `last_login`   TIMESTAMP     NULL DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Also add user_id FK to orders table (if not already linked)
ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `user_id` INT DEFAULT NULL AFTER `order_id`;
