<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php?auth=login"); exit;
}
$user = $_SESSION['user'];

// ── DB ──────────────────────────────────────────────────────
$db = new mysqli("localhost", "root", "", "koppee_db");
if ($db->connect_error) die("DB error: " . $db->connect_error);
$db->set_charset("utf8mb4");

// Ensure user_id column exists on orders
$db->query("ALTER TABLE `orders` ADD COLUMN IF NOT EXISTS `user_id` INT DEFAULT NULL AFTER `order_id`");

$uid       = intval($user['id']);
$email_esc = $db->real_escape_string($user['email']);

// ── Fetch user profile ──────────────────────────────────────
$ur = $db->query("SELECT * FROM users WHERE user_id=$uid");
$urow = ($ur && $ur->num_rows) ? $ur->fetch_assoc() : [
    'user_id'    => $uid,
    'name'       => $user['name'],
    'email'      => $user['email'],
    'phone'      => '',
    'avatar'     => $user['avatar'],
    'created_at' => date('Y-m-d H:i:s'),
    'last_login' => null,
];

// ── Handle account update (POST) ───────────────────────────
$update_msg = '';
$update_ok  = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_name  = trim($_POST['new_name']  ?? '');
    $new_phone = trim($_POST['new_phone'] ?? '');
    $new_pass  = $_POST['new_password']   ?? '';
    $cur_pass  = $_POST['current_password'] ?? '';

    if (strlen($new_name) < 2) {
        $update_msg = 'Name must be at least 2 characters.';
    } else {
        $stmt = $db->prepare("UPDATE users SET name=?, phone=? WHERE user_id=?");
        $stmt->bind_param("ssi", $new_name, $new_phone, $uid);
        $stmt->execute(); $stmt->close();

        // Change password if requested
        if (!empty($new_pass)) {
            if (strlen($new_pass) < 6) {
                $update_msg = 'New password must be at least 6 characters.';
            } else {
                // Verify current password
                $pr = $db->prepare("SELECT password FROM users WHERE user_id=?");
                $pr->bind_param("i", $uid); $pr->execute();
                $pr->bind_result($hash); $pr->fetch(); $pr->close();
                if (!password_verify($cur_pass, $hash)) {
                    $update_msg = 'Current password is incorrect.';
                } else {
                    $new_hash = password_hash($new_pass, PASSWORD_BCRYPT);
                    $upw = $db->prepare("UPDATE users SET password=? WHERE user_id=?");
                    $upw->bind_param("si", $new_hash, $uid); $upw->execute(); $upw->close();
                    $update_msg = 'Password updated successfully!';
                    $update_ok  = true;
                }
            }
        } else {
            $update_msg = 'Profile updated successfully!';
            $update_ok  = true;
        }

        // Refresh session name
        if ($update_ok) {
            $_SESSION['user']['name'] = $new_name;
            $urow['name']  = $new_name;
            $urow['phone'] = $new_phone;
        }
    }
}

// ── Fetch orders ────────────────────────────────────────────
$ord_res = $db->query(
    "SELECT * FROM orders WHERE email='$email_esc' OR user_id=$uid ORDER BY created_at DESC"
);
$orders = $ord_res ? $ord_res->fetch_all(MYSQLI_ASSOC) : [];

// ── Fetch items for each order (while $db is still open) ───
$order_items_map = [];
foreach ($orders as $ord) {
    $oid = intval($ord['order_id']);
    $ir  = $db->query("SELECT * FROM order_items WHERE order_id=$oid");
    $order_items_map[$oid] = $ir ? $ir->fetch_all(MYSQLI_ASSOC) : [];
}

