<?php
include 'db.php';
$booking_id = $_GET['booking_id'];
$stmt = $conn->prepare("SELECT b.*, h.name AS hotel_name FROM bookings b JOIN hotels h ON b.hotel_id=h.id WHERE b.id=?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Confirmed</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Booking Confirmed!</h2>
<p>You have successfully booked <strong><?= $booking['hotel_name'] ?></strong>.</p>
<p>Check-in: <?= $booking['check_in'] ?> | Check-out: <?= $booking['check_out'] ?></p>
<p>Guests: <?= $booking['guests'] ?></p>
<p>Booking ID: <?= $booking['id'] ?></p>
<a href="index.php">Back to Home</a>
</body>
</html>
