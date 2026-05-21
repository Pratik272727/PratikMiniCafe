-- ================================================================
--  Mini Cafe — Complete Order + Reservation Database
--  Import in XAMPP > phpMyAdmin > Import
-- ================================================================

CREATE DATABASE IF NOT EXISTS `koppee_db`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `koppee_db`;

-- ----------------------------------------------------------------
-- RESERVATIONS (table booking details)
-- ----------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `reservations`;

CREATE TABLE `reservations` (
    `reservation_id`    INT           NOT NULL AUTO_INCREMENT,
    `customer_name`     VARCHAR(150)  NOT NULL,
    `email`             VARCHAR(200)  NOT NULL,
    `phone`             VARCHAR(20)   DEFAULT NULL,
    `reservation_date`  DATE          NOT NULL,
    `reservation_time`  TIME          NOT NULL,
    `persons`           INT           NOT NULL DEFAULT 1,
    `special_request`   TEXT          DEFAULT NULL,
    `discount_pct`      DECIMAL(5,2)  NOT NULL DEFAULT 30.00,
    `status`            ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    `created_at`        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`reservation_id`),
    INDEX `idx_date`  (`reservation_date`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- ORDERS (one order per checkout)
-- ----------------------------------------------------------------
CREATE TABLE `orders` (
    `order_id`          INT            NOT NULL AUTO_INCREMENT,
    `reservation_id`    INT            DEFAULT NULL,
    `customer_name`     VARCHAR(150)   NOT NULL,
    `email`             VARCHAR(200)   NOT NULL,
    `phone`             VARCHAR(20)    DEFAULT NULL,
    `reservation_date`  DATE           DEFAULT NULL,
    `reservation_time`  TIME           DEFAULT NULL,
    `persons`           INT            DEFAULT 1,
    `special_request`   TEXT           DEFAULT NULL,
    `subtotal`          DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `discount_pct`      DECIMAL(5,2)   NOT NULL DEFAULT 30.00,
    `discount_amount`   DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `total_amount`      DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `payment_method`    ENUM('card','upi','cash') NOT NULL DEFAULT 'card',
    `payment_status`    ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
    `payment_ref`       VARCHAR(50)    DEFAULT NULL,
    `order_status`      ENUM('placed','preparing','ready','delivered','cancelled') NOT NULL DEFAULT 'placed',
    `created_at`        TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`order_id`),
    FOREIGN KEY (`reservation_id`) REFERENCES `reservations`(`reservation_id`) ON DELETE SET NULL,
    INDEX `idx_email`   (`email`),
    INDEX `idx_status`  (`order_status`),
    INDEX `idx_payment` (`payment_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------------
-- ORDER ITEMS (line items per order)
-- ----------------------------------------------------------------
CREATE TABLE `order_items` (
    `item_id`       INT            NOT NULL AUTO_INCREMENT,
    `order_id`      INT            NOT NULL,
    `category`      VARCHAR(100)   NOT NULL,
    `item_name`     VARCHAR(150)   NOT NULL,
    `unit_price`    DECIMAL(10,2)  NOT NULL,
    `quantity`      INT            NOT NULL DEFAULT 1,
    `line_total`    DECIMAL(10,2)  NOT NULL,
    PRIMARY KEY (`item_id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`) ON DELETE CASCADE,
    INDEX `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ================================================================
--  DONE — Tables: reservations, orders, order_items | DB: koppee_db
-- ================================================================
