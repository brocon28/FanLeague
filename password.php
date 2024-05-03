<?php
session_start();

// Ensure that the session contains a valid username
if (!isset($_SESSION['username'])) {
    // If not, redirect to login page or display an error message
    header("Location: login.php");
    exit();
}

// Include the database functions
require "DataBase.php";

// Create an instance of the DataBase class
$db = new DataBase();

// Establish the database connection
$db->dbConnect();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    /*if ($db->usernameExists($username)) {
        */
        if (isset($_POST['password'])) {
            // Get the new password from the form submission
            $newPassword = $_POST['password'];
            
            // Assuming $db is your database object and $username is set appropriately
            // Update the password in the database
            $school = $db->getSchoolName($username);
            $db->updatePassword($username, $newPassword, $school);
        
            // Redirect to login page or any other page
            header("Location: login.php");
            exit;
        }
}
?>

<!doctype html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Login</title>
      <link rel="stylesheet" href="forgotPassword.css" />
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Asap+Condensed:wght@400;700&family=Barlow:wght@400;700&display=swap" rel="stylesheet">
   </head>
   <body>
      <!--General Login Background can put an image behind if needed-->
      <div class="login-background">
         <!--Actual container for the login elements-->
         <div class="login-container">
            <!--Left login section-->
            <div class="login-section">
               <h2>Forgot Password</h2>
                 <form action="password.php" method="post">
                 <div class="question">
                 <!--<form action="password.php" method="post">-->
                 <h1>Enter Your New Password</h1>
                 </div>
                 <input type="password" id="password" name="password" placeholder="New Password" required />
                 <button type="submit" class="button2">Submit</button>
          </div>
               </form>
                <!-- Display error message -->
                <?php if (!empty($errorMessage)): ?>
                    <p style="color: red;"><?php echo $errorMessage; ?></p>
                    <?php endif; ?>
            </div>
           </div>
        </body>
</html>