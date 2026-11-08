<?php

require_once __DIR__ . '/App/Core/Database.php';
require_once __DIR__ . '/Config/database.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    echo "Connected to database successfully.\n";

    // 1. New Amenities (20-39)
    $amenities = [
        [20, 'Sauna Access', 'fa-hot-tub-person'],
        [21, 'Private Home Cinema', 'fa-video'],
        [22, 'Yoga Studio', 'fa-child-reaching'],
        [23, 'Game Console (PS5)', 'fa-gamepad'],
        [24, 'Wine Cellar Access', 'fa-wine-bottle'],
        [25, 'Kitchenette', 'fa-kitchen-set'],
        [26, 'Heated Flooring', 'fa-socks'],
        [27, 'Smart Lighting', 'fa-lightbulb'],
        [28, 'Voice Control (Alexa)', 'fa-microphone-lines'],
        [29, 'Oversized Balcony', 'fa-mountain-sun'],
        [30, 'Pet-Friendly Kit', 'fa-paw'],
        [31, 'Electric Vehicle Charging', 'fa-charging-station'],
        [32, 'Soundproof Walls', 'fa-volume-xmark'],
        [33, 'Library Corner', 'fa-book'],
        [34, 'Indoor Vertical Garden', 'fa-leaf'],
        [35, 'In-room Exercise Bike', 'fa-bicycle'],
        [36, 'Direct Beach Access', 'fa-umbrella-beach'],
        [37, 'Stargazing Telescope', 'fa-telescope'],
        [38, 'Personal Butler Service', 'fa-user-tie'],
        [39, 'Sleep Aid Machine', 'fa-moon']
    ];

    foreach ($amenities as $a) {
        $stmt = $db->prepare("INSERT IGNORE INTO amenities (id, name, icon_url) VALUES (?, ?, ?)");
        $stmt->execute($a);
    }
    echo "Inserted amenities.\n";

    // 2. New Inventory Items
    $inventory = [
        ['Premium Bathrobe', 850000.00, 20],
        ['Mini-bar Red Wine', 450000.00, 30],
        ['Satin Pillowcase', 120000.00, 50],
        ['Shoeshine Kit', 25000.00, 100],
        ['L\'Occitane Shampoo (50ml)', 75000.00, 150],
        ['L\'Occitane Shower Gel (50ml)', 75000.00, 150],
        ['Bamboo Toothbrush', 12000.00, 300],
        ['Artisanal Tea Tin', 180000.00, 40],
        ['Luxury Sleeping Mask', 55000.00, 60],
        ['Room Fragrance Mist', 150000.00, 25],
        ['Universal Charger Adaptor', 350000.00, 15],
        ['High-quality Stationery Set', 45000.00, 100],
        ['Mini Umbrella', 120000.00, 40],
        ['Hand Sanitizer (Luxury)', 35000.00, 200],
        ['Imported Chocolate Bar', 65000.00, 80],
        ['Premium Coffee Beans (200g)', 220000.00, 30],
        ['Cotton Slippers (Extra Soft)', 30000.00, 100],
        ['Sewing Kit', 5000.00, 200],
        ['Shower Cap (Sustainable)', 3000.00, 300],
        ['Bath Bomb (Jasmine)', 85000.00, 50]
    ];

    foreach ($inventory as $i) {
        $stmt = $db->prepare("INSERT IGNORE INTO inventory_items (name, default_price, stock_quantity) VALUES (?, ?, ?)");
        $stmt->execute($i);
    }
    echo "Inserted inventory items.\n";

    // 3. New Room Types (44-63)
    $roomTypes = [
        [44, 'Executive Sky Suite', 'executive-sky-suite', 4500000.00, 2, 1, 'Premium suite with panoramic city views and exclusive lounge access.', 65.00],
        [45, 'Garden Zen Villa', 'garden-zen-villa', 7500000.00, 2, 2, 'Spacious villa surrounded by private Japanese gardens.', 120.00],
        [46, 'Royal Penthouse', 'royal-penthouse', 15000000.00, 4, 2, 'Ultimate luxury living on the top floor with private pool.', 250.00],
        [47, 'Family Connecting Suite', 'family-connecting-suite', 3200000.00, 4, 2, 'Two interconnected rooms designed for absolute family comfort.', 85.00],
        [48, 'Digital Nomad Studio', 'digital-nomad-studio', 1200000.00, 1, 0, 'Compact room optimized for work-from-home with high-speed desk.', 25.00],
        [49, 'Ocean Breeze Studio', 'ocean-breeze-studio', 2200000.00, 2, 0, 'Wake up to the sound of waves in this minimalist beachfront studio.', 40.00],
        [50, 'Heritage Loft', 'heritage-loft', 2800000.00, 2, 1, 'Classic design meets modern comfort in this industrial-style loft.', 55.00],
        [51, 'Eco-Friendly Cocoon', 'eco-friendly-cocoon', 1500000.00, 2, 0, 'Sustainability-focused room with organic materials.', 30.00],
        [52, 'Artisan Suite', 'artisan-suite', 3500000.00, 2, 1, 'Decorated with local art, featuring a dedicated creative space.', 50.00],
        [53, 'Skyline Balcony Room', 'skyline-balcony-room', 2100000.00, 2, 0, 'Modern room with a large private balcony overlooking the skyline.', 38.00],
        [54, 'Romantic Hideaway', 'romantic-hideaway', 3800000.00, 2, 0, 'Perfect for couples, featuring a sunken bathtub and candlelit service.', 45.00],
        [55, 'Business Class Single', 'business-class-single', 1800000.00, 1, 0, 'Tailored for the professional traveler with ergonomic furniture.', 28.00],
        [56, 'Luxury Triple Room', 'luxury-triple-room', 3000000.00, 3, 0, 'Ideal for friends or family, featuring three full-sized beds.', 55.00],
        [57, 'Poolside Terrace Room', 'poolside-terrace-room', 3900000.00, 2, 2, 'Direct access to the swimming pool from your private terrace.', 50.00],
        [58, 'Urban Shelter Pod', 'urban-shelter-pod', 900000.00, 1, 0, 'Smart use of space for the essential traveler.', 18.00],
        [59, 'Presidents Suite', 'presidents-suite', 12000000.00, 4, 2, 'Formal dining area, separate living room, and world-class service.', 180.00],
        [60, 'Sun-Drenched Corner', 'sun-drenched-corner', 2400000.00, 2, 1, 'Double aspect windows flooding the room with natural light.', 42.00],
        [61, 'Quiet Sanctuary', 'quiet-sanctuary', 2600000.00, 2, 0, 'Enhanced soundproofing and sleep-optimized features.', 35.00],
        [62, 'Modern Library Room', 'modern-library-room', 2700000.00, 2, 0, 'surrounded by books, featuring a cozy reading nook.', 48.00],
        [63, 'Pet-Friendly Garden Room', 'pet-friendly-garden-room', 2500000.00, 2, 1, 'Features an enclosed garden area for your furry friends.', 40.00]
    ];

    foreach ($roomTypes as $rt) {
        $stmt = $db->prepare("INSERT IGNORE INTO room_types (id, name, slug, base_price, capacity_adults, capacity_children, description, area_sqm) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($rt);
    }
    echo "Inserted room types.\n";

    // 4. New Rooms (3 per type)
    for ($i = 44; $i <= 63; $i++) {
        $floor = $i - 34; // logical floor
        for ($j = 1; $j <= 3; $j++) {
            $roomNumber = ($floor * 100) + $j;
            $stmt = $db->prepare("INSERT IGNORE INTO rooms (room_type_id, room_number, floor, status) VALUES (?, ?, ?, 'Available')");
            $stmt->execute([$i, (string)$roomNumber, $floor]);
        }
    }
    echo "Inserted rooms.\n";

    // 5. New Services (7-26)
    $services = [
        [7, 1, 'Crystal Ballroom', 500, 8500000.00, 'AVAILABLE'],
        [8, 1, 'Zen Garden Pavilion', 80, 2500000.00, 'AVAILABLE'],
        [9, 1, 'Art Gallery Hall', 150, 3200000.00, 'AVAILABLE'],
        [10, 1, 'Grand Library Hall', 60, 1800000.00, 'AVAILABLE'],
        [11, 1, 'Penthouse Deck', 40, 5500000.00, 'AVAILABLE'],
        [12, 1, 'Basement Theatre', 100, 2200000.00, 'AVAILABLE'],
        [13, 2, 'Table for Two - Terrace', 2, 1500000.00, 'AVAILABLE'],
        [14, 2, 'The Wine Vault Dining', 6, 3500000.00, 'AVAILABLE'],
        [15, 2, 'Sushi Counter VIP', 4, 1200000.00, 'AVAILABLE'],
        [16, 2, 'Private Balcony Brasserie', 4, 1600000.00, 'AVAILABLE'],
        [17, 2, 'Sky Lounge Corner', 5, 2000000.00, 'AVAILABLE'],
        [18, 2, 'Chef\'s Garden Table', 8, 2500000.00, 'AVAILABLE'],
        [19, 3, 'Boardroom Sigma', 20, 1200000.00, 'AVAILABLE'],
        [20, 3, 'Collaboration Hub 1', 12, 850000.00, 'AVAILABLE'],
        [21, 3, 'Private Cabin C1', 2, 350000.00, 'AVAILABLE'],
        [22, 3, 'Executive Lounge A', 6, 950000.00, 'AVAILABLE'],
        [23, 3, 'Innovation Lab', 25, 1800000.00, 'AVAILABLE'],
        [24, 3, 'Media Studio 1', 5, 1500000.00, 'AVAILABLE'],
        [25, 3, 'Quiet Zone Studio', 4, 600000.00, 'AVAILABLE'],
        [26, 3, 'Workshop Space Z', 30, 2100000.00, 'AVAILABLE']
    ];

    foreach ($services as $s) {
        $stmt = $db->prepare("INSERT IGNORE INTO services (id, service_type_id, name, capacity, price_per_hour, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute($s);
    }
    echo "Inserted services.\n";

    echo "ALL SAMPLE DATA SYNCED TO DATABASE SUCCESSFULLY.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
