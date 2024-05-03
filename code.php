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
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which method is selected (email or phone)
    $enteredCode = $_POST["code"];
    echo "Entered Code: $enteredCode";
    echo "Username: $username";
    $school = $db->getSchoolName($username);
    $sql = "SELECT verificationCode FROM " . $school . "users WHERE username = '$username'";
    $result = mysqli_query($db->connect, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $realCode = $row['verificationCode'];

        if ($enteredCode == $realCode) {
            echo "Real Code: $realCode, Entered Code: $enteredCode";
            header("Location: password.php");
            exit;
        } else {
            $errorMessage = "Verification code is incorrect.";
        }
    } else {
        $errorMessage = "Error querying the database.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <form action="code.php" method="post">
                    <div class="question">
                        <h1>Enter the Code Sent to Your Phone#/Email</h1>
                    </div>
                    <input type="text" id="code" name="code" placeholder="Code" required />
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
