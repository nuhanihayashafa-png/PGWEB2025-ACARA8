<?php
// Aktifkan error reporting saat development
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgweb_acara8";

// Fungsi sanitasi string
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Untuk validasi koordinat (opsional, bisa di-extend)
function valid_lng($v) { return is_numeric($v) && $v >= -180 && $v <= 180; }
function valid_lat($v) { return is_numeric($v) && $v >= -90 && $v <= 90; }

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../index.php?msg=Metode+request+salah");
    exit;
}

$kecamatan      = isset($_POST['kecamatan']) ? sanitize($_POST['kecamatan']) : '';
$longitude      = isset($_POST['longitude']) ? sanitize($_POST['longitude']) : '';
$latitude       = isset($_POST['latitude']) ? sanitize($_POST['latitude']) : '';
$luas           = isset($_POST['luas']) ? sanitize($_POST['luas']) : '';
$jumlah_penduduk= isset($_POST['jumlah_penduduk']) ? sanitize($_POST['jumlah_penduduk']) : '';

$errors = [];
// Validasi wajib isi & tipe data
if ($kecamatan == "" || strlen($kecamatan) < 2) $errors[] = "Nama kecamatan minimal 2 karakter";
if (!is_numeric($longitude) || !valid_lng($longitude)) $errors[] = "Longitude harus angka -180 hingga 180";
if (!is_numeric($latitude)  || !valid_lat($latitude))  $errors[] = "Latitude harus angka -90 hingga 90";
if (!is_numeric($luas) || $luas <= 0) $errors[] = "Luas harus angka positif";
if (!is_numeric($jumlah_penduduk) || $jumlah_penduduk < 0) $errors[] = "Jumlah penduduk minimal 0";

// Jika gagal validasi, kembali ke form dengan notifikasi
if (count($errors) > 0) {
    $msg = implode(". ", $errors);
    header("Location: index.html?error=" . urlencode($msg));
    exit;
}

// Koneksi dan input jika lolos validasi
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    header("Location: index.html?error=" . urlencode("Gagal koneksi database"));
    exit;
}

// Cegah kecamatan duplikat
$cek = $conn->prepare("SELECT id FROM data_kecamatan WHERE kecamatan=? LIMIT 1");
$cek->bind_param("s", $kecamatan);
$cek->execute();
$cek->store_result();
if ($cek->num_rows > 0) {
    $cek->close();
    $conn->close();
    header("Location: index.html?error=" . urlencode("Kecamatan sudah ada!"));
    exit;
}
$cek->close();

// Simpan data dengan prepared statement
$stmt = $conn->prepare("INSERT INTO data_kecamatan (kecamatan, longitude, latitude, luas, jumlah_penduduk) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sdddi", $kecamatan, $longitude, $latitude, $luas, $jumlah_penduduk);

if ($stmt->execute()) {
    $msg = "Data kecamatan '$kecamatan' sukses ditambahkan 🤠";
    $stmt->close();
    $conn->close();
    header("Location: ../index.php?msg=" . urlencode($msg));
    exit;
} else {
    $stmt->close();
    $conn->close();
    header("Location: index.html?error=" . urlencode("Gagal menyimpan data"));
    exit;
}
?>
