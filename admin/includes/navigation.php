<header>
	<nav class="navbar navbar-expand-md navbar-light bg-light fixed-top">
		<a class="navbar-brand ml-5" href="index.php">Shaunta's Boutique<?= ((has_permission('admin'))?' Admin':' Editor'); ?></a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav text-center">
					<!--            Menu Items-->
                <li class="nav-item"><a class="nav-link" href="brands.php">Brands</a></li>
				<li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
				<li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
				<li class="nav-item"><a class="nav-link" href="archived.php">Archived</a></li>
				<?php if(has_permission('admin')): ?>
					<li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
				<?php endif; ?>
				<li class="nav-item dropdown">
					<a href="#" class="nav-link dropdown-toggle" id="navbarDropdown"  role="button" data-toggle="dropdown">
						Hello <?php echo $user_data['first']; ?><span class="caret"></span>!
					</a>
					<div class="dropdown-menu">
						<a class="dropdown-item" href="change_password.php">Change Password</a>
						<a class="dropdown-item" href="logout.php">Log Out</a>
					</div>
				</li>

				<!-- <li class="nav-item dropdown">
					<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
						<?php //echo $parent['category']; ?><span class="caret"></span>
					</a>
					<div class="dropdown-menu" role="menu">
						<a class="dropdown-item" href="#"></a>
					</div>
				</li> -->
			</ul>
		</div>
	</nav>
</header>