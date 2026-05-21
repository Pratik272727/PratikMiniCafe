<?php session_start(); $page_title = 'About Us'; $active_page = 'about'; ?>
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
            <h1 class="display-4 mb-3 mt-0 mt-lg-5 text-white text-uppercase">About Us</h1>
            <div class="d-inline-flex mb-lg-5">
                <p class="m-0 text-white"><a class="text-white" href="index.php">Home</a></p>
                <p class="m-0 text-white px-2">/</p>
                <p class="m-0 text-white">About Us</p>
            </div>
        </div>
    </div>

    <!-- About -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="section-title">
                <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">About Us</h4>
                <h1 class="display-4">Serving ESTD 2026</h1>
            </div>
            <div class="row">
                <div class="col-lg-4 py-0 py-lg-5">
                    <h1 class="mb-3">Our Story</h1>
                    <p>Mini Café began in 2019, tucked into a forgotten corner of the city where neon signs flicker and jazz drifts from open windows. We wanted a space for the night owls, the dreamers, the ones who find magic in the hours after midnight.<br><br>
                    Our beans are sourced from small farms in Ethiopia, Colombia, and Guatemala — roasted in-house, brewed with precision, served with care. But it's not just about the coffee. It's about the conversations that unfold over steaming cups, the strangers who become friends, the ideas born at 2 AM.</p>
                </div>
                <div class="col-lg-4 py-5 py-lg-0" style="min-height: 500px;">
                    <div class="position-relative h-100">
                        <img class="position-absolute w-100 h-100" src="img/about.png" style="object-fit: cover;" alt="About Mini Cafe">
                    </div>
                </div>
                <div class="col-lg-4 py-0 py-lg-5">
                    <h1 class="mb-3">Our Vision</h1>
                    <p>To craft a delightful digital café experience where every order flows as smoothly as a perfectly brewed espresso, blending elegant design, seamless ordering, and efficient management to create a warm, modern space for coffee lovers and café staff alike.</p>
                    <h1 class="mb-3 mt-4">Our Values</h1>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fa fa-check text-primary mr-2"></i>Quality above everything</li>
                        <li class="mb-2"><i class="fa fa-check text-primary mr-2"></i>Freshly sourced, roasted in-house</li>
                        <li class="mb-2"><i class="fa fa-check text-primary mr-2"></i>Community &amp; warmth in every cup</li>
                        <li class="mb-2"><i class="fa fa-check text-primary mr-2"></i>Sustainable &amp; ethical sourcing</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Team -->
    <div class="container-fluid py-5 bg-dark">
        <div class="container">
            <div class="section-title">
                <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Our Team</h4>
                <h1 class="display-4 text-white">The People Behind Mini Cafe</h1>
            </div>
            <div class="row">
                <?php
                $team = [
                    ['name'=>'Pranav Dagade',   'role'=>'Founder & CEO',      'img'=>'img/testimonial-1.jpg'],
                    ['name'=>'Soham Padhar',      'role'=>'Head Barista',        'img'=>'img/Soham.jpeg'],
                    ['name'=>'Pratik Parmar',      'role'=>'Customer Experience', 'img'=>'img/Pratik.jpeg'],
                    ['name'=>'Pratik Gurav',    'role'=>'Kitchen Manager',     'img'=>'img/PratikG.jpeg'],
                  
                ];
                foreach ($team as $t): ?>
                <div class="col-lg-3 col-md-6 mb-5">
                    <div class="text-center">
                        <img src="<?= $t['img'] ?>" class="rounded-circle mb-3" style="width:120px;height:120px;object-fit:cover;border:3px solid #d4a017;" alt="<?= $t['name'] ?>">
                        <h5 class="text-white"><?= $t['name'] ?></h5>
                        <p class="text-primary"><?= $t['role'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
