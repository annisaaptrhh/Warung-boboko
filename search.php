<?php
session_start();
require_once('koneksi.php');

// Ambil query dari GET
$query = $_GET['query'];

// Membuat prepared statement untuk meningkatkan keamanan
if (!empty(trim($query))) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE SUBSTRING(nama, 1, 20) LIKE ? OR SUBSTRING(nama, LOCATE(' ', nama) + 1, 20) LIKE ?");
    $searchTerm = $query . "%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {  // Jika ada hasil
        while ($row = $result->fetch_assoc()) {
            echo '<a href="home.php#' . $row['id'] . '" class="search-result-link">' . $row['Nama'] . '</a>' . '<br>';
        }
    } else {  // Jika tidak ada hasil
        echo "product not found";
    }

    $stmt->close();
}
$conn->close();
