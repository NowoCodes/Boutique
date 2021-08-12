<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/core/init.php';
  if (!is_logged_in()){
		login_error_redirect();
	}
	include 'includes/head.php';
  include 'includes/navigation.php';

  //Delete Product
  if (isset($_GET['delete'])) {
    $id = sanitize($_GET['delete']);
    $db->query("UPDATE products SET deleted = 1, featured = 0 WHERE id = '$id'");
    header('Location: products.php');
  }

  $dbpath ='';


  if (isset($_GET['add']) || isset($_GET['edit'])) {
    $brandQuery = $db->query("SELECT * FROM brand ORDER BY brand");
    $parentQuery = $db->query("SELECT * FROM categories WHERE parent = 0 ORDER BY category");
    $title = ((isset($_POST['title']) && $_POST['title'] != '')?sanitize($_POST['title']):'');
    $brand = ((isset($_POST['brand']) && !empty($_POST['brand']))?sanitize($_POST['brand'] ):'');
    $parent = ((isset($_POST['parent']) && !empty($_POST['parent']))?sanitize($_POST['parent'] ):'');
    $category = ((isset($_POST['child']) && !empty($_POST['child']))?sanitize($_POST['child'] ):'');
    $price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):'');
    $list_price = ((isset($_POST['list_price']) && $_POST['list_price'] != '')?sanitize($_POST['list_price']):'');
    $description = ((isset($_POST['description']) && $_POST['description'] != '')?sanitize($_POST['description']):'');
    $sizes = ((isset($_POST['sizes']) && $_POST['sizes'] != '')?sanitize($_POST['sizes']):'');
    $sizes = rtrim($sizes, ',');
    $saved_image = '';

    if(isset($_GET['edit'])){
      $edit_id = (int)$_GET['edit'];
      $productResults = $db->query("SELECT * FROM products WHERE id = '$edit_id'");
      $product = mysqli_fetch_assoc($productResults);
      if (isset($_GET['delete_image'])) {
        $image_url = $_SERVER['DOCUMENT_ROOT'].$product['image']; echo $image_url;
        unlink($image_url);
        $db->query("UPDATE products SET image = '' WHERE id = '$edit_id'");
        header('Location: products.php?edit='.$edit_id);
      }

      $category = ((isset($_POST['child']) && $_POST['child'] != '')?sanitize($_POST['child']):$product['categories']);
      $title = ((isset($_POST['title']) && $_POST['title'] != '')?sanitize($_POST['title']):$product['title']);
      $brand = ((isset($_POST['brand']) && $_POST['brand'] != '')?sanitize($_POST['brand']):$product['brand']);
      $parentQ = $db->query("SELECT * FROM categories WHERE id = '$category'");
      $parentResult = mysqli_fetch_assoc($parentQ);
      $parent = ((isset($_POST['parent']) && $_POST['parent'] != '')?sanitize($_POST['parent']):$parentResult['parent']);
      $price = ((isset($_POST['price']) && $_POST['price'] != '')?sanitize($_POST['price']):$product['price']);
      $list_price = ((isset($_POST['list_price']) && $_POST['list_price'] != '')?sanitize($_POST['list_price']):$product['list_price']);
      $description = ((isset($_POST['description']) && $_POST['description'] != '')?sanitize($_POST['description']):$product['description']);
      $sizes = ((isset($_POST['sizes']) && $_POST['sizes'] != '')?sanitize($_POST['sizes']):$product['sizes']);
      $sizes = rtrim($sizes, ',');
      $saved_image = (($product['image'] != '')?$product['image']:'');
      $dbpath = $saved_image;
    }

    if (!empty($sizes)) {
      $sizeString = sanitize($sizes);
      $sizeString = rtrim($sizeString,',');
      $sizesArray = explode(',',$sizeString);
      $sArray = array();
      $qArray = array();
      foreach ($sizesArray as $ss) {
        $s = explode(':', $ss);
        $sArray[] = $s[0];
        $qArray[] = $s[1];
      }
    } else {
      $sizesArray = array();
    }

    if ($_POST) {
      $errors = array();
      $required = array('title', 'brand', 'parent', 'child', 'price', 'sizes');
      foreach($required as $field){
        if ($_POST[$field] == '') {
          $errors[] = 'All Fields With an Asterics are Required.';
          break;
        }
      }
      
      //Image validation

      if (!empty($_FILES)) {
        $photo = $_FILES['photo'];
        $name = $photo['name'];
        $nameArray = explode('.',$name);
        $fileName = $nameArray[0];
        $fileExt = $nameArray[1];
        $mime = explode('/',$photo['type']);
        $mimeType = $mime[0];
        $mimeExt = $mime[1];
        $tmpLoc = $photo['tmp_name'];
        $fileSize = $photo['size'];
        $allowed = array('png','jpg', 'jpeg', 'gif');
        $uploadName = md5(microtime()). '.' .$fileExt;
        $uploadPath = BASEURL. 'images/uploaded/'.$uploadName;
        $dbpath = '/Projects/InProgress/Boutique/images/uploaded/'.$uploadName;
        if ($mimeType != 'image') {
          $errors[] = 'The file must be an image.';
        }
        if (!in_array($fileExt, $allowed)){
          $errors[] = 'The image extension must be a png, jpg, jpeg or gif.';
        } 
        if ($fileSize > 15000000) {
          $errors[] = 'The file size must be under 15MB';
        }
        if ($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt != 'jpg')) {
          $errors[] = 'File extension does not match the file.';
        }
      }
      if (!empty($errors)) {
        echo display_errors($errors);
      } else {
        // Upload file and insert into database
        if (!empty($_FILES)) {
          move_uploaded_file($tmpLoc,$uploadPath);
        }
        $insertSql = "INSERT INTO products (title, price, list_price, brand, categories, image, description, sizes) VALUES ('$title', '$price', '$list_price', '$brand', '$category', '$dbpath', '$description', '$sizes')";
        $_SESSION['success_flash'] = 'Product has been added';

        if (isset($_GET['edit'])) {
          $insertSql = "UPDATE products SET title = '$title', price = '$price', list_price = '$list_price', brand = '$brand', categories = '$category', image = '$dbpath', description = '$description', sizes = '$sizes' WHERE id='$edit_id'";
          $_SESSION['success_flash'] = 'Product has been updated';
        }

        $db->query($insertSql);
        header('Location: products.php');
      }
    }
