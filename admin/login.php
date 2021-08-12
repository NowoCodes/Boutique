<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/core/init.php';
    include 'includes/head.php';
    
	if (is_logged_in()){
		header('Location: index.php');
	}
    // $password = 'password';
    // $hashed = password_hash($password, PASSWORD_DEFAULT);
    // echo $hashed;

    $email = ((isset($_POST['email']))?sanitize(($_POST['email'])):'');
    $email = trim($email);
    $password = ((isset($_POST['password']))?sanitize(($_POST['password'])):'');
    $password = trim($password);
    $errors = array();
    $success = array();
?>

<style>
    body{
        background-image: url("/Projects/InProgress/Boutique/images/headerlogo/background.png");
        background-size: 100vw 100vh;
        background-attachment: fixed;
    }
</style>
<!-- <div class="container w-50 border border-dark rounded-lg mx-auto shadow-lg" id="login-form"> -->
<div id="login-form">
    <div>
    <?php
        if ($_POST) {
            //form validation
            if (empty($_POST['email']) && empty($_POST['password'])) {
                $errors[] = 'You must provide Email and Password.';
            } else {
                if (empty($_POST['email'])) {
                    $errors[] = 'You must provide email.';
                }

                if (empty($_POST['password'])) {
                    $errors[] = 'You must provide password.';
                }

                // validate email
                if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
                    $errors[] = 'You must enter a valid email.';
                }
            }

            // password is more than 6 characters
            // if (strlen($password) < 6) {
            //     $errors[] = 'Password must be at least 6 characters.';
            // }

            // check if email exists in the database
            $query = $db->query("SELECT * FROM users WHERE email = '$email'");
            $user = mysqli_fetch_assoc($query);
            $userCount = mysqli_num_rows($query); 

            if (!empty($_POST['email'])) {
               if ($userCount == 0) {
                $errors[] = 'Email doesn\'t exist in our database.';
                } 
            }
            
            if (!empty($_POST['password'])) {
                if(!password_verify($password, $user['password'])){
                    $errors[] = 'Invalid password. Try again.';
                }
            }

            //Check for errors
            if(!empty($errors)){
                echo display_errors($errors);
            } else {
                //Log User in
                $user_id = $user['id'];
                login($user_id);
            }
        }
        
    ?>
    </div>
    <h2 class="text-center">Login</h2>
    <hr>
    <form action="login.php" method="POST">
        <div class="form-group">
            <div>
                <label class="mb-2" for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control mb-2" value="<?= $email; ?>">
            </div>
            <div>
                <label class="mb-2" for="password">Password</label>
                <input type="password" name="password" id="password" class="mb-2 form-control" value="<?= $password; ?>">
            </div>
            <div>
                <input type="submit" class="btn btn-primary" value="Login">
            </div>           
        </div>
    </form>
    <div class="clearfix">
    <p class="float-right"><a href="/Projects/InProgress/Boutique/index.php" alt="home">Visit Site</a></p>
    </div>
</div>


<?php include 'includes/footer.php'; ?>