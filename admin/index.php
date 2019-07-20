<?php
	require_once '../core/init.php';
	if (!is_logged_in()){
		header('Location: login.php');
	}


	include 'includes/head.php';
	include 'includes/navigation.php';

?>
<div class="container">
	<h2 class="text-center">Administrator Home</h2>
</div>
<?php include 'includes/footer.php'; ?>

