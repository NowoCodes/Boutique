<?php
	require_once '../core/init.php';
	if (!is_logged_in()){
		login_error_redirect();
	}

	if(!has_permission('admin')){
		permission_error_redirect('index.php');
	}
	include 'includes/head.php';
	include 'includes/navigation.php';

	if (isset($_GET['delete'])) {
		$delete_id = sanitize($_GET['delete']);
		$db->query("DELETE FROM users WHERE id = '$delete_id'");
		$_SESSION['success_flash'] = 'User has been deleted';
		header('Location: users.php');
	}

	if (isset($_GET['add']) || isset($_GET['edit'])) {
		$name = ((isset($_POST['name']))?sanitize($_POST['name']):'');
		$email = ((isset($_POST['email']))?sanitize($_POST['email']):'');
		$password = ((isset($_POST['password']))?sanitize($_POST['password']):'');
		$confirm = ((isset($_POST['confirm']))?sanitize($_POST['confirm']):'');
		$permissions = ((isset($_POST['permissions']))?sanitize($_POST['permissions']):'');
		$errors = array();

		// edit user
		if (isset($_GET['edit'])){
			$edit_id = (int)$_GET['edit'];
			$dbq = $db->query("SELECT * FROM users WHERE id = '$edit_id'");
			$user_profile = mysqli_fetch_assoc($dbq);

			$name = ((isset($_POST['name']))?sanitize($_POST['name']):$user_profile['full_name']);
			$email = ((isset($_POST['email']))?sanitize($_POST['email']):$user_profile['email']);
			$password = ((isset($_POST['password']))?sanitize($_POST['password']):$user_profile['password']);
			$permissions = ((isset($_POST['permissions']))?sanitize($_POST['permissions']):$user_profile['permissions']);
		}


		if ($_POST) {
		    if (isset($_GET['add'])){
                $emailQuery = $db->query("SELECT full_name,email,password,permissions FROM users WHERE email = '$email'");
                $emailCount = mysqli_num_rows($emailQuery);
					
                if($emailCount != 0){
                    $errors[] = 'Email already exists.';
                }
            
                $required = array('name', 'email', 'password', 'confirm', 'permissions');
                foreach ($required as $f) {
                    if (empty($_POST[$f])) {
                        $errors[] = 'You must fill out all fields.';
                        break;
                    }
                }
            
                if (strlen($password) < 6) {
                    $errors[] = 'Password must be at least 6 characters.';
                }
            
                if ($password != $confirm) {
                    $errors[] = 'Passwords do not match.';
                }
            
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'Enter a valid email';
                }
            }

            if ($_GET['edit']) {
            	if(!empty($password) && (strlen($password) < 6) || !empty($confirm) && (strlen($confirm) < 6)){
                    $errors[] = 'Password must be at least 6 characters.';            		
            	}

            	if(!empty($password) && ($password != $confirm) || !empty($confirm) && ($password != $confirm)){
                    $errors[] = 'Passwords do not match.';            		
            	}
            }
		    
			if(!empty($errors)){
				echo display_errors($errors);
			} else {
				// add user to database
				$hashed = password_hash($password, PASSWORD_DEFAULT);
				$insertsql = "INSERT INTO users (full_name, email, password, permissions) VALUES ('$name', '$email', '$hashed', '$permissions')";
				$_SESSION['success_flash'] = 'User has been added';

				if (isset($_GET['edit'])) {
					if (empty($password)) {
						$insertsql = "UPDATE users SET full_name = '$name', email = '$email', permissions = '$permissions' WHERE id = '$edit_id'";
					}
					// $_SESSION['success_flash'] = 'User has been updated';	
					elseif(!empty($password) && ($password = $confirm)){
	                    $insertsql = "UPDATE users SET full_name = '$name', email = '$email', password = '$hashed', permissions = '$permissions' WHERE id = '$edit_id'";
	            	}	
					$_SESSION['success_flash'] = 'User has been updated';	         					
				}
                $db->query($insertsql);
                header('Location: users.php');
			}
		}
