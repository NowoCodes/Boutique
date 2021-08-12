<?php
	$db = mysqli_connect('127.0.0.1', 'root', '', 'shauntas_boutique');
	if (mysqli_connect_errno()){
		echo 'Database connection failed with following errors: '. mysqli_connect_error();
		die();
	}

	session_start();	
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
	require_once BASEURL.'helpers/helpers.php';

	$cart_id = '';
	if (isset($_COOKIE[CART_COOKIE])){
		$cart_id = sanitize($_COOKIE[CART_COOKIE]);
	}

	if (isset($_SESSION['User'])) {
		$user_id = $_SESSION['User'];
		$query = $db->query("SELECT * FROM users WHERE id = '$user_id'");
		$user_data = mysqli_fetch_assoc($query);
		$fn = explode(' ', $user_data['full_name']);
		$user_data['first'] = $fn[0];
		$user_data['last'] = $fn[1];
	}
	if (isset($_SESSION['user'])) {
		$user_idd = $_SESSION['user'];
		$query = $db->query("SELECT * FROM users WHERE id = '$user_idd'");
		$user_data = mysqli_fetch_assoc($query);
		$fn = explode(' ', $user_data['full_name']);
		$user_data['first'] = $fn[0];
		$user_data['last'] = $fn[1];
	}

	if (isset($_SESSION['success_flash'])) {
		echo '<div class="container alert alert-success alert-dismissible fade show">';
		echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		echo '<li class="text-success list-unstyled">'.$_SESSION['success_flash'].'</li>';
		echo '</div>';
		unset($_SESSION['success_flash']);
	}

	if (isset($_SESSION['error_flash'])) {
		echo '<div class="container alert alert-danger alert-dismissible fade show">';
		echo '<button type="button" class="close" data-dismiss="alert">&times;</button>';
		echo '<li class="text-danger list-unstyled">'.$_SESSION['error_flash'].'</li>';
		echo '</div>';
		unset($_SESSION['error_flash']);
	}
?>