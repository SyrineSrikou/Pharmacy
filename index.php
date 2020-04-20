<?php 
session_start();
require_once("config/db.php"); 

require_once('templates/header.php'); 

function getProduits($pdo) {
	$stm = $pdo->prepare("SELECT id, image_path, nom, prix FROM produits LIMIT 8");
	$stm->execute();

	$result = $stm->fetchAll();
	return $result;
}

function getRandomCategoryWithProduit($pdo) {
	$random_cat = mt_rand(1, 7);
	$stm = $pdo->prepare("SELECT p.id as pid, p.image_path, p.nom as pnom, c.nom cnom FROM Produits p LEFT JOIN categories c ON (c.id = p.category) WHERE category = :category LIMIT 3");
	$stm->bindParam(":category", $random_cat, PDO::PARAM_INT);
	$stm->execute();

	$result = $stm->fetchAll();
	return $result;
}

function getOffres($pdo) {
	$stm = $pdo->prepare("SELECT id, nom, image_path FROM offres LIMIT 4");
	$stm->execute();

	$result = $stm->fetchAll();
	return $result;
}
?>

<body>
	<?php require_once("templates/header_body.php"); ?>

	<!-- ========================= SECTION INTRO ========================= -->
	<div id="myCarousel" class="carousel slide" data-ride="carousel">
		<ol class="carousel-indicators">
			<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
			<li data-target="#myCarousel" data-slide-to="1"></li>
			<li data-target="#myCarousel" data-slide-to="2"></li>
		</ol>
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img src="images/c1.png" alt="c1" width="100%" height="100%">
			</div>
			<div class="carousel-item">
				<img src="images/c2.png" alt="c2">
			</div>
			<div class="carousel-item">
				<img src="images/c3.png" alt="c3" width="100%" height="100%">
			</div>
		</div>
		<a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		</a>
	</div>

	<!-- ========================= SECTION INTRO END// ========================= -->

	<!-- ========================= SECTION CONTENT ========================= -->
	<section class="section-content padding-y-sm bg">
		<div class="container">
			<?php
			$offres = getOffres($pdo);
			?>
			<header class="section-heading heading-line">
				<h4 class="title-section bg"> Offres </h4>
			</header>

			<div class="card">
				<div class="row no-gutters random-produit">
					<div class="col-md-12">
						<ul class="row no-gutters border-cols">
							<?php
							foreach($offres as $offre) {
								$format = '
								<li class="col-6 col-md-3">
									<a href="details.php?id=%s" class="itembox">
										<div class="card-body">
											<p> %s </p>
											<img class="img-md" src="%s">
										</div>
									</a>
								</li>';
								echo sprintf($format, $offre['id'], $offre['nom'], $offre['image_path']);
							}
							?>
						</ul>
					</div> <!-- col.// -->
				</div> <!-- row.// -->

			</div> <!-- card.// -->

		</div> <!-- container .//  -->
	</section>
	<!-- ========================= SECTION CONTENT END// ========================= -->


	<!-- ========================= SECTION CONTENT ========================= -->
	<section class="section-content padding-y-sm bg">
		<div class="container">
			<?php
			$produits = getRandomCategoryWithProduit($pdo);
			$cat = $produits[0]['cnom']; 
			?>
			<header class="section-heading heading-line">
				<h4 class="title-section bg"> <?php echo $cat; ?></h4>
			</header>

			<div class="card">
				<div class="row no-gutters random-produit">
					<div class="col-md-3">

						<article href="#" class="card-banner h-100 bg2">
							<div class="card-body zoom-wrap">
								<h5 class="title">Lorem ipsum dolor sit amet</h5>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis lobortis, metus sed
									consequat rhoncus, lacus ipsum semper tortor, ultrices iaculis elit lacus dapibus
									mauris. </p>
					
							</div>
						</article>

					</div> <!-- col.// -->
					<div class="col-md-9">
						<ul class="row no-gutters border-cols">
							<?php
							foreach($produits as $produit) {
								$format = '
								<li class="col-4">
									<a href="details.php?id=%s" class="itembox">
										<div class="card-body">
											<p> %s </p>
											<img class="img-md" src="%s">
										</div>
									</a>
								</li>';
								echo sprintf($format, $produit['pid'], $produit['pnom'] , $produit['image_path']);
							}
							?>
						</ul>
					</div> <!-- col.// -->
				</div> <!-- row.// -->

			</div> <!-- card.// -->

		</div> <!-- container .//  -->
	</section>
	<!-- ========================= SECTION CONTENT END// ========================= -->

	<!-- ========================= SECTION ITEMS ========================= -->
	<section class="section-request bg padding-y-sm">
		<div class="container">
			<header class="section-heading heading-line">
				<h4 class="title-section bg text-uppercase">Produits recommandés</h4>
			</header>

			<div class="row-sm">
				<?php 
				$produits = getProduits($pdo);
				foreach($produits as $produit) {
					echo "
					<div class='col-md-3'>
						<figure class='card card-product'>
							<div class='img-wrap'> <img src='$produit[image_path]'></div>
							<figcaption class='info-wrap'>
								<h6 class='title'><a href='details.php?id=$produit[id]'>$produit[nom]</a></h6>

								<div class='price-wrap'>
									<span class='price-new'>$produit[prix] TND</span>
								</div> <!-- price-wrap.// -->

							</figcaption>
						</figure> <!-- card // -->
					</div> <!-- col // -->";
				}
				?>
			</div> <!-- row.// -->
		</div><!-- container // -->
	</section>
	<!-- ========================= SECTION ITEMS .END// ========================= -->

	<?php require_once('templates/footer.php'); ?>
</body>

</html>