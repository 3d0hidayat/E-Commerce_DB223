<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {
    $uname = $conn->real_escape_string($_POST['username']);
    $password = $conn->real_escape_string($_POST['password']);

    $result = $conn->query("SELECT * FROM users WHERE username='$uname' AND password='$password'");

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user;

        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login - FigureKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    body { font-family: 'Poppins', sans-serif; }
  </style>
</head>
<body class="bg-black flex items-center justify-center min-h-screen">
  <div class="bg-gray-900 p-8 rounded-lg shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-semibold mb-6 text-purple-400 text-center">Login Pengguna</h2>
    <?php if (isset($error)): ?>
      <p class="text-red-500 mb-4"><?= $error ?></p>
    <?php endif; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Username" class="w-full p-2 border border-gray-700 bg-black text-white rounded mb-4" required>
      <input type="password" name="password" placeholder="Password" class="w-full p-2 border border-gray-700 bg-black text-white rounded mb-4" required>
      <button type="submit" name="login" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700">Login</button>
      <p class="mt-4 text-sm text-center text-gray-300">Belum punya akun? <a href="register.php" class="text-purple-400 hover:underline">Daftar</a></p>
    </form>
  </div>
</body>
</html>
