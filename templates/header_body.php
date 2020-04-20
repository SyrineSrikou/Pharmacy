<header class="section-header">
	<section class="header-main">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-3">
					<div class="brand-wrap">
						<h2 class="logo-text"><a href="index.php" >Parapharmacie Nature</a></h2>
					</div> <!-- brand-wrap.// -->
				</div>
				<div class="col-lg-6 col-sm-6">
					<form action="search.php" class="search-wrap">
						<div class="input-group">
							<input type="text" class="form-control" name="q" placeholder="Search product">
							<div class="input-group-append">
								<button class="btn btn-primary" type="submit">
									<i class="fa fa-search"></i>
								</button>
							</div>
						</div>
					</form> <!-- search-wrap .end// -->
				</div> <!-- col.// -->
				<div class="col-lg-3 col-sm-6">
					<div class="widgets-wrap d-flex justify-content-end">
						<div class="widget-header">
							<a href="checkout.php" class="icontext">
								<div class="icon-wrap icon-xs bg2 round text-secondary"><i
										class="fa fa-shopping-cart"></i></div>
								<div class="text-wrap">
									<small>cart</small>
									<span><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) . " item(s)" : "empty"; ?> </span>
								</div>
							</a>
						</div> <!-- widget .// -->
						<div class="widget-header dropdown">
							<a href="#" class="ml-3 icontext" data-toggle="dropdown" data-offset="20,10">
								<div class="icon-wrap icon-xs bg2 round text-secondary"><i class="fa fa-user"></i>
								</div>
								<div class="text-wrap">
								<?php 
								if (isset($_SESSION['loggedin'])) {
									echo "<small>Hello $_SESSION[username] <i class='fa fa-caret-down'></i></small>";
								} else {	echo '<span>Login <i class="fa fa-caret-down"></i></span>';
								} ?>
								</div>
							</a>
							<?php 
							if (isset($_SESSION['loggedin'])) {
								$dropdown = '
								<div class="dropdown-menu dropdown-menu-right">';
								if ($_SESSION['admin'] == 1) {
									$dropdown .= '<a class="dropdown-item" href="admin">admin</a>';
								}
								$dropdown .= '<a class="dropdown-item" href="profile.php">profile</a>
								<a class="dropdown-item" href="reset-password.php">change password</a>
								<a class="dropdown-item" href="logout.php">logout</a>
								</div> <!--  dropdown-menu .// -->';
								echo $dropdown;
							} else {
							echo '
								<div class="dropdown-menu dropdown-menu-right">
									<form action="login.php" method="post" class="px-4 py-3">
										<div class="form-group">
											<label>Username</label>
											<input type="text" class="form-control" name="username" placeholder="username">
										</div>
										<div class="form-group">
											<label>Password</label>
											<input type="password" class="form-control" name="password" placeholder="Password">
										</div>
										<button type="submit" class="btn btn-primary">Sign in</button>
									</form>
									<hr class="dropdown-divider">
									<a class="dropdown-item" href="register.php">Don\'t have account? Sign up</a>
								</div> <!--  dropdown-menu .// -->';
							}
							?>
						</div> <!-- widget  dropdown.// -->
					</div> <!-- widgets-wrap.// -->
				</div> <!-- col.// -->
			</div> <!-- row.// -->
		</div> <!-- container.// -->
	</section> <!-- header-main .// -->

	<?php require_once('templates/nav.php'); ?>
</header> <!-- section-header.// -->