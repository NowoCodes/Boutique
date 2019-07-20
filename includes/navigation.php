<?php
$sql= "SELECT * FROM categories WHERE parent = 0";
$pquery = $db->query($sql);
?>
<!--    Top Nav Bar-->
<header>
	<nav class="navbar navbar-expand-md navbar-light bg-light fixed-top">
		<a class="navbar-brand ml-5" href="index.php">Shaunta's Boutique</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav text-center">
				<?php while ($parent = mysqli_fetch_assoc($pquery)) : ?>
          <?php
            $parent_id = $parent['id'];
            $sql2 = "SELECT * FROM categories WHERE parent = '$parent_id'";
            $cquery = $db->query($sql2)
          ?>
          <!--            Menu Items-->
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <?php echo $parent['category']; ?><span class="caret"></span>
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <?php while ($child = mysqli_fetch_assoc($cquery)) : ?>
              <a class="dropdown-item" href="category.php?cat=<?= $child['id']; ?>"><?= $child['category']; ?></a>
              <?php endwhile; ?>
            </div>
          </li>
        <?php endwhile; ?>
        <li class="nav-item">
          <a class="nav-link" href="cart.php"><span class="fa fa-shopping-cart"></span> My Cart</a>
        </li>
			</ul>
		</div>
	</nav>
</header>