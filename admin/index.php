<?php
session_start();
include "../config.php";

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Proses tambah produk jika form disubmit
if (isset($_POST['submit'])) {
    $nama_produk = $_POST['nama_produk'];
    $kategori_produk = $_POST['kategori_produk'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $stok = $_POST['stok'];

    // Proses upload gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp_name = $_FILES['gambar']['tmp_name'];
    $upload_dir = "uploads/";

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $gambar_path = $upload_dir . basename($gambar);

    if (move_uploaded_file($tmp_name, $gambar_path)) {
        $query = "INSERT INTO produk (nama_produk, kategori_produk, gambar, harga, deskripsi, stok) 
                  VALUES ('$nama_produk', '$kategori_produk', '$gambar', '$harga', '$deskripsi', '$stok')";

        if (mysqli_query($conn, $query)) {
            echo "Produk berhasil ditambahkan!";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Gagal upload gambar.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Produk</title>
</head>

<body>
    <h1>Halaman Admin</h1>
    <h2>Data Produk</h2>
    <h3>Selamat datang, admin <?php echo $_SESSION['username'] ?></h3>

    <!-- Form Logout -->
    <form action="logout.php" method="POST" style="margin-top: 20px;">
        <button type="submit">Logout</button>
    </form>

    <!-- Form Tambah Produk -->
    <form action="" method="POST" enctype="multipart/form-data">
        <label>Nama Produk:</label><br>
        <input type="text" name="nama_produk" required><br><br>

        <label>Kategori Produk:</label><br>
        <select name="kategori_produk" required>
            <option value="Agama">Agama</option>
            <option value="MaPel">MaPel</option>
            <option value="Novel">Novel</option>
        </select><br><br>

        <label>Gambar:</label><br>
        <input type="file" name="gambar" required><br><br>

        <label>Harga:</label><br>
        <input type="number" name="harga" required><br><br>

        <label>Deskripsi:</label><br>
        <textarea name="deskripsi" required></textarea><br><br>

        <label>Stok:</label><br>
        <input type="number" name="stok" required><br><br>

        <button type="submit" name="submit">Tambah Produk</button>
    </form>

    <!-- Tabel Data Produk -->
    <h2>Daftar Produk</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Gambar</th>
            <th>Harga</th>
            <th>Deskripsi</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;
        $query_produk = "SELECT * FROM produk";
        $result_produk = mysqli_query($conn, $query_produk);

        if (mysqli_num_rows($result_produk) > 0) :
            while ($row_produk = mysqli_fetch_assoc($result_produk)) :
        ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row_produk['nama_produk']; ?></td>
                    <td><?php echo $row_produk['kategori_produk']; ?></td>
                    <td><img src="uploads/<?php echo $row_produk['gambar']; ?>" width="50"></td>
                    <td><?php echo $row_produk['harga']; ?></td>
                    <td><?php echo $row_produk['deskripsi']; ?></td>
                    <td><?php echo $row_produk['stok']; ?></td>
                    <td>
                        <a href="edit_produk.php?id=<?php echo $row_produk['id_produk']; ?>">Edit</a> |
                        <a href="hapus_produk.php?id=<?php echo $row_produk['id_produk']; ?>" onclick="return confirm('Yakin ingin hapus produk ini?');">Delete</a>
                    </td>
                </tr>
        <?php
            endwhile;
        else :
            echo "<tr><td colspan='8'>Tidak ada data produk.</td></tr>";
        endif;
        ?>
    </table>

    <!-- Tabel Data User -->
    <h2>Data User</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Username</th>
            <th>Nama Lengkap</th>
            <th>No Telp</th>
            <th>Alamat</th>
            <th>Dibuat Pada</th>
        </tr>

        <?php
        $no = 1;
        $query_user = "SELECT * FROM user";
        $result_user = mysqli_query($conn, $query_user);

        if (mysqli_num_rows($result_user) > 0) :
            while ($row_user = mysqli_fetch_assoc($result_user)) :
        ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $row_user['username']; ?></td>
                    <td><?php echo $row_user['nama_lengkap']; ?></td>
                    <td><?php echo $row_user['no_telepon']; ?></td>
                    <td><?php echo $row_user['alamat']; ?></td>
                    <td><?php echo $row_user['created_at']; ?></td>
                </tr>
        <?php
            endwhile;
        else :
            echo "<tr><td colspan='6'>Tidak ada data user.</td></tr>";
        endif;
        ?>
    </table>

    <!-- Tabel Data Pesanan -->
    <h2>Data Pesanan</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID Pesanan</th>
            <th>Pelanggan</th>
            <th>Total</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>

        <?php
        $query_pesanan = "SELECT pesanan.*, user.username 
                          FROM pesanan 
                          JOIN user ON pesanan.id_user = user.id_user
                          ORDER BY pesanan.created_at DESC";
        $result_pesanan = mysqli_query($conn, $query_pesanan);

        if (mysqli_num_rows($result_pesanan) > 0) :
            while ($row = mysqli_fetch_assoc($result_pesanan)) :
        ?>
                <tr>
                    <td><?= $row['id_pesanan'] ?></td>
                    <td><?= $row['username'] ?></td>
                    <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                    <td>
                        <form action="update_status.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id_pesanan" value="<?= $row['id_pesanan'] ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?= ($row['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="diproses" <?= ($row['status'] == 'diproses') ? 'selected' : '' ?>>Diproses</option>
                                <option value="dikirim" <?= ($row['status'] == 'dikirim') ? 'selected' : '' ?>>Dikirim</option>
                                <option value="selesai" <?= ($row['status'] == 'selesai') ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </form>
                    </td>
                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                    <td><a href="detail_pesanan.php?id=<?= $row['id_pesanan'] ?>">Detail</a></td>
                </tr>
        <?php
            endwhile;
        else :
            echo "<tr><td colspan='6'>Tidak ada pesanan.</td></tr>";
        endif;
        ?>
    </table>
</body>

</html>