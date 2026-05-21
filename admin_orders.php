<?php
$db_host="localhost";$db_user="root";$db_pass="";$db_name="koppee_db";
$conn=new mysqli($db_host,$db_user,$db_pass,$db_name);
if($conn->connect_error) die("DB Error");
$conn->set_charset("utf8mb4");

// Update status
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['update'])){
    $id=intval($_POST['order_id']);
    $status=$conn->real_escape_string($_POST['order_status']);
    $conn->query("UPDATE orders SET order_status='$status' WHERE order_id=$id");
    header("Location: admin_orders.php"); exit;
}

$orders=$conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$rows=$orders?$orders->fetch_all(MYSQLI_ASSOC):[];
$total=count($rows);
$paid=count(array_filter($rows,fn($r)=>$r['payment_status']==='paid'));
$revenue=array_sum(array_column(array_filter($rows,fn($r)=>$r['payment_status']==='paid'),'total_amount'));
$conn->close();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Orders — Mini Cafe</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{--bg:#0f0804;--surface:#1c0e07;--border:rgba(255,255,255,.08);--gold:#c8963a;--cream:#f5ede0;--sand:#b89070;--font:'DM Sans',sans-serif;}
body{font-family:var(--font);background:var(--bg);color:var(--cream);padding:32px 20px;min-height:100vh;}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;}
.header h1{font-size:1.5rem;font-weight:700;}.header h1 span{color:var(--gold);}
.nav-btns{display:flex;gap:10px;}
.btn-link{background:rgba(200,150,58,.1);border:1px solid rgba(200,150,58,.3);color:var(--gold);padding:8px 16px;border-radius:8px;text-decoration:none;font-size:.82rem;transition:background .2s;}
.btn-link:hover{background:rgba(200,150,58,.2);}
.stats{display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap;}
.stat{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:14px 22px;min-width:110px;}
.stat .num{font-size:1.7rem;font-weight:700;color:var(--gold);}
.stat .lbl{font-size:.74rem;color:var(--sand);text-transform:uppercase;letter-spacing:.05em;}
.table-wrap{overflow-x:auto;border-radius:12px;border:1px solid var(--border);}
table{width:100%;border-collapse:collapse;}
thead tr{background:rgba(200,150,58,.08);}
th{padding:12px 14px;text-align:left;font-size:.72rem;text-transform:uppercase;letter-spacing:.08em;color:var(--gold);font-weight:600;white-space:nowrap;}
td{padding:12px 14px;font-size:.86rem;border-top:1px solid var(--border);vertical-align:middle;}
tr:hover td{background:rgba(255,255,255,.02);}
.badge{display:inline-block;padding:3px 10px;border-radius:100px;font-size:.72rem;font-weight:600;}
.b-paid{background:rgba(76,175,125,.15);color:#7ddba4;}
.b-pending{background:rgba(200,150,58,.15);color:#e8b45a;}
.b-failed{background:rgba(224,85,85,.15);color:#f08080;}
.b-placed{background:rgba(100,149,237,.15);color:#90b0f8;}
.b-preparing{background:rgba(255,165,0,.15);color:#ffc875;}
.b-ready{background:rgba(76,175,125,.15);color:#7ddba4;}
.b-delivered{background:rgba(150,150,150,.15);color:#aaa;}
.b-cancelled{background:rgba(224,85,85,.15);color:#f08080;}
.gold-tag{background:linear-gradient(135deg,#c8963a,#e8b45a);color:#1a0a04;font-weight:700;padding:2px 8px;border-radius:100px;font-size:.72rem;}
.status-sel{background:var(--surface);border:1px solid var(--border);color:var(--cream);padding:5px 8px;border-radius:6px;font-size:.8rem;cursor:pointer;font-family:var(--font);}
.save-btn{background:#c8963a;border:none;color:#1a0a04;padding:5px 10px;border-radius:6px;cursor:pointer;font-size:.78rem;font-weight:600;margin-left:4px;}
.save-btn:hover{opacity:.85;}
.empty{text-align:center;padding:50px;color:var(--sand);}
.empty i{font-size:2.8rem;display:block;margin-bottom:10px;opacity:.4;}
.toggle-items{background:transparent;border:1px solid var(--border);color:var(--sand);padding:4px 10px;border-radius:6px;font-size:.75rem;cursor:pointer;}
.toggle-items:hover{color:var(--gold);border-color:var(--gold);}
.items-detail{display:none;background:rgba(0,0,0,.2);border-radius:8px;padding:10px;margin-top:8px;font-size:.8rem;color:var(--cream);}
.items-detail.show{display:block;}
.items-detail .ir{display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px solid rgba(255,255,255,.05);}
.items-detail .ir:last-child{border:none;}
</style>
</head>
<body>
<div class="header">
  <h1><i class="fa fa-receipt" style="margin-right:10px;color:#c8963a;"></i>Orders — <span>Mini Cafe Admin</span></h1>
  <div class="nav-btns">
    <a href="admin_reservations.php" class="btn-link"><i class="fa fa-calendar mr-1"></i>Reservations</a>
    <a href="index.php" class="btn-link"><i class="fa fa-arrow-left mr-1"></i>Back to Site</a>
  </div>
</div>

<?php
$placed=count(array_filter($rows,fn($r)=>$r['order_status']==='placed'));
$preparing=count(array_filter($rows,fn($r)=>$r['order_status']==='preparing'));
?>
<div class="stats">
  <div class="stat"><div class="num"><?=$total?></div><div class="lbl">Total Orders</div></div>
  <div class="stat"><div class="num" style="color:#7ddba4;"><?=$paid?></div><div class="lbl">Paid</div></div>
  <div class="stat"><div class="num" style="color:#e8b45a;"><?=$placed?></div><div class="lbl">New</div></div>
  <div class="stat"><div class="num" style="color:#ffc875;"><?=$preparing?></div><div class="lbl">Preparing</div></div>
  <div class="stat"><div class="num" style="color:#c8963a;">₹<?=number_format($revenue,0)?></div><div class="lbl">Revenue</div></div>
</div>

<div class="table-wrap">
<table>
  <thead><tr><th>#</th><th>Customer</th><th>Date</th><th>Time</th><th>Persons</th><th>Items</th><th>Subtotal</th><th>Discount</th><th>Total</th><th>Payment</th><th>Ref</th><th>Order Status</th><th>Booked</th></tr></thead>
  <tbody>
  <?php if(empty($rows)): ?>
    <tr><td colspan="13" class="empty"><i class="fa fa-inbox"></i>No orders yet.</td></tr>
  <?php else: foreach($rows as $r):
    $db2=new mysqli("localhost","root","","koppee_db");
    $items=$db2->query("SELECT * FROM order_items WHERE order_id=".$r['order_id']);
    $item_rows=$items?$items->fetch_all(MYSQLI_ASSOC):[];
    $db2->close();
    $item_count=count($item_rows);
  ?>
  <tr>
    <td><strong>#<?=$r['order_id']?></strong></td>
    <td>
      <div style="font-weight:600;"><?=htmlspecialchars($r['customer_name'])?></div>
      <div style="color:var(--sand);font-size:.78rem;"><?=htmlspecialchars($r['email'])?></div>
      <?php if($r['phone']): ?><div style="color:var(--sand);font-size:.78rem;"><?=htmlspecialchars($r['phone'])?></div><?php endif; ?>
    </td>
    <td><?=$r['reservation_date']?date('d M Y',strtotime($r['reservation_date'])):'-'?></td>
    <td><?=$r['reservation_time']?date('h:i A',strtotime($r['reservation_time'])):'-'?></td>
    <td style="text-align:center;"><?=$r['persons']?></td>
    <td>
      <button class="toggle-items" onclick="toggleItems(<?=$r['order_id']?>)"><?=$item_count?> item<?=$item_count>1?'s':''?></button>
      <div class="items-detail" id="id_<?=$r['order_id']?>">
        <?php foreach($item_rows as $it): ?>
          <div class="ir"><span><?=htmlspecialchars($it['item_name'])?> ×<?=$it['quantity']?></span><span style="color:#c8963a;">₹<?=number_format($it['line_total'],0)?></span></div>
        <?php endforeach; ?>
      </div>
    </td>
    <td>₹<?=number_format($r['subtotal'],0)?></td>
    <td><span class="gold-tag"><?=number_format($r['discount_pct'],0)?>% OFF</span><br><small style="color:#7ddba4;">-₹<?=number_format($r['discount_amount'],0)?></small></td>
    <td style="font-weight:700;color:#c8963a;">₹<?=number_format($r['total_amount'],0)?></td>
    <td>
      <span class="badge b-<?=$r['payment_status']?>"><?=ucfirst($r['payment_status'])?></span><br>
      <small style="color:var(--sand);"><?=ucfirst($r['payment_method'])?></small>
    </td>
    <td style="font-size:.78rem;color:var(--sand);"><?=htmlspecialchars($r['payment_ref']??'-')?></td>
    <td>
      <form method="POST" style="display:inline-flex;align-items:center;gap:4px;">
        <input type="hidden" name="order_id" value="<?=$r['order_id']?>">
        <select name="order_status" class="status-sel">
          <?php foreach(['placed','preparing','ready','delivered','cancelled'] as $s): ?>
            <option value="<?=$s?>" <?=$r['order_status']===$s?'selected':''?>><?=ucfirst($s)?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" name="update" class="save-btn">Save</button>
      </form>
    </td>
    <td style="color:var(--sand);font-size:.78rem;"><?=date('d M, h:i A',strtotime($r['created_at']))?></td>
  </tr>
  <?php endforeach; endif; ?>
  </tbody>
</table>
</div>

<script>
function toggleItems(id){
  const el=document.getElementById('id_'+id);
  el.classList.toggle('show');
}
</script>
</body></html>
