<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgweb_acara8";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$kecamatan = $_POST['kecamatan'];
$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];
$luas = $_POST['luas'];
$jumlah_penduduk = $_POST['jumlah_penduduk'];

$sql = "UPDATE data_kecamatan SET kecamatan=?, longitude=?, latitude=?, luas=?, jumlah_penduduk=? WHERE id=?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssddii", $kecamatan, $longitude, $latitude, $luas, $jumlah_penduduk, $id);

if ($stmt->execute()) {
    header("Location: index.php?msg=Data berhasil diupdate");
} else {
    header("Location: index.php?msg=Error: " . $sql . "<br>" . $conn->error);
}

$stmt->close();
$conn->close();
?>
