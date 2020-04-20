<?php
// Initialize the session
session_start();
require_once("config/db.php"); 

if (!isset($_SESSION['user_id'])) {
	header("Location: /");
	exit;
}
require_once("templates/header.php");
 
// Define variables and initialize with empty values
$old_password = $new_password = $confirm_password = "";
$old_password_err = $new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
	
	// validate old password
	if (empty(trim($_POST['old_password']))) {
		$old_password_err = "enter old password";
	} else {
		$old_password = $_POST['old_password'];
	} 

    // Validate new password
    if(empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(
		empty($old_password_err) && 
		empty($new_password_err) && 
		empty($confirm_password_err)) {
			
		$sql = "SELECT username, password FROM users WHERE username = :username";

		if ($stmt = $pdo->prepare($sql)) {
			// Set parameters
			$param_username = $_SESSION['username'];
			
			// Bind variables to the prepared statement as parameters
			$stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

			if($stmt->execute()){
				// Check if username exists
                if($stmt->rowCount() == 1) {
					if($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $username = $row["username"];
                        $hashed_password = $row["password"];
                        if(password_verify($old_password, $hashed_password)) {
							// Prepare an update statement
							$sql = "UPDATE users SET password = :password WHERE username = :username";
				
							if($stmt = $pdo->prepare($sql)){
								// Set parameters
								$param_password = password_hash($new_password, PASSWORD_DEFAULT);
								$param_username = $username;
								
								// Bind variables to the prepared statement as parameters
								$stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
								$stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
								
								// Attempt to execute the prepared statement
								if($stmt->execute()){
									// Password updated successfully. Destroy the session, and redirect to login page
									session_destroy();
									header("location: index.php");
									exit();
								} else {
									echo "Oops! Something went wrong. Please try again later.";
								}
							}
                        }
                    }
					
				}
			}
		} 
    }
	// Close statement
	unset($stmt);
    // Close connection
    unset($pdo);
}
?>

<body>
	<?php require_once("templates/header_body.php"); ?>

	<div class="container">
		<h2>Reset Password</h2>
		<p>Please fill out this form to reset your password.</p>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
				<label>old Password</label>
				<input type="password" name="old_password" class="form-control">
				<span class="help-block"><?php echo $old_password_err; ?></span>
			</div>
			<div class="form-group <?php echo (!empty($new_password_err)) ? 'has-error' : ''; ?>">
				<label>New Password</label>
				<input type="password" name="new_password" class="form-control">
				<span class="help-block"><?php echo $new_password_err; ?></span>
			</div>
			<div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
				<label>Confirm Password</label>
				<input type="password" name="confirm_password" class="form-control">
				<span class="help-block"><?php echo $confirm_password_err; ?></span>
			</div>
			<div class="form-group">
				<input type="submit" class="btn btn-primary" value="Submit">
				<a class="btn btn-link" href="index.php">Cancel</a>
			</div>
		</form>
	</div>

	<?php require_once("templates/footer.php"); ?>

</body>
</html>