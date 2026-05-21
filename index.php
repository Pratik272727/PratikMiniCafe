<?php
session_start();
$loggedIn = isset($_SESSION['user']);
$authUser = $loggedIn ? $_SESSION['user'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Mini Cafe</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@200;400&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />
    <link href="css/style.min.css" rel="stylesheet">

    <style>
    /* ── Menu image fix ── */
    .menu-item-img-wrap{width:80px;height:80px;flex-shrink:0;border-radius:50%;overflow:hidden;border:2px solid rgba(212,160,23,.3);}
    .menu-item-img-wrap img{width:100%;height:100%;object-fit:cover;display:block;}
    /* ── Auth modal ── */
    .auth-modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:9999;align-items:center;justify-content:center;backdrop-filter:blur(4px);}
    .auth-modal-bg.show{display:flex;}
    .auth-modal{background:#1e0e04;border:1px solid rgba(212,160,23,.25);border-radius:16px;padding:36px;width:100%;max-width:420px;position:relative;animation:modalIn .3s ease;}
    @keyframes modalIn{from{opacity:0;transform:translateY(-20px);}to{opacity:1;transform:translateY(0);}}
    .auth-modal .close-btn{position:absolute;top:14px;right:16px;background:none;border:none;color:rgba(245,237,224,.4);font-size:1.3rem;cursor:pointer;line-height:1;}
    .auth-modal .close-btn:hover{color:#fff;}
    .auth-modal h3{font-family:'Montserrat',sans-serif;font-size:1.5rem;font-weight:700;color:#fff;margin-bottom:4px;}
    .auth-modal .sub{color:#9a7850;font-size:.84rem;margin-bottom:22px;}
    .auth-field{display:flex;flex-direction:column;gap:5px;margin-bottom:14px;}
    .auth-field label{font-size:.74rem;text-transform:uppercase;letter-spacing:.06em;color:#9a7850;font-weight:600;}
    .auth-field input{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:9px;color:#fff;padding:11px 14px;font-size:.9rem;outline:none;transition:border-color .2s;font-family:'Roboto',sans-serif;}
    .auth-field input:focus{border-color:#d4a017;box-shadow:0 0 0 3px rgba(212,160,23,.1);}
    .auth-field input::placeholder{color:rgba(245,237,224,.25);}
    .auth-btn{width:100%;padding:13px;border:none;border-radius:9px;background:linear-gradient(135deg,#d4a017,#b8860b);color:#1a0a02;font-weight:700;font-size:.95rem;cursor:pointer;margin-top:6px;font-family:'Roboto',sans-serif;transition:opacity .2s;}
    .auth-btn:hover{opacity:.88;}
    .auth-err{background:rgba(220,53,69,.12);border:1px solid rgba(220,53,69,.3);border-radius:8px;padding:10px 14px;color:#f08080;font-size:.82rem;display:none;margin-bottom:12px;}
    .auth-err.show{display:block;}
    .auth-switch{text-align:center;margin-top:16px;font-size:.83rem;color:#9a7850;}
    .auth-switch a{color:#d4a017;cursor:pointer;font-weight:600;text-decoration:none;}
    /* ── Navbar auth buttons ── */
    .nav-auth-btn{padding:7px 18px;border-radius:50px;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;font-family:'Roboto',sans-serif;}
    .nav-login{background:transparent;border:1px solid rgba(212,160,23,.5);color:#d4a017;}
    .nav-login:hover{background:rgba(212,160,23,.1);}
    .nav-signup{background:linear-gradient(135deg,#d4a017,#b8860b);border:none;color:#1a0a02;margin-left:8px;}
    .nav-signup:hover{opacity:.88;}
    .nav-user-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(212,160,23,.12);border:1px solid rgba(212,160,23,.3);border-radius:50px;padding:6px 14px;color:#d4a017;font-size:.84rem;font-weight:600;text-decoration:none;}
    .nav-user-pill:hover{background:rgba(212,160,23,.2);color:#d4a017;}
    /* ── Toast ── */
    #idx-toast{display:none;position:fixed;bottom:22px;right:22px;z-index:10000;padding:12px 18px;border-radius:8px;font-weight:600;font-size:.88rem;box-shadow:0 6px 20px rgba(0,0,0,.35);color:#fff;background:#28a745;}
    #idx-toast.err{background:#dc3545;}
    </style>
</head>
<body>
    <div class="container-fluid p-0 nav-bar">
        <nav class="navbar navbar-expand-lg bg-none navbar-dark py-3">
            <a href="index.php" class="navbar-brand px-lg-4 m-0"><h1 class="m-0 display-4 text-uppercase text-white">Mini Cafe</h1></a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse"><span class="navbar-toggler-icon"></span></button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ml-auto p-4">
                    <a href="index.php" class="nav-item nav-link active">Home</a>
                    <a href="about.php" class="nav-item nav-link">About</a>
                    <a href="service.php" class="nav-item nav-link">Service</a>
                    <a href="menu.php" class="nav-item nav-link">Menu</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu text-capitalize">
                            <a href="reservation.php" class="dropdown-item">Reservation</a>
                            <a href="order.php" class="dropdown-item">Order Online</a>
                             <a href="testimonial.php" class="dropdown-item">Testimonial</a>
                        </div>
                    </div>
                    <a href="contact.php" class="nav-item nav-link">Contact</a>
                    <div class="nav-item d-flex align-items-center ml-lg-3">
                    <?php if($loggedIn): ?>
                        <a href="profile.php" class="nav-user-pill">
                            <?= htmlspecialchars($authUser['avatar']) ?> <?= htmlspecialchars(explode(' ',$authUser['name'])[0]) ?>
                        </a>
                    <?php else: ?>
                        <button class="nav-auth-btn nav-login" onclick="openAuth('login')">Login</button>
                        <button class="nav-auth-btn nav-signup" onclick="openAuth('signup')">Sign Up</button>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <div class="container-fluid p-0 mb-5">
        <div id="blog-carousel" class="carousel slide overlay-bottom" data-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active"><img class="w-100" src="img/carousel-1.jpg" alt="Image"><div class="carousel-caption d-flex flex-column align-items-center justify-content-center"><h2 class="text-primary font-weight-medium m-0">We Have Been Serving</h2><h1 class="display-1 text-white m-0">COFFEE</h1><h2 class="text-white m-0">ESTD 2026</h2></div></div>
                <div class="carousel-item"><img class="w-100" src="img/carousel-2.jpg" alt="Image"><div class="carousel-caption d-flex flex-column align-items-center justify-content-center"><h2 class="text-primary font-weight-medium m-0">We Have Been Serving</h2><h1 class="display-1 text-white m-0">COFFEE</h1><h2 class="text-white m-0">ESTD 2026</h2></div></div>
            </div>
            <a class="carousel-control-prev" href="#blog-carousel" data-slide="prev"><span class="carousel-control-prev-icon"></span></a>
            <a class="carousel-control-next" href="#blog-carousel" data-slide="next"><span class="carousel-control-next-icon"></span></a>
        </div>
    </div>

    <div class="container-fluid py-5">
        <div class="container">
            <div class="section-title"><h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">About Us</h4><h1 class="display-4">Serving ESTD 2026</h1></div>
            <div class="row">
                <div class="col-lg-4 py-0 py-lg-5"><h1 class="mb-3">Our Story</h1><p>Mini Café began in 2019, tucked into a forgotten corner of the city where neon signs flicker and jazz drifts from open windows. We wanted a space for the night owls, the dreamers, the ones who find magic in the hours after midnight.<br><br>Our beans are sourced from small farms in Ethiopia, Colombia, and Guatemala — roasted in-house, brewed with precision, served with care.</p></div>
                <div class="col-lg-4 py-5 py-lg-0" style="min-height: 500px;"><div class="position-relative h-100"><img class="position-absolute w-100 h-100" src="img/about.png" style="object-fit: cover;"></div></div>
                <div class="col-lg-4 py-0 py-lg-5"><h1 class="mb-3">Our Vision</h1><p>To craft a delightful digital café experience where every order flows as smoothly as a perfectly brewed espresso, blending elegant design, seamless ordering, and efficient management to create a warm, modern space for coffee lovers and café staff alike</p></div>
            </div>
        </div>
    </div>

    <div class="container-fluid pt-5">
        <div class="container">
            <div class="section-title"><h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Our Services</h4><h1 class="display-4">Fresh &amp; Organic Beans</h1></div>
            <div class="row">
                <div class="col-lg-6 mb-5"><div class="row align-items-center"><div class="col-sm-5"><img class="img-fluid mb-3 mb-sm-0" src="img/service-1.jpg" alt=""></div><div class="col-sm-7"><h4><i class="fa fa-truck service-icon"></i>Fastest Door Delivery</h4><p class="m-0">Enjoy quick and reliable delivery, bringing your favorite café treats straight to your doorstep.</p></div></div></div>
                <div class="col-lg-6 mb-5"><div class="row align-items-center"><div class="col-sm-5"><img class="img-fluid mb-3 mb-sm-0" src="img/service-2.jpg" alt=""></div><div class="col-sm-7"><h4><i class="fa fa-coffee service-icon"></i>Fresh Coffee Beans</h4><p class="m-0">Carefully selected organic beans ensuring freshness, flavor, and the perfect brew every time</p></div></div></div>
                <div class="col-lg-6 mb-5"><div class="row align-items-center"><div class="col-sm-5"><img class="img-fluid mb-3 mb-sm-0" src="img/service-3.jpg" alt=""></div><div class="col-sm-7"><h4><i class="fa fa-award service-icon"></i>Best Quality Coffee</h4><p class="m-0">Made with freshly roasted beans to deliver a rich aroma and premium coffee experience.</p></div></div></div>
                <div class="col-lg-6 mb-5"><div class="row align-items-center"><div class="col-sm-5"><img class="img-fluid mb-3 mb-sm-0" src="img/service-4.jpg" alt=""></div><div class="col-sm-7"><h4><i class="fa fa-table service-icon"></i>Online Table Booking</h4><p class="m-0">Reserve your table easily online and enjoy a relaxing café experience without waiting.</p></div></div></div>
            </div>
        </div>
    </div>

    <div class="offer container-fluid my-5 py-5 text-center position-relative overlay-top overlay-bottom">
        <div class="container py-5"><h1 class="display-3 text-primary mt-3">50% OFF</h1><h1 class="text-white mb-3">Sunday Special Offer</h1></div>
    </div>

    <div class="container-fluid pt-5">
        <div class="container">
            <div class="section-title"><h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Menu &amp; Pricing</h4><h1 class="display-4">Competitive Pricing</h1></div>
            <div class="row">
                <div class="col-lg-6"><h1 class="mb-5">Coffee</h1>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/menu-1.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;80</h5></div><div class="col-8 col-sm-9"><h4>Black Coffee</h4><p class="m-0">A bold and aromatic brew for a pure coffee experience.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/menu-2.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;100</h5></div><div class="col-8 col-sm-9"><h4>Chocolate Coffee</h4><p class="m-0">Smooth coffee blended with rich chocolate flavor.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/menu-3.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;150</h5></div><div class="col-8 col-sm-9"><h4>Coffee With Milk</h4><p class="m-0">Creamy coffee perfectly balanced with fresh milk.</p></div></div>
                </div>
                <div class="col-lg-6"><h1 class="mb-5">Fries</h1>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POS.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;130</h5></div><div class="col-8 col-sm-9"><h4>Salted Fries</h4><p class="m-0">Crispy golden fries lightly seasoned with salt.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/OIP.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;180</h5></div><div class="col-8 col-sm-9"><h4>Peri Peri Fries</h4><p class="m-0">Spicy fries tossed in flavorful peri peri seasoning.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POK.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;130</h5></div><div class="col-8 col-sm-9"><h4>Jalapeno Fries</h4><p class="m-0">Crunchy fries with a zesty jalapeno kick.</p></div></div>
                </div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-lg-6"><h1 class="mb-5">Sandwich</h1>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POJ.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;220</h5></div><div class="col-8 col-sm-9"><h4>Cheese Sandwich</h4><p class="m-0">Toasted bread layered with melted creamy cheese.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POL.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;250</h5></div><div class="col-8 col-sm-9"><h4>Paneer Sandwich</h4><p class="m-0">Grilled sandwich filled with flavorful paneer and spices</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POI.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;300</h5></div><div class="col-8 col-sm-9"><h4>Pune Special Sandwich</h4><p class="m-0">A loaded sandwich with signature local flavors.</p></div></div>
                </div>
                <div class="col-lg-6"><h1 class="mb-5">Burger</h1>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/BOI.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;180</h5></div><div class="col-8 col-sm-9"><h4>Aloo Tikki Burger</h4><p class="m-0"> Crispy potato patty with fresh veggies in a soft bun.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/BOO.webp" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;230</h5></div><div class="col-8 col-sm-9"><h4>Cheese Burger</h4><p class="m-0">Juicy patty topped with melted cheese and sauces.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/BIO.webp" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;330</h5></div><div class="col-8 col-sm-9"><h4>Pune Special Burger</h4><p class="m-0"> A special burger packed with bold flavors and fillings.</p></div></div>
                </div>
                <div class="col-lg-6 mt-4"><h1 class="mb-5">Pizza</h1>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POJ.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;280</h5></div><div class="col-8 col-sm-9"><h4>Margherita Pizza</h4><p class="m-0">Classic pizza topped with fresh tomato sauce and melted cheese.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POL.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;330</h5></div><div class="col-8 col-sm-9"><h4>Veg Classic Pizza</h4><p class="m-0">Loaded with fresh vegetables and creamy cheese on a crispy crust.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POI.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;380</h5></div><div class="col-8 col-sm-9"><h4>Mexican Green Pizza</h4><p class="m-0"> A spicy pizza with Mexican herbs, veggies, and tangy flavors.</p></div></div>
                </div>
                <div class="col-lg-6 mt-4"><h1 class="mb-5">Dessert</h1>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/BOI.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;180</h5></div><div class="col-8 col-sm-9"><h4>Cheese Cake</h4><p class="m-0">Smooth and creamy cheesecake with a rich, velvety texture.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/BOO.webp" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;240</h5></div><div class="col-8 col-sm-9"><h4>Hot Sizzling Brownie</h4><p class="m-0">Warm chocolate brownie served sizzling with chocolate sauce.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/BIO.webp" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;130</h5></div><div class="col-8 col-sm-9"><h4>Donut</h4><p class="m-0"> Soft and fluffy donut glazed with sweet sugary goodness.</p></div></div>
                </div>
                <div class="col-lg-6 mt-4"><h1 class="mb-5">Mocktail</h1>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POJ.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;100</h5></div><div class="col-8 col-sm-9"><h4>Ice Tea</h4><p class="m-0">Refreshing chilled tea with a light and soothing flavor.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POL.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;140</h5></div><div class="col-8 col-sm-9"><h4>Virgin Mojito</h4><p class="m-0">A refreshing blend of mint, lime, and sparkling soda.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/POI.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;180</h5></div><div class="col-8 col-sm-9"><h4>Blue Mojito</h4><p class="m-0">A cool mint and lemon drink with a vibrant blue twist.</p></div></div>
                </div>
                <div class="col-lg-6 mt-4"><h1 class="mb-5">Shakes</h1>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/BOI.jpg" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;120</h5></div><div class="col-8 col-sm-9"><h4>Oreo Shake</h4><p class="m-0"> Creamy milkshake blended with crunchy Oreo cookies.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/BOO.webp" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;140</h5></div><div class="col-8 col-sm-9"><h4>Black Forest Shake</h4><p class="m-0">Rich chocolate shake inspired by the classic Black Forest cake.</p></div></div>
                    <div class="row align-items-center mb-5"><div class="col-auto" style="width:96px;"><div class="menu-item-img-wrap"><img src="img/BIO.webp" alt="" loading="lazy" onerror="this.src='img/menu-1.jpg'"></div><h5 class="menu-price">&#8377;160</h5></div><div class="col-8 col-sm-9"><h4>Hazelnut Shake</h4><p class="m-0"> Smooth milkshake with the nutty flavor of roasted hazelnuts.</p></div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ORDER CTA SECTION -->
    <div class="container-fluid my-5">
        <div class="container">
            <div class="reservation position-relative overlay-top overlay-bottom">
                <div class="row align-items-center">
                    <div class="col-lg-6 my-5 my-lg-0">
                        <div class="p-5">
                            <div class="mb-4">
                                <h1 class="display-3 text-primary">30% OFF</h1>
                                <h1 class="text-white">For Online Orders &amp; Reservation</h1>
                            </div>
                            <p class="text-white">Order your favourite food, choose your beverages, book your table and pay — all in one smooth flow. Your exclusive online discount is applied automatically at checkout.</p>
                            <ul class="list-inline text-white m-0">
                                <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Pick items from our full menu</li>
                                <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Reserve your table with date &amp; time</li>
                                <li class="py-2"><i class="fa fa-check text-primary mr-3"></i>Pay securely by Card, UPI or Cash</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-center p-5" style="background: rgba(51, 33, 29, .8);">
                            <h1 class="text-white mb-3 mt-5">Ready to Order?</h1>
                            <p class="text-white mb-4" style="opacity:.8;">Browse our full menu, customise your order and book your table — takes under 2 minutes.</p>
                            <a href="order.php" class="btn btn-primary btn-block font-weight-bold py-3 mb-3" style="font-size:1.1rem;">
                                <i class="fa fa-utensils mr-2"></i> Order Online &amp; Book Table
                            </a>
                            <a href="reservation.php" class="btn btn-outline-light btn-block font-weight-bold py-3">
                                <i class="fa fa-calendar-alt mr-2"></i> Table Reservation Only
                            </a>
                            <p class="text-white mt-4 mb-5" style="font-size:.82rem;opacity:.6;">* 30% discount applies to all online orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid footer text-white mt-5 pt-5 px-0 position-relative overlay-top">
        <div class="row mx-0 pt-5 px-sm-3 px-lg-5 mt-4">
            <div class="col-lg-3 col-md-6 mb-5" style="margin-left: 180px;">
                <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Get In Touch</h4>
                <p><i class="fa fa-map-marker-alt mr-2"></i>Bavdhan</p>
                <p><i class="fa fa-phone-alt mr-2"></i>+91 8855039800</p>
                <p class="m-0"><i class="fa fa-envelope mr-2"></i>dagadepranav21@gmail.com</p>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Follow Us</h4>
                <div class="d-flex justify-content-start">
                    <a class="btn btn-lg btn-outline-light btn-lg-square mr-2" href="#"><i class="fab fa-twitter"></i></a>
                    <a class="btn btn-lg btn-outline-light btn-lg-square mr-2" href="#"><i class="fab fa-facebook-f"></i></a>
                    <a class="btn btn-lg btn-outline-light btn-lg-square mr-2" href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a class="btn btn-lg btn-outline-light btn-lg-square" href="https://www.instagram.com/pranavdagade.21/"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-5">
                <h4 class="text-white text-uppercase mb-4" style="letter-spacing: 3px;">Open Hours</h4>
                <div>
                    <h6 class="text-white text-uppercase">Monday - Friday</h6><p>10.00 AM - 11.00 PM</p>
                    <h6 class="text-white text-uppercase">Saturday - Sunday</h6><p>9.00 AM - 12.00 PM</p>
                </div>
            </div>
        </div>
    </div>

    <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="fa fa-angle-double-up"></i></a>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>
    <script src="js/main.js"></script>

    <!-- AUTH MODAL -->
    <div class="auth-modal-bg" id="authBg" onclick="closeAuthIfBg(event)">
      <div class="auth-modal">
        <button class="close-btn" onclick="closeAuth()">&#10005;</button>

        <!-- LOGIN FORM -->
        <div id="loginForm">
          <h3>Welcome back</h3>
          <p class="sub">Sign in to view your orders and profile.</p>
          <div class="auth-err" id="loginErr"></div>
          <div class="auth-field"><label>Email</label>
            <input type="email" id="li_email" placeholder="you@example.com"></div>
          <div class="auth-field"><label>Password</label>
            <input type="password" id="li_pass" placeholder="Your password"></div>
          <button class="auth-btn" onclick="doLogin()">Sign In</button>
          <div class="auth-switch">No account? <a onclick="switchAuthForm('signup')">Create one</a></div>
        </div>

        <!-- SIGNUP FORM -->
        <div id="signupForm" style="display:none;">
          <h3>Create account</h3>
          <p class="sub">Join Mini Cafe for easy ordering &amp; tracking.</p>
          <div class="auth-err" id="signupErr"></div>
          <div class="auth-field"><label>Full Name</label>
            <input type="text" id="su_name" placeholder="Your full name"></div>
          <div class="auth-field"><label>Email</label>
            <input type="email" id="su_email" placeholder="you@example.com"></div>
          <div class="auth-field"><label>Phone <small style="text-transform:none;font-size:.75rem;color:#9a7850;">(optional)</small></label>
            <input type="tel" id="su_phone" placeholder="+91 00000 00000"></div>
          <div class="auth-field"><label>Password</label>
            <input type="password" id="su_pass" placeholder="At least 6 characters"></div>
          <button class="auth-btn" onclick="doSignup()">Create Account</button>
          <div class="auth-switch">Already have an account? <a onclick="switchAuthForm('login')">Sign in</a></div>
        </div>
      </div>
    </div>

    <div id="idx-toast"></div>

    <script>
    // ── AUTH MODAL ────────────────────────────────────────────
    function openAuth(mode){
      document.getElementById('authBg').classList.add('show');
      switchAuthForm(mode||'login');
    }
    function closeAuth(){ document.getElementById('authBg').classList.remove('show'); }
    function closeAuthIfBg(e){ if(e.target===document.getElementById('authBg')) closeAuth(); }
    function switchAuthForm(m){
      document.getElementById('loginForm').style.display  = m==='login' ?'':'none';
      document.getElementById('signupForm').style.display = m==='signup'?'':'none';
      document.getElementById('loginErr').classList.remove('show');
      document.getElementById('signupErr').classList.remove('show');
    }
    function idxToast(msg,type){
      const t=document.getElementById('idx-toast');
      t.className=type==='err'?'err':'';
      t.textContent=msg; t.style.display='block';
      setTimeout(()=>t.style.display='none',4000);
    }

    function doLogin(){
      const email=$('#li_email').val().trim(), pass=$('#li_pass').val();
      if(!email||!pass){ showErr('loginErr','Please fill all fields.'); return; }
      $.post('auth.php',{action:'login',email,password:pass},function(res){
        if(res.success){ idxToast('Welcome back, '+res.user.name+'! 👋'); setTimeout(()=>location.reload(),900); }
        else showErr('loginErr',res.message);
      },'json').fail(()=>showErr('loginErr','Network error. Try again.'));
    }
    function doSignup(){
      const name=$('#su_name').val().trim(), email=$('#su_email').val().trim();
      const phone=$('#su_phone').val().trim(), pass=$('#su_pass').val();
      if(!name||!email||!pass){ showErr('signupErr','Please fill all required fields.'); return; }
      $.post('auth.php',{action:'signup',name,email,phone,password:pass},function(res){
        if(res.success){ idxToast('Account created! Welcome '+res.user.name+' '+res.user.avatar); setTimeout(()=>location.reload(),900); }
        else showErr('signupErr',res.message);
      },'json').fail(()=>showErr('signupErr','Network error. Try again.'));
    }
    function showErr(id,msg){
      const el=document.getElementById(id);
      el.textContent=msg; el.classList.add('show');
    }

    // Open auth modal if URL has ?auth=login
    window.addEventListener('load',function(){
      const p=new URLSearchParams(location.search);
      if(p.get('auth')) openAuth(p.get('auth'));
    });
    </script>

</body>
</html>
