<?php
session_start();
include '../admin/dashHeader.php';
require_once "../koneksi.php";

// Inisialisasi variabel dan pesan kesalahan
$Nama = $kategori = $Harga = "";
$Nama_err = $kategori_err = $Harga_err = "";

// Memproses data formulir saat formulir dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memvalidasi Nama menu
    if (empty(trim($_POST["Nama"]))) {
        $Nama_err = "Nama menu harus diisi.";
    } else {
        $Nama = trim($_POST["Nama"]);
    }

    // Memvalidasi Kategori menu
    if (empty(trim($_POST["kategori"]))) {
        $kategori_err = "Kategori menu harus dipilih.";
    } else {
        $kategori = trim($_POST["kategori"]);
    }

    // Memvalidasi Harga menu
    if (empty(trim($_POST["Harga"]))) {
        $Harga_err = "Harga menu harus diisi.";
    } else {
        $Harga = trim($_POST["Harga"]);
    }

    // Proses upload foto jika tidak ada kesalahan validasi lainnya
    if (empty($Nama_err) && empty($item_category_err) && empty($Harga_err)) {
        // Memeriksa apakah file yang diunggah ada
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $foto = $_FILES['foto'];
            $foto_name = $foto['name'];
            $foto_tmp_name = $foto['tmp_name'];
            $foto_size = $foto['size'];
            $foto_error = $foto['error'];
            $foto_type = $foto['type'];

            // Mendapatkan ekstensi file
            $foto_ext = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));

            // Ekstensi file yang diperbolehkan
            $allowed_ext = array('jpg', 'jpeg', 'png');

            // Memvalidasi ekstensi file
            if (in_array($foto_ext, $allowed_ext)) {
                // Direktori tempat menyimpan file yang diunggah
                $upload_dir = '../img/';

                // Membuat nama file baru untuk mencegah duplikat
                $foto_new_name = uniqid('', true) . '.' . $foto_ext;

                // Menyimpan file ke direktori
                move_uploaded_file($foto_tmp_name, $upload_dir . $foto_new_name);

                // Menyimpan informasi menu ke dalam database
                $sql = "INSERT INTO products (Nama, kategori, Harga, gambar) VALUES (?, ?, ?, ?)";

                if ($stmt = mysqli_prepare($conn, $sql)) {
                    // Bind parameter ke statement
                    mysqli_stmt_bind_param($stmt, "ssis", $param_Nama, $param_Kategori, $param_Harga, $param_Foto);

                    // Set parameter
                    $param_Nama = $Nama;
                    $param_Kategori = $kategori;
                    $param_Harga = $Harga;
                    $param_Foto = $foto_new_name;

                    // Mengeksekusi statement
                    if (mysqli_stmt_execute($stmt)) {
                        // Redirect ke halaman sukses
                        header('Location: menu-panel.php');
                        exit();
                    } else {
                        echo "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
                    }

                    // Menutup statement
                    mysqli_stmt_close($stmt);
                }
            } else {
                echo "File yang diunggah harus berupa JPG, JPEG, atau PNG.";
            }
        } else {
            echo "Silakan pilih file untuk diunggah.";
        }
    }

    // Menutup koneksi
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tambah Menu Baru</title>
    <style>
        .wrapper {
            width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: calc(100% - 20px);
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-control.error {
            border-color: red;
        }

        .invalid-feedback {
            color: red;
        }

        .btn {
            background-color: #28334a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #1a2533;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <h3>Tambah Menu Baru</h3>
        <p>Mohon isi detail menu di bawah ini.</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="Nama">Nama menu:</label>
                <input type="text" name="Nama" id="Nama" placeholder="Masukkan nama menu" required class="form-control <?php echo (!empty($Nama_err)) ? 'error' : ''; ?>" value="<?php echo $Nama; ?>">
                <span class="invalid-feedback"><?php echo $Nama_err; ?></span>
            </div>

            <div class="form-group">
                <label for="kategori">Kategori menu:</label>
                <select name="kategori" id="item_category" class="form-control <?php echo (!empty($kategori_err)) ? 'error' : ''; ?>" required>
                    <option value="">Pilih kategori</option>
                    <option value="Makanan" <?php echo ($kategori == 'Makanan') ? 'selected' : ''; ?>>Makanan</option>
                    <option value="Minuman" <?php echo ($kategori == 'Minuman') ? 'selected' : ''; ?>>Minuman</option>
                </select>
                <span class="invalid-feedback"><?php echo $item_category_err; ?></span>
            </div>

            <div class="form-group">
                <label for="Harga">Harga menu :</label>
                <input type="number" name="Harga" id="Harga" placeholder="12000" required class="form-control <?php echo (!empty($Harga_err)) ? 'error' : ''; ?>" value="<?php echo $Harga; ?>">
                <span class="invalid-feedback"><?php echo $Harga_err; ?></span>
            </div>

            <div class="form-group">
                <label for="foto">Foto menu:</label>
                <input type="file" name="foto" id="foto" class="form-control-file">
            </div>

            <div class="form-group">
                <input type="submit" name="submit" class="btn" value="Tambah Menu">
            </div>
        </form>
    </div>
</body>

</html>