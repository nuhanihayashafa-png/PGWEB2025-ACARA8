<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Data Kecamatan - Coboy Style</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Rye&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: #fbe5b3;
            margin: 0;
            padding: 40px 0;
            background-image: url('https://www.transparenttextures.com/patterns/wood-pattern.png');
        }

        h2 {
            text-align: center;
            font-family: 'Rye', cursive;
            color: #9e572a;
            letter-spacing: 4px;
            font-size: 2.1rem;
            margin-bottom: 14px;
            text-shadow: 2px 2px 0 #fff9ed, 4px 4px 3px #98663b45;
        }

        form {
            background: #fff9ed;
            padding: 32px 30px 28px 30px;
            max-width: 420px;
            margin: 40px auto 0 auto;
            border-radius: 12px;
            box-shadow: 0 4px 24px 0 rgba(80, 42, 6, .14);
            border: 2.5px solid #ddb173;
        }

        label {
            display: block;
            margin-top: 18px;
            font-family: 'Rye', cursive;
            color: #7a4b22;
            font-size: 1.13rem;
            letter-spacing: 1px;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 7px 12px;
            box-sizing: border-box;
            border-radius: 5px;
            border: 2px solid #dab182;
            margin-top: 5px;
            background: #f9f2e1;
            color: #654321;
            font-size: 1em;
        }

        input[type="submit"] {
            background: #98663b;
            color: #fff;
            padding: 11px 35px;
            border: none;
            margin-top: 26px;
            border-radius: 7px;
            cursor: pointer;
            font-family: 'Rye', cursive;
            font-size: 1.08em;
            font-weight: bold;
            box-shadow: 1px 1px 0 #d6ba8b;
            transition: background .15s;
            letter-spacing: 2px;
        }

        input[type="submit"]:hover {
            background: #d99265;
            color: #222;
        }

        .link-tabel {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #b28030;
            font-weight: bold;
            font-family: 'Rye', cursive;
            font-size: 1.1rem;
            text-decoration: none;
        }

        .link-tabel:hover {
            text-decoration: underline;
            color: #9e572a;
        }
    </style>
</head>

<body>
    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pgweb_acara8";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $id = $_GET['id'];
    $sql = "SELECT * FROM data_kecamatan WHERE id=$id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    ?>

    <h2>Edit Data Kecamatan</h2>
    <form action="update.php" method="post" autocomplete="off">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <label>Kecamatan:
            <input type="text" name="kecamatan" value="<?= $row['kecamatan'] ?>" required>
        </label>
        <label>Longitude:
            <input type="text" name="longitude" value="<?= $row['longitude'] ?>" required placeholder="Contoh: 110.3011">
        </label>
        <label>Latitude:
            <input type="text" name="latitude" value="<?= $row['latitude'] ?>" required placeholder="Contoh: -7.7825">
        </label>
        <label>Luas (km&sup2;):
            <input type="number" name="luas" step="any" value="<?= $row['luas'] ?>" required min="0" placeholder="Contoh: 37.50">
        </label>
        <label>Jumlah Penduduk:
            <input type="number" name="jumlah_penduduk" step="1" value="<?= $row['jumlah_penduduk'] ?>" required min="0">
        </label>
        <input type="submit" value="➽ UPDATE">
    </form>
    <a class="link-tabel" href="index.php">« Kembali ke Tabel</a>
</body>

</html>
<?php
$conn->close();
?>
