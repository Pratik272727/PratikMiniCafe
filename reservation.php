<?php
session_start();
$page_title  = 'Reservation';
$active_page = 'reservation';
$loggedIn    = isset($_SESSION['user']);
$authUser    = $loggedIn ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include 'includes/head.php'; ?>
<?php include 'includes/auth_styles.php'; ?>
<style>
/* Reservation-specific extras */
#res-toast{display:none;position:fixed;bottom:28px;right:28px;z-index:9999;background:#28a745;color:#fff;padding:14px 22px;border-radius:10px;font-weight:600;font-size:.92rem;box-shadow:0 8px 24px rgba(0,0,0,.3);}
#res-toast.err{background:#dc3545;}
#booking-ok{display:none;background:rgba(40,167,69,.14);border:1px solid rgba(40,167,69,.35);border-radius:10px;padding:18px 20px;color:#fff;margin-bottom:18px;text-align:center;}
#booking-ok i{font-size:2rem;color:#5adc85;display:block;margin-bottom:8px;}
.ok-id{font-size:.95rem;font-weight:700;color:#5adc85;margin-top:4px;}
.res-spinner{display:none;width:18px;height:18px;border:2px solid rgba(255,255,255,.35);border-top-color:#fff;border-radius:50%;animation:rspin .7s linear infinite;margin:0 auto;}
@keyframes rspin{to{transform:rotate(360deg);}}
.ferr{color:#ff8080;font-size:.78rem;display:none;margin-top:3px;}
.input-invalid{border-color:#dc3545 !important;}
</style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- Page Header -->
<div class="container-fluid page-header mb-5 position-relative overlay-bottom">
    <div class="d-flex flex-column align-items-center justify-content-center pt-0 pt-lg-5" style="min-height: 400px">
        <h1 class="display-4 mb-3 mt-0 mt-lg-5 text-white text-uppercase">Reservation</h1>
        <div class="d-inline-flex mb-lg-5">
            <p class="m-0 text-white"><a class="text-white" href="index.php">Home</a></p>
            <p class="m-0 text-white px-2">/</p>
            <p class="m-0 text-white">Reservation</p>
        </div>
    </div>
</div>

<!-- Reservation Section -->
<div class="container-fluid py-5" id="res-section">
    <div class="container">
        <div class="reservation position-relative overlay-top overlay-bottom">
            <div class="row align-items-center">

                <!-- Left: promo -->
                <div class="col-lg-6 my-5 my-lg-0">
                    <div class="p-5">
                        <div class="mb-4">
                            <h1 class="display-3 text-primary">30% OFF</h1>
                            <h1 class="text-white">For Online Reservation</h1>
                        </div>
                        <p class="text-white">Book your table online and get an exclusive 30% discount on your entire order. Quick confirmation, no waiting — your table is secured instantly.</p>
                        <ul class="list-inline text-white m-0">
                            <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Instant booking confirmation</li>
                            <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>30% discount on your order</li>
                            <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Priority seating guaranteed</li>
                        </ul>
                        <div class="mt-4">
                            <a href="order.php" class="btn btn-outline-light font-weight-bold py-2 px-4">
                                <i class="fa fa-utensils mr-2"></i>Order + Book Together
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right: form -->
                <div class="col-lg-6">
                    <div class="text-center p-5" style="background: rgba(51, 33, 29, .85); border-radius: 8px;">
                        <h1 class="text-white mb-4 mt-5">Book Your Table</h1>

                        <!-- Success box -->
                        <div id="booking-ok">
                            <i class="fa fa-check-circle"></i>
                            <h5 class="text-white mb-1">Table Booked Successfully!</h5>
                            <div class="ok-id" id="ok-id-text"></div>
                            <p class="text-white mt-2" style="font-size:.84rem;">We look forward to seeing you. Your 30% discount is confirmed!</p>
                        </div>

                        <form id="resForm" novalidate autocomplete="off">

                            <div class="form-group text-left">
                                <input type="text" class="form-control bg-transparent border-primary p-4"
                                    id="r_name" placeholder="Your Name" autocomplete="off">
                                <div class="ferr" id="e_name">Please enter your name.</div>
                            </div>

                            <div class="form-group text-left">
                                <input type="email" class="form-control bg-transparent border-primary p-4"
                                    id="r_email" placeholder="Your Email" autocomplete="off"
                                    value="<?= $loggedIn ? htmlspecialchars($authUser['email']) : '' ?>">
                                <div class="ferr" id="e_email">Please enter a valid email.</div>
                            </div>

                            <div class="form-group text-left">
                                <input type="tel" class="form-control bg-transparent border-primary p-4"
                                    id="r_phone" placeholder="Phone Number (optional)">
                            </div>

                            <div class="form-group text-left">
                                <input type="date" class="form-control bg-transparent border-primary p-4"
                                    id="r_date">
                                <div class="ferr" id="e_date">Please select a date.</div>
                            </div>

                            <div class="form-group text-left">
                                <input type="time" class="form-control bg-transparent border-primary p-4"
                                    id="r_time">
                                <div class="ferr" id="e_time">Please select a time.</div>
                            </div>

                            <div class="form-group text-left">
                                <select class="custom-select bg-transparent border-primary px-4" id="r_persons" style="height: 58px; color: rgba(245,237,224,.7);">
                                    <option value="">Number of Persons</option>
                                    <option value="1">1 Person</option>
                                    <option value="2">2 Persons</option>
                                    <option value="3">3 Persons</option>
                                    <option value="4">4 Persons</option>
                                    <option value="5">5 Persons</option>
                                    <option value="6">6 Persons</option>
                                </select>
                                <div class="ferr" id="e_persons">Please select number of persons.</div>
                            </div>

                            <div class="form-group text-left">
                                <textarea class="form-control bg-transparent border-primary p-4" id="r_special"
                                    rows="2" placeholder="Special Request (optional)"></textarea>
                            </div>

                            <button type="button" id="resBtn" class="btn btn-primary btn-block font-weight-bold py-3" onclick="submitReservation()">
                                <span id="res-btn-txt">Book Now — 30% OFF</span>
                                <div class="res-spinner" id="res-spinner"></div>
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div id="res-toast"></div>

<?php include 'includes/footer.php'; ?>

<script>
// Set minimum date = today
document.getElementById('r_date').min = new Date().toISOString().split('T')[0];

// Pre-fill name if logged in
<?php if ($loggedIn): ?>
document.getElementById('r_name').value = '<?= addslashes($authUser['name']) ?>';
<?php endif; ?>

function resToast(msg, type) {
    var t = document.getElementById('res-toast');
    t.className = type === 'err' ? 'err' : '';
    t.textContent = msg;
    t.style.display = 'block';
    setTimeout(function(){ t.style.display = 'none'; }, 5000);
}

function clearErr(id) {
    document.getElementById('e_' + id).style.display = 'none';
    document.getElementById('r_' + id).classList.remove('input-invalid');
}

function showErr(id, msg) {
    var el = document.getElementById('e_' + id);
    if (msg) el.textContent = msg;
    el.style.display = 'block';
    document.getElementById('r_' + id).classList.add('input-invalid');
}

// Live clear on input
['name','email','date','time','persons'].forEach(function(f) {
    var el = document.getElementById('r_' + f);
    if (el) el.addEventListener(f === 'persons' ? 'change' : 'input', function(){ clearErr(f); });
});

function submitReservation() {
    var name    = document.getElementById('r_name').value.trim();
    var email   = document.getElementById('r_email').value.trim();
    var phone   = document.getElementById('r_phone').value.trim();
    var date    = document.getElementById('r_date').value;
    var time    = document.getElementById('r_time').value;
    var persons = document.getElementById('r_persons').value;
    var special = document.getElementById('r_special').value.trim();

    // Validate
    var ok = true;
    if (!name)                                    { showErr('name',    null); ok = false; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showErr('email', null); ok = false; }
    if (!date)                                    { showErr('date',    null); ok = false; }
    if (!time)                                    { showErr('time',    null); ok = false; }
    if (!persons)                                 { showErr('persons', null); ok = false; }
    if (!ok) return;

    // Disable button + show spinner
    var btn = document.getElementById('resBtn');
    btn.disabled = true;
    document.getElementById('res-btn-txt').style.display = 'none';
    document.getElementById('res-spinner').style.display = 'block';

    // POST via fetch (no jQuery dependency issues)
    var body = new URLSearchParams();
    body.append('name',    name);
    body.append('email',   email);
    body.append('phone',   phone);
    body.append('date',    date);
    body.append('time',    time);
    body.append('persons', persons);
    body.append('special', special);

    fetch('process_reservation.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString()
    })
    .then(function(r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(function(res) {
        if (res.success) {
            document.getElementById('ok-id-text').textContent =
                'Booking ID: #' + res.booking_id + '  ·  Discount: 30% OFF';
            document.getElementById('booking-ok').style.display = 'block';
            document.getElementById('resForm').reset();
            resToast('Table booked! ID: #' + res.booking_id, 'ok');
            window.scrollTo({ top: document.getElementById('res-section').offsetTop - 80, behavior: 'smooth' });
        } else {
            resToast(res.message || 'Booking failed. Please try again.', 'err');
        }
    })
    .catch(function(err) {
        resToast('Could not connect. Please check your connection and try again.', 'err');
        console.error('Reservation error:', err);
    })
    .finally(function() {
        btn.disabled = false;
        document.getElementById('res-btn-txt').style.display = 'inline';
        document.getElementById('res-spinner').style.display = 'none';
    });
}
</script>
</body>
</html>
