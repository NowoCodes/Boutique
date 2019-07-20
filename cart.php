<?php
  require_once 'core/init.php';
  include 'includes/head.php';
  include 'includes/navigation.php';
  include 'includes/headerpartial.php';

  if ($cart_id != '') {
    $cartQ = $db->query("SELECT * FROM cart WHERE id = '{$cart_id}'");
    $result = mysqli_fetch_assoc($cartQ);
    $items = json_decode($result['items'], true);
    $i = 1;
    $sub_total = 0;
    $item_count = 0;
  }
?>

<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2 class="text-center">My Shopping Cart</h2>
      <hr>
      <?php if($cart_id == ''): ?>
        <div class="text-danger">
          <p class="text-center">
            Your shoppinig cart is empty!
          </p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-bordered table-sm table-striped">
            <thead>
              <th>#</th>
              <th>Item</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Size</th>
              <th>Sub Total</th>
            </thead>
            <tbody>
              <?php
                foreach ($items as $item) {
                  $product_id = $item['id'];
                  $productQ = $db->query("SELECT * FROM products WHERE id = '{$product_id}'");
                  $product = mysqli_fetch_assoc($productQ);
                  $sArray = explode(',', $product['sizes']);
                  
                  foreach($sArray as $sizeString){
                    $s = explode(':', $sizeString);
                    if ($s[0] == $item['size']) {
                      $available = $s[1];
                    }
                  }
                ?>
                <tr>
                  <td><?= $i; ?></td>
                  <td><?= $product['title']; ?></td>
                  <td><?= money($product['price']); ?></td>
                  <td>
                    <button class="btn btn-sm mr-2 btn-link" onclick="update_cart('removeone', '<?= $product['id']; ?>', '<?= $item['size']; ?>');"><span class="fa fa-minus"></span></button>
                    <?= $item['quantity']; ?>
                    <?php if($item['quantity'] < $available): ?>
                      <button class="btn btn-sm btn-link ml-2" onclick="update_cart('addone', '<?= $product['id']; ?>', '<?= $item['size']; ?>');"><span class="fa fa-plus"></span></button>
                    <?php else: ?>
                      <span class="text-danger ml-2">Max</span>
                    <?php endif; ?>
                  </td>
                  <td><?= $item['size']; ?></td>
                  <td><?= money($item['quantity'] * $product['price']); ?></td>
                </tr>
              <?php 
                $i++;
                $item_count += $item['quantity'];
                $sub_total += ($product['price'] * $item['quantity']);
                } 
                $tax = TAXRATE * $sub_total;
                $tax = number_format($tax, 2);
                $grand_total = $tax + $sub_total;
                ?>
            </tbody>
          </table>

          <table class="table table-bordered table-sm">
            <thead class="text-center">
              <legend>Total</legend>
              <th>Total Items</th>
              <th>Sub Total</th>
              <th>Tax</th>
              <th>Grand Total</th>
            </thead>
            <tbody class="text-right">
              <tr>
                <td><?= $item_count; ?></td>
                <td><?= money($sub_total); ?></td>
                <td><?= money($tax); ?></td>
                <td class="bg-success"><?= money($grand_total); ?></td>
              </tr>
            </tbody>
          </table>

          <!-- Checkout button-->
          <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#checkoutModal">
            <span class="fa fa-shopping-cart mr-2"></span>Check Out
          </button>

          <!-- The Modal -->
          <div class="modal fade" id="checkoutModal">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
              
                <!-- Modal Header -->
                <div class="modal-header">
                  <h4 class="modal-title" id="checkoutModalLabel">Shipping Address</h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <!-- Modal body -->
                <div class="modal-body">
                  <form action="thankYou.php" method="post" id="payment-form">
                    <span class="text-danger" id="payment-errors"></span>
                    <!-- <div id="step1" class="d-block"> -->
                    <div id="step1" style="display : block">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-md-6">
                            <label for="full_name">Full Name:</label>
                            <input type="text" class="form-control" id="full_name" name="full_name">
                          </div>
                          <div class="col-md-6">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email">
                          </div>
                          <div class="col-md-6">
                            <label for="full_name">Street Address:</label>
                            <input type="text" class="form-control" id="street" name="street">
                          </div>
                          <div class="col-md-6">
                            <label for="street2">Street Address 2:</label>
                            <input type="text" class="form-control" id="street2" name="street2">
                          </div>
                          <div class="col-md-6">
                            <label for="city">City:</label>
                            <input type="text" class="form-control" id="city" name="city">
                          </div>
                          <div class="col-md-6">
                            <label for="state">State:</label>
                            <input type="text" class="form-control" id="state" name="state">
                          </div>
                          <div class="col-md-6">
                            <label for="zip_code">Zip Code:</label>
                            <input type="text" class="form-control" id="zip_code" name="zip_code">
                          </div>
                          <div class="col-md-6">
                            <label for="country">Country:</label>
                            <input type="text" class="form-control" id="country" name="country">
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- <div id="step2" class="d-none"> -->
                    <div id="step2" style="display : none">
                      <div class="form-group">
                        <div class="row">
                          <div class="col-md-3">
                            <label for="name">Name on Card:</label>
                            <!-- do not put a name="" here for security purposes -->
                            <input type="text" id="name" class="form-control">
                          </div>
                          <div class="col-md-3">
                            <label for="name">Card Number</label>
                            <input type="text" id="number" class="form-control">
                          </div>
                          <div class="col-md-2">
                            <label for="cvc">CVC:</label>
                            <input type="number" id="cvc" class="form-control">
                          </div>
                          <div class="col-md-2">
                            <label for="name">Expire Month</label>
                            <select id="exp-month" class="form-control custom-select">
                              <option value=""></option>
                              <?php for($i=1; $i<13; $i++): ?>
                                <option value="<?= $i; ?>"><?= $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>
                          <div class="col-md-2">
                            <label for="exp-year">Expire Year</label>
                            <select id="exp-year" class="form-control custom-select">
                              <option value=""></option>
                              <?php $yr = date("Y"); ?>
                              <?php for($i=0; $i<11; $i++): ?>
                                <option value="<?= $yr + $i; ?>"><?= $yr + $i; ?></option>
                              <?php endfor; ?>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
                
                <!-- Modal footer -->
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary" onclick="check_address();" id="next-button">Next<span class="ml-2 fa fa-chevron-circle-right"></span></button>
                  <button type="button" class="btn btn-primary" style="display : none" onclick="back_address();" id="back-button"><span class="mr-2 fa fa-chevron-circle-left"></span>Back</button>
                  <button type="submit" class="btn btn-primary" style="display : none" onclick="check_address();" id="checkout-button"><span class="mr-2 fa fa-check-double"></span>Check Out</button>
                  </form>
                </div>
                
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  function back_address() {
    jQuery('#payment-errors').html("");
    jQuery('#step1').css("display","block");
    jQuery('#step2').css("display","none");
    jQuery('#next-button').css("display","inline-block");
    jQuery('#back-button').css("display","none");
    jQuery('#checkout-button').css("display","none");
    jQuery('#checkoutModalLabel').html("Shipping Address");
  }
  // you can use jQuery() instead of $(), cause you might run into some errors when using some pluggins 
  function check_address() {
    var data = {
      'full_name' : jQuery('#full_name').val(),
      'email' : jQuery('#email').val(),
      'street' : jQuery('#street').val(),
      'street2' : jQuery('#street2').val(),
      'city' : jQuery('#city').val(),
      'state' : jQuery('#state').val(),
      'zip_code' : jQuery('#zip_code').val(),
      'country' : jQuery('#country').val(),
    };
    jQuery.ajax({
      url : '/Projects/InProgress/Boutique/admin/parsers/check_address.php',
      method : 'post',
      data : data,
      success : function(data){
        if (data != 'passed') {
          jQuery('#payment-errors').html(data);
        }
        if (data == 'passed'){
          jQuery('#payment-errors').html("");
          jQuery('#step1').css("display","none");
          jQuery('#step2').css("display","block");
          jQuery('#next-button').css("display","none");
          jQuery('#back-button').css("display","inline-block");
          jQuery('#checkout-button').css("display","inline-block");
          jQuery('#checkoutModalLabel').html("Enter your card details");
        }
      },
      error : function(){alert("Something went wrong!")},
    });
  }
</script>



<?php include 'includes/footer.php'; ?>