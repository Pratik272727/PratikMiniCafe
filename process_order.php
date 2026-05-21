<?php
session_start();
// process_order.php — Fake payment + order backend
header('Content-Type: application/json');
// Turn off error display so we never output HTML before JSON
error_reporting(0);
ini_set('display_errors', 0);

$db_host = "localhost"; $db_user = "root"; $db_pass = ""; $db_name = "koppee_db";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    echo json_encode(["success"=>false,"message"=>"DB connection failed: ".$conn->connect_error]);
    exit;
}
$conn->set_charset("utf8mb4");

// Auto-create tables if not exist
$conn->query("CREATE TABLE IF NOT EXISTS `reservations` (
    `reservation_id` INT NOT NULL AUTO_INCREMENT,
    `customer_name`  VARCHAR(150) NOT NULL,
    `email`          VARCHAR(200) NOT NULL,
    `phone`          VARCHAR(20)  DEFAULT NULL,
    `reservation_date` DATE NOT NULL,
    `reservation_time` TIME NOT NULL,
    `persons`        INT  NOT NULL DEFAULT 1,
    `special_request` TEXT DEFAULT NULL,
    `discount_pct`   DECIMAL(5,2) NOT NULL DEFAULT 30.00,
    `status`         ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`reservation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS `orders` (
    `order_id`        INT NOT NULL AUTO_INCREMENT,
    `reservation_id`  INT DEFAULT NULL,
    `customer_name`   VARCHAR(150) NOT NULL,
    `email`           VARCHAR(200) NOT NULL,
    `phone`           VARCHAR(20)  DEFAULT NULL,
    `reservation_date` DATE        DEFAULT NULL,
    `reservation_time` TIME        DEFAULT NULL,
    `persons`         INT          DEFAULT 1,
    `special_request` TEXT         DEFAULT NULL,
    `subtotal`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `discount_pct`    DECIMAL(5,2)  NOT NULL DEFAULT 30.00,
    `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `total_amount`    DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `payment_method`  ENUM('card','upi','cash') NOT NULL DEFAULT 'card',
    `payment_status`  ENUM('pending','paid','failed') NOT NULL DEFAULT 'pending',
    `payment_ref`     VARCHAR(50)   DEFAULT NULL,
    `order_status`    ENUM('placed','preparing','ready','delivered','cancelled') NOT NULL DEFAULT 'placed',
    `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS `order_items` (
    `item_id`    INT NOT NULL AUTO_INCREMENT,
    `order_id`   INT NOT NULL,
    `category`   VARCHAR(100) NOT NULL,
    `item_name`  VARCHAR(150) NOT NULL,
    `unit_price` DECIMAL(10,2) NOT NULL,
    `quantity`   INT NOT NULL DEFAULT 1,
    `line_total` DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (`item_id`),
    KEY `idx_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success"=>false,"message"=>"Invalid request method."]);
    exit;
}

// ── Read inputs ──────────────────────────────────────────────
$name       = trim($_POST['name']             ?? '');
$email      = trim($_POST['email']            ?? '');
$phone      = trim($_POST['phone']            ?? '');
$res_date   = trim($_POST['reservation_date'] ?? '');
$res_time   = trim($_POST['reservation_time'] ?? '');
$persons    = intval($_POST['persons']         ?? 0);
$special    = trim($_POST['special_request']  ?? '');
$pay_method = trim($_POST['payment_method']   ?? 'card');
$items_json = $_POST['items']                 ?? '[]';

if (!in_array($pay_method, ['card','upi','cash'])) $pay_method = 'card';

$items = json_decode($items_json, true);

// ── Validate ─────────────────────────────────────────────────
if (empty($name))
    { echo json_encode(["success"=>false,"message"=>"Name is required."]); exit; }
if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    { echo json_encode(["success"=>false,"message"=>"Valid email is required."]); exit; }
if (empty($res_date))
    { echo json_encode(["success"=>false,"message"=>"Reservation date is required."]); exit; }
if (empty($res_time))
    { echo json_encode(["success"=>false,"message"=>"Reservation time is required."]); exit; }
if ($persons < 1)
    { echo json_encode(["success"=>false,"message"=>"Number of persons is required."]); exit; }
if (empty($items) || !is_array($items) || count($items) === 0)
    { echo json_encode(["success"=>false,"message"=>"No items selected."]); exit; }

// ── Capture logged-in user ──────────────────────────────────
$user_id = isset($_SESSION['user']['id']) ? intval($_SESSION['user']['id']) : 0;

// ── Calculate totals ──────────────────────────────────────────
$subtotal = 0.0;
foreach ($items as $it) {
    $subtotal += floatval($it['price'] ?? 0) * intval($it['qty'] ?? 1);
}
$discount_amount = round($subtotal * 0.30, 2);
$total           = round($subtotal - $discount_amount, 2);

// ── Fake payment — always succeeds ───────────────────────────
$pay_ref    = strtoupper(substr($pay_method, 0, 3)) . '-' . date('Ymd') . '-' . rand(100000, 999999);
$pay_status = 'paid';

// ── Convert time ─────────────────────────────────────────────
$time_24 = date("H:i:s", strtotime($res_time));

// ── Insert reservation ────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO reservations (customer_name, email, phone, reservation_date, reservation_time, persons, special_request)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssssds", $name, $email, $phone, $res_date, $time_24, $persons, $special);
// persons is int — use correct type
$stmt->close();

$stmt = $conn->prepare(
    "INSERT INTO reservations (customer_name, email, phone, reservation_date, reservation_time, persons, special_request)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssssis", $name, $email, $phone, $res_date, $time_24, $persons, $special);
$stmt->execute();
$res_id = $conn->insert_id;
$stmt->close();

// ── Insert order ──────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO orders (user_id, reservation_id, customer_name, email, phone, reservation_date, reservation_time,
      persons, special_request, subtotal, discount_pct, discount_amount, total_amount,
      payment_method, payment_status, payment_ref)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 30, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    "iisssssissdddss",
    $user_id, $res_id, $name, $email, $phone, $res_date, $time_24,
    $persons, $special, $subtotal, $discount_amount, $total,
    $pay_method, $pay_status, $pay_ref
);
$stmt->execute();
$order_id = $conn->insert_id;
$stmt->close();

// ── Insert order items ────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO order_items (order_id, category, item_name, unit_price, quantity, line_total)
     VALUES (?, ?, ?, ?, ?, ?)"
);
foreach ($items as $it) {
    $cat   = strval($it['category'] ?? '');
    $iname = strval($it['name']     ?? '');
    $price = floatval($it['price']  ?? 0);
    $qty   = intval($it['qty']      ?? 1);
    $line  = round($price * $qty, 2);
    $stmt->bind_param("issdid", $order_id, $cat, $iname, $price, $qty, $line);
    $stmt->execute();
}
$stmt->close();
$conn->close();

echo json_encode([
    "success"     => true,
    "order_id"    => $order_id,
    "res_id"      => $res_id,
    "payment_ref" => $pay_ref,
    "pay_method"  => $pay_method,
    "subtotal"    => $subtotal,
    "discount"    => $discount_amount,
    "total"       => $total,
    "name"        => $name,
    "email"       => $email,
    "phone"       => $phone,
    "res_date"    => $res_date,
    "res_time"    => $res_time,
    "persons"     => $persons,
    "special"     => $special,
    "items"       => $items,
    "message"     => "Order placed successfully!"
]);
