<?php
$attractions = $attractions ?? [];
$baseUrl = rtrim(APP_URL ?? '', '/');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Điểm Đến Nổi Bật') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        body { font-family: 'Segoe UI', system-ui, sans-serif; background-color: #f8fafc; }
        .hero-banner {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.4)), url('https://images.unsplash.com/photo-1542281286-9e0a16bb7366') center/cover;
            color: white; padding: 100px 0; text-align: center;
        }
        #map { height: 500px; width: 100%; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-top: 20px;}
        .attraction-card {
            background: white; border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: transform 0.3s;
            height: 100%;
        }
        .attraction-card:hover { transform: translateY(-5px); }
        .attraction-img { height: 200px; background: #e2e8f0; object-fit: cover; width: 100%; }
        .badge-type { position: absolute; top: 10px; right: 10px; }
    </style>
</head>
<body>

<div class="hero-banner">
    <h1 class="display-4 fw-bold">Khám phá Điểm Đến Gần Bạn</h1>
    <p class="lead">Tận hưởng các tiện ích và địa danh du lịch tuyệt đẹp quanh khách sạn</p>
    <a href="<?= $baseUrl ?>/rooms" class="btn btn-primary btn-lg mt-3"><i class="fa fa-arrow-left"></i> Về đặt phòng</a>
</div>

<div class="container py-5">
    <div class="row mb-5">
        <div class="col-12">
            <h2 class="mb-4"><i class="fa-solid fa-map-location-dot"></i> Bản đồ khu vực</h2>
            <div id="map"></div>
        </div>
    </div>

    <h2 class="mb-4"><i class="fa-solid fa-camera-retro"></i> Danh sách địa điểm</h2>
    <div class="row g-4">
        <?php foreach ($attractions as $item): ?>
        <div class="col-md-4 col-lg-3">
            <div class="attraction-card position-relative">
                <span class="badge bg-warning text-dark badge-type"><?= htmlspecialchars($item['type']) ?></span>
                <img src="<?= htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/400x200?text=Attraction') ?>" class="attraction-img" alt="<?= htmlspecialchars($item['name']) ?>">
                <div class="p-3">
                    <h5 class="fw-bold mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                    <p class="text-primary mb-2"><i class="fa-solid fa-car-side"></i> Cách <?= floatval($item['distance_km']) ?> km</p>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($item['description'] ?? 'Chưa có mô tả') ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // Tọa độ giả lập khách sạn Đà Nẵng
    var hotelLat = 16.060183;
    var hotelLng = 108.241517;

    var map = L.map('map').setView([hotelLat, hotelLng], 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker KS
    var hotelIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3009/3009384.png',
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });
    L.marker([hotelLat, hotelLng], {icon: hotelIcon}).addTo(map).bindPopup("<b>Khách Sạn Của Chúng Tôi</b><br>Tọa độ trung tâm.").openPopup();

    <?php 
    // Sinh tọa độ giả lập cho attraction do DB không có thuộc tính lat/lng
    foreach ($attractions as $idx => $item): 
        // offset small random value based on idx and distance to simulate map position
        $lat = 16.06 + (cos($idx) * ($item['distance_km'] / 111));
        $lng = 108.24 + (sin($idx) * ($item['distance_km'] / 111));
    ?>
    L.marker([<?= $lat ?>, <?= $lng ?>]).addTo(map)
        .bindPopup("<b><?= htmlspecialchars($item['name']) ?></b><br><?= floatval($item['distance_km']) ?> km - <?= htmlspecialchars($item['type']) ?>");
    <?php endforeach; ?>
</script>
</body>
</html>

