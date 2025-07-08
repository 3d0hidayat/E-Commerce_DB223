<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status_verifikasi'];
    $catatan = $_POST['catatan_admin'];

    // Ambil status verifikasi sebelumnya
    $prev = $conn->query("SELECT status_verifikasi FROM orders WHERE id = $order_id")->fetch_assoc();
    $prev_status = $prev['status_verifikasi'];

    // Update status & catatan
    $stmt = $conn->prepare("UPDATE orders SET status_verifikasi=?, catatan_admin=? WHERE id=?");
    $stmt->bind_param("ssi", $status, $catatan, $order_id);
    $stmt->execute();
    $stmt->close();

    // Ambil semua item dalam pesanan
    $items = $conn->query("SELECT product_id, qty FROM order_items WHERE order_id = $order_id");

    if ($status == "Valid" && $prev_status != "Valid") {
        while ($item = $items->fetch_assoc()) {
            $conn->query("UPDATE products SET stock = stock - {$item['qty']} WHERE id = {$item['product_id']}");
        }
    } elseif ($prev_status == "Valid" && $status != "Valid") {
        $items = $conn->query("SELECT product_id, qty FROM order_items WHERE order_id = $order_id");
        while ($item = $items->fetch_assoc()) {
            $conn->query("UPDATE products SET stock = stock + {$item['qty']} WHERE id = {$item['product_id']}");
        }
    }

    header("Location: verifikasi_pembayaran.php");
    exit;
}

$result = $conn->query("SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
if (!$result) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
      body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-black text-white min-h-screen p-6">

  <h1 class="text-3xl font-bold mb-4 text-purple-400">Verifikasi Pembayaran</h1>
  <a href="dashboard.php" class="text-purple-300 hover:underline">← Kembali ke Dashboard</a>

  <div class="overflow-x-auto mt-6">
    <table class="w-full bg-gray-900 text-white rounded-lg shadow-md text-sm">
      <thead class="bg-purple-800 text-purple-200">
        <tr>
          <th class="p-2 text-left">ID</th>
          <th class="p-2 text-left">Nama</th>
          <th class="p-2 text-left">Alamat</th>
          <th class="p-2 text-left">Produk Dibeli</th>
          <th class="p-2 text-left">Total</th>
          <th class="p-2 text-left">Bukti</th>
          <th class="p-2 text-left">Status</th>
          <th class="p-2 text-left">Catatan</th>
          <th class="p-2 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
              $order_id = $row['id'];
              $items = $conn->query("SELECT p.name, oi.qty FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = $order_id");
              $produk_dibeli = [];
              while ($item = $items->fetch_assoc()) {
                  $produk_dibeli[] = htmlspecialchars($item['name']) . " (Qty: " . $item['qty'] . ")";
              }
              $produk_dibeli_str = implode(", ", $produk_dibeli);
          ?>
          <tr class="border-t border-gray-700 hover:bg-gray-800 transition">
            <td class="p-2"><?= $row['id'] ?></td>
            <td class="p-2"><?= htmlspecialchars($row['name']) ?></td>
            <td class="p-2"><?= htmlspecialchars($row['alamat']) ?></td>
            <td class="p-2"><?= $produk_dibeli_str ?></td>
            <td class="p-2">Rp <?= number_format($row['total']) ?></td>
            <td class="p-2">
              <?php if ($row['bukti_transfer']): ?>
                <a href="../uploads/<?= $row['bukti_transfer'] ?>" target="_blank" class="text-purple-400 hover:underline">Lihat</a>
              <?php else: ?>
                <span class="text-gray-400 italic">Belum Upload</span>
              <?php endif; ?>
            </td>
            <td class="p-2"><?= $row['status_verifikasi'] ?></td>
            <td class="p-2"><?= htmlspecialchars($row['catatan_admin']) ?></td>
            <td class="p-2">
              <form method="post" class="space-y-1">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <select name="status_verifikasi" class="w-full bg-gray-800 text-white border border-purple-600 rounded px-2 py-1 text-sm">
                  <option value="Valid" <?= $row['status_verifikasi'] == 'Valid' ? 'selected' : '' ?>>Valid</option>
                  <option value="Tidak Valid" <?= $row['status_verifikasi'] == 'Tidak Valid' ? 'selected' : '' ?>>Tidak Valid</option>
                </select>
                <textarea name="catatan_admin" placeholder="Catatan (jika ada)" class="w-full bg-gray-800 text-white border border-purple-600 p-1 rounded text-sm"><?= htmlspecialchars($row['catatan_admin']) ?></textarea>
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-1 rounded text-sm">
                  Simpan
                </button>
              </form>
            </td>
          </tr>
        <?php endwhile ?>
      </tbody>
    </table>
  </div>

</body>
</html>
