<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/Projects/InProgress/Boutique/core/init.php';
    if(!is_logged_in()){
        login_error_redirect();
    }
    include 'includes/head.php';

    $hashed = $user_data['password'];
    $old_password = ((isset($_POST['old_password']))?sanitize(($_POST['old_password'])):'');
    $old_password = trim($old_password);
    $password = ((isset($_POST['password']))?sanitize(($_POST['password'])):'');
    $password = trim($password);
    $confirm = ((isset($_POST['confirm']))?sanitize(($_POST['confirm'])):'');
    $confirm = trim($confirm);
    $new_hashed = password_hash($password, PASSWORD_DEFAULT);
    $user_id = $user_data['id'];
    $errors = array();
?>
<div id="login-form" class="font-weight-bold">
    <div>
    <?php
        if ($_POST) {
            //form validation
            if (empty($_POST['old_password']) || empty($_POST['password']) || empty($_POST['confirm'])) {
                $errors[] = 'You must fill out all fields.';
            }

            // password is more than 6 characters
            if (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters.';
            }

            // check if new password matches confirm
            if ($password != $confirm) {
                $errors[] = 'Passwords do not match.';
            }

            if(!password_verify($old_password, $hashed)){
                $errors[] = 'Old password does not match our record.';
            }
            
            //Check for errors
            if(!empty($errors)){
                echo display_errors($errors);
            } else {
                //change password
                $db->query("UPDATE users SET password = '$new_hashed' WHERE id = '$user_id'");
                $_SESSION['success_flash'] = 'Your password has been updated';
                header('Location: index.php');
            }
        }
        
        


    ?>
    </div>
    <h2 class="text-center">Change Password</h2>
    <hr>
    <form action="change_password.php" method="POST">
        <div class="form-group">
            <div>
                <label class="mb-2" for="old_password">Old Password:</label>
                <input type="password" name="old_password" id="old_password" class="form-control mb-2" value="<?= $old_password; ?>">
            </div>
            <div>
                <label class="mb-2" for="password">New Password:</label>
                <input type="password" name="password" id="password" class="mb-2 form-control" value="<?= $password; ?>">
            </div>
            <div>
                <label class="mb-2" for="confirm">Confirm New Password:</label>
                <input type="password" name="confirm" id="confirm" class="mb-2 form-control" value="<?= $confirm; ?>">
            </div>
            <div>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <input type="submit" class="btn btn-primary" value="Login">
            </div>           
        </div>
    </form>
    <div class="clearfix">
    <p class="float-right"><a href="/Projects/InProgress/Boutique/index.php" alt="home">Visit Site</a></p>
    </div>
</div>


<?php include 'includes/footer.php'; ?>