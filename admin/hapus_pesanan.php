<?php
include '../config.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $del_detail = mysqli_query($conn, "DELETE FROM detail_pesanan WHERE id_pesanan = $id");
    $del_pesanan = mysqli_query($conn, "DELETE FROM pesanan WHERE id_pesanan = $id");

    if ($del_detail && $del_pesanan) {
        echo "✅ Pesanan dan detail berhasil dihapus. ID: $id<br>";
    } else {
        echo "❌ Gagal menghapus<br>";
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "❌ ID tidak valid.";
}
