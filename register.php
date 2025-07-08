<?php
include 'db.php';

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $uname = $_POST['username'];
    $password = $_POST['password'];

    $conn->query("INSERT INTO users (name, username, password) VALUES ('$name', '$uname', '$password')");
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register - FigureKu</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    body { font-family: 'Poppins', sans-serif; }
  </style>
</head>
<body class="bg-black flex items-center justify-center min-h-screen">
  <div class="bg-gray-900 p-8 rounded-lg shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-semibold mb-6 text-purple-400 text-center">Daftar Akun</h2>
    <form method="post">
      <input type="text" name="name" placeholder="Nama Lengkap" class="w-full p-2 border border-gray-700 bg-black text-white rounded mb-4" required>
      <input type="text" name="username" placeholder="Username" class="w-full p-2 border border-gray-700 bg-black text-white rounded mb-4" required>
      <input type="password" name="password" placeholder="Password" class="w-full p-2 border border-gray-700 bg-black text-white rounded mb-4" required>
      <button type="submit" name="register" class="w-full bg-purple-600 text-white py-2 rounded hover:bg-purple-700">Daftar</button>
      <p class="mt-4 text-sm text-center text-gray-300">Sudah punya akun? <a href="login.php" class="text-purple-400 hover:underline">Login</a></p>
    </form>
  </div>
</body>
</html>
