<?php
session_start();
require_once("config/db.php"); 

if (isset($_POST) && !empty($_POST)) {
	$action = $_POST['action'];

	switch ($action) {
		case 'removeItem':
			removeItem($_POST['id']);
			break;
		
		case 'checkout':
			confirmCheckout($_POST, $pdo);
			break;
		
		default:
			break;
	}
}

function removeItem($id) {
	unset($_SESSION['cart'][$id]);
	exit;
}

function confirmCheckout($post, $pdo) {
	// print_r($post);
	$shipping = $post['shipping'];
	$list_items_quantity = $post['ids'];
	$user_id = $_SESSION['user_id'];
	extract($shipping);
	
	try {

		$stm = $pdo->prepare("INSERT INTO orders (buyer_id) VALUES (:user_id)");
		$stm->bindParam(":user_id", $user_id, PDO::PARAM_INT);
		$stm->execute();

		$order_id = $pdo->lastInsertId();

		$stm = $pdo->prepare("INSERT INTO orderitems (order_id, item_id, quantity) VALUES (:order_id, :item_id, :quantity)");
		foreach($list_items_quantity as $key => $val) {
			$stm->bindParam("order_id", $order_id, PDO::PARAM_INT);
			$stm->bindParam("item_id", $key, PDO::PARAM_INT);
			$stm->bindParam("quantity", $val, PDO::PARAM_INT);
			$stm->execute();
		}

		$stm = $pdo->prepare("INSERT INTO ordershipping (order_id, user_id, firstname, lastname, email, address, country, state, zip) VALUES (:order_id, :user_id, :firstname, :lastname, :email, :address, :country, :state, :zip)");
		$stm->bindParam("order_id", $order_id, PDO::PARAM_INT);
		$stm->bindParam("user_id", $user_id, PDO::PARAM_INT);
		$stm->bindParam("firstname", $firstname, PDO::PARAM_STR);
		$stm->bindParam("lastname", $lastname, PDO::PARAM_STR);
		$stm->bindParam("email", $email, PDO::PARAM_STR);
		$stm->bindParam("address", $address, PDO::PARAM_STR);
		$stm->bindParam("country", $country, PDO::PARAM_STR);
		$stm->bindParam("state", $state, PDO::PARAM_STR);
		$stm->bindParam("zip", $zip, PDO::PARAM_STR);
		$stm->execute();

	} catch(PDOExecption $e) {
		print_r($e->getMessage());
	}

	// checkout success => empty cart
	$_SESSION['cart'] = array();

	exit;
}

function getProductFromSession($pdo) {

	$ids = implode(',', array_keys($_SESSION['cart']));
	$result = $pdo->query("SELECT id, nom, prix, image_path FROM produits WHERE id in ($ids)");

	return $result;
}

require_once("templates/header.php");

?>

