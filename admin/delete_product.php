<?php
include '../db.php';
$id = $_GET['id'];

// Mulai transaction
$conn->begin_transaction();

try {
    // Hapus dulu data di order_items
    $conn->query("DELETE FROM order_items WHERE product_id = $id");
    
    // Hapus data di cart
    $conn->query("DELETE FROM cart WHERE product_id = $id");
    
    // Baru hapus produknya
    $conn->query("DELETE FROM products WHERE id = $id");
    
    // Commit jika semua berhasil
    $conn->commit();
    
    header("Location: dashboard.php");
} catch(Exception $e) {
    // Rollback jika terjadi error
    $conn->rollback();
    echo "Terjadi kesalahan: " . $e->getMessage();
}
?>
