<?php


// if the product id doesnt exists in url then we redirect back to home page
if (!isset($_GET['id']) || empty($_GET['id'])) {
	header('Location: /');
	exit;
}

session_start();
require_once("config/db.php"); 

require_once('templates/header.php');

if (isset($_POST) && !empty($_POST)) {

	$action = $_POST['action'];
	switch ($action) {
		case 'cart':
			addToCart($_POST);
			break;
		case 'review':
			addReview($pdo, $_POST);
			break;

		default:
			break;
	}
	
}

function addToCart($post) {
	$item_id = $_GET['id'];
	$currentLocation = $_SERVER['REQUEST_URI'];
	if (empty($_SESSION['cart'])) {
		$_SESSION['cart'] = array();
		$_SESSION['cart'][$item_id] = $post['quantity'];
	} else {
		if (array_key_exists($item_id, $_SESSION['cart'])) {
			$_SESSION['cart'][$item_id] += $post['quantity'];
		} else {
			$_SESSION['cart'][$item_id] = $post['quantity'];
		}
	}
	
	header("Location: $currentLocation");
	exit;
}

function addReview($pdo, $post) {
	$review = $post['review'];
	$rating = $post['rating'];
	$user = $_SESSION['user_id'];
	$produit = $post['item_id'];

	$stm = $pdo->prepare("INSERT INTO reviews (reviewer_id, item_id, message, rating) VALUES (:reviewer_id, :item_id, :message, :rating)");
	$stm->bindParam(":reviewer_id", $user, PDO::PARAM_INT);
	$stm->bindParam(":item_id", $produit, PDO::PARAM_INT);
	$stm->bindParam(":message", $review, PDO::PARAM_STR);
	$stm->bindParam(":rating", $rating, PDO::PARAM_INT);

	$stm->execute();

	$currentLocation = $_SERVER['REQUEST_URI'];
	header("Location: $currentLocation");
	exit;
}

function getProduitById($pdo, $id) {
	$stm = $pdo->prepare("SELECT p.id, p.nom, p.prix, p.description, p.image_path, COUNT(r.id) as total_reviews, FLOOR(AVG(r.rating)) as rating, COUNT(o.id) as total_orders FROM produits p LEFT JOIN reviews r ON (r.item_id = p.id) LEFT JOIN orderItems o ON (o.item_id = p.id) WHERE p.id = :id");
	$stm->bindParam(':id', $id);
	$stm->execute();

	$result = $stm->fetch();
	return $result;
}

function getReviews($pdo) {
	$stm = $pdo->prepare("SELECT r.message, r.rating, u.username FROM reviews r LEFT JOIN users u On (r.reviewer_id = u.id) WHERE item_id = :id");
	$stm->bindParam(":id", $_GET['id'], PDO::PARAM_INT);
	$stm->execute();

	$result = $stm->fetchAll();
	return $result;
}

function getOrders($pdo) {
	$stm = $pdo->prepare("SELECT count(*) FROM orderItems WHERE item_id = :id");
	$stm->bindParam(':id', $id);
	$stm->execute();

	$result = $stm->fetch();
	return $result;
}

?>

