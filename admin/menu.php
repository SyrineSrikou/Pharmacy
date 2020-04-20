<?php 

require_once('templates/header.php');

if (isset($_POST) && !empty($_POST)) {
	createMenuItem($pdo, $_POST);
}

function createMenuItem($pdo, $menu) {
	$stm = $pdo->prepare("INSERT INTO menu (nom) values (:nom)");
	$stm->bindParam(':nom', $menu['nom'], PDO::PARAM_STR);
	$stm->execute();
}

function getMenuItems($pdo) {
	$stm = $pdo->prepare("SELECT id, nom FROM menu");
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
				<h2>menu: </h2>
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
					new
				</button>
				<hr>

				<table class="table table-striped">
					<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col">nom</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$menu = getMenuItems($pdo);
						foreach($menu as $row) {
							echo "
								<tr>
									<td>$row[id]</td>
									<td>$row[nom]</td>
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
							<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
								<div class="modal-body">
									<div class="form-group">
										<label for="nom">nom</label>
										<input type="text" class="form-control" name="nom" id="menu"
											placeholder="nom">
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