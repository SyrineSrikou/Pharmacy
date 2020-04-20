<?php 

require_once('templates/header.php');

if (isset($_POST) && !empty($_POST)) {
	createOffre($pdo, $_POST);
}

function createOffre($pdo, $offre) {
	$currentLocation = $_SERVER['REQUEST_URI'];
	$img_link = uploadImage();
	if (!$img_link) {
		header("Location: $currentLocation ");
		exit;
	} else {
		$stm = $pdo->prepare("INSERT INTO offres (nom, prix, type, image_path) values (:nom, :prix, :type, :img)");
		$stm->bindParam(':nom', $offre['nom'], PDO::PARAM_STR);
		$stm->bindParam(':prix', $offre['prix'], PDO::PARAM_INT);
		$stm->bindParam(':type', $offre['type'], PDO::PARAM_STR);
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
	
	$target = sprintf("../images/offres/%s.%s", sha1_file($file_tmp), $file_ext);
	if (!move_uploaded_file($file_tmp, $target)) {
		return false;
	}

	return $target;
}

function getoffres($pdo) {
	$stm = $pdo->prepare("SELECT id, nom, prix, type FROM offres");
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
				<h2>Offres: </h2>
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#offreModal">
					new
				</button>
				<hr>
				
				<hr>
				<table id="offres" class="table table-striped">
					<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col">nom</th>
							<th scope="col">prix</th>
							<th scope="col">type</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$offres = getOffres($pdo);
						foreach($offres as $offre) {
							echo "
								<tr>
									<td>$offre[id]</td>
									<td>$offre[nom]</td>
									<td>$offre[prix] TND</td>
									<td>$offre[type]</td>
								</tr>
							";
						}
					?>
					</tbody>
				</table>
				<!-- Modal -->
				<div class="modal fade" id="offreModal" tabindex="-1" role="dialog" aria-labelledby="offreModal"
					aria-hidden="true">
					<div class="modal-dialog" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
								enctype="multipart/form-data">
								<div class="modal-body">
									<div class="form-group">
										<label for="nomOffre">nom</label>
										<input type="text" class="form-control" name="nom" id="nomOffre"
											placeholder="nom Offre">
									</div>
									<div class="form-group">
										<label for="prixOffre">prix</label>
										<input type="number" class="form-control" name="prix" id="prixOffre">
									</div>
									<div class="form-group">
										<label for="imgOffre">image</label>
										<input type="file" class="form-control" name="image" id="imgOffre">
									</div>
									<div class="form-group">
										<label for="type">type</label>
										<input type="text" class="form-control" name="type" id="type"
											placeholder="type Offre">
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
		$('#offres').DataTable();
	});
	</script>
</body>

</html>