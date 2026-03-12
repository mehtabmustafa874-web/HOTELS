<?php

$host = "localhost";
$user = "rsoa_rsoa340_51";
$pass = "123456";
$db   = "rsoa340_hotel_booking";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection error: " . mysqli_connect_error());
}

?>
