<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_projek";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

// Mengecek apakah ada cookie "remember_me"
if (isset($_COOKIE['remember_me'])) {
    list($cookie_username, $cookie_password_hash) = explode(':', $_COOKIE['remember_me']);
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $cookie_username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && password_verify($cookie_password_hash, $user['password'])) {
        // Set session variables
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($user['role'] === 'user') {
            header("Location: user_dashboard.php");
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $remember = isset($_POST['remember']) ? $_POST['remember'] : '';

    if (empty($username) || empty($password) || empty($role)) {
        $error = "All fields are required.";
    } else {
        // Prepare the SQL query to fetch user data
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Check if user exists and password matches
        if ($user) {
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                // Set session variables
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Set cookie for 24 hours if "Remember Me" is checked
                if ($remember) {
                    $cookie_password_hash = password_hash($user['password'], PASSWORD_DEFAULT);
                    setcookie('remember_me', $username . ':' . $cookie_password_hash, time() + 86400, "/");
                }

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($user['role'] === 'user') {
                    header("Location: user_dashboard.php");
                }
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username, password, or role.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/styleLogin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Reset Default Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box; /* Mengatur agar padding dan border disertakan dalam ukuran elemen */
    font-family: Arial, sans-serif; /* Menggunakan font Arial */
}

/* Body styling */
body {
    display: flex;
    flex-direction: column;
    align-items: center; /* Menyelaraskan konten di tengah secara horizontal */
    min-height: 100vh; /* Tinggi minimum layar penuh */
    background: url(resource/hotel.jpg) no-repeat center center fixed;
    background-size: cover; /* Gradasi warna latar belakang */
    color: #fff; /* Warna teks default */
    backdrop-filter: blur(2px);
    
}


/* Header styling */
header {
    width: 100%;
    text-align: center; /* Menyelaraskan teks ke tengah */
    background-color: #29668f; /* Warna latar belakang header */
    padding: 10px;
    position: fixed; /* Menempatkan header tetap di bagian atas */
    top: 0;
    left: 0;
    z-index: 1; /* Menempatkan header di atas konten lainnya */
}

header h1 {
    font-size: 2em; /* Ukuran font judul */
    margin-bottom: 10px;
}

/* Navigasi */
nav ul {
    list-style: none; /* Menghapus bullet pada daftar */
    display: flex;
    justify-content: center; /* Menyelaraskan item ke tengah */
    padding: 0;
}

nav li {
    margin: 0 15px; /* Memberi jarak antar item menu */
}

nav a {
    text-decoration: none; /* Menghapus garis bawah pada link */
    color: #f7e0e0; /* Warna teks link */
    font-weight: bold; /* Membuat teks tebal */
    transition: color 0.3s ease; /* Transisi warna saat di-hover */
}

nav a:hover {
    color: #ff6347; /* Warna link saat di-hover */
}

/* Pengaturan utama untuk menempatkan login box di tengah */
main {
    display: flex;
    justify-content: center;
    align-items: center; /* Menyelaraskan konten di tengah secara vertikal */
    flex-grow: 1; /* Membuat konten utama mengisi sisa ruang */
    padding-top: 80px; /* Mengimbangi tinggi header tetap */
    width: 100%;
}

/* Pengaturan container login */
.login-container {
    background: whitesmoke; /* Warna latar belakang putih untuk kontras */
    width: 100%;
    max-width: 400px; /* Lebar maksimum login box */
    padding: 2em;
    border-radius: 8px; /* Membuat sudut membulat */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); /* Efek bayangan */
    text-align: center;
    color: #333; /* Warna teks */
}

h2 {
    margin-bottom: 0.5em; /* Jarak bawah judul */
    color: #333; /* Warna teks */
}

p {
    color: #666; /* Warna teks abu-abu */
    margin-bottom: 1em; /* Jarak bawah paragraf */
}

/* Pengaturan input group */
.input-group {
    margin-bottom: 1.5em; /* Jarak bawah antara input */
    text-align: left;
}

.input-group label {
    display: block;
    color: #333; /* Warna teks label */
    font-weight: bold; /* Membuat teks label tebal */
    margin-bottom: 0.3em; /* Jarak antara label dan input */
}

.input-group input {
    width: 100%; /* Lebar penuh untuk input */
    padding: 0.8em;
    border: 1px solid #ddd; /* Border abu-abu terang */
    border-radius: 4px; /* Membuat sudut input membulat */
    outline: none; /* Menghapus outline default */
    transition: border 0.3s ease; /* Transisi saat border berubah */
}

.input-group input:focus {
    border-color: #29668fe8; /* Warna border saat input difokuskan */
}

/* Tombol Login */
.login-button {
    width: 100%; /* Lebar penuh tombol */
    padding: 0.8em;
    background: linear-gradient(135deg, #6e8efb, #29668fe8); /* Gradasi warna tombol */
    color: white;
    border: none; /* Menghapus border default */
    border-radius: 4px; /* Membuat sudut tombol membulat */
    font-size: 1em;
    font-weight: bold; /* Membuat teks tebal */
    cursor: pointer; /* Menampilkan cursor pointer */
    transition: background 0.3s ease; /* Transisi warna saat tombol di-hover */
}

.login-button:hover {
    background: linear-gradient(135deg, #5b73db, #29668fe8); /* Warna gradasi saat tombol di-hover */
}

/* Link untuk sign up */
.signup-link {
    margin-top: 1em; /* Jarak atas untuk teks sign up */
    color: #666; /* Warna teks abu-abu */
}

.signup-link a {
    color: #6e8efb; /* Warna teks link sign up */
    text-decoration: none; /* Menghapus garis bawah */
}

.signup-link a:hover {
    text-decoration: underline; /* Menambahkan garis bawah saat di-hover */
}

/* Dropdown Styling */
.input-group select {
    width: 100%; /* Lebar penuh untuk dropdown */
    padding: 0.8em;
    border: 1px solid #ddd; /* Border abu-abu terang */
    border-radius: 4px; /* Membuat sudut dropdown membulat */
    outline: none; /* Menghapus outline default */
    font-size: 1em;
    background-color: #fff; /* Warna latar dropdown */
    color: #333; /* Warna teks */
    cursor: pointer; /* Menampilkan cursor pointer */
    transition: border 0.3s ease;
}

.input-group select:focus {
    border-color: #29668fe8; /* Warna border saat dropdown difokuskan */
}

.input-group label {
    margin-bottom: 0.5em; /* Memberi jarak antara label dan dropdown */
    display: block;
    font-weight: bold;
}

footer {
  color: #fff;
  padding: 10px 20px;
  text-align: center;
  font-size: 20px;

}

    </style>
</head>
<body>

    <header>
        <h1>Welcome To Four Points by Sheraton Makassar</h1>
    </header>

    <main>
        <div class="login-container">
            <h2>Welcome Back</h2>
            <p>Please login to continue</p>
            <?php if (!empty($error)): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo isset($_COOKIE['remember_me']) ? $cookie_username : ''; ?>" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="input-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div style="display: flex; align-items: center ; margin-bottom: 9px;">
                <label for="remember_me">Remember Me</label>
                <input type="checkbox" id="remember_me" name="remember_me" style="margin-right: 10px; margin-left:7px;">
            </div>
                <button type="submit" class="login-button">Login</button>
                <p class="signup-link">Don’t have an account? <a href="signup.php">Sign up</a></p>
            </form> 
        </div>

    </main>
    <footer>
        <p>Four Points by Sheraton Makassar &copy; 2024</p>
    </footer>
</body>
</html>
