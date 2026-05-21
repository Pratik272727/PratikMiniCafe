<?php session_start(); $page_title = 'Menu'; $active_page = 'menu'; ?>
<!DOCTYPE html>
<html lang="en">
<head><?php include 'includes/head.php'; ?>
<?php include 'includes/auth_styles.php'; ?>
<style>
.menu-img-wrap{width:80px;height:80px;flex-shrink:0;border-radius:50%;overflow:hidden;border:2px solid rgba(212,160,23,.3);}
.menu-img-wrap img{width:100%;height:100%;object-fit:cover;display:block;}
.menu-item-row{display:flex;align-items:center;gap:16px;margin-bottom:30px;}
.menu-item-info{flex:1;}
.menu-item-price{font-size:1rem;font-weight:700;color:#d4a017;margin-top:4px;}
</style>
</head>
<body>
<?php include 'includes/navbar.php'; ?>

    <!-- Page Header -->
    <div class="container-fluid page-header mb-5 position-relative overlay-bottom">
        <div class="d-flex flex-column align-items-center justify-content-center pt-0 pt-lg-5" style="min-height: 400px">
            <h1 class="display-4 mb-3 mt-0 mt-lg-5 text-white text-uppercase">Menu</h1>
            <div class="d-inline-flex mb-lg-5">
                <p class="m-0 text-white"><a class="text-white" href="index.php">Home</a></p>
                <p class="m-0 text-white px-2">/</p>
                <p class="m-0 text-white">Menu</p>
            </div>
        </div>
    </div>

    <!-- Menu -->
    <div class="container-fluid pt-5">
        <div class="container">
            <div class="section-title">
                <h4 class="text-primary text-uppercase" style="letter-spacing: 5px;">Menu &amp; Pricing</h4>
                <h1 class="display-4">Competitive Pricing</h1>
            </div>

            <?php
            $menu = [
                'Coffee'   => [
                    ['Black Coffee',         80,  'img/menu-1.jpg'],
                    ['Chocolate Coffee',     100, 'img/menu-2.jpg'],
                    ['Coffee With Milk',     150, 'img/menu-3.jpg'],
                ],
                'Fries'    => [
                    ['Salted Fries',         130, 'img/POS.jpg'],
                    ['Peri Peri Fries',      180, 'img/OIP.jpg'],
                    ['Jalapeno Fries',       130, 'img/POK.jpg'],
                ],
                'Sandwich' => [
                    ['Cheese Sandwich',      220, 'img/POJ.jpg'],
                    ['Paneer Sandwich',      250, 'img/POL.jpg'],
                    ['Pune Special Sandwich',300, 'img/POI.jpg'],
                ],
                'Burger'   => [
                    ['Aloo Tikki Burger',    180, 'img/BOI.jpg'],
                    ['Cheese Burger',        230, 'img/BOO.webp'],
                    ['Pune Special Burger',  330, 'img/BIO.webp'],
                ],
                'Pizza'    => [
                    ['Margherita Pizza',     280, 'img/POH.jpg'],
                    ['Veg Classic Pizza',    330, 'img/POU.jpg'],
                    ['Mexican Green Pizza',  380, 'img/POY.jpg'],
                ],
                'Dessert'  => [
                    ['Cheese Cake',          180, 'img/OKL.jpg'],
                    ['Hot Sizzling Brownie', 240, 'img/LKM.jpg'],
                    ['Donut',                130, 'img/LKI.jpg'],
                ],
                'Mocktail' => [
                    ['Ice Tea',              100, 'img/MNM.jpg'],
                    ['Virgin Mojito',        140, 'img/MNN.jpg'],
                    ['Blue Mojito',          180, 'img/NMM.jpg'],
                ],
                'Shakes'   => [
                    ['Oreo Shake',           120, 'img/HJH.jpg'],
                    ['Black Forest Shake',   140, 'img/JJJ.jpg'],
                    ['Hazelnut Shake',       160, 'img/JKJ.jpg'],
                ],
            ];

            $cats = array_keys($menu);
            // Pair categories for 2-column layout
            for ($i = 0; $i < count($cats); $i += 2):
                $leftCat  = $cats[$i];
                $rightCat = $cats[$i+1] ?? null;
            ?>
            <div class="row mb-5">
                <div class="col-lg-6">
                    <h1 class="mb-4"><?= $leftCat ?></h1>
                    <?php foreach ($menu[$leftCat] as [$name,$price,$img]): ?>
                    <div class="menu-item-row">
                        <div class="menu-img-wrap">
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy" onerror="this.src='img/menu-1.jpg'">
                        </div>
                        <div class="menu-item-info">
                            <h5 class="mb-1"><?= htmlspecialchars($name) ?></h5>
                            <p class="m-0" style="font-size:.85rem;color:#9a7850;">Fresh &amp; made to order</p>
                            <div class="menu-item-price">&#8377;<?= $price ?></div>
                        </div>
                        <a href="order.php" class="btn btn-sm btn-primary">Order</a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if ($rightCat): ?>
                <div class="col-lg-6">
                    <h1 class="mb-4"><?= $rightCat ?></h1>
                    <?php foreach ($menu[$rightCat] as [$name,$price,$img]): ?>
                    <div class="menu-item-row">
                        <div class="menu-img-wrap">
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($name) ?>" loading="lazy" onerror="this.src='img/menu-1.jpg'">
                        </div>
                        <div class="menu-item-info">
                            <h5 class="mb-1"><?= htmlspecialchars($name) ?></h5>
                            <p class="m-0" style="font-size:.85rem;color:#9a7850;">Fresh &amp; made to order</p>
                            <div class="menu-item-price">&#8377;<?= $price ?></div>
                        </div>
                        <a href="order.php" class="btn btn-sm btn-primary">Order</a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endfor; ?>

        </div>
    </div>

    <!-- Order CTA -->
    <div class="container-fluid py-5 text-center" style="background:rgba(212,160,23,.06);border-top:1px solid rgba(212,160,23,.15);">
        <h2 class="text-white mb-3">Ready to order? Get <span class="text-primary">30% OFF</span> online!</h2>
        <a href="order.php" class="btn btn-primary font-weight-bold py-3 px-5"><i class="fa fa-utensils mr-2"></i>Order Online Now</a>
    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
