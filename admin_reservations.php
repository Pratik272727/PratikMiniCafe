<?php
// admin_reservations.php - Admin Panel to manage all bookings
$db_host="localhost"; $db_user="root"; $db_pass=""; $db_name="koppee_db";
$conn=new mysqli($db_host,$db_user,$db_pass,$db_name);
if($conn->connect_error) die("DB Error: ".$conn->connect_error);
$conn->set_charset("utf8mb4");

if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['update_status'])){
    $id=intval($_POST['reservation_id']);
    $status=in_array($_POST['status'],['pending','confirmed','cancelled'])?$_POST['status']:'pending';
    $conn->query("UPDATE reservations SET status='$status' WHERE reservation_id=$id");
    header("Location: admin_reservations.php"); exit;
}

$result=$conn->query("SELECT * FROM reservations ORDER BY created_at DESC");
$rows=$result?$result->fetch_all(MYSQLI_ASSOC):[];
$total=count($rows);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Reservations | Mini Cafe</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        :root{--bg:#0f0804;--surface:#1c0e07;--border:rgba(255,255,255,.08);--gold:#c8963a;--cream:#f5ede0;--sand:#b89070;--font:'DM Sans',sans-serif;}
        body{font-family:var(--font);background:var(--bg);color:var(--cream);padding:40px 24px;min-height:100vh;}
        .header{display:flex;align-items:center;justify-content:space-between;margin-bottom:32px;flex-wrap:wrap;gap:16px;}
        .header h1{font-size:1.6rem;font-weight:700;}
        .header h1 span{color:var(--gold);}
        .back-btn{background:rgba(200,150,58,.1);border:1px solid rgba(200,150,58,.3);color:var(--gold);padding:8px 18px;border-radius:8px;text-decoration:none;font-size:.85rem;transition:background .2s;}
        .back-btn:hover{background:rgba(200,150,58,.2);}
        .stats{display:flex;gap:14px;margin-bottom:28px;flex-wrap:wrap;}
        .stat{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:16px 24px;min-width:120px;}
        .stat .num{font-size:1.8rem;font-weight:700;color:var(--gold);}
        .stat .lbl{font-size:.78rem;color:var(--sand);text-transform:uppercase;letter-spacing:.05em;margin-top:2px;}
        .table-wrap{overflow-x:auto;border-radius:14px;border:1px solid var(--border);}
        table{width:100%;border-collapse:collapse;}
        thead tr{background:rgba(200,150,58,.08);}
        th{padding:14px 16px;text-align:left;font-size:.75rem;text-transform:uppercase;letter-spacing:.08em;color:var(--gold);font-weight:600;white-space:nowrap;}
        td{padding:14px 16px;font-size:.88rem;border-top:1px solid var(--border);vertical-align:middle;}
        tr:hover td{background:rgba(255,255,255,.02);}
        .badge-pending{display:inline-block;padding:3px 10px;border-radius:100px;font-size:.75rem;font-weight:600;background:rgba(200,150,58,.15);color:#e8b45a;}
        .badge-confirmed{display:inline-block;padding:3px 10px;border-radius:100px;font-size:.75rem;font-weight:600;background:rgba(76,175,125,.15);color:#7ddba4;}
        .badge-cancelled{display:inline-block;padding:3px 10px;border-radius:100px;font-size:.75rem;font-weight:600;background:rgba(224,85,85,.15);color:#f08080;}
        .discount-tag{background:linear-gradient(135deg,#c8963a,#e8b45a);color:#1a0a04;font-weight:700;padding:2px 8px;border-radius:100px;font-size:.75rem;}
        select.status-select{background:var(--surface);border:1px solid var(--border);color:var(--cream);padding:5px 10px;border-radius:6px;font-size:.82rem;cursor:pointer;font-family:var(--font);}
        .save-btn{background:#c8963a;border:none;color:#1a0a04;padding:5px 12px;border-radius:6px;cursor:pointer;font-size:.8rem;font-weight:600;margin-left:6px;}
        .save-btn:hover{opacity:.85;}
        .empty{text-align:center;padding:60px;color:var(--sand);}
        .empty i{font-size:3rem;margin-bottom:12px;display:block;opacity:.4;}
    </style>
</head>
<body>
<div class="header">
    <h1><i class="fa fa-calendar-check" style="margin-right:10px;color:#c8963a;"></i>Reservations — <span>Mini Cafe Admin</span></h1>
    <a href="index.php" class="back-btn"><i class="fa fa-arrow-left" style="margin-right:6px;"></i>Back to Site</a>
</div>

<?php
$pending=count(array_filter($rows,fn($r)=>$r['status']==='pending'));
$confirmed=count(array_filter($rows,fn($r)=>$r['status']==='confirmed'));
$cancelled=count(array_filter($rows,fn($r)=>$r['status']==='cancelled'));
?>
<div class="stats">
    <div class="stat"><div class="num"><?=$total?></div><div class="lbl">Total</div></div>
    <div class="stat"><div class="num" style="color:#e8b45a;"><?=$pending?></div><div class="lbl">Pending</div></div>
    <div class="stat"><div class="num" style="color:#7ddba4;"><?=$confirmed?></div><div class="lbl">Confirmed</div></div>
    <div class="stat"><div class="num" style="color:#f08080;"><?=$cancelled?></div><div class="lbl">Cancelled</div></div>
</div>

<div class="table-wrap">
    <table>
        <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Date</th><th>Time</th><th>Persons</th><th>Discount</th><th>Booked On</th><th>Status</th></tr></thead>
        <tbody>
        <?php if(empty($rows)): ?>
            <tr><td colspan="10" class="empty"><i class="fa fa-inbox"></i>No reservations yet.</td></tr>
        <?php else: foreach($rows as $r): ?>
            <tr>
                <td><strong><?=$r['reservation_id']?></strong></td>
                <td><?=htmlspecialchars($r['customer_name'])?></td>
                <td style="color:#b89070;"><?=htmlspecialchars($r['email'])?></td>
                <td><?=htmlspecialchars($r['phone']?:'-')?></td>
                <td><?=date('d M Y',strtotime($r['reservation_date']))?></td>
                <td><?=date('h:i A',strtotime($r['reservation_time']))?></td>
                <td style="text-align:center;"><?=$r['persons']?></td>
                <td><span class="discount-tag"><?=number_format($r['discount'],0)?>% OFF</span></td>
                <td style="color:#b89070;font-size:.82rem;"><?=date('d M Y, h:i A',strtotime($r['created_at']))?></td>
                <td>
                    <form method="POST" style="display:inline-flex;align-items:center;gap:4px;">
                        <input type="hidden" name="reservation_id" value="<?=$r['reservation_id']?>">
                        <select name="status" class="status-select">
                            <option value="pending" <?=$r['status']==='pending'?'selected':''?>>Pending</option>
                            <option value="confirmed" <?=$r['status']==='confirmed'?'selected':''?>>Confirmed</option>
                            <option value="cancelled" <?=$r['status']==='cancelled'?'selected':''?>>Cancelled</option>
                        </select>
                        <button type="submit" name="update_status" class="save-btn">Save</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
