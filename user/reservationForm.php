<?php
session_start();

// Redirect ke halaman login jika belum login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Tangani username dengan aman
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';

// Proses logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: logout.php');
    exit;
}

$servername = "localhost";
$username_db = "root";
$password = "";
$dbname = "db_projek";

$conn = new mysqli($servername, $username_db, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status === 'success') {
        echo "<script>alert('Reservasi berhasil!');</script>";
    } elseif ($status === 'upload_failed') {
        echo "<script>alert('Gagal mengunggah bukti pembayaran.');</script>";
    } elseif ($status === 'error') {
        echo "<script>alert('Terjadi kesalahan. Coba lagi.');</script>";
    }
}

// Fetch Room Data
$result = $conn->query("SELECT * FROM room_types");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
    <link rel="stylesheet" href="../css/styleForm.css">
</head>

<body>

<header>
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <h1>Four Points by Sheraton Makassar</h1>
        <div class="user-greeting" style="font-size: 1.5em; color: white; margin-right: 15%;">Hi, <?= $username ?></div>
    </div>
    <nav>
        <ul>
            <li><a href="user_dashboard.php">Home</a></li>
            <li><a href="user_dashboard.php#About">About</a></li>
            <li><a href="user_dashboard.php#Contact">Contact</a></li>
            <li><a href="tabelRoom.php">Room Types</a></li>
            <li><a href="reservationForm.php">Reservation</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </nav>
</header>
    <main class = "container">
        <section id="Reservation">
            <h2>Make a Reservation</h2>
            <form action="submitReservation.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="roomType">Room Type:</label>
                    <select id="roomType" name="roomType" required>
                        <option value="">Select a room type</option>
                        <?php
                        // Fetch room types
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row['room_type']) . '">' . htmlspecialchars($row['room_type']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="checkIn">Check-In Date:</label>
                    <input type="date" id="checkIn" name="checkIn" required>
                </div>
                <div class="form-group">
                    <label for="checkOut">Check-Out Date:</label>
                    <input type="date" id="checkOut" name="checkOut" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact Info:</label>
                    <input type="text" id="contact" name="contact" placeholder="Enter your contact info" required>
                </div>
                <div class="form-group">
                    <label for="paymentMethod">Payment Method:</label>
                    <select id="paymentMethod" name="paymentMethod" required onchange="showPaymentDetails(this.value)">
                        <option value="">Select payment method</option>
                        <option value="Transfer Bank">Transfer Bank</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>
                <div id="bankDetails" style="display: none; margin-top: 10px;">
                    <p><strong>Bank Account Details:</strong></p>
                    <p>Bank: Bank Central Asia (BCA)</p>
                    <p>Account Number: 1234567890</p>
                    <p>Account Name: Four Points by Sheraton Makassar</p>
                    <div class="form-group">
                        <label for="paymentProof">Upload Payment Proof:</label>
                        <input type="file" id="paymentProof" name="paymentProof" accept="image/*">
                    </div>
                </div>
                <div id="creditCardDetails" style="display: none; margin-top: 10px;">
                    <div class="form-group">
                        <label for="creditCardNumber">Credit Card Number:</label>
                        <input type="text" id="creditCardNumber" name="creditCardNumber" placeholder="Enter your credit card number" maxlength="16" pattern="\d{16}">
                    </div>
                </div>
                <button style="margin-bottom: 10px;" type="submit" class="reserve-button">Reserve Now</button>
            </form>
        </section>
    </main>
    <div class="footer ;">
        <p>&copy; 2023 Four Points by Sheraton Makassar</p>
    </div>
    <script>
        function showPaymentDetails(method) {
            const bankDetails = document.getElementById('bankDetails');
            const creditCardDetails = document.getElementById('creditCardDetails');

            if (method === 'Transfer Bank') {
                bankDetails.style.display = 'block';
                creditCardDetails.style.display = 'none';
            } else if (method === 'Credit Card') {
                creditCardDetails.style.display = 'block';
                bankDetails.style.display = 'none';
            } else {
                bankDetails.style.display = 'none';
                creditCardDetails.style.display = 'none';
            }
        }
    </script>
</body>
</html>
