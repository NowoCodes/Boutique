<?php
	require_once '../core/init.php';
	if (!is_logged_in()){
		login_error_redirect();
	}
	include 'includes/head.php';
	include 'includes/navigation.php';
	//get brands from database
	$sql = "SELECT * FROM brand ORDER BY brand";
	$results = $db->query($sql);
	$errors = array();
	$brand_value = '';
	
	//Edit brand
	if (isset($_GET['edit']) && !empty($_GET['edit'])){
		$edit_id = (int)$_GET['edit'];
		$edit_id = sanitize($edit_id);
		$sql2 = "SELECT * FROM brand WHERE id = '$edit_id'";
		$edit_result = $db->query($sql2);
		$eBrand = mysqli_fetch_assoc($edit_result);
	}
	
	if (isset($_GET['edit'])) {
		$brand_value = $eBrand['brand'];
	} else {
		if (isset($_POST['brand'])) {
			$brand_value = sanitize($_POST['brand']);
		}
	}
	
	// Delete brand
	if(isset($_GET['delete']) && !empty($_GET['delete'])){
		$delete_id = (int)$_GET['delete'];
		$delete_id = sanitize($delete_id);
		$sql = "DELETE FROM brand WHERE id = '$delete_id'";
		$db->query($sql);
		header('Location: brands.php');
	}
	
	
	// If add form is submitted
	if (isset($_POST['add_submit'])){
		$brand = sanitize($_POST['brand']);
		//check if brand is blank
		if ($_POST['brand'] == ''){
			$errors[] .= 'You must enter a brand!!!';
		}
		// check if brand exists in database
		$sql = "SELECT * FROM brand WHERE brand = '$brand'";
		if (isset($_GET['edit'])){
			$sql = "SELECT * FROM brand WHERE brand = '$brand' AND id!= '$edit_id'";
		}
		$result = $db->query($sql);
		$count = mysqli_num_rows($result);
		if ($count > 0){
			$errors[] .= $brand. ' already exists. Please choose another brand name...';
		}
		//display errors
		if(!empty($errors)){
			echo display_errors($errors);
		} else{
				//add brand to database
			$sql = "INSERT INTO brand (brand) VALUES ('$brand')";
	        $_SESSION['success_flash'] = 'Brand has been added';
			if (isset($_GET['edit'])){
				$sql = "UPDATE brand SET brand = '$brand' WHERE  id = '$edit_id'";
	          	$_SESSION['success_flash'] = 'Brand has been updated';

			}
			$db->query($sql);
			header('Location: brands.php');
		}
	}
?>
<div class="container" xmlns="http://www.w3.org/1999/html" xmlns="http://www.w3.org/1999/html">
	<h2 class="text-center">Brands</h2>
	<hr>
	<!--	Brand Form-->
	<div class="d-flex justify-content-center">
		<form class="form-inline" action="brands.php <?= ((isset($_GET['edit']))?'?edit='.$edit_id:'') ?>" method="post">
			<div class="form-group text-center">
				<label for="brand" class="mb-2 mr-sm-2"><strong><?= ((isset($_GET['edit']))?'Edit':'Add A') ?> Brand:</strong></label>
				<div class="text-center">
					<input type="text" name="brand" id="brand" class="form-control text-capitalize mb-2 mr-sm-2" value="<?= $brand_value; ?>">
					<?php if(isset($_GET['edit'])): ?>
						<a href="brands.php" class="btn btn-secondary mb-2 mr-sm-2"><i class="fa fa-times-circle mr-2"></i>Cancel</a>
					<?php endif; ?>
					<button type="submit" name="add_submit" class="btn btn-success mb-2"><?= ((isset($_GET['edit']))?'<span class="fa fa-pen-fancy mr-2"></span>Edit':'<span class="fa fa-plus-circle mr-2"></span>Add'); ?> Brand</button>
				</div>
			</div>
		</form>
	</div>
	<hr>
	<div class="table-responsive-sm">
		<table class="table table-sm table-striped table-hover">
			<thead class="">
			<th></th>
			<th>Brand</th>
			<th></th>
			</thead>
			<tbody class="">
			<?php while ($brand = mysqli_fetch_assoc($results)): ?>
				<tr>
					<td><a href="brands.php?edit=<?= $brand['id']; ?>" class="btn btn-sm btn-primary"><span class="fa fa-pen-fancy"></span></a></td>
					<td><?= $brand['brand']; ?></td>
					<td><a href="brands.php?delete=<?= $brand['id']; ?>" class="btn btn-sm btn-primary"><span class="fa fa-trash-alt"></span></a></td>
				</tr>
			<?php endwhile; ?>
			</tbody>
		</table>
	</div>
</div>
<?php include 'includes/footer.php'; ?>
