<?php
/**
 * process_reservation.php — AJAX handler for table reservation form
 * Always returns JSON. Never outputs HTML.
 */
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');

// ── DB ────────────────────────────────────────────────────────
$conn = new mysqli("localhost", "root", "", "koppee_db");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}
$conn->set_charset("utf8mb4");

// ── Create table if needed ────────────────────────────────────
$conn->query("CREATE TABLE IF NOT EXISTS `reservations` (
    `reservation_id`   INT          NOT NULL AUTO_INCREMENT,
    `customer_name`    VARCHAR(150) NOT NULL,
    `email`            VARCHAR(200) NOT NULL,
    `phone`            VARCHAR(20)  DEFAULT NULL,
    `reservation_date` DATE         NOT NULL,
    `reservation_time` TIME         NOT NULL,
    `persons`          INT          NOT NULL DEFAULT 1,
    `special_request`  TEXT         DEFAULT NULL,
    `discount_pct`     DECIMAL(5,2) NOT NULL DEFAULT 30.00,
    `status`           ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    `created_at`       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`reservation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ── Only POST ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

// ── Inputs ────────────────────────────────────────────────────
$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$phone   = trim($_POST['phone']   ?? '');
$date    = trim($_POST['date']    ?? '');
$time    = trim($_POST['time']    ?? '');
$persons = intval($_POST['persons'] ?? 0);
$special = trim($_POST['special'] ?? '');

// ── Validate ──────────────────────────────────────────────────
if (!$name)
    { echo json_encode(["success"=>false,"message"=>"Name is required."]); exit; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    { echo json_encode(["success"=>false,"message"=>"A valid email is required."]); exit; }
if (!$date)
    { echo json_encode(["success"=>false,"message"=>"Please select a date."]); exit; }
if (!$time)
    { echo json_encode(["success"=>false,"message"=>"Please select a time."]); exit; }
if ($persons < 1)
    { echo json_encode(["success"=>false,"message"=>"Please select number of persons."]); exit; }

// ── Format time safely for MySQL ──────────────────────────────
// <input type="time"> sends "HH:MM" — append seconds for MySQL TIME
if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
    $time_sql = $time . ':00';
} elseif (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $time)) {
    $time_sql = $time;
} else {
    $time_sql = '12:00:00'; // safe fallback
}

// ── Insert ────────────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO reservations (customer_name, email, phone, reservation_date, reservation_time, persons, special_request)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "DB error: " . $conn->error]);
    exit;
}

// Types: s=string s=string s=string s=string s=string i=int s=string
$stmt->bind_param("sssssis", $name, $email, $phone, $date, $time_sql, $persons, $special);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    $stmt->close(); $conn->close();
    echo json_encode([
        "success"    => true,
        "booking_id" => $id,
        "discount"   => "30%",
        "message"    => "Table booked successfully!"
    ]);
} else {
    $err = $stmt->error;
    $stmt->close(); $conn->close();
    echo json_encode(["success" => false, "message" => "Booking failed. " . $err]);
}
