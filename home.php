<?php
session_start(); // Start the session

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

require "Database.php";

// Create an instance of the Database class
$database = new DataBase();

// Connect to the database
$connection = $database->dbConnect();

global $school;
$school = $database->getSchool($connection, $username);
/*function getSchool($connection, $username)
{
    $query = "SELECT school FROM users WHERE username = '$username'";
    $result = mysqli_query($connection, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['school'];
    } else {
        // Handle error if query fails
        return false;
    }
}


function getSchoolInfo($connection) {
   global $school;
   $query = "SELECT * FROM " . $school;
   $result = mysqli_query($connection, $query);
   if ($result && mysqli_num_rows($result) > 0) {
       $row = mysqli_fetch_assoc($result);
       // Store values in variables
       $primaryColor = $row['primaryColor'];
       $secondaryColor = $row['secondaryColor'];
       $primaryModifiedColor = $row['primaryModifiedColor'];
       $topItem = $row['topItem'];
       $upcomingEvent1 = $row['upcomingEvent1'];
       $upcomingEvent2 = $row['upcomingEvent2'];
       $upcomingEvent3 = $row['upcomingEvent3'];
       $topTenTips = $row['topTenTips'];
       $upcomingEventsImage = $row['upcomingEventsImage'];
       // Return an array of values
       return array(
           'primaryColor' => $primaryColor,
           'secondaryColor' => $secondaryColor,
           'primaryModifiedColor' => $primaryModifiedColor,
           'topItem' => $topItem,
           'upcomingEvent1' => $upcomingEvent1,
           'upcomingEvent2' => $upcomingEvent2,
           'upcomingEvent3' => $upcomingEvent3,
           'topTenTips' => $topTenTips,
           'upcomingEventsImage' => $upcomingEventsImage
       );
   } else {
       // Handle error if query fails or no rows found
       return false;
   }
}*/
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





//recent points variables
$recentPoints = getRecentPointsForUser($connection, $username);
$_SESSION['recentPoints'] = $recentPoints;

