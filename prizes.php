<?php
session_start(); // Start the session

require "Database.php";

// Create an instance of the Database class
$database = new DataBase();

// Connect to the database
$connection = $database->dbConnect();


if (isset($_SESSION['username'])) {
    $fullname = $_SESSION['fullname'];
    $username = $_SESSION['username'];
    $fname = $_SESSION['fname'];
} else {
    echo "User not logged in.";
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

if (strlen($fullname) > 15) {
    // Split the fullname into an array of words
    $nameParts = explode(' ', $fullname);
    // Take the first part of the name
    $firstName = $nameParts[0];
    // Take the first letter of the last name
    $lastNameInitial = substr($nameParts[count($nameParts) - 1], 0, 1);
    // Concatenate the first name and the first initial of the last name
    $shortenedName = $firstName . " " . $lastNameInitial . ".";
    // Use $shortenedName as needed
    $fullname = $shortenedName;
 } 

global $school;
$school = $database->getSchool($connection, $username);
$schoolInfo = $database->getSchoolInfo($connection, $school);
$topItem = $schoolInfo['topItem'];
global $topItem;
// Check if data was retrieved successfully
if ($schoolInfo !== false) {
   // Access individual variables
   $primaryColor = $schoolInfo['primaryColor'];
   $secondaryColor = $schoolInfo['secondaryColor'];
   $primaryModifiedColor = $schoolInfo['primaryModifiedColor'];
   $topItem = $schoolInfo['topItem'];
   $upcomingEvent1 = $schoolInfo['upcomingEvent1'];
   $upcomingEvent2 = $schoolInfo['upcomingEvent2'];
   $upcomingEvent3 = $schoolInfo['upcomingEvent3'];
   $topTenTips = $schoolInfo['topTenTips'];
   $upcomingEventsImage = $schoolInfo['upcomingEventsImage'];
   $logo = $schoolInfo['logo'];
   $waves = $schoolInfo['waves'];
   $studentLogo = $schoolInfo['studentLogo'];
   $prizesImage = $schoolInfo['prizesImage'];
   $uploadImage = $schoolInfo['uploadImage'];
   $leaderboardImage = $schoolInfo['leaderboardImage'];
   
} else {
   // Handle the case where data retrieval failed
   echo "Failed to retrieve school information.";
}

mysqli_close($connection);
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Prizes</title>
      <link rel="stylesheet" href="prizes.css">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Asap+Condensed:wght@400;700&family=Barlow:wght@400;700&display=swap" rel="stylesheet">
      <style>
         .sidebar {
            background: linear-gradient(to bottom, <?php echo $primaryColor; ?>, <?php echo $secondaryColor; ?>);
         }
         .table-header.thead {
            color: <?php echo $primaryColor; ?>;
         } 
         h1 {
            color: <?php echo $primaryColor; ?>;
         }

         <?php
         // Check if $uploadImage is set and not empty
         if (isset($prizesImage) && !empty($prizesImage)) {
            // If image data is available, echo the CSS rule with base64 encoded image data
            echo '.background-image {
                        background-image: url("data:image/png;base64,' . base64_encode($prizesImage) . '");
                     }';
         } else {
            // If no image data is available, use the default image
            echo '.background-image {
                        background-image: url("pictures/userLogo.png");
                     }';
         }
         ?>
      </style>
   </head>
   <body>
      <div class="background-image"></div>
      <div class="container">
         <!--Sidebar-->
         <div class="sidebar">
            <div class="sidebar-top">
               <h1 class="league-title"><?php echo $school; ?><br/>Fan League</h1>
               <div class="profile-section">
                  <?php
                  // Check if $logoData is set and not empty
                  if (isset($studentLogo) && !empty($studentLogo)) {
                     // If image data is available, echo the image tag with base64 encoded image data
                     echo '<img src="data:image/png;base64,' . base64_encode($studentLogo) . '" alt="User Image" class="profile-pic" />';
                  } else {
                     // If no image data is available, display a placeholder image
                     echo '<img src="pictures/userLogo.png" alt="User Image" class="profile-pic" />';
                  }
                  ?>
                  <!--<img src="pictures/userLogo.png" alt="User Image" class="profile-pic" />-->
                  <div class="user-info">
                     <div class="user-name"><?php echo isset($fullname) ? $fullname : ''; ?></div>
                     <div class="user-role">Student</div>
                  </div>
               </div>
               <div class="nav">
                  <div class="nav-item"><a href="home.php">Dashboard</a></div>
                  <div class="nav-item"><a href="leaderboard.php">Leaderboard</a></div>
                  <div class="nav-item"><a href="#">Prizes</a></div>
                  <div class="nav-item"><a href="howToPlay.php">How to play</a></div>
                  <div class="nav-last">
                     <a href="login.html" class="sign-out-link">Sign Out</a>
                  </div>
               </div>
            </div>
         </div>
         <div class="main-content">
            <!--start of prizes container-->
            <div class="prizes">
               <div class="header">
                  <?php
                  // Check if $logoData is set and not empty
                  if (isset($leaderboardImage) && !empty($leaderboardImage)) {
                     // If image data is available, echo the image tag with base64 encoded image data
                     echo '<img src="data:image/png;base64,' . base64_encode($leaderboardImage) . '" alt="Duke" class="duke-img" />';
                  } else {
                     // If no image data is available, display a placeholder image
                     echo '<img src="pictures/userLogo.png" alt="Duke" class="duke-img" />';
                  }
                  ?>
                  <!--<img src="pictures/gators.jpg" alt="Duke" class="duke-img">-->
                  <h1>Prizes</h1>
               </div>
               <div class="prizes-container">
                  <h2>Bi-weekly Prizes for Top 5 Individuals</h2>
                  <ul>
                     <li>The top 3 at the end of the season get tickets to the Duke vs UNC game.</li>
                     <li>Bi-weekly top 5 winners choose 1 item based prize or experiential based prize or any Duke gear under $300.</li>
                  </ul>
                  <div class="prizes-row">
                     <div class="prizes-column">
                        <h3>Experience Based Prizes:</h3>
                        <ul>
                           <li>Hurricanes tickets</li>
                           <li>Durham Bulls Tickets</li>
                           <li>Drive Shack</li>
                           <li>Frankies Fun Park</li>
                           <li>Golf at the Wa Duke</li>
                        </ul>
                     </div>
                     <div class="prizes-column">
                        <h3>Item Based Prizes:</h3>
                        <ul>
                           <li>Spikeball</li>
                           <li>Beats Studio Buds</li>
                           <li>Electric Skateboard</li>
                           <li>Massage Gun</li>
                        </ul>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>