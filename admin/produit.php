<?php 

require_once('templates/header.php');

if (isset($_POST) && !empty($_POST)) {
	createProduit($pdo, $_POST);
}

function createProduit($pdo, $produit) {
	$currentLocation = $_SERVER['REQUEST_URI'];
	$img_link = uploadImage();
	if (!$img_link) {
		header("Location: $currentLocation ");
		exit;
	} else {
		$stm = $pdo->prepare("INSERT INTO produits (nom, description, prix, category, image_path) values (:nom, :desc, :prix, :cat, :img)");
		$stm->bindParam(':nom', $produit['nom'], PDO::PARAM_STR);
		$stm->bindParam(':desc', $produit['description'], PDO::PARAM_STR);
		$stm->bindParam(':prix', $produit['prix'], PDO::PARAM_INT);
		$stm->bindParam(':cat', $produit['category'], PDO::PARAM_INT);
		$stm->bindParam(':img', $img_link, PDO::PARAM_STR);
		if ($stm->execute()) {
			header('Location: '.$currentLocation);
			exit;
		}
	}
}

function uploadImage() {
	if(!isset($_FILES['image'])) {
		return false;
	}

	$file_name = $_FILES['image']['name'];
	$file_size = $_FILES['image']['size'];
	$file_tmp = $_FILES['image']['tmp_name'];
	$file_type = $_FILES['image']['type'];

	$file_ext = strtolower(end(explode('.', $file_name)));
	
	$extensions = array("jpeg","jpg","png");
	
	if (in_array($file_ext, $extensions) === false) {
		return false;
	}
	
	$target = sprintf("../images/uploads/%s.%s", sha1_file($file_tmp), $file_ext);
	if (!move_uploaded_file($file_tmp, $target)) {
		return false;
	}

	return $target;
}

function getProduits($pdo) {
	$stm = $pdo->prepare("SELECT id, nom, prix, category FROM produits");
	$stm->execute();

	$result = $stm->fetchAll();
	return $result;
}

function getCategories($pdo) {
	$stm = $pdo->prepare("SELECT id, nom FROM categories");
	$stm->execute();

	$result = $stm->fetchAll();
	return $result;
}

?>

<body>
	<?php require_once("templates/top_nav.php"); ?>

	<div class="container-fluid">
		<div class="row">
			<?php require_once('templates/nav.php'); ?>

			<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
				<h2>produits: </h2>
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
					new
				</button>
				<hr>

				<table id="produits" class="table table-striped">
					<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col">nom</th>
							<th scope="col">prix</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$produits = getProduits($pdo);
						foreach($produits as $produit) {
							echo "
								<tr>
									<td>$produit[id]</td>
									<td>$produit[nom]</td>
									<td>$produit[prix] TND</td>
								</tr>
							";
						}
					?>
					</tbody>
				</table>
				<!-- Modal -->
				<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog"
					aria-labelledby="exampleModalLabel" aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
								<div class="modal-body">
									<div class="form-group">
										<label for="nom">nom</label>
										<input type="text" class="form-control" name="nom" id="nomProduit"
											placeholder="nom produit">
									</div>
									<div class="form-group">
										<label for="descriptionProduit">description</label>
										<textarea class="form-control" name="description" id="descriptionProduit"
											placeholder="description"> </textarea>
									</div>
									<div class="form-group">
										<label for="prixProduit">prix</label>
										<input type="number" class="form-control" name="prix" id="prixProduit">
									</div>
									<div class="form-group">
										<label for="imgProduit">image</label>
										<input type="file" class="form-control" name="image" id="imgProduit">
									</div>
									<div class="form-group">
										<label for="categoryProduit">category</label>
										<select class="form-control" name="category" id="categoryProduit">
											<?php 
											$categories = getCategories($pdo); 
											foreach($categories as $row) {
												echo "<option value='$row[id]'>$row[nom]</option>";
											}
										?>
										</select>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary">Save</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</main>
		</div>
	</div>


	<?php require_once('templates/footer.php'); ?>
	<script>
	$(document).ready(function() {
		$('#produits').DataTable();
	});
	</script>
</body>

</html>