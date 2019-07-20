<?php
  require_once '../core/init.php';
  $id = $_POST['id'];
	// $id = ((isset($_POST['id']))?sanititze($id):'');
	$id = (int)$id;
	$sql = "SELECT * FROM products WHERE id = '$id'";
	$result = $db->query($sql);
	$product = mysqli_fetch_assoc($result);
	$brand_id = $product['brand'];
	$sql = "SELECT brand FROM brand WHERE id = '$brand_id'";
	$brand_query = $db->query($sql);
	$brand = mysqli_fetch_assoc($brand_query);
	$sizestring = $product['sizes'];
	$sizestring = rtrim($sizestring,',');
	$size_array = explode(',', $sizestring);
?>
<!--      Details Modal -->
<?php ob_start(); ?>
<div class="modal fade details-1" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="details-1" aria-hidden="true">

  <!-- To ensure the modal can only be closed when you click the close button or x, add the following attributes:
  data-backdrop="static" data-keyboard="false" -->

	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title text-center w-100"><?= $product['title']; ?></h4>
				<button class="close" type="button" onclick="closeModal()" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<span id="modal_errors" class=""></span>
						</div>
						<div class="col-sm-6 text-center">
							<img src="<?= $product['image']; ?>" alt="<?= $product['image']; ?>" class="pt-5 details img-fluid">
						</div>

						<div class="col-md-6">
							<h4>Details</h4>
							<p><?= nl2br($product['description']); ?></p>
							<hr>
							<p>Price: $<?= $product['price']; ?></p>
							<p>Brand: <?= $brand['brand']; ?></p>

							<form action="" method="POST" id="add_product_form">
								<input type="hidden" name="product_id" value="<?= $id; ?>">
								<input type="hidden" name="available" id="available" value="">
								<div class="form-group">
									<label for="quantity" class="col-form-label">Quantity</label>
									<input type="number" class="form-control w-25" id="quantity" name="quantity" min="1">
									<label for="size" class="col-form-label">Size</label>
									<select name="size" id="size" class="form-control custom-select">
										<option value=""></option>
										<?php foreach ($size_array as $string){
												$string_array = explode(':', $string);
												$size = $string_array[0];
												$available = $string_array[1];
												echo '<option value="'.$size.'" data-available="'.$available.'">'.$size.' ('.$available.' Available)</option>';
										} ?>
									</select>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary" onclick="closeModal()">Close</button>
				<button class="btn btn-warning text-white" onclick="add_to_cart();return false;"><span class="fa fa-shopping-cart mr-2"></span>Add to Cart</button>
			</div>
		</div>
	</div>
</div>
  <script>
  	jQuery('#size').change(function(){
      var available = jQuery('#size option:selected').data("available");
      jQuery('#available').val(available);
    });

		function closeModal() {
			jQuery('#details-modal').modal('hide');
			setTimeout(function () {
				jQuery('#details-modal').remove();
			},500);
			jQuery('.modal-backdrop').remove();
		}
  </script>
<?php echo ob_get_clean(); ?>