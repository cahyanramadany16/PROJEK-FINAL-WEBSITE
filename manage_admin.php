<?php

session_start();
// Timeout in seconds
$timeout_duration = 3600;
// Redirect ke halaman login jika belum login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: manage_admin.php");
    exit;
}

// Tangani username dengan aman
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest';

// Proses logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: logout.php');
}

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_projek";

$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add Room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $room_type = $_POST['room_type'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $benefits = $_POST['benefits'];

    // Process image upload
    if ($_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
        $image_path = ''; // If no image is uploaded, set an empty string
    } elseif ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $image_path = "uploads/" . basename($image['name']);
        if (!move_uploaded_file($image['tmp_name'], $image_path)) {
            die("Error uploading file to destination.");
        }
    } else {
        die("Error uploading file: " . $_FILES['image']['error']);
    }

    // Insert query
    $stmt = $conn->prepare("INSERT INTO room_types (room_type, category, price, benefits, image_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $room_type, $category, $price, $benefits, $image_path);

    if ($stmt->execute()) {
        $message = "Room berhasil ditambahkan";
    } else {
        $message = "Terjadi kesalahan saat menambahkan room";
    }

    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message));
    exit;
}

// Handle Edit Room
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_room'])) {
    $id = $_POST['id'];
    $room_type = $_POST['room_type'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $benefits = $_POST['benefits'];
    $existing_image_path = $_POST['existing_image_path'];

    // Process image upload
    if ($_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
        $image_path = $existing_image_path;
    } elseif ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = $_FILES['image'];
        $image_path = "uploads/" . basename($image['name']);
        if (!move_uploaded_file($image['tmp_name'], $image_path)) {
            die("Error uploading file to destination.");
        }
    } else {
        die("Error uploading file: " . $_FILES['image']['error']);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE room_types SET room_type = ?, category = ?, price = ?, benefits = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param("ssdssi", $room_type, $category, $price, $benefits, $image_path, $id);

    if ($stmt->execute()) {
        $message = "Room berhasil di edit";
    } else {
        $message = "Terjadi kesalahan saat mengedit room";
    }

    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message));
    exit;
}

// Handle Delete Room
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete query
    $stmt = $conn->prepare("DELETE FROM room_types WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        $message = "Room berhasil dihapus";
    } else {
        $message = "Terjadi kesalahan saat menghapus room";
    }

    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($message));
    exit;
}

// Fetch Room Data
$result = $conn->query("SELECT * FROM room_types");
if (!$result) {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Room - Four Points by Sheraton Makassar</title>
    <style>
        /* Untuk perangkat dengan lebar maksimal 768px (tablet) */
    @media (max-width: 768px) {
        body {
            font-size: 14px;
            padding: 15px;
        }
    }

    /* Untuk perangkat dengan lebar maksimal 480px (ponsel) */
    @media (max-width: 480px) {
        body {
            font-size: 12px;
            padding: 10px;
        }
    }
    body {
    font-family: 'Poppins', sans-serif; /* Menggunakan font Poppins */
    margin: 0;
    padding: 0;
    background-color: #f4f4f4; /* Warna latar belakang */
    }

    /* Gaya Header */
    header {
    display: flex;
    flex-direction: column; /* Mengatur nav berada di bawah logo/judul */
    align-items: center; /* Menyelaraskan item ke tengah secara horizontal */
    background-color: #29668f; /* Warna latar belakang header */
    padding: 10px 20px;
    position: fixed; /* Membuat header tetap di posisi atas */
    top: 0; /* Menempatkan header di bagian paling atas halaman */
    width: 100%; /* Membuat header meluas ke seluruh lebar layar */
    z-index: 1000; /* Memastikan header berada di atas konten lain */
    }

    /* Menambahkan padding ke atas body untuk menghindari konten tertutup oleh header */
    body {
    padding-top: 100px; /* Sesuaikan dengan tinggi header */
    }

    /* Gaya Konten Header */
    .header-content {
    display: flex; /* Menggunakan flexbox untuk logo dan judul */
    align-items: center; /* Menyelaraskan logo dan judul secara vertikal */
    }

    /* Gaya Logo dan Judul */
    header h1 {
    margin: 0; /* Menghapus margin default */
    padding: 5px; /* Menambahkan padding untuk memberi jarak */
    font-size: 1.5em; /* Menyesuaikan ukuran font */
    color: #f8f5f5; /* Warna teks */
    margin-left: 10px; /* Memberi jarak antara logo dan judul */
    }
    
        nav {
        display: flex;
        justify-content: center; /* Menyelaraskan item ke tengah */
        align-items: center;

        padding: 0 20px;
        }

        nav ul {
        list-style: none; /* Menghilangkan gaya list */
        display: flex;
        margin: 0;
        padding: 0;
        }

        nav li {
        margin-left: 20px; /* Memberi jarak antar item menu */
        }

        nav a {
        text-decoration: none; /* Menghilangkan garis bawah pada link */
        color: #f7e0e0; /* Warna teks link */
        font-weight: bold; /* Membuat teks lebih tebal */
        transition: color 0.3s ease; /* Menambahkan transisi saat hover */
        }

        nav a:hover {
        color: #ff6347; /* Warna teks saat di-hover */
        }
        /* Main Container */
        main {
            margin-top: 85px;
            display: flex; /* Mengatur Flexbox */
            gap: 20px; /* Memberikan jarak antara kontainer */
            padding: 20px;
            justify-content: center; /* Memusatkan konten */
        }

        /* Form Container */
        #form-container {
            flex: 1; /* Membuat form mengambil ruang */
            max-width: 400px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        #form-container h3 {
            text-align: center;
            color: #29668f;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input, textarea, button, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            background-color: #4aa4e0;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #29668f;
        }

        /* Table Container */
        #table-container {
            flex: 2; /* Membuat tabel mengambil ruang */
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow-x: auto;
        }

        #table-container h2 {
            text-align: center;
            color: #29668f;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
            color: #333;
        }

        .room-image {
            width: 100px;
            height: auto;
        }

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
<?php
// Tampilkan alert jika ada pesan di URL
if (isset($_GET['message'])) {
    $alertMessage = htmlspecialchars($_GET['message']);
    echo "<script>alert('$alertMessage');</script>";
}
?>

