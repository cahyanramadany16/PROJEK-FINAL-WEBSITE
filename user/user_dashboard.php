<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_projek";
session_start();

// Redirect ke halaman login jika belum login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Proses logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location:../logout.php');
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil informasi profil user berdasarkan username
$username = $_SESSION['username'];
$query = "SELECT name, phone, address FROM users WHERE username = '$username'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $userProfile = $result->fetch_assoc();
} else {
    echo "Profile not found.";
}


// Fetch Room Data
$result = $conn->query("SELECT * FROM room_types");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Four Points by Sheraton Makassar - Room Types</title>
  <link rel="stylesheet" href="../css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 18px;
        text-align: left;
    }
    th, td {
        padding: 12px;
        border: 1px solid #ddd;
    }
    th {
        background-color: #f4f4f4;
    }
    .room-image {
        width: 150px;
        height: auto;
    }
  </style>
</head>
<body>
  <header>
  <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
  <h1>Welcome To Four Points by Sheraton Makassar</h1>
  <div class="user-greeting" style="font-size: 1.5em; margin-right: 15%; color: white; padding-top: 50px;">
    Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
    <a href="profil.php" class="profile-icon" style="text-decoration: none; color: white; cursor: pointer;">👤</a>
</div>

  </div>
    <nav>
      <ul>
         <!-- Profil Icon -->
  
        <li><a href="#Home">Home</a></li>
        <li><a href="#About">About</a></li>
        <li><a href="#Contact">Contact</a></li>
        <li><a href="tabelRoom.php">Room Types</a></li>
        <li><a href="reservationForm.php" target="_blank">Reservation</a></li>
        <li><a href="../logout.php">Logout</a></li>

      </ul>
    </nav>
  </header>
  <div id="Home" class="hero" style="background-image: url(../resource/hotel.jpg) ;">
    <div class="hero-content">
      <h1>Four Points by Sheraton Makassar</h1>
      <p>Experience the best of Makassar at our hotel</p>
    </div>
  </div>

  <table>
    <tr>
      <th>
        <div class="content">
          <div id="About" class="section">
            <h2>Welcome to Four Points by Sheraton Makassar</h2>
           <p>Temukan kenyamanan dan pengalaman menginap tak terlupakan di Four Points by Sheraton Makassar, berlokasi strategis dengan akses mudah ke destinasi wisata seperti Benteng Rotterdam dan Masjid 99 Kubah di Pantai Losari. Selama menginap, nikmati fasilitas unggulan seperti kolam renang di lantai 9 dengan pemandangan kota, pusat kebugaran 24 jam, restoran dengan berbagai cita rasa, serta kamar luas dengan desain modern, dilengkapi Four Comfort Beds dan akses internet gratis. Untuk acara, tersedia ruang serbaguna terbesar di Indonesia Timur seluas 43.518 meter persegi, dengan tim profesional siap membantu mewujudkan acara impian Anda. Kami menantikan kedatangan Anda di Four Points by Sheraton Makassar.</p> 
          </div>
        </div>
      </th>
      <th>
        <img src="../resource/hotel.jpg" alt="Hotel Image" class="section-image" width="300" height="300">
      </th>
    </tr>
  </table>
  
  <table>
    <tr>
      <th>
        <img src="../resource/room.jpg" alt="Room Image" class="section-image">
      </th>
      <th>
        <div class="section">
          <div id="About" class="section">
            <h2>OUR ROOM</h2>
            <p>Kamar kami dirancang untuk memberikan kenyamanan dan relaksasi terbaik. Setiap kamar dilengkapi dengan fasilitas modern, termasuk TV layar datar, Wi-Fi gratis, dan tempat tidur yang nyaman.</p>
            
          </div>
        </div>
      </th>
    </tr>
  </table>
  <table>
    <tr>
      <th>
        <div class="section">
          <h2>DINING</h2>
          <p>Hotel kami memiliki restoran yang menyajikan berbagai masakan internasional dan lokal. Kami juga menawarkan layanan kamar untuk kenyamanan Anda.</p>
            
          </div>
        </div>
      </th>
      <th>
        <img src="../resource/dining.jpg" alt="Dining Image" class="section-image">
      </th>
    </tr>
  </table>
  
  <div id="Contact" class="section">
    <h2>Contact</h2>
    <p>
        Four Points by Sheraton Makassar<br>
        Jalan Andi Djemma No. 130 Makassar, South Sulawesi 90222<br>
        Phone: +62 411 8099999<br>
        Email: reservation.makassar@fourpoints.com
    </p>

    <!-- Google Maps iframe -->
    <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.7311544084367!2d119.41914527467783!3d-5.168048753556045!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbefd3f234c5f4d%3A0x5c00edc80b91c82!2sFour%20Points%20by%20Sheraton%20Makassar!5e0!3m2!1sen!2sid!4v1698068485449!5m2!1sen!2sid" 
        width="1000" 
        height="450" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy" 
        referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>
  <footer style="background-color: #29668fe8;   color: #fff;   padding: 10px 0;   text-align: center;   font-size: 20px;">
    <p>&copy; 2023 Four Points by Sheraton Makassar</p>
  </footer>
</body>
</html>
<?php $conn->close(); ?>
