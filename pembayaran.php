<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;
$res = $conn->query("SELECT * FROM orders WHERE id = $order_id AND user_id = {$_SESSION['user']['id']}");
if ($res->num_rows == 0) {
    echo "Pesanan tidak ditemukan.";
    exit;
}

$order = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alamat = $conn->real_escape_string($_POST['alamat']);

    $target_dir = "uploads/";
    $filename = "bukti_" . time() . "_" . basename($_FILES["bukti"]["name"]);
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file)) {
        // Update alamat juga bersama bukti_transfer dan status
        $conn->query("UPDATE orders SET bukti_transfer = '$filename', alamat = '$alamat', status = 'Menunggu Konfirmasi' WHERE id = $order_id");
        echo "<script>alert('Bukti transfer dan alamat berhasil disimpan!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Upload gagal. Coba lagi.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pembayaran - FigureKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-black text-white flex items-center justify-center min-h-screen">
    <div class="bg-gray-900 p-8 rounded-lg shadow-lg w-full max-w-lg border border-purple-600">
        <!-- Tombol kembali -->
        <div class="mb-4">
            <a href="index.php" class="text-purple-400 hover:underline flex items-center space-x-1">
                <span>←</span><span>Back</span>
            </a>
        </div>

        <?php if (isset($order['status_verifikasi'])): ?>
            <?php if ($order['status_verifikasi'] === 'Valid'): ?>
                <div class="mb-6 p-4 rounded bg-green-900/80 border border-green-500 text-green-200 text-center font-semibold">
                    ✅ Pesanan Anda telah <span class="font-bold">DITERIMA</span> & akan segera diproses. Terima kasih!
                </div>
            <?php elseif ($order['status_verifikasi'] === 'Tidak Valid'): ?>
                <div class="mb-6 p-4 rounded bg-red-900/80 border border-red-500 text-red-200 text-center font-semibold">
                    ❌ Pesanan <span class="font-bold">TIDAK DITERIMA</span>. Silakan cek kembali pembayaran Anda dan <a href="checkout.php" class="underline text-red-300">coba lagi</a>.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <h2 class="text-2xl font-semibold text-center text-purple-400 mb-4">Instruksi Pembayaran</h2>
        
        <p class="mb-2 text-gray-300"><strong>Total:</strong> Rp <?= number_format($order['total']) ?></p>
        <p class="mb-4 text-gray-300">
            <strong>Silakan transfer ke rekening berikut:</strong><br>
            <span class="block mt-1">Bank BNI</span>
            <span class="block">No. Rek: <strong>1234567890</strong></span>
            <span class="block mb-2">a.n. FigureKu Bisnis</span>
        </p>

        <?php if (!isset($order['status_verifikasi']) || ($order['status_verifikasi'] !== 'Valid')): ?>
        <form method="post" enctype="multipart/form-data">
            <label class="block mb-2 font-semibold text-purple-300">Alamat Pengiriman:</label>
            <textarea name="alamat" required class="w-full border border-purple-600 bg-gray-800 text-white p-2 rounded mb-4" placeholder="Masukkan alamat pengiriman"><?= htmlspecialchars($order['alamat']) ?></textarea>

            <label class="block mb-2 font-semibold text-purple-300">Upload Bukti Transfer:</label>
            <input type="file" name="bukti" accept="image/*" class="w-full border border-purple-600 bg-gray-800 text-white p-2 rounded mb-4" required>

            <button type="submit" class="w-full bg-purple-700 hover:bg-purple-800 text-white py-2 rounded transition">Kirim Bukti & Alamat</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>


