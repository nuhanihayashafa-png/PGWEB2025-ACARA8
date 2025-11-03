<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pgweb_acara8";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("<div style='color:red;text-align:center;padding:24px;font-family:'Times New Roman',serif;'>Koneksi database gagal: " . $conn->connect_error . "</div>");
}
$markQuery = $conn->query("SELECT kecamatan, latitude, longitude, luas, jumlah_penduduk FROM data_kecamatan");
$markers = [];
while ($row = $markQuery->fetch_assoc()) {
    if (is_numeric($row["latitude"]) && is_numeric($row["longitude"]) && floatval($row["latitude"])!=0 && floatval($row["longitude"])!=0) {
        $markers[] = [
            "kecamatan" => $row["kecamatan"],
            "lat" => floatval($row["latitude"]),
            "lng" => floatval($row["longitude"]),
            "luas" => $row["luas"],
            "penduduk" => $row["jumlah_penduduk"]
        ];
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>MAP</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <style>
        body {
            background: #FFECB3;
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding-bottom: 40px;
        }
        .container {
            max-width: 1100px; margin: 0 auto;
        }
        .main-title {
            margin-top: 36px;
            margin-bottom: 8px;
            text-align: center;
            color: #b40418;
            font-size: 2.7rem;
            font-family: 'Times New Roman', Times, serif;
            letter-spacing: 2px;
            font-weight: bold;
            text-shadow: 1px 1px 8px #fff9f9;
        }
        .subtitle {
            text-align: center;
            color: #374178;
            font-size: 1.18em;
            font-family: 'Times New Roman', Times, serif;
            margin-bottom: 18px;
            font-weight: normal;
            letter-spacing: 0.5px;
        }
        .legend-box {
            background:#fff;
            border-radius:10px;
            border:1.7px solid #b40418;
            max-width: 380px;
            margin:0 auto 20px auto;
            padding:15px 22px 10px 22px;
            box-shadow:0 3px 14px #b4041825;
            font-family:'Times New Roman', Times, serif;
        }
        .legend-title {
            font-size:1.12em;
            color:#b40418;
            font-weight:700;
            margin-bottom:8px;
        }
        .legend-item {
            display:flex; align-items:center; font-size:1em; margin-bottom:6px; color:#31416B;
        }
        .legend-circle {
            display:inline-block; width:17px; height:17px;
            border-radius:50%; background:#e13838; border:2px solid #b40418;
            margin-right:10px;
        }
        .legend-marker {
            display:inline-block; font-size:1.18em;
            margin-right:8px;
        }
        #map {
            height: 510px;
            width: 96vw;
            max-width: 1080px;
            margin:0 auto 10px auto;
            border-radius:14px;
            border:2px solid #F5A7A7;
            box-shadow: 0 6px 28px #dab24538;
        }
        #no-data {
            text-align:center;
            padding:32px 0 10px 0;
            font-size:1.13em;
            color:#876114;
            display:none;
        }
        @media(max-width:900px){
            .main-title{font-size:1.6rem;}
            .container{max-width:99vw;}
            #map{height:320px;}
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-title">MAP</div>
        <div class="subtitle">Peta interaktif persebaran titik Kecamatan.<br>
        Klik marker merah untuk melihat detail kecamatan, luas, dan jumlah penduduk.</div>
        <div class="legend-box">
            <div class="legend-title">Legenda</div>
            <div class="legend-item">
                <span class="legend-circle"></span>
                <span>Titik Kecamatan (marker warna merah)</span>
            </div>
            <div class="legend-item">
                <span class="legend-marker">📍</span>
                <span>Klik marker untuk info detail</span>
            </div>
            <div class="legend-item" style="margin-bottom:1px;">
                <span class="legend-marker" style="color:#31416B;font-size:1.13em;">🗺️</span>
                <span>Drag, zoom, dan geser untuk eksplor peta</span>
            </div>
        </div>
        <div id="map"></div>
        <div id="no-data">Belum ada data kecamatan dengan lokasi valid untuk ditampilkan pada MAP.</div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const markerData = <?php echo json_encode($markers); ?>;
        var map = L.map('map').setView([-7.8, 110.3], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let bounds = [];
        let markerCount = 0;
        markerData.forEach(function(item){
            if(item.lat && item.lng && Math.abs(item.lat) <= 90 && Math.abs(item.lng) <= 180){
                var circle = L.circleMarker([item.lat, item.lng], {
                    radius: 8,
                    color: "#b40418",
                    weight: 2,
                    fillColor: "#e13838",
                    fillOpacity: 0.95
                }).addTo(map);
                circle.bindPopup(
                    `<div style="min-width:150px;font-family:'Times New Roman',Times,serif;font-size:1.06em;">
                        <b style="color:#b40418;">${item.kecamatan}</b><br>
                        <span style="color:#31416B;font-size:0.97em;">
                        <b>Luas:</b> ${parseFloat(item.luas).toFixed(2)} km²<br>
                        <b>Penduduk:</b> ${parseInt(item.penduduk)} jiwa</span>
                    </div>`
                );
                bounds.push([item.lat, item.lng]);
                markerCount++;
            }
        });

        if(markerCount === 0){
            document.getElementById('map').style.display = "none";
            document.getElementById('no-data').style.display = "block";
        } else if(bounds.length > 0){
            map.fitBounds(bounds, {maxZoom: 14, padding:[18,18]});
        }
    </script>
</body>
</html>
