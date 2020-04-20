<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container">

		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main_nav"
			aria-controls="main_nav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="main_nav">
			<ul class="navbar-nav d-flex justify-content-center main-nav">
				<li class="nav-item">
					<a class="nav-link pl-0" href="."> <strong>Home</strong></a>
				</li>
				<?php 
					$stm = $pdo->prepare("SELECT GROUP_CONCAT(c.url_name) as cat_urls, GROUP_CONCAT(c.id) as cat_ids, GROUP_CONCAT(c.nom) as cat_names, m.id, m.nom FROM menu m LEFT JOIN categories c ON (m.id = c.parent) GROUP BY m.id");
					$stm->execute();
					$menu = $stm->fetchAll();
					foreach($menu as $row) {
						if ($row['cat_ids'] == "") {
							continue;
						} else {
							$cat_names = explode(',', $row['cat_names']);
							$cat_urls = explode(',', $row['cat_urls']);

							$format = '<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="dropdown_%s" data-toggle="dropdown"
								aria-haspopup="true" aria-expanded="false">%s</a>
							<div class="dropdown-menu" aria-labelledby="dropdown_%s">';
							for ($i = 0; $i < count($cat_names); $i++) {
								$format .= sprintf('<a class="dropdown-item" href="category.php?c=%s"> %s </a>', $cat_urls[$i], $cat_names[$i]);								
							}
							$format .= '</div></li>';
							echo sprintf($format, $row['id'], $row['nom'], $row['id']);
						}
					}
				?>
				
			</ul>
		</div> <!-- collapse .// -->
	</div> <!-- container .// -->
</nav>