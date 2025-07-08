<?php
include '../db.php';
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Produk</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
    body { font-family: 'Poppins', sans-serif; }
  </style>
</head>
<body class="bg-black text-white min-h-screen p-6">
  <div class="max-w-xl mx-auto bg-gray-900 p-6 rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-purple-400 text-center">Edit Produk</h2>

    <form action="" method="post" enctype="multipart/form-data" class="space-y-5">
      <div>
        <label class="block font-medium text-purple-300">Nama Produk</label>
        <input type="text" name="name" value="<?= $product['name'] ?>" class="w-full p-2 bg-gray-800 border border-purple-600 rounded focus:outline-none focus:ring-2 focus:ring-purple-500 text-white">
      </div>

      <div>
        <label class="block font-medium text-purple-300">Deskripsi</label>
        <textarea name="description" rows="4" class="w-full p-2 bg-gray-800 border border-purple-600 rounded focus:outline-none focus:ring-2 focus:ring-purple-500 text-white"><?= $product['description'] ?></textarea>
      </div>

      <div>
        <label class="block font-medium text-purple-300">Stock</label>
        <input type="number" name="stock" value="<?= $product['stock'] ?>" class="w-full p-2 bg-gray-800 border border-purple-600 rounded focus:outline-none focus:ring-2 focus:ring-purple-500 text-white">
      </div>

      <div>
        <label class="block font-medium text-purple-300">Harga</label>
        <input type="number" name="price" value="<?= $product['price'] ?>" class="w-full p-2 bg-gray-800 border border-purple-600 rounded focus:outline-none focus:ring-2 focus:ring-purple-500 text-white">
      </div>

      <div>
        <label class="block font-medium text-purple-300 mb-1">Gambar Saat Ini</label>
        <img src="../assets/uploads/<?= $product['image'] ?>" width="150" class="rounded shadow border border-purple-700">
      </div>

      <div>
        <label class="block font-medium text-purple-300">Ganti Gambar</label>
        <input type="file" name="image" class="w-full text-sm text-purple-100 bg-gray-800 border border-purple-600 rounded px-2 py-1">
      </div>

      <div class="text-center">
        <button type="submit" name="update" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded transition shadow">
          Update Produk
        </button>
      </div>
    </form>
  </div>

<?php
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $stock = $_POST['stock'];
    $price = $_POST['price'];
    $imageName = $product['image'];

    if ($_FILES['image']['name']) {
        $uploadDir = "../assets/uploads/";
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $imageName;
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, stock=?, price=?, image=? WHERE id=?");
    $stmt->bind_param("ssddsi", $name, $desc, $stock, $price, $imageName, $id);
    $stmt->execute();

    echo "<script>window.location.href = 'dashboard.php';</script>";
}
?>
</body>
</html>
