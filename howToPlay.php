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
      <title>How To Play</title>
      <link rel="stylesheet" href="howToPlay.css">
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
         .how-to-play-section h2 {
            color: <?php echo $primaryColor; ?>;
         }
         .step-number {
            color: <?php echo $secondaryColor; ?>;
         }
         .table-header.thead {
            color: <?php echo $secondaryColor; ?>;
         }
         th {
            background-color: <?php echo $primaryColor; ?>;
         }

         .background-image {
            background-image: url('pictures/prizesUFBackground.jpeg');
         }
      </style>
   </head>
   <body>
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
               <div class="nav-item"><a href="prizes.php">Prizes</a></div>
               <div class="nav-item"><a href="#">How to play</a></div>
               <div class="nav-last">
                  <a href="login.html" class="sign-out-link">Sign Out</a>
               </div>
            </div>
         </div>
      </div>
      <div class="main-content">
         <!--how to play instructions container-->
         <div class="how-to-play-container">
            <div class="points-hierarchy" id="pointsHierarchyArrow">
               <span>Points Hierarchy</span>
               <img src="pictures/arrow.png" alt="Arrow" id="pointsHierarchyArrow" />
            </div>
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
               <h1>How to Play</h1>
            </div>
            <div class="how-to-play-section" id="step1">
               <h2><span class="step-number">Step 1:</span> Capture the Moment</h2>
               <p>Take a photo of yourself at the game; make sure it shows your enthusiasm!</p>
            </div>
            <div class="how-to-play-section" id="step2">
               <h2><span class="step-number">Step 2:</span> New Upload</h2>
               <p>Click the New Upload icon in the Dashboard and fill out the form with the details of the event.</p>
            </div>
            <div class="how-to-play-section" id="step3">
               <h2><span class="step-number">Step 3:</span> Confirm & Win</h2>
               <p>Wait for points confirmation and start earning prizes!</p>
            </div>
         </div>
         <!--Start of Points Hieracrhy-->
         <div class="points-table-container" style="display: none;">
            <div class="back-arrow" id="backToHowToPlayArrow">
               <img src="pictures/back-arrow.png" alt="Arrow"/>
               <span>How to Play</span>
            </div>
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
               <h1>Points Hierarchy</h1>
            </div>
            <!--Table containing the Points Hierarchy-->
            <div class="table-container">
               <table>
                  <thead class="table-header">
                     <th>Sport</th>
                     <th>Points</th>
                  </thead>
                  <tr>
                     <td>Basketball</td>
                     <td>1</td>
                  </tr>
                  <tr>
                     <td>Football</td>
                     <td>1</td>
                  </tr>
                  <tr>
                     <td>Baseball/Softball</td>
                     <td>1</td>
                  </tr>
                  <tr>
                     <td>Soccer</td>
                     <td>2</td>
                  </tr>
                  <tr>
                     <td>Lacrosse</td>
                     <td>2</td>
                  </tr>
                  <tr>
                     <td>Volleyball</td>
                     <td>4</td>
                  </tr>
                  <tr>
                     <td>Tennis</td>
                     <td>4</td>
                  </tr>
                  <tr>
                     <td>Wrestling</td>
                     <td>6</td>
                  </tr>
                  <tr>
                     <td>Fencing</td>
                     <td>10</td>
                  </tr>
                  <tr>
                     <td>Track and Field</td>
                     <td>10</td>
                  </tr>
                  <tr>
                     <td>Cross Country</td>
                     <td>10</td>
                  </tr>
                  <tr>
                     <td>Swimming and Diving</td>
                     <td>12</td>
                  </tr>
                  <tr>
                     <td>Golf</td>
                     <td>12</td>
                  </tr>
                  <tr>
                     <td>Field Hockey</td>
                     <td>14</td>
                  </tr>
                  <tr>
                     <td>Women's Rowing</td>
                     <td>14</td>
                  </tr>
               </table>
            </div>
         </div>
      </div>
      <!--These scripts sense when the arrow to points hierarchy or back arrow is clicked that it will change the table-->
      <script>
         document.getElementById("pointsHierarchyArrow").addEventListener("click", function() {
                   var howToPlay = document.querySelector(".how-to-play-container");
                   var pointsTableContainer = document.querySelector(".points-table-container");
                   howToPlay.style.display = "none";
                   pointsTableContainer.style.display = "block";
               });
         
               document.getElementById("backToHowToPlayArrow").addEventListener("click", function() {
                   var howToPlay = document.querySelector(".how-to-play-container");
                   var pointsTableContainer = document.querySelector(".points-table-container");
                   pointsTableContainer.style.display = "none";
                   howToPlay.style.display = "block";
               });
           
      </script>
   </body>
</html>