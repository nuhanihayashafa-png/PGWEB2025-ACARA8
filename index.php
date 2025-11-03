<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgweb_acara8";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Data marker untuk map
$markQuery = $conn->query("SELECT kecamatan, latitude, longitude, luas, jumlah_penduduk FROM data_kecamatan");
$markers = [];
while ($row = $markQuery->fetch_assoc()) {
    if (
        is_numeric($row["latitude"]) && is_numeric($row["longitude"]) &&
        floatval($row["latitude"]) != 0 && floatval($row["longitude"]) != 0
    ) {
        $markers[] = [
            "kecamatan" => $row["kecamatan"],
            "lat" => floatval($row["latitude"]),
            "lng" => floatval($row["longitude"]),
            "luas" => $row["luas"],
            "penduduk" => $row["jumlah_penduduk"]
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>WEB GIS - Data Kecamatan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Rye&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        body {
            font-family: 'Roboto', Arial, serif;
            background: #fbe5b3;
            margin: 0;
            padding: 0 0 60px 0;
            background-image: url('https://www.transparenttextures.com/patterns/wood-pattern.png');
        }

        .header-gis {
            background: #97684b;
            color: #fffbe9;
            text-align: center;
            padding: 30px 0 15px 0;
            font-size: 2.4rem;
            font-family: 'Rye', cursive;
            letter-spacing: 2.5px;
            font-weight: 700;
            box-shadow: 0 5px 24px #99663c19;
        }

        .subtitle-gis {
            color: #d3c095;
            font-size: 1.12em;
            font-family: 'Roboto', Arial, sans-serif;
            text-align: center;
            margin-bottom: 8px;
        }

        #map-box {
            max-width: 1100px;
            margin: 30px auto 16px auto;
            padding: 0 10px;
        }

        #map {
            height: 420px;
            width: 100%;
            border-radius: 13px;
            border: 2.5px solid #daaf60;
            box-shadow: 0 3px 16px #dab24538;
        }

        .legend-map {
            background: #fff8ed;
            margin: 0 auto 12px auto;
            max-width: 410px;
            padding: 10px 15px 11px 15px;
            border-radius: 11px;
            box-shadow: 0 2px 9px #dbc07b34;
            border: 1.2px solid #dab182;
            color: #7d3a22;
            font-family: 'Roboto', Arial, serif;
            font-size: 1.09em;
            line-height: 1.66;
        }

        .legend-circle {
            display: inline-block;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #e13838;
            border: 2px solid #b40418;
            margin-right: 8px;
            vertical-align: -4px;
        }

        .main-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 10px;
        }

        h2 {
            text-align: center;
            font-family: 'Rye', cursive;
            color: #9e572a;
            letter-spacing: 4px;
            font-size: 2rem;
            margin-bottom: 8px;
            text-shadow: 2px 2px 0 #fff9ed, 4px 4px 3px #98663b45;
        }

        .top-link {
            display: block;
            text-align: center;
            margin-bottom: 16px;
        }

        table {
            margin: 0 auto;
            min-width: 830px;
            border-collapse: collapse;
            background: #fff9ed;
            border-radius: 10px;
            box-shadow: 0 4px 32px 0 rgba(80, 42, 6, .12);
            overflow: hidden;
        }

        th {
            background: #98663b;
            color: #fff7d1;
            letter-spacing: 2px;
            font-size: 1.05rem;
            font-family: 'Rye', cursive;
        }

        td {
            font-family: 'Times New Roman', Times, serif;
        }

        th,
        td {
            padding: 13px 22px;
            border-bottom: 2px solid #dab182;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background: #faedce;
        }

        .notif-cowboy {
            text-align: center;
            background: #ffe3bb;
            color: #684f21;
            border: 2.5px dashed #b7874a;
            margin: -3px auto 14px auto;
            max-width: 480px;
            padding: 13px 0;
            font-family: 'Rye', cursive;
            font-size: 1.12em;
            border-radius: 7px;
            box-shadow: 0 3px 16px #ecd0ad93;
        }

        .top-link a {
            color: #fff;
            background: #98663b;
            border-radius: 5px;
            padding: 7px 25px;
            text-decoration: none;
            font-weight: bold;
            font-family: 'Rye', cursive;
            font-size: 15px;
            box-shadow: 1px 1px 0 #d6ba8b;
            transition: background .15s;
            border: 2px solid #846040;
        }

        .top-link a:hover {
            background: #b7874a;
            color: #fff;
        }

        .edit-link,
        .hapus-link {
            color: #fff;
            text-decoration: none;
            padding: 4px 13px;
            border-radius: 4px;
            font-weight: bold;
            font-family: 'Rye', cursive;
            margin-left: 8px;
        }

        .edit-link {
            background: #4CAF50;
            border: 1px solid #388E3C;
        }

        .edit-link:hover {
            background: #45a049;
        }

        .hapus-link {
            background: #d99265;
            border: 1px solid #b66f36;
        }

        .hapus-link:hover {
            background: #af5635;
        }

        .about-cowboy {
            text-align: center;
            margin-top: 40px;
            font-family: 'Rye', cursive;
            color: #b28030;
            font-size: 1.03em;
            letter-spacing: 1.1px;
        }

        @media (max-width:850px) {
            table {
                min-width: 99vw;
            }
        }
    </style>
</head>

