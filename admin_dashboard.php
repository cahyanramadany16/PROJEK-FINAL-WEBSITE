<?php

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_projek";
session_start();

// Timeout in seconds
$timeout_duration = 60;

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Check for session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header('Location: login.php?message=session_expired');
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity time

// Proses logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: logout.php');
    exit;
}

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user profile information
$username = $_SESSION['username'];
$query = "SELECT name, phone, address FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userProfile = $result->fetch_assoc();
} else {
    echo "Profile not found.";
}

// Fetch room data
$roomQuery = "SELECT * FROM room_types";
$roomResult = $conn->query($roomQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Four Points by Sheraton Makassar</title>
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
     /* Footer Styles */
     footer {
            text-align: center;
            padding: 10px 0;
            background-color: #29668f;
            color: white;
            margin-top: 20px;
            
        }
  </style>
</head>
<body>

<header>
  <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
    <h1>Welcome To Four Points by Sheraton Makassar</h1>
    <div class="user-greeting" style="font-size: 1.5em; margin-right: 15%; color: white; padding-top: 50px;">
      Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
    </div>
  </div>
  <nav>
    <ul>
      <li><a href="admin_dashboard.php">Dashboard Admin</a></li>
      <li><a href="manage_admin.php">Manage Room</a></li>
      <li><a href="logout.php">Logout</a></li> <!-- Menunjukkan path absolut -->

    </ul>
  </nav>
</header>

<div id="Home" class="hero" style="background-image: url(resource/hotel.jpg);">
  <div class="hero-content">
    <h1>Room Management at Four Points by Sheraton Makassar</h1>
    <p>Experience the best of Makassar at our hotel</p>
  </div>
</div>

<footer>
    <p>Four Points by Sheraton Makassar &copy; 2024</p>
</footer>

</body>
</html>