?>
  <div class="container">
    <h2 class="text-center"><?= ((isset($_GET['edit']))?'Edit':'Add A New'); ?> Product</h2>
    <hr>
    <form action="products.php?<?= ((isset($_GET['edit']))?'edit='.$edit_id:'add=1'); ?>" method="POST" enctype="multipart/form-data">
      <div class="form-group font-weight-bold">
        <div class="row">
          <div class="col-md-3 mt-4">
            <label for="title">Title *:</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= $title; ?>">
          </div>
          <div class="col-md-3 mt-4">
            <label for="brand">Brand *:</label>
            <select class="form-control custom-select" id="brand" name="brand">
              <option value=""<?= (($brand == '')?' selected':''); ?>></option>
              <?php while($b = mysqli_fetch_assoc($brandQuery)): ?>
                <option value="<?= $b['id']; ?>" <?= (($brand == $b['id'])?' selected':''); ?>><?= $b['brand']; ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-3 mt-4">
            <label for="parent">Parent Category *:</label>
            <select class="form-control custom-select" name="parent" id="parent">
              <option value=""<?= (($parent == '')?' selected':''); ?>></option>
                <?php while($p = mysqli_fetch_assoc($parentQuery)): ?>
                  <option value="<?= $p['id']; ?>" <?= (($parent == $p['id'])?' selected':''); ?>><?= $p['category']; ?></option>
                <?php endwhile;?>
            </select>
          </div>
          <div class="col-md-3 mt-4">
            <label for="child">Child Category *:</label>
            <select name="child" id="child" class="form-control custom-select "></select>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 mt-4">
            <label for="price">Price *:</label>
            <input type="text" name="price" id="price" class="form-control" value="<?= $price; ?>">
          </div>
          <div class="col-md-3 mt-4">
            <label for="list_price">List Price :</label>
            <input type="text" name="list_price" id="list_price" class="form-control" value="<?= $list_price; ?>">
          </div>
          <div class="col-md-3 mt-4">
            <label>Quantity & Sizes *:</label>
            <button class="btn btn-secondary form-control" onclick="jQuery('#sizesModal').modal('toggle');return false;">Quantity & Sizes</button>
          </div>
          <div class="col-md-3 mt-4">
            <label for="sizes">Sizes & Quantity Preview</label>
            <input type="text" class="form-control" name="sizes" id="sizes" value="<?= $sizes; ?>" readonly>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 mt-4">
            <label for="photo">Product Image :</label>
            <?php if($saved_image != ''): ?>
              <div class="text-center">
                <img src="<?= $saved_image; ?>" class=" img-fluid" style="height:300px;" alt="Saved Image">
              </div>
              <a href="products.php?delete_image=1&edit=<?= $edit_id; ?>" class="text-danger card-link">Delete Image</a></a>
            <?php else: ?>
              <div class="custom-file">
                <input type="file" name="photo" id="photo" class="custom-file-input">
                <label class="custom-file-label" for="photo">Choose file</label>
              </div>  
            <?php endif; ?>        
          </div>
          <div class="col-md-6 mt-4">
            <label for="description">Description :</label>
            <textarea name="description" id="description" cols="30" rows="6" class="form-control"><?= $description; ?></textarea>
          </div>
        </div>
        <div class="clearfix mt-4">
          <div class="float-right">
            <a href="products.php" class="btn btn-secondary mr-2"><i class="fa fa-times-circle mr-2"></i>Cancel</a>
            <button type="submit" class="btn btn-success"><?= ((isset($_GET['edit']))?'<span class="fa fa-pen-fancy mr-2"></span>Edit':'<span class="fa fa-plus-circle mr-2"></span>Add'); ?> Product</button>
          </div>
        </div>
      </div>
    </form>

        <!-- The Modal -->
    <div class="modal fade" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">

          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title" id="sizesModal">Size & Quantity</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Modal body -->
          <div class="modal-body">
            <div class="form-group">
              <div class="row">
                <?php for($i = 1;$i <= 12;$i++): ?>
                  <div class="col-md-4">
                    <label for="size"<?= $i; ?>>Size :</label>
                    <input type="text" name="size<?= $i; ?>" id="size<?= $i; ?>" value="<?= ((!empty($sArray[$i - 1]))?$sArray[$i - 1]:'') ?>" class="form-control">
                  </div>
                  <div class="col-md-2">
                    <label for="qty"<?= $i; ?>>Quantity</label>
                    <input type="number" name="qty<?= $i; ?>" id="qty<?= $i; ?>" value="<?= ((!empty($qArray[$i - 1]))?$qArray[$i - 1]:'') ?>" min="0" class="form-control">
                  </div>
                <?php endfor; ?>
              </div>
            </div>
          </div>

          <!-- Modal footer -->
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" onclick="updateSizes();jQuery('#sizesModal').modal('toggle');return false;">Save Changes</button>
          </div>

        </div>
      </div>
    </div>

  </div>