<header>
  <div style="display: flex; justify-content: space-between; width: 100%; align-items: center; padding: 10px 20px;">
    <!-- Title aligned to the left -->
    <h1 style="flex-grow: 1; margin: 0;">Manage Room Four Points by Sheraton Makassar</h1>

    <!-- "Hi, Admin" aligned to the right -->
    <div class="user-greeting" style="font-size: 1.5em; margin-right: 15%; color: white; padding-top: 50px;">
      Hi, <?php echo htmlspecialchars($_SESSION['username']); ?>
    </div>
  </div>
  
  <!-- Navbar centered -->
  <nav>
    <ul>
      <li><a href="admin_dashboard.php">Dashboard Admin</a></li>
      <li><a href="manage_admin.php">Manage Room</a></li>
      <li><a href="reservationInfo.php">Reservation Info</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>
</header>


<main>
    <!-- Form Section -->
    <div id="form-container">
        <h3 id="formTitle">Add Room</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="id" name="id">
            <label for="room_type">Room Type:</label>
            <input type="text" id="room_type" name="room_type" required>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <label for="benefits">Benefits:</label>
            <textarea id="benefits" name="benefits" required></textarea>

            <label for="image">Room Image:</label>
            <input type="file" id="image" name="image" accept="image/*">
            <input type="hidden" id="existing_image_path" name="existing_image_path">

            <button type="submit" id="formButton" name="add_room">Save</button>
        </form>
    </div>

    <!-- Table Section -->
    <div id="table-container">
        <h2>Room Types Table</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room Type</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Benefits</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['room_type'] ?></td>
                        <td><?= $row['category'] ?></td>
                        <td>Rp. <?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['benefits'] ?></td>
                        <td><img src="<?= $row['image_path'] ?>" alt="Room Image" class="room-image"></td>
                        <td>
                            <button onclick="editRoom(<?= htmlspecialchars(json_encode($row)) ?>)">Edit</button>
                            <button onclick="deleteRoom(<?= $row['id'] ?>)" style="background-color:red;">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
function editRoom(room) {
    document.getElementById('formTitle').textContent = "Edit Room";
    document.getElementById('id').value = room.id;
    document.getElementById('room_type').value = room.room_type;
    document.getElementById('category').value = room.category;
    document.getElementById('price').value = room.price;
    document.getElementById('benefits').value = room.benefits;
    document.getElementById('existing_image_path').value = room.image_path;
    document.getElementById('formButton').name = "edit_room";
}

function deleteRoom(id) {
    if (confirm('Are you sure you want to delete this room?')) {
        window.location.href = "?delete_id=" + id;
    }
}
</script>
</body>
</html>