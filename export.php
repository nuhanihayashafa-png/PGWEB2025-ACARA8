<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=daftar_kecamatan.csv');
$conn = new mysqli("localhost", "root", "", "pgweb_acara8");
if ($conn->connect_error) {
    die("Gagal koneksi DB: " . $conn->connect_error);
}
$sql = "SELECT * FROM data_kecamatan";
$res = $conn->query($sql);

$out = fopen('php://output', 'w');
fputcsv($out, ["ID","Kecamatan","Longitude","Latitude","Luas (km2)","Jumlah Penduduk"]);

while ($row = $res->fetch_assoc()) {
    fputcsv($out, [
        $row['id'],
        $row['kecamatan'],
        $row['longitude'],
        $row['latitude'],
        $row['luas'],
        $row['jumlah_penduduk']
    ]);
}
fclose($out);
$conn->close();
exit;
?>
