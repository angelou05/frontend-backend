<?php
require_once '../frontend/config.php';

$query = "SELECT * FROM featured_listings";
$result = mysqli_query($connect, $query);

$listings = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $listings[] = $row;
    }
}
header('Content-Type: application/json');
echo json_encode($listings);
?>
