<?php


// Initialize the session
session_start();

require_once("config/db.php");

// Define variables and initialize with empty values
$username = $password = "";
$error = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $error = "error";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))) {
        $error = "error";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($error)) {
        // Prepare a select statement
        $sql = "SELECT id, username, password, admin FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()) {
                // Check if username exists, if yes then verify password
                if($stmt->rowCount() == 1) {
                    if($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $username = $row["username"];
                        $admin = $row["admin"];
                        $hashed_password = $row["password"];
                        if(password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["user_id"] = $id;
                            $_SESSION["username"] = $username;                            
                            $_SESSION["admin"] = $admin;
                        }
                    }
                }
			} 
        }

        // Redirect user to welcome page
		header("location: index.php");
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
}
?>