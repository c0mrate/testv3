<?php
ob_start();
session_start();
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Basic Page Needs
  ================================================== -->
    <meta charset="utf-8">
    <title>Welfare Store</title>

    <!-- Mobile Specific Metas
  ================================================== -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Yem-Yem Supermarket">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta name="author" content="Yem-Yem">
    <meta name="generator" content="Yem-Yem Supermarket">
    <link rel="shortcut icon" type="image/x-icon" href="views/images/favicon.png" />
    <link rel="stylesheet" href="views/plugins/themefisher-font/style.css">
    <link rel="stylesheet" href="views/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="views/plugins/animate/animate.css">
    <link rel="stylesheet" href="views/plugins/slick/slick.css">
    <link rel="stylesheet" href="views/plugins/slick/slick-theme.css">
    <link rel="stylesheet" href="views/css/style.css">

</head>

<?php
require __DIR__ . '/db.php';
$statement = $pdo->prepare("SELECT * FROM categories ORDER BY title");
$statement->execute();
$categories = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<body id="body">
    <section class="top-header">
        <div class="ontest">
            <a>+++&nbsp;</a>
            <a class="hover-with-underline">Website อยู่ในระหว่างการทดสอบและพัฒนา ระบบอาจมีความผิดพลาดเกิดขึ้นได้</a>
            <a>&nbsp;+++</a>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-xs-12 col-sm-6">
                    <ul class="nav navbar-nav nav-header">
                        <!-- <li class="dropdown">
                            <a href="http://www.welfare.kmutnb.ac.th" data-link>Welfare</a>
                        </li> -->
                        <li class="dropdown">
                            <a href="/" data-link>Welfare shop</a>
                        </li>

                        <li class="dropdown dropdown-slide">
                            <a href="#!" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="350" role="button" aria-haspopup="true" aria-expanded="false">สินค้า<span class="tf-ion-ios-arrow-down"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="/products">ทั้งหมด</a></li>
                                <?php foreach ($categories as $category) : ?>
                                    <li><a href="/products?c=<?= htmlspecialchars($category['title']); ?>"><?= htmlspecialchars($category['title']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>

                        <!-- <li class="dropdown ">
                            <a href="/products" data-link>สินค้า</a>
                        </li> -->
                        <li class="dropdown ">
                            <a href="/about" data-link>เกี่ยวกับ</a>
                        </li>
                        <?php if (isset($_SESSION['name'])) : ?>
                            <li class="dropdown dropdown-slide">
                                <a href="#!" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="350" role="button" aria-haspopup="true" aria-expanded="false"><?php echo htmlspecialchars($_SESSION['name']); ?><span class="tf-ion-ios-arrow-down"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/profile">ข้อมูลสมาชิก</a></li>
                                    <li><a href="/logout">ออกจขากระบบ</a></li>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="dropdown dropdown-slide">
                                <a href="#!" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="350" role="button" aria-haspopup="true" aria-expanded="false">สมาชิก <span class="tf-ion-ios-arrow-down"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/login">เข้าสู่ระบบ</a></li>
                                    <li><a href="/register">สมัครสมาชิก</a></li>
                                </ul>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
                <div class="col-md-4 col-xs-12 col-sm-4" style="margin-top: 15px;margin-left: 150px;">
                    <ul class="top-menu text-right list-inline">
                        <li class="dropdown cart-nav dropdown-slide">
                            <a href="#!" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"><i class="tf-ion-android-cart"></i>Cart</a>
                            <div class="dropdown-menu cart-dropdown">

                                <?php if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) : ?>
                                    <div class="media">
                                        <div class="media-body">
                                            <h4 class="media-heading text-center">Cart is empty</h4>
                                        </div>
                                    </div>

                                    <div class="cart-summary">
                                        <span>Total</span>
                                        <span class="total-price">฿ 0.00</span>
                                    </div>
                                    <ul class="text-center cart-buttons">
                                        <li><a href="/cart" class="btn btn-small">View Cart</a></li>
                                    </ul>

                                <?php else : ?>
                                    <?php foreach ($_SESSION['cart'] as $item) : ?>
                                        <div class="media">
                                            <a class="pull-left" href="#!">
                                                <img class="media-object" src="<?= htmlspecialchars($item['image']) ?>" alt="image" />
                                            </a>
                                            <div class="media-body">
                                                <h4 class="media-heading"><a href=""><?= htmlspecialchars($item['title']) ?></a></h4>
                                                <div class="cart-price">
                                                    <span><?= htmlspecialchars($item['quantity']) ?> x</span>
                                                    <span><?= number_format($item['price'], 2) ?></span>
                                                </div>
                                                <h5><strong>฿ <?= number_format($item['quantity'] * $item['price'], 2) ?></strong></h5>
                                            </div>
                                            <a href="/cart-remove-item?id=<?= htmlspecialchars($item['id']) ?>"><i class="tf-ion-close"></i></a>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="cart-summary">
                                        <span>Total</span>
                                        <span class="total-price">฿<?php
                                                                    $total = 0;
                                                                    foreach ($_SESSION['cart'] as $item) {
                                                                        $total += $item['price'] * $item['quantity'];
                                                                    }
                                                                    echo number_format($total, 2);
                                                                    ?>
                                        </span>
                                    </div>
                                    <ul class="text-center cart-buttons">
                                        <li><a href="/cart" class="btn btn-small" data-link>View Cart</a></li>
                                    </ul>
                                <?php endif ?>
                            </div>
                        </li>
                    </ul><!-- / .nav .navbar-nav .navbar-right -->
                </div>
            </div>
        </div>
    </section><!-- End Top Header Bar -->