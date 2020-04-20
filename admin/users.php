<?php 

require_once('templates/header.php');

if (isset($_POST) && !empty($_POST)) {
	
	createUser($pdo, $_POST);
}

function createUser($pdo, $user) {
	$stm = $pdo->prepare("INSERT INTO users (username, email, password, admin) values (:username, :email, :password, :admin)");
	$hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
	isset($user['admin']) ? $admin = 1 : $admin = 0;
	$stm->bindParam(':username', $user['username'], PDO::PARAM_STR);
	$stm->bindParam(':email', $user['email'], PDO::PARAM_STR);
	$stm->bindParam(':password', $hashed_password, PDO::PARAM_STR);
	$stm->bindParam(':admin', $admin);
	$stm->execute();
}

function getUsers($pdo) {
	$stm = $pdo->prepare("SELECT id, username, email, admin FROM users");
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

				<table class="table table-striped">
					<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col">nom</th>
							<th scope="col">email</th>
							<th scope="col">type</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$users = getUsers($pdo);
						foreach($users as $user) {
							$admin = $user['admin'] == 1 ? "admin" : "normal user";
							echo "
								<tr>
									<td>$user[id]</td>
									<td>$user[username]</td>
									<td>$user[email]</td>
									<td>$admin</td>
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
									<div class="form-group input-group">
										<input name="username" class="form-control" placeholder="Username" type="text" required>
									</div> <!-- form-group// -->
									<div class="form-group input-group">
										<input name="email" class="form-control" placeholder="Email " type="email" required>
									</div> <!-- form-group// -->
									<div class="form-group input-group">
										<input class="form-control" placeholder="Create password" name="password"
											type="password" required>
									</div> <!-- form-group// -->
									<div class="form-group input-group">
										<input class="form-control" placeholder="confirm password"
											name="confirm_password" type="password" required>
									</div> <!-- form-group// -->
									<div class="form-group form-check">
										<input type="checkbox" class="form-check-input" name="admin" id="admin">
										<label class="form-check-label" for="admin">admin?</label>
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