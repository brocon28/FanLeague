<?php

require "DataBase.php";
session_start(); // Start the session

$db = new DataBase();
$errorMessage = "";

if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($db->dbConnect()) {
        $username = $_POST['username'];
        $query = "SELECT school FROM users WHERE username = '$username'";
        $result = $db->dbQuery($query);
        if ($result) {
            $row = $result->fetch_assoc();
            $school = $row['school'];
            $table = $school . "users";

            if ($db->logIn($table, $_POST['username'], $_POST['password'])) {
                // Set the username and fname in the session
                $_SESSION['username'] = $_POST['username'];

                // Retrieve the fullname from the database
                $userDetails = $db->getUserDetails($table, $_POST['username']);
                if ($userDetails) {
                    $_SESSION['fname'] = $userDetails['fname'];
                    $_SESSION['fullname'] = $userDetails['fullname'];
                }

                header("Location: home.php");
                exit();
            } else {
                $errorMessage = "*Username or Password Wrong*";
            }
    }} else {
        $errorMessage = "Error: Database connection";
    }
}



/*
require "DataBase.php";
session_start(); // Start the session

$db = new DataBase();

if (isset($_POST['username']) && isset($_POST['password'])) {
    if ($db->dbConnect()) {
        if ($db->logIn("users", $_POST['username'], $_POST['password'])) {
            // Set the username and fname in the session
            $_SESSION['username'] = $_POST['username'];

            // Retrieve the fullname from the database
            $userDetails = $db->getUserDetails("users", $_POST['username']);
            if ($userDetails) {
                $_SESSION['fname'] = $userDetails['fname'];
                $_SESSION['fullname'] = $userDetails['fullname'];
            }

            echo "Login Success";
            header("Location: home.php");
            exit();
        } else {
            echo "Username or Password wrong";
        }
    } else {
        echo "Error: Database connection";
    }
} else {
    echo "All fields are required";
}
*/

?>


<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Login</title>
      <link rel="stylesheet" href="login2.css" />
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
               <h2>Sign In</h2>

               <!-- Display error message -->
               <?php if (!empty($errorMessage)): ?>
                  <p style="color: red;"><?php echo $errorMessage; ?></p>
               <?php endif; ?>

               <!--signin form-->
               <form action="login.php" method="post">
                  <input type="text" id="username" name="username" placeholder="Username" required />
                  <input type="password" id="password" name="password" placeholder="Password" required />
                  <div class="forgot-password-container">
                     <a href="email.php" class="forgot-password">Forgot your password?</a>
                  </div>
                  <button type="submit" value="Login" class="sign-in-btn">SIGN IN</button>
               </form>
            </div>
            <!--Right login section-->
            <div class="login-section right">
               <div class="overlay-panel overlay-right">
                  <div class="signup-content">
                     <h1>Hey College Fans</h1>
                     <p>Join Your School's Fan League, and Start Earning Some Great Prizes!</p>
                    <button class="ghost" id="signUp">SIGN UP</button>
                  </div>
                  <!--signup form-->
                  <div class="registration-form" style="display: none;">
                     <h2 class="signup-heading">Create An Account</h2>
                    
                     <form action="signup.php" method="post">
                        <input type="text" id="fullname" name="fullname" class="signup-input" placeholder="Full Name" required />
                        <input type="text" id="username" name="username" class="signup-input" placeholder="Username" required />
                        <input type="password" id="password" name="password" class="signup-input" placeholder="Password" required />
                        <input type="email" id="email" name="email" class="signup-input" placeholder="Email" required />
                        <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required/>
                        <input type="text" id="school" name="school" class="signup-input" placeholder="School" required />
                        <button type="submit" class="ghost2">SIGN UP</button>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>

     <script>


       function redirectToLogin() {
           // Perform form validation if needed
           // Redirect to login.html
           window.location.href = "login.html";
           return true; // Return true to allow form submission
       }
       function redirectToHome() {
           // Perform form validation if needed
           // Redirect to home.html
           window.location.href = "home.html";
           return true; // Return true to allow form submission
       }

       
       const signUpButton = document.getElementById('signUp');
       const container = document.querySelector('.login-container');
       const signupContent = document.querySelector('.signup-content');
       const registrationForm = document.querySelector('.registration-form');
       const overlayPanel = document.querySelector('.overlay-panel.overlay-right');

       signUpButton.addEventListener('click', () => {
           container.classList.add('right-panel-active');
           signupContent.style.display = 'none';
           registrationForm.style.display = 'block';
           overlayPanel.style.padding = '1vw'; // Adjust padding
           
       });
     </script>

   </body>
</html>