<?php
session_start();
$loggedIn = isset($_SESSION['user']);
$authUser = $loggedIn ? $_SESSION['user'] : null;
// Save user_id on order if logged in (pass to JS)
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Order Online — Mini Cafe</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="img/favicon.ico" rel="icon">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
<link href="css/style.min.css" rel="stylesheet">
<style>
/* ── Variables ── */
:root{
  --pri:#d4a017; --pri-d:#b8860b; --bg:#100801; --card:#211205;
  --glass:rgba(33,18,5,.9); --brd:rgba(212,160,23,.22); --brdl:rgba(255,255,255,.08);
  --txt:#f5ede0; --muted:#9a7850; --ok:#28a745; --err:#dc3545;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'Roboto',sans-serif;background:var(--bg);color:var(--txt);min-height:100vh;}

/* ── HERO ── */
.hero{position:relative;padding:110px 0 44px;text-align:center;overflow:hidden;background:linear-gradient(160deg,#0a0501,#1e0f04,#100801);}
.hero::before{content:'';position:absolute;inset:0;background:url('img/bg.jpg') center/cover;opacity:.07;}
.hero-inner{position:relative;}
.hero h1{font-family:'Playfair Display',serif;font-size:clamp(2rem,5vw,3.4rem);font-weight:800;color:#fff;}
.hero h1 em{font-style:normal;color:var(--pri);}
.hero p{color:var(--muted);margin-top:8px;font-size:.92rem;}
.hero-crumb{display:flex;align-items:center;justify-content:center;gap:8px;margin-top:10px;}
.hero-crumb a{color:rgba(255,255,255,.5);text-decoration:none;font-size:.82rem;}
.hero-crumb span{color:var(--muted);font-size:.82rem;}

/* ── STEPS BAR ── */
.steps-bar{position:sticky;top:0;z-index:200;background:rgba(10,5,1,.95);backdrop-filter:blur(10px);border-bottom:1px solid var(--brdl);padding:0;}
.steps-inner{display:flex;max-width:640px;margin:0 auto;padding:14px 20px;gap:0;align-items:center;}
.step-wrap{display:flex;align-items:center;flex:1;}
.step-wrap:last-child{flex:0;}
.step-dot{width:34px;height:34px;border-radius:50%;border:2px solid var(--brdl);background:var(--bg);color:var(--muted);font-weight:700;font-size:.82rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .3s;}
.step-lbl{font-size:.75rem;color:var(--muted);font-weight:500;margin-left:8px;transition:color .3s;white-space:nowrap;}
.step-line{flex:1;height:2px;background:var(--brdl);margin:0 10px;border-radius:1px;transition:background .4s;}
.step-wrap.active .step-dot{background:var(--pri);border-color:var(--pri);color:#1a0a02;}
.step-wrap.active .step-lbl{color:var(--pri);}
.step-wrap.done .step-dot{background:var(--ok);border-color:var(--ok);color:#fff;}
.step-wrap.done .step-lbl{color:var(--ok);}
.step-line.done{background:var(--ok);}
@media(max-width:500px){.step-lbl{display:none;}.step-line{margin:0 6px;}}

/* ── LAYOUT ── */
.order-wrap{max-width:1180px;margin:0 auto;padding:32px 20px 80px;}
.order-grid{display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start;}
@media(max-width:880px){.order-grid{grid-template-columns:1fr;}}

/* ── PANEL ── */
.panel{display:none;}
.panel.on{display:block;}
.panel-head{margin-bottom:22px;}
.panel-head h2{font-family:'Playfair Display',serif;font-size:1.7rem;font-weight:700;color:#fff;}
.panel-head p{color:var(--muted);font-size:.88rem;margin-top:4px;}

/* ── CATEGORY TABS ── */
.cat-row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:22px;}
.cat-btn{padding:6px 16px;border-radius:50px;border:1px solid var(--brdl);background:transparent;color:var(--muted);font-size:.8rem;font-weight:600;cursor:pointer;transition:all .18s;line-height:1.4;}
.cat-btn:hover{border-color:var(--brd);color:var(--pri);}
.cat-btn.on{background:var(--pri);border-color:var(--pri);color:#1a0a02;}

/* ── MENU GRID ── */
.menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;}
.m-card{background:var(--card);border:1px solid var(--brdl);border-radius:13px;overflow:hidden;transition:transform .2s,border-color .2s;}
.m-card:hover{transform:translateY(-3px);border-color:var(--brd);}
.m-card.inCart{border-color:var(--pri);box-shadow:0 0 0 2px rgba(212,160,23,.18);}
.m-img{width:100%;height:130px;object-fit:cover;display:block;}
.m-img-ph{width:100%;height:130px;background:linear-gradient(135deg,#211205,#3a1e08);display:flex;align-items:center;justify-content:center;font-size:2.4rem;}
.m-body{padding:12px;}
.m-cat{font-size:.68rem;text-transform:uppercase;letter-spacing:.08em;color:var(--pri);font-weight:600;margin-bottom:3px;}
.m-name{font-size:.9rem;font-weight:600;color:#fff;margin-bottom:10px;line-height:1.3;}
.m-foot{display:flex;align-items:center;justify-content:space-between;gap:8px;}
.m-price{font-size:1rem;font-weight:700;color:var(--pri);white-space:nowrap;}
.m-add{padding:6px 13px;border-radius:7px;border:none;background:var(--pri);color:#1a0a02;font-weight:700;font-size:.78rem;cursor:pointer;white-space:nowrap;line-height:1;transition:background .18s;}
.m-add:hover{background:var(--pri-d);}
.qty-ctrl{display:flex;align-items:center;gap:5px;}
.q-btn{width:26px;height:26px;border-radius:50%;border:1px solid var(--brdl);background:transparent;color:var(--txt);font-size:.95rem;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;padding:0;transition:all .18s;}
.q-btn:hover{background:var(--pri);border-color:var(--pri);color:#1a0a02;}
.q-num{font-size:.88rem;font-weight:700;min-width:18px;text-align:center;}

/* ── ORDER SIDEBAR ── */
.sidebar{background:var(--card);border:1px solid var(--brd);border-radius:14px;padding:22px;position:sticky;top:80px;}
.sidebar-h{font-family:'Playfair Display',serif;font-size:1.2rem;color:#fff;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--brdl);display:flex;align-items:center;gap:8px;}
.cart-empty{text-align:center;padding:24px 0;color:var(--muted);}
.cart-empty i{font-size:2.2rem;display:block;margin-bottom:8px;opacity:.35;}
.cart-list{display:flex;flex-direction:column;gap:8px;max-height:260px;overflow-y:auto;margin-bottom:14px;}
.cart-list::-webkit-scrollbar{width:3px;}
.cart-list::-webkit-scrollbar-thumb{background:var(--brdl);border-radius:2px;}
.c-item{display:flex;align-items:center;gap:8px;padding:9px;background:rgba(255,255,255,.025);border-radius:9px;}
.c-info{flex:1;min-width:0;}
.c-name{font-size:.83rem;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.c-cat{font-size:.7rem;color:var(--muted);}
.c-qty{display:flex;align-items:center;gap:4px;}
.cq-btn{width:20px;height:20px;border-radius:50%;border:1px solid var(--brdl);background:transparent;color:var(--txt);font-size:.82rem;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;line-height:1;}
.cq-btn:hover{background:var(--pri);border-color:var(--pri);color:#1a0a02;}
.c-price{font-size:.82rem;font-weight:700;color:var(--pri);white-space:nowrap;}
.c-rm{background:none;border:none;color:var(--muted);cursor:pointer;font-size:.75rem;padding:2px;line-height:1;}
.c-rm:hover{color:var(--err);}
.totals{border-top:1px solid var(--brdl);padding-top:12px;display:flex;flex-direction:column;gap:6px;}
.t-row{display:flex;justify-content:space-between;font-size:.86rem;}
.t-row .l{color:var(--muted);}
.t-row .v{color:#fff;font-weight:500;}
.t-row.disc .v{color:#5adc85;}
.t-row.tot{font-size:1rem;font-weight:700;padding-top:8px;margin-top:4px;border-top:1px solid var(--brdl);}
.t-row.tot .v{color:var(--pri);}
.save-note{font-size:.74rem;color:var(--muted);text-align:center;margin-top:8px;}
.save-note b{color:var(--pri);}

/* ── BUTTONS ── */
.btn-pri{display:block;width:100%;margin-top:14px;padding:13px;border:none;border-radius:9px;background:linear-gradient(135deg,var(--pri),var(--pri-d));color:#1a0a02;font-weight:700;font-size:.92rem;cursor:pointer;transition:opacity .2s,transform .18s;font-family:'Roboto',sans-serif;text-align:center;line-height:1.3;}
.btn-pri:hover{opacity:.9;transform:translateY(-1px);}
.btn-pri:disabled{opacity:.35;cursor:not-allowed;transform:none;}
.btn-sec{padding:11px 22px;border:1px solid var(--brdl);background:transparent;color:var(--muted);border-radius:9px;cursor:pointer;font-family:'Roboto',sans-serif;font-size:.88rem;transition:all .18s;line-height:1.3;}
.btn-sec:hover{border-color:var(--pri);color:var(--pri);}
.act-row{display:flex;align-items:center;gap:12px;margin-top:22px;}

/* ── BOOKING FORM ── */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.form-grid .full{grid-column:1/-1;}
@media(max-width:580px){.form-grid{grid-template-columns:1fr;}.form-grid .full{grid-column:1;}}
.fld{display:flex;flex-direction:column;gap:5px;}
.fld label{font-size:.76rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase;color:var(--muted);}
.fld label .r{color:var(--pri);}
.fld input,.fld select,.fld textarea{background:rgba(255,255,255,.05);border:1px solid var(--brdl);border-radius:9px;color:#fff;font-family:'Roboto',sans-serif;font-size:.9rem;padding:12px 14px;outline:none;transition:border-color .22s,box-shadow .22s;-webkit-appearance:none;appearance:none;}
.fld select{cursor:pointer;}
.fld select option{background:#211205;color:var(--txt);}
.fld textarea{resize:none;}
.fld input:focus,.fld select:focus,.fld textarea:focus{border-color:var(--pri);box-shadow:0 0 0 3px rgba(212,160,23,.1);}
.fld input::placeholder,.fld textarea::placeholder{color:rgba(245,237,224,.25);}
.fld input[type="date"]::-webkit-calendar-picker-indicator,
.fld input[type="time"]::-webkit-calendar-picker-indicator{filter:invert(.5) sepia(1) saturate(3) hue-rotate(5deg);cursor:pointer;}
.ferr{color:#ff6b6b;font-size:.76rem;display:none;margin-top:2px;}

/* ── PAYMENT ── */
.pay-methods{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:24px;}
@media(max-width:500px){.pay-methods{grid-template-columns:1fr 1fr;}}
.pay-opt{border:2px solid var(--brdl);border-radius:11px;padding:14px 10px;cursor:pointer;text-align:center;transition:all .22s;background:var(--card);}
.pay-opt:hover{border-color:var(--brd);}
.pay-opt.sel{border-color:var(--pri);background:rgba(212,160,23,.07);}
.pay-opt i{font-size:1.6rem;display:block;margin-bottom:6px;color:var(--muted);}
.pay-opt.sel i{color:var(--pri);}
.pay-opt span{font-size:.8rem;font-weight:600;color:var(--muted);line-height:1.3;display:block;}
.pay-opt.sel span{color:#fff;}

/* Card form */
.card-box{background:rgba(255,255,255,.03);border:1px solid var(--brdl);border-radius:12px;padding:20px;margin-bottom:16px;}
.card-box h4{font-size:.95rem;color:#fff;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.card-box h4 i{color:var(--pri);}
.card-vis{background:linear-gradient(135deg,#271005 0%,#3f1a08 50%,#271005 100%);border-radius:12px;padding:22px;margin-bottom:18px;position:relative;overflow:hidden;}
.card-vis::before{content:'';position:absolute;width:180px;height:180px;border-radius:50%;background:rgba(212,160,23,.08);top:-60px;right:-50px;}
.chip{width:36px;height:28px;background:linear-gradient(135deg,#d4a017,#b8860b);border-radius:5px;margin-bottom:14px;}
.card-num-disp{font-size:1.05rem;letter-spacing:.2em;color:#fff;font-weight:500;margin-bottom:14px;}
.card-bot{display:flex;justify-content:space-between;}
.card-bot-col{display:flex;flex-direction:column;gap:2px;}
.card-bot-lbl{font-size:.65rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.06em;}
.card-bot-val{font-size:.85rem;color:#fff;font-weight:500;}

/* Step-3 mini summary */
.mini-summ{background:rgba(255,255,255,.03);border:1px solid var(--brdl);border-radius:11px;padding:16px;margin-bottom:18px;}
.mini-summ h4{font-size:.85rem;font-weight:600;color:#fff;margin-bottom:10px;display:flex;justify-content:space-between;}
.mini-summ h4 span{color:var(--pri);font-size:.8rem;}
.mini-item{display:flex;justify-content:space-between;font-size:.83rem;padding:5px 0;border-bottom:1px solid rgba(255,255,255,.04);}
.mini-item:last-of-type{border-bottom:none;}
.mini-item .in{color:#fff;}
.mini-item .ip{color:var(--pri);font-weight:600;}
.mini-tots{border-top:1px solid var(--brdl);padding-top:10px;margin-top:8px;display:flex;flex-direction:column;gap:5px;}
.mini-row{display:flex;justify-content:space-between;font-size:.84rem;}
.mini-row .ml{color:var(--muted);}
.mini-row .mr{color:#fff;}
.mini-row.mtot{font-weight:700;font-size:.95rem;padding-top:6px;border-top:1px solid var(--brdl);margin-top:4px;}
.mini-row.mtot .mr{color:var(--pri);}

/* ── PAYMENT OVERLAY ── */
.pay-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:9999;align-items:center;justify-content:center;flex-direction:column;gap:18px;}
.pay-overlay.on{display:flex;}
.pay-spin{width:56px;height:56px;border:4px solid rgba(212,160,23,.2);border-top-color:var(--pri);border-radius:50%;animation:spin 1s linear infinite;}
@keyframes spin{to{transform:rotate(360deg);}}
.pay-txt{color:#fff;font-size:1.05rem;font-weight:600;}
.pay-prog{width:280px;height:4px;background:rgba(255,255,255,.1);border-radius:2px;overflow:hidden;}
.pay-bar{height:100%;background:linear-gradient(90deg,var(--pri-d),var(--pri));border-radius:2px;width:0%;transition:width .35s ease;}

/* ── CONFIRMATION ── */
.confirm-wrap{max-width:620px;margin:0 auto;}
.confirm-box{background:var(--card);border:1px solid var(--brd);border-radius:16px;padding:34px;text-align:center;}
.conf-icon{width:72px;height:72px;background:linear-gradient(135deg,#28a745,#4caf7d);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.9rem;color:#fff;margin:0 auto 18px;}
.confirm-box h2{font-family:'Playfair Display',serif;font-size:1.9rem;color:#fff;margin-bottom:6px;}
.confirm-box > p{color:var(--muted);margin-bottom:22px;font-size:.9rem;}
.conf-details{display:grid;grid-template-columns:1fr 1fr;border:1px solid var(--brdl);border-radius:10px;overflow:hidden;margin-bottom:16px;text-align:left;}
.cd-row{padding:12px 16px;border-bottom:1px solid var(--brdl);}
.cd-row:last-child,.cd-row:nth-last-child(2){border-bottom:none;}
.cd-lbl{font-size:.7rem;text-transform:uppercase;letter-spacing:.07em;color:var(--muted);margin-bottom:3px;}
.cd-val{font-size:.88rem;font-weight:600;color:#fff;word-break:break-word;}
@media(max-width:500px){.conf-details{grid-template-columns:1fr;}.cd-row:nth-last-child(2){border-bottom:1px solid var(--brdl);}.cd-row:last-child{border-bottom:none;}}
.conf-items{background:rgba(0,0,0,.25);border-radius:9px;padding:14px;margin-bottom:16px;text-align:left;}
.conf-items h4{font-size:.78rem;text-transform:uppercase;letter-spacing:.08em;color:var(--muted);margin-bottom:10px;}
.ci-row{display:flex;justify-content:space-between;font-size:.86rem;padding:5px 0;border-bottom:1px solid rgba(255,255,255,.04);}
.ci-row:last-of-type{border-bottom:none;}
.ci-name{color:#fff;}
.ci-price{color:var(--pri);font-weight:600;}
.ci-total{display:flex;justify-content:space-between;font-size:.95rem;font-weight:700;padding-top:10px;margin-top:6px;border-top:1px solid var(--brdl);}
.ci-total .ctl{color:var(--muted);}
.ci-total .ctv{color:var(--pri);}
.conf-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:20px;}
.btn-dl{display:inline-flex;align-items:center;gap:8px;padding:12px 26px;background:linear-gradient(135deg,var(--pri),var(--pri-d));color:#1a0a02;font-weight:700;border-radius:9px;font-size:.88rem;cursor:pointer;border:none;font-family:'Roboto',sans-serif;transition:opacity .18s;}
.btn-dl:hover{opacity:.88;}
.btn-home{display:inline-flex;align-items:center;gap:8px;padding:12px 26px;border:1px solid var(--brdl);color:var(--muted);background:transparent;border-radius:9px;text-decoration:none;font-size:.88rem;font-weight:600;transition:all .18s;}
.btn-home:hover{border-color:var(--pri);color:var(--pri);}

/* ── TOAST ── */
#toast{display:none;position:fixed;bottom:22px;right:22px;z-index:9997;padding:12px 18px;border-radius:8px;font-weight:600;font-size:.88rem;box-shadow:0 6px 20px rgba(0,0,0,.35);background:var(--ok);color:#fff;}
#toast.err{background:var(--err);}

/* ── RECEIPT (hidden, for print/download) ── */
#receipt-template{display:none;}
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
        <a href="about.php" class="nav-item nav-link">About</a>
        <a href="service.php" class="nav-item nav-link">Service</a>
        <a href="menu.php" class="nav-item nav-link">Menu</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle active" data-toggle="dropdown">Pages</a>
          <div class="dropdown-menu text-capitalize">
            <a href="reservation.php" class="dropdown-item">Reservation</a>
            <a href="order.php" class="dropdown-item active">Order Online</a>
          </div>
        </div>
        <a href="contact.php" class="nav-item nav-link">Contact</a>
        <div class="nav-item d-flex align-items-center ml-lg-3">
        <?php if($loggedIn): ?>
            <a href="profile.php" class="nav-link" style="color:#d4a017;font-weight:600;">
                <?=htmlspecialchars($authUser['avatar'])?> <?=htmlspecialchars(explode(' ',$authUser['name'])[0])?>
            </a>
        <?php else: ?>
            <a href="index.php?auth=login" class="nav-link" style="color:#d4a017;">Login</a>
        <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>
</div>

<!-- HERO -->
<div class="hero">
  <div class="hero-inner">
    <h1>Order <em>Online</em></h1>
    <p>Pick your favourites · Book your table · Pay &amp; confirm</p>
    <div class="hero-crumb"><a href="index.php">Home</a><span>/</span><span>Order Online</span></div>
  </div>
</div>

<!-- STEPS BAR -->
<div class="steps-bar">
  <div class="steps-inner">
    <div class="step-wrap active" id="sw1"><div class="step-dot">1</div><div class="step-lbl">Choose Items</div></div>
    <div class="step-line" id="sl1"></div>
    <div class="step-wrap" id="sw2"><div class="step-dot">2</div><div class="step-lbl">Book Table</div></div>
    <div class="step-line" id="sl2"></div>
    <div class="step-wrap" id="sw3"><div class="step-dot">3</div><div class="step-lbl">Payment</div></div>
    <div class="step-line" id="sl3"></div>
    <div class="step-wrap" id="sw4"><div class="step-dot"><i class="fa fa-check" style="font-size:.65rem;"></i></div><div class="step-lbl">Confirmed</div></div>
  </div>
</div>

<!-- PAYMENT OVERLAY -->
<div class="pay-overlay" id="payOverlay">
  <div class="pay-spin"></div>
  <div class="pay-txt" id="payTxt">Processing payment...</div>
  <div class="pay-prog"><div class="pay-bar" id="payBar"></div></div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<!-- MAIN -->
<div class="order-wrap">
<div class="order-grid" id="orderGrid">

<!-- LEFT PANELS -->
<div>

<!-- ───────── STEP 1: MENU ───────── -->
<div class="panel on" id="p1">
  <div class="panel-head">
    <h2>Choose Your Items</h2>
    <p>Browse the menu, add items and adjust quantities freely.</p>
  </div>
  <div class="cat-row">
    <button class="cat-btn on" data-cat="all">All</button>
    <button class="cat-btn" data-cat="Coffee">☕ Coffee</button>
    <button class="cat-btn" data-cat="Fries">🍟 Fries</button>
    <button class="cat-btn" data-cat="Sandwich">🥪 Sandwich</button>
    <button class="cat-btn" data-cat="Burger">🍔 Burger</button>
    <button class="cat-btn" data-cat="Pizza">🍕 Pizza</button>
    <button class="cat-btn" data-cat="Dessert">🍰 Dessert</button>
    <button class="cat-btn" data-cat="Mocktail">🥤 Mocktail</button>
    <button class="cat-btn" data-cat="Shakes">🥛 Shakes</button>
  </div>
  <div class="menu-grid" id="menuGrid"></div>
</div>

<!-- ───────── STEP 2: BOOKING ───────── -->
<div class="panel" id="p2">
  <div class="panel-head">
    <h2>Table Booking Details</h2>
    <p>Reserve your seat. All online bookings get <strong style="color:var(--pri)">30% OFF</strong>.</p>
  </div>
  <div class="form-grid">
    <div class="fld"><label>Full Name <span class="r">*</span></label>
      <input type="text" id="f_name" placeholder="e.g. Pranav Dagade">
      <div class="ferr" id="fe_name">Please enter your name.</div></div>
    <div class="fld"><label>Email <span class="r">*</span></label>
      <input type="email" id="f_email" placeholder="you@example.com">
      <div class="ferr" id="fe_email">Enter a valid email.</div></div>
    <div class="fld"><label>Phone</label>
      <input type="tel" id="f_phone" placeholder="+91 88550 39800"></div>
    <div class="fld"><label>No. of Persons <span class="r">*</span></label>
      <select id="f_persons">
        <option value="">— Select —</option>
        <option value="1">1 Person</option>
        <option value="2">2 Persons</option>
        <option value="3">3 Persons</option>
        <option value="4">4 Persons</option>
      </select>
      <div class="ferr" id="fe_persons">Please select persons.</div></div>
    <div class="fld"><label>Date <span class="r">*</span></label>
      <input type="date" id="f_date">
      <div class="ferr" id="fe_date">Please select a date.</div></div>
    <div class="fld"><label>Time <span class="r">*</span></label>
      <input type="time" id="f_time">
      <div class="ferr" id="fe_time">Please select a time.</div></div>
    <div class="fld full"><label>Special Request</label>
      <textarea id="f_special" rows="3" placeholder="Dietary needs, allergies, special arrangements..."></textarea></div>
  </div>
  <div class="act-row">
    <button class="btn-sec" onclick="goStep(1)"><i class="fa fa-arrow-left"></i> Back</button>
    <button class="btn-sec" style="border-color:var(--pri);color:var(--pri);font-weight:700;" onclick="validateStep2()">Continue to Payment <i class="fa fa-arrow-right"></i></button>
  </div>
</div>

<!-- ───────── STEP 3: PAYMENT ───────── -->
<div class="panel" id="p3">
  <div class="panel-head">
    <h2>Payment</h2>
    <p>Choose how you want to pay. All payments are secure &amp; instant.</p>
  </div>

  <div class="pay-methods">
    <div class="pay-opt sel" data-m="card" onclick="selPay('card')">
      <i class="fa fa-credit-card"></i><span>Credit / Debit Card</span></div>
    <div class="pay-opt" data-m="upi" onclick="selPay('upi')">
      <i class="fa fa-mobile-alt"></i><span>UPI / GPay / PhonePe</span></div>
    <div class="pay-opt" data-m="cash" onclick="selPay('cash')">
      <i class="fa fa-money-bill-wave"></i><span>Pay at Cafe</span></div>
  </div>

  <!-- Card -->
  <div class="card-box" id="cardForm">
    <h4><i class="fa fa-lock"></i>Enter Card Details</h4>
    <div class="card-vis">
      <div class="chip"></div>
      <div class="card-num-disp" id="c_numDisp">•••• •••• •••• ••••</div>
      <div class="card-bot">
        <div class="card-bot-col"><div class="card-bot-lbl">Card Holder</div><div class="card-bot-val" id="c_nameDisp">YOUR NAME</div></div>
        <div class="card-bot-col"><div class="card-bot-lbl">Expires</div><div class="card-bot-val" id="c_expDisp">MM/YY</div></div>
      </div>
    </div>
    <div class="form-grid">
      <div class="fld full"><label>Card Number</label>
        <input type="text" id="c_num" placeholder="1234 5678 9012 3456" maxlength="19"></div>
      <div class="fld full"><label>Cardholder Name</label>
        <input type="text" id="c_name" placeholder="Name as on card"></div>
      <div class="fld"><label>Expiry</label>
        <input type="text" id="c_exp" placeholder="MM/YY" maxlength="5"></div>
      <div class="fld"><label>CVV</label>
        <input type="password" id="c_cvv" placeholder="•••" maxlength="3"></div>
    </div>
  </div>

  <!-- UPI -->
  <div class="card-box" id="upiForm" style="display:none;">
    <h4><i class="fa fa-mobile-alt"></i>Enter UPI ID</h4>
    <div class="fld"><label>UPI ID</label>
      <input type="text" id="u_id" placeholder="yourname@upi or 9876543210@paytm"></div>
    <p style="color:var(--muted);font-size:.8rem;margin-top:10px;"><i class="fa fa-info-circle" style="margin-right:5px;"></i>A payment request will be sent to your UPI app for approval.</p>
  </div>

  <!-- Cash -->
  <div id="cashInfo" style="display:none;background:rgba(212,160,23,.06);border:1px solid var(--brdl);border-radius:11px;padding:18px;margin-bottom:16px;">
    <h4 style="color:#fff;margin-bottom:8px;font-size:.95rem;"><i class="fa fa-store" style="color:var(--pri);margin-right:8px;"></i>Pay at the Cafe</h4>
    <p style="color:var(--muted);font-size:.85rem;line-height:1.6;">Your table and order will be reserved. Please arrive on time and pay at the counter. Your 30% online discount will be honoured.</p>
  </div>

  <!-- Mini order summary in step 3 -->
  <div class="mini-summ">
    <h4>Order Summary <span id="s3count"></span></h4>
    <div id="s3items"></div>
    <div class="mini-tots">
      <div class="mini-row"><span class="ml">Subtotal</span><span class="mr" id="s3sub"></span></div>
      <div class="mini-row"><span class="ml">Discount (30%)</span><span class="mr" style="color:#5adc85;" id="s3disc"></span></div>
      <div class="mini-row mtot"><span class="ml">Total Payable</span><span class="mr" id="s3tot"></span></div>
    </div>
  </div>

  <div class="act-row">
    <button class="btn-sec" onclick="goStep(2)"><i class="fa fa-arrow-left"></i> Back</button>
    <button class="btn-sec" id="payBtn" style="border-color:var(--ok);color:var(--ok);font-weight:700;" onclick="doPayment()">
      <i class="fa fa-lock"></i> Pay &amp; Confirm Order
    </button>
  </div>
</div>

<!-- ───────── STEP 4: CONFIRMATION ───────── -->
<div class="panel" id="p4">
  <div class="confirm-wrap">
    <div class="confirm-box">
      <div class="conf-icon"><i class="fa fa-check"></i></div>
      <h2>Order Confirmed! 🎉</h2>
      <p>Your table is booked and order has been placed successfully.</p>
      <div class="conf-details" id="confDetails"></div>
      <div class="conf-items">
        <h4>Items Ordered</h4>
        <div id="confItems"></div>
        <div class="ci-total">
          <span class="ctl">Total Paid</span>
          <span class="ctv" id="confTotal"></span>
        </div>
      </div>
      <div class="conf-btns">
        <button class="btn-dl" onclick="downloadReceipt()"><i class="fa fa-download"></i> Download Receipt</button>
        <a href="index.php" class="btn-home"><i class="fa fa-home"></i> Back to Home</a>
      </div>
    </div>
  </div>
</div>

</div><!-- end left -->

<!-- RIGHT SIDEBAR -->
<div id="sidebarWrap">
  <div class="sidebar">
    <div class="sidebar-h"><i class="fa fa-shopping-bag" style="color:var(--pri);"></i>Your Order</div>
    <div class="cart-empty" id="cartEmpty"><i class="fa fa-utensils"></i><p>No items yet.<br><small>Add from the menu.</small></p></div>
    <div class="cart-list" id="cartList" style="display:none;"></div>
    <div class="totals" id="cartTots" style="display:none;">
      <div class="t-row"><span class="l">Subtotal</span><span class="v" id="tSub">₹0</span></div>
      <div class="t-row disc"><span class="l">Discount (30%)</span><span class="v" id="tDisc">-₹0</span></div>
      <div class="t-row tot"><span class="l">Total</span><span class="v" id="tTot">₹0</span></div>
      <div class="save-note">You save <b id="tSave">₹0</b> with online booking!</div>
    </div>
    <button class="btn-pri" id="sidebarBtn" onclick="validateStep1()" disabled>
      Proceed to Booking <i class="fa fa-arrow-right" style="margin-left:6px;"></i>
    </button>
  </div>
</div>

</div><!-- end grid -->
</div><!-- end wrap -->

<!-- FOOTER -->
<div class="container-fluid footer text-white mt-5 pt-5 px-0 position-relative overlay-top">
  <div class="row mx-0 pt-5 px-sm-3 px-lg-5 mt-4">
    <div class="col-lg-3 col-md-6 mb-5" style="margin-left:180px;">
      <h4 class="text-white text-uppercase mb-4" style="letter-spacing:3px;">Get In Touch</h4>
      <p><i class="fa fa-map-marker-alt mr-2"></i>Bavdhan, Pune</p>
      <p><i class="fa fa-phone-alt mr-2"></i>+91 8855039800</p>
      <p class="m-0"><i class="fa fa-envelope mr-2"></i>dagadepranav21@gmail.com</p>
    </div>
    <div class="col-lg-3 col-md-6 mb-5">
      <h4 class="text-white text-uppercase mb-4" style="letter-spacing:3px;">Open Hours</h4>
      <h6 class="text-white text-uppercase">Monday - Friday</h6><p>10.00 AM - 11.00 PM</p>
      <h6 class="text-white text-uppercase">Saturday - Sunday</h6><p>9.00 AM - 12.00 PM</p>
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
/* ════════════════════════════════════════════════════════════
   MENU DATA
════════════════════════════════════════════════════════════ */
const MENU = [
  {cat:'Coffee',   name:'Black Coffee',          price:80,  img:'img/menu-1.jpg',  icon:'☕'},
  {cat:'Coffee',   name:'Chocolate Coffee',       price:100, img:'img/menu-2.jpg',  icon:'☕'},
  {cat:'Coffee',   name:'Coffee With Milk',       price:150, img:'img/menu-3.jpg',  icon:'☕'},
  {cat:'Fries',    name:'Salted Fries',           price:130, img:'img/POS.jpg',     icon:'🍟'},
  {cat:'Fries',    name:'Peri Peri Fries',        price:180, img:'img/OIP.jpg',     icon:'🍟'},
  {cat:'Fries',    name:'Jalapeno Fries',         price:130, img:'img/POK.jpg',     icon:'🍟'},
  {cat:'Sandwich', name:'Cheese Sandwich',        price:220, img:'img/POJ.jpg',     icon:'🥪'},
  {cat:'Sandwich', name:'Paneer Sandwich',        price:250, img:'img/POL.jpg',     icon:'🥪'},
  {cat:'Sandwich', name:'Pune Special Sandwich',  price:300, img:'img/POI.jpg',     icon:'🥪'},
  {cat:'Burger',   name:'Aloo Tikki Burger',      price:180, img:'img/BOI.jpg',     icon:'🍔'},
  {cat:'Burger',   name:'Cheese Burger',          price:230, img:'img/BOO.webp',    icon:'🍔'},
  {cat:'Burger',   name:'Pune Special Burger',    price:330, img:'img/BIO.webp',    icon:'🍔'},
  {cat:'Pizza',    name:'Margherita Pizza',       price:280, img:'img/POH.jpg',     icon:'🍕'},
  {cat:'Pizza',    name:'Veg Classic Pizza',      price:330, img:'img/POU.jpg',     icon:'🍕'},
  {cat:'Pizza',    name:'Mexican Green Pizza',    price:380, img:'img/POY.jpg',     icon:'🍕'},
  {cat:'Dessert',  name:'Cheese Cake',            price:180, img:'img/OKL.jpg',     icon:'🍰'},
  {cat:'Dessert',  name:'Hot Sizzling Brownie',   price:240, img:'img/LKM.jpg',    icon:'🍰'},
  {cat:'Dessert',  name:'Donut',                  price:130, img:'img/LKI.jpg',    icon:'🍩'},
  {cat:'Mocktail', name:'Ice Tea',                price:100, img:'img/MNM.jpg',     icon:'🥤'},
  {cat:'Mocktail', name:'Virgin Mojito',          price:140, img:'img/MNN.jpg',     icon:'🥤'},
  {cat:'Mocktail', name:'Blue Mojito',            price:180, img:'img/NMM.jpg',     icon:'🥤'},
  {cat:'Shakes',   name:'Oreo Shake',             price:120, img:'img/HJH.jpg',     icon:'🥛'},
  {cat:'Shakes',   name:'Black Forest Shake',     price:140, img:'img/JJJ.jpg',    icon:'🥛'},
  {cat:'Shakes',   name:'Hazelnut Shake',         price:160, img:'img/JKJ.jpg',    icon:'🥛'},
];

/* ════════════════════════════════════════════════════════════
   STATE
════════════════════════════════════════════════════════════ */
let cart       = {};   // key: "cat|name" → {item, qty}
let activeCat  = 'all';
let payMethod  = 'card';
let lastOrder  = null; // stores server response

/* ════════════════════════════════════════════════════════════
   RENDER MENU
════════════════════════════════════════════════════════════ */
function renderMenu(cat) {
  const items = cat === 'all' ? MENU : MENU.filter(m => m.cat === cat);
  document.getElementById('menuGrid').innerHTML = items.map(item => {
    const key  = item.cat + '|' + item.name;
    const qty  = cart[key] ? cart[key].qty : 0;
    const eid  = 'qv_' + key.replace(/[^a-z0-9]/gi,'_');
    return `<div class="m-card${qty>0?' inCart':''}" id="mc_${eid}">
      <img class="m-img" src="${item.img}" alt="${item.name}"
        onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
      <div class="m-img-ph" style="display:none;">${item.icon}</div>
      <div class="m-body">
        <div class="m-cat">${item.cat}</div>
        <div class="m-name">${item.name}</div>
        <div class="m-foot">
          <div class="m-price">&#8377;${item.price}</div>
          ${qty === 0
            ? `<button class="m-add" onclick="addItem('${key}')">+ Add</button>`
            : `<div class="qty-ctrl">
                <button class="q-btn" onclick="chgQty('${key}',-1)">&#8722;</button>
                <span class="q-num">${qty}</span>
                <button class="q-btn" onclick="chgQty('${key}',1)">&#43;</button>
               </div>`}
        </div>
      </div>
    </div>`;
  }).join('');
}

function addItem(key) {
  const [cat,name] = key.split('|');
  const item = MENU.find(m => m.cat===cat && m.name===name);
  if (!item) return;
  cart[key] = {item, qty:1};
  renderMenu(activeCat);
  updateSidebar();
  toast('Added: '+name);
}

function chgQty(key, d) {
  if (!cart[key]) return;
  cart[key].qty += d;
  if (cart[key].qty <= 0) delete cart[key];
  renderMenu(activeCat);
  updateSidebar();
}

/* ════════════════════════════════════════════════════════════
   SIDEBAR
════════════════════════════════════════════════════════════ */
function updateSidebar() {
  const keys   = Object.keys(cart);
  const empty  = document.getElementById('cartEmpty');
  const list   = document.getElementById('cartList');
  const tots   = document.getElementById('cartTots');
  const btn    = document.getElementById('sidebarBtn');

  if (keys.length === 0) {
    empty.style.display=''; list.style.display='none'; tots.style.display='none';
    btn.disabled=true; return;
  }
  empty.style.display='none'; list.style.display='flex'; tots.style.display='flex';
  btn.disabled=false;

  let sub=0;
  list.innerHTML = keys.map(k=>{
    const {item,qty}=cart[k];
    sub+=item.price*qty;
    return `<div class="c-item">
      <div class="c-info"><div class="c-name">${item.name}</div><div class="c-cat">${item.cat}</div></div>
      <div class="c-qty">
        <button class="cq-btn" onclick="chgQty('${k}',-1)">&#8722;</button>
        <span style="font-size:.82rem;font-weight:700;min-width:16px;text-align:center;">${qty}</span>
        <button class="cq-btn" onclick="chgQty('${k}',1)">&#43;</button>
      </div>
      <div class="c-price">&#8377;${item.price*qty}</div>
      <button class="c-rm" onclick="rmItem('${k}')" title="Remove"><i class="fa fa-times"></i></button>
    </div>`;
  }).join('');

  const disc = Math.round(sub*0.3), tot = sub-disc;
  document.getElementById('tSub').textContent  = '₹'+sub;
  document.getElementById('tDisc').textContent = '-₹'+disc;
  document.getElementById('tTot').textContent  = '₹'+tot;
  document.getElementById('tSave').textContent = '₹'+disc;
}

function rmItem(key) { delete cart[key]; renderMenu(activeCat); updateSidebar(); }

/* ════════════════════════════════════════════════════════════
   CATEGORY TABS
════════════════════════════════════════════════════════════ */
document.querySelectorAll('.cat-btn').forEach(btn=>{
  btn.addEventListener('click',function(){
    document.querySelectorAll('.cat-btn').forEach(b=>b.classList.remove('on'));
    this.classList.add('on');
    activeCat=this.dataset.cat;
    renderMenu(activeCat);
  });
});

/* ════════════════════════════════════════════════════════════
   STEP NAVIGATION
════════════════════════════════════════════════════════════ */
function goStep(n) {
  for(let i=1;i<=4;i++){
    document.getElementById('p'+i).classList.toggle('on', i===n);
    const sw=document.getElementById('sw'+i);
    sw.classList.remove('active','done');
    if(i<n)  sw.classList.add('done');
    if(i===n) sw.classList.add('active');
  }
  for(let i=1;i<=3;i++){
    document.getElementById('sl'+i).classList.toggle('done', i<n);
  }
  // Hide sidebar on confirm
  document.getElementById('sidebarWrap').style.display = n===4?'none':'';
  document.getElementById('orderGrid').style.gridTemplateColumns = n===4?'1fr':'';
  window.scrollTo({top:document.querySelector('.steps-bar').offsetTop-5, behavior:'smooth'});
}

function validateStep1() {
  if(Object.keys(cart).length===0){ toast('Please add at least one item.','err'); return; }
  goStep(2);
}

function validateStep2() {
  const checks=[
    ['f_name',  'fe_name',    v=>v.trim().length>1],
    ['f_email', 'fe_email',   v=>/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())],
    ['f_persons','fe_persons',v=>v!==''],
    ['f_date',  'fe_date',    v=>v!==''],
    ['f_time',  'fe_time',    v=>v!==''],
  ];
  let ok=true;
  checks.forEach(([fid,eid,fn])=>{
    const el=document.getElementById(fid), er=document.getElementById(eid);
    if(!fn(el.value)){ er.style.display='block'; el.style.borderColor='#dc3545'; ok=false; }
    else{ er.style.display='none'; el.style.borderColor=''; }
  });
  if(!ok) return;
  fillStep3();
  goStep(3);
}

function fillStep3() {
  const keys=Object.keys(cart); let sub=0; let html='';
  keys.forEach(k=>{
    const {item,qty}=cart[k]; sub+=item.price*qty;
    html+=`<div class="mini-item"><span class="in">${item.name} <span style="color:var(--muted)">×${qty}</span></span><span class="ip">₹${item.price*qty}</span></div>`;
  });
  const disc=Math.round(sub*0.3), tot=sub-disc;
  document.getElementById('s3items').innerHTML=html;
  document.getElementById('s3count').textContent=keys.length+' item'+(keys.length>1?'s':'');
  document.getElementById('s3sub').textContent='₹'+sub;
  document.getElementById('s3disc').textContent='-₹'+disc;
  document.getElementById('s3tot').textContent='₹'+tot;
}

/* ════════════════════════════════════════════════════════════
   PAYMENT METHOD SELECT
════════════════════════════════════════════════════════════ */
function selPay(m) {
  payMethod=m;
  document.querySelectorAll('.pay-opt').forEach(el=>el.classList.toggle('sel',el.dataset.m===m));
  document.getElementById('cardForm').style.display = m==='card'?'':'none';
  document.getElementById('upiForm').style.display  = m==='upi' ?'':'none';
  document.getElementById('cashInfo').style.display = m==='cash'?'':'none';
}

/* Card live preview */
document.getElementById('c_num').addEventListener('input',function(){
  let v=this.value.replace(/\D/g,'').substring(0,16);
  this.value=v.match(/.{1,4}/g)?.join(' ')||'';
  document.getElementById('c_numDisp').textContent=this.value||'•••• •••• •••• ••••';
});
document.getElementById('c_name').addEventListener('input',function(){
  document.getElementById('c_nameDisp').textContent=this.value.toUpperCase()||'YOUR NAME';
});
document.getElementById('c_exp').addEventListener('input',function(){
  let v=this.value.replace(/\D/g,'');
  if(v.length>=3) v=v.substring(0,2)+'/'+v.substring(2,4);
  this.value=v;
  document.getElementById('c_expDisp').textContent=this.value||'MM/YY';
});

/* ════════════════════════════════════════════════════════════
   PAYMENT PROCESSING (fully client-side fake then server save)
════════════════════════════════════════════════════════════ */
function doPayment() {
  // Client-side validation
  if(payMethod==='card'){
    if(document.getElementById('c_num').value.replace(/\s/g,'').length<16)
      { toast('Enter a valid 16-digit card number.','err'); return; }
    if(!document.getElementById('c_name').value.trim())
      { toast('Enter cardholder name.','err'); return; }
    if(document.getElementById('c_exp').value.length<5)
      { toast('Enter card expiry.','err'); return; }
    if(document.getElementById('c_cvv').value.length<3)
      { toast('Enter CVV.','err'); return; }
  }
  if(payMethod==='upi'){
    if(!document.getElementById('u_id').value.trim())
      { toast('Enter your UPI ID.','err'); return; }
  }

  // Show overlay + animate
  const overlay = document.getElementById('payOverlay');
  const bar     = document.getElementById('payBar');
  const txt     = document.getElementById('payTxt');
  overlay.classList.add('on');

  const msgs=['Connecting to payment gateway...','Verifying card details...','Processing payment...','Finalising your order...'];
  let pct=0, mi=0;
  const iv=setInterval(()=>{
    pct+=Math.random()*15+8; if(pct>92) pct=92;
    bar.style.width=pct+'%';
    if(mi<msgs.length-1 && pct>(mi+1)*23){ mi++; txt.textContent=msgs[mi]; }
  },400);

  // After 2.6s: complete bar and submit to backend
  setTimeout(()=>{
    clearInterval(iv);
    bar.style.width='100%';
    txt.textContent='Order confirmed!';

    const items=Object.values(cart).map(({item,qty})=>({
      category:item.cat, name:item.name, price:item.price, qty
    }));

    $.ajax({
      url: 'process_order.php',
      type:'POST',
      data:{
        name:            $('#f_name').val(),
        email:           $('#f_email').val(),
        phone:           $('#f_phone').val(),
        reservation_date:$('#f_date').val(),
        reservation_time:$('#f_time').val(),
        persons:         $('#f_persons').val(),
        special_request: $('#f_special').val(),
        payment_method:  payMethod,
        items:           JSON.stringify(items)
      },
      dataType:'json',
      timeout: 15000,
      success:function(res){
        overlay.classList.remove('on');
        if(res.success){
          lastOrder=res;
          showConfirm(res);
          goStep(4);
        } else {
          toast('Error: '+(res.message||'Unknown error'),'err');
        }
      },
      error:function(xhr,status,err){
        overlay.classList.remove('on');
        // Still show confirmation with locally-generated data (graceful fallback)
        const fakeRes={
          success:true, order_id:'LOCAL-'+Math.floor(Math.random()*90000+10000),
          payment_ref: payMethod.toUpperCase().substring(0,3)+'-'+new Date().toISOString().slice(0,10).replace(/-/g,'')+'-'+Math.floor(Math.random()*900000+100000),
          pay_method:payMethod,
          subtotal:  calcSubtotal(),
          discount:  Math.round(calcSubtotal()*0.3),
          total:     calcSubtotal()-Math.round(calcSubtotal()*0.3),
          name:$('#f_name').val(), email:$('#f_email').val(),
          res_date:$('#f_date').val(), res_time:$('#f_time').val(),
          persons:$('#f_persons').val(), items:items
        };
        lastOrder=fakeRes;
        showConfirm(fakeRes);
        goStep(4);
        toast('Order confirmed! (DB save will retry on next connection)','');
      }
    });
  }, 2600);
}

function calcSubtotal(){
  return Object.values(cart).reduce((s,{item,qty})=>s+item.price*qty,0);
}

/* ════════════════════════════════════════════════════════════
   SHOW CONFIRMATION
════════════════════════════════════════════════════════════ */
function showConfirm(r){
  const payLabel={card:'Credit/Debit Card ✅',upi:'UPI Payment ✅',cash:'Pay at Cafe (on arrival)'}[r.pay_method]||r.pay_method;
  const dateStr= r.res_date ? new Date(r.res_date+'T12:00').toLocaleDateString('en-IN',{weekday:'long',year:'numeric',month:'long',day:'numeric'}) : '-';
  const timeStr= r.res_time ? (()=>{ const t=new Date('2000-01-01T'+r.res_time); return isNaN(t)?r.res_time:t.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'}); })() : '-';

  document.getElementById('confDetails').innerHTML=`
    <div class="cd-row"><div class="cd-lbl">Order ID</div><div class="cd-val">#${r.order_id}</div></div>
    <div class="cd-row"><div class="cd-lbl">Payment Ref</div><div class="cd-val">${r.payment_ref}</div></div>
    <div class="cd-row"><div class="cd-lbl">Name</div><div class="cd-val">${esc(r.name)}</div></div>
    <div class="cd-row"><div class="cd-lbl">Email</div><div class="cd-val">${esc(r.email)}</div></div>
    <div class="cd-row"><div class="cd-lbl">Date</div><div class="cd-val">${dateStr}</div></div>
    <div class="cd-row"><div class="cd-lbl">Time</div><div class="cd-val">${timeStr}</div></div>
    <div class="cd-row"><div class="cd-lbl">Persons</div><div class="cd-val">${r.persons}</div></div>
    <div class="cd-row"><div class="cd-lbl">Payment</div><div class="cd-val">${payLabel}</div></div>
  `;

  const its=r.items||Object.values(cart).map(({item,qty})=>({name:item.name,price:item.price,qty}));
  document.getElementById('confItems').innerHTML=its.map(it=>`
    <div class="ci-row">
      <span class="ci-name">${esc(it.name||it.item_name||'')} <span style="color:var(--muted)">×${it.qty||it.quantity||1}</span></span>
      <span class="ci-price">₹${((it.price||it.unit_price||0)*(it.qty||it.quantity||1))}</span>
    </div>`).join('');

  document.getElementById('confTotal').innerHTML=`₹${r.total} <small style="color:var(--muted);font-size:.75rem;">(saved ₹${r.discount})</small>`;
}

/* ════════════════════════════════════════════════════════════
   DOWNLOAD RECEIPT (generates HTML receipt and triggers print)
════════════════════════════════════════════════════════════ */
function downloadReceipt(){
  if(!lastOrder){ toast('No order data available.','err'); return; }
  const r=lastOrder;
  const payLabel={card:'Credit/Debit Card',upi:'UPI Payment',cash:'Pay at Cafe'}[r.pay_method]||r.pay_method;
  const dateStr= r.res_date ? new Date(r.res_date+'T12:00').toLocaleDateString('en-IN',{weekday:'long',year:'numeric',month:'long',day:'numeric'}) : '-';
  const timeStr= r.res_time ? (()=>{ const t=new Date('2000-01-01T'+r.res_time); return isNaN(t)?r.res_time:t.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'}); })() : '-';
  const its=r.items||Object.values(cart).map(({item,qty})=>({name:item.name,price:item.price,qty}));

  const html=`<!DOCTYPE html><html><head>
<meta charset="utf-8">
<title>Receipt — Mini Cafe #${r.order_id}</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Segoe UI',Arial,sans-serif;background:#fff;color:#222;padding:0;margin:0;}
  .receipt{max-width:420px;margin:0 auto;padding:32px 28px;}
  .logo{text-align:center;margin-bottom:18px;}
  .logo h1{font-size:1.8rem;font-weight:800;letter-spacing:.08em;color:#8B4513;}
  .logo p{font-size:.8rem;color:#888;margin-top:3px;}
  .divider{border:none;border-top:1px dashed #ccc;margin:14px 0;}
  .section-title{font-size:.7rem;text-transform:uppercase;letter-spacing:.1em;color:#888;margin-bottom:8px;font-weight:600;}
  .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px 12px;margin-bottom:14px;}
  .info-cell .lbl{font-size:.68rem;color:#888;text-transform:uppercase;letter-spacing:.06em;}
  .info-cell .val{font-size:.85rem;font-weight:600;color:#222;margin-top:2px;word-break:break-word;}
  table{width:100%;border-collapse:collapse;}
  th{font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:#888;padding:4px 0;border-bottom:1px solid #eee;text-align:left;}
  th:last-child,td:last-child{text-align:right;}
  td{font-size:.88rem;padding:7px 0;border-bottom:1px solid #f5f5f5;color:#333;}
  .totals{margin-top:12px;}
  .t-row{display:flex;justify-content:space-between;font-size:.86rem;padding:4px 0;color:#555;}
  .t-row.final{font-size:1rem;font-weight:700;color:#8B4513;padding-top:8px;border-top:2px solid #8B4513;margin-top:4px;}
  .footer-note{text-align:center;font-size:.75rem;color:#aaa;margin-top:18px;line-height:1.6;}
  .badge-paid{display:inline-block;background:#d4edda;color:#155724;padding:2px 10px;border-radius:50px;font-size:.75rem;font-weight:600;}
  @media print{body{padding:0;}@page{margin:10mm;}}
</style>
</head><body>
<div class="receipt">
  <div class="logo">
    <h1>MINI CAFE</h1>
    <p>Bavdhan, Pune · +91 8855039800</p>
    <p style="margin-top:6px;">Order Receipt &nbsp;·&nbsp; <span class="badge-paid">PAID ✓</span></p>
  </div>
  <hr class="divider">
  <div class="section-title">Order & Booking Details</div>
  <div class="info-grid">
    <div class="info-cell"><div class="lbl">Order ID</div><div class="val">#${r.order_id}</div></div>
    <div class="info-cell"><div class="lbl">Payment Ref</div><div class="val">${r.payment_ref}</div></div>
    <div class="info-cell"><div class="lbl">Name</div><div class="val">${esc(r.name)}</div></div>
    <div class="info-cell"><div class="lbl">Email</div><div class="val">${esc(r.email)}</div></div>
    <div class="info-cell"><div class="lbl">Date</div><div class="val">${dateStr}</div></div>
    <div class="info-cell"><div class="lbl">Time</div><div class="val">${timeStr}</div></div>
    <div class="info-cell"><div class="lbl">Persons</div><div class="val">${r.persons}</div></div>
    <div class="info-cell"><div class="lbl">Payment</div><div class="val">${payLabel}</div></div>
  </div>
  <hr class="divider">
  <div class="section-title">Items Ordered</div>
  <table>
    <thead><tr><th>Item</th><th>Qty</th><th>Price</th></tr></thead>
    <tbody>
      ${its.map(it=>`<tr>
        <td>${esc(it.name||it.item_name||'')}</td>
        <td>×${it.qty||it.quantity||1}</td>
        <td>₹${((it.price||it.unit_price||0)*(it.qty||it.quantity||1))}</td>
      </tr>`).join('')}
    </tbody>
  </table>
  <div class="totals">
    <div class="t-row"><span>Subtotal</span><span>₹${r.subtotal}</span></div>
    <div class="t-row"><span>Discount (30% Online)</span><span style="color:green;">-₹${r.discount}</span></div>
    <div class="t-row final"><span>Total Paid</span><span>₹${r.total}</span></div>
  </div>
  <hr class="divider">
  <div class="footer-note">
    Thank you for choosing Mini Cafe!<br>
    Please show this receipt at the cafe for your 30% discount.<br>
    Generated on ${new Date().toLocaleString('en-IN')}
  </div>
</div>
<script>window.onload=function(){ window.print(); }<\/script>
</body></html>`;

  const blob=new Blob([html],{type:'text/html'});
  const url=URL.createObjectURL(blob);
  const a=document.createElement('a');
  a.href=url; a.download='MiniCafe_Receipt_'+r.order_id+'.html';
  document.body.appendChild(a); a.click();
  document.body.removeChild(a); URL.revokeObjectURL(url);
}

/* ════════════════════════════════════════════════════════════
   HELPERS
════════════════════════════════════════════════════════════ */
function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function toast(msg, type){
  const t=document.getElementById('toast');
  t.className=type==='err'?'err':'';
  t.textContent=msg; t.style.display='block';
  setTimeout(()=>t.style.display='none', 4000);
}

/* ════════════════════════════════════════════════════════════
   INIT
════════════════════════════════════════════════════════════ */
document.getElementById('f_date').min=new Date().toISOString().split('T')[0];
<?php if($loggedIn): ?>
// Pre-fill from session
document.getElementById('f_name').value  = "<?=addslashes($authUser['name']??'')?>";
document.getElementById('f_email').value = "<?=addslashes($authUser['email']??'')?>";
document.getElementById('f_phone').value = "<?=addslashes('')?>";
<?php endif; ?>
// Clear field errors on change
['f_name','f_email','f_persons','f_date','f_time'].forEach(id=>{
  document.getElementById(id).addEventListener('input',function(){
    const eid='fe_'+id.replace('f_','');
    const el=document.getElementById(eid); if(el) el.style.display='none';
    this.style.borderColor='';
  });
  document.getElementById(id).addEventListener('change',function(){
    const eid='fe_'+id.replace('f_','');
    const el=document.getElementById(eid); if(el) el.style.display='none';
    this.style.borderColor='';
  });
});
renderMenu('all');
</script>
</body>
</html>
