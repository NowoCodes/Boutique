<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/Projects/InProgress/Boutique/core/init.php';
  if (!is_logged_in()){
	 login_error_redirect();
  }
	include 'includes/head.php';
	include 'includes/navigation.php';
	$sql = "SELECT * FROM categories WHERE parent = 0";
	$result = $db->query($sql);
	$errors = array();
	$category = '';
	$post_parent = '';
	
	//Edit Category
  if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_id = sanitize($edit_id);
    $edit_sql = "SELECT * FROM categories WHERE id = '$edit_id'";
    $edit_result = $db->query($edit_sql);
    $edit_category = mysqli_fetch_assoc($edit_result);
  }
	$category_value = '';
	$parent_value = 0;
	if (isset($_GET['edit'])){
		$category_value = $edit_category['category'];
		$parent_value = $edit_category['parent'];
	} else {
		if (isset($_POST)){
			$category_value = $category;
			$parent_value = $post_parent;
		}
	}
 
	//Delete Category
  if (isset($_GET['delete']) && !empty($_GET['delete'])){
    $delete_id = (int)$_GET['delete'];
    $delete_id = sanitize($delete_id);
    $sql = "SELECT * FROM categories WHERE id = '$delete_id'";
    $result = $db->query($sql);
    $delete_category = mysqli_fetch_assoc($result);
    if ($delete_category['parent'] == 0){
      $sql = "DELETE FROM categories WHERE parent = '$delete_id'";
      $db->query($sql);
    }
    $dsql = "DELETE FROM categories WHERE id = '$delete_id'";
    $db->query($dsql);
    header('Location: categories.php');
  }
	
	//Process form
  if (isset($_POST) && !empty($_POST)){
    $post_parent = sanitize($_POST['parent']);
    $category = sanitize($_POST['category']);
    $sqlform = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent'";
    if (isset($_GET['edit'])){
      $id = $edit_category['id'];
      $sqlform = "SELECT * FROM categories WHERE category = '$category' AND parent = '$post_parent' AND id != '$id'";
    }
    $fresult = $db->query($sqlform);
    $count = mysqli_num_rows($fresult);
    //if category is blank
    if ($category == ''){
      $errors[] .= 'The category cannot be left blank.';
    }
  
    //If exists in the database
    if ($count > 0){
      $errors[] .= $category. ' already exists. Please choose a new category.';
    }
    
    //Display Errors or Update Database
    if (!empty($errors)){
      //display errors
      $display = display_errors($errors); ?>
      <script>
        jQuery('document').ready(function () {
          jQuery('#errors').html('<?= $display; ?>')
        });
      </script>
    <?php
    } else {
      //update database
      $updatesql = "INSERT INTO categories (category, parent) VALUES ('$category','$post_parent')";
      if (isset($_GET['edit'])){
        $updatesql = "UPDATE categories SET category = '$category', parent = '$post_parent' WHERE id = '$edit_id'";
      }
      $db->query($updatesql);
      header('Location: categories.php');
    }
  }
?>

<div class="container">
  <h2 class="text-center">Categories</h2>
  <hr>
  <div class="row">
<!--        form-->
    <div class="col-md-6">
      <form action="categories.php<?= ((isset($_GET['edit']))?'?edit='.$edit_id:''); ?>" method="post">
          <h2><?= ((isset($_GET['edit']))?'Edit':'Add A'); ?> Category</h2><hr>
          <div id="errors"></div>
          <div class="form-group">
            <label for="parent" class="font-weight-bold">Parent</label>
            <select class="form-control custom-select" name="parent" id="parent">
              <option value="0"<?= (($parent_value == 0)?'selected = "selected"':'');?>>Parent</option>
              <?php while ($parent = mysqli_fetch_assoc($result)) : ?>
                <option value="<?= $parent['id']; ?>"<?= (($parent_value == $parent['id'])?'selected = "selected"':''); ?>><?= $parent['category']; ?></option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="category" class="font-weight-bold">Category</label>
            <input type="text" class="form-control text-capitalize" id="category" name="category" value="<?= $category_value; ?>">
          </div>
          <div class="form-group">
            <?php if(isset($_GET['edit'])): ?>
              <a class="btn btn-secondary mr-2" href="categories.php"><span class="fa fa-times-circle mr-2"></span>Cancel</a>
            <?php endif; ?>
            <button type="submit" class="btn btn-success"><?= ((isset($_GET['edit']))?'<span class="fa fa-pen-fancy mr-2"></span>Edit':'<span class="fa fa-plus-circle mr-2"></span>Add'); ?> Category</button>
          </div>
      </form>
    </div>
<!--        Category Table-->
    <div class="col-md-6">
      <div class="table-responsive-sm">
        <table class="table table-sm table-hover">
          <thead>
            <th>Category</th>
            <th>Parent</th>
            <th></th>
          </thead>
          <tbody>
            <?php
              $sql = "SELECT * FROM categories WHERE parent = 0";
              $result = $db->query($sql);
              while ($parent = mysqli_fetch_assoc($result)) :
              $parent_id = (int)$parent['id'];
              $sql2 = "SELECT * FROM categories WHERE parent = '$parent_id'";
              $cresult = $db->query($sql2);
            ?>
            <tr class="bg-primary font-weight-bold">
              <td><?= $parent['category']; ?></td>
              <td>Parent</td>
              <td>
              <div class="btn-group btn-group-sm">
                <a href="categories.php?edit=<?= $parent['id']; ?>" class="btn btn-sm btn-secondary mr-sm-2"><span class="fa fa-pen-fancy"></span></a>
                <a href="categories.php?delete=<?= $parent['id']; ?>" class="btn btn-sm btn-secondary"><span class="fa fa-trash-alt"></span></a>
              </div>
                <!-- <a href="categories.php?edit=<?= $parent['id']; ?>" class="btn btn-sm btn-secondary"><span class="fa fa-pen-alt"></span></a>
                <a href="categories.php?delete=<?= $parent['id']; ?>" class="btn btn-sm btn-secondary"><span class="fa fa-trash-alt"></span></a> -->
              </td>
            </tr>
              <?php while ($child = mysqli_fetch_assoc($cresult)) : ?>
                <tr class="" style="background-color: lightblue;">
                  <td><?= $child['category']; ?></td>
                  <td><?= $parent['category']; ?></td>
                  <td>
                  <div class="btn-group btn-group-sm">
                    <a href="categories.php?edit=<?= $child['id']; ?>" class="btn btn-sm btn-secondary mr-sm-2"><span class="fa fa-pen-fancy"></span></a>
                    <a href="categories.php?delete=<?= $child['id']; ?>" class="btn btn-sm btn-secondary"><span class="fa fa-trash-alt"></span></a>
                  </div>
                    <!-- <a href="categories.php?edit=<?= $child['id']; ?>" class="btn btn-sm btn-secondary"><span class="fa fa-pen-alt"></span></a>
                    <a href="categories.php?delete=<?= $child['id']; ?>" class="btn btn-sm btn-secondary"><span class="fa fa-trash-alt"></span></a> -->
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
	
<?php include 'includes/footer.php';