<?php  
  } else {
  $sql = "SELECT * FROM products WHERE deleted = 0";
  $presults = $db->query($sql);
  
  // if (isset($_GET['featured']) && !empty($_GET['featured'])) {
  if (isset($_GET['featured'])) {
    $id = (int)$_GET['id'];
    $featured = (int)$_GET['featured'];
    $featuredsql = "UPDATE products SET featured = '$featured' WHERE id = '$id'";
    $db->query($featuredsql);
    header('Location: products.php');
  }
?>


<div class="container">
  <h2 class="text-center">Products</h2>
  <div class="clearfix">
    <a href="products.php?add=1" class="btn btn-success float-right"><?= ((isset($_GET['edit']))?'':'<span class="fa fa-plus-circle mr-2"></span>Add'); ?> Product</a>
  </div>
  <hr>
  <div class="table-responsive-sm">
    <table class="table table-sm table-hover table-striped">
      <thead>
        <th></th>
        <th>Product</th>
        <th>Price</th>
        <!-- <th>List Price</th> -->
        <th>Category</th> 
        <th>Featured</th>
        <th>Sold</th>
      </thead>
      <tbody class="">
        <?php while($product = mysqli_fetch_assoc($presults)): 
          $childID = $product['categories'];
          $catSql = "SELECT * FROM categories WHERE id = '$childID'";
          $result = $db->query($catSql);
          $child = mysqli_fetch_assoc($result);
          $parentID = $child['parent'];
          $pSql = "SELECT * FROM categories WHERE id = '$parentID'";
          $presult = $db->query($pSql);
          $parent = mysqli_fetch_assoc($presult);
          $category = $parent['category'].' - '.$child['category'];
          // $category = get_category($product);
        ?>
        <tr>
          <td>
          <div class="btn-group btn-group-sm">
            <a href="products.php?edit=<?= $product['id']; ?>" class="btn btn-sm btn-primary mr-sm-2"><span class="fa fa-pen-fancy"></span></a>
            <a href="products.php?delete=<?= $product['id']; ?>" class="btn btn-sm btn-primary"><span class="fa fa-trash-alt"></span></a>
          </div>
            <!-- <a href="products.php?edit=<?= $product['id']; ?>" class="btn btn-sm btn-primary mb-2 mr-sm-2"><span class="fa fa-pen-alt"></span></a>
            <a href="products.php?delete=<?= $product['id']; ?>" class="btn btn-sm btn-primary mb-2"><span class="fa fa-trash-alt"></span></a> -->
          </td>
          <td><?= $product['title']; ?></td>
          <td><?= money($product['price']); ?></td>
          <!-- <td><?= money($product['list_price']); ?></td> -->
          <td><?= $category; ?></td>
          <td>
            <a href="products.php?featured=<?= (($product['featured'] == 0)?'1':'0'); ?>&id=<?= $product['id']; ?>" class="btn btn-sm btn-primary">
              <span class="fa fa-<?= (($product['featured'] == 1)?'minus':'plus'); ?>"></span>
              </a>
              &nbsp; <?= (($product['featured'] == 1)?'Featured Product':''); ?>
            </td>
            <td>0</td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php } include 'includes/footer.php'; ?>
<script>
  jQuery('document').ready(function(){
    get_child_options('<?= $category; ?>');
  });
</script>