// ── Stats ───────────────────────────────────────────────────
$total_orders   = count($orders);
$total_spent    = array_sum(array_column($orders, 'total_amount'));
$total_saved    = array_sum(array_column($orders, 'discount_amount'));
$fav_method_arr = array_count_values(array_column($orders, 'payment_method'));
arsort($fav_method_arr);
$fav_method = ucfirst(array_key_first($fav_method_arr) ?? 'Card');

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>My Profile — Mini Cafe</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<link href="img/favicon.ico" rel="icon">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
<link href="css/style.min.css" rel="stylesheet">
<style>
:root{
  --pri:#d4a017;--pri-d:#b8860b;--bg:#100801;--card:#1c0d03;
  --brd:rgba(212,160,23,.22);--brdl:rgba(255,255,255,.08);
  --txt:#f5ede0;--muted:#9a7850;--ok:#28a745;--err:#dc3545;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Roboto',sans-serif;background:var(--bg);color:var(--txt);min-height:100vh;}

/* ── Hero ── */
.hero{background:linear-gradient(160deg,#070300,#1a0b02);padding:110px 0 0;position:relative;overflow:hidden;}
.hero::before{content:'';position:absolute;inset:0;background:url('img/bg.jpg') center/cover;opacity:.06;}
.hero-inner{position:relative;max-width:940px;margin:0 auto;padding:0 24px;display:flex;align-items:flex-end;gap:28px;flex-wrap:wrap;}
.avatar-wrap{position:relative;flex-shrink:0;}
.avatar-circle{width:96px;height:96px;background:linear-gradient(135deg,var(--pri),var(--pri-d));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:3rem;box-shadow:0 8px 32px rgba(212,160,23,.35);border:3px solid rgba(212,160,23,.4);}
.hero-info{flex:1;min-width:200px;padding-bottom:6px;}
.hero-name{font-family:'Playfair Display',serif;font-size:clamp(1.6rem,4vw,2.2rem);font-weight:800;color:#fff;}
.hero-email{color:var(--muted);font-size:.86rem;margin-top:3px;}
.hero-joined{color:var(--muted);font-size:.78rem;margin-top:4px;display:flex;align-items:center;gap:5px;}
.hero-joined i{color:var(--pri);font-size:.7rem;}
.stats-row{max-width:940px;margin:0 auto;padding:20px 24px 0;display:grid;grid-template-columns:repeat(4,1fr);gap:1px;background:var(--brdl);border-radius:0;}
@media(max-width:600px){.stats-row{grid-template-columns:repeat(2,1fr);}}
.stat-box{background:#110902;padding:18px 16px;text-align:center;}
.stat-box .sn{font-size:1.4rem;font-weight:700;color:var(--pri);}
.stat-box .sl{font-size:.7rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-top:3px;}

/* ── Tab bar ── */
.tab-bar{background:rgba(5,2,0,.9);border-bottom:1px solid var(--brdl);backdrop-filter:blur(10px);position:sticky;top:0;z-index:100;}
.tab-inner{display:flex;max-width:940px;margin:0 auto;padding:0 24px;gap:4px;}
.tab-btn{padding:15px 18px;font-size:.86rem;font-weight:600;color:var(--muted);border:none;background:transparent;cursor:pointer;border-bottom:2px solid transparent;transition:all .2s;font-family:'Roboto',sans-serif;white-space:nowrap;}
.tab-btn:hover{color:var(--pri);}
.tab-btn.on{color:var(--pri);border-bottom-color:var(--pri);}

/* ── Main content ── */
.prof-main{max-width:940px;margin:32px auto 80px;padding:0 24px;}
.tab-pane{display:none;animation:fadeIn .3s ease;}
.tab-pane.on{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px);}to{opacity:1;transform:translateY(0);}}
.sec-h{margin-bottom:20px;}
.sec-h h2{font-family:'Playfair Display',serif;font-size:1.45rem;color:#fff;}
.sec-h p{color:var(--muted);font-size:.84rem;margin-top:4px;}

/* ── Order cards ── */
.order-card{background:var(--card);border:1px solid var(--brdl);border-radius:12px;margin-bottom:14px;overflow:hidden;transition:border-color .2s;}
.order-card:hover{border-color:var(--brd);}
.ord-header{display:flex;align-items:center;gap:12px;padding:16px 20px;cursor:pointer;flex-wrap:wrap;}
.ord-num{font-size:.75rem;color:var(--muted);font-weight:600;white-space:nowrap;}
.ord-num span{color:var(--pri);font-size:.92rem;}
.ord-date{font-size:.8rem;color:var(--muted);flex:1;}
.ord-total{font-size:.98rem;font-weight:700;color:var(--pri);white-space:nowrap;}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:50px;font-size:.7rem;font-weight:600;}
.b-paid{background:rgba(40,167,69,.15);color:#7ddba4;}
.b-pending{background:rgba(212,160,23,.15);color:#e8b45a;}
.b-failed{background:rgba(220,53,69,.15);color:#f08080;}
.b-placed{background:rgba(100,149,237,.15);color:#90b0f8;}
.b-preparing{background:rgba(255,165,0,.15);color:#ffc875;}
.b-ready{background:rgba(40,167,69,.15);color:#7ddba4;}
.b-delivered{background:rgba(180,180,180,.1);color:#aaa;}
.b-cancelled{background:rgba(220,53,69,.15);color:#f08080;}
.chev{color:var(--muted);font-size:.78rem;transition:transform .25s;flex-shrink:0;}
.ord-body{display:none;padding:16px 20px 18px;border-top:1px solid var(--brdl);}
.ord-body.open{display:block;}
.ord-meta{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px;}
@media(max-width:500px){.ord-meta{grid-template-columns:1fr 1fr;}}
.om-cell{background:rgba(255,255,255,.03);border-radius:8px;padding:10px 12px;}
.om-lbl{font-size:.68rem;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);}
.om-val{font-size:.86rem;font-weight:600;color:#fff;margin-top:2px;}
.items-tbl{width:100%;border-collapse:collapse;margin-bottom:14px;}
.items-tbl th{font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);padding:6px 0;border-bottom:1px solid var(--brdl);text-align:left;}
.items-tbl th:last-child,.items-tbl td:last-child{text-align:right;}
.items-tbl td{font-size:.85rem;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04);color:var(--txt);}
.price-summary{background:rgba(0,0,0,.2);border-radius:9px;padding:14px 16px;margin-bottom:14px;}
.ps-row{display:flex;justify-content:space-between;font-size:.84rem;padding:4px 0;}
.ps-row .pl{color:var(--muted);}
.ps-row .pr{color:#fff;}
.ps-row.disc .pr{color:#5adc85;}
.ps-row.tot{font-weight:700;font-size:.94rem;padding-top:10px;border-top:1px solid var(--brdl);margin-top:6px;}
.ps-row.tot .pr{color:var(--pri);}
.ord-actions{display:flex;gap:10px;flex-wrap:wrap;}
.btn-sm{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;border:none;font-family:'Roboto',sans-serif;transition:all .18s;text-decoration:none;}
.btn-gold{background:linear-gradient(135deg,var(--pri),var(--pri-d));color:#1a0a02;}
.btn-gold:hover{opacity:.87;}
.btn-ghost{background:transparent;border:1px solid var(--brdl);color:var(--muted);}
.btn-ghost:hover{border-color:var(--pri);color:var(--pri);}
.btn-danger-ghost{background:transparent;border:1px solid rgba(220,53,69,.35);color:#f08080;}
.btn-danger-ghost:hover{background:rgba(220,53,69,.1);border-color:var(--err);}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted);}
.empty-state i{font-size:3rem;display:block;margin-bottom:14px;opacity:.3;}
.empty-state a{display:inline-block;margin-top:16px;padding:11px 26px;background:linear-gradient(135deg,var(--pri),var(--pri-d));color:#1a0a02;border-radius:8px;font-weight:700;text-decoration:none;font-size:.9rem;}

/* ── Settings / Account ── */
.settings-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
@media(max-width:720px){.settings-grid{grid-template-columns:1fr;}}
.s-card{background:var(--card);border:1px solid var(--brdl);border-radius:12px;padding:22px;}
.s-card.full{grid-column:1/-1;}
.s-card-h{display:flex;align-items:center;gap:10px;margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid var(--brdl);}
.s-card-h i{width:32px;height:32px;background:rgba(212,160,23,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--pri);font-size:.85rem;flex-shrink:0;}
.s-card-h h3{font-size:.98rem;color:#fff;font-weight:600;}
.info-pair{display:flex;flex-direction:column;gap:3px;margin-bottom:14px;}
.info-pair:last-child{margin-bottom:0;}
.ip-lbl{font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);}
.ip-val{font-size:.9rem;font-weight:500;color:#fff;}
/* form fields */
.sf{display:flex;flex-direction:column;gap:5px;margin-bottom:14px;}
.sf label{font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);font-weight:600;}
.sf label .r{color:var(--pri);}
.sf input{background:rgba(255,255,255,.05);border:1px solid var(--brdl);border-radius:9px;color:#fff;padding:11px 14px;font-size:.9rem;outline:none;transition:border-color .2s;font-family:'Roboto',sans-serif;}
.sf input:focus{border-color:var(--pri);box-shadow:0 0 0 3px rgba(212,160,23,.1);}
.sf input::placeholder{color:rgba(245,237,224,.25);}
.alert-box{padding:11px 14px;border-radius:8px;font-size:.85rem;font-weight:500;margin-bottom:16px;}
.alert-ok{background:rgba(40,167,69,.12);border:1px solid rgba(40,167,69,.3);color:#7ddba4;}
.alert-err{background:rgba(220,53,69,.1);border:1px solid rgba(220,53,69,.3);color:#f08080;}
/* activity timeline */
.timeline{display:flex;flex-direction:column;gap:0;}
.tl-item{display:flex;gap:14px;padding:12px 0;border-bottom:1px solid var(--brdl);}
.tl-item:last-child{border-bottom:none;}
.tl-dot{width:30px;height:30px;border-radius:50%;background:rgba(212,160,23,.12);border:1px solid var(--brd);display:flex;align-items:center;justify-content:center;color:var(--pri);font-size:.75rem;flex-shrink:0;margin-top:2px;}
.tl-text{flex:1;}
.tl-title{font-size:.86rem;font-weight:600;color:#fff;}
.tl-sub{font-size:.78rem;color:var(--muted);margin-top:2px;}
/* toast */
#toast{display:none;position:fixed;bottom:22px;right:22px;z-index:9999;padding:12px 18px;border-radius:8px;font-weight:600;font-size:.88rem;box-shadow:0 6px 20px rgba(0,0,0,.35);background:var(--ok);color:#fff;}
#toast.err{background:var(--err);}
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="container-fluid p-0 nav-bar">
  <nav class="navbar navbar-expand-lg bg-none navbar-dark py-3">
    <a href="index.php" class="navbar-brand px-lg-4 m-0"><h1 class="m-0 display-4 text-uppercase text-white">Mini Cafe</h1></a>
    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
      <div class="navbar-nav ml-auto p-4">
        <a href="index.php" class="nav-item nav-link">Home</a>
        <a href="menu.php" class="nav-item nav-link">Menu</a>
        <a href="order.php" class="nav-item nav-link">Order Online</a>
        <a href="reservation.php" class="nav-item nav-link">Reservation</a>
        <a href="profile.php" class="nav-item nav-link active" style="color:#d4a017;">
            <?=htmlspecialchars($urow['avatar'])?> <?=htmlspecialchars($urow['name'])?>
        </a>
      </div>
    </div>
  </nav>
</div>

<div id="toast"></div>

<!-- HERO BANNER -->
<div class="hero">
  <div class="hero-inner">
    <div class="avatar-wrap">
      <div class="avatar-circle"><?=htmlspecialchars($urow['avatar'])?></div>
    </div>
    <div class="hero-info">
      <div class="hero-name"><?=htmlspecialchars($urow['name'])?></div>
      <div class="hero-email"><?=htmlspecialchars($urow['email'])?></div>
      <div class="hero-joined"><i class="fa fa-calendar-alt"></i> Member since <?=date('F Y', strtotime($urow['created_at']))?></div>
    </div>
  </div>
  <div class="stats-row">
    <div class="stat-box"><div class="sn"><?=$total_orders?></div><div class="sl">Orders</div></div>
    <div class="stat-box"><div class="sn">₹<?=number_format($total_spent,0)?></div><div class="sl">Total Spent</div></div>
    <div class="stat-box"><div class="sn" style="color:#5adc85;">₹<?=number_format($total_saved,0)?></div><div class="sl">Total Saved</div></div>
    <div class="stat-box"><div class="sn" style="font-size:1rem;"><?=$fav_method?></div><div class="sl">Fav Payment</div></div>
  </div>
  <!-- TABS -->
  <div class="tab-bar">
    <div class="tab-inner">
      <button class="tab-btn on"   onclick="switchTab('orders',this)"><i class="fa fa-receipt mr-2"></i>My Orders</button>
      <button class="tab-btn"      onclick="switchTab('account',this)"><i class="fa fa-user-edit mr-2"></i>Account Settings</button>
      <button class="tab-btn"      onclick="switchTab('activity',this)"><i class="fa fa-clock mr-2"></i>Activity</button>
    </div>
  </div>
</div>

<!-- ── MAIN CONTENT ── -->
<div class="prof-main">

<!-- ══════════ ORDERS TAB ══════════ -->
<div class="tab-pane on" id="tab-orders">
  <div class="sec-h">
    <h2>Order History</h2>
    <p>Click any order to expand details and download the receipt.</p>
  </div>

  <?php if (empty($orders)): ?>
  <div class="empty-state">
    <i class="fa fa-shopping-bag"></i>
    <p>No orders yet. Start ordering your favourites!</p>
    <a href="order.php"><i class="fa fa-utensils mr-2"></i>Order Now</a>
  </div>
  <?php else: foreach ($orders as $ord):
    $oid   = intval($ord['order_id']);
    $items = $order_items_map[$oid] ?? [];
    $payLabel = ['card'=>'Card','upi'=>'UPI','cash'=>'Pay at Cafe'][$ord['payment_method']] ?? ucfirst($ord['payment_method']);
    $dateStr  = $ord['reservation_date'] ? date('d M Y', strtotime($ord['reservation_date'])) : '-';
    $timeStr  = $ord['reservation_time'] ? date('h:i A', strtotime($ord['reservation_time'])) : '-';
  ?>
  <div class="order-card">
    <div class="ord-header" onclick="toggleOrd(<?=$oid?>)">
      <div>
        <div class="ord-num">Order <span>#<?=$oid?></span></div>
        <div class="ord-date"><?=date('d M Y, h:i A', strtotime($ord['created_at']))?></div>
      </div>
      <span class="badge b-<?=$ord['payment_status']??'paid'?>"><?=ucfirst($ord['payment_status']??'paid')?></span>
      <span class="badge b-<?=$ord['order_status']?>"><?=ucfirst($ord['order_status'])?></span>
      <div class="ord-total">₹<?=number_format($ord['total_amount'],0)?></div>
      <i class="fa fa-chevron-down chev" id="chev_<?=$oid?>"></i>
    </div>
    <div class="ord-body" id="ob_<?=$oid?>">
      <!-- Meta grid -->
      <div class="ord-meta">
        <div class="om-cell"><div class="om-lbl">Date</div><div class="om-val"><?=$dateStr?></div></div>
        <div class="om-cell"><div class="om-lbl">Time</div><div class="om-val"><?=$timeStr?></div></div>
        <div class="om-cell"><div class="om-lbl">Persons</div><div class="om-val"><?=$ord['persons']??'-'?></div></div>
        <div class="om-cell"><div class="om-lbl">Payment</div><div class="om-val"><?=$payLabel?></div></div>
        <div class="om-cell"><div class="om-lbl">Ref</div><div class="om-val" style="font-size:.78rem;"><?=htmlspecialchars($ord['payment_ref']??'-')?></div></div>
        <?php if(!empty($ord['special_request'])): ?>
        <div class="om-cell"><div class="om-lbl">Special Note</div><div class="om-val" style="font-size:.8rem;"><?=htmlspecialchars($ord['special_request'])?></div></div>
        <?php endif; ?>
      </div>
      <!-- Items -->
      <?php if (!empty($items)): ?>
      <table class="items-tbl">
        <thead><tr><th>Item</th><th>Category</th><th>Qty</th><th>Price</th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?>
          <tr>
            <td><?=htmlspecialchars($it['item_name'])?></td>
            <td style="color:var(--muted);"><?=htmlspecialchars($it['category'])?></td>
            <td>×<?=$it['quantity']?></td>
            <td>₹<?=number_format($it['line_total'],0)?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
      <!-- Price summary -->
      <div class="price-summary">
        <div class="ps-row"><span class="pl">Subtotal</span><span class="pr">₹<?=number_format($ord['subtotal'],0)?></span></div>
        <div class="ps-row disc"><span class="pl">Discount (30%)</span><span class="pr">-₹<?=number_format($ord['discount_amount'],0)?></span></div>
        <div class="ps-row tot"><span class="pl">Total Paid</span><span class="pr">₹<?=number_format($ord['total_amount'],0)?></span></div>
      </div>
      <!-- Actions -->
      <div class="ord-actions">
        <button class="btn-sm btn-gold" onclick='dlReceipt(<?=json_encode([
          "order_id"    => $oid,
          "payment_ref" => $ord["payment_ref"] ?? "",
          "name"        => $ord["customer_name"],
          "email"       => $ord["email"],
          "res_date"    => $ord["reservation_date"] ?? "",
          "res_time"    => $ord["reservation_time"] ?? "",
          "persons"     => $ord["persons"] ?? 1,
          "pay_method"  => $ord["payment_method"] ?? "card",
          "subtotal"    => floatval($ord["subtotal"]),
          "discount"    => floatval($ord["discount_amount"]),
          "total"       => floatval($ord["total_amount"]),
          "items"       => $items,
        ])?>)'>
          <i class="fa fa-download"></i> Receipt
        </button>
        <a href="order.php" class="btn-sm btn-ghost"><i class="fa fa-redo"></i> Reorder</a>
      </div>
    </div>
  </div>
  <?php endforeach; endif; ?>
</div>

<!-- ══════════ ACCOUNT SETTINGS TAB ══════════ -->
<div class="tab-pane" id="tab-account">
  <div class="sec-h"><h2>Account Settings</h2><p>Update your profile information and password.</p></div>

  <?php if ($update_msg): ?>
  <div class="alert-box <?=$update_ok?'alert-ok':'alert-err'?>">
    <i class="fa <?=$update_ok?'fa-check-circle':'fa-exclamation-circle'?> mr-2"></i><?=htmlspecialchars($update_msg)?>
  </div>
  <?php endif; ?>

  <div class="settings-grid">

    <!-- Profile Info (read-only display) -->
    <div class="s-card">
      <div class="s-card-h">
        <div class="s-card-h i"><i class="fa fa-id-card"></i></div>
        <h3>Profile Info</h3>
      </div>
      <div class="info-pair"><div class="ip-lbl">Full Name</div><div class="ip-val"><?=htmlspecialchars($urow['name'])?></div></div>
      <div class="info-pair"><div class="ip-lbl">Email Address</div><div class="ip-val"><?=htmlspecialchars($urow['email'])?></div></div>
      <div class="info-pair"><div class="ip-lbl">Phone</div><div class="ip-val"><?=htmlspecialchars($urow['phone']??'Not set')?></div></div>
      <div class="info-pair"><div class="ip-lbl">Avatar</div><div class="ip-val" style="font-size:1.4rem;"><?=htmlspecialchars($urow['avatar'])?></div></div>
      <div class="info-pair"><div class="ip-lbl">Member Since</div><div class="ip-val"><?=date('d M Y', strtotime($urow['created_at']))?></div></div>
      <?php if($urow['last_login']): ?>
      <div class="info-pair"><div class="ip-lbl">Last Login</div><div class="ip-val"><?=date('d M Y, h:i A', strtotime($urow['last_login']))?></div></div>
      <?php endif; ?>
    </div>

    <!-- Edit Profile -->
    <div class="s-card">
      <div class="s-card-h">
        <div class="s-card-h i"><i class="fa fa-user-edit"></i></div>
        <h3>Edit Profile</h3>
      </div>
      <form method="POST">
        <div class="sf"><label>Full Name <span class="r">*</span></label>
          <input type="text" name="new_name" value="<?=htmlspecialchars($urow['name'])?>" placeholder="Your full name" required></div>
        <div class="sf"><label>Phone</label>
          <input type="tel" name="new_phone" value="<?=htmlspecialchars($urow['phone']??'')?>" placeholder="+91 00000 00000"></div>
        <button type="submit" name="update_profile" class="btn-sm btn-gold" style="width:100%;justify-content:center;padding:12px;">
          <i class="fa fa-save"></i> Save Changes
        </button>
      </form>
    </div>

    <!-- Change Password -->
    <div class="s-card">
      <div class="s-card-h">
        <div class="s-card-h i"><i class="fa fa-lock"></i></div>
        <h3>Change Password</h3>
      </div>
      <form method="POST">
        <div class="sf"><label>Current Password</label>
          <input type="password" name="current_password" placeholder="Your current password"></div>
        <div class="sf"><label>New Password</label>
          <input type="password" name="new_password" placeholder="At least 6 characters"></div>
        <button type="submit" name="update_profile" class="btn-sm btn-ghost" style="width:100%;justify-content:center;padding:12px;">
          <i class="fa fa-key"></i> Update Password
        </button>
      </form>
    </div>

    <!-- Order Stats -->
    <div class="s-card">
      <div class="s-card-h">
        <div class="s-card-h i"><i class="fa fa-chart-bar"></i></div>
        <h3>Your Stats</h3>
      </div>
      <div class="info-pair"><div class="ip-lbl">Total Orders</div><div class="ip-val"><?=$total_orders?></div></div>
      <div class="info-pair"><div class="ip-lbl">Total Spent</div><div class="ip-val">₹<?=number_format($total_spent,2)?></div></div>
      <div class="info-pair"><div class="ip-lbl">Total Saved (30% disc.)</div><div class="ip-val" style="color:#5adc85;">₹<?=number_format($total_saved,2)?></div></div>
      <div class="info-pair"><div class="ip-lbl">Preferred Payment</div><div class="ip-val"><?=$fav_method?></div></div>
      <div class="info-pair"><div class="ip-lbl">Account Status</div><div class="ip-val"><span class="badge b-paid">Active ✓</span></div></div>
    </div>

    <!-- Danger Zone -->
    <div class="s-card full" style="border-color:rgba(220,53,69,.2);">
      <div class="s-card-h">
        <div class="s-card-h i" style="background:rgba(220,53,69,.1);"><i class="fa fa-exclamation-triangle" style="color:#f08080;"></i></div>
        <h3 style="color:#f08080;">Account Actions</h3>
      </div>
      <p style="color:var(--muted);font-size:.86rem;margin-bottom:16px;">Sign out of your account on this device.</p>
      <button class="btn-sm btn-danger-ghost" onclick="doLogout()"><i class="fa fa-sign-out-alt"></i> Sign Out</button>
    </div>

  </div>
</div>

<!-- ══════════ ACTIVITY TAB ══════════ -->
<div class="tab-pane" id="tab-activity">
  <div class="sec-h"><h2>Recent Activity</h2><p>Your latest actions and order updates.</p></div>
  <div class="timeline">
    <?php if (empty($orders)): ?>
    <div class="empty-state"><i class="fa fa-history"></i><p>No activity yet.</p></div>
    <?php else: foreach (array_slice($orders, 0, 10) as $ord):
      $oid = intval($ord['order_id']);
      $ago = (time() - strtotime($ord['created_at']));
      if($ago < 3600)      $when = round($ago/60).' min ago';
      elseif($ago < 86400) $when = round($ago/3600).' hr ago';
      else                 $when = date('d M Y', strtotime($ord['created_at']));
      $ic  = ['placed'=>'fa-shopping-bag','preparing'=>'fa-fire','ready'=>'fa-check','delivered'=>'fa-box','cancelled'=>'fa-times'];
      $icon = $ic[$ord['order_status']] ?? 'fa-circle';
      $icount = count($order_items_map[$oid] ?? []);
    ?>
    <div class="tl-item">
      <div class="tl-dot"><i class="fa <?=$icon?>"></i></div>
      <div class="tl-text">
        <div class="tl-title">Order #<?=$oid?> — ₹<?=number_format($ord['total_amount'],0)?> <span class="badge b-<?=$ord['order_status']?>" style="margin-left:6px;"><?=ucfirst($ord['order_status'])?></span></div>
        <div class="tl-sub"><?=$icount?> item<?=$icount!=1?'s':''?> · <?=ucfirst($ord['payment_method']??'card')?> · <?=$when?></div>
      </div>
      <button class="btn-sm btn-ghost" style="font-size:.76rem;padding:5px 12px;" onclick="toggleOrd(<?=$oid?>);switchTab('orders',document.querySelector('.tab-btn'))">
        View
      </button>
    </div>
    <?php endforeach; endif; ?>
  </div>
</div>

</div><!-- end prof-main -->

<!-- FOOTER -->
<div class="container-fluid footer text-white mt-5 pt-5 px-0 position-relative overlay-top">
  <div class="row mx-0 pt-5 px-sm-3 px-lg-5 mt-4">
    <div class="col-lg-3 col-md-6 mb-5" style="margin-left:160px;">
      <h4 class="text-white text-uppercase mb-4" style="letter-spacing:3px;">Get In Touch</h4>
      <p><i class="fa fa-map-marker-alt mr-2"></i>Bavdhan, Pune</p>
      <p><i class="fa fa-phone-alt mr-2"></i>+91 8855039800</p>
      <p class="m-0"><i class="fa fa-envelope mr-2"></i>dagadepranav21@gmail.com</p>
    </div>
    <div class="col-lg-3 col-md-6 mb-5">
      <h4 class="text-white text-uppercase mb-4" style="letter-spacing:3px;">Quick Links</h4>
      <a href="index.php" class="text-white d-block mb-2">Home</a>
      <a href="order.php" class="text-white d-block mb-2">Order Online</a>
      <a href="reservation.php" class="text-white d-block mb-2">Reservation</a>
      <a href="contact.php" class="text-white d-block">Contact</a>
    </div>
    <div class="col-lg-3 col-md-6 mb-5">
      <h4 class="text-white text-uppercase mb-4" style="letter-spacing:3px;">Open Hours</h4>
      <h6 class="text-white text-uppercase">Mon–Fri</h6><p>10:00 AM – 11:00 PM</p>
      <h6 class="text-white text-uppercase">Sat–Sun</h6><p>9:00 AM – 12:00 AM</p>
    </div>
  </div>
</div>
<a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
<script src="lib/easing/easing.min.js"></script>
<script src="lib/waypoints/waypoints.min.js"></script>
<script src="js/main.js"></script>
<script>
function switchTab(name, btn) {
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('on'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('on'));
  document.getElementById('tab-' + name).classList.add('on');
  if (btn) btn.classList.add('on');
  else document.querySelector('.tab-btn').classList.add('on');
}
function toggleOrd(id) {
  const body = document.getElementById('ob_' + id);
  const chev = document.getElementById('chev_' + id);
  const open = body.classList.toggle('open');
  chev.style.transform = open ? 'rotate(180deg)' : '';
}
function doLogout() {
  $.post('auth.php', {action:'logout'}, function(){
    window.location.href = 'index.php';
  });
}
function toast(msg, type) {
  const t = document.getElementById('toast');
  t.className = type === 'err' ? 'err' : '';
  t.textContent = msg; t.style.display = 'block';
  setTimeout(() => t.style.display = 'none', 3500);
}
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function dlReceipt(r) {
  const items = r.items || [];
  const payLabel = {card:'Credit/Debit Card',upi:'UPI Payment',cash:'Pay at Cafe'}[r.pay_method] || r.pay_method;
  const dateStr = r.res_date ? new Date(r.res_date + 'T12:00').toLocaleDateString('en-IN',{weekday:'long',year:'numeric',month:'long',day:'numeric'}) : '-';
  const timeStr = r.res_time ? (()=>{ const t=new Date('2000-01-01T'+r.res_time); return isNaN(t)?r.res_time:t.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'}); })() : '-';

  const html = `<!DOCTYPE html><html><head><meta charset="utf-8">
<title>Receipt — Mini Cafe #${r.order_id}</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Segoe UI',Arial,sans-serif;background:#fff;color:#222;}
.receipt{max-width:440px;margin:0 auto;padding:36px 30px;}
.logo{text-align:center;border-bottom:2px solid #8B4513;padding-bottom:18px;margin-bottom:18px;}
.logo h1{font-size:2rem;font-weight:900;letter-spacing:.1em;color:#8B4513;}
.logo p{font-size:.8rem;color:#888;margin-top:4px;}
.badge-paid{display:inline-block;background:#d4edda;color:#155724;padding:3px 12px;border-radius:50px;font-size:.75rem;font-weight:700;margin-top:8px;}
.section{margin-bottom:16px;}
.sec-title{font-size:.68rem;text-transform:uppercase;letter-spacing:.12em;color:#8B4513;font-weight:700;margin-bottom:10px;padding-bottom:4px;border-bottom:1px solid #f0d9c0;}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px 16px;}
.info-cell .l{font-size:.65rem;color:#aaa;text-transform:uppercase;letter-spacing:.06em;}
.info-cell .v{font-size:.85rem;font-weight:600;color:#333;margin-top:1px;word-break:break-word;}
table{width:100%;border-collapse:collapse;}
th{font-size:.7rem;text-transform:uppercase;color:#8B4513;padding:6px 0;border-bottom:1px solid #f0d9c0;text-align:left;letter-spacing:.06em;}
th:last-child,td:last-child{text-align:right;}
td{font-size:.88rem;padding:9px 0;border-bottom:1px solid #faf5f0;color:#333;}
.totals{margin-top:14px;}
.tr{display:flex;justify-content:space-between;font-size:.86rem;padding:5px 0;color:#666;}
.tf{font-size:1.05rem;font-weight:800;color:#8B4513;padding-top:10px;border-top:2px solid #8B4513;margin-top:6px;}
.footer-note{text-align:center;font-size:.74rem;color:#bbb;margin-top:20px;padding-top:16px;border-top:1px dashed #ddd;line-height:1.7;}
@media print{body{padding:0;}@page{margin:12mm;size:A5;}}
</style></head><body>
<div class="receipt">
  <div class="logo">
    <h1>MINI CAFE</h1>
    <p>Bavdhan, Pune &nbsp;·&nbsp; +91 8855039800 &nbsp;·&nbsp; dagadepranav21@gmail.com</p>
    <div class="badge-paid">✓ PAYMENT CONFIRMED</div>
  </div>
  <div class="section">
    <div class="sec-title">Order & Booking Details</div>
    <div class="info-grid">
      <div class="info-cell"><div class="l">Order ID</div><div class="v">#${r.order_id}</div></div>
      <div class="info-cell"><div class="l">Payment Ref</div><div class="v">${esc(r.payment_ref||'-')}</div></div>
      <div class="info-cell"><div class="l">Customer</div><div class="v">${esc(r.name)}</div></div>
      <div class="info-cell"><div class="l">Email</div><div class="v">${esc(r.email)}</div></div>
      <div class="info-cell"><div class="l">Date</div><div class="v">${dateStr}</div></div>
      <div class="info-cell"><div class="l">Time</div><div class="v">${timeStr}</div></div>
      <div class="info-cell"><div class="l">Persons</div><div class="v">${r.persons}</div></div>
      <div class="info-cell"><div class="l">Payment</div><div class="v">${payLabel}</div></div>
    </div>
  </div>
  <div class="section">
    <div class="sec-title">Items Ordered</div>
    <table>
      <thead><tr><th>Item</th><th>Qty</th><th>Amount</th></tr></thead>
      <tbody>
      ${items.map(it => `<tr>
        <td>${esc(it.item_name||it.name||'')}</td>
        <td>×${it.quantity||it.qty||1}</td>
        <td>₹${Number(it.line_total||((it.unit_price||it.price||0)*(it.quantity||it.qty||1))).toFixed(0)}</td>
      </tr>`).join('')}
      </tbody>
    </table>
  </div>
  <div class="totals">
    <div class="tr"><span>Subtotal</span><span>₹${Number(r.subtotal).toFixed(0)}</span></div>
    <div class="tr"><span>Online Discount (30%)</span><span style="color:green;">−₹${Number(r.discount).toFixed(0)}</span></div>
    <div class="tr tf"><span>Total Paid</span><span>₹${Number(r.total).toFixed(0)}</span></div>
  </div>
  <div class="footer-note">
    Thank you for dining with Mini Cafe! 🍵<br>
    Please present this receipt at the cafe for your 30% discount.<br>
    Generated on ${new Date().toLocaleString('en-IN')}
  </div>
</div>
<script>window.onload=function(){window.print();}<\/script>
</body></html>`;

  const blob = new Blob([html], {type:'text/html'});
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = 'MiniCafe_Receipt_Order' + r.order_id + '.html';
  document.body.appendChild(a); a.click();
  document.body.removeChild(a); URL.revokeObjectURL(url);
  toast('Receipt downloaded! Open it in your browser to print/save as PDF.');
}

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
</body>
</html>
