<?php
session_start();
require_once("config/db.php"); 

require_once("templates/header.php");

// Define variables and initialize with empty values
$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = ""; 

if (isset($_POST) && !empty($_POST)) {
	 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
	}
	
	// Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "This email is already taken.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
         
        if($stmt = $pdo->prepare($sql)){
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Bind variables to the prepared statement as parameters
			$stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
			$stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                header("location: index.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
}


?>

<body>
	<?php require_once("templates/header_body.php"); ?>

	<div class="container">
		<div class="card">
			<article class="card-body mx-auto" style="max-width: 400px;">
				<h4 class="card-title mt-3 text-center">Create Account</h4>
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
					<div class="form-group input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"> <i class="fa fa-user"></i> </span>
						</div>
						<input name="username" class="form-control" placeholder="Username" type="text" required>
						<span class="help-block"><?php echo $username_err; ?></span>
					</div> <!-- form-group// -->
					<div class="form-group input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"> <i class="fa fa-envelope"></i> </span>
						</div>
						<input name="email" class="form-control" placeholder="Email " type="email" required>
						<span class="help-block"><?php echo $email_err; ?></span>
					</div> <!-- form-group// -->
					<div class="form-group input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"> <i class="fa fa-lock"></i> </span>
						</div>
						<input class="form-control" placeholder="Create password" name="password" type="password"
							required>
							<span class="help-block"><?php echo $password_err; ?></span>
					</div> <!-- form-group// -->
					<div class="form-group input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"> <i class="fa fa-lock"></i> </span>
						</div>
						<input class="form-control" placeholder="confirm password" name="confirm_password"
							type="password" required>
					</div> <!-- form-group// -->
					<div class="form-group">
						<button type="submit" class="btn btn-primary btn-block"> Create Account </button>
					</div> <!-- form-group// -->
				</form>
			</article>
		</div> <!-- card.// -->
	</div>

	<?php require_once("templates/footer.php"); ?>
</body>

</html>