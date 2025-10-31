<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgweb_acara8";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id === 0){
    header("Location: index.php");
    exit;
}

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "DELETE FROM data_kecamatan WHERE id=$id";
mysqli_query($conn, $sql);
mysqli_close($conn);

header("Location: index.php");
exit;
?>
