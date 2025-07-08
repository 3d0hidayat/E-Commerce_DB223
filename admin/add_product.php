<?php
session_start();
if (!isset($_SESSION['admin'])) ;
include '../db.php';

if ($_POST) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];

    $image = $_FILES['image']['name'];
    $target = "../assets/uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    $stmt = $conn->prepare("INSERT INTO products (name, description, stock,price, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdds", $name, $desc, $stock, $price, $image);
    $stmt->execute();

    header('Location: dashboard.php');
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Produk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-black min-h-screen p-6 text-white">

  <div class="max-w-xl mx-auto bg-gray-900 p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-purple-400 text-center">Tambah Produk</h2>

    <form method="post" enctype="multipart/form-data" class="space-y-4">
      <div>
        <input name="name" placeholder="Nama Produk" required class="w-full p-2 bg-gray-800 border border-purple-600 rounded focus:outline-none focus:ring-2 focus:ring-purple-500 text-white">
      </div>

      <div>
        <textarea name="description" placeholder="Deskripsi" rows="4" required class="w-full p-2 bg-gray-800 border border-purple-600 rounded focus:outline-none focus:ring-2 focus:ring-purple-500 text-white"></textarea>
      </div>

      <div>
        <input name="stock" type="number" placeholder="Stock" required class="w-full p-2 bg-gray-800 border border-purple-600 rounded focus:outline-none focus:ring-2 focus:ring-purple-500 text-white">
      </div>

      <div>
        <input name="price" type="number" placeholder="Harga" required class="w-full p-2 bg-gray-800 border border-purple-600 rounded focus:outline-none focus:ring-2 focus:ring-purple-500 text-white">
      </div>

      <div>
        <input name="image" type="file" accept="image/*" required class="w-full text-sm text-purple-100 bg-gray-800 border border-purple-600 rounded px-2 py-1">
      </div>

      <div class="text-center">
        <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded transition shadow">
          Simpan
        </button>
      </div>
    </form>
  </div>

</body>
</html>