<body>

	<?php require_once("templates/header_body.php"); ?>

	<!-- ========================= SECTION CONTENT ========================= -->
	<section class="section-content bg padding-y border-top">
		<div class="container">

			<div class="row">
				<main class="col-sm-9">

					<div class="card">
						<table class="table table-hover shopping-cart-wrap">
							<thead class="text-muted">
								<tr>
									<th scope="col">Product</th>
									<th scope="col" width="120">Quantity</th>
									<th scope="col" width="120">Price</th>
									<th scope="col" class="text-right" width="200">Action</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$total_price = 0;
								if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
									$produits = getProductFromSession($pdo);
									$list_item_price = array();
									$list_item_quantity = array();
									
									foreach($produits as $produit) {
										
										$list_item_quantity[$produit['id']] = $_SESSION['cart'][$produit['id']];
									$list_item_price[] = $produit['prix'] * $_SESSION['cart'][$produit['id']];
									$total_price += $produit['prix'] * $_SESSION['cart'][$produit['id']];
									$format = '<tr data-item_id="%s">
										<td>
											<figure class="media">
											<div class="img-wrap"><img src="%s"
														class="img-thumbnail img-sm"></div>
														<figcaption class="media-body">
													<h6 class="title text-truncate"><a href="details.php?id=%s"> %s </a></h6>
													</figcaption>
											</figure>
											</td>
										<td>
										<div class="price-wrap">
												<var class="quantity"> %s</var>
												</div> <!-- price-wrap .// -->
												</td>
										<td>
										<div class="price-wrap">
										<var class="price">TND %s</var>
											</div> <!-- price-wrap .// -->
											</td>
											<td class="text-right">
											<button class="btn btn-outline-danger remove" data-id="%s"> × Remove</button>
										</td>
										</tr>';
									echo sprintf($format, $produit['id'], $produit['image_path'], $produit['id'], $produit['nom'], $_SESSION['cart'][$produit['id']], $produit['prix'], $produit['id']);
								} 
							}
							?>
							</tbody>
						</table>
					</div> <!-- card.// -->

				</main> <!-- col.// -->
				<aside class="col-sm-3 total-price">
					<?php
					if (isset($list_item_price)) {
						foreach($list_item_price as $price) {
							$format = '
							<dl class="dlist-align">
							<dt>produit:</dt>
							<dd class="text-right"> %s</dd>
							</dl>';
							echo sprintf($format, $price);
						}
					}
					?>
					<dl class="dlist-align h4">
						<dt>Total:</dt>
						<dd class="text-right"><strong id="total_price">TND <?php echo $total_price; ?></strong></dd>
					</dl>
					<hr>
				</aside> <!-- col.// -->
			</div>
			<div class="row" style="margin-top:10px">
				<div class="col-9">
					<h4 class="mb-3">Shipping address</h4>
					<form id="checkout-form">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="firstName">First name</label>
								<input type="text" class="form-control" name="firstname" id="firstname" placeholder="" value="dd" required>
								<div class="invalid-feedback">
									Valid first name is required.
								</div>
							</div>
							<div class="col-md-6 mb-3">
								<label for="lastName">Last name</label>
								<input type="text" class="form-control" name="lastname" id="lastname" placeholder="" value="ss" required>
								<div class="invalid-feedback">
									Valid last name is required.
								</div>
							</div>
						</div>

						<div class="mb-3">
							<label for="email">Email</label>
							<input type="email" class="form-control" name="email" id="email" value="you@example.com" placeholder="you@example.com">
							<div class="invalid-feedback">
								Please enter a valid email address for shipping updates.
							</div>
						</div>

						<div class="mb-3">
							<label for="address">Address</label>
							<input type="text" class="form-control" name="address" id="address" value="1234 Main St" placeholder="1234 Main St" required>
							<div class="invalid-feedback">
								Please enter your shipping address.
							</div>
						</div>

						<div class="row">
							<div class="col-md-5 mb-3">
								<label for="country">Country</label>
								<select class="custom-select d-block w-100" name="country" id="country" required>
								</select>
								<div class="invalid-feedback">
									Please select a valid country.
								</div>
							</div>
							<div class="col-md-4 mb-3">
								<label for="state">State</label>
								<select class="custom-select d-block w-100" name="state" id="state" required>
								</select>
								<div class="invalid-feedback">
									Please provide a valid state.
								</div>
							</div>
							<div class="col-md-3 mb-3">
								<label for="zip">Zip</label>
								<input type="text" class="form-control" id="zip" placeholder="444" value="444" required>
								<div class="invalid-feedback">
									Zip code required.
								</div>
							</div>
						</div>
						<hr class="mb-4">

						<button class="btn btn-primary btn-lg btn-block" type="submit">Checkout</button>
					</form>
				</div>
			</div>

		</div> <!-- container .//  -->
	</section>
	<!-- ========================= SECTION CONTENT END// ========================= -->

	<?php require_once("templates/footer.php"); ?>
	<script src="plugins/countries/countries.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

	<script>
		$(document).ready(function(){
			populateCountries("country", "state");
			const removebtns = document.querySelectorAll(".remove");
			removebtns.forEach(btn => btn.addEventListener('click', removeItem));
			const checkout_form = document.querySelector("#checkout-form");
			checkout_form.addEventListener('submit', handleCheckout);
		});

		function removeItem(e) {
			const item_id = e.target.dataset.id;
			const total_price_tag = document.querySelector('#total_price');
			let total_price = parseInt(total_price_tag.innerHTML.replace(/[^0-9.-]+/g,""));
			const current = $(this).parentsUntil('tbody');
			const item = $(current[current.length - 1]);
			const item_quantity = parseInt(item.find(".quantity")[0].innerHTML);
			const item_price  = parseInt(item.find(".price")[0].innerHTML.replace(/[^0-9.-]+/g,""));

			total_price = total_price - (item_quantity * item_price);
			total_price_tag.innerHTML = "TND " + total_price;
			item.remove();

			$.ajax({
				method: "POST",
				url: "<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>",
				data: { action: "removeItem", id: item_id },
				success: function(response, status, xhr) {
					console.log(response, status);
				},
				error: function(xhr, status, error) {
					console.log(error, status);
				}
			});
		}

		function handleCheckout(e) {
			e.preventDefault();

			const trs = document.querySelectorAll("tbody > tr");
			const items = {};
			trs.forEach(tr => {
				items[tr.dataset.item_id] = tr.querySelector(".quantity").innerHTML;
			});
			const shipping = {};
			shipping.firstname = document.querySelector("#firstname").value;
			shipping.lastname = document.querySelector("#lastname").value;
			shipping.email = document.querySelector("#email").value;
			shipping.address = document.querySelector("#address").value;
			shipping.country = document.querySelector("#country").value;
			shipping.state = document.querySelector("#state").value;
			shipping.zip = document.querySelector("#zip").value;

			Swal.fire({
				title: 'Are you sure?',
				text: "You won't be able to revert this!",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes',
				showLoaderOnConfirm: true,
				preConfirm: () => {
					$.ajax({
						method: "POST",
						url: "<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]); ?>",
						data: { action: "checkout", ids: items, shipping },
						success: function(response, status, xhr) {
							console.log(response, status);
							return response;
						},
						error: function(xhr, status, error) {
							console.log(status, error);
							return error;
						}
					});
				},
				allowOutsideClick: () => !Swal.isLoading()
			}).then((result) => {
				if (result.value) {
					Swal.fire({
						title: 'done',
						text: "checkout ok",
						type: 'success',
					});
				}
			}).then(() => {
				window.location.reload(true);
			})
		}
	</script>
</body>
</html>