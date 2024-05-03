<?php

session_start(); // Start the session

if (isset($_SESSION['username'])) {
    $fullname = $_SESSION['fullname'];
    $username = $_SESSION['username'];
    $fname = $_SESSION['fname'];
} else {
    echo "User not logged in.";
    // Redirect to login page if not logged in
    header("Location: login.html");
    exit();
}
require "Database.php";

// Create an instance of the Database class
$database = new DataBase();

// Connect to the database
$connection = $database->dbConnect();

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


// Check if the connection was successful
if (!$connection) {
    die("ERROR: Could not connect to the database. " . mysqli_connect_error());
}
$query1 = "CREATE TEMPORARY TABLE tmpRanking
   SELECT u.id, u.totalPoints,
          (SELECT COUNT(*) + 1 
           FROM " . $school . "users u2 
           WHERE u2.totalPoints > u.totalPoints) AS newRank
   FROM " . $school . "users u;";


   $query2 = "UPDATE " . $school . "users u
   JOIN tmpRanking t ON u.id = t.id
   SET u.userRank = t.newRank;";

   $query3 = "DROP TEMPORARY TABLE IF EXISTS tmpRanking;";

   if (!mysqli_query($connection, $query1)) {
       die("Query failed: " . mysqli_error($connection));
   }
   if (!mysqli_query($connection, $query2)) {
      die("Query failed: " . mysqli_error($connection));
  }
  if (!mysqli_query($connection, $query3)) {
   die("Query failed: " . mysqli_error($connection));
   }


// Execute SQL query to retrieve the top 20 users ordered by total points
$query = "SELECT * FROM " . $school . "users ORDER BY totalPoints DESC LIMIT 20";
$result = mysqli_query($connection, $query);

// HTML content for the table
$tableContent = "";

// Counter for rank
$rank = 1;

// Loop through the results and append each user's information to the HTML content
while ($row = mysqli_fetch_assoc($result)) {
    $userRank = $row['userRank'];
    $leaderName = $row['fullname'];
    if (strlen($leaderName) > 15) {
      // Split the fullname into an array of words
      $nameParts = explode(' ', $leaderName);
      // Take the first part of the name
      $firstName = $nameParts[0];
      // Take the first letter of the last name
      $lastNameInitial = substr($nameParts[count($nameParts) - 1], 0, 1);
      // Concatenate the first name and the first initial of the last name
      $shortenedName = $firstName . " " . $lastNameInitial . ".";
      // Use $shortenedName as needed
      $leaderName = $shortenedName;
   } 
    $tableContent .= "<tr>";
    $tableContent .= "<td>" . $userRank . "</td>";
    $tableContent .= "<td>" . $leaderName . "</td>";
    
    // Replace null values with 0
    $totalPoints = ($row['totalPoints'] === null) ? 0 : $row['totalPoints'];
    
    $tableContent .= "<td>" . $totalPoints . "</td>";
    $tableContent .= "</tr>";
    $rank++; // Increment rank
}

// Close the database connection
mysqli_close($connection);
?>



<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Leaderboard</title>
      <link rel="stylesheet" href="leaderboard.css">
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
         th {
            background-color: <?php echo $primaryColor; ?>;
         }
      </style>
   </head>
   <body>
      <div class ="container">
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
                     
                     <!--<div class="user-name">Sean Forbes</div>-->
                     <div class="user-role">Student</div>
                  </div>
               </div>
               <div class="nav">
                  <div class="nav-item"><a href="home.php">Dashboard</a></div>
                  <div class="nav-item"><a href="#">Leaderboard</a></div>
                  <div class="nav-item"><a href="prizes.php">Prizes</a></div>
                  <div class="nav-item"><a href="howToPlay.php">How to play</a></div>
                  <div class="nav-last">
                     <a href="login.php" class="sign-out-link">Sign Out</a>
                  </div>
               </div>
            </div>
         </div>
         <div class="main-content">
            <!--Start of Leaderboard-->
            <div class="leaderboard">
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
                  <h1>Leaderboard</h1>
               </div>
               <div class="table-container">
                  <table>
                     <!--table info-->
                     <thead class="table-header">
                        <th>Rank</th>
                        <th>Name</th>
                        <th>Points</th>
                     </thead>
                     <!-- Dynamic table content from PHP -->
                     <?php echo $tableContent; ?>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </body>
</html>
