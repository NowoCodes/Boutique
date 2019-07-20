<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/Projects/InProgress/Boutique/core/init.php';
    if (!is_logged_in()){
		login_error_redirect();
	}
	include 'includes/head.php';
    include 'includes/navigation.php';

    //restore product
    if (isset($_GET['restore'])) {
        $id = (int)$_GET['restore'];
        $id = sanitize($_GET['restore']);
        $db->query("UPDATE products SET deleted = 0 WHERE id = '$id'");
        header('Location: archived.php');
    }
    $sql = $db->query("SELECT * FROM products WHERE deleted = 1");
      
?>

<div class="container">
    <h2 class="text-center">Archived Products</h2>
    <hr>
    <div class="table-responsive-sm">
        <table class="table table-hover table-sm table-striped">
            <thead>
                <th></th>
                <th>Product</th>
                <th>Price</th>
                <th>Category</th>
                <th>Sold</th>
            </thead>
            <tbody>
                <?php while($product = mysqli_fetch_assoc($sql)): 
                    $childID = $product['categories'];
                    $catSql = "SELECT * FROM categories WHERE id = '$childID'";
                    $productq = $db->query($catSql);
                    $child = mysqli_fetch_assoc($productq);
                    $parentID = $child['parent'];
                    $pSql = "SELECT * FROM categories WHERE id = '$parentID'";
                    $presult = $db->query($pSql);
                    $parent = mysqli_fetch_assoc($presult);
                    $category = $parent['category'].' - '.$child['category'];
                    ?>
                <tr>
                    <td>
                        <a href="archived.php?restore=<?= $product['id']; ?>" class="btn btn-sm btn-primary mr-sm-2"><span class="fas fa-recycle"></span></a>
                    <td><?= $product['title']; ?></td>
                    <td><?= $product['price']; ?></td>
                    <td><?= $category; ?></td>
                    <td>0</td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


<?php include 'includes/footer.php'; ?>
