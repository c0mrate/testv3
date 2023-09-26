<?php
require __DIR__ . '/../csrf.php';
require __DIR__ . '/header.php';
require __DIR__ . '/db.php';


$items;
$statement = $pdo->prepare("SELECT * FROM products ORDER BY update_time DESC LIMIT 4");
$statement->execute();
if ($statement->rowCount() > 0) {
	$items = $statement->fetchAll(PDO::FETCH_ASSOC);
}

$products;
$searchEmpty = false;
$page = 1;
$results_per_page = 12;
$page_first_result;
$number_of_pages;

$statement = $pdo->prepare("SELECT * FROM categories ORDER BY title");
$statement->execute();
$categories = $statement->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_GET['p'])) {
	$page = 1;
} else {
	$page = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_NUMBER_INT);
}

if (isset($_POST['q']) && isset($_GET['c']) && CSRF::validateToken($_POST['token'])) {
	$query = filter_input(INPUT_POST, 'q');
	$category = filter_input(INPUT_GET, 'c');
	$statement = $pdo->prepare("SELECT * FROM products WHERE category='$category' AND CONCAT(`title`, `price`, `description`, `category`) LIKE '%$query%'");
	$statement->execute();
	if ($statement->rowCount() > 0) {
		$products = $statement->fetchAll(PDO::FETCH_ASSOC);
	} else {
		$searchEmpty = true;
	}
} elseif (isset($_POST['q']) && CSRF::validateToken($_POST['token'])) {
	$query = filter_input(INPUT_POST, 'q');
	$statement = $pdo->prepare("SELECT * FROM products WHERE CONCAT(`title`, `price`, `description`, `category`) LIKE '%$query%'");
	$statement->execute();
	if ($statement->rowCount() > 0) {
		$products = $statement->fetchAll(PDO::FETCH_ASSOC);
	} else {
		$searchEmpty = true;
	}
} elseif (isset($_GET['c'])) {
	$page_first_result = ($page - 1) * $results_per_page;
	$statement = $pdo->prepare("SELECT count(*) FROM products WHERE category=?");
	$statement->execute(array(filter_input(INPUT_GET, 'c')));
	$number_of_result = $statement->fetchColumn();
	$number_of_pages = ceil($number_of_result / $results_per_page);

	$statement = $pdo->prepare("SELECT * FROM products WHERE category=? LIMIT $page_first_result, $results_per_page");
	$statement->execute(array(filter_input(INPUT_GET, 'c')));
	if ($statement->rowCount() > 0) {
		$products = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
} else {
	$page_first_result = ($page - 1) * $results_per_page;
	$statement = $pdo->prepare("SELECT count(*) FROM products");
	$statement->execute();
	$number_of_result = $statement->fetchColumn();
	$number_of_pages = ceil($number_of_result / $results_per_page);
	$statement = $pdo->prepare("SELECT * FROM products LIMIT $page_first_result, $results_per_page");
	$statement->execute();
	if ($statement->rowCount() > 0) {
		$products = $statement->fetchAll(PDO::FETCH_ASSOC);
	}
}

?>

<section class="products bg-gray">
	<div class="container_">
		<?php if (isset($items) && !empty($items)) : ?>
			<div class="slideshow-container">
				<?php foreach ($items as $item) : ?>
					<?php $images = unserialize($item['images']); ?>
					<?php foreach ($images as $index => $image) : ?>
						<div class="slideshow-image-container <?= $index === 0 ? 'active-slide' : '' ?>">
							<img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="slideshow-image">
							<div class="slide-content">
								<div class="slide-text">
									ร้านค้าสวัสดิการ
								</div>
								<div class="slide-text-msg">
									ศูนย์ส่งเสริมสวัสดิการและสิ่งจูงใจ มหาวิทยาลัยเทคโนโลยีพระจอมเกล้าพระนครเหนือ
								</div>
								<button class="slide-button" onclick="gotostore()">ดูสินค้าทั้งหมด</button>
								<script>
									function gotostore() {
										window.location.href = "/products"
									}
								</script>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</div>
		<?php endif ?>
	</div>
</section>

<section class="products section bg-gray">
	<div class="container">
		<div class="row">
			<div class="title text-center">
				<h1>สินค้าใหม่</h1>
			</div>
		</div>
		<div class="row">
			<?php if (isset($items)) : ?>
				<?php foreach ($items as $item) : ?>
					<div class="col-md-3">
						<div class="product-item">
							<div class="product-thumb">
								<img class="img-responsive" src="<?= htmlspecialchars(unserialize($item['images'])[0]) ?>" alt="<?= htmlspecialchars($item['title']) ?>" />
							</div>
							<div class="product-content-new">
								<h4><a class="sec23a" href="/item?id=<?= htmlspecialchars($item['id']) ?>"><?= htmlspecialchars($item['title']) ?></a></h4>
								<p class="sec23a"><?= number_format($item['price'], 2) ?> ฿ THB</p>
								<button class="btn btn-main btn-small"><a href="/item?id=<?= htmlspecialchars($item['id']) ?>" class="buy-button">หยิบใส่ตะกร้า</a></button>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php endif ?>
		</div>
	</div>
</section>

<section class="products section space-top">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<div class="widget product-category">
					<h4 class="widget-title">Categories</h4>
					<div class="panel-group commonAccordion" id="accordion" role="tablist" aria-multiselectable="true">
						<div class="panel panel-default">
							<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
								<div class="panel-body">
									<ul>
										<li><a href="/products">ทั้งหมด</a></li>
										<?php foreach ($categories as $category) : ?>
											<li><a href="/products?c=<?= htmlspecialchars($category['title']); ?>"><?= htmlspecialchars($category['title']); ?></a></li>
										<?php endforeach; ?>
									</ul>
								</div>
							</div>
						</div>
						<br>
						<?php if (isset($_GET['c'])) : ?>
							<form action="/products?c=<?= filter_input(INPUT_GET, 'c') ?>" method="post">
								<?php CSRF::csrfInputField() ?>
								<div class="form-group">
									<input name="q" type="search" class="form-control" placeholder="Search...">
								<?php else : ?>
									<form action="/products" method="post">
										<?php CSRF::csrfInputField() ?>
										<div class="form-group">
											<input name="q" type="search" class="form-control" placeholder="Search...">
										<?php endif ?>
										</div>
										<div class="text-center">
											<button name="search" type="submit" class="btn btn-main btn-small">Search</button>
										</div>
									</form>
								</div>
					</div>
				</div>
				<div class="col-md-9">
					<div class="row">
						<?php if (!$searchEmpty) : ?>
							<?php foreach ($products as $product) : ?>
								<div class="col-md-4">
									<div class="product-item">
										<div class="product-thumb">
											<!--<span class="bage">Sale</span>-->
											<img class="img-responsive" src="<?= htmlspecialchars(unserialize($product['images'])[0]) ?>" alt="product-img" />
										</div>
										<div class="product-content">
											<h4><a class="hover-with-underline" href="/item?id=<?= htmlspecialchars($product['id']) ?>"><?= htmlspecialchars($product['title']) ?></a></h4>
											<p class="price"><?= number_format($product['price'], 2) ?> ฿ THB</p>
											<button class="btn btn-main btn-small"><a href="/item?id=<?= htmlspecialchars($product['id']) ?>" class="buy-button">หยิบใส่ตะกร้า</a></button>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php else : ?>
							<div class="col-md-6 col-md-offset-3">
								<div class="block text-center">
									<i class="tf-ion-ios-cart-outline"></i>
									<h2 class="text-center">No items found.</h2>
									<a href="/products" class="btn btn-main mt-20">Return to shop</a>
								</div>
							</div>
						<?php endif ?>
					</div>
				</div>
			</div>
			<?php if (!isset($_POST['q'])) : ?>
				<div class="row">
					<div class="col-sm-12 text-center">
						<?php
						if (isset($_GET['c'])) {
							if ($page == 1) {
								for ($i = $page; $i <= $number_of_pages; $i++) {
									echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $i . '">' . $i . '</a>';
									if ($i == 3) {
										break;
									}
								}
							} elseif ($page == $number_of_pages) {
								if ($page - 3 > 0) {
									echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $page - 2 . '">' . $page - 2 . ' </a>';
									echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $page - 1 . '">  ' . $page - 1 . ' </a>';
									echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $page  . '">  ' . $page . '</a>';
								} elseif ($page - 2 > 0) {
									echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $page - 1 . '">  ' . $page - 1 . ' </a>';
									echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $page . '">  ' . $page . ' </a>';
								} else {
									echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $page . '">  ' . $page . ' </a>';
								}
							} else {
								echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $page - 1 . '">  ' . $page - 1 . ' </a>';
								echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $page . '">  ' . $page . ' </a>';
								echo '<a href="/products?c=' . filter_input(INPUT_GET, 'c') . '&p=' . $page + 1 . '">  ' . $page + 1 . ' </a>';
							}
						} else {
							if ($page == 1) {
								for ($i = $page; $i <= $number_of_pages; $i++) {
									echo '<a href="/products?p=' . $i . '">' . $i . '</a>';
									if ($i == 3) {
										break;
									}
								}
							} elseif ($page == $number_of_pages) {
								if ($page - 3 > 0) {
									echo '<a href="/products?p=' . $page - 2 . '">  ' . $page - 2 . ' </a>';
									echo '<a href="/products?p=' . $page - 1 . '">  ' . $page - 1 . ' </a>';
									echo '<a href="/products?p=' . $page  . '">  ' . $page . '</a>';
								} elseif ($page - 2 > 0) {
									echo '<a href="/products?p=' . $page - 1 . '">  ' . $page - 1 . ' </a>';
									echo '<a href="/products?p=' . $page . '">  ' . $page . ' </a>';
								} else {
									echo '<a href="/products?p=' . $page . '">  ' . $page . ' </a>';
								}
							} else {
								echo '<a href="/products?p=' . $page - 1 . '">  ' . $page - 1 . ' </a>';
								echo '<a href="/products?p=' . $page . '">' . $page . ' </a>';
								echo '<a href="/products?p=' . $page + 1 . '">  ' . $page + 1 . ' </a>';
							}
						}
						?>
					</div>
				</div>
			<?php endif ?>
		</div>
</section>



<section class="call-to-action bg-gray section">
	<div class="container">
		<div class="row">
			<div class="col-md-12 text-center">
				<div class="title">
					<h2>SUBSCRIBE TO NEWSLETTER</h2>
					<h4>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fugiat, <br> facilis numquam impedit ut sequi. Minus facilis vitae excepturi sit laboriosam.</h4>
				</div>
				<div class="col-lg-6 col-md-offset-3">
					<div class="input-group subscription-form">
						<input type="text" class="form-control" placeholder="Enter Your Email Address">
						<span class="input-group-btn">
							<button class="btn btn-main" type="button">Subscribe Now!</button>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	const slideshowImageContainers = document.querySelectorAll('.slideshow-image-container');
	let currentImageIndex = 0;

	function showImage(index) {
		slideshowImageContainers.forEach((container, i) => {
			if (i === index) {
				container.classList.add('active-slide');
			} else {
				container.classList.remove('active-slide');
			}
		});
	}

	function changeImage() {
		currentImageIndex = (currentImageIndex + 1) % slideshowImageContainers.length;
		showImage(currentImageIndex);
	}
	showImage(currentImageIndex);
	setInterval(changeImage, 6000);
</script>
<?php require __DIR__ . '/footer.php'; ?>