?>
		<div class="container font-weight-bold">
			<h2 class="text-center"><?= ((isset($_GET['add']))?'Add A New':'Edit'); ?> User</h2>
			<hr>
			<form action="users.php?<?= ((isset($_GET['add']))?'add=1':'edit=' .$edit_id) ?>" method="POST">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6 mb-2">
							<label for="name">Full Name:</label>
							<input type="text" name="name" id="name" class="form-control" value="<?= $name; ?>">
						</div>
						<div class="col-md-6 mb-2">
							<label for="email">Email:</label>
							<input type="email" name="email" id="email" class="form-control" value="<?= $email; ?>">
						</div>
						<div class="col-md-6 mb-2">
							<label for="password">Password:</label>
							<input type="password" name="password" id="password" class="form-control" value="">
						</div>
						<div class="col-md-6 mb-2">
							<label for="confirm">Confirm Password:</label>
							<input type="password" name="confirm" id="confirm" class="form-control" value="">
						</div>
						<div class="col-md-6 mb-2">
							<label for="permissions">Permissions:</label>
<!--                            Permissions cannot be changed by anyone-->
							<select name="permissions" id="permissions" class="form-control" <?= ((isset($_GET['edit'])
                            )?' disabled':''); ?>>
								<option value=""<?= (($permissions == '')?' selected':''); ?>></option>
								<option value="editor"<?= (($permissions == 'editor')?' selected':'');
								?>>Editor</option>
								<option value="admin,editor"<?= (($permissions == 'admin,editor')?' selected':''); ?>>Admin</option>
							</select>
						</div>
						<div class="col-md-6 mb-2 clearfix mt-4">
							<div class="float-right">
								<a href="users.php" class="btn btn-secondary mr-2"><span class="fa fa-times-circle mr-2"></span>Cancel</a>
								<button class="btn btn-success" type="submit"><?= ((isset($_GET['add']))?'<span class="fa fa-plus-circle mr-2"></span>Add':'<span class="fa fa-pen-fancy mr-2"></span>Edit') ?> User</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
<?php
	} else {
	$userQuery = $db->query("SELECT * FROM users ORDER BY permissions,full_name");
	$sam = mysqli_num_rows($userQuery);
?>

<div class="container">
	<h2 class="text-center">Users</h2>
	<div class="clearfix">
		<a href="users.php?add=1" class="btn btn-success float-right"><span class="fa fa-plus-circle mr-2"></span>Add New User</a>
	</div>
	<hr>
	<div class="table-responsive-sm">
	<table class="table table-borderless table-striped table-sm">
		<thead>
<!--            <th>#</th>-->
			<th></th>
			<th>Name</th>
			<th>Email</th>
			<th>Join Date</th>
			<th>Last Login</th>
			<th>Permissions</th>
		</thead>
		<tbody>
			<?php while($user = mysqli_fetch_assoc($userQuery)): ?>

<!--                    <td>--><?php //for($i=1; $i<=$sam; $i++) : ?>
<!--                        --><?//= $i; ?>
<!--                      --><?php //endfor; ?>
<!--                    </td>-->
					<td>
						<?php if($user['id'] != $user_data['id']): ?>
                          <div class="btn-group">
                              <a href="users.php?delete=<?= $user['id']; ?>" class="btn btn-primary mr-sm-2 btn-sm"><span class="fa fa-trash-alt"></span></a>
                              <a href="users.php?edit=<?= $user['id']; ?>" class="btn btn-primary btn-sm"><span
                                          class="fa fa-pen-fancy"></span></a>
                          </div>
						<?php endif; ?>
					</td>
					<td><?= $user['full_name']; ?></td>
					<td><?= $user['email']; ?></td>
					<td><?= pretty_date($user['join_date']); ?></td>
					<td><?= (($user['last_login']) == '0000-00-00 00:00:00')?'Never':pretty_date($user['last_login']); ?></td>
                    <td><span class="badge badge-pill badge-dark"><?= $user['permissions']; ?></span></td>
				</tr>
			<?php endwhile; ?>
		</tbody>
	</table>
	</div>
</div>
<?php } include 'includes/footer.php'; ?>
