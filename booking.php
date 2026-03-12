<?php
include 'db.php';

$hotel_id = $_GET['hotel_id'];
$check_in = $_GET['check_in'];
$check_out = $_GET['check_out'];

$stmt = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotel_id]);
$hotel = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $guests = $_POST['guests'];

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Register user
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        $user_id = $conn->lastInsertId();
    } else {
        $user_id = $user['id'];
    }

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, hotel_id, check_in, check_out, guests) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $hotel_id, $check_in, $check_out, $guests]);

    header("Location: confirm.php?booking_id=".$conn->lastInsertId());
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Book <?= $hotel['name'] ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Book <?= $hotel['name'] ?></h2>
<form method="post">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="number" name="guests" value="1" min="1" required>
    <input type="hidden" name="check_in" value="<?= $check_in ?>">
    <input type="hidden" name="check_out" value="<?= $check_out ?>">
    <button type="submit">Confirm Booking</button>
</form>
</body>
</html>
