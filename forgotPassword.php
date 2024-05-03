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

    // Retrieve contact information
    $school = $db->getSchoolName($username);
    $contactInfo = $db->getContactInfoByUsername($username, $school);

    // Check if contactInfo is not null before accessing the variables
    if ($contactInfo) {
        $email = $contactInfo['email'];
        $phoneNumber = $contactInfo['phoneNumber'];
    } else {
        // Handle the case where contact information is not found
        $email = "N/A";
        $phoneNumber = "N/A";
    }
} else {
    echo "User not logged in.";
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

/*echo "Username: $username";
echo "Email: $email";
echo "Phone Number: $phoneNumber";*/


// Ensure that email and phone number are not empty
if (empty($email) && empty($phoneNumber)) {
    // Invalid username, redirect to login page or show an error message
    header("Location: email.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which method is selected (email or phone)
    $sendMethod = $_POST["sendMethod"];

    // ... (other existing logic)

    // Send verification code
    $verificationCode = $db->generateVerificationCode();
    echo "Verification Code: $verificationCode";
    $school = $db->getSchoolName($username);
    $result = $db->storeVerificationCode($username, $verificationCode, $school);

    if ($result) {
        echo 'Verification code stored successfully in the database.';
    } else {
        echo 'Error storing verification code in the database.';
    }


    $contactInfo = $db->getContactInfoByUsername($username, $school);


    if ($sendMethod === "email") {
        $success = $db->sendVerificationCodeByEmail($contactInfo['email'], $verificationCode);
        echo "Success report: $success";
    } elseif ($sendMethod === "phone") {
        $success = $db->sendVerificationCodeBySMS($contactInfo['phoneNumber'], $verificationCode);
        echo "Success report: $success";
        $success = true;  // Placeholder for SMS sending success
    }

    if ($success) {
        echo "Verification code sent successfully!";
    } else {
        echo "Error sending verification code.";
    }
    header("Location: code.php");
    exit();
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
               <form action="forgotPassword.php" method="post">
                 <div class="question">
                 <h1>Would You Like Us to Send the Code to Your<br>Email or Phone Number?</h1>
                 </div>
                 <button type="submit" name="sendMethod" value="email" class="button2">Email</button>
                 <button type="submit" name="sendMethod" value="phone" class="button2">Phone #</button>
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