<body>
	<?php require_once("templates/header_body.php"); ?>

	<!-- ========================= SECTION CONTENT ========================= -->
	<section class="section-content bg padding-y-sm">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<?php $produit = getProduitById($pdo, $_GET['id']); ?>
					<main class="card">
						<div class="row no-gutters">
							<aside class="col-sm-6 border-right">
								<article class="gallery-wrap">
									<div class="img-big-wrap">
										<div>
											<a href="<?php echo $produit['image_path']; ?>" data-fancybox="">
												<img src="<?php echo $produit['image_path']; ?>"></a>
										</div>
									</div> <!-- slider-product.// -->

								</article> <!-- gallery-wrap .end// -->
							</aside>
							<aside class="col-sm-6">
								<article class="card-body">
									<!-- short-info-wrap -->
									<h3 class="title mb-3"><?php echo $produit['nom']; ?></h3>

									<div class="mb-3">
										<var class="price h3 text-warning">
											<span>TND </span>
											<span><?php echo $produit['prix']; ?></span>
										</var>
									</div> <!-- price-detail-wrap .// -->
									<dl>
										<dt>Description</dt>
										<dd>
											<p> <?php echo $produit['description']; ?> </p>
										</dd>
									</dl>

									<div class="rating-wrap">

										<ul class="rating-stars">
											<?php $rating = sprintf("width:%s%%", 20 * $produit['rating']); ?>
											<li style="<?php echo $rating ?>" class="stars-active">
												<i class="fa fa-star"></i> <i class="fa fa-star"></i>
												<i class="fa fa-star"></i> <i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</li>
											<li>
												<i class="fa fa-star"></i> <i class="fa fa-star"></i>
												<i class="fa fa-star"></i> <i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</li>
										</ul>
										<div class="label-rating"><?php echo "$produit[total_reviews] reviews" ?></div>
										<div class="label-rating"><?php echo "$produit[total_orders] orders" ?></div>
									</div> <!-- rating-wrap.// -->
									<hr>
									<form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>"
										method="post">
										<div class="row">
											<div class="col-sm-6">
												<dl class="dlist-inline">

													<dt>Quantity: </dt>
													<dd>
														<input type="number" name="quantity" class="form-control"
															style="width:100px;" />
													</dd>
												</dl> <!-- item-property .// -->
											</div> <!-- col.// -->
											<input type="hidden" name="action" value="cart">
											<button type="submit"  class="btn btn-warning" id="btncart">
												<i class="fa fa-shopping-cart"></i>
												Add to cart
											</button>
										</div> <!-- row.// -->
									</form>
									<!-- short-info-wrap .// -->
								</article> <!-- card-body.// -->
							</aside> <!-- col.// -->
						</div> <!-- row.// -->
					</main> <!-- card.// -->

					<div class="review-form">
						<form action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>" method="post">
							<div class="form-group input-group">
								<textarea name="review" class="form-control" placeholder="write review"
									required></textarea>
								<input type="hidden" name="item_id" value="<?php echo $_GET['id'] ?>" />
								<input type="hidden" name="action" value="review" />
							</div> <!-- form-group// -->
							<div class="row">
								<div class="col-4">
									<input placeholder="rating" class="form-control" type="number" name="rating" min="0"
										max="5" id="rating" />
								</div>
								<div class="col-8">
									<input type="submit" class="btn btn-primary form-control" value="submit" />
								</div>
							</div>
						</form>
					</div>
					<div class="user-reviews">
						<!-- PRODUCT REVIEWS -->
						<h4>Reviews: </h4>
						<?php
					if ($produit['total_reviews'] == 0) echo "<p>no reviews</p>";
					else {
						$reviews = getReviews($pdo);
						foreach($reviews as $review) {
							$rating = sprintf("width:%s%%", 20 * $review['rating']);
							$stars_format = '<ul class="rating-stars">
											<li style="%s" class="stars-active">
												<i class="fa fa-star"></i> <i class="fa fa-star"></i>
												<i class="fa fa-star"></i> <i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</li>
											<li>
												<i class="fa fa-star"></i> <i class="fa fa-star"></i>
												<i class="fa fa-star"></i> <i class="fa fa-star"></i>
												<i class="fa fa-star"></i>
											</li>
										</ul>';
							$stars = sprintf($stars_format, $rating);
							$format = '<div class="card">
							<div class="card-header">
							%s
							<div class="float-right">
							%s
							</div>
							</div>
							<div class="card-body">
							<p class="card-text"> %s </p>
							</div>
							</div>';
							echo sprintf($format, $review['username'], $stars, $review['message']);
						}
					}
					?>
						<!-- PRODUCT REVIEWS .// -->
					</div>

				</div> <!-- col // -->
			</div> <!-- row.// -->



		</div><!-- container // -->
	</section>
	<!-- ========================= SECTION CONTENT .END// ========================= -->

	<?php require_once('templates/footer.php'); ?>
</body>

</html>