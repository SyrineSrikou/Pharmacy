<?php

// if the category doesnt exists in url then we redirect back to home page
if (!isset($_GET['c']) || empty($_GET['c'])) {
	header('Location: /');
	exit;
}

session_start();
require_once("config/db.php"); 

require_once("templates/header.php");
if (isset($_POST) && !empty($_POST)) {
	$item_id = $_POST['item_id'];
	$currentLocation = $_SERVER['REQUEST_URI'];
	if (empty($_SESSION['cart'])) {
		$_SESSION['cart'] = array();
		$_SESSION['cart'][$item_id] = 1;
	} else {
		if (array_key_exists($item_id, $_SESSION['cart'])) {
			$_SESSION['cart'][$item_id] += 1;
		} else {
			$_SESSION['cart'][$item_id] = 1;
		}
	}
	
	header("Location: $currentLocation");
	exit;
}

function getProduits($pdo) {
	$url_name = $_GET['c'];
	$stm = $pdo->prepare("SELECT p.id, p.nom, p.description, p.prix, p.category, p.image_path, IFNULL(FLOOR(AVG(r.rating)), 0) as rating, COUNT(o.id) as total_orders, COUNT(r.id) as reviews FROM produits p LEFT JOIN reviews r ON (r.item_id = p.id) LEFT JOIN categories c ON (c.id = p.category) LEFT JOIN orderItems o ON (o.item_id = p.id) WHERE c.url_name = :url_name GROUP BY p.id");
	$stm->bindParam(":url_name", $url_name);
	$stm->execute();

	$result = $stm->fetchAll();
	$count = count($result);
	return ["result" => $result, "count" => $count];
}

?>

<body>
	<?php require_once("templates/header_body.php"); ?>
	<!-- ========================= SECTION CONTENT ========================= -->
	<section class="section-content bg padding-y-sm">
		<div class="container">
			<div class="padding-y-sm">
				<?php 
				$array = getProduits($pdo);
				echo "<span>$array[count] results </span>";
			?>
			</div>

			<div class="row-sm">
				<?php
				foreach($array["result"] as $row) {
					$rating = 20 * $row['rating'];
					$rating = $rating . "%";
					$description = substr($row['description'], 0, 300) . '...';
					$format = '
					<article class="card card-product" style="min-width:100%%">
						<div class="card-body">
							<div class="row">
								<aside class="col-sm-3">
									<div class="img-wrap"><img src="%s"></div>
								</aside> <!-- col.// -->
								<article class="col-sm-6">
									<h4 class="title"> %s </h4>
									<div class="rating-wrap">
										<ul class="rating-stars">
											<li style="width:%s" class="stars-active"> 
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
										<div class="label-rating"> %s reviews</div>
										<div class="label-rating"> %s orders </div>
									</div> <!-- rating-wrap.// -->
									<p> %s </p>
								</article> <!-- col.// -->
								<aside class="col-sm-3 border-left">
									<div class="action-wrap">
										<div class="price-wrap h4">
											<span class="price"> TND %s </span>	
										</div> <!-- info-price-detail // -->
										<p class="text-success">Free shipping</p>
										<br>
										
											<form class="form-inline" method="post">
												<input type="submit" class="btn btn-primary" value="Add to cart" />
												<input type="hidden" value="%s" name="item_id" />
												<a href="details.php?id=%s" class="btn btn-secondary"> Details  </a>
											</form>
										
									</div> <!-- action-wrap.// -->
								</aside> <!-- col.// -->
							</div> <!-- row.// -->
						</div> <!-- card-body .// -->
					</article> <!-- product-list.// -->';
					echo sprintf($format, $row['image_path'], $row['nom'], $rating, $row['reviews'], $row['total_orders'], $description, $row['prix'], $row['id'], $row['id']);
				}
				?>
			</div> <!-- row.// -->
		</div><!-- container // -->
	</section>
	<!-- ========================= SECTION CONTENT .END// ========================= -->



	<?php require_once("templates/footer.php"); ?>
</body>

</html>