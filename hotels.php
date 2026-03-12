<?php
include 'db.php';
$location = $_GET['location'] ?? '';
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';

$query = "SELECT * FROM hotels WHERE location LIKE :location";
$stmt = $conn->prepare($query);
$stmt->execute(['location' => "%$location%"]);
$hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Hotels in <?= htmlspecialchars($location) ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Hotels in <?= htmlspecialchars($location) ?></h2>
<div class="hotel-list">
<?php foreach ($hotels as $hotel): ?>
    <div class="hotel-card">
        <img src="<?= $hotel['image'] ?>" alt="<?= $hotel['name'] ?>">
        <h3><?= $hotel['name'] ?></h3>
        <p><?= $hotel['description'] ?></p>
        <p>Price: $<?= $hotel['price'] ?> | Rating: <?= $hotel['rating'] ?></p>
        <a href="booking.php?hotel_id=<?= $hotel['id'] ?>&check_in=<?= $check_in ?>&check_out=<?= $check_out ?>">Book Now</a>
    </div>
<?php endforeach; ?>
</div>
</body>
</html>
