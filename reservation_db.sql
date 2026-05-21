-- =============================================================
--  Mini Cafe — RESERVATION DATABASE ONLY
--  Import in XAMPP > phpMyAdmin
-- =============================================================

CREATE DATABASE IF NOT EXISTS `koppee_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `koppee_db`;

-- Drop if exists so reimport works cleanly
DROP TABLE IF EXISTS `reservations`;

CREATE TABLE `reservations` (
    `reservation_id`    INT           NOT NULL AUTO_INCREMENT,
    `customer_name`     VARCHAR(150)  NOT NULL,
    `email`             VARCHAR(200)  NOT NULL,
    `phone`             VARCHAR(20)   DEFAULT NULL,
    `reservation_date`  DATE          NOT NULL,
    `reservation_time`  TIME          NOT NULL,
    `persons`           INT           NOT NULL DEFAULT 1,
    `discount`          DECIMAL(5,2)  NOT NULL DEFAULT 30.00 COMMENT '30% online reservation discount',
    `status`            ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    `created_at`        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`reservation_id`),
    INDEX `idx_date`   (`reservation_date`),
    INDEX `idx_email`  (`email`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Stores all table reservation bookings';

-- Sample data (3 demo bookings)
INSERT INTO `reservations` (`customer_name`,`email`,`phone`,`reservation_date`,`reservation_time`,`persons`,`discount`,`status`) VALUES
('Pranav Dagade',  'pranav@example.com',  '+91 8855039800', '2026-03-25', '19:00:00', 2, 30.00, 'confirmed'),
('Riya Sharma',    'riya@example.com',    '+91 9876543210', '2026-03-26', '13:30:00', 3, 30.00, 'pending'),
('Amit Kulkarni',  'amit@example.com',    '+91 9000011111', '2026-03-28', '20:00:00', 4, 30.00, 'pending');

-- =============================================================
--  DONE. Table: reservations | DB: koppee_db
-- =============================================================
