<?php session_start(); $page_title = 'Testimonials'; $active_page = 'testimonial'; ?>
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
            <h1 class="display-4 mb-3 mt-0 mt-lg-5 text-white text-uppercase">Testimonial</h1>
            <div class="d-inline-flex mb-lg-5">
                <p class="m-0 text-white"><a class="text-white" href="index.php">Home</a></p>
                <p class="m-0 text-white px-2">/</p>
                <p class="m-0 text-white">Testimonial</p>
            </div>
        </div>
    </div>

    <!-- Testimonials Carousel -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="section-title">
                <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Testimonial</h4>
                <h1 class="display-4">Our Clients Say</h1>
            </div>
            <div class="owl-carousel testimonial-carousel">
                <?php
                $reviews = [
                    ['img'=>'img/Ye.jpeg','name'=>'Archees Potdar',   'role'=>'Student',  'text'=>'Amazing coffee and vibes! The Pune Special Burger is a must-try. The online ordering system is so smooth — got 30% off my first order!'],
                    ['img'=>'img/OM.jpeg','name'=>'Sarvdnya Dagade',    'role'=>'Student',   'text'=>'Love the cozy atmosphere and the variety on the menu. The mocktails are fantastic and the baristas really know their craft.'],
                    ['img'=>'img/Amar.jpeg','name'=>'Amar Patil',  'role'=>'Student',       'text'=>'Best cheese cake in Bavdhan. Great service, super friendly staff, and the online table booking is incredibly convenient.'],
                    ['img'=>'img/VV.jpeg','name'=>'Sandesh Wagh',    'role'=>'Student',    'text'=>'Perfect place to hang out with friends. The hazelnut shake is absolutely divine! I order online almost every weekend.'],
                  
                ];
                foreach ($reviews as $r): ?>
                <div class="testimonial-item">
                    <div class="d-flex align-items-center mb-3">
                        <img class="img-fluid rounded-circle" src="<?= $r['img'] ?>" style="width:60px;height:60px;object-fit:cover;" alt="<?= $r['name'] ?>">
                        <div class="ml-3">
                            <h4><?= $r['name'] ?></h4>
                            <i><?= $r['role'] ?></i>
                        </div>
                    </div>
                    <p class="m-0"><?= $r['text'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Stats Banner -->
    <div class="container-fluid py-5" style="background:rgba(212,160,23,.06);border-top:1px solid rgba(212,160,23,.1);border-bottom:1px solid rgba(212,160,23,.1);">
        <div class="container">
            <div class="row text-center">
                <?php $stats = [['500+','Happy Customers'],['4.8★','Google Rating'],['3000+','Orders Served'],['30%','Online Discount']]; ?>
                <?php foreach($stats as [$n,$l]): ?>
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <h2 class="text-primary"><?= $n ?></h2>
                    <p class="text-white"><?= $l ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
