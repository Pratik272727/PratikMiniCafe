<?php session_start(); $page_title = 'Our Services'; $active_page = 'service'; ?>
<!DOCTYPE html>
<html lang="en">
<head><?php include 'includes/head.php'; ?>
<?php include 'includes/auth_styles.php'; ?>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

    <!-- Page Header -->
    <div class="container-fluid page-header mb-5 position-relative overlay-bottom">
        <div class="d-flex flex-column align-items-center justify-content-center pt-0 pt-lg-5" style="min-height: 400px">
            <h1 class="display-4 mb-3 mt-0 mt-lg-5 text-white text-uppercase">Services</h1>
            <div class="d-inline-flex mb-lg-5">
                <p class="m-0 text-white"><a class="text-white" href="index.php">Home</a></p>
                <p class="m-0 text-white px-2">/</p>
                <p class="m-0 text-white">Services</p>
            </div>
        </div>
    </div>

    <!-- Services -->
    <div class="container-fluid pt-5">
        <div class="container">
            <div class="section-title">
                <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Our Services</h4>
                <h1 class="display-4">Fresh &amp; Organic Beans</h1>
            </div>
            <div class="row">
                <?php
                $services = [
                    ['icon'=>'fa-truck',        'title'=>'Fastest Door Delivery', 'desc'=>'Hot coffee and fresh food delivered to your door in record time. We partner with trusted delivery services to ensure your order arrives warm and perfect.', 'img'=>'img/service-1.jpg'],
                    ['icon'=>'fa-coffee',       'title'=>'Fresh Coffee Beans',    'desc'=>'Our beans are sourced from Ethiopia, Colombia, and Guatemala — roasted in-house weekly for peak freshness and flavour in every single cup.', 'img'=>'img/service-2.jpg'],
                    ['icon'=>'fa-award',        'title'=>'Best Quality Coffee',   'desc'=>'Every cup is brewed by trained baristas using precision equipment. We never compromise on quality — from bean selection to the final pour.', 'img'=>'img/service-3.jpg'],
                    ['icon'=>'fa-table',        'title'=>'Online Table Booking',  'desc'=>'Reserve your table in advance through our website. Enjoy 30% off all orders when you book online — instant confirmation, no waiting.', 'img'=>'img/service-4.jpg'],
                    ['icon'=>'fa-utensils',     'title'=>'Full Food Menu',        'desc'=>'Beyond coffee — enjoy our full menu of burgers, pizzas, sandwiches, fries, desserts, mocktails, and shakes. Something for everyone.', 'img'=>'img/service-1.jpg'],
                    ['icon'=>'fa-mobile-alt',   'title'=>'Order Online',          'desc'=>'Browse our full menu, add items to your cart, pick your table, and pay securely — all from the comfort of your phone or laptop.', 'img'=>'img/service-2.jpg'],
                ];
                foreach ($services as $s): ?>
                <div class="col-lg-6 mb-5">
                    <div class="row align-items-center">
                        <div class="col-sm-5">
                            <img class="img-fluid mb-3 mb-sm-0" src="<?= $s['img'] ?>" alt="<?= $s['title'] ?>">
                        </div>
                        <div class="col-sm-7">
                            <h4><i class="fa <?= $s['icon'] ?> service-icon"></i><?= $s['title'] ?></h4>
                            <p class="m-0"><?= $s['desc'] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="offer container-fluid my-5 py-5 text-center position-relative overlay-top overlay-bottom">
        <div class="container py-5">
            <h1 class="display-3 text-primary mt-3">30% OFF</h1>
            <h1 class="text-white mb-3">For All Online Orders</h1>
            <a href="order.php" class="btn btn-primary font-weight-bold py-3 px-5 mt-3">Order Now</a>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