function getRecentPointsForUser($connection, $username) {
      global $school;
      //$statement = "SELECT recentPoints FROM users WHERE username = '$username'";
      $statement = "SELECT recentPoints FROM " . $school . "Users WHERE username = '$username'";
      $result = mysqli_query($connection, $statement);
      if ($result) {
         $row = mysqli_fetch_assoc($result);
         $recentPoints2 = $row['recentPoints'];
      }
      
      return $recentPoints2;
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

// Execute SQL query to retrieve the rank of the logged-in user
$query = "SELECT fullname, COALESCE(totalPoints, 0) AS totalPoints FROM " . $school . "Users WHERE username = '$username'";
$result = mysqli_query($connection, $query);

// Check if the query was successful
if ($result) {
    $row = mysqli_fetch_assoc($result);

    // Get the rank of the user
    $rank = 1; // Default rank if the user is not found

    // If the user is found, fetch their rank based on totalPoints
    if ($row) {
        $userPoints = $row['totalPoints'];

        // Execute another query to get the rank
        $rankQuery = "SELECT COUNT(*) AS rank FROM " . $school . "Users WHERE COALESCE(totalPoints, 0) > $userPoints";
        $rankResult = mysqli_query($connection, $rankQuery);

        if ($rankResult && $rankRow = mysqli_fetch_assoc($rankResult)) {
            $rank = $rankRow['rank'] + 1; // Adding 1 to start the rank from 1
        }
    }

    if ($rank != 1){
      $difference = calculatePointsDifference($username);
    }

    // Close the database connection
    mysqli_close($connection);
} else {
    // Handle query error
    die("ERROR: Could not execute query. " . mysqli_error($connection));
}

// Function to add the appropriate suffix to the rank
function addSuffix($num) {
    if ($num % 100 >= 11 && $num % 100 <= 13) {
        $suffix = 'th';
    } else {
        switch ($num % 10) {
            case 1: $suffix = 'st'; break;
            case 2: $suffix = 'nd'; break;
            case 3: $suffix = 'rd'; break;
            default: $suffix = 'th'; break;
        }
    }
    return $num . $suffix;
}

// Function to generate the appropriate message based on the user's rank
function generateRankMessage($rank, $userPoints) {
   global $difference;
   global $topItem;
    if ($rank == 1) {
        // User is in the 1st position
        $message = "You're THE TOP $topItem, Welcome to the top";
    } elseif ($rank <= 10) {
        // User is in the top 10
        if ($difference == 1) {
         $points = "point";
        } else {
         $points = "points";
        }
        $message = "You're a TOP TEN $topItem and $difference $points behind " . addSuffix($rank - 1);
        
    } else {
        // User is outside the top 10
        if ($difference == 1) {
         $points = "point";
        } else {
         $points = "points";
        }
        $message = "You are $difference $points behind " . addSuffix($rank - 1) . ", fight for the top 10";
    }

    return $message;
}

// Function to calculate points difference
function calculatePointsDifference($username) {
   global $school;
   global $connection;
   // Query to get the rank and totalPoints of the logged-in user

   $query1 = "CREATE TEMPORARY TABLE tmpRanking
   SELECT u.id, u.totalPoints,
          (SELECT COUNT(*) + 1 
           FROM " . $school . "users u2 
           WHERE u2.totalPoints > u.totalPoints) AS newRank
      FROM " . $school . "Users u;";


   $query2 = "UPDATE " . $school . "Users u
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




      $sql = "SELECT userRank FROM " . $school . "Users WHERE username = '$username'";
      $result = mysqli_query($connection, $sql);
      
      // Check if the query was successful
      if ($result) {
          $row = mysqli_fetch_assoc($result);
          $loggedInUserRank = $row['userRank'];
      
          // Retrieve totalPoints for the user with the given userRank
          $sql1 = "SELECT totalPoints FROM " . $school . "Users WHERE userRank = $loggedInUserRank";
          $result1 = mysqli_query($connection, $sql1);
      
          // Check if the query was successful
          if ($result1) {
              $row1 = mysqli_fetch_assoc($result1);
              $loggedInUserTotalPoints = $row1['totalPoints'];
      
              // Calculate points difference for the user one rank above
              $userAboveRank = $loggedInUserRank - 1;

              $sql2 = "SELECT totalPoints FROM " . $school . "Users WHERE userRank = $userAboveRank";
              $result2 = mysqli_query($connection, $sql2);
              

              while (mysqli_num_rows($result2) === 0) {
               // Adjust userAboveRank for the next attempt
               $userAboveRank--;
           
               // Rerun the query with the updated userAboveRank
               $sql2 = "SELECT totalPoints FROM " . $school . "Users WHERE userRank = $userAboveRank";
               $result2 = mysqli_query($connection, $sql2);
           
               // Output the error message if the query fails
               if ($result2 === false) {
                   echo "Error: " . mysqli_error($connection) . "\n";
               }
           }
      
              // Check if the query was successful
              if ($result2) {
                  $row2 = mysqli_fetch_assoc($result2);
                  $userAboveTotalPoints = $row2['totalPoints'];
      
                  // Calculate points difference
                  $pointsDifference = $userAboveTotalPoints - $loggedInUserTotalPoints;
      
                  /*echo "Points Difference: $pointsDifference";*/
              } else {
                  echo "Error retrieving user above totalPoints: " . mysqli_error($connection);
              }
      
              // Free the result set
              mysqli_free_result($result2);
          } else {
              echo "Error retrieving user totalPoints: " . mysqli_error($connection);
          }
      
          // Free the result set
          mysqli_free_result($result1);
      } else {
          echo "Error retrieving userRank: " . mysqli_error($connection);
      }
       /*
       echo "Logged-in User Rank: $loggedInUserRank, Total Points: $loggedInUserTotalPoints<br>";
       echo "User Above Rank: $userAboveRank, Total Points: $userAboveTotalPoints<br>";
       echo "Difference in Points: $pointsDifference";*/
       
       return $pointsDifference;
   }
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Dashboard</title>
      <link rel="stylesheet" href="home-styles2.css">
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Asap+Condensed:wght@400;700&family=Barlow:wght@400;700&display=swap" rel="stylesheet">
      <style>
         .sidebar {
            background: linear-gradient(to bottom, <?php echo $primaryColor; ?>, <?php echo $secondaryColor; ?>);
         }
         .dashboard-item.top-devil {
            background: linear-gradient(to right, <?php echo $primaryColor; ?>, <?php echo $primaryModifiedColor; ?>);
         }
         .circle-badge {
            background: linear-gradient(to right, <?php echo $primaryColor; ?>, <?php echo $primaryModifiedColor; ?>);
         }
         .dashboard-item.dfl {
            background: linear-gradient(225deg, <?php echo $primaryColor; ?>, <?php echo $secondaryColor; ?>);
         }
         .ptw-content {
            color: <?php echo $secondaryColor; ?>;
         }
         .ptw-waves {
            position: absolute;
            /* Absolute positioning inside the .ptw-dashboard */
            bottom: -13vh;
            /* Aligns to the bottom */
            left: 0;
            width: 100%;
            /* Full width of the container */
            height: 100%;
            <?php
            // Check if $wavesData is set and not empty
            if (isset($waves) && !empty($waves)) {
               // If image data is available, echo the background image with base64 encoded image data
               echo "background: url('data:image/png;base64," . base64_encode($waves) . "') no-repeat bottom;";
            } else {
               // If no image data is available, provide a fallback background image path
               echo "background: url('pictures/UFWaves.png') no-repeat bottom;";
            }
            ?>
            background-size: cover;
            /* Ensures the image covers the div */
            z-index: 1;
            /* Lower z-index to be behind the text content */
         }
         .events-background h1 {
            color: <?php echo $secondaryColor; ?>;
            border-bottom: .3vh solid <?php echo $secondaryColor; ?>;
         }
         .events-right {
            color: <?php echo $secondaryColor; ?>;
         }
         .dashboard-item.new-upload {
            background-color: <?php echo $primaryColor; ?>;
         }
      </style>
   </head>
   <body>
      <div class="container">
         <!--Sidebar that's displayed on every page-->
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
                  <div class="nav-item"><a href="#">Dashboard</a></div>
                  <div class="nav-item"><a href="leaderboard.php">Leaderboard</a></div>
                  <div class="nav-item"><a href="prizes.php">Prizes</a></div>
                  <div class="nav-item"><a href="howToPlay.php">How to play</a></div>
                  <div class="nav-last">
                     <a href="login.php" class="sign-out-link">Sign Out</a>
                  </div>
               </div>
            </div>
         </div>
         <div class="main-content">
            <!--Header-->
            <div class="header">Hello, <?php echo isset($fname) ? $fname : ''; ?>. Welcome back!</div>
            <div class="header-2">Your Dashboard</div>
            <div class="dashboard">
               <a href="upload.php" class="new-upload-link">
                  <div class="dashboard-item new-upload">
                     <img src="pictures/camera.png" alt="Placeholder Image" class="background-image" />
                  </div>
               </a>
               <!--Top Devil Dashboard-->
               <div class="dashboard-item top-devil">
                  <div class="circle-badge">
                     <div class="circle-text">
                        <div class="circle-number"><?php echo addSuffix($rank); ?></div>
                        <div class="circle-label">TOP <?php echo $topItem; ?></div>
                     </div>
                  </div>
                  <div class="top-devil-textbox">
                     <div class="top-devil-info"><?php echo generateRankMessage($rank, $userPoints); ?></div>
                     <div class="top-devil-action"><?php echo $topTenTips; ?></div>
                  </div>
               </div>
               <!--Point of the week dashboard-->
               <div class="dashboard-item ptw-dashboard">
                  <div class="ptw-content">
                     <div class="ptw-title">Points Earned This Week</div>
                     <div class="ptw-points"><?php echo isset($_SESSION['recentPoints']) ? $_SESSION['recentPoints'] : '-1'; ?></div>
                  </div>
                  <div class="ptw-waves"></div>
               </div>
               <!--Upcoming Events dashboard-->
               <div class="dashboard-item upcoming-events" style = "background-color: #F3F4EF;">
                  <div class="events-background">
                     <div class="events-text">
                     <h1>Upcoming Events</h1>
                     </div>
                     <?php
                        // Check if $upcomingEventsImage is set and not empty
                        if (isset($upcomingEventsImage) && !empty($upcomingEventsImage)) {
                              // If image data is available, echo the image tag with base64 encoded image data
                              echo '<img src="data:image/png;base64,' . base64_encode($upcomingEventsImage) . '" alt="Upcoming Event Image" class="events-background-image"/>';
                        } else {
                              // If no image data is available, display a placeholder image
                              echo '<img src="pictures/info.png" alt="Event Image" class="events-background-image"/>';
                        }
                     ?>
                     <!--<img src="pictures/UFEvents.png" alt="Event Image" class="events-background-image"/>-->
                  </div>
                  <div class="events-right">
                     <ul class="events-list">
                        <li><?php echo $upcomingEvent1; ?></li>
                        <hr>
                        <li><?php echo $upcomingEvent2; ?></li>
                        <hr>
                        <li><?php echo $upcomingEvent3; ?></li>
                     </ul>
                  </div>
               </div>
               <!--DFL Logo Dashboard and display-->
               <div class="dashboard-item dfl">
                  <?php
                  // Check if $logoData is set and not empty
                  if (isset($logo) && !empty($logo)) {
                     // If image data is available, echo the image tag with base64 encoded image data
                     echo '<img src="data:image/png;base64,' . base64_encode($logo) . '" alt="DFL Image" />';
                  } else {
                     // If no image data is available, display a placeholder image
                     echo '<img src="pictures/UFLogo2.png" alt="DFL Image" />';
                  }
                  ?>
                  <!--<img src="pictures/UFLogo2.png" alt="DFL Image" />-->
               </div>
            </div>
         </div>
      </div>
      <!--JavaScript to dissplay current date and time, possible idea for future dashboard-->
      <script>
         document.addEventListener('DOMContentLoaded', function() {
             var now = new Date();
             var day = now.getDate();
             var monthNames = ["January", "February", "March", "April", "May", "June",
                 "July", "August", "September", "October", "November", "December"];
             var month = monthNames[now.getMonth()];
             document.getElementById('current-day').textContent = day;
             document.getElementById('current-month').textContent = month;
         });
      </script>
   </body>
</html>
