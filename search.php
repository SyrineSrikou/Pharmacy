<?php
session_start();
require_once("config/db.php"); 

require_once("templates/header.php");

// if the query doesnt exists in url then we redirect back to home page
if (!isset($_GET['q']) || empty($_GET['q'])) {
	header('Location: /');
	exit;
}

function searchProduit($pdo) {
	$stm = $pdo->prepare("SELECT p.id, p.image_path, p.nom, p.description, p.prix, p.category, IFNULL(CEIl(AVG(r.rating)), 0) as rating, COUNT(o.id) as total_orders, COUNT(r.id) as reviews FROM produits p LEFT JOIN reviews r ON (r.item_id = p.id) LEFT JOIN categories c ON (c.id = p.category) LEFT JOIN orderItems o ON (o.item_id = p.id) WHERE p.nom like '$_GET[q]%' GROUP BY p.id ");
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
				$array = searchProduit($pdo);
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
						<article class="card card-product">
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
									<p>
										<a href="#" class="btn btn-primary add-to-cart"> Add to cart </a>
										<a href="details.php?id=%s" class="btn btn-secondary"> Details  </a>
									</p>
								</div> <!-- action-wrap.// -->
							</aside> <!-- col.// -->
						</div> <!-- row.// -->
						</div> <!-- card-body .// -->
					</article> <!-- product-list.// -->';
					echo sprintf($format, $row['image_path'], $row['nom'], $rating, $row['reviews'], $row['total_orders'], $description, $row['prix'], $row['id']);
				}
				?>
			</div> <!-- row.// -->
		</div><!-- container // -->
	</section>
	<!-- ========================= SECTION CONTENT .END// ========================= -->

	<?php
	$produits = searchProduit($pdo);
	?>

	<?php require_once("templates/footer.php"); ?>
</body>

</html>