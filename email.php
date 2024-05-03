<?php
session_start();
require "DataBase.php";

$db = new DataBase();

$db->dbConnect();

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $emailUsername = $db->grabUsernameByEmail($username);

    if ($emailUsername) {
        // Username retrieved, proceed with password reset
        //$_SESSION['username'] = $emailUsername;
        $username = $emailUsername;
    } else {
        // Username does not exist, display error message
        $errorMessage = "Email Not Real";
    }

   if ($db->usernameExists($username)) {
      // Username exists, store it in the session
      $_SESSION['username'] = $username;

      // Redirect to forgotPassword.php
       header("Location: forgotPassword.php");
       exit();
   } else {
      // Username does not exist, display error message
      $errorMessage = "Invalid username or email.";
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
               <form action="email.php" method="post">
                 <div class="question">
                 <h1>Enter Your Username or Email</h1>
                 </div>
                 <input type="text" id="username" name="username" placeholder="Username/Email" required />
                 <button type="submit" class="button2">Submit</button>
               </form>
               <!-- Display error message -->
               <?php if (!empty($errorMessage)): ?>
                  <p style="color: red;"><?php echo $errorMessage; ?></p>
               <?php endif; ?>
            </div>
         </div>
      </div>
   </body>
</html>
