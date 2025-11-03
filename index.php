<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Daftar Data Kecamatan (Coboy Style)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Rye&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
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
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 0 #fff9ed, 4px 4px 3px #98663b45;
        }

        .top-link {
            display: block;
            text-align: center;
            margin-bottom: 20px;
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

        .cowboy-search {
            text-align: center;
            margin-bottom: 14px;
        }

        .tombol-cowboy {
            font-family: 'Rye', cursive;
            background: #98663b;
            color: #fff;
            padding: 7px 22px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 8px;
        }

        .tombol-cowboy:hover {
            background: #d99265;
            color: #56321c;
        }

        .tombol-export {
            font-family: 'Rye', cursive;
            background: #b28030;
            color: #fff;
            padding: 7px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 8px;
        }

        .tombol-export:hover {
            background: #98663b;
            color: #fff;
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

        .hapus-link {
            color: #fff;
            background: #d99265;
            font-family: 'Rye', cursive;
            font-weight: bold;
            text-decoration: none;
            padding: 4px 13px;
            border-radius: 4px;
            transition: background .15s;
            border: 1px solid #b66f36;
            margin-left: 8px;
        }

        .hapus-link:hover {
            background: #af5635;
        }

        .edit-link {
            color: #fff;
            background: #4CAF50;
            font-family: 'Rye', cursive;
            font-weight: bold;
            text-decoration: none;
            padding: 4px 13px;
            border-radius: 4px;
            transition: background .15s;
            border: 1px solid #388E3C;
            margin-left: 8px;
        }

        .edit-link:hover {
            background: #45a049;
        }

        .about-cowboy {
            text-align: center;
            margin-top: 40px;
            font-family: 'Rye', cursive;
            color: #b28030;
            font-size: 1.03em;
            letter-spacing: 1.1px;
        }

        @media (max-width: 700px) {

            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            th {
                text-align: center;
            }

            td {
                padding: 10px 7px;
                min-width: 90px;
            }

            table {
                min-width: 90vw;
            }
        }

        /* Modal Map Style */
        #map-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4)
        }

        #map-modal .modal-box {
            position: relative;
            width: 85vw;
            max-width: 700px;
            background: #fff9ed;
            margin: 60px auto 0 auto;
            box-shadow: 0 4px 24px #44280654;
            border-radius: 16px;
            padding: 24px;
        }

        #daftarMap {
            width: 100%;
            height: 400px;
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <h2>Daftar Data Kecamatan</h2>
    <div class="top-link"><a href="input/index.html">Tambah Data Baru ↠</a></div>

    <div style="text-align:center; margin-bottom:20px;">
        <button id="show-map-btn" class="tombol-cowboy">Click Map</button>
    </div>
    <div id="map-modal">
        <div class="modal-box">
            <div style="text-align:right;margin-bottom:2px;">
                <button onclick="closeMap()" class="tombol-cowboy"
                    style="padding:7px 17px;font-size:1em;">Close</button>
            </div>
            <div id="daftarMap"></div>
        </div>
    </div>

    <?php if (isset($_GET['msg'])): ?>
        <div class="notif-cowboy"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <div style="text-align:center; margin-bottom:14px;">
        <button onclick="window.print()" class="tombol-cowboy">🖨 Print Tabel</button>
        <form method="post" action="export.php" style="display:inline;">
            <button type="submit" class="tombol-export">Export CSV</button>
        </form>
    </div>

    <form method="get" class="cowboy-search">
        <input type="text" name="q" placeholder="Cari Kecamatan..." value="<?= htmlspecialchars(@$_GET['q']) ?>"
            style="padding:7px 14px;width:200px;border-radius:6px;border:2px solid #dab182;">
        <input type="number" name="min_jp" placeholder="Min Penduduk" value="<?= htmlspecialchars(@$_GET['min_jp']) ?>"
            style="padding:7px 8px;width:120px;border-radius:6px;border:2px solid #dab182;">
        <input type="number" name="max_jp" placeholder="Max Penduduk" value="<?= htmlspecialchars(@$_GET['max_jp']) ?>"
            style="padding:7px 8px;width:120px;border-radius:6px;border:2px solid #dab182;">
        <button type="submit" class="tombol-cowboy" style="margin-left:8px;">Cari</button>
    </form>

    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pgweb_acara8";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $where = [];
    if (!empty($_GET['q'])) {
        $key = $conn->real_escape_string($_GET['q']);
        $where[] = "kecamatan LIKE '%$key%'";
    }
    if (!empty($_GET['min_jp'])) {
        $min = intval($_GET['min_jp']);
        $where[] = "jumlah_penduduk >= $min";
    }
    if (!empty($_GET['max_jp'])) {
        $max = intval($_GET['max_jp']);
        $where[] = "jumlah_penduduk <= $max";
    }

    // Paging
    $per_page = 10;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $start = ($page - 1) * $per_page;

    $sqlc = "SELECT COUNT(*) FROM data_kecamatan";
    if (count($where) > 0) {
        $sqlc .= " WHERE " . implode(' AND ', $where);
    }
    $total = $conn->query($sqlc)->fetch_row()[0];
    $total_pages = max(1, ceil($total / $per_page));

    $sql = "SELECT * FROM data_kecamatan";
    if (count($where) > 0) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " LIMIT $start, $per_page";
    $result = $conn->query($sql);

    // Data marker untuk peta
    $markQuery = $conn->query("SELECT kecamatan, latitude, longitude FROM data_kecamatan");
    $markers = [];
    while ($row = $markQuery->fetch_assoc()) {
        $markers[] = [
            "kecamatan" => $row["kecamatan"],
            "lat" => floatval($row["latitude"]),
            "lng" => floatval($row["longitude"])
        ];
    }
    ?>

    <script>
        const kecamatanMarkers = <?= json_encode($markers) ?>;
    </script>

    <?php
    echo "<table>
    <tr>
        <th>ID</th>
        <th>Kecamatan</th>
        <th>Longitude</th>
        <th>Latitude</th>
        <th>Luas (km&sup2;)</th>
        <th>Jumlah Penduduk</th>
        <th>Aksi</th>
    </tr>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
            <td>" . $row['id'] . "</td>
            <td>" . $row['kecamatan'] . "</td>
            <td>" . $row['longitude'] . "</td>
            <td>" . $row['latitude'] . "</td>
            <td>" . $row['luas'] . "</td>
            <td align='right'>" . $row['jumlah_penduduk'] . "</td>
            <td>
                <a class='edit-link' href='edit.php?id=" . $row['id'] . "'>Edit</a>
                <a class='hapus-link' href='delete.php?id=" . $row['id'] . "' onclick=\"return confirm('Yakin hapus data ini?')\">Hapus</a>
            </td>
        </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>0 results</td></tr>";
    }
    echo "</table>";

    // Paging links
    echo "<div style='text-align:center;margin:20px 0'>";
    for ($i = 1; $i <= $total_pages; $i++) {
        $aktif = $i == $page ? "background:#98663b;color:#fff;border-radius:4px;" : "color:#98663b;";
        $url = "?page=$i&q=" . urlencode(@$_GET['q']) . "&min_jp=" . @$_GET['min_jp'] . "&max_jp=" . @$_GET['max_jp'];
        echo "<a href='$url' style='margin:0 3px;padding:5px 12px;font-family:Rye,cursive;$aktif'>$i</a>";
    }
    echo "</div>";

    $conn->close();
    ?>
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
        function closeMap() {
            document.getElementById('map-modal').style.display = "none";
        }
        document.addEventListener("DOMContentLoaded", function () {
            var mapInited = false, map;
            document.getElementById("show-map-btn").onclick = function () {
                document.getElementById("map-modal").style.display = "block";
                if (!mapInited) {
                    map = L.map('daftarMap').setView([-7.8, 110.3], 11);
                    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                        attribution: 'Tiles © Esri'
                    }).addTo(map);
                    kecamatanMarkers.forEach(function (item) {
                        if (item.lat && item.lng) {
                            L.marker([item.lat, item.lng]).addTo(map)
                                .bindPopup(item.kecamatan);
                        }
                    });
                    mapInited = true;
                }
            }
        });
    </script>
</body>

</html>