<body>
    <div class="header-gis">WEB GIS - Data Kecamatan di Kabupaten Sleman</div>
    <div class="subtitle-gis">map &amp; data</div>
    <div id="map-box">
        <div class="legend-map">
            <b>INFORMASI PETA:</b><br>
            <span class="legend-circle"></span> Titik lokasi kecamatan<br>
            <span style="font-size:1.13em;vertical-align:-1px;color:#e13838;"></span>
            Klik titik untuk info detail area
        </div>
        <div id="map"></div>
    </div>
    <div class="main-wrapper">
        <h2>Daftar Data Kecamatan</h2>
        <div class="top-link"><a href="input/index.html">Tambah Data Baru ↠</a></div>
        <?php if (isset($_GET['msg'])): ?>
            <div class="notif-cowboy"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>
        <form method="get" class="cowboy-search" style="margin-bottom:14px;text-align:center;">
            <input type="text" name="q" placeholder="Cari Kecamatan..." value="<?= htmlspecialchars(@$_GET['q']) ?>"
                style="padding:7px 14px;width:200px;border-radius:6px;border:2px solid #dab182;">
            <input type="number" name="min_jp" placeholder="Min Penduduk"
                value="<?= htmlspecialchars(@$_GET['min_jp']) ?>"
                style="padding:7px 8px;width:120px;border-radius:6px;border:2px solid #dab182;">
            <input type="number" name="max_jp" placeholder="Max Penduduk"
                value="<?= htmlspecialchars(@$_GET['max_jp']) ?>"
                style="padding:7px 8px;width:120px;border-radius:6px;border:2px solid #dab182;">
            <button type="submit" class="tombol-cowboy" style="margin-left:8px;">Cari</button>
        </form>
        <div style="text-align:center; margin-bottom:14px;">
            <button onclick="window.print()" class="tombol-cowboy">🖨 Print Tabel</button>
            <form method="post" action="export.php" style="display:inline;">
                <button type="submit" class="tombol-export">Export CSV</button>
            </form>
        </div>
        <?php
        $where = [];
        if (!empty($_GET['q'])) {
            $key = $conn->real_escape_string($_GET['q']);
            $where[] = "kecamatan LIKE '%$key%'";
        }
        if (!empty($_GET['min_jp']))
            $where[] = "jumlah_penduduk >= " . intval($_GET['min_jp']);
        if (!empty($_GET['max_jp']))
            $where[] = "jumlah_penduduk <= " . intval($_GET['max_jp']);

        $per_page = 10;
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $start = ($page - 1) * $per_page;

        $sqlc = "SELECT COUNT(*) FROM data_kecamatan";
        if ($where)
            $sqlc .= " WHERE " . implode(" AND ", $where);
        $total = $conn->query($sqlc)->fetch_row()[0];
        $total_pages = max(1, ceil($total / $per_page));

        $sql = "SELECT * FROM data_kecamatan";
        if ($where)
            $sql .= " WHERE " . implode(" AND ", $where);
        $sql .= " LIMIT $start, $per_page";
        $result = $conn->query($sql);

        echo "<table>";
        echo "<tr>
        <th>ID</th><th>Kecamatan</th><th>Longitude</th><th>Latitude</th>
        <th>Luas (km&sup2;)</th><th>Jumlah Penduduk</th><th>Aksi</th></tr>";
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['kecamatan']}</td>
                <td>{$row['longitude']}</td>
                <td>{$row['latitude']}</td>
                <td>{$row['luas']}</td>
                <td align='right'>{$row['jumlah_penduduk']}</td>
                <td>
                    <a class='edit-link' href='edit.php?id={$row['id']}'>Edit</a>
                    <a class='hapus-link' href='delete.php?id={$row['id']}' onclick=\"return confirm('Yakin hapus data ini?')\">Hapus</a>
                </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>0 results</td></tr>";
        }
        echo "</table>";
        echo "<div style='text-align:center;margin:20px 0'>";
        for ($i = 1; $i <= $total_pages; $i++) {
            $aktif = $i == $page ? "background:#98663b;color:#fff;border-radius:4px;" : "color:#98663b;";
            $url = "?page=$i&q=" . urlencode(@$_GET['q']) . "&min_jp=" . @$_GET['min_jp'] . "&max_jp=" . @$_GET['max_jp'];
            echo "<a href='$url' style='margin:0 3px;padding:5px 12px;font-family:Rye,cursive;$aktif'>$i</a>";
        }
        echo "</div>";
        $conn->close();
        ?>
    </div>
    <div class="about-cowboy">
        <hr style="margin:40px 0 18px 0;height:2px;border:none;background:#dab182;">
        <div>
            <span style="font-family:'Rye',cursive;font-size:1.18em;color:#98663b">About:</span><br>
            <span style="font-family:'Rye',cursive;color:#654321;font-size:1.05em;">
                Nuha Nihaya Shafa<br>
            </span>
            <span style="font-family:Roboto,Arial;color:#b28030;">NIM: 24/545410/SV/25677</span>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Tampilkan marker di peta utama
        const markerData = <?php echo json_encode($markers); ?>;
        var map = L.map('map').setView([-7.8, 110.3], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        let bounds = [];
        markerData.forEach(function (item) {
            if (item.lat && item.lng && Math.abs(item.lat) <= 90 && Math.abs(item.lng) <= 180) {
                var circle = L.circleMarker([item.lat, item.lng], {
                    radius: 8,
                    color: "#b40418",
                    weight: 2,
                    fillColor: "#e13838",
                    fillOpacity: 0.94
                }).addTo(map);
                circle.bindPopup(
                    `<div style="min-width:150px;font-family:'Times New Roman',Times,serif;">
                        <b style="color:#b40418;">${item.kecamatan}</b><br>
                        <b>Luas:</b> ${parseFloat(item.luas).toFixed(2)} km²<br>
                        <b>Penduduk:</b> ${parseInt(item.penduduk)} jiwa
                    </div>`
                );
                bounds.push([item.lat, item.lng]);
            }
        });
        if (bounds.length > 0) { map.fitBounds(bounds, { maxZoom: 14, padding: [18, 18] }); }
    </script>
</body>

</html>