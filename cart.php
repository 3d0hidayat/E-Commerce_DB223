<?php
session_start();
include 'db.php';

// Tambah produk ke cart
if ($_POST && $_POST['action'] == 'add') {
    $id = (int) $_POST['product_id'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}

// Update jumlah
if (isset($_POST['update_qty'])) {
    $id = (int) $_POST['product_id'];
    $new_qty = max(1, (int) $_POST['new_qty']);
    $_SESSION['cart'][$id] = $new_qty;
}

// Tambah Qty
if (isset($_GET['plus'])) {
    $id = (int) $_GET['plus'];
    $_SESSION['cart'][$id]++;
    header("Location: cart.php");
    exit;
}

// Kurangi Qty
if (isset($_GET['minus'])) {
    $id = (int) $_GET['minus'];
    if ($_SESSION['cart'][$id] > 1) {
        $_SESSION['cart'][$id]--;
    } else {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit;
}

// Hapus produk
if (isset($_GET['remove'])) {
    $id = (int) $_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - FigureKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-black text-white min-h-screen p-6">
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold text-center text-purple-400 mb-6">🛒 Keranjang Belanja</h1>

        <div class="text-center mb-6">
            <a href="index.php" class="text-purple-300 underline hover:text-purple-400 transition">&larr; Kembali ke toko</a>
        </div>

        <div class="overflow-x-auto bg-gray-900 rounded-lg shadow-lg">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-800 text-purple-300 uppercase text-sm">
                    <tr>
                        <th class="p-4">Produk</th>
                        <th class="p-4 text-center">Qty</th>
                        <th class="p-4 text-right">Harga</th>
                        <th class="p-4 text-right">Total</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-white">
                    <?php
                    $total = 0;
                    if (!empty($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $id => $qty) {
                            $id = (int) $id;
                            $res = $conn->query("SELECT * FROM products WHERE id = $id");
                            if ($res && $res->num_rows > 0) {
                                $p = $res->fetch_assoc();
                                $subtotal = $p['price'] * $qty;
                                $total += $subtotal;
                                echo "<tr class='border-t border-gray-800 hover:bg-gray-800 transition duration-200'>
                                    <td class='p-4 font-medium text-purple-200'>{$p['name']}</td>
                                    <td class='p-4 text-center'>
                                        <div class='flex justify-center items-center gap-2'>
                                            <a href='cart.php?minus=$id' class='bg-purple-700 hover:bg-purple-800 text-white px-3 py-1 rounded-full'>−</a>
                                            <span class='px-2'>$qty</span>
                                            <a href='cart.php?plus=$id' class='bg-purple-700 hover:bg-purple-800 text-white px-3 py-1 rounded-full'>+</a>
                                        </div>
                                    </td>
                                    <td class='p-4 text-right text-gray-300'>Rp " . number_format($p['price']) . "</td>
                                    <td class='p-4 text-right text-gray-300'>Rp " . number_format($subtotal) . "</td>
                                    <td class='p-4 text-center'>
                                        <a href='cart.php?remove=$id' class='text-red-400 hover:text-red-300 transition'>Hapus</a>
                                    </td>
                                </tr>";
                            }
                        }
                        $_SESSION['total'] = $total;
                        echo "<tr class='bg-gray-800 text-purple-300 font-semibold border-t border-purple-500'>
                                <td colspan='3' class='text-right p-4'>Total:</td>
                                <td class='text-right p-4'>Rp " . number_format($total) . "</td>
                                <td></td>
                            </tr>";
                    } else {
                        echo "<tr>
                                <td colspan='5' class='text-center py-10 text-purple-400 text-lg'>
                                    Yah, Keranjangnya masih kosong 😔
                                </td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="mt-8 flex justify-end">
                <form action="checkout.php" method="post">
                    <input type="hidden" name="total" value="<?= $total ?>">
                    <button type="submit" class="bg-gradient-to-r from-purple-600 to-purple-800 hover:from-purple-700 hover:to-purple-900 text-white px-6 py-3 rounded-md shadow-lg transition-all duration-200">
                        ✅ Lanjut ke Checkout
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
