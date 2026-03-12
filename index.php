<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

$confirmation = "";
$search_location = $_GET['location'] ?? "";

/* ===============================
   FETCH HOTELS SAFELY
=================================*/
try {
    if ($search_location) {
        $stmt = $conn->prepare("SELECT * FROM hotels WHERE location LIKE ? LIMIT 10");
        $stmt->execute(["%$search_location%"]);
    } else {
        $stmt = $conn->prepare("SELECT * FROM hotels LIMIT 10");
        $stmt->execute();
    }
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $hotels = [];
}

/* ===============================
   HANDLE BOOKING SAFELY
=================================*/
if (isset($_POST['hotel_id_booking'])) {
    $hotel_id = $_POST['hotel_id_booking'];
    $amount = $_POST['amount'];

    try {
        $stmt = $conn->prepare("SELECT * FROM hotels WHERE id=?");
        $stmt->execute([$hotel_id]);
        $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($hotel && $amount == $hotel['price']) {
            $confirmation = "Booking confirmed for {$hotel['name']}! Amount: $" . $amount;
        } else {
            $confirmation = "Entered amount does not match hotel price.";
        }
    } catch (Exception $e) {
        $confirmation = "Booking failed.";
    }
}

/* ===============================
   HANDLE COMMENT SAFELY
=================================*/
if (isset($_POST['hotel_id_comment'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO hotel_comments (hotel_id, username, comment) VALUES (?, ?, ?)");
        $stmt->execute([
            $_POST['hotel_id_comment'],
            $_POST['username'],
            $_POST['comment']
        ]);
    } catch (Exception $e) {
        // Do nothing (prevents 500 crash)
    }
}

/* ===============================
   FUNCTIONS (SAFE)
=================================*/
function get_images($conn, $hotel_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM hotel_images WHERE hotel_id=? LIMIT 5");
        $stmt->execute([$hotel_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function get_comments($conn, $hotel_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM hotel_comments WHERE hotel_id=? ORDER BY id DESC");
        $stmt->execute([$hotel_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>SkyBlue Hotels</title>
<style>
body{
    font-family:Arial;
    background:linear-gradient(to right,#87cefa,#e0bbff,#ffffff);
    padding:20px;
}
.hotel-card{
    background:white;
    padding:20px;
    margin:20px auto;
    width:700px;
    border-radius:10px;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}
.hotel-images img{
    width:120px;
    height:80px;
    margin:3px;
}
</style>
</head>
<body>

<h1 align="center">Instant Hotels Booking</h1>

<form method="get" align="center">
    <input type="text" name="location" placeholder="Search location">
    <button type="submit">Search</button>
</form>

<?php if($confirmation): ?>
    <h3 style="text-align:center;color:green;">
        <?php echo $confirmation; ?>
    </h3>
<?php endif; ?>

<?php foreach($hotels as $hotel): ?>
<div class="hotel-card">

<h2><?php echo $hotel['name']; ?> (<?php echo $hotel['rating']; ?>⭐)</h2>
<p><b>Price:</b> $<?php echo $hotel['price']; ?></p>
<p><?php echo $hotel['description']; ?></p>
<p><b>Amenities:</b> <?php echo $hotel['amenities']; ?></p>

<div class="hotel-images">
<?php
$images = get_images($conn, $hotel['id']);
foreach($images as $img){
    echo "<img src='{$img['image_path']}'>";
}
?>
</div>

<!-- Booking -->
<form method="post">
    <input type="hidden" name="hotel_id_booking" value="<?php echo $hotel['id']; ?>">
    <input type="number" name="amount" placeholder="Enter amount" required>
    <button type="submit">Book Now</button>
</form>

<!-- Comments -->
<h4>Comments:</h4>
<?php
$comments = get_comments($conn, $hotel['id']);
foreach($comments as $c){
    echo "<p><b>{$c['username']}:</b> {$c['comment']}</p>";
}
?>

<form method="post">
    <input type="hidden" name="hotel_id_comment" value="<?php echo $hotel['id']; ?>">
    <input type="text" name="username" placeholder="Your Name" required>
    <input type="text" name="comment" placeholder="Write comment" required>
    <button type="submit">Add Comment</button>
</form>

</div>
<?php endforeach; ?>

</body>
</html>
